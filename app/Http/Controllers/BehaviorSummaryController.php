<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 17 — Auto Behavior Summary (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → lihat daftar ringkasan → generate/regenerate (on-demand).
 * - Majikan(role 2): pilih anak → daftar ringkasan (read-only).
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (BehaviorSummaryResource):
 *   GET  behavior-summaries?id_anak&summary_date&page&per_page → { data: {data:[...],meta} }
 *   POST behavior-summaries/generate  { id_anak, summary_date? }
 *   POST behavior-summaries/{id}/regenerate
 */
class BehaviorSummaryController extends Controller
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
                        'id'     => $id,
                        'nama'   => $anak['nama'] ?? 'Child',
                        'foto'   => $anak['foto'] ?? null,
                        'gender' => $anak['gender'] ?? null,
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

    /** Ambil daftar ringkasan anak. Backend returns {data:{data:[BehaviorSummaryResource],meta:{...}}} */
    private function fetchSummaries(int $idAnak, int $page = 1, int $perPage = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/behavior-summaries'), [
                'id_anak'   => $idAnak,
                'page'      => $page,
                'per_page'  => $perPage,
            ]);

        if (!$response->successful()) {
            return ['records' => [], 'pagination' => null];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return ['records' => [], 'pagination' => null];
        }

        // Backend wraps: {data: {data: [...], meta: {...}}}
        $records = $data['data'] ?? $data;
        $meta    = $data['meta'] ?? $json['meta'] ?? [];

        return [
            'records'    => is_array($records) ? $records : [],
            'pagination' => [
                'current_page' => $meta['current_page'] ?? $page,
                'last_page'    => $meta['last_page'] ?? 1,
                'total'        => $meta['total'] ?? 0,
                'per_page'     => $meta['per_page'] ?? $perPage,
            ],
        ];
    }

    /** Ambil satu ringkasan by id. Backend returns {success, data: BehaviorSummaryResource}. */
    private function fetchSummary(int $id): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/behavior-summaries/' . $id));

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? null;
        return is_array($data) && $this->isSuccess($json) ? $data : [];
    }

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.behavior-summary.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: ringkasan per anak ─────────────────────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchSummaries($idAnak, $page);
        $children = $this->fetchNannyChildren();

        return view('nanny.behavior-summary.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $children,
            'namaAnak'   => $this->childName($children, $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: riwayat AJAX ───────────────────────────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-behavior-summaries-show', $idAnak);
        }
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchSummaries($idAnak, $page);

        return view('nanny.behavior-summary._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    /** Nanny: generate ringkasan (on-demand, per tanggal). */
    public function generate(Request $request)
    {
        $request->validate([
            'id_anak'      => 'required|integer',
            'summary_date' => 'nullable|date_format:Y-m-d',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/behavior-summaries/generate'), [
                'id_anak'      => $request->id_anak,
                'summary_date' => $request->input('summary_date', today()->format('Y-m-d')),
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-behavior-summaries-show', $request->id_anak)
                ->with('success', 'Behavior summary berhasil dibuat.');
        }

        return redirect()->route('nanny-behavior-summaries-show', $request->id_anak)
            ->with('error', $this->extractMessage($response));
    }

    /** Proxy — regenerate ringkasan yang sudah ada. */
    public function regenerate(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/behavior-summaries/' . $id . '/regenerate'));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            $idAnak = (int) ($json['data']['id_anak'] ?? $request->input('id_anak', 0));
            return redirect()->route('nanny-behavior-summaries-show', $idAnak ?: $request->input('id_anak', 1))
                ->with('success', 'Behavior summary berhasil diperbarui.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Majikan: pilih anak ────────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.behavior-summary.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: ringkasan per anak (read-only) ───────────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchSummaries($idAnak, $page);
        $children = $this->fetchMajikanChildren();

        return view('majikan.behavior-summary.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $children,
            'namaAnak'   => $this->childName($children, $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: riwayat AJAX ─────────────────────────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-behavior-summaries-show', $idAnak);
        }
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchSummaries($idAnak, $page);

        return view('majikan.behavior-summary._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Detail satu ringkasan ────────────────────────────────────────────

    /** Nanny: detail satu ringkasan (full text + rekomendasi + regenerate). */
    public function nannyDetail(Request $request, int $id)
    {
        $record = $this->fetchSummary($id);
        if (!$record) {
            abort(404, 'Behavior summary tidak ditemukan.');
        }

        return view('nanny.behavior-summary.detail', [
            'record'   => $record,
            'idAnak'   => (int) ($record['id_anak'] ?? 0),
            'namaAnak' => (string) ($record['nama_anak'] ?? 'Anak'),
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    /** Majikan: detail satu ringkasan (full text, read-only). */
    public function majikanDetail(Request $request, int $id)
    {
        $record = $this->fetchSummary($id);
        if (!$record) {
            abort(404, 'Behavior summary tidak ditemukan.');
        }

        return view('majikan.behavior-summary.detail', [
            'record'   => $record,
            'idAnak'   => (int) ($record['id_anak'] ?? 0),
            'namaAnak' => (string) ($record['nama_anak'] ?? 'Anak'),
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }
}