{{-- resources/views/majikan/konsultan-list.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Konsultan</title>
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
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-up         { animation: slideUp 0.4s ease forwards; }
        .anim-up.delay-1 { animation-delay: 0.05s; opacity: 0; }
        .anim-up.delay-2 { animation-delay: 0.12s; opacity: 0; }
        .anim-up.delay-3 { animation-delay: 0.20s; opacity: 0; }

        /* GridCard — matches RN activeOpacity:0.7 */
        .konsultan-card { transition: transform 0.15s ease, opacity 0.15s ease; }
        .konsultan-card:hover  { opacity: 0.85; }
        .konsultan-card:active { transform: scale(0.95); opacity: 0.7; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .search-input:focus { outline: none; }
        .search-wrapper:focus-within {
            border-color: #7B1E5A !important;
            box-shadow: 0 0 0 3px rgba(123,30,90,0.12);
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

    <!-- HEADER -->
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0" style="margin-bottom:20px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <!-- Back button -->
        <a href="{{ route('dashboard') }}"
           class="absolute z-10 flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px; left:20px; width:40px; height:40px;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>

        <!-- Header content — alignItems:'center' -->
        <div class="flex flex-col items-center anim-up delay-1">
            <!-- headerIconContainer: w-64 h-64 rounded-full bg-white mb-16 -->
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg shadow-plum-dark/20"
                 style="width:64px;height:64px;">
                <ion-icon name="people" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <!-- headerTitle: fontSize:24, fontWeight:700, color:#fff, letterSpacing:0.5, mb:4 -->
            <h1 class="font-bold text-white mb-1" style="font-size:24px; letter-spacing:0.5px;">Daftar Konsultan</h1>
            <!-- headerSubtitle: fontSize:14, color:#F3E6FA, fontWeight:500 -->
            <p class="font-medium" style="font-size:14px; color:#F3E6FA;">
                {{ count($konsultans ?? []) }} konsultan tersedia
            </p>
        </div>
    </div>

    <!-- SCROLLABLE BODY — scrollView: flex:1, paddingHorizontal:20 -->
    <div class="flex-1 overflow-y-auto no-scrollbar" style="padding: 0 20px;">

        <!-- SEARCH BAR — searchContainer: flexDirection:row, mb:20, gap:10 -->
        <div class="flex anim-up delay-2" style="gap:10px; margin-bottom:20px;">
            <form action="{{ route('majikan-konsultan-list') }}" method="GET" class="flex w-full" style="gap:10px;">
                {{-- searchInputContainer: flex:1, flexDirection:row, alignItems:center, bg:#fff,
                     borderRadius:16, px:16, py:12, borderWidth:2, borderColor:#F3E6FA, gap:12 --}}
                <div class="search-wrapper flex-1 flex items-center bg-white"
                     style="border-radius:16px; padding:12px 16px; border:2px solid #F3E6FA; gap:12px; transition: border-color 0.2s, box-shadow 0.2s;">
                    <ion-icon name="search" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari konsultan..."
                        class="search-input flex-1 bg-transparent font-medium"
                        style="font-size:15px; color:#4A0E35;"
                        placeholder-style="color:#B895C8"
                    >
                    @if(request('search'))
                    <a href="{{ route('majikan-konsultan-list') }}" style="color:#B895C8; flex-shrink:0;">
                        <ion-icon name="close-circle" style="font-size:20px; display:block;"></ion-icon>
                    </a>
                    @endif
                </div>
                {{-- searchButton: bg:#7B1E5A, w:48, h:48, borderRadius:16 --}}
                <button type="submit"
                        class="flex items-center justify-center flex-shrink-0 hover:opacity-90 active:scale-95 transition-all shadow-md"
                        style="background:#7B1E5A; width:48px; height:48px; border-radius:16px; box-shadow: 0 4px 12px rgba(123,30,90,0.3);">
                    <ion-icon name="search" style="font-size:20px;color:white;"></ion-icon>
                </button>
            </form>
        </div>

        <!-- CONTENT -->
        <div class="anim-up delay-3">

            @if(isset($konsultans) && count($konsultans) > 0)

            {{--
                GridCard — gridContainer: flexDirection:row, flexWrap:wrap, justifyContent:space-between, pb:40
                card: width:31%, bg:#fff, borderRadius:16, py:16, px:8, alignItems:center, mb:16,
                      borderWidth:2, borderColor:#F3E6FA
                avatarContainer: mb:12
                avatar/placeholder: w:64, h:64, borderRadius:32, bg:#F3E6FA, border:3 solid #F3E6FA
                name: fontSize:14, fontWeight:700, color:#4A0E35, textAlign:center, mb:6, px:4
                roleContainer: flexDirection:row, alignItems:center, gap:4, px:4
                role: fontSize:12, color:#A2397B, fontWeight:500
            --}}
            <div class="flex flex-wrap justify-between" style="padding-bottom:40px;">
                @foreach($konsultans as $i => $konsultan)
                <a href="{{ route('majikan-konsultan-detail', $konsultan['id']) }}"
                   class="konsultan-card flex flex-col items-center bg-white"
                   style="width:31%;
                          border-radius:16px;
                          padding: 16px 8px;
                          margin-bottom:16px;
                          border: 2px solid #F3E6FA;
                          animation: slideUp 0.3s ease {{ $i * 0.05 }}s both;
                          opacity:0;"
                >
                    <!-- avatarContainer: mb:12 -->
                    <div style="margin-bottom:12px;">
                        @if(!empty($konsultan['foto']))
                        <img src="{{ $konsultan['foto'] }}"
                             alt="{{ $konsultan['name'] }}"
                             class="object-cover"
                             style="width:64px; height:64px; border-radius:32px; background:#F3E6FA; border:3px solid #F3E6FA;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <div class="items-center justify-center hidden"
                             style="width:64px; height:64px; border-radius:32px; background:#F3E6FA; border:3px solid #F3E6FA;">
                            <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                        </div>
                        @else
                        <div class="flex items-center justify-center"
                             style="width:64px; height:64px; border-radius:32px; background:#F3E6FA; border:3px solid #F3E6FA;">
                            <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                        </div>
                        @endif
                    </div>

                    <!-- name: fontSize:14, fontWeight:700, color:#4A0E35, textAlign:center, mb:6, px:4 -->
                    <p class="text-center line-clamp-1 w-full"
                       style="font-size:14px; font-weight:700; color:#4A0E35; margin-bottom:6px; padding:0 4px;">
                        {{ $konsultan['name'] }}
                    </p>

                    <!-- roleContainer + role -->
                    @if(!empty($konsultan['role']))
                    <div class="flex items-center" style="gap:4px; padding:0 4px;">
                        <ion-icon name="briefcase-outline" style="font-size:12px;color:#A2397B;flex-shrink:0;"></ion-icon>
                        <span class="text-center line-clamp-1"
                              style="font-size:12px; color:#A2397B; font-weight:500;">
                            {{ $konsultan['role'] }}
                        </span>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>

            @elseif(request('search'))
            <!-- emptyState: alignItems:center, pt:60, px:40 -->
            <div class="flex flex-col items-center" style="padding-top:60px; padding-bottom:40px; padding-left:40px; padding-right:40px;">
                <!-- emptyIconCircle: w:120, h:120, borderRadius:60, bg:#F3E6FA, mb:24 -->
                <div class="float-anim flex items-center justify-center"
                     style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;margin-bottom:24px;">
                    <ion-icon name="people-outline" style="font-size:60px;color:#B895C8;"></ion-icon>
                </div>
                <!-- emptyText: fontSize:18, fontWeight:700, color:#4A0E35, textAlign:center, mb:8 -->
                <h3 class="text-center" style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;">
                    Konsultan tidak ditemukan
                </h3>
                <!-- emptySubtext: fontSize:14, color:#A2397B, textAlign:center, lineHeight:20 -->
                <p class="text-center" style="font-size:14px;color:#A2397B;line-height:20px;">
                    Tidak ada konsultan yang sesuai dengan pencarian
                    "<span style="font-weight:600;color:#7B1E5A;">{{ request('search') }}</span>"
                </p>
                <a href="{{ route('majikan-konsultan-list') }}"
                   class="mt-6 text-white font-bold hover:opacity-90 transition-opacity"
                   style="background:#7B1E5A;font-size:14px;padding:12px 24px;border-radius:16px;box-shadow:0 4px 12px rgba(123,30,90,0.3);">
                    Lihat Semua Konsultan
                </a>
            </div>

            @else
            <!-- empty — belum ada data -->
            <div class="flex flex-col items-center" style="padding-top:60px; padding-bottom:40px; padding-left:40px; padding-right:40px;">
                <div class="float-anim flex items-center justify-center"
                     style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;margin-bottom:24px;">
                    <ion-icon name="people-outline" style="font-size:60px;color:#B895C8;"></ion-icon>
                </div>
                <h3 class="text-center" style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;">
                    Belum ada konsultan tersedia
                </h3>
                <p class="text-center" style="font-size:14px;color:#A2397B;line-height:20px;">
                    Daftar konsultan akan muncul di sini
                </p>
            </div>
            @endif

        </div>

        <!-- Bottom Spacing: height:30 -->
        <div style="height:30px;"></div>
    </div>

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