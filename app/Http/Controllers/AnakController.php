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

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->post("{$this->apiBaseUrl}/anak-detail-by-majikan", ['id_anak' => $id]);

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

        return view('profil.anak.detail', compact('anak'));
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

            $response = $http->post("{$this->apiBaseUrl}/anak", $request->except(['_token', 'foto']));
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

        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->post("{$this->apiBaseUrl}/anak-detail-by-majikan", ['id_anak' => $id]);

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

        $isEdit = true;
        return view('profil.anak.form', compact('anak', 'isEdit'));
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

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $http = $http->attach('foto', file_get_contents($file->getRealPath()), $file->getClientOriginalName(), ['Content-Type' => $file->getMimeType()]);
            }

            $response = $http->post("{$this->apiBaseUrl}/anak-update", $request->except(['_token', 'foto']));
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
                ->post("{$this->apiBaseUrl}/anak-delete", ['id' => $id]);

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
}