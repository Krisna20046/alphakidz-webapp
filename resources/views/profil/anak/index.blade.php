<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Data Anak</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { screens: { sm: '1024px' } } } };
    </script>
    <script>
        // Force mobile layout on phones even when "Desktop Site" mode is active
        (function() {
            var isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            var isPhoneScreen = window.screen.width <= 430 || window.screen.height <= 932;
            if (isTouchDevice && isPhoneScreen && window.innerWidth >= 1024) {
                var meta = document.querySelector('meta[name="viewport"]');
                if (meta) meta.content = 'width=430, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                var s = document.createElement('style');
                s.textContent = '.phone-wrapper{min-height:100vh!important;display:block!important;padding:0!important;background:#F0EDFB!important}.phone-frame{min-height:100vh!important;width:100%!important;border-radius:0!important;box-shadow:none!important}';
                document.head.appendChild(s);
            }
        })();
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .card-item { transition: transform 0.15s ease; }
        .card-item:active { transform: scale(0.97); }

        @keyframes fabIn {
            0%   { transform: scale(0); opacity: 0; }
            70%  { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        .fab-in { animation: fabIn 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.3s both; }

        @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">

<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    <!-- STATUS BAR (desktop only) -->
    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PURPLE HEADER -->
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('profil.index') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Child Data</span>
                <p class="text-white/60 text-xs font-medium mt-0.5">All your child's data</p>
            </div>
        </div>
    </div>

    <!-- WHITE BODY — rounded top, overlaps header -->
    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 flex flex-col gap-4 hide-scrollbar">

        @if(count($anakList) > 0)
        <!-- Count header -->
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-[#8B46D3] font-extrabold text-[15px]">Child Data List</h2>
            <div class="bg-[#EDE9FE] px-4 py-1.5 rounded-full">
                <span class="text-[#8B46D3] text-xs font-bold">{{ count($anakList) }} Nanny</span>
            </div>
        </div>

        <!-- LIST -->
        <div class="flex flex-col gap-3">
            @foreach($anakList as $i => $anak)
            @php
                $lahir = new \DateTime($anak['tanggal_lahir']);
                $now   = new \DateTime();
                $diff  = $now->diff($lahir);
                $umur  = ($diff->y > 0 ? $diff->y.' Tahun ' : '') . $diff->m.' Bulan';
            @endphp
            <a href="{{ route('profil.anak.detail', $anak['id']) }}"
               class="card-item block bg-white rounded-[20px] p-[14px] flex items-center gap-4 shadow-[0_2px_12px_rgba(0,0,0,0.07)]"
               style="animation: slideUp 0.4s ease {{ $i * 0.07 }}s both; opacity:0;">

                <!-- Avatar -->
                @if($anak['foto'] ?? null)
                    <img src="{{ $anak['foto'] }}" alt="{{ $anak['nama'] }}"
                         class="w-[64px] h-[64px] rounded-[14px] object-cover shrink-0"/>
                @else
                    <div class="w-[64px] h-[64px] rounded-[14px] bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="happy-outline" style="font-size:28px;color:#8B46D3;"></ion-icon>
                    </div>
                @endif

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-[#1E1B2E] font-extrabold text-[14px] mb-[6px] truncate">{{ $anak['nama'] }}</p>

                    <!-- Umur -->
                    <div class="flex items-center gap-[6px] mb-[4px]">
                        <iconify-icon icon="mingcute:birthday-2-fill" style="font-size:13px;color:#8B46D3;"></iconify-icon>
                        <span class="text-[#6B6589] text-[12px] font-semibold">{{ $umur }}</span>
                    </div>

                    <!-- Tidak suka makanan -->
                    @if($anak['catatan_khusus'] ?? null)
                    <div class="flex items-center gap-[6px] mb-[4px]">
                        <iconify-icon icon="garden:notes-fill-12" style="font-size:13px;color:#3B82F6;"></iconify-icon>
                        <span class="text-[#6B6589] text-[12px] font-semibold truncate">{{ $anak['catatan_khusus'] }}</span>
                    </div>
                    @endif

                    <!-- Alergi -->
                    @if($anak['alergi'] ?? null)
                    <div class="flex items-center gap-[6px]">
                        <iconify-icon icon="material-symbols:warning" style="font-size:13px;color:#FF8A00;"></iconify-icon>
                        <span class="text-[#6B6589] text-[12px] font-semibold truncate">{{ $anak['alergi'] }}</span>
                    </div>
                    @endif
                </div>

            </a>
            @endforeach
        </div>

        @else
        <!-- EMPTY STATE -->
        <div class="flex flex-col items-center justify-center pt-20 pb-8 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                <ion-icon name="happy-outline" style="font-size:56px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">Belum ada data anak</h3>
            <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                Tambahkan data anak Anda<br>untuk memulai
            </p>
        </div>
        @endif

    </div>

    <!-- FAB -->
    <a href="{{ route('profil.anak.tambah') }}"
       class="fab-in fixed sm:absolute bottom-24 right-5 w-14 h-14 rounded-full bg-[#8B46D3] shadow-[0_8px_24px_rgba(139,70,211,0.45)] flex items-center justify-center z-30">
        <ion-icon name="add" style="font-size:30px;color:white;"></ion-icon>
    </a>

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'profil'])

</div>
</div>

<script>
(function () {
    const el = document.getElementById('statusTime');
    function tick() {
        const now = new Date();
        if (el) el.textContent = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
    }
    tick(); setInterval(tick, 30000);
})();
</script>

@include('partials.auth-guard')

</body>
</html>