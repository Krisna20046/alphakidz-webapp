<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SchoolSubjectController extends Controller
{
    /**
     * Proxy untuk API school-subjects (Module 1).
     * Semua request diteruskan ke API eksternal dengan token dari session
     * agar token tidak terekspos ke client.
     *
     * Catatan response backend (SchoolSubjectController backend):
     *   List:   { success, message, data: { data: [...], links, meta: { current_page, last_page, total, ... } } }
     *   Single: { success, message, data: { id, name, icon, color } }
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

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/school-subjects'), [
                'per_page' => $perPage,
                'page'     => $page,
            ]);

        $subjects   = [];
        $pagination = null;

        if ($response->successful()) {
            $json = $response->json();
            if (is_array($json) && $this->isSuccess($json)) {
                $data = $json['data'] ?? [];

                // Bentuk paginated Laravel resource: items di data['data'], pagination di data['meta']
                if (is_array($data) && array_key_exists('data', $data)) {
                    $subjects = is_array($data['data']) ? $data['data'] : [];
                    $meta     = $data['meta'] ?? [];
                    $pagination = [
                        'current_page' => $meta['current_page'] ?? 1,
                        'last_page'    => $meta['last_page'] ?? 1,
                        'total'        => $meta['total'] ?? count($subjects),
                        'per_page'     => $meta['per_page'] ?? $perPage,
                    ];
                } else {
                    $subjects   = is_array($data) ? $data : [];
                    $pagination = $json['pagination'] ?? null;
                }
            }
        }

        return view('admin.school-subject.index', compact('subjects', 'pagination'));
    }

    // ─── Show Detail ──────────────────────────────────────────────────────────

    public function show($id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/school-subjects/' . $id));

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return redirect()->route('admin-school-subject')
                ->with('error', 'Failed to load subject data.');
        }

        $subject = $json['data'] ?? null;
        if (!is_array($subject)) {
            return redirect()->route('admin-school-subject')
                ->with('error', 'Failed to load subject data.');
        }

        return view('admin.school-subject.show', compact('subject'));
    }

    // ─── Create Form ──────────────────────────────────────────────────────────

    public function create()
    {
        return view('admin.school-subject.create');
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'icon'  => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $response = Http::withHeaders($this->headers())
            ->asMultipart()
            ->post($this->apiUrl('/school-subjects'), [
                ['name' => 'name',  'contents' => $request->name],
                ['name' => 'icon',  'contents' => (string) ($request->icon ?? '')],
                ['name' => 'color', 'contents' => (string) ($request->color ?? '')],
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('admin-school-subject')
                ->with('success', 'Subject added successfully.');
        }

        return back()
            ->with('error', $this->extractMessage($response))
            ->withInput();
    }

    // ─── Edit Form ────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl('/school-subjects/' . $id));

        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !$this->isSuccess($json)) {
            return redirect()->route('admin-school-subject')
                ->with('error', 'Failed to load subject data.');
        }

        $subject = $json['data'] ?? null;
        if (!is_array($subject)) {
            return redirect()->route('admin-school-subject')
                ->with('error', 'Failed to load subject data.');
        }

        return view('admin.school-subject.edit', compact('subject'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'icon'  => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $response = Http::withHeaders($this->headers())
            ->put($this->apiUrl('/school-subjects/' . $id), [
                'name'  => $request->name,
                'icon'  => (string) ($request->icon ?? ''),
                'color' => (string) ($request->color ?? ''),
            ]);

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('admin-school-subject')
                ->with('success', 'Subject updated successfully.');
        }

        return back()
            ->with('error', $this->extractMessage($response))
            ->withInput();
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl('/school-subjects/' . $id));

        $json = $response->json();
        if ($response->successful() && is_array($json) && $this->isSuccess($json)) {
            return redirect()->route('admin-school-subject')
                ->with('success', 'Subject deleted successfully.');
        }

        return back()->with('error', $this->extractMessage($response));
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function extractMessage($response): string
    {
        $json = $response->json();
        if (is_array($json)) {
            // Ambil pesan error validasi yang pertama jika ada
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
