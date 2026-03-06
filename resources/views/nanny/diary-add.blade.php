{{-- resources/views/nanny/diary-add.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tambah Aktivitas</title>
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
        @keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .anim-up{animation:slideUp .35s ease forwards;}
        .anim-up.d1{animation-delay:.05s;opacity:0;}.anim-up.d2{animation-delay:.10s;opacity:0;}
        .anim-up.d3{animation-delay:.15s;opacity:0;}.anim-up.d4{animation-delay:.20s;opacity:0;}
        .anim-up.d5{animation-delay:.25s;opacity:0;}.anim-up.d6{animation-delay:.30s;opacity:0;}
        /* Section card */
        .sec{background:#fff;border-radius:20px;padding:20px;margin-bottom:16px;border:2px solid #F3E6FA;}
        .sec-title{display:flex;align-items:center;gap:8px;margin-bottom:16px;}
        .sec-title span{font-size:16px;font-weight:700;color:#4A0E35;}
        /* Kategori grid */
        .kat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
        .kat-btn{display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 8px;border-radius:14px;border:2px solid #F3E6FA;background:#fff;cursor:pointer;transition:border-color .15s,background .15s,transform .12s;}
        .kat-btn:active{transform:scale(0.94);}
        .kat-btn.sel{border-color:var(--kc);background:color-mix(in srgb,var(--kc) 12%,#fff);}
        .kat-btn span{font-size:12px;font-weight:600;color:#B895C8;}
        .kat-btn.sel span{font-weight:700;}
        /* Time row */
        .time-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
        .time-lbl{display:flex;align-items:center;gap:6px;font-size:14px;font-weight:600;color:#4A0E35;}
        .time-btn{display:flex;align-items:center;gap:8px;background:#FFF9FB;border:2px solid #F3E6FA;border-radius:12px;padding:12px 16px;cursor:pointer;transition:border-color .15s;}
        .time-btn:hover{border-color:#B895C8;}
        .time-btn span{font-size:16px;font-weight:600;color:#4A0E35;}
        /* Duration bar */
        .dur-bar{display:flex;align-items:center;justify-content:center;gap:8px;background:#F3E6FA;border-radius:12px;padding:12px;margin-top:8px;}
        .dur-bar span{font-size:14px;color:#7B1E5A;font-weight:600;}
        .dur-bar b{font-size:14px;color:#4A0E35;font-weight:700;}
        /* Mood */
        .mood-row{display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;}
        .mood-row::-webkit-scrollbar{display:none;}
        .mood-btn{display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 18px;border-radius:14px;border:2px solid #F3E6FA;background:#fff;cursor:pointer;flex-shrink:0;transition:border-color .15s,background .15s,transform .12s;}
        .mood-btn:active{transform:scale(0.94);}
        .mood-btn.sel{border-color:#7B1E5A;background:#F3E6FA;}
        .mood-btn p{font-size:12px;font-weight:600;color:#B895C8;}
        .mood-btn.sel p{color:#7B1E5A;font-weight:700;}
        /* Textarea */
        .desk-input{width:100%;background:#FFF9FB;border:2px solid #F3E6FA;border-radius:12px;padding:14px;font-size:14px;color:#4A0E35;font-family:'Plus Jakarta Sans',sans-serif;resize:none;outline:none;transition:border-color .15s;}
        .desk-input:focus{border-color:#B895C8;}
        .desk-input::placeholder{color:#B895C8;}
        /* Foto */
        .foto-btns{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;}
        .foto-btn{display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;border-radius:12px;border:2px solid #F3E6FA;background:#fff;cursor:pointer;font-size:14px;font-weight:600;color:#7B1E5A;transition:background .15s,border-color .15s;}
        .foto-btn:hover{background:#F3E6FA;border-color:#B895C8;}
        .foto-btn:active{transform:scale(0.96);}
        /* Preview */
        .foto-preview{position:relative;border-radius:16px;overflow:hidden;border:2px solid #F3E6FA;}
        .foto-preview img{width:100%;height:200px;object-fit:cover;display:block;}
        .foto-remove{position:absolute;top:8px;right:8px;width:30px;height:30px;border-radius:15px;background:#FF6B6B;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;}
        .foto-name{font-size:12px;color:#A2397B;text-align:center;font-weight:500;margin-top:8px;}
        /* Footer */
        .footer-bar{background:#fff;border-top:2px solid #F3E6FA;padding:16px 20px;flex-shrink:0;}
        .submit-btn{width:100%;display:flex;align-items:center;justify-content:center;gap:8px;background:#7B1E5A;color:#fff;border:none;border-radius:16px;padding:16px;font-size:16px;font-weight:700;letter-spacing:.5px;cursor:pointer;transition:opacity .15s,transform .12s;}
        .submit-btn:active{transform:scale(0.98);}
        .submit-btn:disabled{background:#B895C8;opacity:.6;cursor:not-allowed;}
        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;padding:20px;z-index:50;opacity:0;pointer-events:none;transition:opacity .2s ease;}
        .modal-overlay.open{opacity:1;pointer-events:auto;}
        .modal-box{background:#fff;border-radius:24px;padding:24px;width:100%;max-width:320px;transform:translateY(20px);transition:transform .25s ease;}
        .modal-overlay.open .modal-box{transform:translateY(0);}
        .no-scrollbar::-webkit-scrollbar{display:none;}.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        /* Alert */
        .alert-bar{margin:0 0 12px;padding:12px 16px;border-radius:12px;font-size:13px;font-weight:600;display:none;}
        .alert-bar.err{display:flex;background:#FEE2E2;color:#B91C1C;gap:8px;align-items:center;}
        .alert-bar.ok{display:flex;background:#DCFCE7;color:#166534;gap:8px;align-items:center;}
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col" style="max-height:100vh;">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum flex-shrink-0">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <svg class="w-4 h-3" viewBox="0 0 16 12" fill="white" opacity="0.8"><path d="M8 2.4C5.6 2.4 3.4 3.4 1.8 5L0 3.2C2.2 1.2 5 0 8 0s5.8 1.2 8 3.2L14.2 5C12.6 3.4 10.4 2.4 8 2.4z"/><path d="M8 6c-1.4 0-2.6.6-3.6 1.4L2.6 5.6C4 4.4 5.8 3.6 8 3.6s4 .8 5.4 2L11.6 7.4C10.6 6.6 9.4 6 8 6z"/><circle cx="8" cy="10" r="2"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="header-bg relative flex-shrink-0 overflow-hidden"
         style="padding:50px 20px 24px;border-bottom-left-radius:24px;border-bottom-right-radius:24px;margin-bottom:16px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>
        <a href="{{ route('nanny-diary', ['id_anak' => $idAnak]) }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px;left:20px;width:40px;height:40px;z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px;color:#fff;"></ion-icon>
        </a>
        <div class="flex flex-col items-center anim-up d1">
            <div class="flex items-center justify-center bg-white rounded-full mb-4 shadow-lg" style="width:64px;height:64px;">
                <ion-icon name="add-circle" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-bold text-white mb-1" style="font-size:24px;letter-spacing:0.5px;">Tambah Aktivitas</h1>
            <p style="font-size:14px;color:#F3E6FA;font-weight:500;">Catat kegiatan anak hari ini</p>
        </div>
    </div>

    <!-- SCROLLABLE CONTENT -->
    <div class="flex-1 overflow-y-auto no-scrollbar" style="padding:0 20px;">

        <!-- Alert bar -->
        <div id="alertBar" class="alert-bar"></div>

        <!-- ── KATEGORI ──────────────────────────────────────────────────── -->
        <div class="sec anim-up d2">
            <div class="sec-title">
                <ion-icon name="grid-outline" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Kategori Aktivitas</span>
                <span style="color:#FF6B6B;font-size:16px;font-weight:700;">*</span>
            </div>
            <div class="kat-grid">
                @php
                    $katOptions = [
                        ['value'=>'makan',   'label'=>'Makan',   'icon'=>'restaurant',  'color'=>'#FF6B6B'],
                        ['value'=>'tidur',   'label'=>'Tidur',   'icon'=>'bed',          'color'=>'#4ECDC4'],
                        ['value'=>'main',    'label'=>'Main',    'icon'=>'game-controller','color'=>'#FFD93D'],
                        ['value'=>'belajar', 'label'=>'Belajar', 'icon'=>'school',       'color'=>'#6BCB77'],
                        ['value'=>'mandi',   'label'=>'Mandi',   'icon'=>'water',        'color'=>'#95B8D1'],
                    ];
                @endphp
                @foreach($katOptions as $k)
                <button type="button"
                        class="kat-btn"
                        data-kat="{{ $k['value'] }}"
                        data-color="{{ $k['color'] }}"
                        style="--kc:{{ $k['color'] }};"
                        onclick="selectKat(this)">
                    <ion-icon name="{{ $k['icon'] }}" style="font-size:26px;color:#B895C8;" id="katIco-{{ $k['value'] }}"></ion-icon>
                    <span id="katLbl-{{ $k['value'] }}">{{ $k['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- ── WAKTU ─────────────────────────────────────────────────────── -->
        <div class="sec anim-up d3">
            <div class="sec-title">
                <ion-icon name="time-outline" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Waktu Aktivitas</span>
                <span style="color:#FF6B6B;font-size:16px;font-weight:700;">*</span>
            </div>
            <!-- Mulai -->
            <div class="time-row">
                <div class="time-lbl">
                    <ion-icon name="play" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    Mulai
                </div>
                <button type="button" class="time-btn" onclick="openTimePicker('mulai')">
                    <span id="displayMulai">--:--</span>
                    <ion-icon name="time-outline" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                </button>
            </div>
            <!-- Selesai -->
            <div class="time-row">
                <div class="time-lbl">
                    <ion-icon name="stop" style="font-size:16px;color:#7B1E5A;"></ion-icon>
                    Selesai
                </div>
                <button type="button" class="time-btn" onclick="openTimePicker('selesai')">
                    <span id="displaySelesai">--:--</span>
                    <ion-icon name="time-outline" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                </button>
            </div>
            <!-- Durasi -->
            <div class="dur-bar">
                <ion-icon name="hourglass-outline" style="font-size:18px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Durasi:</span>
                <b id="durasiDisplay">—</b>
            </div>
        </div>

        <!-- ── MOOD ──────────────────────────────────────────────────────── -->
        <div class="sec anim-up d4">
            <div class="sec-title">
                <ion-icon name="happy-outline" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Mood Anak</span>
            </div>
            <div class="mood-row">
                @php
                    $moods = [
                        ['value'=>'senang','label'=>'Senang','emoji'=>'😊'],
                        ['value'=>'sedih', 'label'=>'Sedih', 'emoji'=>'😢'],
                        ['value'=>'marah', 'label'=>'Marah', 'emoji'=>'😠'],
                        ['value'=>'biasa', 'label'=>'Biasa', 'emoji'=>'😐'],
                    ];
                @endphp
                @foreach($moods as $m)
                <button type="button"
                        class="mood-btn {{ $m['value']==='biasa'?'sel':'' }}"
                        data-mood="{{ $m['value'] }}"
                        onclick="selectMood(this)">
                    <span style="font-size:26px;">{{ $m['emoji'] }}</span>
                    <p>{{ $m['label'] }}</p>
                </button>
                @endforeach
            </div>
        </div>

        <!-- ── DESKRIPSI ─────────────────────────────────────────────────── -->
        <div class="sec anim-up d5">
            <div class="sec-title">
                <ion-icon name="document-text-outline" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Deskripsi</span>
            </div>
            <textarea id="deskripsi" class="desk-input" rows="4"
                      placeholder="Tuliskan detail aktivitas yang dilakukan..."></textarea>
        </div>

        <!-- ── FOTO ──────────────────────────────────────────────────────── -->
        <div class="sec anim-up d6">
            <div class="sec-title">
                <ion-icon name="camera-outline" style="font-size:20px;color:#7B1E5A;flex-shrink:0;"></ion-icon>
                <span>Foto</span>
                <span style="font-size:12px;color:#A2397B;font-weight:500;margin-left:2px;">(Opsional)</span>
            </div>
            <div class="foto-btns">
                <label class="foto-btn" for="inputFoto">
                    <ion-icon name="images-outline" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                    Galeri
                </label>
                <button type="button" class="foto-btn" onclick="capturePhoto()">
                    <ion-icon name="camera-outline" style="font-size:20px;color:#7B1E5A;"></ion-icon>
                    Kamera
                </button>
            </div>
            <input type="file" id="inputFoto" accept="image/*" class="hidden" onchange="previewFoto(this)">
            <input type="file" id="inputCamera" accept="image/*" capture="environment" class="hidden" onchange="previewFoto(this)">
            <div id="fotoPreviewWrap" style="display:none;">
                <div class="foto-preview">
                    <img id="fotoPreviewImg" src="" alt="Preview">
                    <button type="button" class="foto-remove" onclick="removeFoto()">
                        <ion-icon name="close" style="font-size:16px;color:#fff;"></ion-icon>
                    </button>
                </div>
                <p class="foto-name" id="fotoName"></p>
            </div>
        </div>

        <div style="height:20px;"></div>
    </div>

    <!-- FOOTER SUBMIT -->
    <div class="footer-bar">
        <button id="submitBtn" class="submit-btn" onclick="handleSubmit()" disabled>
            <ion-icon name="save-outline" style="font-size:22px;color:#fff;"></ion-icon>
            Simpan Aktivitas
        </button>
    </div>

</div>
</div>

<!-- MODAL TIME PICKER -->
<div class="modal-overlay" id="modalTime">
    <div class="modal-box">
        <div class="flex items-center justify-between" style="margin-bottom:24px;">
            <span style="font-size:18px;font-weight:700;color:#4A0E35;" id="modalTimeTitle">Pilih Waktu</span>
            <button onclick="closeTimePicker()" style="width:32px;height:32px;border-radius:16px;background:#F3E6FA;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;">
                <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div class="flex items-end justify-center" style="gap:8px;margin-bottom:24px;">
            <div class="flex flex-col items-center">
                <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:8px;">Jam</p>
                <input type="number" id="inputJam" min="0" max="23"
                       style="width:72px;background:#FFF9FB;border:2px solid #F3E6FA;border-radius:12px;padding:12px;font-size:20px;font-weight:700;color:#4A0E35;text-align:center;outline:none;font-family:'Plus Jakarta Sans',sans-serif;"
                       oninput="clampTime(this,0,23);updateDurasi()">
            </div>
            <span style="font-size:26px;font-weight:700;color:#4A0E35;padding-bottom:10px;">:</span>
            <div class="flex flex-col items-center">
                <p style="font-size:12px;color:#A2397B;font-weight:600;margin-bottom:8px;">Menit</p>
                <input type="number" id="inputMenit" min="0" max="59"
                       style="width:72px;background:#FFF9FB;border:2px solid #F3E6FA;border-radius:12px;padding:12px;font-size:20px;font-weight:700;color:#4A0E35;text-align:center;outline:none;font-family:'Plus Jakarta Sans',sans-serif;"
                       oninput="clampTime(this,0,59);updateDurasi()">
            </div>
        </div>
        <div class="flex" style="gap:10px;">
            <button onclick="closeTimePicker()"
                    style="flex:1;padding:14px;border-radius:12px;background:#F3E6FA;font-size:16px;font-weight:600;color:#7B1E5A;cursor:pointer;border:none;">
                Batal
            </button>
            <button onclick="confirmTime()"
                    style="flex:1;padding:14px;border-radius:12px;background:#7B1E5A;font-size:16px;font-weight:700;color:#fff;cursor:pointer;border:none;letter-spacing:.3px;">
                Pilih
            </button>
        </div>
    </div>
</div>

<script>
// ── Constants ──────────────────────────────────────────────────────────────────
const ID_ANAK      = {{ $idAnak ?? 'null' }};
const ID_ASSIGNMENT= {{ $idAssignment ?? 'null' }};
const SUBMIT_URL   = "{{ route('nanny-diary-store') }}";
const CSRF         = "{{ csrf_token() }}";
const KAT_COLORS   = {makan:'#FF6B6B',tidur:'#4ECDC4',main:'#FFD93D',belajar:'#6BCB77',mandi:'#95B8D1'};

// ── State ─────────────────────────────────────────────────────────────────────
let selKat    = '';
let selMood   = 'biasa';
let jamMulai  = '';
let jamSelesai= '';
let fotoFile  = null;
let pickTarget= 'mulai'; // which time we're editing

// ── Clock ─────────────────────────────────────────────────────────────────────
function pad(n){return String(n).padStart(2,'0');}
function updateClock(){const n=new Date(),e=document.getElementById('statusTime');if(e)e.textContent=pad(n.getHours())+':'+pad(n.getMinutes());}
updateClock();setInterval(updateClock,30000);

// ── Default times on load ─────────────────────────────────────────────────────
(function(){
    const now=new Date();
    jamMulai  =pad(now.getHours())+':'+pad(now.getMinutes());
    const end =new Date(now.getTime()+30*60000);
    jamSelesai=pad(end.getHours())+':'+pad(end.getMinutes());
    document.getElementById('displayMulai').textContent  =fmtDisplay(jamMulai);
    document.getElementById('displaySelesai').textContent=fmtDisplay(jamSelesai);
    updateDurasi();
})();

function fmtDisplay(t){
    if(!t||t==='--:--')return'--:--';
    const [h,m]=t.split(':');
    const hr=parseInt(h);
    return `${hr%12||12}:${m} ${hr>=12?'PM':'AM'}`;
}

// ── Kategori ──────────────────────────────────────────────────────────────────
function selectKat(btn){
    document.querySelectorAll('.kat-btn').forEach(b=>{
        const kat=b.dataset.kat;
        const col=b.dataset.color;
        b.classList.remove('sel');
        const ico=document.getElementById('katIco-'+kat);
        const lbl=document.getElementById('katLbl-'+kat);
        if(ico)ico.style.color='#B895C8';
        if(lbl)lbl.style.color='#B895C8';
    });
    btn.classList.add('sel');
    selKat=btn.dataset.kat;
    const col=KAT_COLORS[selKat]||'#7B1E5A';
    const ico=document.getElementById('katIco-'+selKat);
    const lbl=document.getElementById('katLbl-'+selKat);
    if(ico)ico.style.color=col;
    if(lbl){lbl.style.color=col;}
    checkReady();
}

// ── Mood ─────────────────────────────────────────────────────────────────────
function selectMood(btn){
    document.querySelectorAll('.mood-btn').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');
    selMood=btn.dataset.mood;
}

// ── Time Picker ───────────────────────────────────────────────────────────────
function openTimePicker(target){
    pickTarget=target;
    const t=target==='mulai'?jamMulai:jamSelesai;
    const [h,m]=t.split(':');
    document.getElementById('inputJam').value=parseInt(h)||0;
    document.getElementById('inputMenit').value=parseInt(m)||0;
    document.getElementById('modalTimeTitle').textContent='Pilih Waktu '+(target==='mulai'?'Mulai':'Selesai');
    document.getElementById('modalTime').classList.add('open');
}
function closeTimePicker(){document.getElementById('modalTime').classList.remove('open');}
function clampTime(el,mn,mx){
    let v=parseInt(el.value)||0;
    if(v<mn)v=mn;if(v>mx)v=mx;
    el.value=v;
}
function confirmTime(){
    const h=pad(parseInt(document.getElementById('inputJam').value)||0);
    const m=pad(parseInt(document.getElementById('inputMenit').value)||0);
    const t=`${h}:${m}`;
    if(pickTarget==='mulai'){
        jamMulai=t;
        document.getElementById('displayMulai').textContent=fmtDisplay(t);
    } else {
        jamSelesai=t;
        document.getElementById('displaySelesai').textContent=fmtDisplay(t);
    }
    updateDurasi();
    closeTimePicker();
    checkReady();
}
document.getElementById('modalTime').addEventListener('click',e=>{if(e.target.id==='modalTime')closeTimePicker();});

function updateDurasi(){
    if(!jamMulai||!jamSelesai){document.getElementById('durasiDisplay').textContent='—';return;}
    const [h1,m1]=jamMulai.split(':').map(Number);
    const [h2,m2]=jamSelesai.split(':').map(Number);
    const total=(h2*60+m2)-(h1*60+m1);
    if(total<=0){document.getElementById('durasiDisplay').textContent='—';return;}
    const jam=Math.floor(total/60),mnt=total%60;
    document.getElementById('durasiDisplay').textContent=(jam>0?jam+' jam ':'')+mnt+' menit';
}

// ── Foto ─────────────────────────────────────────────────────────────────────
function previewFoto(input){
    const file=input.files[0];
    if(!file)return;
    fotoFile=file;
    const reader=new FileReader();
    reader.onload=e=>{
        document.getElementById('fotoPreviewImg').src=e.target.result;
        document.getElementById('fotoName').textContent=file.name;
        document.getElementById('fotoPreviewWrap').style.display='block';
    };
    reader.readAsDataURL(file);
}
function capturePhoto(){document.getElementById('inputCamera').click();}
function removeFoto(){
    fotoFile=null;
    document.getElementById('fotoPreviewWrap').style.display='none';
    document.getElementById('inputFoto').value='';
    document.getElementById('inputCamera').value='';
}

// ── Validation ────────────────────────────────────────────────────────────────
function checkReady(){
    const ok=selKat&&jamMulai&&jamSelesai;
    document.getElementById('submitBtn').disabled=!ok;
}

function showAlert(msg,type='err'){
    const el=document.getElementById('alertBar');
    el.className='alert-bar '+(type==='ok'?'ok':'err');
    el.innerHTML=`<ion-icon name="${type==='ok'?'checkmark-circle':'alert-circle'}-outline" style="font-size:18px;flex-shrink:0;"></ion-icon> ${msg}`;
    el.style.display='flex';
    setTimeout(()=>{el.style.display='none';},4000);
}

// ── Submit ────────────────────────────────────────────────────────────────────
async function handleSubmit(){
    if(!selKat)        {showAlert('Pilih kategori aktivitas.');return;}
    if(!jamMulai||!jamSelesai){showAlert('Isi waktu mulai dan selesai.');return;}

    const [h1,m1]=jamMulai.split(':').map(Number);
    const [h2,m2]=jamSelesai.split(':').map(Number);
    if((h2*60+m2)-(h1*60+m1)<=0){showAlert('Waktu selesai harus setelah waktu mulai.');return;}

    if(!ID_ANAK||!ID_ASSIGNMENT){showAlert('Data tidak lengkap, kembali dan coba lagi.');return;}

    const today=new Date();
    const ymd=today.getFullYear()+'-'+pad(today.getMonth()+1)+'-'+pad(today.getDate());
    const jamMulaiDT=`${ymd} ${jamMulai}:00`;
    const jamSelesaiDT=`${ymd} ${jamSelesai}:00`;

    const btn=document.getElementById('submitBtn');
    btn.disabled=true;
    btn.innerHTML='<ion-icon name="sync-outline" style="font-size:22px;color:#fff;animation:spin 1s linear infinite;"></ion-icon> Menyimpan...';

    const fd=new FormData();
    fd.append('_token',    CSRF);
    fd.append('id_assignment', ID_ASSIGNMENT);
    fd.append('id_anak',       ID_ANAK);
    fd.append('kategori',      selKat);
    fd.append('deskripsi',     document.getElementById('deskripsi').value);
    fd.append('jam_mulai',     jamMulaiDT);
    fd.append('jam_selesai',   jamSelesaiDT);
    fd.append('mood',          selMood);
    if(fotoFile) fd.append('foto', fotoFile);

    try{
        const res=await fetch(SUBMIT_URL,{method:'POST',body:fd});
        const data=await res.json();
        if(data.status==='success'||data.success){
            showAlert('Aktivitas berhasil ditambahkan!','ok');
            setTimeout(()=>{
                window.location.href='{{ route("nanny-diary", ["id_anak"=>$idAnak??0]) }}';
            },1200);
        } else {
            showAlert(data.message||'Gagal menyimpan aktivitas.');
            btn.disabled=false;
            btn.innerHTML='<ion-icon name="save-outline" style="font-size:22px;color:#fff;"></ion-icon> Simpan Aktivitas';
        }
    }catch(err){
        showAlert('Terjadi kesalahan koneksi.');
        btn.disabled=false;
        btn.innerHTML='<ion-icon name="save-outline" style="font-size:22px;color:#fff;"></ion-icon> Simpan Aktivitas';
    }
}
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@include('partials.auth-guard')
</body>
</html>