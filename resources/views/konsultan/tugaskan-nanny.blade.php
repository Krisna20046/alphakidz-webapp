{{-- resources/views/konsultan/tugaskan-nanny.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tugaskan Nanny</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { plum: { DEFAULT:'#7B1E5A',light:'#9B2E72',dark:'#4A0E35',pale:'#FFF9FB',soft:'#F3E6FA',muted:'#A2397B',accent:'#B895C8' } }, fontFamily:{sans:['Plus Jakarta Sans','sans-serif']} } }
        }
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
        @keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .anim-up{animation:slideUp .4s ease forwards;}
        .anim-up.d1{animation-delay:.05s;opacity:0;} .anim-up.d2{animation-delay:.12s;opacity:0;} .anim-up.d3{animation-delay:.20s;opacity:0;}
        .nanny-card{transition:transform .15s ease,box-shadow .15s,opacity .15s;}
        .nanny-card:hover{box-shadow:0 6px 20px rgba(123,30,90,.12);opacity:.92;}
        .nanny-card:active{transform:scale(.98);opacity:.75;}
        @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .float-anim{animation:floatEmpty 3s ease-in-out infinite;}
        .no-scrollbar::-webkit-scrollbar{display:none;}
        .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        @keyframes spin2{to{transform:rotate(360deg)}} .spin{animation:spin2 1s linear infinite;}
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center"><div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white rounded-xs flex-1"></div></div></div></div>
    </div>

    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>
        <a href="{{ route('dashboard') }}" class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center anim-up d1">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-lg shadow-plum-dark/20">
                <ion-icon name="people" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Tugaskan Nanny</h1>
            <p class="text-white/60 text-xs font-medium text-center">Kelola penugasan nanny di bawah pengawasan Anda</p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto no-scrollbar px-4 pt-5 pb-4">
        <div id="loadingState" class="flex flex-col items-center justify-center pt-20">
            <div class="w-14 h-14 rounded-full bg-plum-soft flex items-center justify-center mb-4 spin">
                <ion-icon name="sync" style="font-size:26px;color:#7B1E5A;"></ion-icon>
            </div>
            <p class="text-plum-muted text-sm font-medium">Memuat data nanny...</p>
        </div>
        <div id="emptyState" class="hidden flex-col items-center pt-16 pb-10 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-plum-soft flex items-center justify-center mb-6">
                <ion-icon name="people-outline" style="font-size:52px;color:#B895C8;"></ion-icon>
            </div>
            <h3 class="text-plum-dark font-extrabold text-lg mb-2">Belum ada nanny</h3>
            <p class="text-plum-muted text-sm text-center leading-relaxed">Anda belum memiliki nanny yang terdaftar</p>
        </div>
        <div id="listState" class="hidden">
            <div class="flex items-center justify-between mb-4 anim-up d2">
                <h2 class="text-plum-dark font-extrabold text-base">Daftar Nanny</h2>
                <div class="bg-plum-soft px-3 py-1 rounded-full"><span class="text-plum text-xs font-extrabold" id="nannyCount">0</span></div>
            </div>
            <div id="nannyCards" class="space-y-4 anim-up d3"></div>
        </div>
        <div class="h-6"></div>
    </div>

    @include('partials.bottom-nav', ['active' => 'home'])
</div>
</div>

<script>
const API_BASE_URL   = '{{ env("API_BASE_URL") }}';
const API_TOKEN = '{{ session("token") }}';

const STATUS_MAP = {
    active:   {label:'Aktif Bertugas',       icon:'time',             bg:'bg-blue-100',  text:'text-blue-600'},
    pending:  {label:'Menunggu Konfirmasi',  icon:'hourglass',        bg:'bg-yellow-100',text:'text-yellow-700'},
    completed:{label:'Tugas Selesai',        icon:'checkmark-done',   bg:'bg-gray-100',  text:'text-gray-600'},
    cancelled:{label:'Dibatalkan',           icon:'close-circle',     bg:'bg-red-100',   text:'text-red-600'},
    tersedia: {label:'Tersedia',             icon:'checkmark-circle', bg:'bg-green-100', text:'text-green-700'},
};

function avatar(foto,size='w-14 h-14'){
    if(foto) return `<img src="${foto}" class="${size} rounded-full object-cover flex-shrink-0" style="border:3px solid #F3E6FA;" onerror="this.outerHTML='<div class=\\'${size} rounded-full bg-plum-soft flex items-center justify-center flex-shrink-0\\' style=\\'border:3px solid #F3E6FA;\\'><ion-icon name=\\'person\\' style=\\'font-size:24px;color:#7B1E5A;\\'></ion-icon></div>'">`;
    return `<div class="${size} rounded-full bg-plum-soft flex items-center justify-center flex-shrink-0" style="border:3px solid #F3E6FA;"><ion-icon name="person" style="font-size:24px;color:#7B1E5A;"></ion-icon></div>`;
}

function cardHtml(item,i){
    const s    = item.is_assigned ? (STATUS_MAP[item.assignment_status]||STATUS_MAP.tersedia) : STATUS_MAP.tersedia;
    const href = !item.is_assigned
        ? `{{ url('/konsultan/tugaskan-nanny') }}/${item.id}/tambah`
        : `{{ url('/konsultan/tugaskan-nanny/assignment') }}/${item.id_assignment}/ubah`;
    return `
    <a href="${href}" class="nanny-card bg-white rounded-3xl border-2 border-plum-soft overflow-hidden block" style="animation:slideUp .3s ease ${i*.06}s both;opacity:0;">
        <div class="flex items-center gap-3 p-4 border-b border-plum-soft">
            ${avatar(item.foto)}
            <div class="flex-1 min-w-0">
                <p class="text-plum-dark font-extrabold text-base truncate mb-1.5">${item.name||'-'}</p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold ${s.bg} ${s.text}">
                    <ion-icon name="${s.icon}" style="font-size:12px;"></ion-icon>${s.label}
                </span>
            </div>
        </div>
        <div class="px-4 py-3 space-y-2">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0"><ion-icon name="person-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon></div>
                <div><p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Gender</p><p class="text-plum-dark text-xs font-semibold">${item.gender==='L'?'Laki-laki':'Perempuan'}</p></div>
            </div>
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0"><ion-icon name="mail-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon></div>
                <div class="flex-1 min-w-0"><p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Email</p><p class="text-plum-dark text-xs font-semibold truncate">${item.email||'-'}</p></div>
            </div>
            ${item.id_assignment?`<div class="flex items-center gap-2.5"><div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0"><ion-icon name="document-text-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon></div><div><p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Assignment ID</p><p class="text-plum-dark text-xs font-semibold">#${item.id_assignment}</p></div></div>`:''}
            ${item.catatan?`<div class="flex items-start gap-2.5 pt-1 border-t border-plum-soft"><div class="w-7 h-7 rounded-lg bg-plum-soft flex items-center justify-center flex-shrink-0 mt-0.5"><ion-icon name="document-text" style="font-size:13px;color:#7B1E5A;"></ion-icon></div><div><p class="text-plum-muted text-[10px] font-semibold uppercase tracking-wide">Catatan</p><p class="text-plum-dark text-xs font-semibold leading-snug">${item.catatan}</p></div></div>`:''}
        </div>
        <div class="flex items-center justify-end gap-2 px-4 py-3 border-t border-plum-soft bg-plum-pale/50">
            <span class="text-plum-muted text-[11px] italic">${!item.is_assigned?'Tap untuk tugaskan':'Tap untuk lihat detail'}</span>
            <div class="w-7 h-7 rounded-full bg-plum-soft flex items-center justify-center"><ion-icon name="chevron-forward" style="font-size:14px;color:#7B1E5A;"></ion-icon></div>
        </div>
    </a>`;
}

async function loadNannies(){
    try{
        const res  = await fetch(`${API_BASE_URL}/konsultan-nanny`,{headers:{'Accept':'application/json','Authorization':`Bearer ${API_TOKEN}`}});
        const json = await res.json();
        const data = json.status==='success'&&Array.isArray(json.data)?json.data:[];
        document.getElementById('loadingState').classList.add('hidden');
        if(!data.length){document.getElementById('emptyState').classList.remove('hidden');document.getElementById('emptyState').classList.add('flex');return;}
        document.getElementById('nannyCount').textContent=data.length;
        document.getElementById('nannyCards').innerHTML=data.map((item,i)=>cardHtml(item,i)).join('');
        document.getElementById('listState').classList.remove('hidden');
    }catch(e){
        document.getElementById('loadingState').innerHTML=`<div class="text-center px-8"><div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4"><ion-icon name="alert-circle" style="font-size:32px;color:#DC2626;"></ion-icon></div><p class="text-red-600 font-bold mb-3">Gagal memuat data</p><button onclick="loadNannies()" class="bg-plum text-white text-sm font-bold px-6 py-2.5 rounded-xl">Coba lagi</button></div>`;
    }
}

function updateClock(){const now=new Date(),h=String(now.getHours()).padStart(2,'0'),m=String(now.getMinutes()).padStart(2,'0'),el=document.getElementById('statusTime');if(el)el.textContent=`${h}:${m}`;}
updateClock();setInterval(updateClock,30000);
loadNannies();
</script>
@include('partials.auth-guard')
</body>
</html>