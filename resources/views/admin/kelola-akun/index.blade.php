@extends('layouts.app')

@section('title', 'Kelola Akun')

@push('styles')
<style>
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

    .user-card { transition:transform .15s ease,box-shadow .15s ease; }
    .user-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(139,70,211,.12); }

    @keyframes spin { to{transform:rotate(360deg);} }
    .page-loader { animation:spin .6s linear infinite; }

    .badge-admin     { background:#F3E5F5; color:#6A1B9A; }
    .badge-konsultan { background:#E3F2FD; color:#0D47A1; }
    .badge-majikan   { background:#E8F5E9; color:#1B5E20; }
    .badge-nanny     { background:#FFF3E0; color:#E65100; }

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
        max-width: 390px;
        border-radius: 24px 24px 0 0;
        transform: translateY(100%);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
        display: flex;
        flex-direction: column;
        max-height: 85vh;
    }
    .modal-backdrop.open .modal-sheet { transform: translateY(0); }
    .handle-container {
        flex-shrink: 0;
        background: white;
        border-radius: 24px 24px 0 0;
    }
    .scrollable-content {
        overflow-y: auto;
        flex: 1;
        -webkit-overflow-scrolling: touch;
    }
    .handle-container,
    .scrollable-content { background: white; }

    .chip { border:1.5px solid #E7DDF2; background:#F0EDFB; color:#8B46D3; }
    .chip.active { background:#8B46D3; border-color:#8B46D3; color:#fff; }

    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }

    body.modal-open { overflow:hidden; }

    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }

    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(.92); }

    .fab-wrap {
        position: fixed;
        bottom: 80px;
        right: calc(50% - 175px);
        z-index: 30;
    }
    @media (max-width: 639px) {
        .fab-wrap { right: 20px; }
    }
    @keyframes fabIn {
        0%   { transform:scale(0) rotate(-20deg); opacity:0; }
        70%  { transform:scale(1.1) rotate(5deg); }
        100% { transform:scale(1) rotate(0); opacity:1; }
    }
    .fab-in { animation:fabIn .5s cubic-bezier(.34,1.56,.64,1) .3s forwards; opacity:0; }

    .detail-section-title {
        font-size:.75rem; font-weight:700; color:#8B46D3;
        text-transform:uppercase; letter-spacing:.06em;
        margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem;
    }
    .detail-row { display:flex; align-items:flex-start; gap:.75rem; padding:.5rem 0; }
    .detail-val { font-size:.875rem; color:#1E1B2E; font-weight:600; flex:1; }

    .search-wrapper:focus-within {
        border-color: #8B46D3;
        box-shadow: 0 0 0 3px rgba(139,70,211,0.14);
    }
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
            <span class="text-white text-[17px] font-extrabold tracking-wide">Kelola Akun</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">
                @if($pagination)
                    {{ $pagination['total'] }} pengguna terdaftar
                @else
                    {{ count($users) }} pengguna terdaftar
                @endif
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

    {{-- Search + Filter --}}
    <div class="flex gap-3 anim delay-2">
        <div class="search-wrapper flex-1 flex items-center gap-2 bg-white rounded-full px-4 py-2.5 border border-[#DDD6EF]">
            <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
            <input id="searchInput" type="text" placeholder="Cari nama, email, atau no. HP..."
                class="flex-1 bg-transparent text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] outline-none"
                oninput="filterUsers()">
            <button id="clearSearch" onclick="clearSearch()" class="hidden">
                <ion-icon name="close-circle" style="font-size:16px;color:#8B86A5;"></ion-icon>
            </button>
        </div>
        <button onclick="openFilter()"
            class="w-9 h-9 bg-white border border-[#DDD6EF] rounded-full flex items-center justify-center flex-shrink-0 active:scale-95 transition-all">
            <ion-icon name="options-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
            <span id="filterDot" class="hidden w-2 h-2 rounded-full bg-[#8B46D3] absolute mt-6 ml-5"></span>
        </button>
    </div>

    {{-- Active Filter Chips --}}
    <div id="activeFilters" class="hidden flex gap-2 flex-wrap">
        <span id="roleChip" class="hidden items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-full bg-[#8B46D3] text-white">
            <span id="roleChipLabel"></span>
            <button onclick="clearRoleFilter()"><ion-icon name="close" style="font-size:12px;"></ion-icon></button>
        </span>
        <span id="statusChip" class="hidden items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-full bg-[#8B46D3] text-white">
            <span id="statusChipLabel"></span>
            <button onclick="clearStatusFilter()"><ion-icon name="close" style="font-size:12px;"></ion-icon></button>
        </span>
    </div>

    {{-- Result count --}}
    <div class="flex items-center justify-between">
        <p class="text-xs font-bold text-[#8B86A5]">
            Menampilkan <span id="resultCount" class="text-[#8B46D3]">{{ count($users) }}</span> pengguna
        </p>
        @if($pagination)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button id="pagePrev" onclick="goToPage({{ $pagination['current_page'] - 1 }})"
                class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : 'hover:border-[#8B46D3] hover:text-[#8B46D3]' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]">
                <span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}
            </span>
            <button id="pageNext" onclick="goToPage({{ $pagination['current_page'] + 1 }})"
                class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : 'hover:border-[#8B46D3] hover:text-[#8B46D3]' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
        @endif
    </div>

    {{-- User List --}}
    <div id="userList" class="space-y-3">

        {{-- Empty state --}}
        <div id="emptyState" class="{{ count($users) > 0 ? 'hidden' : '' }} flex flex-col items-center py-16">
            <div class="float-anim w-20 h-20 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
                <ion-icon name="people-outline" style="font-size:36px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-extrabold text-base mb-1">Tidak ada pengguna</h3>
            <p class="text-[#8B86A5] text-xs text-center">Coba ubah filter atau kata kunci pencarian</p>
            <button onclick="resetAll()"
                class="mt-4 px-5 py-2 rounded-xl bg-[#8B46D3] text-white text-sm font-bold">
                Reset Filter
            </button>
        </div>

        {{-- User Cards --}}
        <div id="cardsContainer" class="space-y-3">
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

        <div class="user-card bg-white rounded-2xl p-4 border border-[#DDD6EF]"
             data-name="{{ strtolower($user['name'] ?? '') }}"
             data-email="{{ strtolower($user['email'] ?? '') }}"
             data-phone="{{ $user['no_hp'] ?? '' }}"
             data-role="{{ $role }}"
             data-status="{{ $isActive ? 'active' : 'inactive' }}">

            <div class="flex items-start gap-3 mb-3">
                @if(!empty($user['foto']))
                <img src="{{ $user['foto'] }}" alt="{{ $user['name'] }}"
                     class="w-11 h-11 rounded-xl object-cover shrink-0">
                @else
                <div class="w-11 h-11 rounded-xl bg-[#8B46D3] flex items-center justify-center shrink-0 text-white text-base font-bold">
                    {{ $initial }}
                </div>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="text-[#1E1B2E] font-bold text-sm truncate">{{ $user['name'] }}</p>
                    <p class="text-[#8B86A5] text-xs truncate mt-0.5">{{ $user['email'] }}</p>
                </div>

                <div class="flex flex-col items-end gap-1.5 shrink-0">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-{{ $role }}">{{ $roleLabel }}</span>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full
                        {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            <div class="flex gap-4 mb-3">
                <div class="flex items-center gap-1.5">
                    <ion-icon name="call-outline" style="font-size:13px;color:#8B46D3;"></ion-icon>
                    <span class="text-[11px] text-[#8B86A5] font-medium">{{ $phone }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <ion-icon name="calendar-outline" style="font-size:13px;color:#8B46D3;"></ion-icon>
                    <span class="text-[11px] text-[#8B86A5] font-medium">{{ $joinDate }}</span>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="button"
                    onclick="openDetail({{ json_encode([
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $roleLabel,
                        'roleCls' => $role,
                        'isActive' => $isActive,
                        'phone' => $phone,
                        'address' => $address,
                        'gender' => $gender,
                        'birth' => $birthDate,
                        'age' => $age,
                        'joined' => $joinDate,
                        'updated' => $updatedDate,
                        'foto' => $user['foto'] ?? '',
                        'editUrl' => route('admin-kelola-akun.edit', $user['id'])
                    ]) }})"
                    class="act-btn flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl bg-[#EDE9FE] text-[#8B46D3] text-xs font-bold">
                    <ion-icon name="eye-outline" style="font-size:14px;"></ion-icon>
                    Detail
                </button>

                @if($isActive)
                <form action="{{ route('admin-kelola-akun.status', $user['id']) }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="is_active" value="0">
                    <button type="submit"
                        onclick="return confirm('Nonaktifkan akun {{ addslashes($user['name']) }}?')"
                        class="act-btn w-full flex items-center justify-center gap-1.5 py-2 rounded-xl bg-orange-50 text-orange-600 text-xs font-bold">
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
                        class="act-btn w-full flex items-center justify-center gap-1.5 py-2 rounded-xl bg-green-50 text-green-700 text-xs font-bold">
                        <ion-icon name="play-circle-outline" style="font-size:14px;"></ion-icon>
                        Aktifkan
                    </button>
                </form>
                @endif

                <form action="{{ route('admin-kelola-akun.destroy', $user['id']) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Hapus akun {{ addslashes($user['name']) }}? Tindakan ini tidak dapat dibatalkan.')"
                        class="act-btn w-full flex items-center justify-center gap-1.5 py-2 rounded-xl bg-red-50 text-red-500 text-xs font-bold">
                        <ion-icon name="trash-outline" style="font-size:14px;"></ion-icon>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        </div>
    </div>

</div>{{-- /rounded-top section --}}

{{-- Page loading overlay --}}
<div id="pageLoader" class="hidden fixed inset-0 z-50 bg-white/60 flex items-center justify-center">
    <div class="flex items-center gap-3 bg-white rounded-2xl px-6 py-4 shadow-xl border border-[#EDE9FE]">
        <div class="page-loader w-5 h-5 border-2 border-[#EDE9FE] border-t-[#8B46D3] rounded-full"></div>
        <span class="text-sm font-bold text-[#8B46D3]">Memuat...</span>
    </div>
</div>

{{-- FAB --}}
<div class="fab-wrap fab-in">
    <a href="{{ route('admin-kelola-akun.create') }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>

{{-- FILTER MODAL --}}
<div id="filterModal" class="modal-backdrop">
    <div class="modal-sheet">
        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
        </div>
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#EDE9FE]">
            <h2 class="text-[#1E1B2E] text-lg font-extrabold">Filter Pengguna</h2>
            <button onclick="closeFilter()" class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="px-5 py-5 space-y-6">
            <div>
                <p class="text-[#1E1B2E] font-bold text-sm mb-3">Peran</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['all'=>'Semua','admin'=>'Admin','konsultan'=>'Konsultan','majikan'=>'Majikan','nanny'=>'Nanny'] as $val=>$label)
                    <button onclick="setRole('{{ $val }}')" id="role-{{ $val }}"
                        class="chip text-xs font-bold px-4 py-2 rounded-xl {{ $val === 'all' ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="text-[#1E1B2E] font-bold text-sm mb-3">Status</p>
                <div class="flex gap-2">
                    @foreach(['all'=>'Semua','active'=>'Aktif','inactive'=>'Nonaktif'] as $val=>$label)
                    <button onclick="setStatus('{{ $val }}')" id="status-{{ $val }}"
                        class="chip text-xs font-bold px-4 py-2 rounded-xl {{ $val === 'all' ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-5 pb-20">
            <button onclick="resetAll()" class="flex-1 py-3 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] font-bold text-sm">Reset</button>
            <button onclick="applyFilter()" class="flex-1 py-3 rounded-2xl bg-[#8B46D3] text-white font-bold text-sm">Terapkan Filter</button>
        </div>
    </div>
</div>

{{-- DETAIL MODAL --}}
<div id="detailModal" class="modal-backdrop">
    <div class="modal-sheet" id="detailSheet">
        <div class="handle-container">
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#EDE9FE]">
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">Detail Pengguna</h2>
                <button onclick="closeDetail()" class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </button>
            </div>
        </div>

        <div class="scrollable-content">
            <div class="px-5 pt-5 pb-4 flex items-center gap-4 border-b border-[#EDE9FE]/50">
                <div id="dAvatar" class="w-16 h-16 rounded-2xl bg-[#8B46D3] flex items-center justify-center shrink-0 text-white text-2xl font-bold overflow-hidden"></div>
                <div class="flex-1 min-w-0">
                    <p id="dName"  class="text-[#1E1B2E] font-extrabold text-lg leading-tight truncate"></p>
                    <p id="dEmail" class="text-[#8B86A5] text-sm truncate mt-0.5"></p>
                    <div class="flex gap-2 mt-2 flex-wrap">
                        <span id="dRoleBadge"   class="text-[11px] font-bold px-2.5 py-1 rounded-full"></span>
                        <span id="dStatusBadge" class="text-[11px] font-bold px-2.5 py-1 rounded-full"></span>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 space-y-5">
                <div>
                    <p class="detail-section-title">Informasi Kontak</p>
                    <div class="detail-row">
                        <ion-icon name="call-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">No. HP</p>
                            <p id="dPhone" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                    <div class="detail-row">
                        <ion-icon name="location-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Alamat</p>
                            <p id="dAddress" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="detail-section-title">Informasi Pribadi</p>
                    <div class="detail-row">
                        <ion-icon name="person-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Jenis Kelamin</p>
                            <p id="dGender" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                    <div class="detail-row">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Tanggal Lahir</p>
                            <p id="dBirth" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="detail-section-title">Informasi Akun</p>
                    <div class="detail-row">
                        <ion-icon name="calendar-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Bergabung</p>
                            <p id="dJoined" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                    <div class="detail-row">
                        <ion-icon name="time-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Update Terakhir</p>
                            <p id="dUpdated" class="detail-val mt-0.5"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-5 pb-20">
                <a id="dEditBtn" href="#"
                   class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-[#8B46D3] text-white font-bold text-sm shadow-lg shadow-[#8B46D3]/30">
                    <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                    Ubah Data Akun
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Search & Filter ─────────────────────────────────────────────────────────
let activeRole   = 'all';
let activeStatus = 'all';

function openFilter()  { document.getElementById('filterModal').classList.add('open'); document.body.classList.add('modal-open'); }
function closeFilter() { document.getElementById('filterModal').classList.remove('open'); document.body.classList.remove('modal-open'); }

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

// ── Detail Modal ──────────────────────────────────────────────────────────────
const roleBadgeClasses = {
    admin:     'bg-purple-50 text-purple-800',
    konsultan: 'bg-blue-50 text-blue-800',
    majikan:   'bg-green-50 text-green-800',
    nanny:     'bg-orange-50 text-orange-800',
};

function openDetail(u) {
    const av = document.getElementById('dAvatar');
    if (u.foto) {
        av.innerHTML = `<img src="${u.foto}" class="w-full h-full object-cover">`;
    } else {
        av.innerHTML = u.name.charAt(0).toUpperCase();
        av.style.background = '#8B46D3';
    }

    document.getElementById('dName').textContent  = u.name;
    document.getElementById('dEmail').textContent = u.email;

    const rb = document.getElementById('dRoleBadge');
    rb.textContent  = u.role;
    rb.className    = 'text-[11px] font-bold px-2.5 py-1 rounded-full ' + (roleBadgeClasses[u.roleCls] || 'bg-gray-100 text-gray-700');

    const sb = document.getElementById('dStatusBadge');
    sb.textContent = u.isActive ? 'Aktif' : 'Nonaktif';
    sb.className   = 'text-[11px] font-bold px-2.5 py-1 rounded-full ' + (u.isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600');

    document.getElementById('dPhone').textContent   = u.phone;
    document.getElementById('dAddress').textContent = u.address;
    document.getElementById('dGender').textContent = u.gender;
    document.getElementById('dBirth').textContent  = u.birth + (u.age ? ` (${u.age})` : '');
    document.getElementById('dJoined').textContent  = u.joined;
    document.getElementById('dUpdated').textContent = u.updated;
    document.getElementById('dEditBtn').href = u.editUrl;

    document.getElementById('detailModal').classList.add('open');
    document.body.classList.add('modal-open');
}

function closeDetail() {
    document.getElementById('detailModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});

// ── Pagination ──────────────────────────────────────────────────────────
function goToPage(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    document.getElementById('pageLoader').classList.remove('hidden');
    window.location.href = url.toString();
}

// Toast auto-dismiss
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush
