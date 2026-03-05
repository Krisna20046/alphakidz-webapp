<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Data Anak</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { plum:{ DEFAULT:'#7B1E5A',light:'#9B2E72',dark:'#4A0E35',pale:'#FFF9FB',soft:'#F3E6FA',muted:'#A2397B' } },
                fontFamily: { sans:['Plus Jakarta Sans','sans-serif'] }
            }}
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#FFF9FB; }
        @media (min-width:640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3,#ede0f0,#e8d5ee); }
            .phone-frame   { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,0.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; position:relative; }
        }
        .card-item { transition:transform .15s ease; }
        .card-item:active { transform:scale(0.97); }
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        @keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim-up { animation:slideUp .35s ease both; }
        @keyframes fabIn { 0%{transform:scale(0);opacity:0} 70%{transform:scale(1.15)} 100%{transform:scale(1);opacity:1} }
        .fab-in { animation:fabIn .5s cubic-bezier(0.34,1.56,0.64,1) .3s both; }
        @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER PLUM -->
    <div class="bg-gradient-to-br from-plum to-plum-light px-5 pt-10 pb-14 relative shrink-0"
         style="border-radius:0 0 28px 28px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8"></div>
        <a href="{{ route('profil.index') }}"
           class="absolute top-10 left-5 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
            <ion-icon name="arrow-back" style="font-size:20px;color:white;"></ion-icon>
        </a>
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-3 shadow-lg">
                <ion-icon name="happy-outline" style="font-size:32px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold">Data Anak</h1>
            <p class="text-white/60 text-sm mt-1">Kelola data anak Anda</p>
        </div>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar -mt-6 px-4 pb-24 relative">

        @if(count($anakList) > 0)
        <!-- Count header -->
        <div class="flex items-center justify-between mb-3 pt-2">
            <h2 class="text-plum-dark font-extrabold text-base">Daftar Anak</h2>
            <div class="bg-plum-soft px-3 py-1 rounded-full">
                <span class="text-plum text-xs font-bold">{{ count($anakList) }} anak</span>
            </div>
        </div>

        <!-- LIST -->
        <div class="space-y-3">
            @foreach($anakList as $i => $anak)
            @php
                // Hitung umur
                $lahir    = new \DateTime($anak['tanggal_lahir']);
                $now      = new \DateTime();
                $diff     = $now->diff($lahir);
                $umur     = ($diff->y > 0 ? $diff->y.' tahun ' : '') . $diff->m.' bulan';
            @endphp
            <a href="{{ route('profil.anak.detail', $anak['id']) }}"
               class="card-item anim-up bg-white rounded-3xl p-4 flex items-center gap-4 shadow-sm shadow-plum/10 border border-plum-soft/40 block"
               style="animation-delay: {{ $i * 0.06 }}s">

                <!-- Avatar -->
                @if($anak['foto'] ?? null)
                    <img src="{{ $anak['foto'] }}" alt="{{ $anak['nama'] }}"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-plum-soft shrink-0"/>
                @else
                    <div class="w-14 h-14 rounded-2xl bg-plum-soft flex items-center justify-center shrink-0 border-2 border-plum-soft/60">
                        <ion-icon name="happy-outline" style="font-size:26px;color:#7B1E5A;"></ion-icon>
                    </div>
                @endif

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-plum-dark font-bold text-sm truncate">{{ $anak['nama'] }}</p>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="flex items-center gap-1 text-plum-muted text-xs font-medium">
                            <ion-icon name="calendar-outline" style="font-size:12px;"></ion-icon>
                            {{ $umur }}
                        </span>
                        <span class="flex items-center gap-1 text-plum-muted text-xs font-medium">
                            <ion-icon name="{{ $anak['gender'] === 'L' ? 'male' : 'female' }}-outline" style="font-size:12px;"></ion-icon>
                            {{ $anak['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>
                </div>

                <!-- Chevron -->
                <div class="w-8 h-8 rounded-full bg-plum-soft flex items-center justify-center shrink-0">
                    <ion-icon name="chevron-forward" style="font-size:15px;color:#7B1E5A;"></ion-icon>
                </div>
            </a>
            @endforeach
        </div>

        @else
        <!-- EMPTY STATE -->
        <div class="flex flex-col items-center justify-center pt-16 pb-8 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-plum-soft flex items-center justify-center mb-6">
                <ion-icon name="happy-outline" style="font-size:56px;color:#E0BBE4;"></ion-icon>
            </div>
            <h3 class="text-plum-dark font-bold text-lg mb-2">Belum ada data anak</h3>
            <p class="text-plum-muted text-sm text-center leading-relaxed">
                Tambahkan data anak Anda<br>untuk memulai
            </p>
        </div>
        @endif

    </div>

    <!-- FAB -->
    <a href="{{ route('profil.anak.tambah') }}"
       class="fab-in absolute bottom-24 right-5 w-14 h-14 rounded-full bg-gradient-to-br from-plum to-plum-light shadow-xl shadow-plum/40 flex items-center justify-center z-10">
        <ion-icon name="add" style="font-size:28px;color:white;"></ion-icon>
    </a>

</div>
</div>

<script>
function updateClock(){const el=document.getElementById('statusTime');if(el){const n=new Date();el.textContent=`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;}}
updateClock();setInterval(updateClock,30000);
</script>
</body>
</html>