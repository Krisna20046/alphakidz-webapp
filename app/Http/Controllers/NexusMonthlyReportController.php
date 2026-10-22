<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 22 — Nexus Monthly Report (frontend proxy).
 *
 * PDF manual tim Nexus (berisi saran + rekap bulanan nanny & anak):
 * - Nexus  (role 5): daftar semua + upload PDF + resend + delete.
 * - Majikan(role 2): pilih anak → daftar laporan → lihat/download PDF.
 * - Nanny  (role 3): pilih anak → daftar laporan → lihat/download PDF.
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend:
 *   GET    nexus-monthly-reports?page&report_month&id_anak → { data:[...], meta }
 *   GET    nexus-monthly-reports/children                  → { data:[...] }
 *   POST   nexus-monthly-reports (multipart: id_anak, report_month, title, description, pdf)
 *   POST   nexus-monthly-reports/{id}/resend
 *   DELETE nexus-monthly-reports/{id}
 *   GET    nexus-monthly-reports/{id}/download (PDF stream)
 *   GET    children/{id_anak}/nexus-monthly-reports        → { data:[...], meta }
 */
class NexusMonthlyReportController extends Controller
{
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

    private function extractMessage($response): string
    {
        $json = $response->json();
        if (is_array($json)) {
            if (!empty($json['errors']) && is_array($json['errors'])) {
                $first = reset($json['errors']);
                return is_array($first) ? (string) reset($first) : (string) $first;
            }
            if (!empty($json['message'])) {
                return (string) $json['message'];
            }
        }
        return 'Something went wrong on the server.';
    }

    // ─── Fetch helpers ──────────────────────────────────────────────────────

    /** Nexus: daftar semua laporan (paginated). */
    private function fetchAll(int $page = 1, int $perPage = 20, ?string $reportMonth = null, ?int $idAnak = null): array
    {
        $params = ['page' => $page, 'per_page' => $perPage];
        if ($reportMonth) {
            $params['report_month'] = $reportMonth;
        }
        if ($idAnak) {
            $params['id_anak'] = $idAnak;
        }

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus-monthly-reports'), $params);

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return ['records' => [], 'meta' => null];
        }

        return [
            'records' => is_array($json['data'] ?? null) ? $json['data'] : [],
            'meta'    => is_array($json['meta'] ?? null) ? $json['meta'] : null,
        ];
    }

    /** Nexus: daftar semua anak (untuk form upload). */
    private function fetchAllChildren(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus-monthly-reports/children'));

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return [];
        }
        return is_array($json['data'] ?? null) ? $json['data'] : [];
    }

    /** Nanny: daftar anak yang di-assign aktif (untuk selector). */
    private function fetchNannyChildren(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nanny-assignments-anak-for-nanny'));

        if (!$response->successful()) {
            return [];
        }
        $body = $response->json();
        $data = $body['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($body)) {
            return [];
        }

        $children = [];
        foreach ($data as $assignment) {
            foreach (($assignment['anak'] ?? []) as $anak) {
                $id = (int) ($anak['id'] ?? 0);
                if ($id > 0) {
                    $children[$id] = [
                        'id'   => $id,
                        'nama' => $anak['nama'] ?? 'Child',
                        'foto' => $anak['foto'] ?? null,
                    ];
                }
            }
        }
        return array_values($children);
    }

    /** Majikan: daftar anak miliknya (untuk selector). */
    private function fetchMajikanChildren(): array
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

    private function childName(array $children, int $idAnak): string
    {
        foreach ($children as $c) {
            if ((int) ($c['id'] ?? 0) === $idAnak) {
                return (string) ($c['nama'] ?? '-');
            }
        }
        return '-';
    }

    /** Laporan per anak (paginated) — dipakai Majikan & Nanny. */
    private function fetchByChild(int $idAnak, int $page = 1, int $perPage = 12): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/nexus-monthly-reports'), [
                'page'     => $page,
                'per_page' => $perPage,
            ]);

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return ['records' => [], 'meta' => null];
        }

        return [
            'records' => is_array($json['data'] ?? null) ? $json['data'] : [],
            'meta'    => is_array($json['meta'] ?? null) ? $json['meta'] : null,
        ];
    }

    /** Stream PDF dari backend (inline=lihat, attachment=unduh). */
    private function servePdf($response, string $disposition)
    {
        if (!$response->successful() || stripos($response->header('Content-Type'), 'application/pdf') === false) {
            return null;
        }
        $res = $response->header('Content-Disposition');
        $filename = preg_match('/filename="?([^";]+)"?/', $res ?: '', $m) ? $m[1] : 'nexus_monthly_report.pdf';

        return response($response->body(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
        ]);
    }

    // ─── Nexus (role 5): daftar + upload + resend + delete ──────────────────

    public function nexusIndex(Request $request)
    {
        $role = (int) (session('user')['id_role'] ?? 0);
        if ($role === 2) {
            return redirect()->route('majikan-monthly-reports');
        }
        if ($role === 3) {
            return redirect()->route('nanny-monthly-reports');
        }

        $page  = max(1, (int) $request->input('page', 1));
        $month = $request->filled('report_month') ? (string) $request->input('report_month') : null;

        $result   = $this->fetchAll($page, 20, $month);
        $children = $this->fetchAllChildren();

        return view('nexus.monthly-report.index', [
            'records'     => $result['records'],
            'meta'        => $result['meta'],
            'children'    => $children,
            'filterMonth' => $month,
        ]);
    }

    /** Upload PDF (multipart → backend). Navigasi kembali + flash message. */
    public function nexusStore(Request $request)
    {
        $request->validate([
            'id_anak'      => 'required|integer',
            'report_month' => 'required',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:5000',
            'pdf'          => 'required|file|mimes:pdf|max:20480',
        ]);

        // Pola multipart terbukti (AssistantAttendanceController) — file via fopen(resource).
        $multipart = [
            ['name' => 'id_anak',      'contents' => (string) $request->id_anak],
            ['name' => 'report_month', 'contents' => (string) $request->report_month],
            ['name' => 'title',        'contents' => (string) $request->title],
            ['name' => 'description',  'contents' => (string) ($request->description ?? '')],
        ];

        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $multipart[] = [
                'name'     => 'pdf',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->asMultipart()
            ->post($this->apiUrl('/nexus-monthly-reports'), $multipart);

        if ($response->successful() && $this->isSuccess($response->json() ?? [])) {
            return redirect()->route('nexus-monthly-reports')->with('success', $this->extractMessage($response));
        }

        return redirect()->back()->withInput()->with('error', $this->extractMessage($response));
    }

    /** Kirim ulang notifikasi FCM (form POST). */
    public function nexusResend(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/nexus-monthly-reports/' . $id . '/resend'));

        if ($response->successful() && $this->isSuccess($response->json() ?? [])) {
            return redirect()->back()->with('success', $this->extractMessage($response));
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    /** Hapus laporan + file PDF (form DELETE). */
    public function nexusDestroy(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/nexus-monthly-reports/' . $id));

        if ($response->successful() && $this->isSuccess($response->json() ?? [])) {
            return redirect()->back()->with('success', $this->extractMessage($response));
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Majikan (role 2): pilih anak → daftar → PDF ────────────────────────

    public function majikanIndex()
    {
        return view('monthly-report.index', [
            'anakList'   => $this->fetchMajikanChildren(),
            'indexRoute' => 'majikan-monthly-reports',
            'showRoute'  => 'majikan-monthly-reports-show',
            'title'      => 'Laporan Bulanan',
            'subtitle'   => 'Rekap & saran bulanan dari tim Nexus',
        ]);
    }

    public function majikanShow(Request $request, int $idAnak)
    {
        $page     = max(1, (int) $request->input('page', 1));
        $children = $this->fetchMajikanChildren();
        $result   = $this->fetchByChild($idAnak, $page);

        return view('monthly-report.show', [
            'idAnak'        => $idAnak,
            'anakList'      => $children,
            'namaAnak'      => $this->childName($children, $idAnak),
            'records'       => $result['records'],
            'meta'          => $result['meta'],
            'indexRoute'    => 'majikan-monthly-reports',
            'showRoute'     => 'majikan-monthly-reports-show',
            'historyRoute'  => 'majikan-monthly-reports-history',
            'downloadRoute' => 'majikan-monthly-reports-download',
            'viewRoute'     => 'majikan-monthly-reports-view',
            'jsPrefix'      => 'mjmr',
        ]);
    }

    public function majikanHistory(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchByChild($idAnak, $page);

        return view('monthly-report._history', [
            'idAnak'   => $idAnak,
            'records'  => $result['records'],
            'meta'     => $result['meta'],
            'historyRoute' => 'majikan-monthly-reports-history',
            'downloadRoute' => 'majikan-monthly-reports-download',
            'viewRoute'    => 'majikan-monthly-reports-view',
            'jsPrefix'     => 'mjmr',
        ]);
    }

    public function majikanDownload(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus-monthly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'attachment');
        if ($pdf) {
            return $pdf;
        }

        return redirect()->back()->with('error', 'Failed to download PDF.');
    }

    public function majikanViewPdf(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus-monthly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'inline');
        if ($pdf) {
            return $pdf;
        }

        abort(404, 'PDF tidak tersedia.');
    }

    // ─── Nanny (role 3): pilih anak → daftar → PDF ──────────────────────────

    public function nannyIndex()
    {
        return view('monthly-report.index', [
            'anakList'   => $this->fetchNannyChildren(),
            'indexRoute' => 'nanny-monthly-reports',
            'showRoute'  => 'nanny-monthly-reports-show',
            'title'      => 'Laporan Bulanan',
            'subtitle'   => 'Rekap & saran bulanan dari tim Nexus',
        ]);
    }

    public function nannyShow(Request $request, int $idAnak)
    {
        $page     = max(1, (int) $request->input('page', 1));
        $children = $this->fetchNannyChildren();
        $result   = $this->fetchByChild($idAnak, $page);

        return view('monthly-report.show', [
            'idAnak'        => $idAnak,
            'anakList'      => $children,
            'namaAnak'      => $this->childName($children, $idAnak),
            'records'       => $result['records'],
            'meta'          => $result['meta'],
            'indexRoute'    => 'nanny-monthly-reports',
            'showRoute'     => 'nanny-monthly-reports-show',
            'historyRoute'  => 'nanny-monthly-reports-history',
            'downloadRoute' => 'nanny-monthly-reports-download',
            'viewRoute'     => 'nanny-monthly-reports-view',
            'jsPrefix'      => 'nvmr',
        ]);
    }

    public function nannyHistory(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchByChild($idAnak, $page);

        return view('monthly-report._history', [
            'idAnak'   => $idAnak,
            'records'  => $result['records'],
            'meta'     => $result['meta'],
            'historyRoute' => 'nanny-monthly-reports-history',
            'downloadRoute' => 'nanny-monthly-reports-download',
            'viewRoute'    => 'nanny-monthly-reports-view',
            'jsPrefix'     => 'nvmr',
        ]);
    }

    public function nannyDownload(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus-monthly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'attachment');
        if ($pdf) {
            return $pdf;
        }

        return redirect()->back()->with('error', 'Failed to download PDF.');
    }

    public function nannyViewPdf(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nexus-monthly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'inline');
        if ($pdf) {
            return $pdf;
        }

        abort(404, 'PDF tidak tersedia.');
    }
}
