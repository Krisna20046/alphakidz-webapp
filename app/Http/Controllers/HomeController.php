<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', env('API_BASE_URL', '')), '/');
    }

    /**
     * Dashboard / Home page
     */
    public function index()
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login');
        }

        // Ambil user detail & menu dari API (parallel)
        [$userRes, $menuRes] = $this->fetchUserData($token);

        // Simpan fresh ke session
        if ($userRes) {
            session(['user' => $userRes]);
        }

        $menus = $menuRes ?? [];

        // Ambil artikel dari WP API
        $artikels = $this->fetchArticles();

        // Modul 9 — Reminder tugas/exam (task berisiko utk dashboard)
        $riskyTasks = $this->riskyTaskList();

        return view('home', compact('menus', 'artikels', 'riskyTasks'));
    }

    /**
     * Proxy endpoint: GET /api/unread-count
     * Dipanggil oleh JavaScript di front-end (bukan langsung ke API)
     * agar token tidak exposed ke client side
     */
    public function unreadCount()
    {
        $token = session('token');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        try {
            $response = Http::timeout(8)
                ->withToken($token)
                ->acceptJson()
                ->get("{$this->apiBaseUrl}/unread-count");

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'data'    => $data['data'] ?? ['unread_count' => 0],
                ]);
            }

            return response()->json(['success' => true, 'data' => ['unread_count' => 0]]);

        } catch (\Exception $e) {
            Log::error('HomeController@unreadCount - ' . $e->getMessage());
            return response()->json(['success' => true, 'data' => ['unread_count' => 0]]);
        }
    }

    /**
     * Modul 9 — Reminder tugas & exam.
     * Proxy: panggil POST /api/reminders/check-now (fallback hosting tanpa cron).
     * Dipicu otomatis dari dashboard (JS) saat halaman dimuat.
     */
    public function triggerTaskReminders()
    {
        $token = session('token');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(15)
                ->post("{$this->apiBaseUrl}/reminders/check-now");

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false) === true) {
                return response()->json([
                    'success' => true,
                    'message' => $data['message'] ?? 'Reminder check done.',
                    'data'    => $data['data'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Failed to run reminder check.',
            ], ($response->status() >= 500 ? 502 : 422));

        } catch (\Exception $e) {
            Log::error('HomeController@triggerTaskReminders - ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ambil artikel dari WP API untuk section di dashboard
     */
    private function fetchArticles(): array
    {
        try {
            $response = Http::timeout(8)
                ->get('https://pnpro.id/wp-json/wp/v2/posts', [
                    'per_page' => 5,
                    '_embed'   => 'wp:featuredmedia',
                ]);

            if (!$response->successful()) {
                return [];
            }

            return $response->collect()->map(function ($post) {
                return [
                    'id'        => $post['id'],
                    'judul'     => $post['title']['rendered'] ?? '',
                    'thumbnail' => $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null,
                    'kategori'  => $post['_embedded']['wp:term'][0][0]['name'] ?? 'Article',
                    'read_time' => '3',
                    'views'     => '0',
                    'link'      => $post['link'] ?? '#',
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('HomeController@fetchArticles - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch user detail & menu role secara parallel
     * Returns [userArray|null, menusArray|null]
     */
    private function fetchUserData(string $token): array
    {
        try {
            $responses = Http::pool(fn ($pool) => [
                $pool->as('user')
                    ->withToken($token)
                    ->acceptJson()
                    ->timeout(10)
                    ->get("{$this->apiBaseUrl}/user/detail"),

                $pool->as('menus')
                    ->withToken($token)
                    ->acceptJson()
                    ->timeout(10)
                    ->get("{$this->apiBaseUrl}/role-menu-user"),
            ]);

            $user  = null;
            $menus = [];

            // ── User detail ──
            if ($responses['user']->successful()) {
                $body = $responses['user']->json();
                if (($body['status'] ?? '') === 'success') {
                    $user = $body['data'];
                }
            } else {
                Log::warning('HomeController - user detail failed: ' . $responses['user']->status());
            }

            // ── Menus ──
            if ($responses['menus']->successful()) {
                $body  = $responses['menus']->json();
                if (($body['status'] ?? '') === 'success') {
                    $menus = $body['menus'] ?? [];
                }
            }

            return [$user, $menus];

        } catch (\Exception $e) {
            Log::error('HomeController@fetchUserData - ' . $e->getMessage());
            return [null, []];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MODUL 9 — Reminder tugas & exam (dashboard): daftar task berisiko
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ambil daftar task berisiko (deadline ≤ 3 hari, overdue, exam / type=exam)
     * untuk ditampilkan di dashboard. Role-ware: Nanny (anak assign aktif) vs
     * Majikan (anak miliknya).
     *
     * @return array<int, array{id:int,title:string,type:string,status:string,deadline:?string,nama_anak:string}>
     */
    private function riskyTaskList(): array
    {
        $token = session('token');
        if (!$token) {
            return [];
        }

        $role = (int) (session('user')['id_role'] ?? 0);

        // Hanya Nanny (role 3) yang dapat reminder in-app — Majikan tidak
        // (keputusan user 2026-08-07: redirect ke halaman update-progress Nanny salah
        // & reminder berhari-hari mengganggu Majikan).
        if ($role !== 3) {
            return [];
        }

        // Kumpulkan id anak sesuai role
        $children = $this->childrenByRole($role, $token);
        if (empty($children)) {
            return [];
        }

        $now      = time();
        $windowH  = 3 * 24 * 3600; // 3 hari
        $risky    = [];

        foreach ($children as $c) {
            try {
                $response = Http::withToken($token)->acceptJson()->timeout(10)
                    ->get("{$this->apiBaseUrl}/children/{$c['id']}/academic-tasks");

                if (!$response->successful()) {
                    continue;
                }
                $json  = $response->json();
                $tasks = $json['data']['data'] ?? $json['data'] ?? [];
                if (!is_array($tasks) || !(($json['success'] ?? false) || ($json['status'] ?? '') === 'success')) {
                    continue;
                }

                foreach ($tasks as $t) {
                    $status = (string) ($t['status'] ?? '');
                    if (in_array($status, ['completed', 'cancelled'], true)) {
                        continue;
                    }

                    $deadline = $t['deadline'] ?? null;
                    $type     = strtolower((string) ($t['type'] ?? ''));

                    if ($deadline) {
                        $ts = strtotime((string) $deadline);
                        if ($ts === false) {
                            continue;
                        }

                        $isOverdue   = $ts < $now;
                        $isNear      = !$isOverdue && ($ts - $now) <= $windowH;
                        $isExam      = $type === 'exam' && !$isOverdue;
                        if (!$isOverdue && !$isNear && !$isExam) {
                            continue;
                        }
                    } else {
                        // Tanpa deadline, hanya exam yang dianggap berisiko
                        if ($type !== 'exam') {
                            continue;
                        }
                    }

                    $risky[] = [
                        'id'        => (int) ($t['id'] ?? 0),
                        'title'     => (string) ($t['title'] ?? 'Tugas'),
                        'type'      => $type,
                        'status'    => $status,
                        'deadline'  => $deadline,
                        'nama_anak' => (string) ($c['nama'] ?? 'Anak'),
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('HomeController@riskyTaskList anak ' . $c['id'] . ' - ' . $e->getMessage());
            }
        }

        // Sortir: yang paling mendesak (overdue dulu, lalu deadline terdekat)
        usort($risky, function ($a, $b) use ($now) {
            $ka = ($this->taskUrgency($a['deadline'], $a['status']));
            $kb = ($this->taskUrgency($b['deadline'], $b['status']));
            return $ka <=> $kb;
        });

        return $risky;
    }

    /** id anak sesuai role. */
    private function childrenByRole(int $role, string $token): array
    {
        if ($role === 3) { // Nanny
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/nanny-assignments-anak-for-nanny");

            if (!$response->successful()) {
                return [];
            }
            $body = $response->json();
            $data = $body['data'] ?? [];
            if (!is_array($data) || !(($body['status'] ?? '') === 'success')) {
                return [];
            }

            $children = [];
            foreach ($data as $assignment) {
                foreach (($assignment['anak'] ?? []) as $anak) {
                    // Backend bisa kirim array asosiatif ATAU stdClass (hasil DB->get()).
                    $anak = (array) $anak;   // normalisasi → array
                    $id = (int) ($anak['id'] ?? 0);
                    if ($id > 0) {
                        $children[$id] = [
                            'id'   => $id,
                            'nama' => $anak['nama'] ?? 'Child',
                        ];
                    }
                }
            }
            return array_values($children);
        }

        if ($role === 2) { // Majikan
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/user-anak-by-majikan");

            if (!$response->successful()) {
                return [];
            }
            $body = $response->json();
            $data = $body['data'] ?? [];
            if (!is_array($data)) {
                return [];
            }

            return array_values(array_filter(array_map(fn ($c) => [
                'id'   => (int) (($c['id'] ?? $c->id ?? 0)),
                'nama' => $c['nama'] ?? $c->nama ?? 'Child',
            ], $data), fn ($c) => $c['id'] > 0));
        }

        return [];
    }

    /** Kunci pengurutan: 0 = paling mendesak. */
    private function taskUrgency(?string $deadline, string $status): int
    {
        if ($status === 'overdue') {
            return 0;
        }
        if ($deadline) {
            $ts = strtotime((string) $deadline);
            if ($ts !== false) {
                return (int) floor(($ts - time()) / 3600); // jam tersisa
            }
        }
        return 9999;
    }
}