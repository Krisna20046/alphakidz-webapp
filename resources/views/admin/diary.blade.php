{{-- resources/views/admin/diary.blade.php --}}
{{-- Route: GET /admin/diary/{id_nanny}/anak/{id_anak} --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Diary Anak – Admin</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { plum: { DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B', accent:'#B895C8' } },
                    fontFamily: { sans: ['Plus Jakarta Sans','sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF9FB; }
        @media (min-width: 640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3 0%,#ede0f0 60%,#e8d5ee 100%); }
            .phone-frame   { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; position:relative; }
        }
        @media (max-width: 639px) { .phone-wrapper{min-height:100vh;} .phone-frame{min-height:100vh;} }

        .header-bg { background: linear-gradient(135deg,#7B1E5A 0%,#9B2E72 100%); }

        @keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .anim-up    { animation:slideUp .35s ease forwards; }
        .anim-up.d1 { animation-delay:.05s; opacity:0; }
        .anim-up.d2 { animation-delay:.10s; opacity:0; }
        .anim-up.d3 { animation-delay:.15s; opacity:0; }
        .anim-up.d4 { animation-delay:.20s; opacity:0; }

        @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
        .skeleton { background:linear-gradient(90deg,#f0dcea 25%,#fce8f5 50%,#f0dcea 75%); background-size:400px 100%; animation:shimmer 1.4s infinite; border-radius:12px; }

        .akt-card { transition:opacity .15s, transform .15s; cursor:pointer; }
        .akt-card:hover  { opacity:.85; }
        .akt-card:active { transform:scale(0.98); opacity:.7; }

        .filter-chip { cursor:pointer; transition:background .15s, color .15s, border-color .15s; white-space:nowrap; }
        .filter-chip:active { transform:scale(0.95); }

        .date-arrow { cursor:pointer; border:none; background:transparent; transition:background .15s; border-radius:10px; padding:8px; display:flex; align-items:center; justify-content:center; }
        .date-arrow:hover { background:#F3E6FA; }

        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }

        @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; padding:20px; z-index:50; opacity:0; pointer-events:none; transition:opacity .2s ease; }
        .modal-overlay.open { opacity:1; pointer-events:auto; }
        .modal-box { background:#fff; border-radius:24px; width:100%; max-height:82vh; overflow:hidden; transform:translateY(20px); transition:transform .25s ease; display:flex; flex-direction:column; max-width:390px; }
        .modal-overlay.open .modal-box { transform:translateY(0); }

        .picker-scroll { height:200px; overflow-y:auto; border-radius:12px; background:#FFF9FB; }
        .picker-scroll::-webkit-scrollbar { display:none; }
        .picker-scroll { -ms-overflow-style:none; scrollbar-width:none; }
        .picker-item { padding:10px 12px; text-align:center; border-radius:8px; cursor:pointer; margin:2px 4px; font-size:14px; color:#4A0E35; font-weight:500; transition:background .12s; }
        .picker-item.active { background:#7B1E5A; color:#fff; font-weight:700; }
        .picker-item:hover:not(.active) { background:#F3E6FA; }

        .badge-admin { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; background:rgba(243,230,250,0.9); color:#7B1E5A; }
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
         style="padding:50px 20px 24px; border-bottom-left-radius:24px; border-bottom-right-radius:24px; margin-bottom:14px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <button id="btnBack"
                class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
                style="top:54px; left:20px; width:40px; height:40px; z-index:10; border:none; cursor:pointer;">
            <ion-icon name="arrow-back" style="font-size:20px; color:#fff;"></ion-icon>
        </button>

        <div class="flex flex-col items-center anim-up d1">
            <div class="flex items-center justify-center bg-white rounded-full mb-3 shadow-lg" style="width:64px; height:64px;">
                <ion-icon name="book" style="font-size:30px; color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-extrabold text-white mb-1" style="font-size:22px; letter-spacing:.4px;" id="judulAnak">Diary Anak</h1>
            <p id="subtitleNanny" style="font-size:13px; color:#F3E6FA; font-weight:500;">Memuat data…</p>
            <div class="badge-admin mt-2">
                <ion-icon name="shield-checkmark-outline" style="font-size:12px;"></ion-icon>
                <span>Tampilan Admin</span>
            </div>
        </div>
    </div>

    <!-- FILTER KATEGORI -->
    <div class="flex-shrink-0 anim-up d2" style="margin-bottom:14px;">
        <div class="no-scrollbar flex" style="overflow-x:auto; padding:0 20px; gap:10px;">
            <button class="filter-chip" data-kat="" onclick="setKategori(this)"
                    style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #7B1E5A; background:#7B1E5A; font-weight:700; color:#fff; flex-shrink:0;">
                Semua
            </button>
            <button class="filter-chip" data-kat="makan" onclick="setKategori(this)"
                    style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #F3E6FA; background:#fff; font-weight:600; color:#7B1E5A; flex-shrink:0;">
                🍽 Makan
            </button>
            <button class="filter-chip" data-kat="tidur" onclick="setKategori(this)"
                    style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #F3E6FA; background:#fff; font-weight:600; color:#7B1E5A; flex-shrink:0;">
                😴 Tidur
            </button>
            <button class="filter-chip" data-kat="main" onclick="setKategori(this)"
                    style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #F3E6FA; background:#fff; font-weight:600; color:#7B1E5A; flex-shrink:0;">
                ⚽ Main
            </button>
            <button class="filter-chip" data-kat="belajar" onclick="setKategori(this)"
                    style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #F3E6FA; background:#fff; font-weight:600; color:#7B1E5A; flex-shrink:0;">
                📚 Belajar
            </button>
            <button class="filter-chip" data-kat="mandi" onclick="setKategori(this)"
                    style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #F3E6FA; background:#fff; font-weight:600; color:#7B1E5A; flex-shrink:0;">
                🛁 Mandi
            </button>
        </div>
    </div>

    <!-- DATE SELECTOR -->
    <div class="flex-shrink-0 anim-up d3" style="padding:0 20px; margin-bottom:14px;">
        <div class="flex items-center justify-between bg-white"
             style="padding:14px 16px; border-radius:16px; border:2px solid #F3E6FA;">
            <button class="date-arrow" id="btnPrev">
                <ion-icon name="chevron-back" style="font-size:26px; color:#7B1E5A;"></ion-icon>
            </button>
            <button id="btnDatePicker" class="flex flex-col items-center"
                    style="flex:1; background:transparent; cursor:pointer; border:none;">
                <span style="font-size:15px; font-weight:700; color:#4A0E35;" id="tanggalLabel">–</span>
                <span style="font-size:12px; color:#A2397B; margin-top:3px; font-weight:500;" id="totalLabel">0 aktivitas</span>
            </button>
            <button class="date-arrow" id="btnNext">
                <ion-icon name="chevron-forward" style="font-size:26px; color:#7B1E5A;"></ion-icon>
            </button>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 overflow-y-auto no-scrollbar anim-up d4" style="padding:0 20px 80px;">

        <!-- Skeleton -->
        <div id="skeletonList" style="display:none;">
            @for($i = 0; $i < 4; $i++)
            <div class="flex items-center bg-white mb-3" style="border-radius:16px; padding:16px; border:2px solid #F3E6FA; gap:12px;">
                <div class="skeleton" style="width:50px; height:50px; border-radius:25px; flex-shrink:0;"></div>
                <div style="flex:1; display:flex; flex-direction:column; gap:8px;">
                    <div class="skeleton" style="height:14px; width:60%;"></div>
                    <div class="skeleton" style="height:12px; width:40%;"></div>
                    <div class="skeleton" style="height:11px; width:30%;"></div>
                </div>
                <div class="skeleton" style="width:32px; height:32px; border-radius:16px; flex-shrink:0;"></div>
            </div>
            @endfor
        </div>

        <div id="aktList" style="display:none;"></div>

        <!-- Empty -->
        <div id="emptyState" style="display:none; flex-direction:column; align-items:center; justify-content:center; padding:50px 20px;">
            <div class="float-anim" style="width:110px; height:110px; border-radius:55px; background:#F3E6FA; margin-bottom:20px; display:flex; align-items:center; justify-content:center;">
                <ion-icon name="calendar-clear-outline" style="font-size:54px; color:#B895C8;"></ion-icon>
            </div>
            <p style="font-size:17px; font-weight:700; color:#4A0E35; margin-bottom:6px;">Tidak ada aktivitas</p>
            <p style="font-size:13px; color:#A2397B;" id="emptyDesc">pada tanggal ini</p>
        </div>

        <!-- Error -->
        <div id="errorState" style="display:none; flex-direction:column; align-items:center; padding:40px 20px; gap:12px;">
            <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#B895C8;"></ion-icon>
            <p style="font-size:15px; font-weight:700; color:#4A0E35;">Gagal memuat data</p>
            <button onclick="loadDiary()" style="background:#7B1E5A; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Coba Lagi</button>
        </div>

    </div>

    @include('partials.bottom-nav', ['active' => 'diary'])
</div>
</div>

<!-- MODAL: DATE PICKER -->
<div class="modal-overlay" id="modalDatePicker">
    <div class="modal-box">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-shrink:0; padding:20px; border-bottom:2px solid #F3E6FA;">
            <span style="font-size:19px; font-weight:700; color:#4A0E35;">Pilih Tanggal</span>
            <button onclick="closeDatePicker()" style="width:32px; height:32px; border-radius:16px; background:#F3E6FA; display:flex; align-items:center; justify-content:center; cursor:pointer; border:none;">
                <ion-icon name="close" style="font-size:20px; color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div style="display:flex; flex:1; overflow:hidden; padding:10px 20px 0; gap:0;">
            <div style="display:flex; flex-direction:column; flex:1; margin:0 4px;">
                <p style="font-size:13px; font-weight:700; color:#7B1E5A; text-align:center; margin-bottom:8px;">Tahun</p>
                <div class="picker-scroll" id="pickerYear"></div>
            </div>
            <div style="display:flex; flex-direction:column; flex:1; margin:0 4px;">
                <p style="font-size:13px; font-weight:700; color:#7B1E5A; text-align:center; margin-bottom:8px;">Bulan</p>
                <div class="picker-scroll" id="pickerMonth"></div>
            </div>
            <div style="display:flex; flex-direction:column; flex:1; margin:0 4px;">
                <p style="font-size:13px; font-weight:700; color:#7B1E5A; text-align:center; margin-bottom:8px;">Tanggal</p>
                <div class="picker-scroll" id="pickerDay"></div>
            </div>
        </div>
        <div style="display:flex; flex-shrink:0; padding:10px 20px 20px; gap:10px;">
            <button onclick="closeDatePicker()" style="flex:1; padding:13px; border-radius:12px; background:#F3E6FA; font-size:15px; font-weight:600; color:#7B1E5A; cursor:pointer; border:none;">Batal</button>
            <button onclick="confirmDatePicker()" style="flex:1; padding:13px; border-radius:12px; background:#7B1E5A; font-size:15px; font-weight:700; color:#fff; cursor:pointer; border:none;">Pilih</button>
        </div>
    </div>
</div>

<!-- MODAL: DETAIL AKTIVITAS -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-shrink:0; padding:20px; border-bottom:2px solid #F3E6FA;">
            <span style="font-size:19px; font-weight:700; color:#4A0E35;">Detail Aktivitas</span>
            <button onclick="closeDetail()" style="width:32px; height:32px; border-radius:16px; background:#F3E6FA; display:flex; align-items:center; justify-content:center; cursor:pointer; border:none;">
                <ion-icon name="close" style="font-size:20px; color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div class="no-scrollbar" id="detailBody" style="flex:1; overflow-y:auto; padding:20px;"></div>
        <div style="flex-shrink:0; padding:0 20px 20px;">
            <button onclick="closeDetail()" style="width:100%; background:#7B1E5A; padding:15px; border-radius:16px; font-size:15px; font-weight:700; color:#fff; cursor:pointer; border:none;">Tutup</button>
        </div>
    </div>
</div>

<script>
// ── Config ─────────────────────────────────────────────────────────────────
var API_BASE_URL = '{{ env("API_BASE_URL") }}';
var API_TOKEN    = '{{ session("token") }}';
var MONTHS_ID    = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var KAT_COLOR    = { makan:'#FF6B6B', tidur:'#4ECDC4', main:'#FFD93D', belajar:'#6BCB77', mandi:'#95B8D1' };
var KAT_ICON     = { makan:'restaurant', tidur:'bed', main:'football', belajar:'school', mandi:'water' };

// ── Parse URL: /admin/diary/{id_nanny}/anak/{id_anak} ──────────────────────
var _parts   = window.location.pathname.split('/').filter(Boolean);
var _dIdx    = _parts.indexOf('diary');
var ID_NANNY = (_dIdx !== -1 && _parts[_dIdx + 1]) ? _parts[_dIdx + 1] : null;
var ID_ANAK  = _parts[_parts.length - 1] || null;

// ── State ──────────────────────────────────────────────────────────────────
var currentDate = new Date();   // default = hari ini
var activeKat   = '';
var tY, tM, tD;

// ── Clock ──────────────────────────────────────────────────────────────────
function pad(n) { return String(n).padStart(2, '0'); }

function updateClock() {
    var el = document.getElementById('statusTime');
    if (el) {
        var now = new Date();
        el.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes());
    }
}
updateClock();
setInterval(updateClock, 30000);

// ── Date helpers ───────────────────────────────────────────────────────────
function fmtDate(d) {
    return pad(d.getDate()) + ' ' + MONTHS_ID[d.getMonth()] + ' ' + d.getFullYear();
}

function fmtYMD(d) {
    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
}

function fmtTime(dt) {
    if (!dt) return '-';
    var d = new Date(dt);
    return isNaN(d.getTime()) ? String(dt) : pad(d.getHours()) + ':' + pad(d.getMinutes());
}

// durasi dari API adalah object {jam, menit, total_menit}
function parseDurasi(durasi, jam_mulai, jam_selesai) {
    if (durasi && typeof durasi === 'object') {
        var j = durasi.jam   || 0;
        var m = durasi.menit || 0;
        if (j > 0 && m > 0) return j + ' jam ' + m + ' menit';
        if (j > 0)          return j + ' jam';
        if (m > 0)          return m + ' menit';
        var tot = durasi.total_menit || 0;
        return tot > 0 ? tot + ' menit' : '-';
    }
    if (durasi && typeof durasi === 'string') return durasi;
    // fallback hitung dari selisih waktu
    if (!jam_mulai || !jam_selesai) return '-';
    var a = new Date(jam_mulai), b = new Date(jam_selesai);
    if (isNaN(a.getTime()) || isNaN(b.getTime())) return '-';
    var diff = Math.abs(b - a);
    var h = Math.floor(diff / 3600000);
    var m2 = Math.floor((diff % 3600000) / 60000);
    return h > 0 ? (h + ' jam ' + m2 + ' menit') : (m2 + ' menit');
}

function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

// ── DOM helpers ────────────────────────────────────────────────────────────
function showEl(id, flex) {
    var el = document.getElementById(id);
    if (el) el.style.display = flex ? 'flex' : 'block';
}
function hideEl(id) {
    var el = document.getElementById(id);
    if (el) el.style.display = 'none';
}

// ── Back ───────────────────────────────────────────────────────────────────
document.getElementById('btnBack').addEventListener('click', function () {
    window.location.href = '/admin/diary/' + ID_NANNY + '/anak';
});

// ── Update tanggal label di header date selector ───────────────────────────
function updateDateLabel() {
    document.getElementById('tanggalLabel').textContent = fmtDate(currentDate);
}

// ── Main fetch: dipanggil setiap kali tanggal atau kategori berubah ─────────
async function loadDiary() {
    if (!ID_NANNY || !ID_ANAK) {
        showEl('errorState', true);
        return;
    }

    // Tampilkan skeleton, sembunyikan yang lain
    showEl('skeletonList');
    hideEl('aktList');
    hideEl('emptyState');
    hideEl('errorState');
    document.getElementById('totalLabel').textContent = '0 aktivitas';

    try {
        // Kirim tanggal & kategori sebagai query param
        var params = new URLSearchParams({
            id_anak  : ID_ANAK,
            id_nanny : ID_NANNY,
            tanggal  : fmtYMD(currentDate)
        });
        if (activeKat) params.set('kategori', activeKat);

        var res = await fetch(API_BASE_URL + '/diary-for-admin?' + params.toString(), {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
                'Accept'       : 'application/json',
                'Content-Type' : 'application/x-www-form-urlencoded'
            }
        });
        var data = await res.json();

        if (data.status === 'success' && data.data) {
            // Update header hanya jika pertama kali (isi sudah ada nama)
            var namaAnak = data.data.nama_anak || 'Anak';
            document.getElementById('judulAnak').textContent = 'Diary ' + namaAnak;

            // Ambil aktivitas dari tanggal yang diminta (API sudah filter by tanggal)
            var groups = data.data.aktivitas_per_tanggal || [];
            var list   = [];
            groups.forEach(function (g) {
                (g.aktivitas || []).forEach(function (a) { list.push(a); });
            });

            // Ambil nama nanny dari item pertama (satu kali)
            if (list.length > 0 && list[0].nanny_name) {
                document.getElementById('subtitleNanny').textContent = 'Nanny: ' + list[0].nanny_name;
            } else {
                document.getElementById('subtitleNanny').textContent = 'Dicatat oleh Nanny';
            }

            document.getElementById('totalLabel').textContent = list.length + ' aktivitas';
            renderAktivitas(list);
        } else {
            hideEl('skeletonList');
            showEl('errorState', true);
        }
    } catch (e) {
        console.error('[loadDiary]', e);
        hideEl('skeletonList');
        showEl('errorState', true);
    }
}

// ── Render list aktivitas ──────────────────────────────────────────────────
function renderAktivitas(list) {
    hideEl('skeletonList');
    hideEl('errorState');

    if (list.length === 0) {
        hideEl('aktList');
        showEl('emptyState', true);
        document.getElementById('emptyDesc').textContent = activeKat
            ? 'kategori "' + activeKat + '" pada tanggal ini'
            : 'pada tanggal ini';
        return;
    }

    hideEl('emptyState');
    var container = document.getElementById('aktList');
    showEl('aktList');

    container.innerHTML = list.map(function (item, i) {
        var bg      = KAT_COLOR[item.kategori] || '#B895C8';
        var ico     = KAT_ICON[item.kategori]  || 'calendar';
        var mulai   = fmtTime(item.jam_mulai);
        var selesai = fmtTime(item.jam_selesai);
        var durasi  = parseDurasi(item.durasi, item.jam_mulai, item.jam_selesai);
        var encoded = encodeURIComponent(JSON.stringify(item));

        return '<div class="akt-card"'
             + ' style="display:flex; align-items:center; justify-content:space-between; background:#fff;'
             + ' padding:16px; border-radius:16px; margin-bottom:12px; border:2px solid #F3E6FA;'
             + ' border-left:4px solid ' + bg + '; animation:slideUp .25s ease ' + (i * 0.05) + 's both; opacity:0;"'
             + ' onclick="openDetail(JSON.parse(decodeURIComponent(\'' + encoded + '\')))">'

             + '<div style="display:flex; align-items:center; flex:1; min-width:0;">'
             +   '<div style="width:50px; height:50px; border-radius:25px; background:' + bg + '20;'
             +        ' margin-right:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">'
             +     '<ion-icon name="' + ico + '" style="font-size:24px; color:' + bg + ';"></ion-icon>'
             +   '</div>'
             +   '<div style="flex:1; min-width:0;">'
             +     '<p style="font-size:15px; font-weight:700; color:#4A0E35; margin-bottom:3px;">' + ucFirst(item.kategori) + '</p>'
             +     '<p style="font-size:13px; color:#7B1E5A; font-weight:500; margin-bottom:2px;">' + mulai + ' – ' + selesai + '</p>'
             +     '<p style="font-size:12px; color:#A2397B; font-weight:500;">Durasi: ' + durasi + '</p>'
             +     (item.nanny_name ? '<p style="font-size:11px; color:#B895C8; font-weight:500; margin-top:2px;">👤 ' + item.nanny_name + '</p>' : '')
             +   '</div>'
             + '</div>'
             + '<div style="width:32px; height:32px; border-radius:16px; background:#F3E6FA; margin-left:8px;'
             +      ' display:flex; align-items:center; justify-content:center; flex-shrink:0;">'
             +   '<ion-icon name="chevron-forward" style="font-size:20px; color:#7B1E5A;"></ion-icon>'
             + '</div>'
             + '</div>';
    }).join('');
}

// ── Kategori filter → refetch ──────────────────────────────────────────────
function setKategori(btn) {
    activeKat = btn.getAttribute('data-kat');
    document.querySelectorAll('.filter-chip').forEach(function (b) {
        var on = (b === btn);
        b.style.background  = on ? '#7B1E5A' : '#fff';
        b.style.color       = on ? '#fff'    : '#7B1E5A';
        b.style.borderColor = on ? '#7B1E5A' : '#F3E6FA';
        b.style.fontWeight  = on ? '700'     : '600';
    });
    loadDiary();
}

// ── Date navigation → update tanggal lalu refetch ─────────────────────────
document.getElementById('btnPrev').addEventListener('click', function () {
    currentDate.setDate(currentDate.getDate() - 1);
    updateDateLabel();
    loadDiary();
});
document.getElementById('btnNext').addEventListener('click', function () {
    currentDate.setDate(currentDate.getDate() + 1);
    updateDateLabel();
    loadDiary();
});

// ── Date Picker ────────────────────────────────────────────────────────────
function daysInMonth(y, m) { return new Date(y, m + 1, 0).getDate(); }

function buildPicker(id, items, active, cb) {
    var el = document.getElementById(id);
    el.innerHTML = '';
    items.forEach(function (item) {
        var d = document.createElement('div');
        d.className   = 'picker-item' + (item.v === active ? ' active' : '');
        d.textContent = item.l;
        d.onclick = function () {
            el.querySelectorAll('.picker-item').forEach(function (x) { x.classList.remove('active'); });
            d.classList.add('active');
            cb(item.v);
            d.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        };
        el.appendChild(d);
    });
    var activeEl = el.querySelector('.active');
    if (activeEl) setTimeout(function () { activeEl.scrollIntoView({ block: 'center' }); }, 60);
}

function buildDayPicker() {
    var total = daysInMonth(tY, tM);
    if (tD > total) tD = total;
    buildPicker('pickerDay',
        Array.from({ length: total }, function (_, i) { return { v: i + 1, l: String(i + 1) }; }),
        tD, function (v) { tD = v; }
    );
}

function openDatePicker() {
    tY = currentDate.getFullYear();
    tM = currentDate.getMonth();
    tD = currentDate.getDate();
    var curY = new Date().getFullYear();
    buildPicker('pickerYear',
        Array.from({ length: 8 }, function (_, i) { return { v: curY - 6 + i, l: String(curY - 6 + i) }; }),
        tY, function (v) { tY = v; buildDayPicker(); }
    );
    buildPicker('pickerMonth',
        MONTHS_ID.map(function (m, i) { return { v: i, l: m }; }),
        tM, function (v) { tM = v; buildDayPicker(); }
    );
    buildDayPicker();
    document.getElementById('modalDatePicker').classList.add('open');
}

function closeDatePicker() { document.getElementById('modalDatePicker').classList.remove('open'); }

function confirmDatePicker() {
    currentDate = new Date(tY, tM, tD);
    closeDatePicker();
    updateDateLabel();
    loadDiary();  // refetch dengan tanggal baru dari picker
}

document.getElementById('btnDatePicker').addEventListener('click', openDatePicker);
document.getElementById('modalDatePicker').addEventListener('click', function (e) {
    if (e.target.id === 'modalDatePicker') closeDatePicker();
});

// ── Detail Modal ───────────────────────────────────────────────────────────
function getMoodEmoji(m) {
    return ({ senang:'😊', sedih:'😢', marah:'😠', biasa:'😐' })[m] || '😊';
}

function detailRow(iconName, label, value, isLast) {
    var sep = isLast ? '' : 'margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #F3E6FA;';
    return '<div style="display:flex; align-items:flex-start; ' + sep + '">'
         +   '<div style="width:36px; height:36px; border-radius:10px; background:#F3E6FA;'
         +        ' display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0;">'
         +     '<ion-icon name="' + iconName + '" style="font-size:18px; color:#7B1E5A;"></ion-icon>'
         +   '</div>'
         +   '<div style="flex:1;">'
         +     '<p style="font-size:11px; color:#A2397B; font-weight:600; margin-bottom:4px;">' + label + '</p>'
         +     '<p style="font-size:14px; color:#4A0E35; font-weight:500; line-height:20px;">' + (value || '-') + '</p>'
         +   '</div>'
         + '</div>';
}

function openDetail(item) {
    var bg      = KAT_COLOR[item.kategori] || '#B895C8';
    var ico     = KAT_ICON[item.kategori]  || 'calendar';
    var mulai   = fmtTime(item.jam_mulai);
    var selesai = fmtTime(item.jam_selesai);
    var durasi  = parseDurasi(item.durasi, item.jam_mulai, item.jam_selesai);

    var rows = [
        { icon: 'time-outline',          label: 'Waktu Mulai',   value: mulai   },
        { icon: 'time-outline',          label: 'Waktu Selesai', value: selesai },
        { icon: 'hourglass',             label: 'Durasi',        value: durasi  },
        { icon: 'person-outline',        label: 'Dicatat Oleh',  value: item.nanny_name || '-' }
    ];
    if (item.mood)      rows.push({ emoji: getMoodEmoji(item.mood), label: 'Mood',       value: ucFirst(item.mood) });
    if (item.deskripsi) rows.push({ icon: 'document-text-outline', label: 'Deskripsi',  value: item.deskripsi });

    var html = '<div style="display:flex; flex-direction:column; align-items:center; padding:20px;'
             + ' border-radius:16px; margin-bottom:20px; background:' + bg + '20;">'
             + '<ion-icon name="' + ico + '" style="font-size:40px; color:' + bg + ';"></ion-icon>'
             + '<p style="font-size:21px; font-weight:700; color:#4A0E35; margin-top:10px;">' + ucFirst(item.kategori) + '</p>'
             + '</div>';

    rows.forEach(function (r, idx) {
        var isLast = (idx === rows.length - 1) && !item.foto_url;
        var sep    = isLast ? '' : 'margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #F3E6FA;';

        if (r.emoji) {
            html += '<div style="display:flex; align-items:flex-start; ' + sep + '">'
                  +   '<div style="width:36px; height:36px; border-radius:10px; background:#F3E6FA;'
                  +        ' display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0;">'
                  +     '<span style="font-size:20px;">' + r.emoji + '</span>'
                  +   '</div>'
                  +   '<div style="flex:1;">'
                  +     '<p style="font-size:11px; color:#A2397B; font-weight:600; margin-bottom:4px;">' + r.label + '</p>'
                  +     '<p style="font-size:14px; color:#4A0E35; font-weight:500;">' + (r.value || '-') + '</p>'
                  +   '</div>'
                  + '</div>';
        } else {
            html += detailRow(r.icon, r.label, r.value, isLast);
        }
    });

    if (item.foto_url) {
        html += '<div style="margin-top:10px;">'
              +   '<p style="font-size:11px; color:#A2397B; font-weight:600; margin-bottom:8px;">FOTO</p>'
              +   '<img src="' + item.foto_url + '" loading="lazy"'
              +        ' style="width:100%; height:200px; border-radius:16px; object-fit:cover; background:#F3E6FA; border:2px solid #F3E6FA;">'
              + '</div>';
    }

    document.getElementById('detailBody').innerHTML = html;
    document.getElementById('modalDetail').classList.add('open');
}

function closeDetail() { document.getElementById('modalDetail').classList.remove('open'); }
document.getElementById('modalDetail').addEventListener('click', function (e) {
    if (e.target.id === 'modalDetail') closeDetail();
});

// ── Init: set tanggal hari ini lalu fetch ──────────────────────────────────
updateDateLabel();
(async function () { await loadDiary(); })();
</script>

@include('partials.auth-guard')
</body>
</html>