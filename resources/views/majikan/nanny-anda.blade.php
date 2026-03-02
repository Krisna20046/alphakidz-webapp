{{-- resources/views/majikan/nanny-anda.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Nanny Anda</title>
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
        .anim-up.d2  { animation-delay: 0.10s; opacity: 0; }
        .anim-up.d3  { animation-delay: 0.15s; opacity: 0; }

        /* card: activeOpacity:0.8 */
        .nanny-card { transition: opacity 0.15s ease, transform 0.15s ease; }
        .nanny-card:hover  { opacity: 0.88; }
        .nanny-card:active { transform: scale(0.98); opacity: 0.8; }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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

    <!-- HEADER
         header: bg:#7B1E5A, pt:50, pb:24, px:20, borderBottomLeftRadius:24, borderBottomRightRadius:24 -->
    <div class="header-bg relative shrink-0"
         style="padding:50px 20px 24px; border-bottom-left-radius:24px; border-bottom-right-radius:24px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <!-- backButton: absolute, top:54, left:20, w:40, h:40, borderRadius:20, bg:rgba(255,255,255,0.2) -->
        <a href="{{ route('dashboard') }}"
           class="absolute flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors"
           style="top:54px;left:20px;width:40px;height:40px;z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>

        <!-- headerContent: alignItems:center -->
        <div class="flex flex-col items-center anim-up d1">
            <!-- headerIconContainer: w:64, h:64, borderRadius:32, bg:#fff, mb:16 -->
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg shadow-plum-dark/20"
                 style="width:64px;height:64px;">
                <ion-icon name="people" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <!-- headerTitle: fontSize:24, fontWeight:700, color:#fff, letterSpacing:0.5, mb:4 -->
            <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Nanny Anda</h1>
            <!-- headerSubtitle: fontSize:14, color:#F3E6FA, fontWeight:500 -->
            <p style="font-size:14px;color:#F3E6FA;font-weight:500;">Daftar nanny yang sedang bertugas</p>
        </div>
    </div>

    @if(isset($assignments) && count($assignments) > 0)

    <!-- LIST HEADER
         listHeader: flexDirection:row, alignItems:center, justifyContent:space-between,
                     px:20, pt:24, pb:12 -->
    <div class="flex items-center justify-between anim-up d2"
         style="padding:24px 20px 12px;">
        <!-- listTitle: fontSize:18, fontWeight:700, color:#4A0E35 -->
        <span style="font-size:18px;font-weight:700;color:#4A0E35;">Daftar Nanny Aktif</span>
        <!-- countBadge: bg:#F3E6FA, px:12, py:4, borderRadius:12 -->
        <span class="flex items-center justify-center"
              style="background:#F3E6FA;padding:4px 12px;border-radius:12px;">
            <span style="font-size:12px;font-weight:700;color:#7B1E5A;">{{ count($assignments) }}</span>
        </span>
    </div>

    <!-- SCROLL — listContent: padding:20, paddingTop:8 -->
    <div class="flex-1 overflow-y-auto no-scrollbar" style="padding:8px 20px 0;">

        @foreach($assignments as $i => $item)
        <!-- CARD
             card: bg:#fff, borderRadius:20, mb:16, border:2 solid #F3E6FA, overflow:hidden -->
        <a href="{{ route('majikan-nanny-anda-detail', $item['id_nanny']) }}"
           class="nanny-card block"
           style="background:#fff;
                  border-radius:20px;
                  margin-bottom:16px;
                  border:2px solid #F3E6FA;
                  overflow:hidden;
                  animation: slideUp 0.3s ease {{ $i * 0.06 }}s both;
                  opacity:0;"
        >
            <!-- cardHeader: flexDirection:row, padding:16, alignItems:center -->
            <div class="flex items-center" style="padding:16px;">
                <!-- avatarContainer: mr:12 -->
                <div style="margin-right:12px;flex-shrink:0;">
                    @if(!empty($item['nanny_foto']))
                    <!-- avatar: w:60, h:60, borderRadius:30, border:3 solid #F3E6FA -->
                    <img src="{{ $item['nanny_foto'] }}"
                         alt="{{ $item['nanny_name'] }}"
                         class="object-cover"
                         style="width:60px;height:60px;border-radius:30px;border:3px solid #F3E6FA;"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                    >
                    <div class="items-center justify-center hidden"
                         style="width:60px;height:60px;border-radius:30px;background:#F3E6FA;border:3px solid #F3E6FA;">
                        <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                    </div>
                    @else
                    <!-- avatarPlaceholder: w:60, h:60, borderRadius:30, bg:#F3E6FA, border:3 -->
                    <div class="flex items-center justify-center"
                         style="width:60px;height:60px;border-radius:30px;background:#F3E6FA;border:3px solid #F3E6FA;">
                        <ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon>
                    </div>
                    @endif
                </div>

                <!-- cardHeaderInfo: flex:1 -->
                <div class="flex-1 min-w-0">
                    <!-- nama: fontSize:18, fontWeight:700, color:#4A0E35, mb:8 -->
                    <p style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;"
                       class="line-clamp-1">
                        {{ $item['nanny_name'] }}
                    </p>
                    <!-- infoRow > emailContainer: flexDirection:row, alignItems:center, gap:4 -->
                    <div class="flex items-center" style="gap:4px;">
                        <ion-icon name="mail" style="font-size:14px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                        <!-- infoText: fontSize:13, color:#7B1E5A, fontWeight:500 -->
                        <span class="truncate" style="font-size:13px;color:#7B1E5A;font-weight:500;">
                            {{ $item['nanny_email'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- divider: height:1, bg:#F3E6FA, mx:16 -->
            <div style="height:1px;background:#F3E6FA;margin:0 16px;"></div>

            <!-- detailSection: padding:16, gap:12 -->
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">

                <!-- Status Penugasan -->
                <div class="flex items-start">
                    <!-- detailIconContainer: w:32, h:32, borderRadius:10, bg:#F3E6FA, mr:12 -->
                    <div class="flex items-center justify-center flex-shrink-0"
                         style="width:32px;height:32px;border-radius:10px;background:#F3E6FA;margin-right:12px;">
                        <ion-icon name="information-circle" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <!-- detailContent: flex:1 -->
                    <div class="flex-1">
                        <!-- detailLabel: fontSize:12, color:#A2397B, fontWeight:600, mb:2 -->
                        <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:2px;">
                            Status Penugasan
                        </p>
                        <!-- statusBadge: px:12, py:4, borderRadius:12, alignSelf:flex-start, mt:4 -->
                        @php $isAktif = ($item['status'] ?? '') === 'aktif'; @endphp
                        <span style="display:inline-block;
                                     padding:4px 12px;
                                     border-radius:12px;
                                     margin-top:4px;
                                     background:{{ $isAktif ? '#E8F5E9' : '#F3E6FA' }};
                                     font-size:12px;
                                     font-weight:600;
                                     color:{{ $isAktif ? '#2E7D32' : '#7B1E5A' }};
                                     text-transform:capitalize;">
                            {{ $item['status'] ?? '-' }}
                        </span>
                    </div>
                </div>

                <!-- Periode Penugasan -->
                <div class="flex items-start">
                    <div class="flex items-center justify-center flex-shrink-0"
                         style="width:32px;height:32px;border-radius:10px;background:#F3E6FA;margin-right:12px;">
                        <ion-icon name="calendar" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:2px;">
                            Periode Penugasan
                        </p>
                        <!-- detailValue: fontSize:14, color:#4A0E35, fontWeight:500, lineHeight:20 -->
                        <p style="font-size:14px;color:#4A0E35;font-weight:500;line-height:20px;">
                            {{ $item['tanggal_mulai'] }} - {{ $item['tanggal_selesai'] }}
                        </p>
                    </div>
                </div>

                <!-- Catatan (kondisional) -->
                @if(!empty($item['catatan']))
                <div class="flex items-start">
                    <div class="flex items-center justify-center flex-shrink-0"
                         style="width:32px;height:32px;border-radius:10px;background:#F3E6FA;margin-right:12px;">
                        <ion-icon name="document-text" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:2px;">Catatan</p>
                        <p style="font-size:14px;color:#4A0E35;font-weight:500;line-height:20px;">
                            {{ $item['catatan'] }}
                        </p>
                    </div>
                </div>
                @endif

            </div>
        </a>
        @endforeach

        <!-- Bottom Spacing: height:30 -->
        <div style="height:30px;"></div>
    </div>

    @else

    <!-- EMPTY STATE
         emptyContainer: flex:1, justifyContent:center, alignItems:center, padding:40 -->
    <div class="flex-1 flex flex-col items-center justify-center" style="padding:40px;">
        <!-- emptyIconCircle: w:120, h:120, borderRadius:60, bg:#F3E6FA, mb:24 -->
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;margin-bottom:24px;">
            <ion-icon name="people-outline" style="font-size:80px;color:#B895C8;"></ion-icon>
        </div>
        <!-- emptyText: fontSize:18, fontWeight:700, color:#4A0E35, mb:8 -->
        <p class="text-center" style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;">
            Belum ada nanny yang aktif
        </p>
        <!-- emptySubtext: fontSize:14, color:#A2397B, textAlign:center, lineHeight:20 -->
        <p class="text-center" style="font-size:14px;color:#A2397B;line-height:20px;">
            Anda belum memiliki nanny yang sedang bertugas saat ini
        </p>
    </div>

    @endif

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'nanny-anda'])

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
</body>
</html>