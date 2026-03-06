{{-- resources/views/admin/kelola-akun/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kelola Akun</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: { DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans','sans-serif'] }
                }
            }
        }
    </script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

        /* ── Desktop phone frame ── */
        @media (min-width: 640px) {
            .phone-wrapper {
                display:flex; align-items:flex-start; justify-content:center;
                min-height:100vh; padding:32px 0;
                background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%);
            }
            .phone-frame {
                width:420px; min-height:844px; border-radius:44px;
                box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020;
                /* overflow hidden hanya di sm+ agar FAB tidak terpotong */
                overflow:hidden; position:relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height:100vh; }
            .phone-frame   { min-height:100vh; position:relative; }
        }

        .header-bg   { background:linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%); }
        .header-wave { border-radius:0 0 30px 30px; }

        /* ── Slide-up ── */
        @keyframes slideUp { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }
        .anim-up { animation:slideUp .35s ease forwards; }
        .delay-1 { animation-delay:.05s;opacity:0; }
        .delay-2 { animation-delay:.12s;opacity:0; }
        .delay-3 { animation-delay:.20s;opacity:0; }

        /* ── FAB — sticky bottom inside flex column ── */
        .fab-wrap {
            /* Sits above bottom-nav, never scrolls away */
            position: fixed;;
            bottom: 80px;   /* clear the bottom nav height */
            right: 20px;
            z-index: 30;
        }
        @keyframes fabIn {
            0%   { transform:scale(0) rotate(-20deg); opacity:0; }
            70%  { transform:scale(1.1) rotate(5deg); }
            100% { transform:scale(1) rotate(0); opacity:1; }
        }
        .fab-in { animation:fabIn .5s cubic-bezier(.34,1.56,.64,1) .3s forwards; opacity:0; }

        /* ── Cards ── */
        .user-card { transition:transform .15s ease,box-shadow .15s ease; }
        .user-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(123,30,90,.12); }

        /* ── Role badges ── */
        .badge-admin     { background:#F3E5F5; color:#6A1B9A; }
        .badge-konsultan { background:#E3F2FD; color:#0D47A1; }
        .badge-majikan   { background:#E8F5E9; color:#1B5E20; }
        .badge-nanny     { background:#FFF3E0; color:#E65100; }

        /* ── Generic modal (filter + detail share the same base) ── */
        .modal-backdrop {
            position:fixed; inset:0; background:rgba(0,0,0,.55);
            display:flex; align-items:flex-end; justify-content:center;
            z-index:50; opacity:0; pointer-events:none;
            transition:opacity .25s ease;
        }
        .modal-backdrop.open { opacity:1; pointer-events:all; }
        .modal-sheet {
            background: #fff;
            width: 100%;
            max-width: 420px;
            border-radius: 24px 24px 0 0;
            transform: translateY(100%);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            display: flex;
            flex-direction: column;
            max-height: 85vh; /* Batasi tinggi maksimal */
        }
        .modal-backdrop.open .modal-sheet { transform: translateY(0); }
        /* Container untuk handle dan header - TETAP */
        .handle-container {
            flex-shrink: 0; /* Mencegah mengecil */
            background: white;
            border-radius: 24px 24px 0 0;
        }

        /* Container untuk konten yang bisa di-scroll */
        .scrollable-content {
            overflow-y: auto;
            flex: 1; /* Mengisi sisa ruang */
            -webkit-overflow-scrolling: touch; /* Smooth scrolling di iOS */
        }

        /* Pastikan background putih konsisten */
        .handle-container,
        .scrollable-content {
            background: white;
        }

        /* ── Filter chips ── */
        .chip { border:1.5px solid #F0E6F5; background:#F8F0F5; color:#7B1E5A; }
        .chip.active { background:#7B1E5A; border-color:#7B1E5A; color:#fff; }

        /* ── Scrollbar hide ── */
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }

        /* ── Toast ── */
        @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
        .toast { animation:toastIn .3s ease forwards; }

        /* ── Empty float ── */
        @keyframes floatEmpty { 0%,100%{transform:translateY(0);}50%{transform:translateY(-6px);} }
        .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

        /* ── Action btn press ── */
        .act-btn { transition:transform .1s ease; }
        .act-btn:active { transform:scale(.92); }

        /* ── Detail modal section title ── */
        .detail-section-title {
            font-size:.75rem; font-weight:700; color:#A2397B;
            text-transform:uppercase; letter-spacing:.06em;
            margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem;
        }
        .detail-row { display:flex; align-items:flex-start; gap:.75rem; padding:.5rem 0; }
        .detail-val { font-size:.875rem; color:#4A0E35; font-weight:600; flex:1; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col" id="phoneFrame">

    {{-- ─── STATUS BAR ─────────────────────────────────────────────────────── --}}
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    {{-- ─── HEADER ──────────────────────────────────────────────────────────── --}}
    <div class="header-bg header-wave px-5 pt-10 pb-8 relative shrink-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white/5 translate-y-6 -translate-x-6"></div>
        <div class="relative flex items-center gap-3 anim-up delay-1">
            <a href="{{ route('dashboard') }}"
               class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
            </a>
            <div>
                <h1 class="text-white text-xl font-extrabold leading-tight">Kelola Akun</h1>
                <p class="text-white/60 text-xs mt-0.5">{{ count($users) }} pengguna terdaftar</p>
            </div>
        </div>
    </div>

    {{-- ─── FLASH TOAST ─────────────────────────────────────────────────────── --}}
    @if(session('success') || session('error'))
    <div id="toast" class="toast absolute top-28 left-4 right-4 z-40 rounded-2xl px-4 py-3 flex items-center gap-3
        {{ session('success') ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center
            {{ session('success') ? 'bg-green-100' : 'bg-red-100' }}">
            <ion-icon name="{{ session('success') ? 'checkmark-circle' : 'close-circle' }}"
                style="font-size:18px;color:{{ session('success') ? '#4CAF50' : '#F44336' }};"></ion-icon>
        </div>
        <p class="text-sm font-semibold {{ session('success') ? 'text-green-800' : 'text-red-800' }} flex-1">
            {{ session('success') ?? session('error') }}
        </p>
        <button onclick="document.getElementById('toast').remove()">
            <ion-icon name="close" style="font-size:16px;color:#999;"></ion-icon>
        </button>
    </div>
    @endif

    {{-- ─── SEARCH + FILTER ─────────────────────────────────────────────────── --}}
    <div class="px-4 pt-5 pb-2 flex gap-3 anim-up delay-2 shrink-0">
        <div class="flex-1 flex items-center gap-2 bg-white rounded-2xl px-4 py-3
                    shadow-sm shadow-plum/10 border border-plum-soft">
            <ion-icon name="search" style="font-size:18px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
            <input id="searchInput" type="text" placeholder="Cari nama, email, atau no. HP..."
                class="flex-1 bg-transparent text-sm text-plum-dark placeholder-plum/40 outline-none"
                oninput="filterUsers()">
            <button id="clearSearch" onclick="clearSearch()" class="hidden">
                <ion-icon name="close-circle" style="font-size:18px;color:#B895C8;"></ion-icon>
            </button>
        </div>
        <button onclick="openFilter()"
            class="flex items-center gap-1.5 bg-white rounded-2xl px-4 py-3
                   shadow-sm shadow-plum/10 border border-plum-soft act-btn">
            <ion-icon name="options" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            <span class="text-sm font-semibold text-plum">Filter</span>
            <span id="filterDot" class="hidden w-2 h-2 rounded-full bg-plum"></span>
        </button>
    </div>

    {{-- ─── ACTIVE FILTER CHIPS ─────────────────────────────────────────────── --}}
    <div id="activeFilters" class="hidden px-4 pb-2 flex gap-2 flex-wrap shrink-0">
        <span id="roleChip" class="hidden items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-full bg-plum text-white">
            <span id="roleChipLabel"></span>
            <button onclick="clearRoleFilter()"><ion-icon name="close" style="font-size:12px;"></ion-icon></button>
        </span>
        <span id="statusChip" class="hidden items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-full bg-plum text-white">
            <span id="statusChipLabel"></span>
            <button onclick="clearStatusFilter()"><ion-icon name="close" style="font-size:12px;"></ion-icon></button>
        </span>
    </div>

    {{-- ─── RESULT COUNT ────────────────────────────────────────────────────── --}}
    <div class="px-4 pb-2 shrink-0">
        <p class="text-xs text-plum-muted font-medium">
            Menampilkan <span id="resultCount" class="font-bold text-plum">{{ count($users) }}</span> pengguna
        </p>
    </div>

    {{-- ─── USER LIST ───────────────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 pb-28 anim-up delay-3" id="userList">

        {{-- Empty state --}}
        <div id="emptyState" class="{{ count($users) > 0 ? 'hidden' : '' }} flex flex-col items-center py-16">
            <div class="float-anim w-20 h-20 rounded-full bg-plum-soft flex items-center justify-center mb-4">
                <ion-icon name="people-outline" style="font-size:36px;color:#E0BBE4;"></ion-icon>
            </div>
            <h3 class="text-plum-dark font-bold text-base mb-1">Tidak ada pengguna</h3>
            <p class="text-plum-muted text-xs text-center">Coba ubah filter atau kata kunci pencarian</p>
            <button onclick="resetAll()"
                class="mt-4 px-5 py-2 rounded-xl bg-plum text-white text-sm font-semibold">
                Reset Filter
            </button>
        </div>

        {{-- User Cards --}}
        <div id="cardsContainer" class="space-y-3 pt-1">
        @foreach($users as $user)
        @php
            $isActive  = (int)$user['is_active'] === 1;
            $role      = strtolower($user['role'] ?? 'nanny');
            $roleMap   = ['admin'=>'Admin','konsultan'=>'Konsultan','majikan'=>'Majikan','nanny'=>'Nanny'];
            $roleLabel = $roleMap[$role] ?? ucfirst($role);
            $initial   = strtoupper(substr($user['name'] ?? '?', 0, 1));
            $phone     = $user['no_hp'] ?? '-';
            $joinDate  = !empty($user['created_at'])
                ? \Carbon\Carbon::parse($user['created_at'])->translatedFormat('j F Y') : '-';
            // All detail fields for modal (JSON-safe)
            $gender    = $user['gender'] === 'L' ? 'Laki-laki' : ($user['gender'] === 'P' ? 'Perempuan' : '-');
            $birthDate = !empty($user['tanggal_lahir'])
                ? \Carbon\Carbon::parse($user['tanggal_lahir'])->translatedFormat('j F Y') : '-';
            $age = !empty($user['tanggal_lahir'])
                ? \Carbon\Carbon::parse($user['tanggal_lahir'])->age . ' tahun' : '';
            $address = collect([$user['alamat'] ?? null, $user['kota'] ?? null, $user['provinsi'] ?? null])
                ->filter()->implode(', ') ?: '-';
            $updatedDate = !empty($user['updated_at'])
                ? \Carbon\Carbon::parse($user['updated_at'])->translatedFormat('j F Y') : '-';
        @endphp

        <div class="user-card bg-white rounded-2xl p-4 shadow-sm shadow-plum/10 border border-plum-soft/60"
             data-name="{{ strtolower($user['name'] ?? '') }}"
             data-email="{{ strtolower($user['email'] ?? '') }}"
             data-phone="{{ $user['no_hp'] ?? '' }}"
             data-role="{{ $role }}"
             data-status="{{ $isActive ? 'active' : 'inactive' }}">

            {{-- Top row --}}
            <div class="flex items-start gap-3 mb-3">
                @if(!empty($user['foto']))
                <img src="{{ $user['foto'] }}" alt="{{ $user['name'] }}"
                     class="w-11 h-11 rounded-xl object-cover shrink-0">
                @else
                <div class="w-11 h-11 rounded-xl bg-plum flex items-center justify-center shrink-0 text-white text-base font-bold">
                    {{ $initial }}
                </div>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="text-plum-dark font-bold text-sm truncate">{{ $user['name'] }}</p>
                    <p class="text-plum-muted text-xs truncate mt-0.5">{{ $user['email'] }}</p>
                </div>

                <div class="flex flex-col items-end gap-1.5 shrink-0">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-{{ $role }}">{{ $roleLabel }}</span>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full
                        {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="flex gap-4 mb-3">
                <div class="flex items-center gap-1.5">
                    <ion-icon name="call-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                    <span class="text-[11px] text-plum-muted font-medium">{{ $phone }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <ion-icon name="calendar-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                    <span class="text-[11px] text-plum-muted font-medium">{{ $joinDate }}</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">

                {{-- Detail → open modal --}}
                <button type="button"
                    onclick="openDetail({
                        id:       {{ $user['id'] }},
                        name:     {{ json_encode($user['name']) }},
                        email:    {{ json_encode($user['email']) }},
                        role:     {{ json_encode($roleLabel) }},
                        roleCls:  {{ json_encode($role) }},
                        isActive: {{ $isActive ? 'true' : 'false' }},
                        phone:    {{ json_encode($phone) }},
                        address:  {{ json_encode($address) }},
                        gender:   {{ json_encode($gender) }},
                        birth:    {{ json_encode($birthDate) }},
                        age:      {{ json_encode($age) }},
                        joined:   {{ json_encode($joinDate) }},
                        updated:  {{ json_encode($updatedDate) }},
                        foto:     {{ json_encode($user['foto'] ?? '') }},
                        editUrl:  {{ json_encode(route('admin-kelola-akun.edit', $user['id'])) }}
                    })"
                    class="act-btn flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl bg-plum-soft text-plum text-xs font-semibold">
                    <ion-icon name="eye-outline" style="font-size:14px;"></ion-icon>
                    Detail
                </button>

                {{-- Toggle Status --}}
                @if($isActive)
                <form action="{{ route('admin-kelola-akun.status', $user['id']) }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="is_active" value="0">
                    <button type="submit"
                        onclick="return confirm('Nonaktifkan akun {{ addslashes($user['name']) }}?')"
                        class="act-btn w-full flex items-center justify-center gap-1.5 py-2 rounded-xl bg-orange-50 text-orange-600 text-xs font-semibold">
                        <ion-icon name="pause-circle-outline" style="font-size:14px;"></ion-icon>
                        Nonaktifkan
                    </button>
                </form>
                @else
                <form action="{{ route('admin-kelola-akun.status', $user['id']) }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="is_active" value="1">
                    <button type="submit"
                        onclick="return confirm('Aktifkan akun {{ addslashes($user['name']) }}?')"
                        class="act-btn w-full flex items-center justify-center gap-1.5 py-2 rounded-xl bg-green-50 text-green-700 text-xs font-semibold">
                        <ion-icon name="play-circle-outline" style="font-size:14px;"></ion-icon>
                        Aktifkan
                    </button>
                </form>
                @endif

                {{-- Delete --}}
                <form action="{{ route('admin-kelola-akun.destroy', $user['id']) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Hapus akun {{ addslashes($user['name']) }}? Tindakan ini tidak dapat dibatalkan.')"
                        class="act-btn w-full flex items-center justify-center gap-1.5 py-2 rounded-xl bg-red-50 text-red-500 text-xs font-semibold">
                        <ion-icon name="trash-outline" style="font-size:14px;"></ion-icon>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        </div>
    </div>

    {{-- ─── FAB ADD — inside phone-frame, above bottom nav ─────────────────── --}}
    {{-- Using absolute inside the relative phone-frame --}}
    <div class="fab-wrap fab-in">
        <a href="{{ route('admin-kelola-akun.create') }}"
           class="w-14 h-14 rounded-2xl bg-plum shadow-xl shadow-plum/40 flex items-center justify-center block">
            <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
        </a>
    </div>

    {{-- ─── BOTTOM NAV ──────────────────────────────────────────────────────── --}}
    @include('partials.bottom-nav', ['active' => 'home'])

</div>{{-- /phone-frame --}}
</div>{{-- /phone-wrapper --}}

{{-- ═══════════════════════════════════════════════════════════════════════════
     FILTER MODAL
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="filterModal" class="modal-backdrop">
    <div class="modal-sheet">
        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
        </div>
        <div class="flex items-center justify-between px-5 py-4 border-b border-plum-soft">
            <h2 class="text-plum-dark text-lg font-extrabold">Filter Pengguna</h2>
            <button onclick="closeFilter()" class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div class="px-5 py-5 space-y-6">
            <div>
                <p class="text-plum-dark font-bold text-sm mb-3">Peran</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['all'=>'Semua','admin'=>'Admin','konsultan'=>'Konsultan','majikan'=>'Majikan','nanny'=>'Nanny'] as $val=>$label)
                    <button onclick="setRole('{{ $val }}')" id="role-{{ $val }}"
                        class="chip text-xs font-semibold px-4 py-2 rounded-xl {{ $val === 'all' ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="text-plum-dark font-bold text-sm mb-3">Status</p>
                <div class="flex gap-2">
                    @foreach(['all'=>'Semua','active'=>'Aktif','inactive'=>'Nonaktif'] as $val=>$label)
                    <button onclick="setStatus('{{ $val }}')" id="status-{{ $val }}"
                        class="chip text-xs font-semibold px-4 py-2 rounded-xl {{ $val === 'all' ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-5 pb-8">
            <button onclick="resetAll()" class="flex-1 py-3 rounded-2xl bg-plum-soft text-plum font-bold text-sm">Reset</button>
            <button onclick="applyFilter()" class="flex-1 py-3 rounded-2xl bg-plum text-white font-bold text-sm">Terapkan Filter</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     DETAIL MODAL (DIPERBAIKI)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="detailModal" class="modal-backdrop">
    <div class="modal-sheet" id="detailSheet">
        {{-- Handle — TETAP (TIDAK IKUT SCROLL) --}}
        <div class="handle-container">
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
            </div>

            {{-- Modal Header bar — TETAP (TIDAK IKUT SCROLL) --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-plum-soft">
                <h2 class="text-plum-dark text-lg font-extrabold">Detail Pengguna</h2>
                <button onclick="closeDetail()" class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                    <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </button>
            </div>
        </div>

        {{-- SCROLLABLE CONTENT — User profile + semua informasi di bawahnya --}}
        <div class="scrollable-content">
            {{-- ── User profile row ── --}}
            <div class="px-5 pt-5 pb-4 flex items-center gap-4 border-b border-plum-soft/50">
                <div id="dAvatar" class="w-16 h-16 rounded-2xl bg-plum flex items-center justify-center shrink-0 text-white text-2xl font-bold overflow-hidden"></div>
                <div class="flex-1 min-w-0">
                    <p id="dName"  class="text-plum-dark font-extrabold text-lg leading-tight truncate"></p>
                    <p id="dEmail" class="text-plum-muted text-sm truncate mt-0.5"></p>
                    <div class="flex gap-2 mt-2 flex-wrap">
                        <span id="dRoleBadge"   class="text-[11px] font-bold px-2.5 py-1 rounded-full"></span>
                        <span id="dStatusBadge" class="text-[11px] font-bold px-2.5 py-1 rounded-full"></span>
                    </div>
                </div>
            </div>

            {{-- ── Sections ── --}}
            <div class="px-5 py-4 space-y-5">
                {{-- Kontak --}}
                <div>
                    <p class="detail-section-title">
                        Informasi Kontak
                    </p>
                    <div class="detail-row">
                        <ion-icon name="call-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">No. HP</p>
                            <p id="dPhone" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                    <div class="detail-row">
                        <ion-icon name="location-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Alamat</p>
                            <p id="dAddress" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                </div>

                {{-- Pribadi --}}
                <div>
                    <p class="detail-section-title">
                        Informasi Pribadi
                    </p>
                    <div class="detail-row">
                        <ion-icon name="person-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Jenis Kelamin</p>
                            <p id="dGender" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                    <div class="detail-row">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Tanggal Lahir</p>
                            <p id="dBirth" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                </div>

                {{-- Akun --}}
                <div>
                    <p class="detail-section-title">
                        Informasi Akun
                    </p>
                    <div class="detail-row">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Bergabung</p>
                            <p id="dJoined" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                    <div class="detail-row">
                        <ion-icon name="time-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Update Terakhir</p>
                            <p id="dUpdated" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Edit button ── --}}
            <div class="px-5 pb-8">
                <a id="dEditBtn" href="#"
                   class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-plum text-white font-bold text-sm shadow-lg shadow-plum/30">
                    <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                    Ubah Data Akun
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ─── JAVASCRIPT ──────────────────────────────────────────────────────────── --}}
<script>
// ── Clock ─────────────────────────────────────────────────────────────────────
(function() {
    const el = document.getElementById('statusTime');
    function tick() {
        if (!el) return;
        const n = new Date();
        el.textContent = String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0');
    }
    tick(); setInterval(tick, 30000);
})();

// ── Toast auto-dismiss ────────────────────────────────────────────────────────
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);

// ══════════════════════════════════════════════════════════════════════════════
// FILTER MODAL
// ══════════════════════════════════════════════════════════════════════════════
let activeRole   = 'all';
let activeStatus = 'all';

function openFilter()  { document.getElementById('filterModal').classList.add('open'); }
function closeFilter() { document.getElementById('filterModal').classList.remove('open'); }

document.getElementById('filterModal').addEventListener('click', function(e) {
    if (e.target === this) closeFilter();
});

function setRole(val) {
    activeRole = val;
    document.querySelectorAll('[id^="role-"]').forEach(b => b.classList.remove('active'));
    document.getElementById('role-' + val).classList.add('active');
}
function setStatus(val) {
    activeStatus = val;
    document.querySelectorAll('[id^="status-"]').forEach(b => b.classList.remove('active'));
    document.getElementById('status-' + val).classList.add('active');
}
function applyFilter()  { closeFilter(); filterUsers(); updateActiveChips(); }

function resetAll() {
    activeRole = 'all'; activeStatus = 'all';
    document.querySelectorAll('[id^="role-"]').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('[id^="status-"]').forEach(b => b.classList.remove('active'));
    document.getElementById('role-all').classList.add('active');
    document.getElementById('status-all').classList.add('active');
    document.getElementById('searchInput').value = '';
    closeFilter(); filterUsers(); updateActiveChips();
}
function clearRoleFilter()   { setRole('all');   filterUsers(); updateActiveChips(); }
function clearStatusFilter() { setStatus('all'); filterUsers(); updateActiveChips(); }
function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearch').classList.add('hidden');
    filterUsers();
}

// ── Filter Logic ──────────────────────────────────────────────────────────────
function filterUsers() {
    const query    = document.getElementById('searchInput').value.toLowerCase().trim();
    const cards    = document.querySelectorAll('#cardsContainer .user-card');
    const clearBtn = document.getElementById('clearSearch');
    clearBtn.classList.toggle('hidden', !query);

    let visible = 0;
    cards.forEach(card => {
        const matchSearch = !query ||
            (card.dataset.name  || '').includes(query) ||
            (card.dataset.email || '').includes(query) ||
            (card.dataset.phone || '').includes(query);
        const matchRole   = activeRole   === 'all' || card.dataset.role   === activeRole;
        const matchStatus = activeStatus === 'all' || card.dataset.status === activeStatus;
        const show = matchSearch && matchRole && matchStatus;
        card.classList.toggle('hidden', !show);
        if (show) visible++;
    });

    document.getElementById('resultCount').textContent = visible;
    document.getElementById('emptyState').classList.toggle('hidden', visible > 0);
    document.getElementById('filterDot').classList.toggle('hidden', activeRole === 'all' && activeStatus === 'all');
}

function updateActiveChips() {
    const roleLabels   = {all:'Semua',admin:'Admin',konsultan:'Konsultan',majikan:'Majikan',nanny:'Nanny'};
    const statusLabels = {all:'Semua',active:'Aktif',inactive:'Nonaktif'};
    const showRole   = activeRole !== 'all';
    const showStatus = activeStatus !== 'all';

    const rc = document.getElementById('roleChip');
    const sc = document.getElementById('statusChip');
    const af = document.getElementById('activeFilters');

    rc.classList.toggle('hidden', !showRole);  rc.classList.toggle('flex', showRole);
    sc.classList.toggle('hidden', !showStatus); sc.classList.toggle('flex', showStatus);
    af.classList.toggle('hidden', !showRole && !showStatus);
    af.classList.toggle('flex', showRole || showStatus);

    document.getElementById('roleChipLabel').textContent   = roleLabels[activeRole]     ?? activeRole;
    document.getElementById('statusChipLabel').textContent = statusLabels[activeStatus] ?? activeStatus;
}

// ══════════════════════════════════════════════════════════════════════════════
// DETAIL MODAL
// ══════════════════════════════════════════════════════════════════════════════
const roleBadgeClasses = {
    admin:     'bg-purple-50 text-purple-800',
    konsultan: 'bg-blue-50 text-blue-800',
    majikan:   'bg-green-50 text-green-800',
    nanny:     'bg-orange-50 text-orange-800',
};

function openDetail(u) {
    // Avatar
    const av = document.getElementById('dAvatar');
    if (u.foto) {
        av.innerHTML = `<img src="${u.foto}" class="w-full h-full object-cover">`;
    } else {
        av.innerHTML = u.name.charAt(0).toUpperCase();
        av.style.background = '#7B1E5A';
    }

    // Basic
    document.getElementById('dName').textContent  = u.name;
    document.getElementById('dEmail').textContent = u.email;

    // Role badge
    const rb = document.getElementById('dRoleBadge');
    rb.textContent  = u.role;
    rb.className    = 'text-[11px] font-bold px-2.5 py-1 rounded-full ' + (roleBadgeClasses[u.roleCls] || 'bg-gray-100 text-gray-700');

    // Status badge
    const sb = document.getElementById('dStatusBadge');
    sb.textContent = u.isActive ? 'Aktif' : 'Nonaktif';
    sb.className   = 'text-[11px] font-bold px-2.5 py-1 rounded-full ' + (u.isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600');

    // Contact
    document.getElementById('dPhone').textContent   = u.phone;
    document.getElementById('dAddress').textContent = u.address;

    // Personal
    document.getElementById('dGender').textContent = u.gender;
    document.getElementById('dBirth').textContent  = u.birth + (u.age ? ` (${u.age})` : '');

    // Account
    document.getElementById('dJoined').textContent  = u.joined;
    document.getElementById('dUpdated').textContent = u.updated;

    // Edit URL
    document.getElementById('dEditBtn').href = u.editUrl;

    document.getElementById('detailModal').classList.add('open');
}

function closeDetail() {
    document.getElementById('detailModal').classList.remove('open');
}

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});
</script>
@include('partials.auth-guard')
</body>
</html>