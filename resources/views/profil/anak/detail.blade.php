<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Anak</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { plum:{DEFAULT:'#7B1E5A',light:'#9B2E72',dark:'#4A0E35',pale:'#FFF9FB',soft:'#F3E6FA',muted:'#A2397B'} },
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
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        @keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim-up { animation:slideUp .4s ease forwards; }
        .d1{animation-delay:.05s;opacity:0} .d2{animation-delay:.12s;opacity:0} .d3{animation-delay:.19s;opacity:0} .d4{animation-delay:.26s;opacity:0}
        #deleteModal { transition:opacity .2s ease; }
        #deleteModalBox { transition:transform .3s cubic-bezier(0.34,1.56,0.64,1); }
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

    <!-- HEADER -->
    <div class="bg-gradient-to-br from-plum to-plum-light px-5 pt-10 pb-14 relative shrink-0" style="border-radius:0 0 28px 28px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8"></div>
        <a href="{{ route('profil.data-anak') }}"
           class="absolute top-10 left-5 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
            <ion-icon name="arrow-back" style="font-size:20px;color:white;"></ion-icon>
        </a>
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-3 shadow-lg">
                <ion-icon name="happy-outline" style="font-size:32px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold">Detail Anak</h1>
            <p class="text-white/60 text-sm mt-1">Informasi lengkap anak</p>
        </div>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar -mt-6 px-4 pb-6 space-y-4">

        <!-- Profile Card -->
        <div class="anim-up d1 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 flex flex-col items-center">
            @if($anak['foto'] ?? null)
                <img src="{{ $anak['foto'] }}" alt="{{ $anak['nama'] }}"
                     class="w-24 h-24 rounded-2xl object-cover border-4 border-plum-soft mb-4 shadow-md"/>
            @else
                <div class="w-24 h-24 rounded-2xl bg-plum-soft border-4 border-plum-soft/60 flex items-center justify-center mb-4 shadow-md">
                    <ion-icon name="happy-outline" style="font-size:44px;color:#7B1E5A;"></ion-icon>
                </div>
            @endif
            <h2 class="text-plum-dark text-xl font-extrabold">{{ $anak['nama'] }}</h2>
            <div class="mt-2 flex items-center gap-1.5 bg-plum-soft px-4 py-1.5 rounded-full">
                <ion-icon name="{{ $anak['gender'] === 'L' ? 'male' : 'female' }}-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon>
                <span class="text-plum text-xs font-bold">
                    {{ $anak['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' }}
                </span>
            </div>
        </div>

        @php
            $lahir  = new \DateTime($anak['tanggal_lahir']);
            $now    = new \DateTime();
            $diff   = $now->diff($lahir);
            $umur   = ($diff->y > 0 ? $diff->y.' tahun ' : '') . $diff->m.' bulan';
        @endphp

        <!-- Umur Card -->
        <div class="anim-up d2 bg-white rounded-2xl p-4 shadow-sm shadow-plum/10 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-plum-soft flex items-center justify-center shrink-0">
                <ion-icon name="balloon-outline" style="font-size:22px;color:#7B1E5A;"></ion-icon>
            </div>
            <div>
                <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">Usia</p>
                <p class="text-plum-dark text-lg font-extrabold">{{ $umur }}</p>
            </div>
        </div>

        <!-- Info Detail Card -->
        <div class="anim-up d2 bg-white rounded-3xl p-5 shadow-sm shadow-plum/10 space-y-4">
            <p class="text-plum-dark font-bold text-sm">Informasi Detail</p>

            @php
                $rows = [
                    ['icon'=>'calendar-outline', 'label'=>'Tanggal Lahir', 'value'=>$anak['tanggal_lahir']],
                ];
                if($anak['catatan_khusus'] ?? null) $rows[] = ['icon'=>'document-text-outline','label'=>'Catatan Khusus','value'=>$anak['catatan_khusus']];
                if($anak['alergi'] ?? null)         $rows[] = ['icon'=>'warning-outline','label'=>'Alergi','value'=>$anak['alergi']];
                if($anak['hobi'] ?? null)            $rows[] = ['icon'=>'heart-outline','label'=>'Hobi','value'=>$anak['hobi']];
            @endphp

            @foreach($rows as $row)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-plum-soft flex items-center justify-center shrink-0 mt-0.5">
                    <ion-icon name="{{ $row['icon'] }}" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-plum-muted font-semibold uppercase tracking-wide">{{ $row['label'] }}</p>
                    <p class="text-plum-dark text-sm font-medium mt-0.5 break-words">{{ $row['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="anim-up d3 grid grid-cols-2 gap-3">
            <a href="{{ route('profil.anak.ubah', $anak['id']) }}"
               class="flex items-center justify-center gap-2 py-4 rounded-2xl bg-amber-50 border-2 border-amber-200 active:scale-95 transition-all">
                <ion-icon name="create-outline" style="font-size:18px;color:#F59E0B;"></ion-icon>
                <span class="text-amber-600 font-bold text-sm">Ubah Data</span>
            </a>
            <button onclick="showDeleteModal()"
                    class="flex items-center justify-center gap-2 py-4 rounded-2xl bg-red-50 border-2 border-red-200 active:scale-95 transition-all">
                <ion-icon name="trash-outline" style="font-size:18px;color:#EF4444;"></ion-icon>
                <span class="text-red-500 font-bold text-sm">Hapus Data</span>
            </button>
        </div>

        <div class="h-4"></div>
    </div>

</div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 hidden opacity-0 px-4 pb-8 sm:pb-0">
    <div id="deleteModalBox" class="w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl scale-90">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <ion-icon name="trash-outline" style="font-size:30px;color:#ef4444;"></ion-icon>
            </div>
            <h3 class="text-plum-dark text-lg font-extrabold mb-1">Hapus Data Anak?</h3>
            <p class="text-plum-muted text-sm leading-relaxed">Tindakan ini tidak dapat dibatalkan. Data anak <strong class="text-plum-dark">{{ $anak['nama'] }}</strong> akan dihapus permanen.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="hideDeleteModal()"
                    class="flex-1 py-3.5 rounded-2xl border-2 border-plum-soft text-plum font-bold text-sm active:bg-plum-soft transition-all">
                Batal
            </button>
            <form method="POST" action="{{ route('profil.anak.hapus', $anak['id']) }}" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" id="deleteSubmitBtn"
                        class="w-full py-3.5 rounded-2xl bg-red-500 text-white font-bold text-sm active:bg-red-600 transition-all flex items-center justify-center gap-2">
                    <span>Ya, Hapus</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function updateClock(){const el=document.getElementById('statusTime');if(el){const n=new Date();el.textContent=`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;}}
updateClock();setInterval(updateClock,30000);

const modal    = document.getElementById('deleteModal');
const modalBox = document.getElementById('deleteModalBox');

function showDeleteModal() {
    modal.classList.remove('hidden');
    requestAnimationFrame(() => { modal.style.opacity='1'; modalBox.style.transform='scale(1)'; });
}
function hideDeleteModal() {
    modal.style.opacity='0'; modalBox.style.transform='scale(0.9)';
    setTimeout(() => modal.classList.add('hidden'), 200);
}
modal.addEventListener('click', e => { if(e.target===modal) hideDeleteModal(); });
</script>
</body>
</html>