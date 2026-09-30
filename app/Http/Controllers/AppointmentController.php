<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 15 — Appointment & Extracurricular (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → lihat jadwal → tambah/ubah/hapus appointment.
 * - Majikan(role 2): pilih anak → lihat jadwal (read-only) + upcoming.
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (ChildAppointmentResource):
 *   GET    children/{id_anak}/appointments          → { data: { data: [ {...} ], meta } }  (paginated)
 *   GET    children/{id_anak}/appointments/upcoming → { data: [ {...} ] }                    (non-paginated)
 *   POST   child-appointments
 *   PUT    child-appointments/{id}
 *   DELETE child-appointments/{id}
 */
class AppointmentController extends Controller
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
                $anak = (array) $anak;
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

    /** Ambil daftar appointment per anak (paginated, urut schedule_at desc). */
    private function fetchRecords(int $idAnak, int $page = 1, int $perPage = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/appointments'), [
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

    /** Ambil upcoming appointments (schedule_at > now, ascending, non-paginated). */
    private function fetchUpcoming(int $idAnak, int $limit = 20): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/appointments/upcoming'), [
                'limit' => $limit,
            ]);

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        $data = $json['data'] ?? [];
        if (!is_array($data) || !$this->isSuccess($json)) {
            return [];
        }
        return is_array($data) ? $data : [];
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

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.appointment.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: jadwal per anak ─────────────────────────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);
        $anakList = $this->fetchNannyChildren();

        return view('nanny.appointment.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $anakList,
            'namaAnak'   => $this->childName($anakList, $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
            'upcoming'   => $this->fetchUpcoming($idAnak),
        ]);
    }

    // ─── Nanny: riwayat (AJAX partial, paginated) ───────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-appointment-show', $idAnak);
        }

        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('nanny.appointment._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: form tambah appointment ─────────────────────────────────────

    public function nannyCreate(int $idAnak)
    {
        $anakList = $this->fetchNannyChildren();

        return view('nanny.appointment.create', [
            'idAnak'   => $idAnak,
            'namaAnak' => $this->childName($anakList, $idAnak),
        ]);
    }

    // ─── Nanny: form edit appointment ───────────────────────────────────────

    public function nannyEdit(int $idAnak, int $id)
    {
        $anakList = $this->fetchNannyChildren();
        $record = null;

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/child-appointments/' . $id));

        if ($response->successful()) {
            $json = $response->json();
            $data = $json['data'] ?? null;
            if (is_array($data) && $this->isSuccess($json)) {
                $record = $data;
            }
        }

        if (!$record) {
            return redirect()->route('nanny-appointment-show', $idAnak)
                ->with('error', 'Appointment tidak ditemukan.');
        }

        return view('nanny.appointment.edit', [
            'idAnak'   => $idAnak,
            'namaAnak' => $this->childName($anakList, $idAnak),
            'record'   => $record,
        ]);
    }

    // ─── Nanny: simpan appointment ─────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'                 => 'required|integer',
            'type'                    => 'required|string|in:extracurricular,doctor,therapy,school_event',
            'title'                   => 'required|string|max:255',
            'schedule_at'             => 'required|date',
            'location'                => 'nullable|string|max:255',
            'notes'                   => 'nullable|string',
            'reminder_before_minutes' => 'nullable|integer|min:0',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/child-appointments'), [
                'id_anak'                 => $request->id_anak,
                'type'                    => $request->type,
                'title'                   => $request->title,
                'schedule_at'             => $request->schedule_at,
                'location'                => $request->filled('location') ? $request->location : null,
                'notes'                   => $request->filled('notes') ? $request->notes : null,
                'reminder_before_minutes' => $request->filled('reminder_before_minutes') ? (int) $request->reminder_before_minutes : 60,
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-appointment-show', $request->id_anak)
                ->with('success', 'Appointment saved.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: update appointment ─────────────────────────────────────────

    public function update(Request $request, int $id)
    {
        $request->validate([
            'id_anak'                 => 'required|integer',
            'type'                    => 'required|string|in:extracurricular,doctor,therapy,school_event',
            'title'                   => 'required|string|max:255',
            'schedule_at'             => 'required|date',
            'location'                => 'nullable|string|max:255',
            'notes'                   => 'nullable|string',
            'reminder_before_minutes' => 'nullable|integer|min:0',
        ]);

        $response = Http::withHeaders($this->headers())
            ->put($this->apiUrl('/child-appointments/' . $id), [
                'type'                    => $request->type,
                'title'                   => $request->title,
                'schedule_at'             => $request->schedule_at,
                'location'                => $request->filled('location') ? $request->location : null,
                'notes'                   => $request->filled('notes') ? $request->notes : null,
                'reminder_before_minutes' => $request->filled('reminder_before_minutes') ? (int) $request->reminder_before_minutes : 60,
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-appointment-show', $request->id_anak)
                ->with('success', 'Appointment updated.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: hapus appointment ──────────────────────────────────────────

    public function destroy(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/child-appointments/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->back()->with('success', 'Appointment deleted.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Majikan: pilih anak ────────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.appointment.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: jadwal per anak (read-only) ──────────────────────────────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);
        $anakList = $this->fetchMajikanChildren();

        return view('majikan.appointment.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $anakList,
            'namaAnak'   => $this->childName($anakList, $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
            'upcoming'   => $this->fetchUpcoming($idAnak),
        ]);
    }

    // ─── Majikan: riwayat (AJAX partial, paginated) ─────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-appointment-show', $idAnak);
        }

        $page = max(1, (int) $request->input('page', 1));
        $result = $this->fetchRecords($idAnak, $page);

        return view('majikan.appointment._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }
}