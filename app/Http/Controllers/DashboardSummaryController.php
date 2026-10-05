<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 20 — Dashboard Summary (frontend proxy).
 *
 * - Nanny  (role 3) & Admin: GET api/dashboards/nanny — panel asisten.
 * - Majikan(role 2):        GET api/dashboards/parent — panel majikan.
 * Keduanya read-only: GET tidak memicu generate AI di backend.
 * Diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (DashboardSummaryResource):
 *   GET dashboards/nanny?child_id=&tanggal=      → { data: {...} }
 *   GET dashboards/parent?id_anak=&tanggal=      → { data: {...} }
 */
class DashboardSummaryController extends Controller
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

    /** Nanny: GET dashboards/nanny?child_id= — panel asisten (jadwal, kehadiran, reminder, checklist, PR). */
    private function fetchNannyDashboard(int $idAnak, ?string $tanggal): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/dashboards/nanny'), [
                'child_id' => $idAnak,
                'tanggal'  => $tanggal,
            ]);

        $json = $response->json();
        $data = $json['data'] ?? null;
        if (!$response->successful() || !is_array($data) || !is_array($json) || !$this->isSuccess($json)) {
            return [];
        }
        return $data;
    }

    /** Majikan: GET dashboards/parent?id_anak= — panel majikan (ringkasan AI + statistik + laporan). */
    private function fetchParentDashboard(int $idAnak, ?string $tanggal): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/dashboards/parent'), [
                'id_anak' => $idAnak,
                'tanggal' => $tanggal,
            ]);

        $json = $response->json();
        $data = $json['data'] ?? null;
        if (!$response->successful() || !is_array($data) || !is_array($json) || !$this->isSuccess($json)) {
            return [];
        }
        return $data;
    }

    // ─── Nanny: pilih anak → panel asisten ────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.dashboard-summary.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    public function nannyShow(Request $request, int $idAnak)
    {
        $tanggal = $request->filled('tanggal') ? (string) $request->input('tanggal') : null;
        $dashboard = $this->fetchNannyDashboard($idAnak, $tanggal);
        $children  = $this->fetchNannyChildren();

        return view('nanny.dashboard-summary.show', [
            'idAnak'    => $idAnak,
            'anakList'  => $children,
            'namaAnak'  => $dashboard['nama_anak'] ?? $this->childName($children, $idAnak),
            'tanggal'   => $tanggal,
            'dashboard' => $dashboard,
        ]);
    }

    // ─── Majikan: pilih anak → panel majikan (read-only) ───────────────────

    public function majikanIndex()
    {
        return view('majikan.dashboard-summary.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    public function majikanShow(Request $request, int $idAnak)
    {
        $tanggal = $request->filled('tanggal') ? (string) $request->input('tanggal') : null;
        $dashboard = $this->fetchParentDashboard($idAnak, $tanggal);
        $children  = $this->fetchMajikanChildren();

        return view('majikan.dashboard-summary.show', [
            'idAnak'    => $idAnak,
            'anakList'  => $children,
            'namaAnak'  => $dashboard['nama_anak'] ?? $this->childName($children, $idAnak),
            'tanggal'   => $tanggal,
            'dashboard' => $dashboard,
        ]);
    }
}