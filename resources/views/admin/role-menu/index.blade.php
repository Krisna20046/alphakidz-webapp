@extends('layouts.app')

@section('title', 'Manajemen Role Menu')

@push('styles')
<style>
    .role-chip {
        display:inline-flex; gap:6px; align-items:center;
        padding:2px 8px; border-radius:12px; font-size:11px; font-weight:800;
        text-transform:uppercase; letter-spacing:.04em;
    }
    .chip-admin   { background:#F3E5F5; color:#6A1B9A; border:1px solid #E1BEE7; }
    .chip-majikan { background:#E8F5E9; color:#1B5E20; border:1px solid #C8E6C9; }
    .chip-nanny   { background:#FFF3E0; color:#E65100; border:1px solid #FFE0B2; }
    .chip-nexus   { background:#E3F2FD; color:#0D47A1; border:1px solid #BBDEFB; }

    .perm-cell {
        display:flex; gap:6px; align-items:center;
        padding:6px 8px; background:#F8F6FF;
        border:1px solid #EAE6F5; border-radius:10px;
    }
    .perm-label {
        font-size:10px; font-weight:800; text-transform:uppercase;
        color:#8B86A5; white-space:nowrap;
    }
    .perm-switch {
        position:relative; display:inline-block; width:34px; height:20px;
    }
    .perm-switch input { opacity:0; width:0; height:0; }
    .perm-slider {
        position:absolute; cursor:pointer; inset:0;
        background:#E2E8F0; border-radius:20px; transition:.2s;
    }
    .perm-slider:before {
        position:absolute; content:""; height:14px; width:14px;
        left:3px; top:3px; background:#fff; border-radius:50%; transition:.2s;
    }
    input:checked + .perm-slider { background:#8B46D3; }
    input:checked + .perm-slider:before { transform:translateX(14px); }
    .perm-switch input:disabled + .perm-slider {
        background:#E5E7EB; cursor:not-allowed;
    }
    .perm-switch input:disabled + .perm-slider:before {
        background:#cbd5e1;
    }

    .menu-row {
        transition: background .15s ease;
    }
    .menu-row:hover { background:#F5F3FF; }

    .menu-icon {
        width:32px; height:32px; border-radius:10px;
        background:#EDE9FE; color:#8B46D3;
        display:flex; align-items:center; justify-content:center;
        font-size:16px; flex-shrink:0;
    }

    .menu-cell {
        display:flex; align-items:center; gap:10px;
    }
    .menu-meta .menu-nama {
        font-size:13px; font-weight:800; color:#1E1B2E;
    }
    .menu-meta .menu-route {
        font-size:11px; color:#8B86A5; word-break:break-all;
    }
    .badge-active {
        font-size:10px; font-weight:800; padding:2px 8px;
        border-radius:10px; background:#D1FAE5; color:#065F46;
    }
    .badge-inactive {
        font-size:10px; font-weight:800; padding:2px 8px;
        border-radius:10px; background:#FEE2E2; color:#991B1B;
    }

    .add-card {
        background:#fff; border:1px solid #EAE6F5; border-radius:16px;
        padding:16px; margin-bottom:16px;
    }
    .add-grid {
        display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap:12px; align-items:end;
    }
    .input-group label {
        display:block; font-size:11px; font-weight:800; color:#8B86A5;
        text-transform:uppercase; margin-bottom:6px;
    }
    .input-group input, .input-group select {
        width:100%; padding:8px 10px; border:1px solid #EAE6F5;
        border-radius:10px; font-size:13px; font-weight:700; color:#1E1B2E;
        background:#F8F6FF;
    }
    .input-group input:focus, .input-group select:focus {
        outline:none; border-color:#8B46D3;
    }
    .toggle-row {
        display:flex; align-items:center; gap:10px; margin-top:4px;
    }
    .empty-state {
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        padding:40px 20px; color:#8B86A5;
    }
    .empty-state ion-icon { font-size:40px; color:#C4B5FD; margin-bottom:12px; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Manajemen Role Menu</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">
                Atur hak akses menu per role (Admin, Majikan, Nanny, Nexus)
            </p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Flash Toast --}}
    @if(session('success') || session('error'))
    <div id="toast" class="toast rounded-2xl px-4 py-3 flex items-center gap-3
        {{ session('success') ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center
            {{ session('success') ? 'bg-green-100' : 'bg-red-100' }}">
            <ion-icon name="{{ session('success') ? 'checkmark-circle' : 'close-circle' }}"
                style="font-size:18px;color:{{ session('success') ? '#4CAF50' : '#F44336' }};"></ion-icon>
        </div>
        <p class="text-sm font-bold {{ session('success') ? 'text-green-800' : 'text-red-800' }} flex-1">
            {{ session('success') ?? session('error') }}
        </p>
        <button onclick="document.getElementById('toast').remove()">
            <ion-icon name="close" style="font-size:16px;color:#999;"></ion-icon>
        </button>
    </div>
    @endif

    {{-- Tambah Menu Baru --}}
    <div class="add-card anim delay-2">
        <div class="flex items-center justify-between mb-4">
            <span class="font-extrabold text-[#1E1B2E] text-sm">Tambah Menu Baru</span>
            <span class="text-xs font-bold text-[#8B46D3]">POST /menu (Admin)</span>
        </div>
        <form id="addMenuForm" class="add-grid">
            <div class="input-group">
                <label>Nama Menu</label>
                <input name="nama" type="text" placeholder="Contoh: Laporan Bulanan" required
                    value="{{ old('nama') }}">
            </div>
            <div class="input-group">
                <label>Icon (Ionicons)</label>
                <input name="icon" type="text" placeholder="Contoh: document-text-outline"
                    value="{{ old('icon', 'document-text-outline') }}">
            </div>
            <div class="input-group">
                <label>Route</label>
                <input name="route" type="text" placeholder="Contoh: admin-role-menu"
                    value="{{ old('route') }}">
            </div>
            <div class="input-group">
                <label for="menuCategory">Kategori</label>
                <input id="menuCategory" name="category" type="text" maxlength="50"
                    placeholder="Contoh: Sekolah" value="{{ old('category') }}">
            </div>
            <div class="input-group">
                <label>Urutan</label>
                <input name="urutan" type="number" placeholder="0" required
                    value="{{ old('urutan', 0) }}">
            </div>
            <div class="toggle-row">
                <input name="is_active" id="isActive" type="checkbox"
                    {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="isActive" class="font-bold text-[#1E1B2E] text-sm">Aktif</label>
            </div>
            <button type="submit"
                class="w-full py-3 rounded-xl bg-[#8B46D3] text-white font-extrabold text-sm flex items-center justify-center gap-2 active:scale-95 transition-all">
                <ion-icon name="add-outline" style="font-size:16px;"></ion-icon>
                Simpan Menu
            </button>
        </form>
    </div>

    {{-- Tabel Matriks Menu × Role --}}
    <div class="anim delay-3">
        <div class="flex items-center justify-between mb-4">
            <span class="font-extrabold text-[#1E1B2E] text-sm">Matriks Hak Akses Menu × Role</span>
            <span class="text-xs font-bold text-[#8B46D3]">{{ count($menus) }} menu</span>
        </div>

        @if(count($matrix) === 0)
        <div class="empty-state">
            <ion-icon name="document-text-outline"></ion-icon>
            <h3 class="font-extrabold text-[#1E1B2E] text-sm mt-2">Belum ada menu</h3>
            <p class="text-[12px] text-center mt-1">Tambah menu lewat formulir di atas.</p>
        </div>
        @else
        <table class="w-full border-collapse text-left">
            <thead>
            <tr class="text-[10px] font-extrabold text-[#8B86A5] uppercase border-b border-[#EAE6F5]">
                <th class="py-4 px-4 font-normal text-[#1E1B2E] uppercase">Menu</th>
                @foreach($roles as $role)
                <th class="py-4 px-3 text-center">
                    <span class="role-chip
                        @switch($role['id'])
                            @case(1) chip-admin @break
                            @case(2) chip-majikan @break
                            @case(3) chip-nanny @break
                            @case(5) chip-nexus @break
                        @endswitch">{{ $role['nama'] }}</span>
                </th>
                @endforeach
            </tr>
            </thead>
            <tbody class="divide-y divide-[#EAE6F5]">
            @foreach($matrix as $row)
            @php $m = $row['menu']; @endphp
            <tr class="menu-row">
                <td class="py-4 px-4">
                    <div class="menu-cell">
                        <div class="menu-icon">
                            <ion-icon name="{{ $m['icon'] ?? 'document-text-outline' }}"></ion-icon>
                        </div>
                        <div class="menu-meta">
                            <div class="menu-nama">{{ $m['nama'] ?? '-' }}</div>
                            <div class="menu-route">{{ $m['route'] ?? '-' }}</div>
                            <div class="menu-route">Kategori: {{ $m['category'] ?? 'Lainnya' }}</div>
                        </div>
                        <span class="{{ $m['is_active'] ? 'badge-active' : 'badge-inactive' }}">
                            {{ $m['is_active'] ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </td>
                @foreach($roles as $role)
                <td class="py-4 px-3 text-center">
                    <div class="perm-cell">
                        @php
                            $perm = $row['roles'][$role['id']] ?? [];
                            $rowId = $perm['id'] ?? null;
                        @endphp
                        <div>
                            <span class="perm-label">View</span>
                            <label class="perm-switch">
                                <input type="checkbox"
                                    data-id="{{ $rowId }}"
                                    data-id-role="{{ $role['id'] }}"
                                    data-id-menu="{{ (int) ($m['id'] ?? 0) }}"
                                    data-field="can_view"
                                    {{ $perm['can_view'] ?? false ? 'checked' : '' }}
                                    {{ $rowId === null ? 'disabled' : '' }}
                                    onchange="savePermission(this)">
                            </label>
                        </div>
                        <div>
                            <span class="perm-label">Create</span>
                            <label class="perm-switch">
                                <input type="checkbox"
                                    data-id="{{ $rowId }}"
                                    data-id-role="{{ $role['id'] }}"
                                    data-id-menu="{{ (int) ($m['id'] ?? 0) }}"
                                    data-field="can_create"
                                    {{ $perm['can_create'] ?? false ? 'checked' : '' }}
                                    {{ $rowId === null ? 'disabled' : '' }}
                                    onchange="savePermission(this)">
                            </label>
                        </div>
                        <div>
                            <span class="perm-label">Edit</span>
                            <label class="perm-switch">
                                <input type="checkbox"
                                    data-id="{{ $rowId }}"
                                    data-id-role="{{ $role['id'] }}"
                                    data-id-menu="{{ (int) ($m['id'] ?? 0) }}"
                                    data-field="can_edit"
                                    {{ $perm['can_edit'] ?? false ? 'checked' : '' }}
                                    {{ $rowId === null ? 'disabled' : '' }}
                                    onchange="savePermission(this)">
                            </label>
                        </div>
                        <div>
                            <span class="perm-label">Delete</span>
                            <label class="perm-switch">
                                <input type="checkbox"
                                    data-id="{{ $rowId }}"
                                    data-id-role="{{ $role['id'] }}"
                                    data-id-menu="{{ (int) ($m['id'] ?? 0) }}"
                                    data-field="can_delete"
                                    {{ $perm['can_delete'] ?? false ? 'checked' : '' }}
                                    {{ $rowId === null ? 'disabled' : '' }}
                                    onchange="savePermission(this)">
                            </label>
                        </div>
                        @if($rowId === null)
                        <div class="mt-2 text-[10px] text-[#DC2626] font-bold">Belum terdaftar</div>
                        @endif
                    </div>
                </td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    async function apiSend(url, method, body) {
        const res = await fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body
        });
        return await res.json();
    }

    async function savePermission(el) {
        const idRoleMenu = el.getAttribute('data-id');
        const idRole     = el.getAttribute('data-id-role');
        const idMenu     = el.getAttribute('data-id-menu');
        const field      = el.getAttribute('data-field');
        const on         = el.checked ? '1' : '0';

        // Susun payload: hanya field yang diklik yang berubah, sisanya 0.
        // Backend hanya menerima boolean sebagai true/false/0/1.
        const flags = { can_view: '0', can_create: '0', can_edit: '0', can_delete: '0' };
        flags[field] = on;

        try {
            if (!idRoleMenu) {
                // Kombinasi belum punya baris → buat baru (POST /role-menu) dengan flag langsung.
                const params = new URLSearchParams({ id_role: idRole, id_menu: idMenu, ...flags });
                const json = await apiSend("{{ route('admin-role-menu.store-role-menu') }}", 'POST', params);
                if (json.status === 'success') {
                    showAppAlert('Hak akses tersimpan.', 'ok');
                    setTimeout(() => location.reload(), 700);
                } else {
                    showAppAlert(json.message || 'Gagal menyimpan hak akses.', 'error');
                    el.checked = !el.checked;
                }
                return;
            }

            const form = new FormData();
            form.append('id', idRoleMenu);
            form.append('id_role', idRole);
            form.append('id_menu', idMenu);
            Object.entries(flags).forEach(([k, v]) => form.append(k, v));

            const url = "{{ route('admin-role-menu.update', 0) }}".replace(/0\/?$/, idRoleMenu);
            const json = await apiSend(url, 'PUT', form);
            if (json.status === 'success') {
                showAppAlert('Hak akses tersimpan.', 'ok');
            } else {
                showAppAlert(json.message || 'Gagal menyimpan hak akses.', 'error');
                el.checked = !el.checked;
            }
        } catch (e) {
            showAppAlert('Terjadi kesalahan jaringan.', 'error');
            el.checked = !el.checked;
        }
    }

    document.getElementById('addMenuForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = new FormData(e.target);
        // Backend validasi is_active sebagai boolean → kirim '1'/'0' (bukan "on").
        form.set('is_active', e.target.querySelector('#isActive').checked ? '1' : '0');
        try {
            const json = await apiSend("{{ route('admin-role-menu.store-menu') }}", 'POST', form);
            if (json.status === 'success') {
                showAppAlert('Menu berhasil ditambahkan.', 'ok');
                setTimeout(() => location.reload(), 800);
            } else {
                showAppAlert(json.message || 'Gagal menambahkan menu.', 'error');
            }
        } catch (err) {
            showAppAlert('Terjadi kesalahan jaringan.', 'error');
        }
    });
</script>
@endpush
