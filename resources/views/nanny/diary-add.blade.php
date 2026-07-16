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
        grid-template-columns: repeat(4, 1fr);
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
        width: 100%;
        height: 120px;
        border-radius: 14px;
        border: 2px dashed #C4B5FD;
        background: #F8F7FF;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        margin-bottom: 5px;
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

    /* ── BAB / BAK Components ── */
    .bab-section { background:#fff; border-radius:16px; border:1.5px solid #EDE9FE; padding:16px; margin-bottom:24px; }
    .swatch-group { display:flex; gap:10px; flex-wrap:wrap; }
    .swatch-btn {
        display:flex; align-items:center; gap:6px; padding:8px 14px;
        border-radius:20px; border:1.5px solid #EDE9FE; background:#fff;
        cursor:pointer; font-family:'Nunito',sans-serif; font-size:13px;
        font-weight:700; color:#5A556E; transition:all .15s;
    }
    .swatch-btn:active { transform:scale(0.95); }
    .swatch-btn.sel { border-color:#8B46D3; background:#F8F7FF; color:#8B46D3; }
    .swatch-dot { width:16px;height:16px;border-radius:50%;display:inline-block;border:1.5px solid rgba(0,0,0,.1);flex-shrink:0; }
    .pill-group { display:flex; gap:8px; flex-wrap:wrap; }
    .pill-btn {
        padding:8px 18px; border-radius:20px; border:1.5px solid #EDE9FE;
        background:#fff; cursor:pointer; font-family:'Nunito',sans-serif;
        font-size:13px; font-weight:700; color:#5A556E; transition:all .15s;
    }
    .pill-btn:active { transform:scale(0.95); }
    .pill-btn.sel { border-color:#8B46D3; background:#8B46D3; color:#fff; }
    .stepper-wrap {
        display:flex; align-items:center; gap:14px;
        background:#fff; border-radius:12px; border:1.5px solid #EDE9FE;
        padding:10px 16px; max-width:160px;
    }
    .stepper-btn {
        width:36px; height:36px; border-radius:50%; border:none;
        background:#EDE9FE; display:flex; align-items:center;
        justify-content:center; cursor:pointer;
        font-size:20px; font-weight:900; color:#8B46D3; transition:all .12s;
    }
    .stepper-btn:active { transform:scale(0.9); }
    .stepper-btn:disabled { opacity:.3; cursor:not-allowed; }
    .stepper-val { font-size:20px; font-weight:900; color:#1E1B2E; min-width:28px; text-align:center; }

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
                    ['value'=>'minum',   'label'=>'Drink',      'icon'=>'cafe',          'bg'=>'#E0F7FA', 'color'=>'#00BCD4'],
                    ['value'=>'tidur',   'label'=>'Sleep',      'icon'=>'moon',           'bg'=>'#EEF4FF', 'color'=>'#7BB4F0'],
                    ['value'=>'main',    'label'=>'Play',       'icon'=>'car-sport',      'bg'=>'#FFF0F7', 'color'=>'#FF6BA3'],
                    ['value'=>'belajar', 'label'=>'Study',      'icon'=>'book',           'bg'=>'#EEFFF3', 'color'=>'#4CAF7D'],
                    ['value'=>'mandi',   'label'=>'Take A Bath','icon'=>'water',          'bg'=>'#EEF7FF', 'color'=>'#7BB4F0'],
                    ['value'=>'bab',     'label'=>'BAB',        'icon'=>'ellipse',        'bg'=>'#EFEBE9', 'color'=>'#5D4037'],
                    ['value'=>'bak',     'label'=>'BAK',        'icon'=>'ellipse',        'bg'=>'#FFF8E1', 'color'=>'#F9A825'],
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
    <div class="anim d4" data-section="mood">
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
    <div class="anim d5" data-section="description">
        <p class="sec-label">Activity Description</p>
        <div class="desk-wrap">
            <textarea id="deskripsi" class="desk-input" rows="4"
                      placeholder="Write down the details of your child's activities today...."></textarea>
        </div>
    </div>

    {{-- MAKAN / MINUM DETAILS --}}
    <div class="bab-section anim" id="makanMinumSection" style="display:none;" data-section="makan-minum">
        <p class="sec-label" style="margin-bottom:16px;">Makan & Minum Details</p>

        {{-- Porsi --}}
        <div style="margin-bottom:18px;">
            <p class="time-field-label">Porsi</p>
            <div class="pill-group">
                @php $porsiOpts = ['Habis','Setengah','Sedikit','Tidak Makan']; @endphp
                @foreach($porsiOpts as $p)
                <button type="button" class="pill-btn" data-porsi="{{ strtolower(str_replace(' ','_',$p)) }}" onclick="selectPorsi(this)">{{ $p }}</button>
                @endforeach
            </div>
        </div>

        {{-- Nafsu Makan --}}
        <div style="margin-bottom:18px;">
            <p class="time-field-label">Nafsu Makan</p>
            <div class="pill-group">
                @php $nafsuOpts = ['Lapar','Biasa','Tidak Nafsu']; @endphp
                @foreach($nafsuOpts as $n)
                <button type="button" class="pill-btn" data-nafsu="{{ strtolower(str_replace(' ','_',$n)) }}" onclick="selectNafsu(this)">{{ $n }}</button>
                @endforeach
            </div>
        </div>

        {{-- Foto Sebelum & Sesudah --}}
        <div style="margin-bottom:12px;">
            <p class="time-field-label">Foto Makanan</p>
        </div>
        <div class="photo-grid" style="margin-bottom:0;">
            <div>
                <p style="font-size:11px;font-weight:700;color:#A8A2C2;margin-bottom:6px;">Sebelum</p>
                <div id="fotoSebelumPreview" class="photo-slot" style="display:none;">
                    <img id="fotoSebelumImg" src="" alt="Sebelum">
                    <button type="button" class="photo-remove" onclick="removeFotoSebelum()">
                        <ion-icon name="close" style="font-size:14px;color:#fff;"></ion-icon>
                    </button>
                </div>
                <div id="fotoSebelumActions" style="display:contents;">
                    <label class="upload-slot" for="inputFotoSebelum">
                        <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                            <ion-icon name="images-outline" style="font-size:20px;color:#8B46D3;"></ion-icon>
                        </div>
                        <span>Gallery</span>
                    </label>
                    <button type="button" class="upload-slot" onclick="captureFotoSebelum()">
                        <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                            <ion-icon name="camera" style="font-size:20px;color:#8B46D3;"></ion-icon>
                        </div>
                        <span>Camera</span>
                    </button>
                </div>
                <input type="file" id="inputFotoSebelum" accept="image/*" class="hidden" onchange="previewFotoSebelum(this)">
                <input type="file" id="inputCameraSebelum" accept="image/*" capture="environment" class="hidden" onchange="previewFotoSebelum(this)">
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#A8A2C2;margin-bottom:6px;">Sesudah</p>
                <div id="fotoSesudahPreview" class="photo-slot" style="display:none;">
                    <img id="fotoSesudahImg" src="" alt="Sesudah">
                    <button type="button" class="photo-remove" onclick="removeFotoSesudah()">
                        <ion-icon name="close" style="font-size:14px;color:#fff;"></ion-icon>
                    </button>
                </div>
                <div id="fotoSesudahActions" style="display:contents;">
                    <label class="upload-slot" for="inputFotoSesudah">
                        <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                            <ion-icon name="images-outline" style="font-size:20px;color:#8B46D3;"></ion-icon>
                        </div>
                        <span>Gallery</span>
                    </label>
                    <button type="button" class="upload-slot" onclick="captureFotoSesudah()">
                        <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                            <ion-icon name="camera" style="font-size:20px;color:#8B46D3;"></ion-icon>
                        </div>
                        <span>Camera</span>
                    </button>
                </div>
                <input type="file" id="inputFotoSesudah" accept="image/*" class="hidden" onchange="previewFotoSesudah(this)">
                <input type="file" id="inputCameraSesudah" accept="image/*" capture="environment" class="hidden" onchange="previewFotoSesudah(this)">
            </div>
        </div>
    </div>

    {{-- LOCATION --}}
    <div class="anim d55" data-section="location">
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

    {{-- BAB / BAK DETAILS --}}
    <div id="babBakSection" class="bab-section anim" style="display:none;">
        <p class="sec-label" style="margin-bottom:16px;">BAB / BAK Details</p>

        {{-- Warna (Color Swatches) --}}
        <div style="margin-bottom:18px;">
            <p class="time-field-label">Warna</p>
            <div class="swatch-group" id="warnaGroupBab" style="display:none;">
                <button type="button" class="swatch-btn" data-warna="coklat" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#6D4C41;"></span>Coklat</button>
                <button type="button" class="swatch-btn" data-warna="hijau" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#2E7D32;"></span>Hijau</button>
                <button type="button" class="swatch-btn" data-warna="kuning" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#F9A825;"></span>Kuning</button>
                <button type="button" class="swatch-btn" data-warna="hitam" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#212121;"></span>Hitam</button>
                <button type="button" class="swatch-btn" data-warna="merah" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#C62828;"></span>Merah</button>
            </div>
            <div class="swatch-group" id="warnaGroupBak" style="display:none;">
                <button type="button" class="swatch-btn" data-warna="kuning" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#F9A825;"></span>Kuning</button>
                <button type="button" class="swatch-btn" data-warna="jernih" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#CFD8DC;"></span>Jernih</button>
                <button type="button" class="swatch-btn" data-warna="keruh" onclick="selectWarna(this)"><span class="swatch-dot" style="background:#8D6E63;"></span>Keruh</button>
            </div>
        </div>

        {{-- Tekstur (BAB only) --}}
        <div id="teksturField" style="margin-bottom:18px;">
            <p class="time-field-label">Tekstur</p>
            <div class="pill-group">
                @php $teksturOpts = ['Padat','Lembek','Cair','Keras','Berbusa']; @endphp
                @foreach($teksturOpts as $t)
                <button type="button" class="pill-btn" data-tekstur="{{ strtolower($t) }}" onclick="selectTekstur(this)">{{ $t }}</button>
                @endforeach
            </div>
        </div>

        {{-- Volume --}}
        <div style="margin-bottom:18px;">
            <p class="time-field-label">Volume</p>
            <div class="pill-group">
                @php $volOpts = ['Sedikit','Sedang','Banyak']; @endphp
                @foreach($volOpts as $v)
                <button type="button" class="pill-btn" data-volume="{{ strtolower($v) }}" onclick="selectVolume(this)">{{ $v }}</button>
                @endforeach
            </div>
        </div>

        {{-- Frekuensi (Stepper) --}}
        <div style="margin-bottom:18px;">
            <p class="time-field-label">Frekuensi (kali)</p>
            <div class="stepper-wrap">
                <button type="button" class="stepper-btn" onclick="adjustFrekuensi(-1)" id="frekuensiMin">−</button>
                <span class="stepper-val" id="frekuensiDisplay">1</span>
                <button type="button" class="stepper-btn" onclick="adjustFrekuensi(1)" id="frekuensiPlus">+</button>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div>
            <p class="time-field-label">Deskripsi</p>
            <div class="desk-wrap">
                <textarea id="catatanKondisi" class="desk-input" rows="3" placeholder="Deskripsi tambahan tentang kondisi..."></textarea>
            </div>
        </div>
    </div>

    {{-- UPLOAD CHILD PHOTOS --}}
    <div class="anim d6" data-section="photo">
        <p class="sec-label">Upload Child Photos</p>
        <div class="photo-grid">
            <div id="fotoPreviewSlot" class="photo-slot" style="display:none;">
                <img id="fotoPreviewImg" src="" alt="Preview">
                <button type="button" class="photo-remove" onclick="removeFoto()">
                    <ion-icon name="close" style="font-size:14px;color:#fff;"></ion-icon>
                </button>
            </div>
            <div id="photoActions" class="photo-actions" style="display:contents;">
                <label id="uploadSlot" class="upload-slot" for="inputFoto">
                    <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                        <ion-icon name="images-outline" style="font-size:20px;color:#8B46D3;"></ion-icon>
                    </div>
                    <span>Gallery</span>
                </label>
                <button type="button" id="cameraSlot" class="upload-slot" onclick="capturePhoto()">
                    <div style="width:40px;height:40px;border-radius:50%;background:#EDE9FE;display:flex;align-items:center;justify-content:center;">
                        <ion-icon name="camera" style="font-size:20px;color:#8B46D3;"></ion-icon>
                    </div>
                    <span>Camera</span>
                </button>
            </div>
        </div>
        <input type="file" id="inputFoto" accept="image/*" capture="environment" class="hidden" onchange="previewFoto(this)">
        <input type="file" id="inputCamera" accept="image/*" capture="environment" class="hidden" onchange="previewFoto(this)">
        <p style="font-size:11px;font-weight:600;color:#A8A2C2;text-align:center;margin-top:-16px;margin-bottom:24px;">
            Choose <strong>Gallery</strong> to pick from photos or <strong>Camera</strong> to take a new photo
        </p>
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
let selWarna   = '';
let selTekstur = '';
let selVolume  = '';
let frekuensi  = 1;
let selPorsi  = '';
let selNafsu  = '';
let fotoSebelumFile = null;
let fotoSesudahFile = null;

const KAT_DURASI = {
    makan:   30,
    minum:   10,
    tidur:   120,
    main:    60,
    belajar: 45,
    mandi:   15,
    bab:     10,
    bak:     2,
};

function pad(n){ return String(n).padStart(2,'0'); }

function setDurasi(menit){
    const now=new Date();
    jamMulai=pad(now.getHours())+':'+pad(now.getMinutes());
    const end=new Date(now.getTime()+menit*60000);
    jamSelesai=pad(end.getHours())+':'+pad(end.getMinutes());
    document.getElementById('displayMulai').textContent=jamMulai;
    document.getElementById('displaySelesai').textContent=jamSelesai;
    updateDurasi();
}

// ── Default times ──
(function(){ setDurasi(30); })();

// ── Category ──
function selectKat(btn){
    document.querySelectorAll('.kat-btn').forEach(b=>{ b.classList.remove('sel'); });
    btn.classList.add('sel');
    selKat = btn.dataset.kat;
    setDurasi(KAT_DURASI[selKat] || 30);

    const isBabBak = selKat === 'bab' || selKat === 'bak';
    const isMakanMinum = selKat === 'makan' || selKat === 'minum';
    const babSection = document.getElementById('babBakSection');
    const makanSection = document.getElementById('makanMinumSection');
    const moodSection = document.querySelector('[data-section="mood"]');
    const descSection = document.querySelector('[data-section="description"]');
    const locationSection = document.querySelector('[data-section="location"]');
    const photoSection = document.querySelector('[data-section="photo"]');

    // All categories: hide BAB and Makan sections first
    if (babSection) babSection.style.display = 'none';
    if (makanSection) makanSection.style.display = 'none';

    if (isBabBak) {
        babSection.style.display = 'block';
        if (descSection) descSection.style.display = 'none';
        if (locationSection) locationSection.style.display = 'none';
        if (photoSection) photoSection.style.display = 'none';

        // Show/hide BAB vs BAK specific fields
        document.getElementById('warnaGroupBab').style.display = selKat === 'bab' ? 'flex' : 'none';
        document.getElementById('warnaGroupBak').style.display = selKat === 'bak' ? 'flex' : 'none';
        document.getElementById('teksturField').style.display = selKat === 'bab' ? 'block' : 'none';
    } else if (isMakanMinum) {
        if (makanSection) makanSection.style.display = 'block';
        if (moodSection) moodSection.style.display = '';
        if (descSection) descSection.style.display = '';
        if (locationSection) locationSection.style.display = '';
        if (photoSection) photoSection.style.display = 'none'; // hide general photo, use makan-specific ones
    } else {
        if (moodSection) moodSection.style.display = '';
        if (descSection) descSection.style.display = '';
        if (locationSection) locationSection.style.display = '';
        if (photoSection) photoSection.style.display = '';
    }
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

const MAX_PHOTO_SIZE = 10 * 1024 * 1024; // 10 MB
const COMPRESS_MAX_DIM = 1200; // max pixel (longest edge)
const COMPRESS_QUALITY = 0.7;  // JPEG quality 0-1

/**
 * Jika file > 10MB, tunjukkan tombol "Kompres Foto" via alert bar.
 * Kompresi via Canvas API — resize + turunkan kualitas JPEG.
 * Returns Promise<File | null> — null berarti user batal.
 */
function handleOversizedFile(file) {
    return new Promise(resolve => {
        if (!file || file.size <= MAX_PHOTO_SIZE) {
            resolve(file);
            return;
        }

        const el = document.getElementById('alertBar');
        el.className = 'alert-bar err';
        el.style.display = 'flex';
        el.style.justifyContent = 'space-between';
        el.style.alignItems = 'center';
        el.innerHTML = [
            '<span style="font-size:13px;">Ukuran foto ' + (file.size / 1048576).toFixed(1) + ' MB (max 10 MB)</span>',
            '<div style="display:flex;gap:6px;flex-shrink:0;">',
            '<button id="btnCompress"',
            '  style="background:#8B46D3;color:#fff;border:none;border-radius:20px;padding:6px 14px;font-size:12px;font-weight:800;cursor:pointer;">',
            '  ✦ Kompres Foto',
            '</button>',
            '<button onclick="cancelOversized()"',
            '  style="background:transparent;color:#B91C1C;border:1.5px solid #B91C1C;border-radius:20px;padding:6px 12px;font-size:12px;font-weight:800;cursor:pointer;">',
            '  Ganti Foto',
            '</button>',
            '</div>'
        ].join('');

        // Prevent auto-hide
        if (window._alertTimeout) clearTimeout(window._alertTimeout);

        window._oversizedFile = file;
        window._oversizedResolve = resolve;

        // Attach listener for the compress button
        document.getElementById('btnCompress').addEventListener('click', compressAndResolve);
    });
}

async function compressAndResolve() {
    const file = window._oversizedFile;
    if (!file) return;

    const el = document.getElementById('alertBar');
    el.innerHTML = [
        '<span style="font-size:13px;">⏳ Mengompres foto...</span>',
        '<div style="width:20px;height:20px;border:3px solid #8B46D3;border-top-color:transparent;border-radius:50%;animation:spin .6s linear infinite;"></div>'
    ].join('');

    try {
        const compressed = await compressImage(file);
        window._oversizedResolve(compressed);
    } catch (e) {
        window._oversizedResolve(null);
    }
    el.style.display = 'none';
    window._oversizedFile = null;
    window._oversizedResolve = null;
}

function cancelOversized() {
    document.getElementById('alertBar').style.display = 'none';
    if (window._oversizedResolve) {
        window._oversizedResolve(null);
    }
    window._oversizedFile = null;
    window._oversizedResolve = null;
}

/**
 * Compress image via Canvas API.
 * Resize longest edge to COMPRESS_MAX_DIM, output JPEG at COMPRESS_QUALITY.
 */
function compressImage(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                // Calculate new dimensions
                let w = img.width, h = img.height;
                if (w > COMPRESS_MAX_DIM || h > COMPRESS_MAX_DIM) {
                    const ratio = Math.min(COMPRESS_MAX_DIM / w, COMPRESS_MAX_DIM / h);
                    w = Math.round(w * ratio);
                    h = Math.round(h * ratio);
                }

                // Draw onto canvas
                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                // Export as JPEG blob
                canvas.toBlob(function (blob) {
                    if (!blob) { reject(new Error('Compression failed')); return; }
                    const compressedFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    });
                    resolve(compressedFile);
                }, 'image/jpeg', COMPRESS_QUALITY);
            };
            img.onerror = reject;
            img.src = e.target.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

// ── Photo ──
async function previewFoto(input){
    const file=input.files[0];
    if(!file) return;
    const safeFile = await handleOversizedFile(file);
    if (!safeFile) { input.value = ''; return; }
    fotoFile=safeFile;
    const reader=new FileReader();
    reader.onload=e=>{
        document.getElementById('fotoPreviewImg').src=e.target.result;
        document.getElementById('fotoPreviewSlot').style.display='block';
        document.getElementById('photoActions').style.display='none';
    };
    reader.readAsDataURL(safeFile);
}
function capturePhoto(){ document.getElementById('inputCamera').click(); }
function removeFoto(){
    fotoFile=null;
    document.getElementById('fotoPreviewSlot').style.display='none';
    document.getElementById('photoActions').style.display='contents';
    document.getElementById('inputFoto').value='';
    document.getElementById('inputCamera').value='';
}

// ── MAKAN / MINUM ──
function selectPorsi(btn){
    document.querySelectorAll('.pill-btn[data-porsi]').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');
    selPorsi = btn.dataset.porsi;
}
function selectNafsu(btn){
    document.querySelectorAll('.pill-btn[data-nafsu]').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');
    selNafsu = btn.dataset.nafsu;
}
async function previewFotoSebelum(input){
    const file=input.files[0];
    if(!file) return;
    const safeFile = await handleOversizedFile(file);
    if (!safeFile) { input.value = ''; return; }
    fotoSebelumFile=safeFile;
    const reader=new FileReader();
    reader.onload=e=>{
        document.getElementById('fotoSebelumImg').src=e.target.result;
        document.getElementById('fotoSebelumPreview').style.display='block';
        document.getElementById('fotoSebelumActions').style.display='none';
    };
    reader.readAsDataURL(safeFile);
}
function captureFotoSebelum(){ document.getElementById('inputCameraSebelum').click(); }
function removeFotoSebelum(){
    fotoSebelumFile=null;
    document.getElementById('fotoSebelumPreview').style.display='none';
    document.getElementById('fotoSebelumActions').style.display='contents';
    document.getElementById('inputFotoSebelum').value='';
    document.getElementById('inputCameraSebelum').value='';
}
async function previewFotoSesudah(input){
    const file=input.files[0];
    if(!file) return;
    const safeFile = await handleOversizedFile(file);
    if (!safeFile) { input.value = ''; return; }
    fotoSesudahFile=safeFile;
    const reader=new FileReader();
    reader.onload=e=>{
        document.getElementById('fotoSesudahImg').src=e.target.result;
        document.getElementById('fotoSesudahPreview').style.display='block';
        document.getElementById('fotoSesudahActions').style.display='none';
    };
    reader.readAsDataURL(safeFile);
}
function captureFotoSesudah(){ document.getElementById('inputCameraSesudah').click(); }
function removeFotoSesudah(){
    fotoSesudahFile=null;
    document.getElementById('fotoSesudahPreview').style.display='none';
    document.getElementById('fotoSesudahActions').style.display='contents';
    document.getElementById('inputFotoSesudah').value='';
    document.getElementById('inputCameraSesudah').value='';
}

// ── BAB / BAK ──
function selectWarna(btn){
    document.querySelectorAll('.swatch-btn').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');
    selWarna = btn.dataset.warna;
}
function selectTekstur(btn){
    document.querySelectorAll('.pill-btn[data-tekstur]').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');
    selTekstur = btn.dataset.tekstur;
}
function selectVolume(btn){
    document.querySelectorAll('.pill-btn[data-volume]').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');
    selVolume = btn.dataset.volume;
}
function adjustFrekuensi(delta){
    frekuensi = Math.max(1, Math.min(10, frekuensi + delta));
    document.getElementById('frekuensiDisplay').textContent = frekuensi;
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
    fd.append('mood', selMood);
    const isMakanMinum = selKat === 'makan' || selKat === 'minum';
    const isBabBak = selKat === 'bab' || selKat === 'bak';
    if (!isBabBak) {
        if(fotoFile) fd.append('foto', fotoFile);
        if(userLat)  fd.append('lat', userLat);
        if(userLng)  fd.append('lng', userLng);
    }
    // BAB/BAK fields
    if (isBabBak) {
        fd.append('warna',           selWarna);
        fd.append('tekstur',         selTekstur);
        fd.append('volume',          selVolume);
        fd.append('frekuensi',       frekuensi);
        fd.append('deskripsi',       document.getElementById('catatanKondisi').value);
    }
    // Makan/Minum fields
    if (isMakanMinum) {
        fd.append('porsi',            selPorsi);
        fd.append('nafsu_makan',      selNafsu);
        if(fotoSebelumFile) fd.append('foto_sebelum', fotoSebelumFile);
        if(fotoSesudahFile) fd.append('foto_sesudah', fotoSesudahFile);
    }

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
