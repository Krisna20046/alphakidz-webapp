{{-- resources/views/nanny/diary-add.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Create Diary</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { font-family: 'Nunito', sans-serif; background: #E5E2F5; margin: 0; }

        /* ── Phone frame ── */
        @media (min-width: 640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0 60px; background:#E5E2F5; }
            .phone-frame  { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(124,58,237,.28),0 0 0 8px #1a1030,0 0 0 10px #2d1a50; overflow:hidden; position:relative; }
        }
        @media (max-width: 639px) {
            .phone-wrapper { min-height:100vh; }
            .phone-frame  { min-height:100vh; }
        }

        /* ── Header ── */
        .header-bg {
            background: #8B46D3;
            position: relative;
            padding: 52px 24px 28px;
            flex-shrink: 0;
        }
        .header-bg::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 10% 80%, rgba(255,255,255,0.06) 0%, transparent 40%),
                repeating-linear-gradient(45deg, transparent, transparent 18px, rgba(255,255,255,0.025) 18px, rgba(255,255,255,0.025) 19px);
            pointer-events: none;
        }

        /* ── Content card ── */
        /* .content-scroll {
            flex: 1;
            overflow-y: auto;
            background: linear-gradient(180deg, #F8F7FF 0%, #F0EDFB 100%);
            border-radius: 32px 32px 0 0;
            margin-top: -24px;
            position: relative;
            z-index: 20;
            padding: 28px 20px 24px;
        }
        .content-scroll::-webkit-scrollbar { display: none; }
        .content-scroll { -ms-overflow-style: none; scrollbar-width: none; } */

        /* ── Section labels ── */
        .sec-label {
            font-size: 13px; font-weight: 900; color: #1E1B2E;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 14px;
        }

        /* ── Category grid (horizontal 5 items) ── */
        .kat-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 24px;
        }
        .kat-btn {
            display: flex; flex-direction: column; align-items: center; gap: 8px;
            padding: 14px 6px 12px;
            border-radius: 16px;
            border: 2px solid #EDE9FE;
            background: #fff;
            cursor: pointer;
            transition: border-color .15s, background .15s, transform .12s;
        }
        .kat-btn:active { transform: scale(0.94); }
        .kat-btn .kat-ico-wrap {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .kat-btn .kat-lbl {
            font-size: 11px; font-weight: 700; color: #A8A2C2;
            text-align: center; line-height: 1.2;
            transition: color .15s;
        }
        .kat-btn.sel { border-color: #8B46D3; }
        .kat-btn.sel .kat-lbl { color: #8B46D3; }

        /* ── Time row (start + end side by side) ── */
        .time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }
        .time-field-label {
            font-size: 12px; font-weight: 800; color: #5A556E; margin-bottom: 6px;
        }
        .time-field-btn {
            width: 100%;
            display: flex; align-items: center; justify-content: space-between;
            background: #fff;
            border: 1.5px solid #EDE9FE;
            border-radius: 12px;
            padding: 13px 14px;
            cursor: pointer;
            transition: border-color .15s;
            font-family: 'Nunito', sans-serif;
        }
        .time-field-btn:hover { border-color: #8B46D3; }
        .time-field-btn span {
            font-size: 15px; font-weight: 800; color: #1E1B2E;
        }

        /* ── Duration field ── */
        .dur-field {
            display: flex; align-items: center; gap: 10px;
            background: #EDE9FE;
            border-radius: 12px;
            padding: 13px 16px;
            margin-bottom: 24px;
        }
        .dur-field .dur-val {
            font-size: 14px; font-weight: 800; color: #1E1B2E;
        }
        .dur-field .dur-placeholder {
            font-size: 14px; font-weight: 600; color: #A8A2C2;
        }

        /* ── Mood row ── */
        .mood-box {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #EDE9FE;
            padding: 16px;
            display: flex;
            gap: 0;
            justify-content: space-around;
            margin-bottom: 24px;
        }
        .mood-btn {
            display: flex; flex-direction: column; align-items: center; gap: 7px;
            padding: 8px 14px;
            border-radius: 12px;
            border: none; background: transparent;
            cursor: pointer;
            transition: background .15s, transform .12s;
            font-family: 'Nunito', sans-serif;
        }
        .mood-btn:active { transform: scale(0.93); }
        .mood-btn .mood-lbl { font-size: 12px; font-weight: 700; color: #A8A2C2; }
        .mood-btn.sel { background: #EDE9FE; }
        .mood-btn.sel .mood-lbl { color: #8B46D3; }

        /* ── Textarea ── */
        .desk-wrap {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #EDE9FE;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .desk-input {
            width: 100%; padding: 14px 16px;
            font-size: 13px; font-weight: 600; color: #1E1B2E;
            font-family: 'Nunito', sans-serif;
            border: none; outline: none; resize: none;
            background: transparent;
            line-height: 1.6;
        }
        .desk-input::placeholder { color: #C4B5FD; }

        /* ── Photo upload ── */
        .photo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 28px;
        }
        /* Preview slot */
        .photo-slot {
            height: 120px;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            border: 1.5px solid #EDE9FE;
            background: #fff;
        }
        .photo-slot img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .photo-remove {
            position: absolute; top: 6px; right: 6px;
            width: 26px; height: 26px; border-radius: 13px;
            background: rgba(139,70,211,.85); border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        /* Upload slot */
        .upload-slot {
            height: 120px;
            border-radius: 14px;
            border: 2px dashed #C4B5FD;
            background: #F8F7FF;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }
        .upload-slot:hover { border-color: #8B46D3; background: #EDE9FE; }
        .upload-slot span { font-size: 12px; font-weight: 700; color: #A8A2C2; }

        /* ── Alert ── */
        .alert-bar { margin-bottom:12px; padding:12px 16px; border-radius:12px; font-size:13px; font-weight:700; display:none; gap:8px; align-items:center; }
        .alert-bar.err { display:flex; background:#FEE2E2; color:#B91C1C; }
        .alert-bar.ok  { display:flex; background:#DCFCE7; color:#166534; }

        /* ── Footer ── */
        .footer-bar {
            background: linear-gradient(180deg, #F0EDFB 0%, #E5E2F5 100%);
            padding: 14px 20px 28px;
            flex-shrink: 0;
        }
        .submit-btn {
            width: 100%; display:flex; align-items:center; justify-content:center; gap:10px;
            background: #8B46D3;
            color: #fff; border: none; border-radius: 18px;
            padding: 16px;
            font-size: 16px; font-weight: 900; font-family: 'Nunito', sans-serif;
            letter-spacing: .4px; cursor: pointer;
            box-shadow: 0 8px 24px rgba(139,70,211,.4);
            transition: opacity .15s, transform .12s;
        }
        .submit-btn:active { transform: scale(0.98); }
        .submit-btn:disabled { background: #C4B5FD; box-shadow: none; cursor: not-allowed; }

        /* ── Time Modal ── */
        .modal-overlay {
            position:fixed; inset:0; background:rgba(0,0,0,.45);
            display:flex; align-items:center; justify-content:center;
            padding:24px; z-index:60;
            opacity:0; pointer-events:none;
            transition:opacity .22s ease;
        }
        .modal-overlay.open { opacity:1; pointer-events:auto; }
        .modal-box {
            background:#fff; border-radius:22px; padding:24px;
            width:100%; max-width:320px;
            transform:translateY(18px) scale(0.97);
            transition:transform .25s cubic-bezier(.22,1,.36,1);
            box-shadow:0 24px 60px rgba(139,70,211,.22);
        }
        .modal-overlay.open .modal-box { transform:translateY(0) scale(1); }
        .time-input {
            width:72px; background:#F8F7FF; border:2px solid #EDE9FE; border-radius:12px;
            padding:12px; font-size:22px; font-weight:900; color:#1E1B2E;
            text-align:center; outline:none; font-family:'Nunito',sans-serif;
            transition:border-color .15s;
        }
        .time-input:focus { border-color:#8B46D3; }

        /* ── Animations ── */
        @keyframes slideUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim  { animation:slideUp .35s ease forwards; opacity:0; }
        .d1    { animation-delay:.05s; } .d2 { animation-delay:.10s; }
        .d3    { animation-delay:.15s; } .d4 { animation-delay:.20s; }
        .d5    { animation-delay:.25s; } .d6 { animation-delay:.30s; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-[#F0EDFB] flex flex-col" style="max-height:100vh;">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between bg-[#8B46D3] px-6 pt-[14px] pb-1 text-white text-xs font-bold flex-shrink-0">
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

    <!-- HEADER -->
    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('nanny-diary', ['id_anak' => $idAnak, 'id_assignment' => $idAssignment]) }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" style="font-size:18px;color:#fff;"></ion-icon>
            </a>
            <span class="text-white font-extrabold tracking-wide" style="font-size:18px;">Create Diary</span>
        </div>
    </div>

    <!-- SCROLLABLE CONTENT -->
    <div class="flex-1 overflow-y-auto px-[20px] pt-[40px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

        <!-- Alert -->
        <div id="alertBar" class="alert-bar"></div>

        <!-- ── SELECT CATEGORY ── -->
        <div class="anim d2">
            <p class="sec-label">Select Category</p>
            <div class="kat-row">
                @php
                    $katOptions = [
                        ['value'=>'makan',   'label'=>'Eat',        'icon'=>'restaurant',    'bg'=>'#FFF0E6', 'color'=>'#FF9A6C'],
                        ['value'=>'tidur',   'label'=>'Sleep',      'icon'=>'moon',           'bg'=>'#EEF4FF', 'color'=>'#7BB4F0'],
                        ['value'=>'main',    'label'=>'Play',       'icon'=>'car-sport',      'bg'=>'#FFF0F7', 'color'=>'#FF6BA3'],
                        ['value'=>'belajar', 'label'=>'Study',      'icon'=>'book',           'bg'=>'#EEFFF3', 'color'=>'#4CAF7D'],
                        ['value'=>'mandi',   'label'=>'Take A Bath','icon'=>'water',          'bg'=>'#EEF7FF', 'color'=>'#7BB4F0'],
                    ];
                @endphp
                @foreach($katOptions as $k)
                <button type="button"
                        class="kat-btn"
                        data-kat="{{ $k['value'] }}"
                        data-bg="{{ $k['bg'] }}"
                        data-color="{{ $k['color'] }}"
                        onclick="selectKat(this)">
                    <div class="kat-ico-wrap" id="katIcoWrap-{{ $k['value'] }}" style="background:{{ $k['bg'] }};">
                        <ion-icon name="{{ $k['icon'] }}" style="font-size:22px;color:{{ $k['color'] }};" id="katIco-{{ $k['value'] }}"></ion-icon>
                    </div>
                    <span class="kat-lbl" id="katLbl-{{ $k['value'] }}">{{ $k['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- ── START TIME / END TIME ── -->
        <div class="anim d3">
            <div class="time-grid">
                <div>
                    <p class="time-field-label">Start Time</p>
                    <button type="button" class="time-field-btn" onclick="openTimePicker('mulai')">
                        <span id="displayMulai">--:--</span>
                        <ion-icon name="time-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                    </button>
                </div>
                <div>
                    <p class="time-field-label">End Time</p>
                    <button type="button" class="time-field-btn" onclick="openTimePicker('selesai')">
                        <span id="displaySelesai">--:--</span>
                        <ion-icon name="time-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                    </button>
                </div>
            </div>

            <!-- Total Duration -->
            <p class="time-field-label" style="margin-bottom:8px;">Total Duration</p>
            <div class="dur-field" style="margin-bottom:24px;">
                <ion-icon name="hourglass-outline" style="font-size:18px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                <span id="durasiDisplay" class="dur-placeholder">—</span>
            </div>
        </div>

        <!-- ── CHILD'S MOOD ── -->
        <div class="anim d4">
            <p class="sec-label">Child's Mood</p>
            <div class="mood-box">
                @php
                    $moods = [
                        ['value'=>'senang','label'=>'Happy','emoji'=>'😊'],
                        ['value'=>'sedih', 'label'=>'Sad',  'emoji'=>'😢'],
                        ['value'=>'marah', 'label'=>'Angry','emoji'=>'😠'],
                        ['value'=>'biasa', 'label'=>'Flat', 'emoji'=>'😐'],
                    ];
                @endphp
                @foreach($moods as $m)
                <button type="button"
                        class="mood-btn {{ $m['value']==='biasa'?'sel':'' }}"
                        data-mood="{{ $m['value'] }}"
                        onclick="selectMood(this)">
                    <span style="font-size:30px;line-height:1;">{{ $m['emoji'] }}</span>
                    <span class="mood-lbl">{{ $m['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- ── ACTIVITY DESCRIPTION ── -->
        <div class="anim d5">
            <p class="sec-label">Activity Description</p>
            <div class="desk-wrap">
                <textarea id="deskripsi" class="desk-input" rows="4"
                          placeholder="Write down the details of your child's activities today...."></textarea>
            </div>
        </div>

        <!-- ── UPLOAD CHILD PHOTOS ── -->
        <div class="anim d6">
            <p class="sec-label">Upload Child Photos</p>
            <div class="photo-grid">
                <!-- Preview slot (hidden until photo selected) -->
                <div id="fotoPreviewSlot" class="photo-slot" style="display:none;">
                    <img id="fotoPreviewImg" src="" alt="Preview">
                    <button type="button" class="photo-remove" onclick="removeFoto()">
                        <ion-icon name="close" style="font-size:14px;color:#fff;"></ion-icon>
                    </button>
                </div>

                <!-- Upload slot -->
                <label id="uploadSlot" class="upload-slot" for="inputFoto">
                    <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                        <ion-icon name="camera-outline" style="font-size:20px;color:#8B46D3;"></ion-icon>
                    </div>
                    <span>Upload</span>
                </label>

                <!-- Kamera slot (hanya muncul jika belum ada foto dan kita mau kasih opsi) -->
                <button type="button" id="cameraSlot" class="upload-slot" onclick="capturePhoto()" style="display:none;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                        <ion-icon name="camera" style="font-size:20px;color:#8B46D3;"></ion-icon>
                    </div>
                    <span>Camera</span>
                </button>
            </div>
            <input type="file" id="inputFoto" accept="image/*" class="hidden" onchange="previewFoto(this)">
            <input type="file" id="inputCamera" accept="image/*" capture="environment" class="hidden" onchange="previewFoto(this)">
        </div>

    </div><!-- end content-scroll -->

    <!-- FOOTER -->
    <div class="footer-bar">
        <button id="submitBtn" class="submit-btn" onclick="handleSubmit()" disabled>
            <ion-icon name="save-outline" style="font-size:22px;color:#fff;"></ion-icon>
            Save Diary
        </button>
    </div>

</div>
</div>

<!-- ── MODAL: Time Picker ── -->
<div class="modal-overlay" id="modalTime">
    <div class="modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <span style="font-size:17px;font-weight:900;color:#1E1B2E;" id="modalTimeTitle">Pilih Waktu</span>
            <button onclick="closeTimePicker()"
                style="width:32px;height:32px;border-radius:16px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;">
                <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div style="display:flex;align-items:flex-end;justify-content:center;gap:10px;margin-bottom:24px;">
            <div style="display:flex;flex-direction:column;align-items:center;">
                <p style="font-size:12px;color:#A8A2C2;font-weight:800;margin-bottom:8px;">Jam</p>
                <input type="number" id="inputJam" min="0" max="23" class="time-input"
                       oninput="clampTime(this,0,23);updateDurasi()">
            </div>
            <span style="font-size:28px;font-weight:900;color:#1E1B2E;padding-bottom:10px;">:</span>
            <div style="display:flex;flex-direction:column;align-items:center;">
                <p style="font-size:12px;color:#A8A2C2;font-weight:800;margin-bottom:8px;">Menit</p>
                <input type="number" id="inputMenit" min="0" max="59" class="time-input"
                       oninput="clampTime(this,0,59);updateDurasi()">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <button onclick="closeTimePicker()"
                style="padding:13px;border-radius:13px;border:2px solid #FFCACA;background:#FFF9F9;
                       font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#FF0000;cursor:pointer;">
                Batal
            </button>
            <button onclick="confirmTime()"
                style="padding:13px;border-radius:13px;border:none;background:#8B46D3;
                       font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#fff;cursor:pointer;
                       box-shadow:0 4px 12px rgba(139,70,211,.3);">
                Pilih
            </button>
        </div>
    </div>
</div>

<script>
const ID_ANAK       = {{ $idAnak ?? 'null' }};
const ID_ASSIGNMENT = {{ $idAssignment ?? 'null' }};
const SUBMIT_URL    = "{{ route('nanny-diary-store') }}";
const CSRF          = "{{ csrf_token() }}";

let selKat     = '';
let selMood    = 'biasa';
let jamMulai   = '';
let jamSelesai = '';
let fotoFile   = null;
let pickTarget = 'mulai';

// ── Clock ──
function pad(n){ return String(n).padStart(2,'0'); }
function updateClock(){
    const n=new Date(),e=document.getElementById('statusTime');
    if(e) e.textContent=pad(n.getHours())+':'+pad(n.getMinutes());
}
updateClock(); setInterval(updateClock,30000);

// ── Default times ──
(function(){
    const now=new Date();
    jamMulai=pad(now.getHours())+':'+pad(now.getMinutes());
    const end=new Date(now.getTime()+30*60000);
    jamSelesai=pad(end.getHours())+':'+pad(end.getMinutes());
    document.getElementById('displayMulai').textContent=jamMulai;
    document.getElementById('displaySelesai').textContent=jamSelesai;
    updateDurasi();
})();

// ── Category ──
function selectKat(btn){
    document.querySelectorAll('.kat-btn').forEach(b=>{
        b.classList.remove('sel');
    });
    btn.classList.add('sel');
    selKat = btn.dataset.kat;
    checkReady();
}

// ── Mood ──
function selectMood(btn){
    document.querySelectorAll('.mood-btn').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');
    selMood = btn.dataset.mood;
}

// ── Time Picker ──
function openTimePicker(target){
    pickTarget = target;
    const t = target==='mulai' ? jamMulai : jamSelesai;
    const [h,m] = t.split(':');
    document.getElementById('inputJam').value   = parseInt(h)||0;
    document.getElementById('inputMenit').value = parseInt(m)||0;
    document.getElementById('modalTimeTitle').textContent =
        'Pilih Waktu ' + (target==='mulai' ? 'Mulai' : 'Selesai');
    document.getElementById('modalTime').classList.add('open');
}
function closeTimePicker(){ document.getElementById('modalTime').classList.remove('open'); }
function clampTime(el,mn,mx){
    let v=parseInt(el.value)||0;
    if(v<mn)v=mn; if(v>mx)v=mx;
    el.value=v;
}
function confirmTime(){
    const h = pad(parseInt(document.getElementById('inputJam').value)||0);
    const m = pad(parseInt(document.getElementById('inputMenit').value)||0);
    const t = `${h}:${m}`;
    if(pickTarget==='mulai'){
        jamMulai=t;
        document.getElementById('displayMulai').textContent=t;
    } else {
        jamSelesai=t;
        document.getElementById('displaySelesai').textContent=t;
    }
    updateDurasi();
    closeTimePicker();
    checkReady();
}
document.getElementById('modalTime').addEventListener('click',e=>{ if(e.target.id==='modalTime') closeTimePicker(); });

function updateDurasi(){
    const el = document.getElementById('durasiDisplay');
    if(!jamMulai||!jamSelesai){ el.textContent='—'; el.className='dur-placeholder'; return; }
    const [h1,m1]=jamMulai.split(':').map(Number);
    const [h2,m2]=jamSelesai.split(':').map(Number);
    const total=(h2*60+m2)-(h1*60+m1);
    if(total<=0){ el.textContent='—'; el.className='dur-placeholder'; return; }
    const jam=Math.floor(total/60), mnt=total%60;
    el.textContent=(jam>0?jam+' Jam ':'')+mnt+' Menit';
    el.className='dur-val';
}

// ── Photo ──
function previewFoto(input){
    const file=input.files[0];
    if(!file) return;
    fotoFile=file;
    const reader=new FileReader();
    reader.onload=e=>{
        document.getElementById('fotoPreviewImg').src=e.target.result;
        document.getElementById('fotoPreviewSlot').style.display='block';
        // Ubah upload slot jadi camera slot
        document.getElementById('uploadSlot').style.display='none';
        document.getElementById('cameraSlot').style.display='flex';
    };
    reader.readAsDataURL(file);
}
function capturePhoto(){ document.getElementById('inputCamera').click(); }
function removeFoto(){
    fotoFile=null;
    document.getElementById('fotoPreviewSlot').style.display='none';
    document.getElementById('uploadSlot').style.display='flex';
    document.getElementById('cameraSlot').style.display='none';
    document.getElementById('inputFoto').value='';
    document.getElementById('inputCamera').value='';
}

// ── Validation ──
function checkReady(){
    const ok = selKat && jamMulai && jamSelesai;
    document.getElementById('submitBtn').disabled = !ok;
}

function showAlert(msg, type='err'){
    const el=document.getElementById('alertBar');
    el.className='alert-bar '+(type==='ok'?'ok':'err');
    el.innerHTML=`<ion-icon name="${type==='ok'?'checkmark-circle':'alert-circle'}-outline" style="font-size:18px;flex-shrink:0;"></ion-icon> ${msg}`;
    el.style.display='flex';
    setTimeout(()=>{ el.style.display='none'; }, 4000);
}

// ── Submit ──
async function handleSubmit(){
    if(!selKat)             { showAlert('Pilih kategori aktivitas.'); return; }
    if(!jamMulai||!jamSelesai) { showAlert('Isi waktu mulai dan selesai.'); return; }

    const [h1,m1]=jamMulai.split(':').map(Number);
    const [h2,m2]=jamSelesai.split(':').map(Number);
    if((h2*60+m2)-(h1*60+m1)<=0){ showAlert('Waktu selesai harus setelah waktu mulai.'); return; }

    if(!ID_ANAK||!ID_ASSIGNMENT){ showAlert('Data tidak lengkap, kembali dan coba lagi.'); return; }

    const today=new Date();
    const ymd=today.getFullYear()+'-'+pad(today.getMonth()+1)+'-'+pad(today.getDate());

    const btn=document.getElementById('submitBtn');
    btn.disabled=true;
    btn.innerHTML='<ion-icon name="sync-outline" style="font-size:22px;color:#fff;animation:spin 1s linear infinite;"></ion-icon> Menyimpan...';

    const fd=new FormData();
    fd.append('_token',       CSRF);
    fd.append('id_assignment',ID_ASSIGNMENT);
    fd.append('id_anak',      ID_ANAK);
    fd.append('kategori',     selKat);
    fd.append('deskripsi',    document.getElementById('deskripsi').value);
    fd.append('jam_mulai',    `${ymd} ${jamMulai}:00`);
    fd.append('jam_selesai',  `${ymd} ${jamSelesai}:00`);
    fd.append('mood',         selMood);
    if(fotoFile) fd.append('foto', fotoFile);

    try{
        const res  = await fetch(SUBMIT_URL,{method:'POST',body:fd});
        const data = await res.json();
        if(data.status==='success'||data.success){
            showAlert('Aktivitas berhasil ditambahkan!','ok');
            setTimeout(()=>{
                window.location.href='{{ route("nanny-diary", ["id_anak"=>$idAnak??0]) }}?id_assignment={{ $idAssignment ?? "" }}';
            },1200);
        } else {
            showAlert(data.message||'Gagal menyimpan aktivitas.');
            btn.disabled=false;
            btn.innerHTML='<ion-icon name="save-outline" style="font-size:22px;color:#fff;"></ion-icon> Save Diary';
        }
    } catch(err){
        showAlert('Terjadi kesalahan koneksi.');
        btn.disabled=false;
        btn.innerHTML='<ion-icon name="save-outline" style="font-size:22px;color:#fff;"></ion-icon> Save Diary';
    }
}
</script>
@include('partials.auth-guard')
</body>
</html>