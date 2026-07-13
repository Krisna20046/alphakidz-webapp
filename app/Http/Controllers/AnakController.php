<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnakController extends Controller
{
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(config('services.api.base_url', env('API_BASE_URL', '')), '/');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Proxy helper — call backend API
    // ─────────────────────────────────────────────────────────────────────────

    private function apiGet(string $path)
    {
        $token = session('token');
        if (!$token) return null;
        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)->get("{$this->apiBaseUrl}{$path}");
            $data = $response->json();
            return ($response->successful() && ($data['status'] ?? '') === 'success') ? ($data['data'] ?? null) : null;
        } catch (\Exception $e) {
            Log::error("AnakController::apiGet({$path}) - " . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LIST  GET /profil/data-anak
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $token    = session('token');
        $anakList = [];

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->get("{$this->apiBaseUrl}/user-anak-by-majikan");

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                $anakList = $data['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('AnakController@index - ' . $e->getMessage());
        }

        return view('profil.anak.index', compact('anakList'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DETAIL  GET /profil/data-anak/{id}
    // ─────────────────────────────────────────────────────────────────────────

    public function detail(int $id)
    {
        $token = session('token');
        $anak  = null;
        $rumahSakit = [];
        $dokter = [];
        $vaksin = [];

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->post("{$this->apiBaseUrl}/user-anak-detail-by-majikan", ['id_anak' => $id]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                $anak = $data['data'][0] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('AnakController@detail - ' . $e->getMessage());
        }

        if (!$anak) {
            return redirect()->route('profil.data-anak')
                ->with('error', 'Data anak tidak ditemukan.');
        }

        // Fetch medical data
        $rumahSakit = $this->apiGet("/anak/medical/{$id}/rumah-sakit") ?? [];
        $dokter     = $this->apiGet("/anak/medical/{$id}/dokter") ?? [];
        $vaksin     = $this->apiGet("/anak/medical/{$id}/vaksin") ?? [];

        return view('profil.anak.detail', compact('anak', 'rumahSakit', 'dokter', 'vaksin'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TAMBAH  GET /profil/data-anak/tambah
    // ─────────────────────────────────────────────────────────────────────────

    public function tambah()
    {
        $anak   = [];
        $isEdit = false;
        return view('profil.anak.form', compact('anak', 'isEdit'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE  POST /profil/data-anak/store
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $token = session('token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        try {
            $http = Http::withToken($token)->acceptJson()->timeout(20);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $http = $http->attach('foto', file_get_contents($file->getRealPath()), $file->getClientOriginalName(), ['Content-Type' => $file->getMimeType()]);
            }

            $response = $http->post("{$this->apiBaseUrl}/user-anak", $request->except(['_token', 'foto']));
            $data     = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return response()->json([
                    'success'  => true,
                    'message'  => $data['message'] ?? 'Data anak berhasil ditambahkan!',
                    'redirect' => route('profil.data-anak'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Gagal menyimpan data anak.',
                'errors'  => $data['errors'] ?? null,
            ], 422);

        } catch (\Exception $e) {
            Log::error('AnakController@store - ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UBAH  GET /profil/data-anak/{id}/ubah
    // ─────────────────────────────────────────────────────────────────────────

    public function ubah(int $id)
    {
        $token = session('token');
        $anak  = [];
        $rumahSakit = [];
        $dokter = [];
        $vaksin = [];

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->post("{$this->apiBaseUrl}/user-anak-detail-by-majikan", ['id_anak' => $id]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                $anak = $data['data'][0] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('AnakController@ubah - ' . $e->getMessage());
        }

        if (empty($anak)) {
            return redirect()->route('profil.data-anak')
                ->with('error', 'Data anak tidak ditemukan.');
        }

        // Fetch medical data for edit form
        $rumahSakit = $this->apiGet("/anak/medical/{$id}/rumah-sakit") ?? [];
        $dokter     = $this->apiGet("/anak/medical/{$id}/dokter") ?? [];
        $vaksin     = $this->apiGet("/anak/medical/{$id}/vaksin") ?? [];

        $isEdit = true;
        return view('profil.anak.form', compact('anak', 'isEdit', 'rumahSakit', 'dokter', 'vaksin'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE  POST /profil/data-anak/update
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request)
    {
        $token = session('token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        try {
            $http = Http::withToken($token)->acceptJson()->timeout(20);

            $payload = $request->except(['_token', 'foto', 'foto_lama']);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $http = $http->attach('foto', file_get_contents($file->getRealPath()), $file->getClientOriginalName(), ['Content-Type' => $file->getMimeType()]);
            }

            $response = $http->post("{$this->apiBaseUrl}/user-anak-update", $payload);
            $data     = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return response()->json([
                    'success'  => true,
                    'message'  => $data['message'] ?? 'Data anak berhasil diperbarui!',
                    'redirect' => route('profil.data-anak'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Gagal memperbarui data anak.',
                'errors'  => $data['errors'] ?? null,
            ], 422);

        } catch (\Exception $e) {
            Log::error('AnakController@update - ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HAPUS  DELETE /profil/data-anak/{id}
    // ─────────────────────────────────────────────────────────────────────────

    public function hapus(int $id)
    {
        $token = session('token');
        if (!$token) {
            return redirect()->route('profil.data-anak')->with('error', 'Unauthenticated');
        }

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->post("{$this->apiBaseUrl}/user-anak-delete", ['id' => $id]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                return redirect()->route('profil.data-anak')
                    ->with('success', 'Data anak berhasil dihapus.');
            }

            return redirect()->back()->with('error', $data['message'] ?? 'Gagal menghapus data anak.');

        } catch (\Exception $e) {
            Log::error('AnakController@hapus - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Server error');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MEDICAL — proxy CRUD untuk RS, Dokter, Vaksin
    // ─────────────────────────────────────────────────────────────────────────

    public function medicalStore(Request $request, string $type)
    {
        $valid = ['rumah-sakit', 'dokter', 'vaksin'];
        if (!in_array($type, $valid)) {
            return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
        }
        $token = session('token');
        if (!$token) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        try {
            $http = Http::withToken($token)->acceptJson()->timeout(10);
            $response = $http->post("{$this->apiBaseUrl}/anak/medical/{$type}", $request->all());
            $result = $response->json();
            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => $result['message'] ?? 'Berhasil']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal'], 422);
        } catch (\Exception $e) {
            Log::error("AnakController@medicalStore - " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function medicalUpdate(Request $request, string $type)
    {
        $valid = ['rumah-sakit', 'dokter', 'vaksin'];
        if (!in_array($type, $valid)) {
            return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
        }
        $token = session('token');
        if (!$token) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        try {
            $http = Http::withToken($token)->acceptJson()->timeout(10);
            $response = $http->put("{$this->apiBaseUrl}/anak/medical/{$type}", $request->all());
            $result = $response->json();
            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => $result['message'] ?? 'Berhasil']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal'], 422);
        } catch (\Exception $e) {
            Log::error("AnakController@medicalUpdate - " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function medicalDelete(string $type)
    {
        $valid = ['rumah-sakit', 'dokter', 'vaksin'];
        if (!in_array($type, $valid)) {
            return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
        }
        $token = session('token');
        if (!$token) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        try {
            $id = request('id');
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->delete("{$this->apiBaseUrl}/anak/medical/{$type}/{$id}");
            $result = $response->json();
            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => $result['message'] ?? 'Berhasil dihapus']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal'], 422);
        } catch (\Exception $e) {
            Log::error("AnakController@medicalDelete - " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}