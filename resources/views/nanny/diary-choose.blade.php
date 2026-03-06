{{-- resources/views/nanny/diary-choose.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pilih Diary Anak</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config={theme:{extend:{colors:{plum:{DEFAULT:'#7B1E5A',light:'#9B2E72',dark:'#4A0E35',pale:'#FFF9FB',soft:'#F3E6FA',muted:'#A2397B',accent:'#B895C8'}},fontFamily:{sans:['Plus Jakarta Sans','sans-serif']}}}}
    </script>
    <style>
        *{-webkit-tap-highlight-color:transparent;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:#FFF9FB;}
        @media(min-width:640px){
            .phone-wrapper{display:flex;align-items:flex-start;justify-content:center;min-height:100vh;padding:32px 0;background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%);}
            .phone-frame{width:390px;min-height:844px;border-radius:44px;box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020;overflow:hidden;position:relative;}
        }
        @media(max-width:639px){.phone-wrapper{min-height:100vh;}.phone-frame{min-height:100vh;}}
        .header-bg{background:linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%);}
        @keyframes slideUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
        .anim-up{animation:slideUp .35s ease forwards;}
        .anim-up.d1{animation-delay:.05s;opacity:0;}
        .anim-up.d2{animation-delay:.10s;opacity:0;}
        .anak-card{transition:opacity .15s ease,transform .15s ease;display:flex;align-items:center;background:#fff;}
        .anak-card:hover{opacity:.85;}
        .anak-card:active{transform:scale(0.98);opacity:.7;}
        @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .float-anim{animation:floatEmpty 3s ease-in-out infinite;}
        .no-scrollbar::-webkit-scrollbar{display:none;}.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
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
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header-bg relative shrink-0 overflow-hidden"
         style="padding:50px 20px 24px;border-bottom-left-radius:24px;border-bottom-right-radius:24px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>
        <a href="{{ route('dashboard') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px;left:20px;width:40px;height:40px;z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center anim-up d1">
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg"
                 style="width:64px;height:64px;">
                <ion-icon name="book" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Pilih Diary Anak</h1>
            <p style="font-size:14px;color:#F3E6FA;font-weight:500;">Pilih anak untuk mencatat diary</p>
        </div>
    </div>

    @php
        $anak       = $assignmentData['anak'] ?? [];
        $majikan    = $assignmentData['majikan_name'] ?? null;
        $tglMulai   = $assignmentData['tanggal_mulai'] ?? '';
        $tglSelesai = $assignmentData['tanggal_selesai'] ?? '';
    @endphp

    @if(count($anak) > 0)

    {{-- Assignment info --}}
    <div class="anim-up d2" style="padding:20px 20px 0;">
        <div class="flex items-center bg-white"
             style="border-radius:16px;border:2px solid #F3E6FA;padding:14px 16px;gap:12px;">
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:44px;height:44px;border-radius:12px;background:#F3E6FA;">
                <ion-icon name="briefcase" style="font-size:20px;color:#7B1E5A;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <p style="font-size:11px;color:#A2397B;font-weight:600;margin-bottom:2px;">Penugasan dengan</p>
                <p class="truncate" style="font-size:15px;font-weight:700;color:#4A0E35;margin-bottom:3px;">{{ $majikan ?? '-' }}</p>
                <div class="flex items-center" style="gap:5px;">
                    <ion-icon name="calendar-outline" style="font-size:12px;color:#A2397B;flex-shrink:0;"></ion-icon>
                    <span style="font-size:12px;color:#A2397B;font-weight:500;">{{ $tglMulai }} – {{ $tglSelesai }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- List header --}}
    <div class="flex items-center justify-between anim-up d2" style="padding:20px 20px 12px;">
        <span style="font-size:18px;font-weight:700;color:#4A0E35;">Daftar Anak</span>
        <span style="background:#F3E6FA;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700;color:#7B1E5A;">
            {{ count($anak) }}
        </span>
    </div>

    {{-- Scrollable list --}}
    <div class="flex-1 overflow-y-auto no-scrollbar" style="padding:0 20px;">
        @foreach($anak as $i => $child)
        @php
            $lahir  = new DateTime($child['tanggal_lahir'] ?? 'now');
            $diff   = $lahir->diff(new DateTime());
            $umur   = ($diff->y > 0 ? $diff->y.' tahun ' : '') . $diff->m . ' bulan';
            $isMale = ($child['gender'] ?? '') === 'L';
        @endphp
        <a href="{{ route('nanny-diary', ['id_anak' => $child['id']]) }}"
           class="anak-card"
           style="border-radius:16px;margin-bottom:12px;border:2px solid #F3E6FA;padding:16px;
                  animation:slideUp .3s ease {{ $i*.06 }}s both;opacity:0;">
            {{-- Avatar --}}
            <div style="margin-right:12px;flex-shrink:0;">
                @if(!empty($child['foto']))
                <img src="{{ $child['foto'] }}" alt="{{ $child['nama'] }}" class="object-cover"
                     style="width:56px;height:56px;border-radius:28px;border:3px solid #F3E6FA;"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="items-center justify-center hidden"
                     style="width:56px;height:56px;border-radius:28px;background:#F3E6FA;border:3px solid #F3E6FA;">
                    <ion-icon name="body" style="font-size:24px;color:#7B1E5A;"></ion-icon>
                </div>
                @else
                <div class="flex items-center justify-center"
                     style="width:56px;height:56px;border-radius:28px;background:#F3E6FA;border:3px solid #F3E6FA;">
                    <ion-icon name="body" style="font-size:24px;color:#7B1E5A;"></ion-icon>
                </div>
                @endif
            </div>
            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="truncate" style="font-size:16px;font-weight:700;color:#4A0E35;margin-bottom:6px;">
                    {{ $child['nama'] }}
                </p>
                <div class="flex items-center" style="gap:12px;">
                    <div class="flex items-center" style="gap:4px;">
                        <ion-icon name="gift" style="font-size:13px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                        <span style="font-size:13px;color:#7B1E5A;font-weight:500;">{{ trim($umur) }}</span>
                    </div>
                    <div class="flex items-center" style="gap:4px;">
                        <ion-icon name="{{ $isMale ? 'male' : 'female' }}" style="font-size:13px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                        <span style="font-size:13px;color:#7B1E5A;font-weight:500;">{{ $isMale ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                </div>
            </div>
            {{-- Arrow --}}
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:32px;height:32px;border-radius:16px;background:#F3E6FA;margin-left:8px;">
                <ion-icon name="chevron-forward" style="font-size:20px;color:#B895C8;"></ion-icon>
            </div>
        </a>
        @endforeach
        <div style="height:30px;"></div>
    </div>

    @else
    {{-- Empty state --}}
    <div class="flex-1 flex flex-col items-center justify-center" style="padding:40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#F3E6FA;margin-bottom:24px;">
            <ion-icon name="body-outline" style="font-size:60px;color:#B895C8;"></ion-icon>
        </div>
        <p class="text-center" style="font-size:18px;font-weight:700;color:#4A0E35;margin-bottom:8px;">Belum ada data anak</p>
        <p class="text-center" style="font-size:14px;color:#A2397B;line-height:22px;">
            @if($assignmentData)
                Penugasan dengan {{ $assignmentData['majikan_name'] ?? '' }}<br>
                {{ $assignmentData['tanggal_mulai'] ?? '' }} – {{ $assignmentData['tanggal_selesai'] ?? '' }}
            @else
                Tidak ada penugasan aktif saat ini
            @endif
        </p>
    </div>
    @endif

    @include('partials.bottom-nav', ['active' => ''])
</div>
</div>
<script>
    function updateClock(){const n=new Date(),e=document.getElementById('statusTime');if(e)e.textContent=String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0');}
    updateClock();setInterval(updateClock,30000);
</script>
@include('partials.auth-guard')
</body>
</html>