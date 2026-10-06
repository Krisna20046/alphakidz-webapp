<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 21 — Nexus Supervision (frontend proxy).
 *
 * Dashboard read-only untuk Nexus (role 5) mengawasi nanny:
 *   - GET nexus/supervision                              — overview semua nanny aktif
 *   - GET nexus/supervision/nanny/{idNanny}              — detail per nanny
 *   - GET nexus/supervision/nanny/{idNanny}/attendance   — riwayat kehadiran (paginated)
 *
 * Semua GET read-only, diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (NexusSupervisionController — role 5 only):
 *   GET nexus/supervision                                     → { data: dashboard }
 *   GET nexus/supervision/nanny/{idNanny}                     → { data: detail }
 *   GET nexus/supervision/nanny/{idNanny}/attendance?from=&to= → { data: history }
 */
class NexusSupervisionController extends Controller
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

    /** Dashboard overview: daftar nanny aktif + total open tickets. */
    private function fetchDashboard(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus/supervision'));

        $json = $response->json();
        $data = $json['data'] ?? null;
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json) || !is_array($data)) {
            return [];
        }
        return $data;
    }

    /** Detail per nanny. */
    private function fetchNannyDetail(int $idNanny): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus/supervision/nanny/' . $idNanny));

        $json = $response->json();
        $data = $json['data'] ?? null;
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json) || !is_array($data)) {
            return [];
        }
        return $data;
    }

    /** Riwayat kehadiran nanny (date range + pagination). */
    private function fetchAttendanceHistory(int $idNanny, ?string $from, ?string $to, int $page = 1, int $perPage = 20): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus/supervision/nanny/' . $idNanny . '/attendance'), [
                'from'     => $from,
                'to'       => $to,
                'page'     => $page,
                'per_page' => $perPage,
            ]);

        $json = $response->json();
        $data = $json['data'] ?? null;
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json) || !is_array($data)) {
            return [];
        }
        return $data;
    }

    /** GET nexus/supervision — overview semua nanny aktif. */
    public function dashboard()
    {
        $data = $this->fetchDashboard();

        return view('nexus.supervision.index', [
            'date'       => $data['date'] ?? now()->toDateString(),
            'totalNannies'   => $data['total_nannies'] ?? 0,
            'totalOpenTickets' => $data['total_open_tickets'] ?? 0,
            'nannies'    => $data['nannies'] ?? [],
        ]);
    }

    /** GET nexus/supervision/nanny/{idNanny} — detail per nanny. */
    public function nannyDetail(int $idNanny)
    {
        $data = $this->fetchNannyDetail($idNanny);

        return view('nexus.supervision.nanny', [
            'idNanny'   => $idNanny,
            'nanny'     => $data['nanny'] ?? [],
            'assignments' => $data['assignments'] ?? [],
            'children'  => $data['children'] ?? [],
            'todayAttendance' => $data['today_attendance'] ?? [],
            'recentTimeline'  => $data['recent_timeline'] ?? [],
            'openTickets'     => $data['open_tickets'] ?? [],
        ]);
    }

    /** GET nexus/supervision/nanny/{idNanny}/attendance — riwayat kehadiran. */
    public function attendanceHistory(Request $request, int $idNanny)
    {
        $from = $request->filled('from') ? (string) $request->input('from') : null;
        $to   = $request->filled('to')   ? (string) $request->input('to')   : null;
        $page = max(1, (int) $request->input('page', 1));

        $data = $this->fetchAttendanceHistory($idNanny, $from, $to, $page);
        $nanny = $this->fetchNannyDetail($idNanny);

        return view('nexus.supervision.attendance', [
            'idNanny'     => $idNanny,
            'nanny'       => $nanny['nanny'] ?? [],
            'from'        => $from,
            'to'          => $to,
            'rows'        => $data['data'] ?? [],
            'meta'        => $data['meta'] ?? [
                'current_page' => 1,
                'last_page'    => 1,
                'total'        => 0,
                'per_page'     => 20,
            ],
        ]);
    }
}