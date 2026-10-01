<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 16 — AI Learning Insight (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → lihat daftar insight → generate/regenerate (on-demand).
 * - Majikan(role 2): pilih anak → daftar insight (read-only).
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (AiLearningInsightResource):
 *   GET  ai-learning-insights?id_anak&page&per_page → { data: {data:[...],meta} }
 *   POST ai-learning-insights/generate  { id_anak, days }
 *   POST ai-learning-insights/{id}/regenerate
 */
class AiLearningInsightController extends Controller
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

    /** Ambil daftar insight anak. Backend returns {data:{data:[AiLearningInsightResource],meta:{...}}} */
    private function fetchInsights(int $idAnak, int $page = 1, int $perPage = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/ai-learning-insights'), [
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

    /** Ambil satu insight by id. Backend returns {success, data: AiLearningInsightResource}. */
    private function fetchInsight(int $id): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/ai-learning-insights/' . $id));

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
        return view('nanny.ai-learning-insight.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: insight per anak ────────────────────────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchInsights($idAnak, $page);

        return view('nanny.ai-learning-insight.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchNannyChildren(),
            'namaAnak'   => $this->childName($this->fetchNannyChildren(), $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: riwayat AJAX ────────────────────────────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-ai-learning-insights-show', $idAnak);
        }
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchInsights($idAnak, $page);

        return view('nanny.ai-learning-insight._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    /** Nanny: generate insight (on-demand). */
    public function generate(Request $request)
    {
        $request->validate([
            'id_anak' => 'required|integer',
            'days'    => 'nullable|integer|min:1|max:365',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/ai-learning-insights/generate'), [
                'id_anak' => $request->id_anak,
                'days'    => $request->input('days', 30),
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-ai-learning-insights-show', $request->id_anak)
                ->with('success', 'AI Learning Insight berhasil dibuat.');
        }

        return redirect()->route('nanny-ai-learning-insights-show', $request->id_anak)
            ->with('error', $this->extractMessage($response));
    }

    /** Proxy — regenerate insight yang sudah ada. */
    public function regenerate(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/ai-learning-insights/' . $id . '/regenerate'));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            $idAnak = (int) ($json['data']['id_anak'] ?? $request->input('id_anak', 0));
            return redirect()->route('nanny-ai-learning-insights-show', $idAnak ?: $request->input('id_anak', 1))
                ->with('success', 'AI Learning Insight berhasil diperbarui.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Majikan: pilih anak ────────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.ai-learning-insight.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: insight per anak (read-only) ──────────────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchInsights($idAnak, $page);

        return view('majikan.ai-learning-insight.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchMajikanChildren(),
            'namaAnak'   => $this->childName($this->fetchMajikanChildren(), $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: riwayat AJAX ──────────────────────────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-ai-learning-insights-show', $idAnak);
        }
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchInsights($idAnak, $page);

        return view('majikan.ai-learning-insight._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Detail satu insight ────────────────────────────────────────────────

    /** Nanny: detail satu insight (full text + rekomendasi + regenerate). */
    public function nannyDetail(Request $request, int $id)
    {
        $record = $this->fetchInsight($id);
        if (!$record) {
            abort(404, 'AI Learning Insight tidak ditemukan.');
        }

        return view('nanny.ai-learning-insight.detail', [
            'record'   => $record,
            'idAnak'   => (int) ($record['id_anak'] ?? 0),
            'namaAnak' => (string) ($record['nama_anak'] ?? 'Anak'),
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    /** Majikan: detail satu insight (full text, read-only). */
    public function majikanDetail(Request $request, int $id)
    {
        $record = $this->fetchInsight($id);
        if (!$record) {
            abort(404, 'AI Learning Insight tidak ditemukan.');
        }

        return view('majikan.ai-learning-insight.detail', [
            'record'   => $record,
            'idAnak'   => (int) ($record['id_anak'] ?? 0),
            'namaAnak' => (string) ($record['nama_anak'] ?? 'Anak'),
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }
}