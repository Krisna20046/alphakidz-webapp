<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SchoolScheduleController extends Controller
{
    /**
     * Proxy untuk API school-schedules (Module 2).
     * Role: read (1-4), write (1,3). Backend otomatis memfilter-membatasi
     * nanny ke anak yang di-assign aktif.
     *
     * Response backend (SchoolScheduleResource):
     *   List:   { success, message, data: { data: [ {...} ], links, meta } }
     *   Single: { success, message, data: { id, id_anak, subject_id,
     *             subject: { id,name,icon,color }, day_of_week, start_time,
     *             end_time, teacher_name, notes } }
     */

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

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $page    = (int) $request->input('page', 1);
        $day     = (string) $request->input('day', '');

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/school-schedules'), [
                'per_page' => $perPage,
                'page'     => $page,
                'day'      => $day !== '' ? $day : null,
            ]);

        $schedules  = [];
        $pagination = null;

        if ($response->successful()) {
            $json = $response->json();
            if (is_array($json) && $this->isSuccess($json)) {
                $data = $json['data'] ?? [];
                if (is_array($data) && array_key_exists('data', $data)) {
                    $schedules  = is_array($data['data']) ? $data['data'] : [];
                    $meta       = $data['meta'] ?? [];
                    $pagination = [
                        'current_page' => $meta['current_page'] ?? 1,
                        'last_page'    => $meta['last_page'] ?? 1,
                        'total'        => $meta['total'] ?? count($schedules),
                        'per_page'     => $meta['per_page'] ?? $perPage,
                    ];
                } else {
                    $schedules  = is_array($data) ? $data : [];
                    $pagination = $json['pagination'] ?? null;
                }
            }
        }

        $childNames = collect($this->fetchChildren())->pluck('nama', 'id');
        $activeDay  = $day;

        // ─── AJAX request: hanya kembalikan partial list, tanpa layout penuh ───
        if ($request->ajax() || $request->wantsJson()) {
            return view('nanny.school-schedule._list', compact(
                'schedules',
                'pagination',
                'childNames',
                'activeDay'
            ));
        }

        // Preview jadwal mingguan: ambil SEMUA jadwal (tanpa filter hari) dari endpoint yang sama,
        // karena backend mem-paginate 20 per halaman. Hanya diperlukan saat initial (non-AJAX) load,
        // karena modal preview tidak berubah oleh filter hari/pagination list.
        $previewSchedules = [];
        $previewPage = 1;
        while (true) {
            $previewResponse = Http::withHeaders($this->headers())
                ->get($this->apiUrl('/school-schedules'), ['page' => $previewPage]);

            if (!$previewResponse->successful()) {
                break;
            }

            $previewJson = $previewResponse->json();
            if (!is_array($previewJson) || !$this->isSuccess($previewJson)) {
                break;
            }

            $previewData = $previewJson['data'] ?? [];
            if (!is_array($previewData) || !array_key_exists('data', $previewData)) {
                break;
            }

            foreach ((array) ($previewData['data'] ?? []) as $previewItem) {
                $previewSchedules[] = $previewItem;
            }

            $previewMeta  = $previewData['meta'] ?? [];
            $currentPage  = (int) ($previewMeta['current_page'] ?? $previewPage);
            $lastPage     = (int) ($previewMeta['last_page'] ?? $currentPage);

            if ($currentPage >= $lastPage) {
                break;
            }

            $previewPage++;
        }

        $previewSchedules = $previewSchedules ?: $schedules;

        return view('nanny.school-schedule.index', compact(
            'schedules',
            'pagination',
            'childNames',
            'previewSchedules',
            'activeDay'
        ));
    }

    // ─── Show Detail ──────────────────────────────────────────────────────────

    public function show($id)
    {
        $schedule = $this->fetchSchedule($id);
        if (!$schedule) {
            return redirect()->route('school-schedule.index')
                ->with('error', 'Failed to load schedule data.');
        }

        $childNames = collect($this->fetchChildren())->pluck('nama', 'id');

        return view('nanny.school-schedule.show', compact('schedule', 'childNames'));
    }

    // ─── Create Form ──────────────────────────────────────────────────────────

    public function create()
    {
        return view('nanny.school-schedule.create', [
            'children' => $this->fetchChildren(),
            'subjects' => $this->fetchSubjects(),
        ]);
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'      => 'required|integer',
            'subject_id'   => 'required|integer',
            'day_of_week'  => 'required|string|max:20',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'teacher_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $response = Http::withHeaders($this->headers())
            ->asForm()
            ->post($this->apiUrl('/school-schedules'), [
                'id_anak'      => $request->id_anak,
                'subject_id'   => $request->subject_id,
                'day_of_week'  => $request->day_of_week,
                'start_time'   => $request->start_time,
                'end_time'     => $request->end_time,
                'teacher_name' => (string) ($request->teacher_name ?? ''),
                'notes'        => (string) ($request->notes ?? ''),
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('school-schedule.index')
                ->with('success', 'Schedule added successfully.');
        }

        return back()
            ->with('error', $this->extractMessage($response))
            ->withInput();
    }

    // ─── Edit Form ────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $schedule = $this->fetchSchedule($id);
        if (!$schedule) {
            return redirect()->route('school-schedule')
                ->with('error', 'Failed to load schedule data.');
        }

        return view('nanny.school-schedule.edit', [
            'schedule' => $schedule,
            'children' => $this->fetchChildren(),
            'subjects' => $this->fetchSubjects(),
        ]);
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_anak'      => 'required|integer',
            'subject_id'   => 'required|integer',
            'day_of_week'  => 'required|string|max:20',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'teacher_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $response = Http::withHeaders($this->headers())
            ->asForm()
            ->put($this->apiUrl('/school-schedules/' . $id), [
                'id_anak'      => $request->id_anak,
                'subject_id'   => $request->subject_id,
                'day_of_week'  => $request->day_of_week,
                'start_time'   => $request->start_time,
                'end_time'     => $request->end_time,
                'teacher_name' => (string) ($request->teacher_name ?? ''),
                'notes'        => (string) ($request->notes ?? ''),
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('school-schedule.index')
                ->with('success', 'Schedule updated successfully.');
        }

        return back()
            ->with('error', $this->extractMessage($response))
            ->withInput();
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/school-schedules/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('school-schedule.index')
                ->with('success', 'Schedule deleted successfully.');
        }

        return back()->with('error', $this->extractMessage($response));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function fetchSchedule($id): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/school-schedules/' . $id));

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return null;
        }
        $schedule = $json['data'] ?? null;
        return is_array($schedule) ? $schedule : null;
    }

    /**
     * Daftar anak yang di-assign ke nanny (untuk dropdown id_anak).
     * Response (getAnakForNanny):
     *   { status: 'success', data: [ { id_assignment, majikan_name, anak: [ { id, nama, ... } ] } ] }
     */
    private function fetchChildren(): array
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
                $children[] = [
                    'id'   => (int) ($anak['id'] ?? 0),
                    'nama' => $anak['nama'] ?? 'Child',
                ];
            }
        }
        // Dedup kalau anak muncul di lebih dari satu assignment
        return collect($children)->unique('id')->values()->all();
    }

    /**
     * Daftar mata pelajaran untuk dropdown subject_id.
     * Ambil halaman pertama (20) sudah cukup untuk form.
     */
    private function fetchSubjects(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/school-subjects'), ['per_page' => 20]);

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        // Bentuk paginated Laravel resource: items di data['data']
        if (array_key_exists('data', $data)) {
            return is_array($data['data']) ? $data['data'] : [];
        }
        return is_array($data) ? $data : [];
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
}