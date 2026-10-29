<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 14 — Activity Timeline (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → timelijn aktivité per anak (diary, task progress,
 *   catatan asisten, kehadiran, reminder) merged chronologisch.
 * - Majikan(role 2): pilih anak → timelijn read-only.
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (ActivityTimelineController):
 *   GET children/{id_anak}/activity-timeline?date=&from=&to=&source_type=&page=&per_page=
 *     → { data: { data: [ {id, source_type, event_time, title, description, meta} ], meta } }
 *   GET children/{id_anak}/activity-timeline/today → { data: [ {...} ] }
 */
class ActivityTimelineController extends Controller
{
    private function apiUrl(string $path = ''): string
    {
        $base = rtrim(config('services.api.base_url', env('API_BASE_URL', 'http://localhost:8000/api')), '/');
        return $base . '/' . ltrim($path, '/');
    }

    private function headers(): array
    {
        return [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . session('token'),
        ];
    }

    private function isSuccess(array $json): bool
    {
        return ($json['success'] ?? null) === true || ($json['status'] ?? '') === 'success';
    }

    /** Nanny: daftar anak yang di-assign aktif (untuk selector). */
    private function fetchNannyChildren(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nanny-assignments-anak-for-nanny'));

        if (!$response->successful()) {
            return [];
        }
        $body = $response->json();
        $data = $body['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($body)) {
            return [];
        }

        $children = [];
        foreach ($data as $assignment) {
            foreach (($assignment['anak'] ?? []) as $anak) {
                $anak = (array) $anak;
                $id = (int) ($anak['id'] ?? 0);
                if ($id > 0) {
                    $children[$id] = [
                        'id'    => $id,
                        'nama'  => $anak['nama'] ?? 'Child',
                        'foto'  => $anak['foto'] ?? null,
                        'gender'=> $anak['gender'] ?? null,
                    ];
                }
            }
        }
        return array_values($children);
    }

    /** Majikan: daftar anak miliknya (untuk selector). */
    private function fetchMajikanChildren(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/user-anak-by-majikan'));

        if (!$response->successful()) {
            return [];
        }
        $body = $response->json();
        $data = $body['data'] ?? [];
        return is_array($data) && $this->isSuccess($body) ? $data : [];
    }

    private function childName(array $children, int $idAnak): string
    {
        foreach ($children as $c) {
            if ((int) ($c['id'] ?? 0) === $idAnak) {
                return (string) ($c['nama'] ?? 'Child');
            }
        }
        return 'Child';
    }

    /** Timelijn per anak (paginated), optional filter tanggal/sumber. */
    private function fetchTimeline(int $idAnak, string $date, ?string $sourceType, int $page = 1, int $perPage = 50): array
    {
        $params = [
            'page'     => $page,
            'per_page' => $perPage,
        ];
        if ($date) {
            $params['date'] = $date;
        }
        if ($sourceType) {
            $params['source_type'] = $sourceType;
        }

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/activity-timeline'), $params);

        if (!$response->successful()) {
            return ['records' => [], 'pagination' => null];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return ['records' => [], 'pagination' => null];
        }

        // Paginated shape: data['data'] + data['meta']
        if (is_array($data) && array_key_exists('data', $data)) {
            $meta = $data['meta'] ?? [];
            return [
                'records'    => is_array($data['data']) ? $data['data'] : [],
                'pagination' => [
                    'current_page' => $meta['current_page'] ?? 1,
                    'last_page'    => $meta['last_page'] ?? 1,
                    'total'        => $meta['total'] ?? 0,
                    'per_page'     => $meta['per_page'] ?? $perPage,
                ],
            ];
        }

        return ['records' => is_array($data) ? $data : [], 'pagination' => null];
    }

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.activity-timeline.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: timelijn per anak ───────────────────────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $date = (string) $request->input('date', '');
        $type = $request->filled('source_type') ? (string) $request->input('source_type') : null;
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchTimeline($idAnak, $date, $type, $page);

        return view('nanny.activity-timeline.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchNannyChildren(),
            'namaAnak'   => $this->childName($this->fetchNannyChildren(), $idAnak),
            'date'       => $date,
            'sourceType' => $type,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: riwayat (AJAX partial, paginated) ───────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-activity-timeline-show', $idAnak);
        }

        $date = (string) $request->input('date', '');
        $type = $request->filled('source_type') ? (string) $request->input('source_type') : null;
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchTimeline($idAnak, $date, $type, $page);

        return view('nanny.activity-timeline._timeline', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: pilih anak ────────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.activity-timeline.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: timelijn per anak (read-only) ─────────────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $date = (string) $request->input('date', '');
        $type = $request->filled('source_type') ? (string) $request->input('source_type') : null;
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchTimeline($idAnak, $date, $type, $page);

        return view('majikan.activity-timeline.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchMajikanChildren(),
            'namaAnak'   => $this->childName($this->fetchMajikanChildren(), $idAnak),
            'date'       => $date,
            'sourceType' => $type,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: riwayat (AJAX partial, paginated) ─────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-activity-timeline-show', $idAnak);
        }

        $date = (string) $request->input('date', '');
        $type = $request->filled('source_type') ? (string) $request->input('source_type') : null;
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchTimeline($idAnak, $date, $type, $page);

        return view('majikan.activity-timeline._timeline', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }
}