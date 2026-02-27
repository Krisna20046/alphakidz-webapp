<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', env('API_BASE_URL', '')), '/');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROFILE INDEX (ProfileScreen)
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        // Refresh user dari API supaya data selalu fresh
        $this->refreshSessionUser();
        return view('profil.index');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROFILE DETAIL — View & Edit (ProfileDetailScreen)
    // ─────────────────────────────────────────────────────────────────────────

    public function detail(Request $request)
    {
        $this->refreshSessionUser();

        $user      = session('user', []);
        $isEditing = $request->has('edit') || ($user['is_filled'] ?? 1) == 0;

        return view('profil.detail', compact('user', 'isEditing'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE PROFILE (POST)
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request)
    {
        $token = session('token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = session('user', []);
        $id   = $user['id'] ?? null;

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ID user tidak ditemukan'], 400);
        }

        try {
            // Build multipart request
            $http = Http::withToken($token)
                ->acceptJson()
                ->timeout(20);

            // Attach foto jika ada
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $http = $http->attach(
                    'foto',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName(),
                    ['Content-Type' => $file->getMimeType()]
                );
            }

            $response = $http->post("{$this->apiBaseUrl}/user-detail-update", array_merge(
                ['id' => $id],
                $request->except(['_token', 'foto'])
            ));

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                // Refresh session user
                $this->refreshSessionUser();

                return response()->json([
                    'success' => true,
                    'message' => $data['message'] ?? 'Profil berhasil disimpan!',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Gagal menyimpan profil.',
                'errors'  => $data['errors'] ?? null,
            ], 422);

        } catch (\Exception $e) {
            Log::error('ProfileController@update - ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROVINSI DROPDOWN
    // GET /profil/provinsi
    // ─────────────────────────────────────────────────────────────────────────

    public function getProvinsi()
    {
        $token = session('token');
        if (!$token) return response()->json(['success' => false], 401);

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/provinsi");

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'data' => $data['data']]);
            }

            return response()->json(['success' => false, 'data' => []]);

        } catch (\Exception $e) {
            Log::error('ProfileController@getProvinsi - ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => []], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // KOTA BY PROVINSI DROPDOWN
    // GET /profil/kota/{id_provinsi}
    // ─────────────────────────────────────────────────────────────────────────

    public function getKota(int $idProvinsi)
    {
        $token = session('token');
        if (!$token) return response()->json(['success' => false], 401);

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/kota/{$idProvinsi}");

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'data' => $data['data']]);
            }

            return response()->json(['success' => false, 'data' => []]);

        } catch (\Exception $e) {
            Log::error('ProfileController@getKota - ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => []], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT AKUN (UbahDataAkunScreen) — placeholder, buat blade-nya terpisah
    // ─────────────────────────────────────────────────────────────────────────

    public function editAkun()
    {
        return view('profil.edit-akun');
    }

    public function updateAkun(Request $request)
    {
        $token = session('token');
        if (!$token) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

        try {
            $formData = new \Illuminate\Http\Client\Request();

            // Build multipart
            $http = Http::withToken($token)->acceptJson()->timeout(15);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $http = $http->attach('foto', file_get_contents($file->getRealPath()), $file->getClientOriginalName());
            }

            $response = $http->post("{$this->apiBaseUrl}/updateAccount", $request->except(['_token', 'foto']));
            $data     = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                $this->refreshSessionUser();
                return response()->json(['success' => true, 'message' => $data['message'] ?? 'Akun berhasil diperbarui.']);
            }

            return response()->json(['success' => false, 'message' => $data['message'] ?? 'Gagal memperbarui akun.', 'errors' => $data['errors'] ?? null], 422);

        } catch (\Exception $e) {
            Log::error('ProfileController@updateAkun - ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DATA ANAK (hanya Majikan) — placeholder
    // ─────────────────────────────────────────────────────────────────────────

    public function dataAnak()
    {
        return view('coming-soon');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: Refresh user session dari API
    // ─────────────────────────────────────────────────────────────────────────

    private function refreshSessionUser(): void
    {
        $token = session('token');
        if (!$token) return;

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/user-detail");

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                session(['user' => $data['data']]);
            }
        } catch (\Exception $e) {
            Log::warning('ProfileController@refreshSessionUser - ' . $e->getMessage());
        }
    }
}