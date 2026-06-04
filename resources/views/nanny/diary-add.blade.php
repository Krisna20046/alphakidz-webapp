@extends('layouts.app')

@section('title', 'Create Diary')

@push('styles')
<style>
    .sec-label {
        font-size: 13px; font-weight: 900; color: #1E1B2E;
        text-transform: uppercase; letter-spacing: 1px;
        margin-bottom: 14px;
    }

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

    .photo-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 28px;
    }
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

    .alert-bar {
        margin-bottom:12px; padding:12px 16px; border-radius:12px; font-size:13px; font-weight:700;
        display:none; gap:8px; align-items:center;
    }
    .alert-bar.err { display:flex; background:#FEE2E2; color:#B91C1C; }
    .alert-bar.ok  { display:flex; background:#DCFCE7; color:#166534; }

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

    @keyframes spin { to { transform: rotate(360deg); } }

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
</style>
@endpush

@section('content')
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

<div class="flex-1 overflow-y-auto px-[20px] pt-[40px] pb-24 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Alert --}}
    <div id="alertBar" class="alert-bar"></div>

    {{-- SELECT CATEGORY --}}
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

    {{-- START TIME / END TIME --}}
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

        <p class="time-field-label" style="margin-bottom:8px;">Total Duration</p>
        <div class="dur-field" style="margin-bottom:24px;">
            <ion-icon name="hourglass-outline" style="font-size:18px;color:#8B46D3;flex-shrink:0;"></ion-icon>
            <span id="durasiDisplay" class="dur-placeholder">—</span>
        </div>
    </div>

    {{-- CHILD'S MOOD --}}
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

    {{-- ACTIVITY DESCRIPTION --}}
    <div class="anim d5">
        <p class="sec-label">Activity Description</p>
        <div class="desk-wrap">
            <textarea id="deskripsi" class="desk-input" rows="4"
                      placeholder="Write down the details of your child's activities today...."></textarea>
        </div>
    </div>

    {{-- LOCATION --}}
    <div class="anim d55">
        <p class="sec-label">Location</p>
        <div style="background:#fff;border-radius:14px;border:1.5px solid #EDE9FE;padding:14px 16px;margin-bottom:24px;">
            <div id="locationInfo" style="display:none;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                    <ion-icon name="location" style="font-size:18px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                    <span style="font-size:13px;font-weight:700;color:#1E1B2E;" id="locationText">Detecting...</span>
                </div>
                <div style="display:flex;gap:12px;font-size:11px;font-weight:600;color:#A8A2C2;">
                    <span id="displayLat"></span>
                    <span id="displayLng"></span>
                </div>
            </div>
            <button type="button" id="getLocationBtn" onclick="getLocation()"
                    style="display:flex;align-items:center;gap:8px;width:100%;padding:6px 0;background:none;border:none;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:#8B46D3;">
                <ion-icon name="navigate-outline" style="font-size:18px;"></ion-icon>
                <span id="locationBtnText">Detect Current Location</span>
            </button>
        </div>
    </div>

    {{-- UPLOAD CHILD PHOTOS --}}
    <div class="anim d6">
        <p class="sec-label">Upload Child Photos</p>
        <div class="photo-grid">
            <div id="fotoPreviewSlot" class="photo-slot" style="display:none;">
                <img id="fotoPreviewImg" src="" alt="Preview">
                <button type="button" class="photo-remove" onclick="removeFoto()">
                    <ion-icon name="close" style="font-size:14px;color:#fff;"></ion-icon>
                </button>
            </div>
            <label id="uploadSlot" class="upload-slot" for="inputFoto">
                <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                    <ion-icon name="camera-outline" style="font-size:20px;color:#8B46D3;"></ion-icon>
                </div>
                <span>Upload</span>
            </label>
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
    <button id="submitBtn" class="submit-btn" onclick="handleSubmit()" disabled>
        <ion-icon name="save-outline" style="font-size:22px;color:#fff;"></ion-icon>
        Save Diary
    </button>
</div>
@endsection

@push('modals')
{{-- TIME PICKER MODAL --}}
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
@endpush

@push('scripts')
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
let userLat    = '';
let userLng    = '';
let locationName = '';
let locationAttempted = false;

function pad(n){ return String(n).padStart(2,'0'); }

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
    document.querySelectorAll('.kat-btn').forEach(b=>{ b.classList.remove('sel'); });
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

// ── Location ──
function getLocation(){
    if(!navigator.geolocation){
        document.getElementById('locationBtnText').textContent = 'Geolocation not supported';
        return;
    }
    const btn = document.getElementById('getLocationBtn');
    btn.disabled = true;
    document.getElementById('locationBtnText').textContent = 'Mendeteksi...';
    locationAttempted = true;
    navigator.geolocation.getCurrentPosition(
        pos => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            document.getElementById('locationInfo').style.display = 'block';
            document.getElementById('locationText').textContent = 'Lokasi terdeteksi';
            document.getElementById('displayLat').textContent = 'Lat: ' + userLat.toFixed(6);
            document.getElementById('displayLng').textContent = 'Lng: ' + userLng.toFixed(6);
            document.getElementById('locationBtnText').textContent = 'Deteksi Ulang Lokasi';
            btn.disabled = false;
        },
        err => {
            document.getElementById('locationBtnText').textContent = 'Gagal deteksi lokasi, ketuk untuk coba lagi';
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
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
    if(userLat)  fd.append('lat', userLat);
    if(userLng)  fd.append('lng', userLng);

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
@endpush
