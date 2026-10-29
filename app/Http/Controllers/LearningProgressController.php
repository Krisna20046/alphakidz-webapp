<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 5 — Learning Progress (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → lihat chart + riwayat → tambah/hapus skor.
 * - Majikan(role 2): pilih anak → lihat chart + riwayat (read-only).
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (LearningProgressResource / learningChart):
 *   GET  children/{id_anak}/learning-progress → { data: [ {...} ] }
 *   GET  learning-progress/chart?id_anak&from&to&group_by
 *          → data: { id_anak, group_by, categories: {cat: {series, current, delta, attention}}, rubric }
 *   POST learning-progress
 *   DELETE learning-progress/{id}
 */
class LearningProgressController extends Controller
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

    private function extractMessage($response): string
    {
        $json = $response->json();
        if (is_array($json)) {
            if (!empty($json['errors']) && is_array($json['errors'])) {
                $first = reset($json['errors']);
                return is_array($first) ? (string) reset($first) : (string) $first;
            }
            if (!empty($json['message'])) {
                return (string) $json['message'];
            }
        }
        return 'Something went wrong on the server.';
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

    private function fetchRecords(int $idAnak, int $page = 1, int $perPage = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/learning-progress'), [
                'page'     => $page,
                'per_page' => $perPage,
            ]);

        if (!$response->successful()) {
            return ['records' => [], 'pagination' => null];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return ['records' => [], 'pagination' => null];
        }

        // Paginated shape: data['data'] + data['meta']; non-paginated: data[] langsung
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

    private function fetchChart(int $idAnak): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/learning-progress/chart'), ['id_anak' => $idAnak]);

        if (!$response->successful()) {
            return null;
        }
        $json = $response->json();
        $data = $json['data'] ?? null;
        return is_array($data) && $this->isSuccess($json) ? $data : null;
    }

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.learning-progress.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: chart + riwayat per anak ────────────────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('nanny.learning-progress.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchNannyChildren(),
            'namaAnak'   => $this->childName($this->fetchNannyChildren(), $idAnak),
            'chart'      => $this->fetchChart($idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: riwayat (AJAX partial, paginated) ───────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-learning-show', $idAnak);
        }

        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('nanny.learning-progress._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: form tambah skor ────────────────────────────────────────────

    public function nannyCreate(int $idAnak)
    {
        $children = $this->fetchNannyChildren();
        $chart = $this->fetchChart($idAnak);

        // Skor terakhir per kategori sebagai pembanding (agar konsisten antar input)
        $lastScores = [];
        foreach ($this->fetchRecords($idAnak)['records'] as $r) {
            $cat = $r['category'] ?? null;
            if ($cat && !isset($lastScores[$cat])) {
                $lastScores[$cat] = [
                    'score' => $r['score'] ?? null,
                    'date'  => $r['recorded_date'] ?? null,
                ];
            }
        }

        return view('nanny.learning-progress.create', [
            'children'   => $children,
            'idAnak'     => $idAnak,
            'namaAnak'   => $this->childName($children, $idAnak),
            'rubric'     => $chart['rubric'] ?? null,
            'lastScores' => $lastScores,
        ]);
    }

    // ─── Nanny: simpan skor ─────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'       => 'required|integer',
            'category'      => 'required|string|in:reading,math,science,language,focus,communication',
            'score'         => 'required|integer|between:0,100',
            'note'          => 'nullable|string|max:1000',
            'recorded_date' => 'required|date_format:Y-m-d',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/learning-progress'), [
                'id_anak'       => $request->id_anak,
                'category'      => $request->category,
                'score'         => (int) $request->score,
                'note'          => (string) ($request->note ?? ''),
                'recorded_date' => $request->recorded_date,
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-learning-show', $request->id_anak)
                ->with('success', 'Learning progress saved.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: hapus skor ──────────────────────────────────────────────────

    public function destroy(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/learning-progress/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->back()->with('success', 'Learning progress deleted.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Majikan: pilih anak ────────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.learning-progress.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: chart per anak (read-only) ────────────────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('majikan.learning-progress.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchMajikanChildren(),
            'namaAnak'   => $this->childName($this->fetchMajikanChildren(), $idAnak),
            'chart'      => $this->fetchChart($idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: riwayat (AJAX partial, paginated) ─────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-learning-show', $idAnak);
        }

        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('majikan.learning-progress._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
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
}
