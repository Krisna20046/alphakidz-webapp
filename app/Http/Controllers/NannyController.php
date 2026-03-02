<?php

namespace App\Http\Controllers;

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

    // ── Choose Diary: daftar anak dari penugasan nanny ─────────────────────────

    public function chooseDiary()
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

        return view('nanny.diary-choose', compact('assignmentData'));
    }

    // ── Diary: list aktivitas anak pada tanggal tertentu ──────────────────────

    public function showDiary(Request $request, int $id_anak)
    {
        $tanggal  = $request->query('tanggal', now()->format('Y-m-d'));
        $kategori = $request->query('kategori', '');

        $formData = ['id_anak' => $id_anak, 'tanggal' => $tanggal];
        if ($kategori) $formData['kategori'] = $kategori;

        $res = Http::withHeaders($this->headers())
            ->asForm()
            ->post($this->apiUrl('/diary-for-nanny'), $formData);

        $diaryData  = null;
        $aktivitas  = [];
        $idAssignment = null;

        if ($res->successful()) {
            $body = $res->json();
            if (($body['status'] ?? '') === 'success') {
                $diaryData = $body['data'];
                $idAssignment = $diaryData['id_assignment'] ?? null;
                $idAnak = $diaryData['id_anak'] ?? 0;

                // Flatten aktivitas dan format field waktu
                $rawAkt = $diaryData['aktivitas_per_tanggal'][0]['aktivitas'] ?? [];
                foreach ($rawAkt as $item) {
                    $dur     = $item['durasi'] ?? ['jam' => 0, 'menit' => 0];
                    $durFmt  = ($dur['jam'] > 0 ? $dur['jam'] . ' jam ' : '') . $dur['menit'] . ' menit';
                    $aktivitas[] = array_merge($item, [
                        'jam_mulai_fmt'  => $this->fmtJam($item['jam_mulai']  ?? ''),
                        'jam_selesai_fmt'=> $this->fmtJam($item['jam_selesai'] ?? ''),
                        'durasi_fmt'     => $durFmt,
                    ]);
                }
            }
        }

        $tanggalIndo = $this->tanggalIndo($tanggal);

        return view('nanny.diary', compact(
            'diaryData', 'aktivitas', 'idAnak', 'tanggal', 'tanggalIndo',
            'kategori', 'idAssignment'
        ) + ['idAnak' => $id_anak]);
    }

    // ── Add Diary: tampilkan form ──────────────────────────────────────────────

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

        // Build multipart array — setiap elemen harus berupa array asosiatif
        // dengan key 'name' dan 'contents' (Laravel Http::asMultipart format)
        $multipart = [
            ['name' => 'id_assignment', 'contents' => (string) $request->id_assignment],
            ['name' => 'id_anak',       'contents' => (string) $request->id_anak],
            ['name' => 'kategori',      'contents' => (string) $request->kategori],
            ['name' => 'jam_mulai',     'contents' => (string) $request->jam_mulai],
            ['name' => 'jam_selesai',   'contents' => (string) $request->jam_selesai],
            ['name' => 'mood',          'contents' => (string) ($request->mood ?? 'biasa')],
            ['name' => 'deskripsi',     'contents' => (string) ($request->deskripsi ?? '')],
        ];

        // Jika ada file foto, tambahkan sebagai stream
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
}