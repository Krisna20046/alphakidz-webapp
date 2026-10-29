<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Role Menu Management (Admin only, role 1).
 *
 * Proxy ke backend AlphaKidz-Backend endpoints:
 *   GET    /menu          -> daftar semua menu
 *   GET    /role-menu     -> daftar semua baris role_menu
 *   PUT    /role-menu     -> update flag can_view/create/edit/delete per baris
 *   POST   /menu          -> tambah menu baru
 *
 * Semua mutate endpoint di backend dilindungi admin gate
 * (Auth::user()->role->id !== 1 -> 403). Frontend juga guard role 1.
 */
class RoleMenuController extends Controller
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

    private function isOk($response): bool
    {
        return $response->successful() && $response->json('status') === 'success';
    }

    /** Guard: hanya role 1 (Admin) yang boleh mengakses. */
    private function requireAdmin(): ?\Illuminate\Http\RedirectResponse
    {
        if ((int) (session('user')['id_role'] ?? 0) !== 1) {
            return redirect()->route('dashboard')
                ->with('error', 'Hanya Admin yang dapat mengakses halaman ini.');
        }
        return null;
    }

    /** GET /admin/role-menu — matriks menu × role dengan flag permission. */
    public function index(Request $request)
    {
        $guard = $this->requireAdmin();
        if ($guard) return $guard;

        $menusResp  = Http::withHeaders($this->headers())->get($this->apiUrl() . '/menu');
        $roleMenuResp = Http::withHeaders($this->headers())->get($this->apiUrl() . '/role-menu');

        $menus   = [];
        $roles   = [];
        $matrix  = [];

        if ($this->isOk($menusResp)) {
            $menus = (array) ($menusResp->json('data') ?? $menusResp->json('menus') ?? []);
            usort($menus, fn ($a, $b) => ((int) ($a['urutan'] ?? 0)) <=> ((int) ($b['urutan'] ?? 0)));
        }

        $roleRows = [];
        if ($this->isOk($roleMenuResp)) {
            $roleRows = (array) ($roleMenuResp->json('data') ?? $roleMenuResp->json('roleMenus') ?? []);
        }

        $roleIds = [1, 2, 3, 5]; // Admin, Majikan, Nanny, Nexus
        $roleNames = [1 => 'Admin', 2 => 'Majikan', 3 => 'Nanny', 5 => 'Nexus'];

        foreach ($roleIds as $rid) {
            $roles[] = ['id' => $rid, 'nama' => $roleNames[$rid]];
        }

        // Index baris role_menu per kombinasi (id_role, id_menu) → first row id + flags
        $lookup = [];
        foreach ($roleRows as $row) {
            $key = (int) ($row['id_role'] ?? 0) . ':' . (int) ($row['id_menu'] ?? 0);
            if (!isset($lookup[$key])) {
                $lookup[$key] = [
                    'id'        => (int) ($row['id'] ?? 0),
                    'can_view'  => (bool) ($row['can_view'] ?? false),
                    'can_create'=> (bool) ($row['can_create'] ?? false),
                    'can_edit'  => (bool) ($row['can_edit'] ?? false),
                    'can_delete'=> (bool) ($row['can_delete'] ?? false),
                ];
            }
        }

        foreach ($menus as $m) {
            $mid = (int) ($m['id'] ?? 0);
            $rowMenu = ['menu' => $m, 'roles' => []];
            foreach ($roleIds as $rid) {
                $key = $rid . ':' . $mid;
                $rowMenu['roles'][$rid] = $lookup[$key] ?? [
                    'id'        => null,
                    'can_view'  => false,
                    'can_create'=> false,
                    'can_edit'  => false,
                    'can_delete'=> false,
                ];
            }
            $matrix[] = $rowMenu;
        }

        return view('admin.role-menu.index', compact('menus', 'roles', 'matrix'));
    }

    /** PUT /admin/role-menu/{id} — update flag permission satu baris role_menu. */
    public function update(Request $request, $id)
    {
        $guard = $this->requireAdmin();
        if ($guard) return $guard;

        $request->validate([
            'id_role'   => 'required|integer',
            'id_menu'   => 'required|integer',
            'can_view'  => 'nullable|boolean',
            'can_create'=> 'nullable|boolean',
            'can_edit'  => 'nullable|boolean',
            'can_delete'=> 'nullable|boolean',
        ]);

        $payload = [
            'id'        => (int) $id,
            'id_role'   => (int) $request->id_role,
            'id_menu'   => (int) $request->id_menu,
            'can_view'  => (bool) ($request->can_view ?? false),
            'can_create'=> (bool) ($request->can_create ?? false),
            'can_edit'  => (bool) ($request->can_edit ?? false),
            'can_delete'=> (bool) ($request->can_delete ?? false),
        ];

        $response = Http::withHeaders($this->headers())
            ->put($this->apiUrl() . '/role-menu', $payload);

        if ($this->isOk($response)) {
            return back()->with('success', 'Hak akses menu berhasil diperbarui.');
        }

        return back()->with('error', $response->json('message') ?? 'Gagal memperbarui hak akses menu.')
            ->withInput();
    }

    /**
     * POST /admin/role-menu/role-menu — buat baris role_menu baru.
     * Dipakai saat kombinasi menu × role belum punya baris di backend.
     */
    public function storeRoleMenu(Request $request)
    {
        $guard = $this->requireAdmin();
        if ($guard) return $guard;

        $request->validate([
            'id_role'   => 'required|integer',
            'id_menu'   => 'required|integer',
            'can_view'  => 'nullable|boolean',
            'can_create'=> 'nullable|boolean',
            'can_edit'  => 'nullable|boolean',
            'can_delete'=> 'nullable|boolean',
        ]);

        $payload = [
            'id_role'   => (int) $request->id_role,
            'id_menu'   => (int) $request->id_menu,
            'can_view'  => (bool) ($request->can_view ?? false),
            'can_create'=> (bool) ($request->can_create ?? false),
            'can_edit'  => (bool) ($request->can_edit ?? false),
            'can_delete'=> (bool) ($request->can_delete ?? false),
        ];

        $response = Http::withHeaders($this->headers())
            ->post($this->apiUrl() . '/role-menu', $payload);

        if ($this->isOk($response)) {
            return back()->with('success', 'Hak akses menu berhasil ditambahkan.');
        }

        return back()->with('error', $response->json('message') ?? 'Gagal menambahkan hak akses menu.')
            ->withInput();
    }

    /** POST /admin/role-menu/menu — tambah menu baru (Admin). */
    public function storeMenu(Request $request)
    {
        $guard = $this->requireAdmin();
        if ($guard) return $guard;

        $request->validate([
            'parent_id' => 'nullable|integer',
            'nama'      => 'required|string|max:255',
            'category'  => 'nullable|string|max:50',
            'icon'      => 'nullable|string|max:255',
            'route'     => 'nullable|string|max:255',
            'urutan'    => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'parent_id' => $request->filled('parent_id') ? (int) $request->parent_id : null,
            'nama'      => $request->nama,
            'category'  => $request->category,
            'icon'      => $request->icon,
            'route'     => $request->route,
            'urutan'    => (int) $request->urutan,
            'is_active' => (bool) $request->is_active,
        ];

        $response = Http::withHeaders($this->headers())
            ->asMultipart()
            ->post($this->apiUrl() . '/menu', $data);

        if ($this->isOk($response)) {
            return redirect()->route('admin-role-menu')
                ->with('success', 'Menu berhasil ditambahkan.');
        }

        return back()->with('error', $response->json('message') ?? 'Gagal menambahkan menu.')
            ->withInput();
    }
}
