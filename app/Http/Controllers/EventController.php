<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Modul 23 — Event Manage (frontend proxy).
 *
 * Konten fleksibel tim Nexus (event/info/pengumuman) yang tampil sebagai
 * slider di home semua role (majikan, nanny, konsultan).
 * - Nexus (role 5): daftar + tambah + ubah + hapus.
 * Home slider diambil server-side oleh HomeController (fetchActiveEvents).
 * Request diteruskan ke backend (token di session, tidak terekspos browser).
 *
 * Backend:
 *   GET    events/active?limit&type  → { data:[...] }
 *   GET    events?page&type&is_active → { data:[...], meta }
 *   POST   events (multipart, banner optional)
 *   PUT    events/{id} (multipart)
 *   DELETE events/{id}
 */
class EventController extends Controller
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

    /** Daftar semua event (paginated, filter type/is_active). */
    private function fetchAll(int $page = 1, int $perPage = 20, ?string $type = null, ?string $isActive = null): array
    {
        $params = ['page' => $page, 'per_page' => $perPage];
        if ($type !== null && $type !== '') {
            $params['type'] = $type;
        }
        if ($isActive !== null && $isActive !== '') {
            $params['is_active'] = $isActive;
        }

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/events'), $params);

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return ['records' => [], 'meta' => null];
        }

        return [
            'records' => is_array($json['data'] ?? null) ? $json['data'] : [],
            'meta'    => is_array($json['meta'] ?? null) ? $json['meta'] : null,
        ];
    }

    /**
     * GET events/{id} — detail event utk user (semua role login).
     * Dipakai kartu "Events & Info" di home → halaman detail.
     */
    public function show(int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/events/' . $id));

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json) || empty($json['data'])) {
            abort(404, 'Event tidak ditemukan.');
        }

        return view('event.show', ['event' => $json['data']]);
    }

    /** Bentuk data form → array assoc name => value (untuk POST update). */
    private function buildFieldsAssoc(Request $request): array
    {
        return [
            'title'          => (string) $request->input('title', ''),
            'subtitle'       => (string) $request->input('subtitle', ''),
            'description'    => (string) $request->input('description', ''),
            'type'           => (string) $request->input('type', ''),
            'location'       => (string) $request->input('location', ''),
            'map_url'        => (string) $request->input('map_url', ''),
            'event_start_at' => $this->toBackendDate($request->input('event_start_at')),
            'event_end_at'   => $this->toBackendDate($request->input('event_end_at')),
            'publish_at'     => $this->toBackendDate($request->input('publish_at')),
            'expired_at'     => $this->toBackendDate($request->input('expired_at')),
            'link_url'       => (string) $request->input('link_url', ''),
            'link_label'     => (string) $request->input('link_label', ''),
            'contact_name'   => (string) $request->input('contact_name', ''),
            'contact_phone'  => (string) $request->input('contact_phone', ''),
            'price'          => (string) $request->input('price', ''),
            'quota'          => (string) $request->input('quota', ''),
            'is_active'      => $request->boolean('is_active') ? '1' : '0',
            'sort_order'     => (string) $request->input('sort_order', '0'),
        ];
    }

    /** Bentuk data form → array of multipart parts (untuk POST create). */
    private function buildFields(Request $request): array
    {
        $multipart = [];
        foreach ($this->buildFieldsAssoc($request) as $name => $contents) {
            $multipart[] = ['name' => $name, 'contents' => $contents];
        }

        return $multipart;
    }

    /** datetime-local (Y-m-d\TH:i) → Y-m-d H:i:s; kosong → ''. */
    private function toBackendDate($value): string
    {
        $value = (string) $value;
        if ($value === '') {
            return '';
        }
        return str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
    }

    // ─── Nexus (role 5): daftar + tambah + ubah + hapus ─────────────────────

    public function nexusIndex(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $type = $request->filled('type') ? (string) $request->input('type') : null;
        $isActive = $request->input('is_active');
        $isActive = $isActive === null || $isActive === '' ? null : (string) $isActive;

        $result = $this->fetchAll($page, 20, $type, $isActive);

        return view('nexus.event.index', [
            'records'    => $result['records'],
            'meta'       => $result['meta'],
            'filterType' => $type,
            'filterActive' => $isActive,
        ]);
    }

    /** Tambah event (multipart → backend POST). */
    public function nexusStore(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:10000',
            'banner_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'event_end_at'   => 'nullable|date|after_or_equal:event_start_at',
            'expired_at'     => 'nullable|date|after_or_equal:publish_at',
            'link_url'       => 'nullable|url|max:500',
            'price'          => 'nullable|integer|min:0',
            'quota'          => 'nullable|integer|min:1',
        ]);

        $multipart = $this->buildFields($request);

        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $multipart[] = [
                'name'     => 'banner_image',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->asMultipart()
            ->post($this->apiUrl('/events'), $multipart);

        if ($response->successful() && $this->isSuccess($response->json() ?? [])) {
            return redirect()->route('nexus-events')->with('success', $this->extractMessage($response));
        }

        return redirect()->back()->withInput()->with('error', $this->extractMessage($response));
    }

    /** Ubah event (multipart → backend PUT). */
    public function nexusUpdate(Request $request, int $id)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:10000',
            'banner_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'event_end_at'   => 'nullable|date|after_or_equal:event_start_at',
            'expired_at'     => 'nullable|date|after_or_equal:publish_at',
            'link_url'       => 'nullable|url|max:500',
            'price'          => 'nullable|integer|min:0',
            'quota'          => 'nullable|integer|min:1',
        ]);

        $fields = $this->buildFieldsAssoc($request);

        // Pola terbukti AcademicTaskController: POST biasa (assoc) + attach file.
        // PUT multipart tidak diurai PHP → validated() kosong; _method query pun
        // tidak andal — karena itu backend menyediakan POST events/{id} = update.
        $http = Http::withHeaders($this->headers());
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $http = $http->attach(
                'banner_image',
                fopen($file->getRealPath(), 'r'),
                $file->getClientOriginalName()
            );
        }

        $response = $http->post($this->apiUrl('/events/' . $id), $fields);

        if ($response->successful() && $this->isSuccess($response->json() ?? [])) {
            return redirect()->route('nexus-events')->with('success', $this->extractMessage($response));
        }

        return redirect()->back()->withInput()->with('error', $this->extractMessage($response));
    }

    /** Hapus event + banner (form DELETE). */
    public function nexusDestroy(Request $request, int $id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/events/' . $id));

        if ($response->successful() && $this->isSuccess($response->json() ?? [])) {
            return redirect()->back()->with('success', $this->extractMessage($response));
        }

        return redirect()->back()->with('error', $this->extractMessage($response));
    }
}
