{{-- resources/views/majikan/konsultan-detail.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Konsultan — {{ $konsultan['name'] ?? 'Konsultan' }}</title>
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
                width: 390px; min-height: 844px; border-radius: 44px;
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
        .anim-up     { animation: slideUp 0.35s ease forwards; }
        .anim-up.d1  { animation-delay: 0.05s; opacity: 0; }
        .anim-up.d2  { animation-delay: 0.12s; opacity: 0; }
        .anim-up.d3  { animation-delay: 0.19s; opacity: 0; }
        .anim-up.d4  { animation-delay: 0.26s; opacity: 0; }
        .anim-up.d5  { animation-delay: 0.33s; opacity: 0; }
        .anim-up.d6  { animation-delay: 0.40s; opacity: 0; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* infoCard hover */
        .info-card { transition: box-shadow 0.2s ease; }
        .info-card:hover { box-shadow: 0 4px 16px rgba(123,30,90,0.10); }

        /* Button — gradient matches RN Button component */
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

    @if(!isset($konsultan))
    {{-- ── NOT FOUND ─────────────────────────────────────────────────────── --}}
    {{-- header --}}
    <div class="header-bg rounded-b-[24px] relative shrink-0"
         style="padding: 50px 20px 24px;">
        <a href="{{ route('majikan-konsultan-list') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px;left:20px;width:40px;height:40px;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center">
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg"
                 style="width:64px;height:64px;">
                <ion-icon name="person" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Detail Konsultan</h1>
            <p style="font-size:14px;color:#F3E6FA;font-weight:500;">Informasi lengkap konsultan</p>
        </div>
    </div>
    {{-- emptyContainer: flex:1, justifyContent:center, alignItems:center, padding:40 --}}
    <div class="flex-1 flex flex-col items-center justify-center" style="padding:40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;margin-bottom:24px;">
            <ion-icon name="person-circle-outline" style="font-size:80px;color:#B895C8;"></ion-icon>
        </div>
        <p class="text-center" style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;">
            Data konsultan tidak ditemukan
        </p>
        <p class="text-center" style="font-size:14px;color:#A2397B;line-height:20px;margin-bottom:24px;">
            Data yang Anda cari tidak tersedia
        </p>
        <a href="{{ route('majikan-konsultan-list') }}"
           class="btn-primary text-white font-bold"
           style="font-size:14px;padding:12px 28px;border-radius:16px;">
            Kembali ke Daftar
        </a>
    </div>

    @else
    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    {{--
        header: bg:#7B1E5A, pt:50, pb:24, px:20,
                borderBottomLeftRadius:24, borderBottomRightRadius:24
    --}}
    <div class="header-bg relative shrink-0"
         style="padding:50px 20px 24px; border-bottom-left-radius:24px; border-bottom-right-radius:24px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        {{-- backButton: absolute, top:54, left:20, w:40, h:40, borderRadius:20, bg:rgba(255,255,255,0.2) --}}
        <a href="{{ route('majikan-konsultan-list') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full anim-up d1"
           style="top:54px;left:20px;width:40px;height:40px;z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>

        {{-- headerContent: alignItems:center --}}
        <div class="flex flex-col items-center anim-up d1">
            {{-- headerIconContainer: w:64, h:64, borderRadius:32, bg:#fff, mb:16 --}}
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg shadow-plum-dark/20"
                 style="width:64px;height:64px;">
                <ion-icon name="person" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            {{-- headerTitle: fontSize:24, fontWeight:700, color:#fff, letterSpacing:0.5, mb:4 --}}
            <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Detail Konsultan</h1>
            {{-- headerSubtitle: fontSize:14, color:#F3E6FA, fontWeight:500 --}}
            <p style="font-size:14px;color:#F3E6FA;font-weight:500;">Informasi lengkap konsultan</p>
        </div>
    </div>

    {{-- ── SCROLLABLE — scrollContent: padding:20 ─────────────────────────── --}}
    <div class="flex-1 overflow-y-auto no-scrollbar" style="padding:20px;">

        {{-- ── PROFILE CARD ──────────────────────────────────────────────────
             profileCard: bg:#fff, borderRadius:20, padding:24, mb:16, border:2 solid #F3E6FA
             profileHeader: alignItems:center
        --}}
        <div class="info-card flex flex-col items-center anim-up d2"
             style="background:#fff; border-radius:20px; padding:24px; margin-bottom:16px; border:2px solid #F3E6FA;">

            {{-- foto: w:120, h:120, borderRadius:60, borderWidth:4, borderColor:#F3E6FA, mb:16 --}}
            @if(!empty($konsultan['foto_url']))
            <img src="{{ $konsultan['foto_url'] }}"
                 alt="{{ $konsultan['name'] }}"
                 class="object-cover"
                 style="width:120px;height:120px;border-radius:60px;border:4px solid #F3E6FA;margin-bottom:16px;"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <div class="items-center justify-center hidden"
                 style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;border:4px solid #F3E6FA;margin-bottom:16px;">
                <ion-icon name="person" style="font-size:50px;color:#7B1E5A;"></ion-icon>
            </div>
            @else
            {{-- fotoPlaceholder: w:120, h:120, borderRadius:60, bg:#F3E6FA, border:4, mb:16 --}}
            <div class="flex items-center justify-center"
                 style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;border:4px solid #F3E6FA;margin-bottom:16px;">
                <ion-icon name="person" style="font-size:50px;color:#7B1E5A;"></ion-icon>
            </div>
            @endif

            {{-- nama: fontSize:22, fontWeight:700, color:#4A0E35, mb:8 --}}
            <p class="text-center" style="font-size:22px;font-weight:700;color:#4A0E35;margin-bottom:8px;">
                {{ $konsultan['name'] }}
            </p>

            {{-- spesialisBadge (jika ada) —
                 flexDirection:row, alignItems:center, bg:#F3E6FA, px:16, py:8, borderRadius:16, gap:6 --}}
            @if(!empty($konsultan['spesialis']))
            <div class="flex items-center" style="background:#F3E6FA;padding:8px 16px;border-radius:16px;gap:6px;">
                <ion-icon name="school" style="font-size:14px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span style="font-size:13px;color:#7B1E5A;font-weight:600;">{{ $konsultan['spesialis'] }}</span>
            </div>
            @endif
        </div>

        {{-- ── BIO CARD (kondisional) ─────────────────────────────────────────
             bioCard: bg:#fff, borderRadius:20, padding:20, mb:16, border:2 solid #F3E6FA
        --}}
        @if(!empty($konsultan['bio']))
        <div class="info-card anim-up d3"
             style="background:#fff;border-radius:20px;padding:20px;margin-bottom:16px;border:2px solid #F3E6FA;">
            {{-- sectionHeader: flexDirection:row, alignItems:center, mb:16, gap:8 --}}
            <div class="flex items-center" style="margin-bottom:16px;gap:8px;">
                <ion-icon name="information-circle" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                <span style="font-size:16px;font-weight:700;color:#4A0E35;">Bio</span>
            </div>
            {{-- bioText: fontSize:15, color:#4A0E35, lineHeight:22 --}}
            <p style="font-size:15px;color:#4A0E35;line-height:22px;">{{ $konsultan['bio'] }}</p>
        </div>
        @endif

        {{-- ── INFORMASI KONTAK ──────────────────────────────────────────────
             infoCard: bg:#fff, borderRadius:20, padding:20, mb:16, border:2 solid #F3E6FA
        --}}
        <div class="info-card anim-up d3"
             style="background:#fff;border-radius:20px;padding:20px;margin-bottom:16px;border:2px solid #F3E6FA;">
            <div class="flex items-center" style="margin-bottom:16px;gap:8px;">
                <ion-icon name="call" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                <span style="font-size:16px;font-weight:700;color:#4A0E35;">Informasi Kontak</span>
            </div>

            {{-- infoRow: flexDirection:row, alignItems:flex-start, mb:16 --}}
            <div class="flex items-start" style="margin-bottom:16px;">
                {{-- infoIconContainer: w:40, h:40, borderRadius:12, bg:#F3E6FA, mr:12 --}}
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    <ion-icon name="mail" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    {{-- infoLabel: fontSize:12, color:#A2397B, fontWeight:600, mb:4 --}}
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Email</p>
                    {{-- infoValue: fontSize:15, color:#4A0E35, fontWeight:500, lineHeight:20 --}}
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">
                        {{ $konsultan['email'] ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    <ion-icon name="call" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Nomor HP</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">
                        {{ $konsultan['no_hp'] ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── INFORMASI PRIBADI ─────────────────────────────────────────── --}}
        <div class="info-card anim-up d4"
             style="background:#fff;border-radius:20px;padding:20px;margin-bottom:16px;border:2px solid #F3E6FA;">
            <div class="flex items-center" style="margin-bottom:16px;gap:8px;">
                <ion-icon name="person-circle" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                <span style="font-size:16px;font-weight:700;color:#4A0E35;">Informasi Pribadi</span>
            </div>

            {{-- Tanggal Lahir --}}
            <div class="flex items-start" style="margin-bottom:16px;">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    <ion-icon name="calendar" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Tanggal Lahir</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">
                        {{ $konsultan['tanggal_lahir'] ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- Gender — icon male/female sesuai data --}}
            <div class="flex items-start" style="margin-bottom:16px;">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    @php $isMale = ($konsultan['gender'] ?? '') === 'L'; @endphp
                    <ion-icon name="{{ $isMale ? 'male' : 'female' }}" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Gender</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">
                        @php
                            $g = $konsultan['gender'] ?? '';
                            echo $g === 'L' ? 'Laki-laki' : ($g === 'P' ? 'Perempuan' : '-');
                        @endphp
                    </p>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="flex items-start" style="margin-bottom:{{ !empty($konsultan['alamat']) ? '16px' : '0' }};">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    <ion-icon name="location" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Lokasi</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">
                        @if(!empty($konsultan['kota']) && !empty($konsultan['provinsi']))
                            {{ $konsultan['kota'] }}, {{ $konsultan['provinsi'] }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>

            {{-- Alamat (kondisional) --}}
            @if(!empty($konsultan['alamat']))
            <div class="flex items-start">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;margin-top:2px;">
                    <ion-icon name="home" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Alamat</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">
                        {{ $konsultan['alamat'] }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- ── INFORMASI PROFESIONAL (kondisional) ──────────────────────────
             Tampil jika ada skill, pengalaman, atau sertifikasi
        --}}
        @if(!empty($konsultan['skill']) || !empty($konsultan['pengalaman']) || !empty($konsultan['sertifikasi']))
        <div class="info-card anim-up d5"
             style="background:#fff;border-radius:20px;padding:20px;margin-bottom:16px;border:2px solid #F3E6FA;">
            <div class="flex items-center" style="margin-bottom:16px;gap:8px;">
                <ion-icon name="briefcase" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                <span style="font-size:16px;font-weight:700;color:#4A0E35;">Informasi Profesional</span>
            </div>

            @if(!empty($konsultan['skill']))
            <div class="flex items-start" style="margin-bottom:16px;">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    <ion-icon name="star" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Skill</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">{{ $konsultan['skill'] }}</p>
                </div>
            </div>
            @endif

            @if(!empty($konsultan['pengalaman']))
            <div class="flex items-start" style="margin-bottom:16px;">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    <ion-icon name="time" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Pengalaman</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">{{ $konsultan['pengalaman'] }}</p>
                </div>
            </div>
            @endif

            @if(!empty($konsultan['sertifikasi']))
            <div class="flex items-start">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:40px;height:40px;border-radius:12px;background:#F3E6FA;margin-right:12px;">
                    <ion-icon name="ribbon" style="font-size:18px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1">
                    <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:4px;">Sertifikasi</p>
                    <p style="font-size:15px;color:#4A0E35;font-weight:500;line-height:20px;">{{ $konsultan['sertifikasi'] }}</p>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ── BUTTON — buttonContainer: mt:8 ──────────────────────────────
             Button component: full width, gradient plum, text white, rounded, py:16
        --}}
        <div class="anim-up d6" style="margin-top:8px;">
            <a href="{{ route('chat.room', $konsultan['id_user'] ?? $konsultan['id']) }}"
               class="btn-primary w-full flex items-center justify-center gap-2 text-white font-bold"
               style="font-size:15px; padding:16px; border-radius:16px; box-shadow:0 8px 20px rgba(123,30,90,0.3);">
                <ion-icon name="chatbubble-ellipses" style="font-size:18px;"></ion-icon>
                Hubungi Konsultan
            </a>
        </div>

        {{-- Bottom Spacing: height:30 --}}
        <div style="height:30px;"></div>
    </div>

    @endif

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        const el = document.getElementById('statusTime');
        if (el) el.textContent = `${h}:${m}`;
    }
    updateClock();
    setInterval(updateClock, 30000);
</script>
@include('partials.auth-guard')
</body>
</html>