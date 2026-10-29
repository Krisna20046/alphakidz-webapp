<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AcademicTaskController extends Controller
{
    /**
     * Proxy untuk API academic-tasks (Module 3) & task-progress (Module 4).
     * Ditujukan utk role Nanny (read & write) — backend tetap melakukan
     * validasi child-access (nanny hanya bisa akses anak yg di-assign aktif).
     *
     * Response backend (AcademicTaskResource):
     *   List (paginated): { success, message, data: { data: [ {...} ], meta } }
     *   Single:           { success, message, data: { id, id_anak, id_assignment,
     *                       subject_id, subject: {...}, type, title, description,
     *                       deadline, status, priority, score, attachment,
     *                       created_by, updated_by, completed_at, progress: [...] } }
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
     * Daftar anak yg di-assign ke nanny beserta id_assignment & status assignment
     * (untuk dropdown id_anak & hidden id_assignment).
     * Response backend (getAnakForNanny):
     *   { status:'success', data: [ { id_assignment, status, majikan_name, anak:[...] } ] }
     */
    private function fetchAssignments(): array
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

        $assignments = [];
        foreach ($data as $assignment) {
            foreach (($assignment['anak'] ?? []) as $anak) {
                $assignments[] = [
                    'id_assignment' => (int) ($assignment['id_assignment'] ?? 0),
                    'status'        => (string) ($assignment['status'] ?? ''),
                    'id_anak'       => (int) ($anak['id'] ?? 0),
                    'nama'          => $anak['nama'] ?? 'Child',
                ];
            }
        }
        return $assignments;
    }

    private function fetchChildren(): array
    {
        // Unique per child; pakai assignment pertama yg aktif utk tiap anak.
        $children = [];
        foreach ($this->fetchAssignments() as $a) {
            $children[$a['id_anak']] = [
                'id'            => $a['id_anak'],
                'nama'          => $a['nama'],
                'id_assignment' => $a['id_assignment'],
            ];
        }
        return array_values($children);
    }

    private function fetchSubjects(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/school-subjects'), ['per_page' => 100]);

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        if (array_key_exists('data', $data)) {
            return is_array($data['data']) ? $data['data'] : [];
        }
        return is_array($data) ? $data : [];
    }

    private function fetchTask(int $id): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/academic-tasks/' . $id));

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return null;
        }
        $task = $json['data'] ?? null;
        return is_array($task) ? $task : null;
    }

    // ─── Index (list + filter + pagination) ─────────────────────────────────

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        $page    = (int) $request->input('page', 1);
        $status  = (string) $request->input('status', '');
        $type    = (string) $request->input('type', '');
        $subject = (string) $request->input('subject_id', '');

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/academic-tasks'), array_filter([
                'per_page'   => $perPage,
                'page'       => $page,
                'status'     => $status !== '' ? $status : null,
                'type'       => $type !== '' ? $type : null,
                'subject_id' => $subject !== '' ? $subject : null,
            ], fn($v) => $v !== null));

        $tasks      = [];
        $pagination = null;

        if ($response->successful()) {
            $json = $response->json();
            if (is_array($json) && $this->isSuccess($json)) {
                $data = $json['data'] ?? [];
                if (is_array($data) && array_key_exists('data', $data)) {
                    $tasks      = is_array($data['data']) ? $data['data'] : [];
                    $meta       = $data['meta'] ?? [];
                    $pagination = [
                        'current_page' => $meta['current_page'] ?? 1,
                        'last_page'    => $meta['last_page'] ?? 1,
                        'total'        => $meta['total'] ?? count($tasks),
                        'per_page'     => $meta['per_page'] ?? $perPage,
                    ];
                } else {
                    $tasks      = is_array($data) ? $data : [];
                    $pagination = null;
                }
            }
        }

        $subjects = $this->fetchSubjects();

        if ($request->ajax() || $request->wantsJson()) {
            return view('nanny.academic-task._list', compact('tasks', 'pagination', 'status', 'type', 'subject'));
        }

        return view('nanny.academic-task.index', compact(
            'tasks', 'pagination', 'status', 'type', 'subject', 'subjects'
        ));
    }

    // ─── Create Form ─────────────────────────────────────────────────────────

    public function create()
    {
        return view('nanny.academic-task.create', [
            'children' => $this->fetchChildren(),
            'subjects' => $this->fetchSubjects(),
        ]);
    }

    // ─── Store (multipart: boleh upload attachment) ─────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'       => 'required|integer',
            'id_assignment' => 'required|integer',
            'subject_id'    => 'required|integer',
            'type'          => 'required|string|in:homework,project,exam',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'deadline'      => 'nullable|date',
            'priority'      => 'nullable|string|in:low,medium,high',
            'status'        => 'nullable|string|in:pending,in_progress,completed,overdue,cancelled',
            'score'         => 'nullable|numeric|between:0,100',
            'attachment'    => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
        ]);

        $http = Http::withHeaders($this->headers());
        if ($request->hasFile('attachment')) {
            $http = $http->attach(
                'attachment',
                file_get_contents($request->file('attachment')->getRealPath()),
                $request->file('attachment')->getClientOriginalName()
            );
        }
        $response = $http->post($this->apiUrl('/academic-tasks'), $this->taskFields($request));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('academic-task.index')
                ->with('success', 'Task added successfully.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Show Detail (incl. progress & upload progres) ──────────────────────

    public function show($id)
    {
        $task = $this->fetchTask($id);
        if (!$task) {
            return redirect()->route('academic-task.index')
                ->with('error', 'Failed to load task data.');
        }

        $childNames = collect($this->fetchChildren())->pluck('nama', 'id');

        // Alasan penolakan terbaru dari majikan (Modul 11, Opsi B: task di-reopen)
        $rejection = $this->fetchLatestRejection($task['id_anak'] ?? 0, (int) $id);

        return view('nanny.academic-task.show', compact('task', 'childNames', 'rejection'));
    }

    /**
     * Ambil riwayat parent-comment untuk satu anak, cari penolakan terbaru atas tugas ini.
     * Response backend (ParentCommentResource): list `decision` approved/rejected/pending/comment.
     */
    private function fetchLatestRejection(int $idAnak, int $taskId): ?array
    {
        if ($idAnak <= 0) {
            return null;
        }

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/parent-comments'), [
                'id_anak'  => $idAnak,
                'task_id'  => $taskId,
                'per_page' => 50,
            ]);

        if (!$response->successful()) {
            return null;
        }
        $json = $response->json();
        $data = $json['data'] ?? [];

        if (!is_array($data) || !$this->isSuccess($json)) {
            return null;
        }
        $list = is_array($data) && array_key_exists('data', $data)
            ? (is_array($data['data']) ? $data['data'] : [])
            : $data;

        // Cari reject paling baru utk tugas ini
        foreach ($list as $h) {
            if (($h['action'] ?? null) === 'reject') {
                return $h;
            }
        }
        return null;
    }

    // ─── Edit Form ───────────────────────────────────────────────────────────

    public function edit($id)
    {
        $task = $this->fetchTask($id);
        if (!$task) {
            return redirect()->route('academic-task.index')
                ->with('error', 'Failed to load task data.');
        }

        return view('nanny.academic-task.edit', [
            'task'     => $task,
            'children' => $this->fetchChildren(),
            'subjects' => $this->fetchSubjects(),
        ]);
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_anak'       => 'required|integer',
            'subject_id'    => 'required|integer',
            'type'          => 'required|string|in:homework,project,exam',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'deadline'      => 'nullable|date',
            'priority'      => 'nullable|string|in:low,medium,high',
            'status'        => 'nullable|string|in:pending,in_progress,completed,overdue,cancelled',
            'score'         => 'nullable|numeric|between:0,100',
            'attachment'    => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
        ]);

        $fields = $this->taskFields($request);
        unset($fields['id_assignment']); // assignment tidak diubah lewat form edit

        $http = Http::withHeaders($this->headers());
        if ($request->hasFile('attachment')) {
            $http = $http->attach(
                'attachment',
                file_get_contents($request->file('attachment')->getRealPath()),
                $request->file('attachment')->getClientOriginalName()
            );
        }
        $response = $http->post($this->apiUrl('/academic-tasks/' . $id . '?_method=PUT'), $fields);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('academic-task.show', $id)
                ->with('success', 'Task updated successfully.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/academic-tasks/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('academic-task.index')
                ->with('success', 'Task deleted successfully.');
        }

        return back()->with('error', $this->extractMessage($response));
    }

    // ─── Update status ───────────────────────────────────────────────────────

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,overdue,cancelled',
        ]);

        $response = Http::withHeaders($this->headers())
            ->withBody('', 'application/json') // PATCH tanpa body tambahan
            ->patch($this->apiUrl('/academic-tasks/' . $id . '/status'), [
                'status' => $request->status,
            ]);

        return $this->redirectBack($response, 'Status updated successfully.');
    }

    // ─── Mark complete ───────────────────────────────────────────────────────

    public function markComplete($id)
    {
        $response = Http::withHeaders($this->headers())
            ->patch($this->apiUrl('/academic-tasks/' . $id . '/complete'));

        return $this->redirectBack($response, 'Task completed successfully.');
    }

    // ─── Tambah progres (foto) ───────────────────────────────────────────────

    public function storeProgress(Request $request, $id)
    {
        $request->validate([
            'progress_percentage' => 'required|integer|between:0,100',
            'note'                => 'nullable|string',
            'status'              => 'nullable|string|in:pending,in_progress,completed',
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
        ]);

        $http = Http::withHeaders($this->headers())
            ->attach(
                'photo',
                $request->hasFile('photo')
                    ? file_get_contents($request->file('photo')->getRealPath())
                    : '',
                $request->hasFile('photo') ? $request->file('photo')->getClientOriginalName() : 'empty'
            );

        if (!$request->hasFile('photo')) {
            $http = Http::withHeaders($this->headers());
        }

        $response = $http->post($this->apiUrl('/task-progress'), [
            'task_id'             => $id,
            'progress_percentage' => $request->progress_percentage,
            'note'                => (string) ($request->note ?? ''),
            'status'              => (string) ($request->status ?? 'in_progress'),
        ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('academic-task.show', $id)
                ->with('success', 'Progress added successfully.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function taskFields(Request $request): array
    {
        return [
            'id_anak'       => $request->id_anak,
            'id_assignment' => $request->id_assignment,
            'subject_id'    => $request->subject_id,
            'type'          => $request->type,
            'title'         => $request->title,
            'description'   => (string) ($request->description ?? ''),
            'deadline'      => $request->deadline ? date('Y-m-d H:i:s', strtotime($request->deadline)) : null,
            'priority'      => (string) ($request->priority ?? 'medium'),
            'status'        => (string) ($request->status ?? 'pending'),
            'score'         => $request->filled('score') ? $request->score : null,
        ];
    }

    private function redirectBack($response, string $successMsg)
    {
        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->back()->with('success', $successMsg);
        }
        return redirect()->back()->with('error', $this->extractMessage($response));
    }
}
