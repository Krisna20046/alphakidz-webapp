@extends('layouts.app')

@section('title', 'Attendance')

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
</style>
@endpush

@php
    // Tutorial steps for this screen (atTutorial*). Now attendance is self-service (no child picker).
    $attendanceSteps = [
        ['color' => '#8B46D3', 'icon' => 'time-outline',       'title' => 'Daily attendance',
         'body'  => 'Your daily attendance is recorded <b>for the child you are assigned to</b> (nanny has one majikan). No need to pick a child — just check in / out here.'],
        ['color' => '#D97706', 'icon' => 'location-outline',   'title' => 'GPS location',
         'body' => 'Before checking in, tap <b>Detect Location</b> to record your GPS position as proof of where you were.'],
        ['color' => '#16A34A', 'icon' => 'log-out-outline',    'title' => 'Check out',
         'body' => 'Tap <b>Check Out Now</b> when you finish. The time is captured automatically. Add a note or photo if useful.'],
        ['color' => '#8B86A5', 'icon' => 'file-tray-outline',  'title' => 'History',
         'body' => 'Your past check-in / out records appear below — swipe through pages to review.'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Attendance</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">
                @if($ctx)
                    {{ $ctx['anak_nama'] }}
                @else
                    Daily check-in / out
                @endif
            </p>
        </div>
        <button type="button" onclick="atTutorialOpen()"
            class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0"
            aria-label="Panduan">
            <ion-icon name="help-circle" class="text-white" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    @if(session('success') || session('error'))
    <div id="toast" class="toast rounded-2xl px-4 py-3 flex items-center gap-3 mb-4
        {{ session('success') ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center
            {{ session('success') ? 'bg-green-100' : 'bg-red-100' }}">
            <ion-icon name="{{ session('success') ? 'checkmark-circle' : 'close-circle' }}"
                style="font-size:18px;color:{{ session('success') ? '#4CAF50' : '#F44336' }};"></ion-icon>
        </div>
        <p class="text-sm font-bold {{ session('success') ? 'text-green-800' : 'text-red-800' }} flex-1">
            {{ session('success') ?? session('error') }}
        </p>
        <button onclick="document.getElementById('toast').remove()">
            <ion-icon name="close" style="font-size:16px;color:#999;"></ion-icon>
        </button>
    </div>
    @endif

    {{-- Today status + no-assignment block (AJAX-refreshed after check-in/out) --}}
    @include('nanny.attendance._ticket', ['ctx' => $ctx, 'today' => $today])

    @if($ctx)
    {{-- History (paginated, via partial) --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Attendance History</span>
            <span class="text-[10px] font-bold text-[#8B86A5]">Daily check-in/out</span>
        </div>
        @include('nanny.attendance._history', ['records' => $records, 'pagination' => $pagination])
    </div>
    @endif
</div>

@include('attendance._tutorial')
@include('attendance._detail_modal')

@endsection

@push('scripts')
<script>
const atToast = document.getElementById('toast');
if (atToast) setTimeout(() => atToast.remove(), 4000);

const CSRF = "{{ csrf_token() }}";

function nowStamp() {
    const d = new Date();
    const pad = n => String(n).padStart(2, '0');
    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + ' ' +
           pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
}

// ── GPS capture (pola nanny diary-add) ──
function atGetLocation() {
    if (!navigator.geolocation) {
        document.getElementById('atLocationErr').classList.remove('hidden');
        return;
    }
    const btn = document.getElementById('atLocationBtn');
    btn.disabled = true;
    document.getElementById('atLocationBtnText').textContent = 'Detecting…';
    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            document.getElementById('checkinLat').value = lat.toFixed(6);
            document.getElementById('checkinLng').value = lng.toFixed(6);
            document.getElementById('atLocationInfo').classList.remove('hidden');
            document.getElementById('atLocationErr').classList.add('hidden');
            document.getElementById('atLocationBtnText').textContent = 'Detect Again';
            btn.disabled = false;
        },
        () => {
            document.getElementById('atLocationInfo').classList.add('hidden');
            document.getElementById('atLocationErr').classList.remove('hidden');
            document.getElementById('atLocationBtnText').textContent = 'Retry Location';
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

function atCheckStamp(checkin) {
    const el = document.getElementById(checkin ? 'checkinTime' : 'checkoutTime');
    if (el) el.value = nowStamp();
}

// ── Photo (wajib) + auto-compress (pola diary-add) ──
const AT_MAX_DIM = 1200;   // max px longest edge
const AT_QUALITY = 0.7;    // JPEG quality 0-1

// Resize + turunkan kualitas via Canvas. Returns Promise<File> yang sudah ringan.
function atCompressImage(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.onload = () => {
                let w = img.width, h = img.height;
                if (w > AT_MAX_DIM || h > AT_MAX_DIM) {
                    const r = Math.min(AT_MAX_DIM / w, AT_MAX_DIM / h);
                    w = Math.round(w * r);
                    h = Math.round(h * r);
                }
                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                canvas.toBlob(blob => {
                    if (!blob) { reject(new Error('Compression failed')); return; }
                    resolve(new File([blob], file.name || 'photo.jpg', { type: 'image/jpeg', lastModified: Date.now() }));
                }, 'image/jpeg', AT_QUALITY);
            };
            img.onerror = reject;
            img.src = e.target.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

// Stash foto yang sudah dikompres per slot (ci = check-in, co = check-out).
const atPhotos = { ci: null, co: null };

// Panggil saat user pilih/kamera photo → kompres otomatis + preview.
function atShowPhoto(inputId, slot, imgId, labelId) {
    const inp  = document.getElementById(inputId);
    const f    = inp && inp.files && inp.files[0];
    const img  = document.getElementById(imgId);
    const lab  = document.getElementById(labelId);
    if (!f) return;
    if (lab) lab.textContent = 'Proses…';

    const setPhoto = comp => {
        atPhotos[slot] = comp;
        if (img) {
            img.src = URL.createObjectURL(comp);
            img.classList.remove('hidden');
        }
        if (lab)  lab.textContent = 'Retake Photo';
    };

    // Selalu kompres → JPEG (gambar kamera pun bisa WebP/HEIC/nama tanpa ekstensi).
    // atCompressImage selalu return image/jpeg → lolos rule `image` backend.
    // Bila decode/compress gagal: kosongkan + alert (JANGAN kirim format asli
    // yang bisa ditolak backend "Foto bukti wajib image").
    atCompressImage(f).then(setPhoto, err => {
        inp.value = '';
        if (lab) lab.textContent = 'Open Camera';
        showAppAlert('Foto tidak bisa diproses browser. Ambil ulang dengan format JPG/PNG.');
    });
}

// Lock semua tombol selama submit (prevent double-click).
let atSubmitting = false;
function atLockSubmit(on) {
    atSubmitting = on;
    ['checkinForm', 'checkoutForm'].forEach(id => {
        const f = document.getElementById(id);
        if (!f) return;
        f.querySelectorAll('button[type="submit"]').forEach(b => {
            b.disabled = on;
            b.style.opacity = on ? '0.55' : '';
            b.style.pointerEvents = on ? 'none' : '';
        });
    });
}
// Pastikan tombol tidak terkunci dari kondisi halaman sebelumnya.
window.addEventListener('load', () => atLockSubmit(false));

// Terapkan foto terkompresi ke input file bila browser mendukung DataTransfer.
// Penting: bila tidak bisa (browser tanpa DataTransfer), KOSONGKAN input supaya
// validasi wajib foto menolak submit — JANGAN kirim file asli (bisa HEIC/WebP
// yang ditolak rule `image` backend → "Foto bukti wajib image").
function atSetCompressedInput(inputId, slot) {
    const comp = atPhotos[slot];
    if (!comp) return;
    const inp = document.getElementById(inputId);
    if (!inp) return;
    try {
        const dt = new DataTransfer();
        dt.items.add(comp);
        inp.files = dt.files;
    } catch (e) {
        inp.value = '';  // kosongkan → atSubmitPhoto menampilkan pesan wajib foto
    }
}

// ── Refresh status card (check-in/out state + form) from server, no reload ──
function atReloadTicket() {
    return fetch('{{ route('nanny-attendance') }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
        .then(res => res.ok ? res.json() : Promise.reject())
        .then(data => {
            const wrap = document.createElement('div');
            wrap.innerHTML = data.html || '';

            const oldStatus = document.getElementById('atTodayStatus');
            const newStatus = wrap.querySelector('#atTodayStatus');
            if (oldStatus && newStatus) oldStatus.replaceWith(newStatus);

            const oldEmpty = document.getElementById('atNoAssignment');
            const newEmpty = wrap.querySelector('#atNoAssignment');
            if (oldEmpty && newEmpty) oldEmpty.replaceWith(newEmpty);

            // Cegah short-circuit `&&` nit: bila ctx hilang, #atTodayStatus tidak ada —
            // cek keberadaan dulu sebelum menyentuh tombol submit (atLockSubmit idempotent).
            if (document.getElementById('atTodayStatus') || document.querySelector('#checkinForm, #checkoutForm')) {
                atLockSubmit(false);
            }

            // History juga ikut diperbarui setelah check-in / out sukses.
            atGoToPage(1);
        })
        .catch(() => {
            showAppAlert('Gagal refresh status. Muat ulang halaman.', 'error');
            atLockSubmit(false);
        });
}

// ── Submit check-in / check-out via AJAX (FormData) ──
// Frontend → web proxy (token di session) → backend API. Tidak reload halaman.
function atPostForm(formId, onSuccess) {
    const form = document.getElementById(formId);
    const fd   = new FormData(form);
    fd.set('_token', CSRF);
    atLockSubmit(true);

    fetch(form.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (res.ok && (data.success || data.status === 'success')) {
                showAppAlert(data.message || 'Berhasil.', 'ok');
                onSuccess && onSuccess();
            } else {
                showAppAlert(data.message || 'Gagal menyimpan. Coba lagi.', 'error');
                atLockSubmit(false);
            }
        })
        .catch(() => {
            showAppAlert('Koneksi gagal. Coba lagi.', 'error');
            atLockSubmit(false);
        });
}

// Wajib check-in: GPS + foto. Submit AJAX.
window.atSubmitCheckin = function (e) {
    e.preventDefault();
    if (!navigator.geolocation) {
        showAppAlert('Lokasi tidak didukung browser. GPS wajib diisi.');
        return false;
    }
    const lat = document.getElementById('checkinLat');
    const lng = document.getElementById('checkinLng');
    if (!lat.value || !lng.value) {
        showAppAlert('GPS belum dideteksi. Tap "Detect Location" dulu sebelum check-in.');
        return false;
    }
    const inp = document.getElementById('ciPhotoInput');
    if (!inp.files || !inp.files[0]) {
        showAppAlert('Foto bukti check-in wajib. Buka kamera & ambil foto.');
        return false;
    }
    atSetCompressedInput('ciPhotoInput', 'ci');
    document.getElementById('checkinTime').value = nowStamp();
    atPostForm('checkinForm', () => atReloadTicket());
    return false;
};

// Wajib check-out: foto. Submit AJAX.
window.atSubmitCheckout = function (e) {
    e.preventDefault();
    const inp = document.getElementById('coPhotoInput');
    if (!inp.files || !inp.files[0]) {
        showAppAlert('Foto bukti check-out wajib. Buka kamera & ambil foto.');
        return false;
    }
    atSetCompressedInput('coPhotoInput', 'co');
    document.getElementById('checkoutTime').value = nowStamp();
    atPostForm('checkoutForm', () => atReloadTicket());
    return false;
};
</script>

<script>
async function atGoToPage(page) {
    const url = "{{ route('nanny-attendance-history') }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    const list = document.getElementById('historyList');
    if (list) list.outerHTML = html;
    window.resolveGeoPlaces && window.resolveGeoPlaces();
}
</script>
@endpush