<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    private function apiUrl(): string
    {
        return config('services.api.base_url', env('API_BASE_URL'));
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . session('token'),
            'Accept'        => 'application/json',
        ];
    }

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $page    = (int) $request->input('page', 1);

        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl() . '/user-all', [
                'per_page' => $perPage,
                'page'     => $page,
            ]);

        $users      = [];
        $pagination = null;
        if ($response->successful() && $response->json('status') === 'success') {
            $allUsers   = $response->json('data') ?? [];
            $pagination = $response->json('pagination');

            // Sembunyikan akun admin lain — hanya tampilkan admin yang sedang login
            $currentUserId = session('user.id_user') ?? session('user.id') ?? null;
            $users = array_values(array_filter($allUsers, function ($u) use ($currentUserId) {
                return !(strtolower($u['role'] ?? '') === 'admin'
                    && (int) ($u['id'] ?? 0) !== (int) $currentUserId);
            }));
        }

        return view('admin.kelola-akun.index', compact('users', 'pagination'));
    }

    // ─── Show Detail ──────────────────────────────────────────────────────────

    public function show($id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl() . '/user-detail', ['id_user' => $id]);

        if (!$response->successful() || $response->json('status') !== 'success') {
            return redirect()->route('admin.kelola-akun.index')
                ->with('error', 'Gagal memuat data pengguna.');
        }

        $user = $response->json('data');
        return view('admin.kelola-akun.show', compact('user'));
    }

    // ─── Create Form ──────────────────────────────────────────────────────────

    public function create()
    {
        return view('admin.kelola-akun.create');
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    // Mirrors AdminKelolaAkunTambahScreen: sends name, email, password, id_role

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'id_role'  => 'required|integer|in:1,2,3,4,5',
        ]);

        $response = Http::withHeaders($this->headers())
            ->asMultipart()
            ->post($this->apiUrl() . '/admin/register', [
                ['name' => 'name',     'contents' => $request->name],
                ['name' => 'email',    'contents' => $request->email],
                ['name' => 'password', 'contents' => $request->password],
                ['name' => 'id_role',  'contents' => (string) $request->id_role],
            ]);

        if ($response->successful() && $response->json('status') === 'success') {
            return redirect()->route('admin-kelola-akun')
                ->with('success', 'Akun berhasil dibuat.');
        }

        return back()
            ->with('error', $response->json('message') ?? 'Gagal membuat akun.')
            ->withInput();
    }

    // ─── Edit Form ────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->apiUrl() . '/user-detail', ['id_user' => $id]);

        if (!$response->successful() || $response->json('status') !== 'success') {
            return redirect()->route('admin-kelola-akun')
                ->with('error', 'Gagal memuat data pengguna.');
        }

        $user = $response->json('data');
        return view('admin.kelola-akun.edit', compact('user'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    // Mirrors AdminKelolaAkunUbahScreen: sends id, name, email, id_role, password (optional)

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'id_role'  => 'required|integer|in:1,2,3,4,5',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'id'      => (int) $id,
            'name'    => $request->name,
            'email'   => $request->email,
            'id_role' => (int) $request->id_role,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $response = Http::withHeaders($this->headers())
            ->put($this->apiUrl() . '/admin/edit', $data);

        if ($response->successful() && $response->json('status') === 'success') {
            return redirect()->route('admin-kelola-akun')
                ->with('success', 'Data akun berhasil diubah.');
        }

        return back()
            ->with('error', $response->json('message') ?? 'Gagal mengubah data akun.')
            ->withInput();
    }

    // ─── Toggle Status ────────────────────────────────────────────────────────

    public function updateStatus(Request $request, $id)
    {
        $status = (int) $request->input('is_active');

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl() . '/admin/update-status', [
                'id'        => (int) $id,
                'is_active' => $status,
            ]);

        if ($response->successful() && $response->json('status') === 'success') {
            $label = $status ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->route('admin-kelola-akun')
                ->with('success', "Akun berhasil {$label}.");
        }

        return back()->with('error', $response->json('message') ?? 'Gagal mengubah status akun.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->apiUrl() . '/admin/delete', ['id' => (int) $id]);

        if ($response->successful() && $response->json('status') === 'success') {
            return redirect()->route('admin-kelola-akun')
                ->with('success', 'Akun berhasil dihapus.');
        }

        return back()->with('error', $response->json('message') ?? 'Gagal menghapus akun.');
    }
}