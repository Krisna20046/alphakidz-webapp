<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 10 — Assistant Attendance (frontend proxy).
 *
 * - Nanny  (role 3): choose child → today attendance for that child's assignment
 *                    → Check-in / Check-out (GPS + foto bukti optional) → history.
 * - Majikan(role 2): choose child → read-only today attendance + history.
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (AssistantAttendanceResource):
 *   POST assistant-attendance/check-in  { id_assignment, checkin_time, lat, lng, status?, notes?, location_photo? }
 *   POST assistant-attendance/check-out { checkout_time, notes?, checkout_photo? }
 *   GET  assistant-attendance/today     ?date&id_anak → { data: { date, data: [...] } }
 *   GET  assistant-attendance           ?from&to&id_anak&per_page → paginated history
 */
class AssistantAttendanceController extends Controller
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

    /**
     * Nanny: assignment aktif miliknya (maks 1) + nama majikan. Asumsi bisnis:
     * nanny punya 1 majikan → 1 assignment aktif → anaknya. Kalau ternyata
     * assignment aktif >1, UI memakai assignment pertama (jarang terjadi).
     */
    private function nannyContext(): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/nanny-assignments-anak-for-nanny'));

        if (!$response->successful()) {
            return null;
        }
        $body = $response->json();
        $data = $body['data'] ?? null;
        if (!is_array($data) || !$this->isSuccess($body) || count($data) === 0) {
            return null;
        }

        // Asumsi bisnis: nanny punya 1 majikan → 1 assignment aktif → pakai yang pertama.
        $assignment = $data[0];
        $anak = $assignment['anak'] ?? null;
        if (!is_array($anak) || count($anak) === 0) {
            return null;
        }
        $first = $anak[0];

        return [
            'id_assignment' => (int) ($assignment['id_assignment'] ?? 0),
            'majikan_name'  => $assignment['majikan_name'] ?? null,
            'anak_id'       => (int) ($first['id'] ?? 0),
            'anak_nama'     => $first['nama'] ?? 'Child',
            'anak_foto'     => $first['foto'] ?? null,
        ];
    }

    /** Majikan: children miliknya (bisa >1 nanny — jadi tetap pilih anak). */
    private function majikanChildren(): array
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

    /** Today's attendance rows: majikan → id_anak (nanny scoped sendiri backend). */
    private function fetchToday(?int $idAnak = null, ?string $date = null): array
    {
        $params = [];
        if ($idAnak) {
            $params['id_anak'] = $idAnak;
        }
        if ($date) {
            $params['date'] = $date;
        }
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/assistant-attendance/today'), $params);

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        return is_array($data['data'] ?? null) ? $data['data'] : [];
    }

    /** Paginated history: majikan → id_anak; nanny (role 3) → scoped sendiri backend. */
    private function fetchHistory(int $page = 1, int $perPage = 10, ?int $idAnak = null): array
    {
        $params = [
            'page'     => $page,
            'per_page' => $perPage,
        ];
        if ($idAnak) {
            $params['id_anak'] = $idAnak;
        }
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/assistant-attendance'), $params);

        if (!$response->successful()) {
            return ['records' => [], 'pagination' => null];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return ['records' => [], 'pagination' => null];
        }

        $records = $data['data'] ?? [];

        // meta bisa nest data['meta'] atau top-level json['meta']
        $meta = (is_array($data) && isset($data['meta']) && is_array($data['meta']))
            ? $data['meta']
            : ($json['meta'] ?? []);

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

    // ─── Nanny: tanpa pilih anak (1 majikan → 1 assignment) ───────────────

    public function nannyIndex(Request $request)
    {
        // AJAX refresh of the "ticket" card: server re-renders today + no-assignment block only.
        if ($request->ajax() || $request->wantsJson()) {
            $ctx   = $this->nannyContext();
            $today = $ctx ? $this->fetchToday() : [];

            return response()->json([
                'success' => true,
                'html'    => view('nanny.attendance._ticket', [
                    'ctx'   => $ctx,
                    'today' => $today,
                ])->render(),
            ]);
        }

        $ctx = $this->nannyContext();
        if (!$ctx) {
            return view('nanny.attendance.show', [
                'ctx'        => null,
                'today'      => [],
                'records'    => [],
                'pagination' => null,
            ]);
        }

        $page   = max(1, (int) request()->input('page', 1));
        $today  = $this->fetchToday();
        $history = $this->fetchHistory($page);

        return view('nanny.attendance.show', [
            'ctx'        => $ctx,
            'today'      => $today,
            'records'    => $history['records'],
            'pagination' => $history['pagination'],
        ]);
    }

    // ─── Nanny: history AJAX (self) ───────────────────────────────────────

    public function nannyHistory(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-attendance');
        }
        $page    = max(1, (int) $request->input('page', 1));
        $history = $this->fetchHistory($page);

        return view('nanny.attendance._history', [
            'records'    => $history['records'],
            'pagination' => $history['pagination'],
        ]);
    }

    // ─── Nanny: check-in / check-out via AJAX (JSON) ──────────────────────
    // Frontend (nanny/attendance/show.blade.php) posts with FormData → proxies
    // straight to backend API; auth token stays server-side in session.

    public function checkInAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'checkin_time'   => 'required|date_format:Y-m-d H:i:s',
            'lat'            => 'required|numeric',
            'lng'            => 'required|numeric',
            'id_nanny'       => 'nullable|integer',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $ctx = $this->nannyContext();
        if (!$ctx || $ctx['id_assignment'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada penugasan aktif untuk check-in.',
            ], 400);
        }

        $multipart = [
            ['name' => 'id_assignment', 'contents' => (string) $ctx['id_assignment']],
            ['name' => 'checkin_time',  'contents' => (string) $request->checkin_time],
            ['name' => 'lat',           'contents' => (string) $request->lat],
            ['name' => 'lng',           'contents' => (string) $request->lng],
            ['name' => 'notes',         'contents' => (string) ($request->notes ?? '')],
        ];
        if ($request->filled('id_nanny')) {
            $multipart[] = ['name' => 'id_nanny', 'contents' => (string) $request->id_nanny];
        }
        if ($request->hasFile('location_photo')) {
            $file = $request->file('location_photo');
            $multipart[] = [
                'name'     => 'location_photo',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->asMultipart()
            ->post($this->apiUrl('/assistant-attendance/check-in'), $multipart);

        $status = $response->successful() ? 201 : ($response->failed() ? $response->status() : 500);

        return response()->json([
            'success' => $response->successful() && $this->isSuccess($response->json() ?? []),
            'message' => $this->extractMessage($response),
        ], $status);
    }

    public function checkOutAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'checkout_time'  => 'required|date_format:Y-m-d H:i:s',
            'id_nanny'       => 'nullable|integer',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $multipart = [
            ['name' => 'checkout_time', 'contents' => (string) $request->checkout_time],
            ['name' => 'notes',         'contents' => (string) ($request->notes ?? '')],
        ];
        if ($request->filled('id_nanny')) {
            $multipart[] = ['name' => 'id_nanny', 'contents' => (string) $request->id_nanny];
        }
        if ($request->hasFile('checkout_photo')) {
            $file = $request->file('checkout_photo');
            $multipart[] = [
                'name'     => 'checkout_photo',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->asMultipart()
            ->post($this->apiUrl('/assistant-attendance/check-out'), $multipart);

        $status = $response->successful() ? 200 : ($response->failed() ? $response->status() : 500);

        return response()->json([
            'success' => $response->successful() && $this->isSuccess($response->json() ?? []),
            'message' => $this->extractMessage($response),
        ], $status);
    }

    // ─── Nanny: check-in ──────────────────────────────────────────────────

    public function checkIn(Request $request)
    {
        // AJAX callers (nanny page, FormData POST) need JSON for success/error, not a redirect.
        if ($request->ajax() || $request->wantsJson()) {
            return $this->checkInAjax($request);
        }

        $request->validate([
            'checkin_time'   => 'required|date_format:Y-m-d H:i:s',
            'lat'            => 'required|numeric',
            'lng'            => 'required|numeric',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $ctx = $this->nannyContext();
        if (!$ctx || $ctx['id_assignment'] <= 0) {
            return redirect()->route('nanny-attendance')->with('error', 'Tidak ada penugasan aktif untuk check-in.');
        }

        // Body dikirim sebagai multipart array (pola NannyController@store yg terbukti jalan),
        // file upload via fopen(resource) → lebih andal daripada attach(UploadedFile).
        $multipart = [
            ['name' => 'id_assignment', 'contents' => (string) $ctx['id_assignment']],
            ['name' => 'checkin_time',  'contents' => (string) $request->checkin_time],
            ['name' => 'lat',           'contents' => (string) $request->lat],
            ['name' => 'lng',           'contents' => (string) $request->lng],
            ['name' => 'notes',         'contents' => (string) ($request->notes ?? '')],
        ];

        if ($request->hasFile('location_photo')) {
            $file = $request->file('location_photo');
            $multipart[] = [
                'name'     => 'location_photo',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/assistant-attendance/check-in'), $multipart);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-attendance')->with('success', 'Check-in saved.');
        }

        return redirect()->route('nanny-attendance')->with('error', $this->extractMessage($response));
    }

    // ─── Nanny: check-out ─────────────────────────────────────────────────

    public function checkOut(Request $request)
    {
        // AJAX callers (nanny page, FormData POST) need JSON for success/error, not a redirect.
        if ($request->ajax() || $request->wantsJson()) {
            return $this->checkOutAjax($request);
        }

        $request->validate([
            'checkout_time'  => 'required|date_format:Y-m-d H:i:s',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $multipart = [
            ['name' => 'checkout_time', 'contents' => (string) $request->checkout_time],
            ['name' => 'notes',         'contents' => (string) ($request->notes ?? '')],
        ];

        if ($request->hasFile('checkout_photo')) {
            $file = $request->file('checkout_photo');
            $multipart[] = [
                'name'     => 'checkout_photo',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/assistant-attendance/check-out'), $multipart);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-attendance')->with('success', 'Check-out saved.');
        }

        return redirect()->route('nanny-attendance')->with('error', $this->extractMessage($response));
    }

    // ─── Majikan: choose child ────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.attendance.index', [
            'anakList' => $this->majikanChildren(),
        ]);
    }

    // ─── Majikan: today attendance (read-only) ────────────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page    = max(1, (int) $request->input('page', 1));
        $result  = $this->fetchToday($idAnak);
        $history = $this->fetchHistory($page, 10, $idAnak);

        return view('majikan.attendance.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->majikanChildren(),
            'namaAnak'   => $this->childName($this->majikanChildren(), $idAnak),
            'today'      => $result,
            'records'    => $history['records'],
            'pagination' => $history['pagination'],
        ]);
    }

    // ─── Majikan: history AJAX (read-only) ────────────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-attendance-show', $idAnak);
        }
        $page    = max(1, (int) $request->input('page', 1));
        $history = $this->fetchHistory($page, 10, $idAnak);

        return view('majikan.attendance._history', [
            'idAnak'     => $idAnak,
            'records'    => $history['records'],
            'pagination' => $history['pagination'],
        ]);
    }
}
