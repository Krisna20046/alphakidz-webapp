<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 6 — Assistant Notes (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → lihat riwayat catatan harian → tambah/hapus catatan.
 * - Majikan(role 2): pilih anak → lihat riwayat (read-only).
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (AssistantNoteResource):
 *   GET    children/{id_anak}/assistant-notes → { data: [ {...} ] }  (paginated: data['data']+data['meta'])
 *   POST   assistant-notes
 *   DELETE assistant-notes/{id}
 */
class AssistantNoteController extends Controller
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
                        'id'    => $id,
                        'nama'  => $anak['nama'] ?? 'Child',
                        'foto'  => $anak['foto'] ?? null,
                        'gender'=> $anak['gender'] ?? null,
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

    private function fetchRecords(int $idAnak, int $page = 1, int $perPage = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/assistant-notes'), [
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

        // Paginated shape: data['data'] + data['meta']; non-paginated: data[] langsung
        if (is_array($data) && array_key_exists('data', $data)) {
            $meta = $data['meta'] ?? [];
            return [
                'records'    => is_array($data['data']) ? $data['data'] : [],
                'pagination' => [
                    'current_page' => $meta['current_page'] ?? 1,
                    'last_page'    => $meta['last_page'] ?? 1,
                    'total'        => $meta['total'] ?? 0,
                    'per_page'     => $meta['per_page'] ?? $perPage,
                ],
            ];
        }

        return ['records' => is_array($data) ? $data : [], 'pagination' => null];
    }

    /** Nanny: daftar tugas anak (optional, utk memilih konteks task pada form). */
    private function fetchChildTasks(int $idAnak): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/academic-tasks'), ['per_page' => 100]);

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        $rows = is_array($data) && array_key_exists('data', $data) ? ($data['data'] ?? []) : $data;

        $tasks = [];
        foreach ((is_array($rows) ? $rows : []) as $t) {
            $id = (int) ($t['id'] ?? 0);
            if ($id > 0) {
                $tasks[] = ['id' => $id, 'title' => (string) ($t['title'] ?? 'Task #' . $id)];
            }
        }
        return $tasks;
    }

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.assistant-notes.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: riwayat catatan per anak ────────────────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('nanny.assistant-notes.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchNannyChildren(),
            'namaAnak'   => $this->childName($this->fetchNannyChildren(), $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: riwayat (AJAX partial, paginated) ───────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-notes-show', $idAnak);
        }

        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('nanny.assistant-notes._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: form tambah catatan ─────────────────────────────────────────

    public function nannyCreate(int $idAnak)
    {
        return view('nanny.assistant-notes.create', [
            'idAnak'   => $idAnak,
            'namaAnak' => $this->childName($this->fetchNannyChildren(), $idAnak),
            'tasks'    => $this->fetchChildTasks($idAnak),
        ]);
    }

    // ─── Nanny: simpan catatan ──────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'        => 'required|integer',
            'task_id'        => 'nullable|integer',
            'mood'           => 'required|string|in:senang,sedih,marah,biasa',
            'highlight'      => 'nullable|string|max:2000',
            'concern'        => 'nullable|string|max:2000',
            'recommendation' => 'nullable|string|max:2000',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/assistant-notes'), [
                'id_anak'        => $request->id_anak,
                'task_id'        => $request->filled('task_id') ? (int) $request->task_id : null,
                'mood'           => $request->mood,
                'highlight'      => (string) ($request->highlight ?? ''),
                'concern'        => (string) ($request->concern ?? ''),
                'recommendation' => (string) ($request->recommendation ?? ''),
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-notes-show', $request->id_anak)
                ->with('success', 'Assistant note saved.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: hapus catatan ───────────────────────────────────────────────

    public function destroy(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/assistant-notes/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->back()->with('success', 'Assistant note deleted.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Majikan: pilih anak ────────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.assistant-notes.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: riwayat catatan per anak (read-only) ──────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('majikan.assistant-notes.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $this->fetchMajikanChildren(),
            'namaAnak'   => $this->childName($this->fetchMajikanChildren(), $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: riwayat (AJAX partial, paginated) ─────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-notes-show', $idAnak);
        }

        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('majikan.assistant-notes._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
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
}
