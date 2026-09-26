<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 13 — Daily Checklist (frontend proxy, NANNY).
 *
 * Mode tunggal (keputusan 2026-09-25): checklist = daftar rutinitas per hari
 * Senin–Minggu (`type=weekly`), TANPA tanggal. Tiap hari punya list-nya sendiri;
 * nanny tinggal centang item yang dikerjakan hari itu. Ganti hari → ganti list.
 * Ganti minggu → semua centang otomatis di-reset (rollover di backend).
 *
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend (DayChecklistController):
 *   GET    children/{id_anak}/daily-checklists/day?type=weekly&date=
 *            → { data: { .. } | null }          (SATU checklist hari itu)
 *   POST   daily-checklists                      (buat baru / MERGE item ke hari sama)
 *   PUT    daily-checklists/{id}
 *   DELETE daily-checklists/{id}
 *   PATCH  daily-checklists/{item}/toggle-item          (param = ITEM id)
 *
 * Tipe `daily` (bertanggal) masih ada di backend utk API/mobile — web TIDAK lagi memakainya.
 */
class DailyChecklistController extends Controller
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

    private function childName(array $children, int $idAnak): string
    {
        foreach ($children as $c) {
            if ((int) ($c['id'] ?? 0) === $idAnak) {
                return (string) ($c['nama'] ?? 'Child');
            }
        }
        return 'Child';
    }

    /**
     * Semua checklist weekly utk hari 1..7 (backend weekByChild → data.days[day]).
     * Satu hari boleh punya TAKUSAN rutinitas (Pagi/Siang/Sore) — jangan cuma 1.
     */
    private function fetchDay(int $idAnak, int $day): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/children/' . $idAnak . '/daily-checklists/week'));

        if (!$response->successful()) {
            return [];
        }
        $json = $response->json();
        if (!is_array($json) || !$this->isSuccess($json)) {
            return [];
        }
        $days = (array) ($json['data']['days'] ?? []);
        $list = $days[$day] ?? [];
        return is_array($list) ? array_values($list) : [];
    }

    private function fetchChecklist(int $id): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/daily-checklists/' . $id));

        if (!$response->successful()) {
            return null;
        }
        $json = $response->json();
        $data = $json['data'] ?? null;
        return is_array($data) ? $data : null;
    }

    /** Nama hari (index 1=Senin .. 7=Minggu) — key pintar utk map days. */
    private function dayNames(): array
    {
        return [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
        ];
    }

    // ─── Nanny: pilih anak ──────────────────────────────────────────────────

    public function nannyIndex()
    {
        return view('nanny.daily-checklist.index', [
            'anakList' => $this->fetchNannyChildren(),
        ]);
    }

    // ─── Nanny: daftar checklist per anak (hari ini default) ──────────────────
    public function nannyShow(Request $request, int $idAnak)
    {
        $anakList = $this->fetchNannyChildren();

        $day = (int) $request->input('day', date('N'));
        $day = max(1, min(7, $day));
        $weekly = $this->fetchDay($idAnak, $day);

        return view('nanny.daily-checklist.show', [
            'idAnak'   => $idAnak,
            'anakList' => $anakList,
            'namaAnak' => $this->childName($anakList, $idAnak),
            'day'      => $day,
            'dayNames' => $this->dayNames(),
            'weekly'   => $weekly,
        ]);
    }

    // ─── Nanny: form tambah checklist ───────────────────────────────────────

    public function nannyCreate(Request $request, int $idAnak)
    {
        $anakList = $this->fetchNannyChildren();

        $day = (int) $request->input('day', date('N'));
        $day = max(1, min(7, $day));

        return view('nanny.daily-checklist.create', [
            'idAnak'   => $idAnak,
            'namaAnak' => $this->childName($anakList, $idAnak),
            'day'      => $day,
            'dayNames' => $this->dayNames(),
            'old'      => (object) [],
        ]);
    }

    // ─── Nanny: form ubah checklist ─────────────────────────────────────────

    public function nannyEdit(int $idAnak, int $id)
    {
        $anakList  = $this->fetchNannyChildren();
        $checklist = $this->fetchChecklist($id);

        if (empty($checklist)) {
            return redirect()->route('nanny-daily-checklists-show', $idAnak)
                ->with('error', 'Checklist tidak ditemukan.');
        }

        return view('nanny.daily-checklist.edit', [
            'idAnak'     => $idAnak,
            'namaAnak'   => $this->childName($anakList, $idAnak),
            'checklist'  => $checklist,
            'dayNames'   => $this->dayNames(),
        ]);
    }

    // ─── Nanny: simpan checklist ────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'id_anak'                => 'required|integer',
            'title'                  => 'required|string|max:255',
            'day_of_week'            => 'required|integer|min:1|max:7',
            'items'                  => 'nullable|array|max:50',
            'items.*.item_name'      => 'required_with:items|string|max:255',
        ]);

        $payload = [
            'id_anak'      => $request->id_anak,
            'title'        => $request->title,
            'type'         => 'weekly',   // mode tunggal → weekly (day-based, no date)
            'day_of_week'  => (int) $request->day_of_week,
            'items'        => [],
        ];

        foreach (($request->input('items') ?? []) as $item) {
            $name = trim((string) ($item['item_name'] ?? ''));
            if ($name !== '') {
                $payload['items'][] = ['item_name' => $name];
            }
        }

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl('/daily-checklists'), $payload);

        $json = $response->json();
        $dayBack = max(1, min(7, (int) ($request->input('day_of_week') ?? date('N'))));
        $backShow = route('nanny-daily-checklists-show', ['id_anak' => $request->id_anak, 'day' => $dayBack]);

        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect($backShow)->with('success', 'Daily checklist berhasil disimpan.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: perbarui checklist ──────────────────────────────────────────

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'day_of_week'=> 'required|integer|min:1|max:7',
            'items'      => 'nullable|array|max:50',
            'items.*.id' => 'nullable|integer',
            'items.*.item_name' => 'required_with:items|string|max:255',
        ]);

        $payload = [
            'title'       => $request->title,
            'type'        => 'weekly',
            'day_of_week' => (int) $request->day_of_week,
            'items'       => [],
        ];

        foreach (($request->input('items') ?? []) as $item) {
            $name = trim((string) ($item['item_name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $row = ['item_name' => $name];
            if (!empty($item['id'])) {
                $row['id'] = (int) $item['id'];
            }
            $payload['items'][] = $row;
        }

        $response = Http::withHeaders($this->headers())
            ->put($this->apiUrl('/daily-checklists/' . $id), $payload);

        $json = $response->json();
        $dayBack  = max(1, min(7, (int) $request->input('day_of_week')));
        $backShow = route('nanny-daily-checklists-show', [
            'id_anak' => (int) $request->input('id_anak'),
            'day'    => $dayBack,
        ]);

        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect($backShow)->with('success', 'Daily checklist berhasil diperbarui.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response))->withInput();
    }

    // ─── Nanny: hapus checklist ─────────────────────────────────────────────

    public function destroy(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/daily-checklists/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->back()->with('success', 'Daily checklist deleted.');
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }

    // ─── Nanny: toggle item selesai/belum (param = ITEM id) ─────────────────

    public function toggleItem(Request $request, int $itemId)
    {
        $response = Http::withHeaders($this->headers())
            ->patch($this->apiUrl('/daily-checklists/' . $itemId . '/toggle-item'));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->back();
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }
}
