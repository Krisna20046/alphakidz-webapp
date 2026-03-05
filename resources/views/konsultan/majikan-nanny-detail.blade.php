{{-- resources/views/konsultan/majikan-nanny-detail.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Penugasan</title>
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
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }

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
                overflow: hidden; position: relative;
            }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height: 100vh; }
            .phone-frame   { min-height: 100vh; }
        }

        .header-bg { background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%); }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up    { animation: slideUp 0.35s ease forwards; }
        .anim-up.d1 { animation-delay: 0.05s; opacity: 0; }
        .anim-up.d2 { animation-delay: 0.12s; opacity: 0; }
        .anim-up.d3 { animation-delay: 0.19s; opacity: 0; }
        .anim-up.d4 { animation-delay: 0.26s; opacity: 0; }
        .anim-up.d5 { animation-delay: 0.33s; opacity: 0; }
        .anim-up.d6 { animation-delay: 0.40s; opacity: 0; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .info-card { transition: box-shadow 0.2s ease; }
        .info-card:hover { box-shadow: 0 4px 16px rgba(123,30,90,0.10); }

        .btn-primary {
            background: linear-gradient(135deg, #7B1E5A, #9B2E72);
            transition: opacity 0.2s, transform 0.15s;
        }
        .btn-primary:hover  { opacity: 0.92; }
        .btn-primary:active { transform: scale(0.97); }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        /* Connecting line between majikan & nanny cards */
        .connector {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 4px 0;
        }
        .connector-line {
            width: 2px;
            height: 20px;
            background: linear-gradient(to bottom, #B895C8, #7B1E5A);
            border-radius: 2px;
        }
        .connector-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #7B1E5A;
            border: 2px solid #F3E6FA;
        }
    </style>
</head>
<body>

<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div>
        </div>
    </div>

    @if(!isset($assignment))
    <!-- NOT FOUND -->
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <a href="{{ route('konsultan-majikan-nanny') }}"
           class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4">
                <ion-icon name="document-text" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Detail Penugasan</h1>
            <p class="text-white/60 text-xs font-medium">Informasi lengkap penugasan</p>
        </div>
    </div>
    <div class="flex-1 flex flex-col items-center justify-center px-10">
        <div class="float-anim w-28 h-28 rounded-full bg-plum-soft flex items-center justify-center mb-6">
            <ion-icon name="document-outline" style="font-size:64px;color:#B895C8;"></ion-icon>
        </div>
        <h2 class="text-plum-dark font-bold text-xl mb-2">Data tidak ditemukan</h2>
        <p class="text-plum-muted text-sm text-center leading-relaxed mb-6">Data penugasan yang Anda cari tidak tersedia</p>
        <a href="{{ route('konsultan-majikan-nanny') }}"
           class="btn-primary text-white text-sm font-bold px-8 py-3 rounded-2xl shadow-md shadow-plum/30">
            Kembali ke Daftar
        </a>
    </div>

    @else
    @php
        $a        = $assignment;
        $isActive = strtolower($a['status'] ?? '') === 'active' || strtolower($a['status'] ?? '') === 'aktif';

        /* Helper: format date */
        function fmtDate(?string $d): string {
            if (!$d) return '-';
            try {
                $dt = new \DateTime($d);
                return $dt->format('d M Y');
            } catch (\Throwable $e) {
                return $d;
            }
        }

        /* Helper: age */
        function calcAge(?string $d): string {
            if (!$d) return '-';
            try {
                $birth = new \DateTime($d);
                $today = new \DateTime();
                return $today->diff($birth)->y . ' tahun';
            } catch (\Throwable $e) {
                return '-';
            }
        }
    @endphp

    <!-- HEADER -->
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <a href="{{ route('konsultan-majikan-nanny') }}"
           class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>

        <div class="flex flex-col items-center anim-up d1">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-lg shadow-plum-dark/20">
                <ion-icon name="document-text" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Detail Penugasan</h1>
            <p class="text-white/60 text-xs font-medium">Informasi lengkap penugasan</p>
        </div>
    </div>

    <!-- SCROLLABLE BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-5 space-y-4">

        <!-- ── INFORMASI PENUGASAN ──────────────────────────────────────── -->
        <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-5 anim-up d2">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-xl bg-plum-soft flex items-center justify-center">
                    <ion-icon name="clipboard-outline" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <h3 class="text-plum-dark font-bold text-sm">Informasi Penugasan</h3>
            </div>

            <div class="space-y-3">
                <!-- Periode -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="calendar" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Periode Penugasan</p>
                        <p class="text-plum-dark text-sm font-semibold">
                            {{ fmtDate($a['tanggal_mulai'] ?? null) }}
                            —
                            {{ fmtDate($a['tanggal_selesai'] ?? null) }}
                        </p>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="time" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Status Penugasan</p>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold
                              {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-green-600' : 'bg-red-600' }}"></span>
                            {{ $isActive ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>

                @if(!empty($a['catatan']))
                <!-- Catatan -->
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                        <ion-icon name="document-text" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide mb-0.5">Catatan</p>
                        <p class="text-plum-dark text-sm font-semibold leading-snug">{{ $a['catatan'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ── RELASI VISUAL: MAJIKAN ↔ NANNY ─────────────────────────── -->
        <div class="anim-up d3">

            <!-- Label -->
            <p class="text-plum-muted text-[10px] font-extrabold uppercase tracking-widest mb-3 text-center">
                Hubungan Penugasan
            </p>

            <!-- Majikan Card -->
            <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-xl bg-plum-soft flex items-center justify-center">
                        <ion-icon name="briefcase-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <h3 class="text-plum-dark font-bold text-xs">Informasi Majikan</h3>
                </div>

                <div class="flex items-center gap-3 mb-4">
                    @if(!empty($a['majikan_foto']))
                    <img src="{{ $a['majikan_foto'] }}"
                         alt="{{ $a['majikan_name'] }}"
                         class="w-16 h-16 rounded-full object-cover border-4 border-plum-soft flex-shrink-0"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                    >
                    <div class="w-16 h-16 rounded-full bg-plum-soft border-4 border-plum-soft hidden items-center justify-center flex-shrink-0">
                        <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                    </div>
                    @else
                    <div class="w-16 h-16 rounded-full bg-plum-soft border-4 border-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                    </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-plum-dark font-extrabold text-base truncate mb-1">{{ $a['majikan_name'] ?? '-' }}</p>
                        @php $majMale = ($a['majikan_gender'] ?? '') === 'L'; @endphp
                        <div class="inline-flex items-center gap-1 bg-plum-soft px-2.5 py-1 rounded-full">
                            <ion-icon name="{{ $majMale ? 'male' : 'female' }}" style="font-size:11px;color:#7B1E5A;"></ion-icon>
                            <span class="text-plum text-[11px] font-bold">{{ $majMale ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <!-- Email -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0">
                            <ion-icon name="mail" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Email</p>
                            <a href="mailto:{{ $a['majikan_email'] ?? '' }}"
                               class="text-plum text-xs font-semibold underline truncate block">
                                {{ $a['majikan_email'] ?? '-' }}
                            </a>
                        </div>
                    </div>
                    <!-- Lokasi -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0">
                            <ion-icon name="location" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Lokasi</p>
                            <p class="text-plum-dark text-xs font-semibold">
                                @if(!empty($a['majikan_kota']) && !empty($a['majikan_provinsi']))
                                    {{ $a['majikan_kota'] }}, {{ $a['majikan_provinsi'] }}
                                @else - @endif
                            </p>
                        </div>
                    </div>
                    <!-- Alamat -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                            <ion-icon name="home" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Alamat</p>
                            <p class="text-plum-dark text-xs font-semibold leading-snug">{{ $a['majikan_alamat'] ?? '-' }}</p>
                        </div>
                    </div>
                    <!-- Tanggal Lahir -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0">
                            <ion-icon name="calendar" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Tanggal Lahir</p>
                            <p class="text-plum-dark text-xs font-semibold">
                                {{ fmtDate($a['majikan_tanggal_lahir'] ?? null) }}
                                @if(!empty($a['majikan_tanggal_lahir']))
                                    <span class="text-plum-muted font-normal">({{ calcAge($a['majikan_tanggal_lahir']) }})</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Connector -->
            <div class="connector my-1">
                <div class="connector-line"></div>
                <div class="connector-dot"></div>
                <div class="connector-line"></div>
            </div>

            <!-- Nanny Card -->
            <div class="info-card bg-white rounded-3xl border-2 border-plum-soft p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-xl bg-plum-soft flex items-center justify-center">
                        <ion-icon name="heart-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <h3 class="text-plum-dark font-bold text-xs">Informasi Nanny</h3>
                </div>

                <div class="flex items-center gap-3 mb-4">
                    @if(!empty($a['nanny_foto']))
                    <img src="{{ $a['nanny_foto'] }}"
                         alt="{{ $a['nanny_name'] }}"
                         class="w-16 h-16 rounded-full object-cover border-4 border-plum-soft flex-shrink-0"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                    >
                    <div class="w-16 h-16 rounded-full bg-plum-soft border-4 border-plum-soft hidden items-center justify-center flex-shrink-0">
                        <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                    </div>
                    @else
                    <div class="w-16 h-16 rounded-full bg-plum-soft border-4 border-plum-soft flex items-center justify-center flex-shrink-0">
                        <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                    </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-plum-dark font-extrabold text-base truncate mb-1">{{ $a['nanny_name'] ?? '-' }}</p>
                        @php $nannyMale = ($a['nanny_gender'] ?? '') === 'L'; @endphp
                        <div class="inline-flex items-center gap-1 bg-plum-soft px-2.5 py-1 rounded-full">
                            <ion-icon name="{{ $nannyMale ? 'male' : 'female' }}" style="font-size:11px;color:#7B1E5A;"></ion-icon>
                            <span class="text-plum text-[11px] font-bold">{{ $nannyMale ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <!-- Email -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0">
                            <ion-icon name="mail" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Email</p>
                            <a href="mailto:{{ $a['nanny_email'] ?? '' }}"
                               class="text-plum text-xs font-semibold underline truncate block">
                                {{ $a['nanny_email'] ?? '-' }}
                            </a>
                        </div>
                    </div>
                    <!-- Lokasi -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0">
                            <ion-icon name="location" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Lokasi</p>
                            <p class="text-plum-dark text-xs font-semibold">
                                @if(!empty($a['nanny_kota']) && !empty($a['nanny_provinsi']))
                                    {{ $a['nanny_kota'] }}, {{ $a['nanny_provinsi'] }}
                                @else - @endif
                            </p>
                        </div>
                    </div>
                    <!-- Tanggal Lahir -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0">
                            <ion-icon name="calendar" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Tanggal Lahir</p>
                            <p class="text-plum-dark text-xs font-semibold">
                                {{ fmtDate($a['nanny_tanggal_lahir'] ?? null) }}
                                @if(!empty($a['nanny_tanggal_lahir']))
                                    <span class="text-plum-muted font-normal">({{ calcAge($a['nanny_tanggal_lahir']) }})</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── ACTION BUTTON ───────────────────────────────────────────── -->
        <div class="anim-up d6 pb-2">
            <a href="{{ route('chat.room', $a['id_majikan'] ?? 0) }}"
               class="btn-primary w-full flex items-center justify-center gap-2 text-white font-bold text-sm py-4 rounded-2xl shadow-lg shadow-plum/30">
                <ion-icon name="chatbubble-ellipses" style="font-size:18px;"></ion-icon>
                Chat dengan Majikan
            </a>
        </div>

        <div class="h-6"></div>
    </div>
    @endif

    <!-- BOTTOM NAV -->
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