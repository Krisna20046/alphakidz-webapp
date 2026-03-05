{{-- resources/views/konsultan/tugaskan-nanny-ubah.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ubah Penugasan</title>
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
        @media(min-width:640px){.phone-wrapper{display:flex;align-items:flex-start;justify-content:center;min-height:100vh;padding:32px 0;background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%);}
        .phone-frame{width:390px;min-height:844px;border-radius:44px;box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020;overflow:hidden;position:relative;}}
        @media(max-width:639px){.phone-wrapper{min-height:100vh;}.phone-frame{min-height:100vh;}}
        .header-bg{background:linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%);}
        @keyframes slideUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
        .anim-up{animation:slideUp .35s ease forwards;}
        .anim-up.d1{animation-delay:.05s;opacity:0;} .anim-up.d2{animation-delay:.12s;opacity:0;} .anim-up.d3{animation-delay:.19s;opacity:0;} .anim-up.d4{animation-delay:.26s;opacity:0;} .anim-up.d5{animation-delay:.33s;opacity:0;} .anim-up.d6{animation-delay:.40s;opacity:0;}
        .no-scrollbar::-webkit-scrollbar{display:none;} .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        .btn-primary{background:linear-gradient(135deg,#7B1E5A,#9B2E72);transition:opacity .2s,transform .15s;} .btn-primary:hover{opacity:.92;} .btn-primary:active{transform:scale(.97);}
        .modal-overlay{background:rgba(74,14,53,.5);backdrop-filter:blur(4px);}
        @keyframes sheetUp{from{transform:translateY(100%)}to{transform:translateY(0)}} .sheet-anim{animation:sheetUp .25s ease forwards;}
        .anak-row{transition:border-color .15s,background .15s;} .anak-row.selected{border-color:#7B1E5A;background:#FFF9FB;}
        .status-opt{transition:border-color .15s,background .15s;} .status-opt.selected{border-color:#7B1E5A;background:#FFF9FB;}
        input[type="date"]{appearance:none;-webkit-appearance:none;}
        @keyframes spin2{to{transform:rotate(360deg)}} .spin{animation:spin2 .8s linear infinite;}
        .search-wrap:focus-within{border-color:#7B1E5A!important;}
        @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}} .float-anim{animation:floatEmpty 3s ease-in-out infinite;}
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col">

    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
    </div>

    <!-- HEADER -->
    <div class="header-bg rounded-b-[30px] px-5 pt-10 pb-8 relative shrink-0">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>
        <a href="{{ url('/konsultan/tugaskan-nanny') }}" class="absolute top-[54px] left-5 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center z-10 hover:bg-white/30 transition-colors">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center anim-up d1">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-lg">
                <ion-icon name="create" style="font-size:28px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-wide mb-1">Ubah Penugasan</h1>
            <p class="text-white/60 text-xs font-medium">Perbarui data penugasan nanny</p>
        </div>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-5">

        <!-- Loading skeleton -->
        <div id="loadingSkeleton" class="space-y-5">
            <div class="bg-white rounded-3xl border-2 border-plum-soft p-4 flex items-center gap-3">
                <div class="w-16 h-16 rounded-full bg-plum-soft animate-pulse flex-shrink-0"></div>
                <div class="flex-1 space-y-2"><div class="h-4 bg-plum-soft rounded animate-pulse w-3/4"></div><div class="h-3 bg-plum-soft rounded animate-pulse w-1/2"></div></div>
            </div>
            <div class="bg-white rounded-2xl border-2 border-plum-soft p-4 h-16 animate-pulse"></div>
            <div class="bg-white rounded-2xl border-2 border-plum-soft p-4 h-16 animate-pulse"></div>
            <div class="bg-white rounded-2xl border-2 border-plum-soft p-4 h-16 animate-pulse"></div>
        </div>

        <!-- Error state -->
        <div id="errorState" class="hidden flex flex-col items-center justify-center pt-16 px-8 text-center">
            <div class="float-anim w-24 h-24 rounded-full bg-red-100 flex items-center justify-center mb-5">
                <ion-icon name="alert-circle" style="font-size:48px;color:#DC2626;"></ion-icon>
            </div>
            <h3 class="text-plum-dark font-bold text-lg mb-2">Data tidak ditemukan</h3>
            <p class="text-plum-muted text-sm mb-6">Data penugasan yang Anda cari tidak tersedia</p>
            <a href="{{ url('/konsultan/tugaskan-nanny') }}" class="btn-primary text-white text-sm font-bold px-8 py-3 rounded-2xl">Kembali</a>
        </div>

        <!-- Form (hidden until data loaded) -->
        <div id="formSection" class="hidden space-y-5">

            <!-- NANNY INFO CARD -->
            <div id="nannyCard" class="bg-white rounded-3xl border-2 border-plum-soft p-4 flex items-center gap-3 anim-up d2"></div>

            <!-- PILIH MAJIKAN -->
            <div class="anim-up d2">
                <div class="flex items-center gap-2 mb-2"><ion-icon name="briefcase" style="font-size:16px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-sm">Pilih Majikan</span><span class="text-red-500 font-bold">*</span></div>
                <button type="button" onclick="openMajikanModal()" class="w-full bg-white rounded-2xl border-2 border-plum-soft p-4 flex items-center justify-between hover:border-plum transition-colors text-left">
                    <div id="majikanDisplay"><span class="text-plum-muted text-sm font-medium">Pilih Majikan</span></div>
                    <ion-icon name="chevron-forward" style="font-size:18px;color:#A2397B;flex-shrink:0;"></ion-icon>
                </button>
            </div>

            <!-- PILIH ANAK -->
            <div class="anim-up d3" id="sectionAnak">
                <div class="flex items-center gap-2 mb-2"><ion-icon name="happy" style="font-size:16px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-sm">Pilih Anak</span><span class="text-red-500 font-bold">*</span></div>
                <button type="button" onclick="openAnakModal()" class="w-full bg-white rounded-2xl border-2 border-plum-soft p-4 flex items-center justify-between hover:border-plum transition-colors text-left">
                    <div id="anakDisplay"><span class="text-plum-muted text-sm font-medium">Pilih Anak</span></div>
                    <ion-icon name="chevron-forward" style="font-size:18px;color:#A2397B;flex-shrink:0;"></ion-icon>
                </button>
            </div>

            <!-- STATUS -->
            <div class="anim-up d3">
                <div class="flex items-center gap-2 mb-2"><ion-icon name="flag" style="font-size:16px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-sm">Status Penugasan</span><span class="text-red-500 font-bold">*</span></div>
                <button type="button" onclick="openStatusModal()" class="w-full bg-white rounded-2xl border-2 border-plum-soft p-4 flex items-center justify-between hover:border-plum transition-colors text-left">
                    <div id="statusDisplay"><span class="text-plum-muted text-sm font-medium">Pilih Status</span></div>
                    <ion-icon name="chevron-down" style="font-size:18px;color:#A2397B;flex-shrink:0;"></ion-icon>
                </button>
            </div>

            <!-- PERIODE -->
            <div class="anim-up d4 space-y-3">
                <div class="flex items-center gap-2 mb-2"><ion-icon name="calendar" style="font-size:16px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-sm">Periode Penugasan</span><span class="text-red-500 font-bold">*</span></div>
                <div class="bg-white rounded-2xl border-2 border-plum-soft p-4">
                    <div class="flex items-center gap-1.5 mb-2"><ion-icon name="log-in-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon><span class="text-plum-muted text-xs font-semibold uppercase tracking-wide">Tanggal Mulai</span></div>
                    <input type="date" id="tanggalMulai" class="w-full text-plum-dark font-semibold text-sm bg-transparent border-none outline-none cursor-pointer" onchange="autoSelesai()">
                </div>
                <div class="bg-white rounded-2xl border-2 border-plum-soft p-4">
                    <div class="flex items-center gap-1.5 mb-2"><ion-icon name="log-out-outline" style="font-size:13px;color:#7B1E5A;"></ion-icon><span class="text-plum-muted text-xs font-semibold uppercase tracking-wide">Tanggal Selesai</span></div>
                    <input type="date" id="tanggalSelesai" class="w-full text-plum-dark font-semibold text-sm bg-transparent border-none outline-none cursor-pointer">
                </div>
            </div>

            <!-- CATATAN -->
            <div class="anim-up d5">
                <div class="flex items-center gap-2 mb-2"><ion-icon name="document-text" style="font-size:16px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-sm">Catatan</span><span class="text-plum-muted text-xs italic font-normal">(Opsional)</span></div>
                <div class="bg-white rounded-2xl border-2 border-plum-soft overflow-hidden">
                    <textarea id="catatanInput" rows="4" placeholder="Tambahkan catatan khusus..." class="w-full p-4 text-sm text-plum-dark placeholder-plum-accent bg-transparent resize-none focus:outline-none leading-relaxed"></textarea>
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="pb-2 anim-up d6">
                <button type="button" id="btnSubmit" onclick="openConfirmModal()"
                        class="btn-primary w-full flex items-center justify-center gap-2 text-white font-bold text-sm py-4 rounded-2xl shadow-lg">
                    <ion-icon name="save" style="font-size:18px;"></ion-icon>
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <div class="h-6"></div>
    </div>

    <!-- ── MODAL MAJIKAN ──────────────────────────────────────────────────── -->
    <div id="modalMajikan" class="hidden fixed inset-0 z-50 flex items-end">
        <div class="modal-overlay absolute inset-0" onclick="closeMajikanModal()"></div>
        <div class="sheet-anim relative bg-white w-full rounded-t-3xl max-h-[85vh] flex flex-col shadow-2xl z-10" style="max-width:390px;margin:0 auto;">
            <div class="flex items-center justify-between px-5 py-4 border-b border-plum-soft shrink-0">
                <div class="flex items-center gap-2"><ion-icon name="briefcase" style="font-size:18px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-base">Pilih Majikan</span></div>
                <button onclick="closeMajikanModal()" class="w-8 h-8 rounded-full bg-plum-soft flex items-center justify-center"><ion-icon name="close" style="font-size:16px;color:#7B1E5A;"></ion-icon></button>
            </div>
            <div class="px-4 py-3 border-b border-plum-soft shrink-0">
                <div class="search-wrap flex items-center bg-plum-pale rounded-xl border-2 border-plum-soft px-3 gap-2 transition-all">
                    <ion-icon name="search" style="font-size:16px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                    <input type="text" id="searchMajikan" class="flex-1 py-2.5 text-sm text-plum-dark bg-transparent focus:outline-none placeholder-plum-accent" placeholder="Cari nama atau email..." oninput="filterMajikan(this.value)">
                </div>
            </div>
            <div id="majikanListModal" class="overflow-y-auto flex-1 px-4 py-3 space-y-2"></div>
        </div>
    </div>

    <!-- ── MODAL ANAK ──────────────────────────────────────────────────────── -->
    <div id="modalAnak" class="hidden fixed inset-0 z-50 flex items-end">
        <div class="modal-overlay absolute inset-0" onclick="closeAnakModal()"></div>
        <div class="sheet-anim relative bg-white w-full rounded-t-3xl max-h-[85vh] flex flex-col shadow-2xl z-10" style="max-width:390px;margin:0 auto;">
            <div class="flex items-center justify-between px-5 py-4 border-b border-plum-soft shrink-0">
                <div class="flex items-center gap-2"><ion-icon name="happy" style="font-size:18px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-base">Pilih Anak</span></div>
                <button onclick="closeAnakModal()" class="w-8 h-8 rounded-full bg-plum-soft flex items-center justify-center"><ion-icon name="close" style="font-size:16px;color:#7B1E5A;"></ion-icon></button>
            </div>
            <div id="anakListModal" class="overflow-y-auto flex-1 px-4 py-3 space-y-2"></div>
            <div class="px-4 py-4 border-t border-plum-soft bg-plum-pale/50 shrink-0 flex items-center justify-between">
                <div class="flex items-center gap-1.5"><ion-icon name="checkmark-circle" style="font-size:16px;color:#7B1E5A;"></ion-icon><span class="text-plum text-sm font-semibold" id="anakCountLabel">0 anak terpilih</span></div>
                <button onclick="closeAnakModal()" class="bg-plum text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-plum-light transition-colors">Konfirmasi</button>
            </div>
        </div>
    </div>

    <!-- ── MODAL STATUS ────────────────────────────────────────────────────── -->
    <div id="modalStatus" class="hidden fixed inset-0 z-50 flex items-end">
        <div class="modal-overlay absolute inset-0" onclick="closeStatusModal()"></div>
        <div class="sheet-anim relative bg-white w-full rounded-t-3xl shadow-2xl z-10 p-4" style="max-width:390px;margin:0 auto;">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2"><ion-icon name="flag" style="font-size:18px;color:#7B1E5A;"></ion-icon><span class="text-plum-dark font-bold text-base">Pilih Status</span></div>
                <button onclick="closeStatusModal()" class="w-8 h-8 rounded-full bg-plum-soft flex items-center justify-center"><ion-icon name="close" style="font-size:16px;color:#7B1E5A;"></ion-icon></button>
            </div>
            <div class="space-y-3" id="statusOptions"></div>
        </div>
    </div>

    <!-- ── MODAL KONFIRMASI ────────────────────────────────────────────────── -->
    <div id="modalConfirm" class="hidden fixed inset-0 z-50 flex items-end">
        <div class="modal-overlay absolute inset-0" onclick="closeConfirmModal()"></div>
        <div class="sheet-anim relative bg-white w-full rounded-t-3xl shadow-2xl z-10 p-6 text-center" style="max-width:390px;margin:0 auto;">
            <div class="w-16 h-16 rounded-full bg-plum-soft flex items-center justify-center mx-auto mb-4">
                <ion-icon name="help-circle" style="font-size:36px;color:#7B1E5A;"></ion-icon>
            </div>
            <h3 class="text-plum-dark font-extrabold text-lg mb-2">Konfirmasi Perubahan</h3>
            <p class="text-plum-muted text-sm leading-relaxed mb-6">Apakah Anda yakin ingin menyimpan perubahan data penugasan ini?</p>
            <div class="flex gap-3">
                <button onclick="closeConfirmModal()" class="flex-1 py-3.5 rounded-2xl border-2 border-plum-soft text-plum font-bold text-sm hover:bg-plum-soft transition-colors">Batal</button>
                <button onclick="handleSubmit()" class="flex-1 py-3.5 rounded-2xl btn-primary text-white font-bold text-sm shadow-lg">Ya, Simpan</button>
            </div>
        </div>
    </div>

    <!-- ── MODAL RESULT ────────────────────────────────────────────────────── -->
    <div id="modalResult" class="hidden fixed inset-0 z-50 flex items-end">
        <div class="modal-overlay absolute inset-0"></div>
        <div class="sheet-anim relative bg-white w-full rounded-t-3xl shadow-2xl z-10 p-6 text-center" style="max-width:390px;margin:0 auto;">
            <div id="resultIcon" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"></div>
            <h3 id="resultTitle" class="text-plum-dark font-extrabold text-lg mb-2"></h3>
            <p id="resultMsg" class="text-plum-muted text-sm leading-relaxed mb-6"></p>
            <button id="resultBtn" class="btn-primary w-full text-white font-bold py-3.5 rounded-2xl text-sm"></button>
        </div>
    </div>

    @include('partials.bottom-nav', ['active' => 'home'])
</div>
</div>

<script>
const API_BASE_URL   = '{{ env("API_BASE_URL") }}';
const API_TOKEN = '{{ session("token") }}';

// Parse assignment id from URL: /konsultan/tugaskan-nanny/assignment/{id}/ubah
const parts       = location.pathname.split('/');
const assignmentId= parts[parts.indexOf('assignment')+1];

const STATUS_OPTS = [
    {value:'active',   label:'Aktif Bertugas',      icon:'time',            bg:'bg-blue-100',  text:'text-blue-600'},
    {value:'pending',  label:'Menunggu Konfirmasi', icon:'hourglass',       bg:'bg-yellow-100',text:'text-yellow-700'},
    {value:'completed',label:'Tugas Selesai',       icon:'checkmark-done',  bg:'bg-gray-100',  text:'text-gray-600'},
    {value:'cancelled',label:'Dibatalkan',          icon:'close-circle',    bg:'bg-red-100',   text:'text-red-600'},
];

// ── State ─────────────────────────────────────────────────────────────────
let assignmentData = null;
let allMajikan     = [];
let selectedMajikan= null;
let anakList       = [];
let selectedAnak   = {};
let currentStatus  = 'active';
let nannyId        = null;

// ── Load assignment detail ─────────────────────────────────────────────────
async function loadAssignment(){
    try{
        const fd=new FormData(); fd.append('id_assignment', assignmentId);
        const res =await fetch(`${API_BASE_URL}/nanny-assignment-detail`,{method:'POST',headers:{'Accept':'application/json','Authorization':`Bearer ${API_TOKEN}`},body:fd});
        const json=await res.json();
        if(json.status==='success' && json.assignment){
            assignmentData=json.assignment;
            populateForm(assignmentData);
        } else { showError(); }
    }catch(e){ showError(); }
}

function showError(){
    document.getElementById('loadingSkeleton').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('errorState').classList.add('flex');
}

function populateForm(a){
    document.getElementById('loadingSkeleton').classList.add('hidden');
    document.getElementById('formSection').classList.remove('hidden');

    nannyId = a.id_nanny;

    // Nanny card
    const wrap = document.getElementById('nannyCard');
    const foto  = a.nanny_foto||null;
    const avHtml= foto
        ? `<img src="${foto}" class="w-16 h-16 rounded-full object-cover border-4 border-plum-soft flex-shrink-0">`
        : `<div class="w-16 h-16 rounded-full bg-plum-soft border-4 border-plum-soft flex items-center justify-center flex-shrink-0"><ion-icon name="person" style="font-size:28px;color:#7B1E5A;"></ion-icon></div>`;
    wrap.innerHTML=`${avHtml}<div class="flex-1 min-w-0"><p class="text-plum-dark font-extrabold text-base truncate mb-1">${a.nanny_name||'-'}</p><div class="flex items-center gap-1 mb-0.5"><ion-icon name="mail-outline" style="font-size:12px;color:#A2397B;"></ion-icon><span class="text-plum-muted text-xs font-medium truncate">${a.nanny_email||'-'}</span></div><div class="flex items-center gap-1"><ion-icon name="${(a.nanny_gender||'')==='L'?'male':'female'}" style="font-size:12px;color:#A2397B;"></ion-icon><span class="text-plum-muted text-xs font-medium">${(a.nanny_gender||'')==='L'?'Laki-laki':'Perempuan'}</span></div></div>`;

    // Majikan
    selectedMajikan={id:a.id_majikan, name:a.majikan_name||'-', email:a.majikan_email||''};
    document.getElementById('majikanDisplay').innerHTML=`<div><p class="text-plum-dark font-bold text-sm">${selectedMajikan.name}</p><p class="text-plum-muted text-xs">${selectedMajikan.email}</p></div>`;

    // Dates
    document.getElementById('tanggalMulai').value  = a.tanggal_mulai  || '';
    document.getElementById('tanggalSelesai').value= a.tanggal_selesai|| '';

    // Status
    currentStatus = a.status || 'active';
    renderStatusDisplay();
    renderStatusOptions();

    // Catatan
    document.getElementById('catatanInput').value = a.catatan||'';

    // Pre-select existing anak
    if(a.anak && Array.isArray(a.anak)){
        a.anak.forEach(c=>{ selectedAnak[c.id_anak]={id:c.id_anak, nama:c.anak_name||c.nama||''}; });
        updateAnakDisplay();
        updateAnakCountLabel();
    }

    // Load anak list from API silently
    loadAnak(a.id_majikan);
}

// ── Date helper ───────────────────────────────────────────────────────────
function autoSelesai(){
    const v=document.getElementById('tanggalMulai').value;
    const s=document.getElementById('tanggalSelesai').value;
    if(!v||s>v) return;
    const d=new Date(v); d.setFullYear(d.getFullYear()+1);
    document.getElementById('tanggalSelesai').value=d.toISOString().split('T')[0];
}

// ── Modal Majikan ─────────────────────────────────────────────────────────
function openMajikanModal(){ document.getElementById('modalMajikan').classList.remove('hidden'); loadMajikan(); }
function closeMajikanModal(){ document.getElementById('modalMajikan').classList.add('hidden'); document.getElementById('searchMajikan').value=''; }

async function loadMajikan(){
    const list=document.getElementById('majikanListModal');
    list.innerHTML=`<p class="text-center text-plum-muted text-sm py-8">Memuat...</p>`;
    try{
        const fd=new FormData(); fd.append('search','');
        const res=await fetch(`${API_BASE_URL}/user-majikan`,{method:'POST',headers:{'Accept':'application/json','Authorization':`Bearer ${API_TOKEN}`},body:fd});
        const json=await res.json(); allMajikan=json.data||[]; renderMajikan(allMajikan);
    }catch(e){ list.innerHTML=`<p class="text-center text-red-500 text-sm py-8">Gagal memuat</p>`; }
}
function filterMajikan(q){ renderMajikan(allMajikan.filter(m=>(m.name||'').toLowerCase().includes(q.toLowerCase())||(m.email||'').toLowerCase().includes(q.toLowerCase()))); }
function renderMajikan(items){
    const list=document.getElementById('majikanListModal');
    if(!items.length){list.innerHTML=`<p class="text-center text-plum-muted text-sm py-8">Tidak ditemukan</p>`;return;}
    list.innerHTML=items.map(m=>`
    <button type="button" onclick="selectMajikan(${m.id},'${esc(m.name)}','${esc(m.email||'')}')"
            class="w-full flex items-center gap-3 p-4 rounded-2xl border-2 border-plum-soft bg-white text-left hover:border-plum transition-colors">
        <div class="w-10 h-10 rounded-full bg-plum-soft flex items-center justify-center flex-shrink-0"><ion-icon name="person-circle" style="font-size:22px;color:#7B1E5A;"></ion-icon></div>
        <div class="flex-1 min-w-0"><p class="text-plum-dark font-bold text-sm truncate">${m.name}</p><p class="text-plum-muted text-xs truncate">${m.email||''}</p></div>
        <div class="w-7 h-7 rounded-full bg-plum-soft flex items-center justify-center flex-shrink-0"><ion-icon name="chevron-forward" style="font-size:13px;color:#7B1E5A;"></ion-icon></div>
    </button>`).join('');
}
function selectMajikan(id,name,email){
    selectedMajikan={id,name,email};
    selectedAnak={};
    document.getElementById('majikanDisplay').innerHTML=`<div><p class="text-plum-dark font-bold text-sm">${name}</p><p class="text-plum-muted text-xs">${email}</p></div>`;
    updateAnakDisplay(); updateAnakCountLabel();
    closeMajikanModal();
    loadAnak(id);
}

// ── Modal Anak ────────────────────────────────────────────────────────────
function openAnakModal(){ document.getElementById('modalAnak').classList.remove('hidden'); renderAnak(); }
function closeAnakModal(){ document.getElementById('modalAnak').classList.add('hidden'); }

async function loadAnak(idMajikan){
    try{
        const fd=new FormData(); fd.append('id_majikan',idMajikan);
        const res=await fetch(`${API_BASE_URL}/user-anak-for-konsultan`,{method:'POST',headers:{'Accept':'application/json','Authorization':`Bearer ${API_TOKEN}`},body:fd});
        const json=await res.json(); anakList=json.data||[];
    }catch(e){ anakList=[]; }
}

function renderAnak(){
    const list=document.getElementById('anakListModal');
    if(!anakList.length){list.innerHTML=`<p class="text-center text-plum-muted text-sm py-8">Belum ada data anak</p>`;return;}
    list.innerHTML=anakList.map(a=>{
        const sel=!!selectedAnak[a.id]; const today=new Date(),b=new Date(a.tanggal_lahir);let age=today.getFullYear()-b.getFullYear();if(today.getMonth()-b.getMonth()<0||(today.getMonth()===b.getMonth()&&today.getDate()<b.getDate()))age--;
        return `
        <button type="button" onclick="toggleAnak(${a.id},'${esc(a.nama||'')}')" id="anakRow_${a.id}"
                class="anak-row w-full flex items-center gap-3 p-4 rounded-2xl border-2 text-left ${sel?'selected border-plum bg-plum-pale/60':'border-plum-soft bg-white'}">
            <div id="cb_${a.id}" class="w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 ${sel?'bg-plum border-plum':'border-plum-accent bg-white'}">
                ${sel?'<ion-icon name="checkmark" style="font-size:12px;color:white;"></ion-icon>':''}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-plum-dark font-bold text-sm">${a.nama||'-'}</p>
                <div class="flex gap-3 mt-0.5"><span class="text-plum-muted text-[11px] font-medium">${a.gender==='L'?'Laki-laki':'Perempuan'}</span><span class="text-plum-muted text-[11px] font-medium">${age} tahun</span></div>
            </div>
        </button>`;
    }).join('');
}

function toggleAnak(id,nama){
    if(selectedAnak[id]) delete selectedAnak[id]; else selectedAnak[id]={id,nama};
    const sel=!!selectedAnak[id];
    const row=document.getElementById(`anakRow_${id}`);const cb=document.getElementById(`cb_${id}`);
    row.className=`anak-row w-full flex items-center gap-3 p-4 rounded-2xl border-2 text-left ${sel?'selected border-plum bg-plum-pale/60':'border-plum-soft bg-white'}`;
    cb.className=`w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 ${sel?'bg-plum border-plum':'border-plum-accent bg-white'}`;
    cb.innerHTML=sel?'<ion-icon name="checkmark" style="font-size:12px;color:white;"></ion-icon>':'';
    updateAnakDisplay(); updateAnakCountLabel();
}

function updateAnakDisplay(){
    const count=Object.keys(selectedAnak).length, names=Object.values(selectedAnak).map(a=>a.nama).join(', ');
    document.getElementById('anakDisplay').innerHTML=count>0
        ?`<div><p class="text-plum-dark font-bold text-sm">${count} Anak Terpilih</p><p class="text-plum-muted text-xs truncate">${names}</p></div>`
        :`<span class="text-plum-muted text-sm font-medium">Pilih Anak</span>`;
}
function updateAnakCountLabel(){ document.getElementById('anakCountLabel').textContent=`${Object.keys(selectedAnak).length} anak terpilih`; }

// ── Modal Status ──────────────────────────────────────────────────────────
function openStatusModal(){ document.getElementById('modalStatus').classList.remove('hidden'); }
function closeStatusModal(){ document.getElementById('modalStatus').classList.add('hidden'); }

function renderStatusOptions(){
    document.getElementById('statusOptions').innerHTML=STATUS_OPTS.map(o=>`
    <button type="button" onclick="selectStatus('${o.value}')" id="statusOpt_${o.value}"
            class="status-opt w-full flex items-center justify-between p-4 rounded-2xl border-2 bg-white hover:border-plum transition-colors ${currentStatus===o.value?'selected border-plum bg-plum-pale/60':'border-plum-soft'}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl ${o.bg} flex items-center justify-center">
                <ion-icon name="${o.icon}" class="${o.text}" style="font-size:20px;"></ion-icon>
            </div>
            <span class="${o.text} font-bold text-sm">${o.label}</span>
        </div>
        <div id="statusCheck_${o.value}" class="${currentStatus===o.value?'':'hidden'}">
            <ion-icon name="checkmark-circle" style="font-size:22px;color:#7B1E5A;"></ion-icon>
        </div>
    </button>`).join('');
}

function renderStatusDisplay(){
    const o=STATUS_OPTS.find(s=>s.value===currentStatus)||STATUS_OPTS[0];
    document.getElementById('statusDisplay').innerHTML=`<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold ${o.bg} ${o.text}"><ion-icon name="${o.icon}" style="font-size:14px;"></ion-icon>${o.label}</span>`;
}

function selectStatus(value){
    currentStatus=value;
    renderStatusDisplay();
    renderStatusOptions();
    closeStatusModal();
}

// ── Confirm / Submit ──────────────────────────────────────────────────────
function openConfirmModal(){ document.getElementById('modalConfirm').classList.remove('hidden'); }
function closeConfirmModal(){ document.getElementById('modalConfirm').classList.add('hidden'); }

async function handleSubmit(){
    closeConfirmModal();

    const mulai  =document.getElementById('tanggalMulai').value;
    const selesai=document.getElementById('tanggalSelesai').value;
    const catatan=document.getElementById('catatanInput').value;

    if(!mulai||!selesai){showResult(false,'Data kurang','Isi tanggal mulai dan selesai.');return;}
    if(mulai>=selesai){showResult(false,'Tanggal tidak valid','Tanggal selesai harus setelah tanggal mulai.');return;}
    if(!selectedMajikan){showResult(false,'Data kurang','Pilih majikan terlebih dahulu.');return;}
    if(!Object.keys(selectedAnak).length){showResult(false,'Data kurang','Pilih minimal satu anak.');return;}

    const btn=document.getElementById('btnSubmit');
    btn.innerHTML=`<ion-icon name="sync" style="font-size:18px;" class="spin"></ion-icon><span>Menyimpan...</span>`;
    btn.disabled=true;

    try{
        const fd=new FormData();
        fd.append('id_assignment', assignmentId);
        fd.append('id_nanny',      nannyId);
        fd.append('id_majikan',    selectedMajikan.id);
        Object.keys(selectedAnak).forEach(id=>fd.append('id_anak[]',id));
        fd.append('tanggal_mulai',  mulai);
        fd.append('tanggal_selesai',selesai);
        fd.append('status',         currentStatus);
        fd.append('catatan',        catatan||'Assignment By Konsultan');

        const res =await fetch(`${API_BASE_URL}/nanny-assignment-update`,{method:'POST',headers:{'Accept':'application/json','Authorization':`Bearer ${API_TOKEN}`},body:fd});
        const json=await res.json();

        if(json.status==='success'){
            showResult(true,'Berhasil!','Penugasan berhasil diubah.',()=>{location.href='{{ url("/konsultan/tugaskan-nanny") }}';});
        } else {
            showResult(false,'Gagal',json.message||'Gagal mengubah penugasan.');
            resetBtn();
        }
    }catch(e){ showResult(false,'Kesalahan','Terjadi kesalahan jaringan.'); resetBtn(); }
}

function resetBtn(){
    const btn=document.getElementById('btnSubmit');
    btn.disabled=false;
    btn.innerHTML=`<ion-icon name="save" style="font-size:18px;"></ion-icon><span>Simpan Perubahan</span>`;
}

// ── Result Modal ──────────────────────────────────────────────────────────
function showResult(success,title,msg,onOk=null){
    document.getElementById('resultIcon').className=`w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ${success?'bg-green-100':'bg-red-100'}`;
    document.getElementById('resultIcon').innerHTML=`<ion-icon name="${success?'checkmark-circle':'alert-circle'}" style="font-size:36px;color:${success?'#16A34A':'#DC2626'};"></ion-icon>`;
    document.getElementById('resultTitle').textContent=title;
    document.getElementById('resultMsg').textContent=msg;
    const rbtn=document.getElementById('resultBtn');
    rbtn.textContent=success?'Kembali ke Daftar':'Coba Lagi';
    rbtn.onclick=onOk||(()=>document.getElementById('modalResult').classList.add('hidden'));
    document.getElementById('modalResult').classList.remove('hidden');
}

// ── Helpers ───────────────────────────────────────────────────────────────
function esc(s){return String(s).replace(/'/g,"\\'").replace(/"/g,'&quot;');}
function updateClock(){const now=new Date(),h=String(now.getHours()).padStart(2,'0'),m=String(now.getMinutes()).padStart(2,'0'),el=document.getElementById('statusTime');if(el)el.textContent=`${h}:${m}`;}

updateClock(); setInterval(updateClock,30000);
loadAssignment();
</script>
@include('partials.auth-guard')
</body>
</html>