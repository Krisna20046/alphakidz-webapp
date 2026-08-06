<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MajikanTrackingController extends Controller
{
    /**
     * Tracking (read-only) untuk role Majikan — memantau data yang diisi Nanny:
     *   - Academic Tasks (Module 3) + progress (Module 4)
     *   - School Schedule (Module 2)
     * Backend mengizinkan role 2 untuk read semua anak miliknya (bypass access).
     */

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

    /**
     * Daftar anak milik majikan (untuk selector).
     * Response backend (getUserAnakByMajikan):
     *   { status:'success', data:[ { id, nama, foto, gender, ... } ] }
     */
    private function fetchChildren(): array
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

    private function fetchChildTasks(int $idAnak): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/academic-tasks'));

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        // Paginated shape: data['data']; non-paginated: data[] langsung
        return is_array($data) && array_key_exists('data', $data) ? (is_array($data['data']) ? $data['data'] : []) : $data;
    }

    private function fetchChildSchedules(int $idAnak): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/schedules'));

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        return is_array($data) && array_key_exists('data', $data) ? (is_array($data['data']) ? $data['data'] : []) : $data;
    }

    private function fetchChildName(int $idAnak): string
    {
        foreach ($this->fetchChildren() as $c) {
            if ((int) ($c['id'] ?? 0) === $idAnak) {
                return (string) ($c['nama'] ?? 'Child');
            }
        }
        return 'Child';
    }

    // ─── Index: pilih anak ────────────────────────────────────────────────────

    public function index()
    {
        $anakList = $this->fetchChildren();
        return view('majikan.tracking.index', compact('anakList'));
    }

    // ─── Show: task + jadwal anak ─────────────────────────────────────────────

    public function show(Request $request, int $idAnak)
    {
        $tasks     = $this->fetchChildTasks($idAnak);
        $schedules = $this->fetchChildSchedules($idAnak);
        $namaAnak  = $this->fetchChildName($idAnak);

        // Ringkasan statistik
        $total   = count($tasks);
        $done    = count(array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'completed'));
        $doing   = count(array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'in_progress'));
        $overdue = count(array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'overdue'
            || (($t['status'] ?? '') !== 'completed' && ($t['status'] ?? '') !== 'cancelled'
                && !empty($t['deadline']) && strtotime($t['deadline']) < time())));

        // Jadwal per hari
        $daysOrder = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $scheduleByDay = [];
        foreach ($schedules as $s) {
            $day = strtolower($s['day_of_week'] ?? '');
            $scheduleByDay[$day][] = $s;
        }
        $scheduleByDay = array_merge(array_fill_keys($daysOrder, []), $scheduleByDay);

        return view('majikan.tracking.show', [
            'tasks'        => $tasks,
            'schedules'    => $schedules,
            'namaAnak'     => $namaAnak,
            'idAnak'       => $idAnak,
            'anakList'     => $this->fetchChildren(),
            'total'        => $total,
            'done'         => $done,
            'doing'        => $doing,
            'overdue'      => $overdue,
            'scheduleByDay'=> $scheduleByDay,
            'daysOrder'    => $daysOrder,
        ]);
    }
}
