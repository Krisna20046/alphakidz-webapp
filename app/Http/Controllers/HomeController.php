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

        return view('home', compact('menus'));
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

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

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
}