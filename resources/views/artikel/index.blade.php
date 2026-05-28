{{-- resources/views/artikel/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Artikel</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim         { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1      { animation-delay: 0.05s; }
        .delay-2      { animation-delay: 0.13s; }
        .delay-3      { animation-delay: 0.21s; }
        .delay-4      { animation-delay: 0.29s; }
        .delay-5      { animation-delay: 0.37s; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .section-card {
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.09);
        }

        @keyframes floatEmpty {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    {{-- STATUS BAR --}}
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0"   y="4"   width="3" height="7"    rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5"  rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9"   y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-start gap-3 relative z-10">
            <a href="{{ route('dashboard') }}"
               class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Articles</span>
                <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Tips & Parenting Information</p>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
                bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
                rounded-t-[50px] -mt-[50px] relative z-20
                hide-scrollbar space-y-4">

        {{-- KATEGORI --}}
        <div class="anim delay-2">
            <div class="flex items-center gap-2 mb-3 px-1">
                <ion-icon name="grid-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
                <span class="text-[#1E1B2E] text-[15px] font-extrabold">Category</span>
            </div>
            <div class="flex gap-2 flex-wrap">
                @php
                $categories = [
                    ['icon'=>'leaf-outline',        'label'=>'Nutrisi',      'bg'=>'#EDE9FE', 'text'=>'#8B46D3'],
                    ['icon'=>'fitness-outline',      'label'=>'Kesehatan',    'bg'=>'#FDE8EF', 'text'=>'#EC4899'],
                    ['icon'=>'bulb-outline',         'label'=>'Edukasi',      'bg'=>'#FFFBEB', 'text'=>'#F59E0B'],
                    ['icon'=>'game-controller-outline','label'=>'Aktivitas',  'bg'=>'#EDE9FE', 'text'=>'#8B46D3'],
                    ['icon'=>'heart-outline',        'label'=>'Parenting',    'bg'=>'#FDF2F8', 'text'=>'#EC4899'],
                    ['icon'=>'medkit-outline',       'label'=>'Imunisasi',    'bg'=>'#EEF2FF', 'text'=>'#4F46E5'],
                ];
                @endphp
                @foreach($categories as $cat)
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold opacity-70"
                     style="background:{{ $cat['bg'] }};color:{{ $cat['text'] }};">
                    <ion-icon name="{{ $cat['icon'] }}" style="font-size:13px;"></ion-icon>
                    {{ $cat['label'] }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- ARTIKEL POPULER --}}
        <div class="anim delay-3">
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <ion-icon name="trending-up-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
                    <span class="text-[#1E1B2E] text-[15px] font-extrabold">Popular Articles</span>
                </div>
                <span class="text-[#9CA3AF] text-[10px] font-bold bg-[#EDE9FE] px-2.5 py-1 rounded-full">Soon</span>
            </div>

            <div class="space-y-3">
                @php
                $articles = [
                    ['title'=>'Cara Tepat Memperkenalkan Makanan Padat pada Bayi',    'cat'=>'Nutrisi',   'icon'=>'leaf-outline',     'time'=>'5 min read', 'bg'=>'bg-[#EDE9FE]', 'iconColor'=>'#8B46D3'],
                    ['title'=>'7 Aktivitas Stimulasi Terbaik untuk Balita 1-3 Tahun', 'cat'=>'Aktivitas', 'icon'=>'game-controller-outline', 'time'=>'7 min read', 'bg'=>'bg-[#FDF2F8]', 'iconColor'=>'#EC4899'],
                    ['title'=>'Jadwal Imunisasi Lengkap yang Wajib Diketahui Orang Tua','cat'=>'Imunisasi','icon'=>'medkit-outline',  'time'=>'4 min read', 'bg'=>'bg-[#EEF2FF]', 'iconColor'=>'#4F46E5'],
                ];
                @endphp
                @foreach($articles as $i => $art)
                <div class="anim section-card p-4 flex items-center gap-3 relative overflow-hidden"
                     style="animation-delay: {{ 0.21 + $i * 0.08 }}s;">
                    <!-- Lock overlay -->
                    <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10 rounded-[18px]">
                        <div class="flex items-center gap-1.5 bg-[#EDE9FE] rounded-full px-3 py-1">
                            <ion-icon name="lock-closed" style="font-size:11px;color:#8B46D3;"></ion-icon>
                            <span class="text-[#8B46D3] text-[10px] font-bold">Coming Soon</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl {{ $art['bg'] }} flex items-center justify-center shrink-0">
                        <ion-icon name="{{ $art['icon'] }}" style="font-size:22px;color:{{ $art['iconColor'] }};"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#1E1B2E] font-bold text-xs leading-snug line-clamp-2 mb-1">{{ $art['title'] }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-[#9CA3AF] text-[10px] font-semibold">{{ $art['cat'] }}</span>
                            <span class="text-[#E5E1F0] text-[10px]">•</span>
                            <span class="text-[#9CA3AF] text-[10px]">{{ $art['time'] }}</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward" style="font-size:14px;color:#C4B5FD;flex-shrink:0;"></ion-icon>
                </div>
                @endforeach
            </div>
        </div>

        {{-- SEMUA ARTIKEL --}}
        <div class="anim delay-4">
            <div class="flex items-center gap-2 mb-3 px-1">
                <ion-icon name="layers-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
                <span class="text-[#1E1B2E] text-[15px] font-extrabold">All Articles</span>
            </div>

            @if(isset($artikels) && count($artikels) > 0)
                <div class="space-y-3">
                    @foreach($artikels as $i => $artikel)
                    <div class="anim section-card p-4 flex items-center gap-3 relative overflow-hidden"
                         style="animation-delay: {{ 0.29 + $i * 0.06 }}s;">
                        <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10 rounded-[18px]">
                            <div class="flex items-center gap-1.5 bg-[#EDE9FE] rounded-full px-3 py-1">
                                <ion-icon name="lock-closed" style="font-size:11px;color:#8B46D3;"></ion-icon>
                                <span class="text-[#8B46D3] text-[10px] font-bold">Coming Soon</span>
                            </div>
                        </div>
                        @if(!empty($artikel['thumbnail']))
                        <div class="w-16 h-16 rounded-xl overflow-hidden shrink-10">
                            <img src="{{ $artikel['thumbnail'] }}" alt="" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="w-16 h-16 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="newspaper-outline" style="font-size:24px;color:#8B46D3;"></ion-icon>
                        </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <span class="inline-block px-2 py-[2px] rounded-full bg-[#FDF2F8] text-[#EC4899] text-[9px] font-bold mb-1.5">{{ $artikel['kategori'] ?? 'Artikel' }}</span>
                            <p class="text-[#1E1B2E] font-bold text-xs leading-snug line-clamp-2">{{ $artikel['judul'] }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                    <ion-icon name="time-outline" style="font-size:10px;"></ion-icon>
                                    {{ $artikel['read_time'] ?? '5' }} min
                                </span>
                                <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                    <ion-icon name="eye-outline" style="font-size:10px;"></ion-icon>
                                    {{ $artikel['views'] ?? '0' }}
                                </span>
                            </div>
                        </div>
                        <ion-icon name="chevron-forward" style="font-size:14px;color:#C4B5FD;flex-shrink:0;"></ion-icon>
                    </div>
                    @endforeach
                </div>
            @else
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center py-12 px-8">
                    <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
                        <ion-icon name="newspaper-outline" style="font-size:48px;color:#C4B5FD;"></ion-icon>
                    </div>
                    <h2 class="text-[#1E1B2E] font-extrabold text-lg mb-1 text-center">No Articles Yet</h2>
                    <p class="text-[#9CA3AF] text-xs text-center leading-relaxed">Articles are being prepared.<br>Come back later!</p>
                </div>
            @endif
        </div>

        <div class="h-4"></div>
    </div>

    @include('partials.bottom-nav', ['active' => 'artikel'])

</div>
</div>

<script>
    (function () {
        const el = document.getElementById('statusTime');
        function tick() {
            const now = new Date();
            if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        tick();
        setInterval(tick, 30000);
    })();
</script>
@include('partials.auth-guard')
@include('partials.permission-modals')
</body>
</html>
