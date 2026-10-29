<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 8 — Weekly Report PDF (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → lihat daftar report mingguan → generate/regenerate.
 * - Majikan(role 2): pilih anak → daftar report (read-only) + download PDF.
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (WeeklyReportResource):
 *   GET  weekly-reports?id_anak&week_start&per_page → { data: [...], meta }
 *   POST weekly-reports/generate  { id_anak, week_start }
 *   POST weekly-reports/{id}/regenerate
 *   GET  weekly-reports/{id}/download (PDF stream)
 */
class WeeklyReportController extends Controller
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
                        'id'     => $id,
                        'nama'   => $anak['nama'] ?? 'Child',
                        'foto'   => $anak['foto'] ?? null,
                        'gender' => $anak['gender'] ?? null,
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
                return (string) ($c['nama'] ?? 'Child');
            }
        }
        return 'Child';
    }

    /** Ambil daftar report mingguan anak. Backend returns paginated {data,meta}. */
    private function fetchReports(int $idAnak, int $page = 1, int $perPage = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/weekly-reports'), [
                'id_anak'  => $idAnak,
                'page'     => $page,
                'per_page' => $perPage,
            ]);

        if (!$response->successful()) {
            return ['records' => [], 'pagination' => null];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return ['records' => [], 'pagination' => null];
        }

        // resource wrap: backend data items + meta (meta di top-level JSON)
        $records = $data['data'] ?? $data;
        $meta    = $json['meta'] ?? [];

        return [
            'records'    => is_array($records) ? $records : [],
            'pagination' => [
                'current_page' => $meta['current_page'] ?? $page,
                'last_page'    => $meta['last_page'] ?? 1,
                'total'        => $meta['total'] ?? 0,
                'per_page'     => $meta['per_page'] ?? $perPage,
            ],
        ];
    }

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.weekly-report.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: report per anak ─────────────────────────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchReports($idAnak, $page);

        return view('nanny.weekly-report.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchNannyChildren(),
            'namaAnak'   => $this->childName($this->fetchNannyChildren(), $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: riwayat AJAX ────────────────────────────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-weekly-report-show', $idAnak);
        }
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchReports($idAnak, $page);

        return view('nanny.weekly-report._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    /** Nanny: generate report mingguan (on-demand). */
    public function generate(Request $request)
    {
        $request->validate([
            'id_anak'   => 'required|integer',
            'week_start'=> 'required|date_format:Y-m-d',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/weekly-reports/generate'), [
                'id_anak'   => $request->id_anak,
                'week_start'=> $request->week_start,
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-weekly-report-show', $request->id_anak)
                ->with('success', 'Weekly report generated.');
        }

        return redirect()->route('nanny-weekly-report-show', $request->id_anak)
            ->with('error', $this->extractMessage($response));
    }

    /** Proxy — regenerate report yang sudah ada. */
    public function regenerate(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/weekly-reports/' . $id . '/regenerate'));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            $idAnak = (int) ($json['data']['id_anak'] ?? $request->input('id_anak', 0));
            return redirect()->route('nanny-weekly-report-show', $idAnak ?: $request->input('id_anak', 1))
                ->with('success', 'Weekly report regenerated.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    /** Proxy — stream PDF dari backend dengan disposition tertentu (inline=lihat, attachment=unduh). */
    private function servePdf($response, string $disposition)
    {
        if (!$response->successful() || stripos($response->header('Content-Type'), 'application/pdf') === false) {
            return null;
        }
        $res = $response->header('Content-Disposition');
        $filename = preg_match('/filename="?([^";]+)"?/', $res ?: '', $m) ? $m[1] : 'weekly_report.pdf';

        return response($response->body(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
        ]);
    }

    /** Proxy — download PDF. */
    public function download(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/weekly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'attachment');
        if ($pdf) {
            return $pdf;
        }

        $json = $response->json();
        return redirect()->back()->with('error', is_array($json) ? $this->extractMessage($response) : 'Failed to download PDF.');
    }

    /** Nanny: lihat PDF di dalam aplikasi (inline, dipakai iframe modal). */
    public function viewPdf(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/weekly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'inline');
        if ($pdf) {
            return $pdf;
        }
        abort(404, 'PDF tidak tersedia.');
    }

    // ─── Majikan: pilih anak ────────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.weekly-report.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: report per anak (read-only) ───────────────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchReports($idAnak, $page);

        return view('majikan.weekly-report.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchMajikanChildren(),
            'namaAnak'   => $this->childName($this->fetchMajikanChildren(), $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: riwayat AJAX ──────────────────────────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-weekly-report-show', $idAnak);
        }
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchReports($idAnak, $page);

        return view('majikan.weekly-report._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    /** Majikan: download PDF (read-only). */
    public function majikanDownload(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/weekly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'attachment');
        if ($pdf) {
            return $pdf;
        }

        return redirect()->back()->with('error', 'Failed to download PDF.');
    }

    /** Majikan: lihat PDF di dalam aplikasi (inline, dipakai iframe modal). */
    public function majikanViewPdf(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/weekly-reports/' . $id . '/download'));

        $pdf = $this->servePdf($response, 'inline');
        if ($pdf) {
            return $pdf;
        }
        abort(404, 'PDF tidak tersedia.');
    }
}