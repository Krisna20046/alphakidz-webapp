<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KonsultanController extends Controller
{
    /**
     * Base API URL dari config/env.
     */
    private function apiUrl(string $path): string
    {
        return rtrim(config('services.api.base_url', env('API_BASE_URL')), '/') . '/' . ltrim($path, '/');
    }

    /**
     * Token milik user yang sedang login (disimpan di session).
     */
    private function token(): ?string
    {
        return session('token');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Nanny List
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /konsultan/nanny
     * Menampilkan daftar nanny (dapat di-filter dengan ?search=...).
     */
    public function indexNanny(Request $request)
    {
        $search = $request->input('search', '');

        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->post($this->apiUrl('/user-nanny'), [
                    'search' => $search,
                ]);

            $json    = $response->json();
            $nannies = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

        } catch (\Throwable $e) {
            $nannies = [];
        }

        return view('konsultan.nanny-list', compact('nannies'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Nanny Detail
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /konsultan/nanny/{id}
     * Menampilkan detail satu nanny beserta info konsultan yang mengawasinya.
     */
    public function showNanny(int $id)
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/nanny-with-konsultan'), [
                    'id_nanny' => $id,
                ]);

            $json  = $response->json();
            $nanny = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;

        } catch (\Throwable $e) {
            $nanny = null;
        }

        return view('konsultan.nanny-detail', compact('nanny'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Majikan Nanny (daftar penugasan majikan-nanny yang diawasi konsultan ini)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /konsultan/majikan-nanny
     * Daftar pasangan majikan-nanny yang berada di bawah pengawasan konsultan ini.
     */
    public function indexMajikanNanny()
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/majikan-nanny-assignment'));

            $json        = $response->json();
            $assignments = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

        } catch (\Throwable $e) {
            $assignments = [];
        }

        return view('konsultan.majikan-nanny', compact('assignments'));
    }

    /**
     * GET /konsultan/majikan-nanny/{id_majikan}
     * Detail satu penugasan: data majikan, nanny yang ditugaskan, dan info assignment.
     */
    public function showMajikanNanny(int $idMajikan)
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->asMultipart()
                ->post($this->apiUrl('/majikan-nanny-assignment-detail'), [
                    ['name' => 'id_majikan', 'contents' => (string) $idMajikan],
                ]);

            $json       = $response->json();
            $assignment = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;

        } catch (\Throwable $e) {
            $assignment = null;
        }

        return view('konsultan.majikan-nanny-detail', compact('assignment'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Nanny Anda (nanny yang diawasi konsultan yang sedang login)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /konsultan/nanny-anda
     * Daftar nanny yang berada di bawah pengawasan konsultan ini.
     */
    public function indexNannyAnda()
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/konsultan-nanny'));

            $json    = $response->json();
            $nannies = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

        } catch (\Throwable $e) {
            $nannies = [];
        }

        return view('konsultan.nanny-anda', compact('nannies'));
    }

    /**
     * GET /konsultan/nanny-anda/{id}
     * Detail satu nanny milik konsultan yang sedang login.
     */
    public function showNannyAnda(int $id)
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/konsultan-user-detail'), [
                    'id_user' => $id,
                ]);

            $json  = $response->json();
            $raw   = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;

            // Normalise integer fields agar konsisten dengan RN layer
            $nanny = $raw ? array_merge($raw, [
                'id'        => (int) ($raw['id']        ?? 0),
                'id_user'   => (int) ($raw['id_user']   ?? 0),
                'is_active' => (int) ($raw['is_active'] ?? 1),
            ]) : null;

        } catch (\Throwable $e) {
            $nanny = null;
        }

        return view('konsultan.nanny-anda-detail', compact('nanny'));
    }

    /**
     * POST /konsultan/nanny-anda/update-status
     * Aktifkan / nonaktifkan akun nanny.
     */
    public function updateStatusNanny(Request $request)
    {
        $request->validate([
            'id'          => ['required', 'integer'],
            'is_active'   => ['required', 'in:0,1'],
            'redirect_id' => ['nullable', 'integer'],
        ]);

        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->asMultipart()
                ->post($this->apiUrl('/update-status-nanny'), [
                    ['name' => 'id',        'contents' => (string) $request->input('id')],
                    ['name' => 'is_active', 'contents' => (string) $request->input('is_active')],
                ]);

            $json   = $response->json();
            $active = (int) $request->input('is_active');

            if (($json['status'] ?? '') === 'success') {
                $redirectId = (int) ($request->input('redirect_id') ?: $request->input('id'));

                return redirect()
                    ->route('konsultan-nanny-anda-detail', $redirectId)
                    ->with('success', 'Akun nanny berhasil ' . ($active === 1 ? 'diaktifkan' : 'dinonaktifkan') . '.');
            }

            return redirect()
                ->back()
                ->with('error', $json['message'] ?? 'Gagal mengubah status nanny.');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tambah Nanny ke Konsultan
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POST /konsultan/nanny/add
     * Menambahkan nanny ke pengawasan konsultan yang sedang login.
     */
    public function addNanny(Request $request)
    {
        $request->validate([
            'id_nanny' => ['required', 'integer'],
        ]);

        $idNanny = (int) $request->input('id_nanny');

        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->asJson()
                ->post($this->apiUrl('/konsultan-nanny'), [
                    'id_nanny' => $idNanny,
                ]);

            $json = $response->json();

            if (($json['status'] ?? '') === 'success') {
                return redirect()
                    ->route('konsultan-nanny-list')
                    ->with('success', 'Nanny berhasil ditambahkan ke daftar pengawasan Anda.');
            }

            return redirect()
                ->back()
                ->with('error', $json['message'] ?? 'Gagal menambahkan nanny.');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Diary Nanny (detailed view for consultant)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /konsultan/nanny/{id}/diary
     * Menampilkan diary detail untuk nanny yang diawasi konsultan.
     */
    public function showDiary(int $id, Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $idAnak = $request->input('id_anak');
        $kategori = $request->input('kategori', '');

        try {
            // Fetch diary data from API
            $params = [
                'id_nanny' => $id,
            ];
            if ($tanggal) {
                $params['tanggal'] = $tanggal;
            }
            if ($kategori) {
                $params['kategori'] = $kategori;
            }

            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/diary-nanny-for-konsultan'), $params);

            $json = $response->json();
            $data = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;

            // Fetch all nannies for selector
            $allNannies = [];
            try {
                $nanniesResponse = Http::withToken($this->token())
                    ->acceptJson()
                    ->get($this->apiUrl('/konsultan-nanny'));
                $nanniesJson = $nanniesResponse->json();
                if (($nanniesJson['status'] ?? '') === 'success') {
                    $allNannies = $nanniesJson['data'] ?? [];
                }
            } catch (\Throwable $e) {
                // Continue without nanny list
            }

            // Fetch nanny's own details
            $nannyDetail = null;
            try {
                $nannyResponse = Http::withToken($this->token())
                    ->acceptJson()
                    ->get($this->apiUrl('/konsultan-user-detail'), ['id_user' => $id]);
                $nannyJson = $nannyResponse->json();
                if (($nannyJson['status'] ?? '') === 'success') {
                    $nannyDetail = $nannyJson['data'] ?? null;
                }
            } catch (\Throwable $e) {
                // Continue without nanny detail
            }

            // Process data for view
            $daftarAnak = [];
            $aktivitas = [];

            if ($data) {
                $daftarAnak = $data['daftar_anak'] ?? [];
                $rekapPerAnak = $data['rekap_per_anak'] ?? [];

                // If id_anak is not specified, use first child
                if (!$idAnak && !empty($daftarAnak)) {
                    $idAnak = $daftarAnak[0]['id'];
                }

                // Find the selected child's data
                $selectedAnakData = null;
                if ($idAnak) {
                    foreach ($rekapPerAnak as $anakData) {
                        if (($anakData['id_anak'] ?? null) == $idAnak) {
                            $selectedAnakData = $anakData;
                            break;
                        }
                    }
                }

                // Fallback to first child if not found
                if (!$selectedAnakData && !empty($rekapPerAnak)) {
                    $selectedAnakData = $rekapPerAnak[0];
                    $idAnak = $selectedAnakData['id_anak'] ?? null;
                }

                if ($selectedAnakData) {
                    // Find activities for the selected date
                    $aktivitasPerTanggal = $selectedAnakData['aktivitas_per_tanggal'] ?? [];
                    foreach ($aktivitasPerTanggal as $tanggalData) {
                        if ($tanggalData['tanggal'] === $tanggal) {
                            $aktivitas = $tanggalData['aktivitas'] ?? [];
                            break;
                        }
                    }
                }

                // Format aktivitas for view
                $aktivitas = collect($aktivitas)->map(function ($item) {
                    $jamMulai = $item['jam_mulai'] ?? '';
                    $jamSelesai = $item['jam_selesai'] ?? '';

                    // Extract time from datetime
                    if (strlen($jamMulai) > 8) {
                        $jamMulai = date('H:i', strtotime($jamMulai));
                    }
                    if (strlen($jamSelesai) > 8) {
                        $jamSelesai = date('H:i', strtotime($jamSelesai));
                    }

                    $durasi = $item['durasi'] ?? [];
                    $durasiStr = '';
                    if (isset($durasi['jam']) && isset($durasi['menit'])) {
                        $durasiStr = $durasi['jam'] . 'h ' . $durasi['menit'] . 'm';
                    }

                    return array_merge($item, [
                        'jam_mulai_fmt' => $jamMulai,
                        'jam_selesai_fmt' => $jamSelesai,
                        'durasi_fmt' => $durasiStr,
                    ]);
                })->toArray();
            }

        } catch (\Throwable $e) {
            $daftarAnak = [];
            $aktivitas = [];
            $nannyDetail = null;
            $allNannies = [];
        }

        return view('konsultan.diary', [
            'id_nanny' => $id,
            'id_anak' => $idAnak,
            'nanny_detail' => $nannyDetail,
            'all_nannies' => $allNannies,
            'daftar_anak' => $daftarAnak,
            'aktivitas' => $aktivitas,
            'tanggal' => $tanggal,
            'activeKat' => $kategori,
        ]);
    }

    /**
     * GET /konsultan/diary
     * Entry point dari menu home - redirect ke diary nanny pertama (ID terkecil).
     */
    public function diaryIndex()
    {
        try {
            $response = Http::withToken($this->token())
                ->acceptJson()
                ->get($this->apiUrl('/konsultan-nanny'));

            $json    = $response->json();
            $nannies = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

            if (!empty($nannies)) {
                // Ambil nanny dengan ID terkecil (paling lama)
                $defaultNanny = collect($nannies)->sortBy('id')->first();
                return redirect()->route('konsultan-nanny-diary', $defaultNanny['id']);
            }

            return redirect()->route('konsultan-nanny-anda')
                ->with('error', 'Anda belum memiliki nanny yang terdaftar.');

        } catch (\Throwable $e) {
            return redirect()->route('konsultan-nanny-anda')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}