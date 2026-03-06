{{-- resources/views/admin/kelola-akun/show.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Pengguna</title>
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
        @media (min-width:640px) {
            .phone-wrapper { display:flex;align-items:flex-start;justify-content:center;min-height:100vh;padding:32px 0;background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%); }
            .phone-frame { width:420px;min-height:844px;border-radius:44px;box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020;overflow:hidden;position:relative; }
        }
        @media (max-width:639px) { .phone-wrapper{min-height:100vh;} .phone-frame{min-height:100vh;} }
        .header-bg { background: linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%); }
        .header-wave { border-radius: 0 0 30px 30px; }
        @keyframes slideUp { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }
        .anim-up{animation:slideUp .35s ease forwards;}
        .delay-1{animation-delay:.05s;opacity:0;}
        .delay-2{animation-delay:.12s;opacity:0;}
        .delay-3{animation-delay:.20s;opacity:0;}
        .no-scrollbar::-webkit-scrollbar{display:none;}
        .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        .badge-admin     { background:#F3E5F5;color:#6A1B9A; }
        .badge-konsultan { background:#E3F2FD;color:#0D47A1; }
        .badge-majikan   { background:#E8F5E9;color:#1B5E20; }
        .badge-nanny     { background:#FFF3E0;color:#E65100; }
        .act-btn { transition:transform .1s ease; }
        .act-btn:active { transform:scale(0.94); }
    </style>
</head>
<body>
@php
    $u        = $user;
    $isActive = (int)($u['is_active'] ?? 0) === 1;
    $role     = strtolower($u['role'] ?? 'nanny');
    $roleMap  = ['admin'=>'Admin','konsultan'=>'Konsultan','majikan'=>'Majikan','nanny'=>'Nanny'];
    $roleLabel = $roleMap[$role] ?? ucfirst($role);
    $initial  = strtoupper(substr($u['name'] ?? '?', 0, 1));
    $genderMap = ['L'=>'Laki-laki','P'=>'Perempuan'];
    $gender    = $genderMap[$u['gender'] ?? ''] ?? '-';
    $address   = collect([$u['alamat'] ?? null, $u['kota'] ?? null, $u['provinsi'] ?? null])->filter()->implode(', ') ?: '-';
    $birthDate = !empty($u['tanggal_lahir'])
        ? \Carbon\Carbon::parse($u['tanggal_lahir'])->translatedFormat('j F Y')
        : '-';
    $age = !empty($u['tanggal_lahir'])
        ? \Carbon\Carbon::parse($u['tanggal_lahir'])->age . ' tahun'
        : '';
    $joinDate = !empty($u['created_at'])
        ? \Carbon\Carbon::parse($u['created_at'])->translatedFormat('j F Y')
        : '-';
    $updateDate = !empty($u['updated_at'])
        ? \Carbon\Carbon::parse($u['updated_at'])->translatedFormat('j F Y')
        : '-';
@endphp

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    {{-- Status bar --}}
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    {{-- Header --}}
    <div class="header-bg header-wave px-5 pt-10 pb-8 relative shrink-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10"></div>
        <div class="relative flex items-center gap-3 anim-up delay-1">
            <a href="{{ route('admin-kelola-akun') }}"
               class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
            </a>
            <div>
                <h1 class="text-white text-xl font-extrabold">Detail Pengguna</h1>
                <p class="text-white/60 text-xs mt-0.5">Informasi lengkap akun</p>
            </div>
        </div>
    </div>

    {{-- Scrollable body --}}
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 pt-5 pb-10 space-y-4">

        {{-- Profile Card --}}
        <div class="anim-up delay-1 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 border border-plum-soft/60">
            <div class="flex items-center gap-4">
                @if(!empty($u['foto']))
                <img src="{{ $u['foto'] }}" alt="{{ $u['name'] }}"
                     class="w-16 h-16 rounded-2xl object-cover shrink-0">
                @else
                <div class="w-16 h-16 rounded-2xl bg-plum flex items-center justify-center shrink-0 text-white text-2xl font-bold">
                    {{ $initial }}
                </div>
                @endif

                <div class="flex-1 min-w-0">
                    <h2 class="text-plum-dark font-extrabold text-lg leading-tight">{{ $u['name'] }}</h2>
                    <p class="text-plum-muted text-sm truncate mt-0.5">{{ $u['email'] }}</p>
                    <div class="flex gap-2 mt-2 flex-wrap">
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full badge-{{ $role }}">{{ $roleLabel }}</span>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full
                            {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="anim-up delay-2 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 border border-plum-soft/60">
            <h3 class="text-plum-dark font-extrabold text-sm mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center">
                    <ion-icon name="call-outline" style="font-size:14px;color:#7B1E5A;"></ion-icon>
                </div>
                Informasi Kontak
            </h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <ion-icon name="call-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                    <div>
                        <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">No. HP</p>
                        <p class="text-plum-dark text-sm font-semibold mt-0.5">{{ $u['no_hp'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <ion-icon name="location-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                    <div>
                        <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Alamat</p>
                        <p class="text-plum-dark text-sm font-semibold mt-0.5">{{ $address }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Info --}}
        <div class="anim-up delay-2 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 border border-plum-soft/60">
            <h3 class="text-plum-dark font-extrabold text-sm mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center">
                    <ion-icon name="person-outline" style="font-size:14px;color:#7B1E5A;"></ion-icon>
                </div>
                Informasi Pribadi
            </h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <ion-icon name="person-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                    <div>
                        <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Jenis Kelamin</p>
                        <p class="text-plum-dark text-sm font-semibold mt-0.5">{{ $gender }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <ion-icon name="calendar-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                    <div>
                        <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Tanggal Lahir</p>
                        <p class="text-plum-dark text-sm font-semibold mt-0.5">
                            {{ $birthDate }}{{ $age ? " ($age)" : '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="anim-up delay-3 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 border border-plum-soft/60">
            <h3 class="text-plum-dark font-extrabold text-sm mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center">
                    <ion-icon name="shield-outline" style="font-size:14px;color:#7B1E5A;"></ion-icon>
                </div>
                Informasi Akun
            </h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <ion-icon name="calendar-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                    <div>
                        <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Bergabung</p>
                        <p class="text-plum-dark text-sm font-semibold mt-0.5">{{ $joinDate }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <ion-icon name="time-outline" style="font-size:16px;color:#7B1E5A;flex-shrink:0;margin-top:2px;"></ion-icon>
                    <div>
                        <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wider">Update Terakhir</p>
                        <p class="text-plum-dark text-sm font-semibold mt-0.5">{{ $updateDate }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="anim-up delay-3 space-y-3">
            {{-- Edit --}}
            <a href="{{ route('admin-kelola-akun.edit', $u['id']) }}"
               class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-plum text-white font-bold text-sm shadow-lg shadow-plum/30">
                <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                Ubah Data Akun
            </a>

            {{-- Toggle Status --}}
            <form action="{{ route('admin-kelola-akun.status', $u['id']) }}" method="POST">
                @csrf
                <input type="hidden" name="is_active" value="{{ $isActive ? 0 : 1 }}">
                <button type="submit"
                    onclick="return confirm('{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')"
                    class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl font-bold text-sm
                        {{ $isActive ? 'bg-orange-50 text-orange-600 border border-orange-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                    <ion-icon name="{{ $isActive ? 'pause-circle-outline' : 'play-circle-outline' }}" style="font-size:18px;"></ion-icon>
                    {{ $isActive ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                </button>
            </form>

            {{-- Delete --}}
            <form action="{{ route('admin-kelola-akun.destroy', $u['id']) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    onclick="return confirm('Hapus akun ini secara permanen? Tindakan tidak dapat dibatalkan.')"
                    class="act-btn flex items-center justify-center gap-2 w-full py-4 rounded-2xl font-bold text-sm bg-red-50 text-red-500 border border-red-200">
                    <ion-icon name="trash-outline" style="font-size:18px;"></ion-icon>
                    Hapus Akun
                </button>
            </form>
        </div>

    </div>
</div>
</div>

<script>
(function() {
    function updateClock() {
        const now = new Date();
        const el  = document.getElementById('statusTime');
        if (el) el.textContent = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
    }
    updateClock();
    setInterval(updateClock, 30000);
})();
</script>
@include('partials.auth-guard')
</body>
</html>