<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MajikanController extends Controller
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.api.base_url', env('API_BASE_URL'));
    }

    public function indexNanny(Request $request)
    {
        $token  = session('token');
        $search = $request->get('search', '');

        try {
            $response = Http::withToken($token)
                ->post("{$this->apiUrl}/user-nanny", [
                    'search' => $search,
                ]);

            $json    = $response->json();
            $nannies = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];
        } catch (\Exception $e) {
            $nannies = [];
        }

        return view('majikan.nanny-list', compact('nannies'));
    }

    public function showNanny(int $id)
    {
        $token = session('token');

        try {
            $response = Http::withToken($token)
                ->get("{$this->apiUrl}/nanny-with-konsultan", [
                    'id_nanny' => $id,
                ]);

            $json  = $response->json();
            $nanny = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;
        } catch (\Exception $e) {
            $nanny = null;
        }

        return view('majikan.nanny-detail', compact('nanny'));
    }

    public function indexKonsultan(Request $request)
    {
        $token  = session('token');
        $search = $request->get('search', '');

        try {
            $response = Http::withToken($token)
                ->post("{$this->apiUrl}/user-konsultan", [
                    'search' => $search,
                ]);

            $json    = $response->json();
            $konsultans = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];
        } catch (\Exception $e) {
            $konsultans = [];
        }

        return view('majikan.konsultan-list', compact('konsultans'));
    }

    public function showKonsultan(int $id)
    {
        $token = session('token');

        try {
            $response = Http::withToken($token)
                ->get("{$this->apiUrl}/user-detail", [
                    'id_user' => $id,
                ]);

            $json  = $response->json();
            $konsultan = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;
        } catch (\Exception $e) {
            $konsultan = null;
        }

        return view('majikan.konsultan-detail', compact('konsultan'));
    }

    public function indexNannyAnda(Request $request)
    {
        $token  = session('token');
        $search = $request->get('search', '');

        try {
            $response = Http::withToken($token)
                ->get("{$this->apiUrl}/nanny-assignments-for-majikan", [
                    'search' => $search,
                ]);

            $json    = $response->json();
            $assignments = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];
        } catch (\Exception $e) {
            $assignments = [];
        }

        return view('majikan.nanny-anda', compact('assignments'));
    }

    public function showNannyAnda(int $id)
    {
        $token = session('token');

        try {
            $response = Http::withToken($token)
                ->get("{$this->apiUrl}/nanny-assignments-detail-for-majikan", [
                    'id_nanny' => $id,
                ]);

            $json  = $response->json();
            $assignment = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? null) : null;
        } catch (\Exception $e) {
            $assignment = null;
        }

        return view('majikan.nanny-anda-detail', compact('assignment'));
    }

    public function chooseDiary()
    {
        $token = session('token');

        try {
            $res     = Http::withToken($token)->get("{$this->apiUrl}/user-anak-by-majikan");
            $json    = $res->json();
            $anakRaw = ($json['status'] ?? '') === 'success' ? ($json['data'] ?? []) : [];

            // Hitung umur di server agar konsisten dengan RN hitungUmurLengkap()
            $anakList = array_map(function ($anak) {
                $anak['umur'] = $this->hitungUmur($anak['tanggal_lahir'] ?? null);
                return $anak;
            }, $anakRaw);

        } catch (\Exception $e) {
            $anakList = [];
        }

        return view('majikan.diary-choose', compact('anakList'));
    }

    /**
     * GET /majikan/diary/{id}
     * Tampilkan diary anak berdasarkan tanggal & kategori
     */
    public function showDiary(Request $request, int $id)
    {
        $token    = session('token');
        $tanggal  = $request->get('tanggal', date('Y-m-d'));
        $kategori = $request->get('kategori', '');

        $diaryData = null;
        $aktivitas = [];
        $namaAnak  = '';

        try {

            $payload = ['id_anak' => $id, 'tanggal' => $tanggal];
            if ($kategori) $payload['kategori'] = $kategori;

            $res  = Http::withToken($token)->asMultipart()->post("{$this->apiUrl}/diary-for-majikan", $payload);
            $json = $res->json();

            if (($json['status'] ?? '') === 'success' && isset($json['data'])) {
                $diaryData = $json['data'];
                $namaAnak  = $diaryData['nama_anak'] ?? '';
                $idAnak    = $diaryData['id_anak'] ?? 0;

                // Flatten aktivitas dari aktivitas_per_tanggal[0]
                $rawAktivitas = $diaryData['aktivitas_per_tanggal'][0]['aktivitas'] ?? [];
                $aktivitas    = array_map(fn($a) => $this->formatAktivitas($a), $rawAktivitas);
            }
        } catch (\Exception $e) {
            // silent — tampil empty state
        }

        // Format tanggal Indonesia untuk header
        $tanggalIndo = $this->formatTanggalIndo($tanggal);

        return view('majikan.diary', compact(
            'idAnak', 'namaAnak', 'tanggal', 'tanggalIndo',
            'diaryData', 'aktivitas'
        ) + ['idAnak' => $id]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function hitungUmur(?string $tanggalLahir): string
    {
        if (!$tanggalLahir) return '-';
        try {
            $lahir    = new \DateTime($tanggalLahir);
            $sekarang = new \DateTime();
            $diff     = $sekarang->diff($lahir);
            $result   = '';
            if ($diff->y > 0) $result .= "{$diff->y} tahun ";
            $result .= "{$diff->m} bulan";
            return trim($result);
        } catch (\Exception $e) {
            return '-';
        }
    }

    private function formatAktivitas(array $a): array
    {
        $jamMulai   = $a['jam_mulai']   ?? '';
        $jamSelesai = $a['jam_selesai'] ?? '';

        $a['jam_mulai_fmt']   = $jamMulai   ? date('H:i', strtotime($jamMulai))   : '-';
        $a['jam_selesai_fmt'] = $jamSelesai ? date('H:i', strtotime($jamSelesai)) : '-';

        // durasi_fmt — mirrors RN: "X jam Y menit"
        $durasi = $a['durasi'] ?? [];
        $jam    = $durasi['jam']   ?? 0;
        $menit  = $durasi['menit'] ?? 0;
        $a['durasi_fmt'] = ($jam > 0 ? "{$jam} jam " : '') . "{$menit} menit";

        return $a;
    }

    private function formatTanggalIndo(string $tanggal): string
    {
        $months = ['', 'Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];
        try {
            $d = new \DateTime($tanggal);
            return $d->format('j') . ' ' . $months[(int)$d->format('n')] . ' ' . $d->format('Y');
        } catch (\Exception $e) {
            return $tanggal;
        }
    }
}