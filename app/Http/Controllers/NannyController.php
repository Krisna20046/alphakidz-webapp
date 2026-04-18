<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NannyController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    private function apiUrl(string $path): string
    {
        return rtrim(config('services.api.base_url', env('API_BASE_URL', 'http://localhost:8000/api')), '/') . '/' . ltrim($path, '/');
    }

    private function headers(): array
    {
        return [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . session('token'),
        ];
    }

    private function fmtJam(string $dt): string
    {
        try { return (new \DateTime($dt))->format('H:i'); } catch (\Exception $e) { return $dt; }
    }

    private function tanggalIndo(string $ymd): string
    {
        $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];
        try {
            $d = new \DateTime($ymd);
            return $d->format('j') . ' ' . $bulan[(int)$d->format('n')] . ' ' . $d->format('Y');
        } catch (\Exception $e) { return $ymd; }
    }

    // ── Data Anak ─────────────────────────────────────────────────────────────

    public function dataAnak()
    {
        $assignmentData = null;

        $res = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nanny-assignments-anak-for-nanny'));

        if ($res->successful()) {
            $body = $res->json();
            if (($body['status'] ?? '') === 'success' && !empty($body['data'])) {
                $assignmentData = $body['data'][0];
            }
        }

        return view('nanny.data-anak', compact('assignmentData'));
    }

    // ── Konsultan ─────────────────────────────────────────────────────────────

    public function konsultan()
    {
        $data = null;

        $res = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/konsultan-by-nanny'));

        if ($res->successful()) {
            $body = $res->json();
            if (($body['status'] ?? '') === 'success' && !empty($body['data'])) {
                $data = $body['data'];
            }
        }

        return view('nanny.data-konsultan', compact('data'));
    }

    // ── Majikan ───────────────────────────────────────────────────────────────

    public function majikan()
    {
        $data     = null;
        $children = [];

        $res = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nanny-assignments-detail-for-nanny'));

        if ($res->successful()) {
            $body = $res->json();
            if (($body['status'] ?? '') === 'success' && !empty($body['data']['assignment'])) {
                $data     = $body['data']['assignment'];
                $children = $body['data']['anak'] ?? [];
            }
        }

        return view('nanny.data-majikan', compact('data', 'children'));
    }

    // ── Choose Diary: langsung redirect ke anak pertama ──────────────────────

    public function chooseDiary()
    {
        ['anakList' => $anakList, 'idAssignment' => $idAssignment] = $this->fetchAnakData();

        if (!empty($anakList)) {
            $firstId = (int) $anakList[0]['id'];
            return redirect()->route('nanny-diary', [
                'id_anak'       => $firstId,
                'id_assignment' => $idAssignment,
            ]);
        }

        // Tidak ada anak — tampil empty state
        return view('nanny.diary', [
            'anakList'     => [],
            'idAnak'       => null,
            'idAssignment' => null,
            'tanggal'      => date('Y-m-d'),
            'tanggalIndo'  => $this->formatTanggalIndo(date('Y-m-d')),
            'diaryData'    => null,
            'aktivitas'    => [],
            'activeKat'    => '',
        ]);
    }

    // ── Diary: list aktivitas anak pada tanggal tertentu ─────────────────────

    public function showDiary(Request $request, int $id_anak)
    {
        $token        = session('token');
        $tanggal      = $request->get('tanggal', date('Y-m-d'));
        $kategori     = $request->get('kategori', '');
        $idAssignment = (int) $request->get('id_assignment', 0);

        $diaryData = null;
        $aktivitas = [];

        // Ambil daftar anak + id_assignment dari API
        ['anakList' => $anakList, 'idAssignment' => $idAssignmentFromApi] = $this->fetchAnakData();

        // Gunakan id_assignment dari query string; fallback ke hasil API
        if (!$idAssignment && $idAssignmentFromApi) {
            $idAssignment = $idAssignmentFromApi;
        }

        try {
            $payload = ['id_anak' => $id_anak, 'tanggal' => $tanggal];
            if ($kategori) $payload['kategori'] = $kategori;

            $res  = Http::withToken($token)
                        ->asMultipart()
                        ->post($this->apiUrl('/diary-for-nanny'), $payload);
            $json = $res->json();

            if (($json['status'] ?? '') === 'success' && isset($json['data'])) {
                $diaryData = $json['data'];

                $rawAktivitas = $diaryData['aktivitas_per_tanggal'][0]['aktivitas'] ?? [];
                $aktivitas    = array_map(fn($a) => $this->formatAktivitas($a), $rawAktivitas);
            }
        } catch (\Exception $e) {
            // silent — tampil empty state
        }

        $tanggalIndo = $this->formatTanggalIndo($tanggal);
        $activeKat   = $kategori;

        return view('nanny.diary', compact(
            'anakList',
            'tanggal',
            'tanggalIndo',
            'diaryData',
            'aktivitas',
            'activeKat',
            'idAssignment'
        ) + ['idAnak' => $id_anak]);
    }

    // ── Add Diary: tampilkan form ─────────────────────────────────────────────

    public function showAdd(Request $request, int $id_anak)
    {
        $idAssignment = $request->query('id_assignment');
        return view('nanny.diary-add', [
            'idAnak'       => $id_anak,
            'idAssignment' => $idAssignment,
        ]);
    }

    // ── Store Diary: kirim ke API ─────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'       => 'required|integer',
            'id_assignment' => 'required|integer',
            'kategori'      => 'required|string',
            'jam_mulai'     => 'required|string',
            'jam_selesai'   => 'required|string',
            'mood'          => 'nullable|string',
            'deskripsi'     => 'nullable|string',
            'foto'          => 'nullable|image|max:4096',
        ]);

        $multipart = [
            ['name' => 'id_assignment', 'contents' => (string) $request->id_assignment],
            ['name' => 'id_anak',       'contents' => (string) $request->id_anak],
            ['name' => 'kategori',      'contents' => (string) $request->kategori],
            ['name' => 'jam_mulai',     'contents' => (string) $request->jam_mulai],
            ['name' => 'jam_selesai',   'contents' => (string) $request->jam_selesai],
            ['name' => 'mood',          'contents' => (string) ($request->mood ?? 'biasa')],
            ['name' => 'deskripsi',     'contents' => (string) ($request->deskripsi ?? '')],
        ];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $multipart[] = [
                'name'     => 'foto',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        $res = Http::withHeaders([
            'Authorization' => 'Bearer ' . session('token'),
            'Accept'        => 'application/json',
        ])
        ->asMultipart()
        ->post($this->apiUrl('/diary'), $multipart);

        $data = $res->json() ?? ['status' => 'error', 'message' => 'Tidak ada respon dari server'];
        return response()->json($data);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Fetch daftar anak + id_assignment dari assignment aktif nanny.
     *
     * Response shape:
     * {
     *   "status": "success",
     *   "data": [
     *     {
     *       "id_assignment": 24,
     *       "anak": [{ "id": "25", "nama": "...", "foto": null, ... }]
     *     }
     *   ]
     * }
     *
     * @return array{ anakList: array, idAssignment: int|null }
     */
    private function fetchAnakData(): array
    {
        $res = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nanny-assignments-anak-for-nanny'));

        if ($res->successful()) {
            $body = $res->json();

            if (($body['status'] ?? '') === 'success' && !empty($body['data'])) {
                // Ambil assignment pertama yang aktif
                $assignment   = $body['data'][0];
                $idAssignment = (int) ($assignment['id_assignment'] ?? 0) ?: null;

                // Normalise: pastikan id anak bertipe int
                $anakList = array_map(function (array $anak) {
                    $anak['id'] = (int) $anak['id'];
                    return $anak;
                }, $assignment['anak'] ?? []);

                return [
                    'anakList'     => $anakList,
                    'idAssignment' => $idAssignment,
                ];
            }
        }

        return ['anakList' => [], 'idAssignment' => null];
    }

    /** @deprecated Gunakan fetchAnakData() */
    private function fetchAnakList(): array
    {
        return $this->fetchAnakData()['anakList'];
    }

    private function formatTanggalIndo(string $tanggal): string
    {
        $months = ['','Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];
        try {
            $d = new \DateTime($tanggal);
            return $d->format('j') . ' ' . $months[(int)$d->format('n')] . ' ' . $d->format('Y');
        } catch (\Exception $e) {
            return $tanggal;
        }
    }

    /**
     * Format satu item aktivitas dari API menjadi array yang siap dipakai view.
     */
private function formatAktivitas(array $a): array
{
    // 1. Format Jam Mulai & Selesai
    $jamMulai   = $a['jam_mulai']   ?? null;
    $jamSelesai = $a['jam_selesai'] ?? null;

    $a['jam_mulai_fmt']   = $jamMulai   ? date('H:i', strtotime($jamMulai))   : '-';
    $a['jam_selesai_fmt'] = $jamSelesai ? date('H:i', strtotime($jamSelesai)) : '-';

    // 2. Format Durasi (Output: "X jam Y menit" atau "Y menit")
    $durasi = $a['durasi'] ?? [];
    
    // Pastikan $durasi adalah array sebelum mengakses key-nya
    if (is_array($durasi)) {
        $jam   = $durasi['jam']   ?? 0;
        $menit = $durasi['menit'] ?? 0;

        if ($jam == 0 && $menit == 0) {
            $a['durasi_fmt'] = '-';
        } else {
            $jamStr = $jam > 0 ? "{$jam} jam " : '';
            $a['durasi_fmt'] = "{$jamStr}{$menit} menit";
        }
    } else {
        // Jika durasi bukan array (misal integer/string), gunakan nilai aslinya atau fallback
        $a['durasi_fmt'] = $durasi ?: '-';
    }

    // 3. Pastikan fallback untuk field lainnya tetap ada
    return [
        'id'              => $a['id'] ?? null,
        'kategori'        => $a['kategori'] ?? 'main',
        'jam_mulai_fmt'   => $a['jam_mulai_fmt'],
        'jam_selesai_fmt' => $a['jam_selesai_fmt'],
        'durasi_fmt'      => $a['durasi_fmt'],
        'mood'            => $a['mood'] ?? '',
        'deskripsi'       => $a['deskripsi'] ?? '',
        'foto_url'        => $a['foto_url'] ?? ($a['foto'] ?? ''),
        'lokasi'          => $a['lokasi'] ?? '',
        'lat'             => $a['lat'] ?? '',
        'lng'             => $a['lng'] ?? '',
        'nanny_name'      => $a['nanny_name'] ?? ($a['nanny'] ?? ''),
    ];
}
}