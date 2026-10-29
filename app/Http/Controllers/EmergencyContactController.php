<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 18 — Emergency Contact (frontend proxy).
 *
 * - Nanny  (role 3): pilih anak → daftar kontakti kurgensi → tambah/ubah/hapus kontakti.
 * - Majikan(role 2): pilih anak → daftar kontakti kurgensi (read-only, tetap bisa tap-to-call).
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (EmergencyContactResource):
 *   GET    children/{id_anak}/emergency-contacts → { data: { data: [..], meta } }
 *   GET    children/{id_anak}/emergency-contacts/quick-access → { data: [..] }
 *   POST   emergency-contacts
 *   PUT    emergency-contacts/{id}
 *   DELETE emergency-contacts/{id}
 */
class EmergencyContactController extends Controller
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

    private function childName(array $children, int $idAnak): string
    {
        foreach ($children as $c) {
            if ((int) ($c['id'] ?? 0) === $idAnak) {
                return (string) ($c['nama'] ?? 'Child');
            }
        }
        return 'Child';
    }

    /** Daftar kontakti kurgensi per anak (paginated). */
    private function fetchContacts(int $idAnak, int $page = 1, int $perPage = 10): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/emergency-contacts'), [
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

    /** Fetch single contact via full list (backend has no show web-proxy need; reuse list). */
    private function fetchContact(int $idAnak, int $id): ?array
    {
        $result = $this->fetchContacts($idAnak, 1, 100);
        foreach ($result['records'] as $c) {
            if ((int) ($c['id'] ?? 0) === $id) {
                return $c;
            }
        }
        return null;
    }

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.emergency-contact.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: daftar kontakti kurgensi per anak ──────────────────────────

    public function nannyShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchContacts($idAnak, $page);
        $anakList = $this->fetchNannyChildren();

        return view('nanny.emergency-contact.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $anakList,
            'namaAnak'   => $this->childName($anakList, $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Nanny: riwayat (AJAX partial, paginated) ──────────────────────────

    public function nannyHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('nanny-emergency-contacts-show', $idAnak);
        }

        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchContacts($idAnak, $page);

        return view('nanny.emergency-contact._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
            'canEdit'    => true,
        ]);
    }

    // ─── Nanny: form tambah kontakti ────────────────────────────────────────

    public function nannyCreate(int $idAnak)
    {
        $anakList = $this->fetchNannyChildren();

        return view('nanny.emergency-contact.create', [
            'idAnak'    => $idAnak,
            'namaAnak'  => $this->childName($anakList, $idAnak),
            'priorities'=> $this->priorityOptions(),
            'old'       => (object) [],
        ]);
    }

    // ─── Nanny: form ubah kontak ────────────────────────────────────────────

    public function nannyEdit(int $idAnak, int $id)
    {
        $anakList = $this->fetchNannyChildren();
        $contact  = $this->fetchContact($idAnak, $id);

        if (empty($contact)) {
            return redirect()->route('nanny-emergency-contacts-show', $idAnak)
                ->with('error', 'Contact tidak ditemukan.');
        }

        return view('nanny.emergency-contact.edit', [
            'idAnak'    => $idAnak,
            'namaAnak'  => $this->childName($anakList, $idAnak),
            'contact'   => $contact,
            'can'       => $this->priorityOptions(),
        ]);
    }

    // ─── Nanny: simpan kontak ──────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'        => 'required|integer',
            'name'           => 'required|string|max:255',
            'relationship'   => 'nullable|string|max:100',
            'phone'          => 'required|string|max:30',
            'priority_order' => 'nullable|integer|min:1|max:100',
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/emergency-contacts'), [
                'id_anak'        => $request->id_anak,
                'name'           => $request->name,
                'relationship'   => (string) ($request->relationship ?? ''),
                'phone'          => $request->phone,
                'priority_order' => $request->filled('priority_order') ? (int) $request->priority_order : null,
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-emergency-contacts-show', $request->id_anak)
                ->with('success', 'Emergency contact saved.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: perbarui kontak ────────────────────────────────────────────

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'relationship'   => 'nullable|string|max:100',
            'phone'          => 'sometimes|required|string|max:30',
            'priority_order' => 'nullable|integer|min:1|max:100',
        ]);

        $response = Http::withHeaders($this->headers())
            ->put($this->apiUrl('/emergency-contacts/' . $id), [
                'name'           => $request->name,
                'relationship'   => (string) ($request->relationship ?? ''),
                'phone'          => $request->phone,
                'priority_order' => $request->filled('priority_order') ? (int) $request->priority_order : null,
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('nanny-emergency-contacts-show', (int) request()->input('id_anak'))
                ->with('success', 'Emergency contact updated.');
        }

        return back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: hapus kontak ──────────────────────────────────────────────

    public function destroy(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/emergency-contacts/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->back()->with('success', 'Emergency contact deleted.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Majikan: pilih anak ───────────────────────────────────────────────

    public function majikanIndex()
    {
        return view('majikan.emergency-contact.index', [
            'anakList' => $this->fetchMajikanChildren(),
        ]);
    }

    // ─── Majikan: daftar kontakti per anak (read-only + tap-to-call) ───────

    public function majikanShow(Request $request, int $idAnak)
    {
        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchContacts($idAnak, $page);
        $anakList = $this->fetchMajikanChildren();

        return view('majikan.emergency-contact.show', [
            'idAnak'     => $idAnak,
            'anakList'   => $anakList,
            'namaAnak'   => $this->childName($anakList, $idAnak),
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
        ]);
    }

    // ─── Majikan: riwayat (AJAX partial, paginated) ────────────────────────

    public function majikanHistory(Request $request, int $idAnak)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('majikan-emergency-contacts-show', $idAnak);
        }

        $page   = max(1, (int) $request->input('page', 1));
        $result = $this->fetchContacts($idAnak, $page);

        return view('majikan.emergency-contact._history', [
            'idAnak'     => $idAnak,
            'records'    => $result['records'],
            'pagination' => $result['pagination'],
            'canEdit'    => false,
        ]);
    }

    /** Untung select priority: option 1..5 + auto. */
    private function priorityOptions(): array
    {
        return [
            ['value' => '', 'label' => 'Auto (next)'],
            ['value' => 1, 'label' => '1 — Most urgent'],
            ['value' => 2, 'label' => '2'],
            ['value' => 3, 'label' => '3'],
            ['value' => 4, 'label' => '4'],
            ['value' => 5, 'label' => '5 — Least urgent'],
        ];
    }
}