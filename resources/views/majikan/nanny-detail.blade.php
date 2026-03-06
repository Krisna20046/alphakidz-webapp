{{-- resources/views/majikan/nanny-detail.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Nanny — {{ $nanny['name'] ?? 'Nanny' }}</title>
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
                        plum: {
                            DEFAULT: '#7B1E5A',
                            light:   '#9B2E72',
                            dark:    '#4A0E35',
                            pale:    '#FFF9FB',
                            soft:    '#F3E6FA',
                            muted:   '#A2397B',
                            accent:  '#B895C8',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
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
                display: flex; align-items: flex-start; justify-content: center;
                min-height: 100vh; padding: 32px 0;
                background: linear-gradient(135deg, #f8e8f3 0%, #ede0f0 60%, #e8d5ee 100%);
            }
            .phone-frame {
                width: 390px; min-height: 844px;
                border-radius: 44px;
                box-shadow: 0 40px 80px rgba(123,30,90,0.25),
                            0 0 0 8px #1a0d14, 0 0 0 10px #2d1020;
                overflow: hidden;
                position: relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }

        .header-bg {
            background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%);
        }

        /* Slide-up animation */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up            { animation: slideUp 0.35s ease forwards; }
        .anim-up.d1         { animation-delay: 0.05s; opacity: 0; }
        .anim-up.d2         { animation-delay: 0.12s; opacity: 0; }
        .anim-up.d3         { animation-delay: 0.19s; opacity: 0; }
        .anim-up.d4         { animation-delay: 0.26s; opacity: 0; }
        .anim-up.d5         { animation-delay: 0.33s; opacity: 0; }
        .anim-up.d6         { animation-delay: 0.40s; opacity: 0; }

        /* Scrollbar hide */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Info card hover */
        .info-card {
            transition: box-shadow 0.2s ease;
        }
        .info-card:hover {
            box-shadow: 0 4px 16px rgba(123,30,90,0.10);
        }

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, #7B1E5A, #9B2E72);
            transition: opacity 0.2s, transform 0.15s;
        }
        .btn-primary:hover:not(:disabled)  { opacity: 0.92; }
        .btn-primary:active:not(:disabled) { transform: scale(0.97); }
        .btn-primary:disabled {
            background: #E2C8D8;
            cursor: not-allowed;
        }

        /* Float empty */
        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        /* Avatar ring animation */
        @keyframes avatarRing {
            0%,100% { box-shadow: 0 0 0 0 rgba(123,30,90,0.3); }
            50%     { box-shadow: 0 0 0 8px rgba(123,30,90,0); }
        }
        .avatar-ring { animation: avatarRing 2.5s ease-in-out 0.5s infinite; }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- ─── STATUS BAR ────────────────────────────────────────────────────── -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    @if(!isset($nanny))
    {{-- ── NOT FOUND STATE ───────────────────────────────────────────────── --}}
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <a href="{{ route('majikan-nanny-list') }}"
           class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-lg shadow-plum-dark/20">
                <ion-icon name="person" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Detail Nanny</h1>
            <p class="text-white/60 text-xs font-medium">Informasi lengkap nanny</p>
        </div>
    </div>
    <div class="flex-1 flex flex-col items-center justify-center px-10">
        <div class="float-anim w-28 h-28 rounded-full bg-plum-soft flex items-center justify-center mb-6">
            <ion-icon name="person-circle-outline" style="font-size:64px;color:#B895C8;"></ion-icon>
        </div>
        <h2 class="text-plum-dark font-bold text-xl mb-2">Data tidak ditemukan</h2>
        <p class="text-plum-muted text-sm text-center leading-relaxed mb-6">Data yang Anda cari tidak tersedia</p>
        <a href="{{ route('majikan-nanny-list') }}"
           class="btn-primary text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-md shadow-plum/30">
            Kembali ke Daftar
        </a>
    </div>

    @else
    {{-- ── HEADER ────────────────────────────────────────────────────────── --}}
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0 overflow-hidden">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <a href="{{ route('majikan-nanny-list') }}"
           class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>

        <div class="flex flex-col items-center anim-up d1">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-lg shadow-plum-dark/20">
                <ion-icon name="person" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Detail Nanny</h1>
            <p class="text-white/60 text-xs font-medium">Informasi lengkap nanny</p>
        </div>
    </div>

    {{-- ── SCROLLABLE BODY ──────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-5 space-y-4">

        {{-- ── PROFILE CARD ────────────────────────────────────────────── --}}
        <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-6 flex flex-col items-center anim-up d2">
            @if(!empty($nanny['foto']))
            <img src="{{ $nanny['foto'] }}"
                 alt="{{ $nanny['name'] }}"
                 class="avatar-ring w-28 h-28 rounded-full object-cover border-4 border-plum-soft mb-4"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <div class="avatar-ring w-28 h-28 rounded-full bg-plum-soft border-4 border-plum-soft mb-4 items-center justify-center hidden">
                <ion-icon name="person" style="font-size:48px;color:#7B1E5A;"></ion-icon>
            </div>
            @else
            <div class="avatar-ring w-28 h-28 rounded-full bg-plum-soft border-4 border-plum-soft mb-4 flex items-center justify-center">
                <ion-icon name="person" style="font-size:48px;color:#7B1E5A;"></ion-icon>
            </div>
            @endif

            <h2 class="text-plum-dark text-xl font-extrabold mb-2 text-center">{{ $nanny['name'] }}</h2>

            <div class="flex items-center gap-1.5 bg-plum-soft px-4 py-2 rounded-full">
                <ion-icon name="briefcase" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                <span class="text-plum text-xs font-bold">{{ $nanny['posisi'] ?? 'Nanny' }}</span>
            </div>
        </div>

        {{-- ── BIO ──────────────────────────────────────────────────────── --}}
        @if(!empty($nanny['bio']))
        <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-5 anim-up d3">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                    <ion-icon name="information-circle" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-sm">Bio</h3>
            </div>
            <p class="text-plum-dark/80 text-sm leading-relaxed">{{ $nanny['bio'] }}</p>
        </div>
        @endif

        {{-- ── CONTACT ──────────────────────────────────────────────────── --}}
        <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-5 anim-up d3">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                    <ion-icon name="call" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-sm">Informasi Kontak</h3>
            </div>

            <div class="space-y-3">
                <!-- Email -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="mail" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Email</p>
                        <p class="text-plum-dark text-sm font-semibold truncate">{{ $nanny['email'] ?? '-' }}</p>
                    </div>
                </div>
                <!-- No HP -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="call" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Nomor HP</p>
                        <p class="text-plum-dark text-sm font-semibold">{{ $nanny['no_hp'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── PERSONAL INFO ────────────────────────────────────────────── --}}
        <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-5 anim-up d4">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                    <ion-icon name="person-circle" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-sm">Informasi Pribadi</h3>
            </div>

            <div class="space-y-3">
                <!-- Tanggal Lahir -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="calendar" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Tanggal Lahir</p>
                        <p class="text-plum-dark text-sm font-semibold">{{ $nanny['tanggal_lahir'] ?? '-' }}</p>
                    </div>
                </div>
                <!-- Gender -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                        @php $isMale = ($nanny['gender'] ?? '') === 'L'; @endphp
                        <ion-icon name="{{ $isMale ? 'male' : 'female' }}" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Gender</p>
                        <p class="text-plum-dark text-sm font-semibold">
                            @php
                                $g = $nanny['gender'] ?? '';
                                echo $g === 'L' ? 'Laki-laki' : ($g === 'P' ? 'Perempuan' : '-');
                            @endphp
                        </p>
                    </div>
                </div>
                <!-- Lokasi -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="location" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Lokasi</p>
                        <p class="text-plum-dark text-sm font-semibold">
                            @if(!empty($nanny['kota']) && !empty($nanny['provinsi']))
                                {{ $nanny['kota'] }}, {{ $nanny['provinsi'] }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
                <!-- Alamat -->
                @if(!empty($nanny['alamat']))
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                        <ion-icon name="home" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Alamat</p>
                        <p class="text-plum-dark text-sm font-semibold leading-snug">{{ $nanny['alamat'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── PROFESSIONAL INFO ────────────────────────────────────────── --}}
        @if(!empty($nanny['skill']) || !empty($nanny['pengalaman']) || !empty($nanny['sertifikasi']))
        <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-5 anim-up d5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                    <ion-icon name="briefcase" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-sm">Informasi Profesional</h3>
            </div>

            <div class="space-y-3">
                @if(!empty($nanny['skill']))
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                        <ion-icon name="star" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Skill</p>
                        <p class="text-plum-dark text-sm font-semibold leading-snug">{{ $nanny['skill'] }}</p>
                    </div>
                </div>
                @endif
                @if(!empty($nanny['pengalaman']))
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                        <ion-icon name="time" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Pengalaman</p>
                        <p class="text-plum-dark text-sm font-semibold leading-snug">{{ $nanny['pengalaman'] }}</p>
                    </div>
                </div>
                @endif
                @if(!empty($nanny['sertifikasi']))
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                        <ion-icon name="ribbon" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Sertifikasi</p>
                        <p class="text-plum-dark text-sm font-semibold leading-snug">{{ $nanny['sertifikasi'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── PENGAWASAN (KONSULTAN) CARD ──────────────────────────────── --}}
        <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-5 anim-up d5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                    <ion-icon name="people" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-sm">Pengawasan</h3>
            </div>

            <div class="flex items-center gap-3 bg-plum-soft rounded-2xl p-4">
                @if(!empty($nanny['konsultan']))
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <ion-icon name="checkmark-circle" style="font-size:22px;color:#16A34A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Konsultan</p>
                    <p class="text-plum-dark text-sm font-extrabold">{{ $nanny['konsultan']['name'] }}</p>
                </div>
                @else
                <div class="w-10 h-10 rounded-full bg-plum-soft border-2 border-plum-accent flex items-center justify-center flex-shrink-0">
                    <ion-icon name="alert-circle" style="font-size:22px;color:#B895C8;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Konsultan</p>
                    <p class="text-plum-dark text-sm font-semibold">Belum ada konsultan</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ── BUTTON ───────────────────────────────────────────────────── --}}
        <div class="anim-up d6 pb-2">
            @if(!empty($nanny['konsultan']))
            <a href="{{ route('chat.room', $nanny['konsultan']['id']) }}"
               class="btn-primary w-full flex items-center justify-center gap-2 text-white font-bold text-sm py-4 rounded-2xl shadow-lg shadow-plum/30">
                <ion-icon name="chatbubble-ellipses" style="font-size:18px;"></ion-icon>
                Hubungi Konsultan
            </a>
            @else
            <button disabled
                    class="btn-primary w-full flex items-center justify-center gap-2 text-white/70 font-bold text-sm py-4 rounded-2xl">
                <ion-icon name="chatbubble-ellipses" style="font-size:18px;"></ion-icon>
                Hubungi Konsultan
            </button>
            @endif
        </div>

        <div class="h-6"></div>
    </div>

    @endif

    <!-- ─── BOTTOM NAV ───────────────────────────────────────────────────── -->
    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const el = document.getElementById('statusTime');
        if (el) el.textContent = `${h}:${m}`;
    }
    updateClock();
    setInterval(updateClock, 30000);
</script>
@include('partials.auth-guard')
</body>
</html>