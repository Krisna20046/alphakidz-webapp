@extends('layouts.app')

@section('title', 'Child Diary – Admin')

@push('styles')
<style>
    @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
    .skeleton { background:linear-gradient(90deg,#f0dcea 25%,#fce8f5 50%,#f0dcea 75%); background-size:400px 100%; animation:shimmer 1.4s infinite; border-radius:12px; }

    .akt-card { transition:opacity .15s, transform .15s; cursor:pointer; }
    .akt-card:hover  { opacity:.85; }
    .akt-card:active { transform:scale(0.98); opacity:.7; }

    .filter-chip { cursor:pointer; transition:background .15s, color .15s, border-color .15s; white-space:nowrap; }
    .filter-chip:active { transform:scale(0.95); }

    .date-arrow { cursor:pointer; border:none; background:transparent; transition:background .15s; border-radius:10px; padding:8px; display:flex; align-items:center; justify-content:center; }
    .date-arrow:hover { background:#EDE9FE; }

    @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

    .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; padding:20px; z-index:50; opacity:0; pointer-events:none; transition:opacity .2s ease; }
    .modal-overlay.open { opacity:1; pointer-events:auto; }
    .modal-box { background:#fff; border-radius:24px; width:100%; max-height:82vh; overflow:hidden; transform:translateY(20px); transition:transform .25s ease; display:flex; flex-direction:column; max-width:390px; }
    .modal-overlay.open .modal-box { transform:translateY(0); }

    .picker-scroll { height:200px; overflow-y:auto; border-radius:12px; background:#F8F7FF; }
    .picker-scroll::-webkit-scrollbar { display:none; }
    .picker-scroll { -ms-overflow-style:none; scrollbar-width:none; }
    .picker-item { padding:10px 12px; text-align:center; border-radius:8px; cursor:pointer; margin:2px 4px; font-size:14px; color:#1E1B2E; font-weight:500; transition:background .12s; }
    .picker-item.active { background:#8B46D3; color:#fff; font-weight:700; }
    .picker-item:hover:not(.active) { background:#EDE9FE; }

    .badge-admin { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; background:rgba(237,233,254,0.9); color:#8B46D3; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex flex-col items-center relative z-10">
        <button id="btnBack"
                class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
                style="top:0; left:0; width:40px; height:40px; z-index:10; border:none; cursor:pointer;">
            <ion-icon name="arrow-back" style="font-size:20px; color:#fff;"></ion-icon>
        </button>
        <div class="flex items-center justify-center bg-white rounded-full mb-3 shadow-lg" style="width:64px; height:64px;">
            <ion-icon name="book" style="font-size:30px; color:#8B46D3;"></ion-icon>
        </div>
        <h1 class="font-extrabold text-white mb-1" style="font-size:22px; letter-spacing:.4px;" id="judulAnak">Child Diary</h1>
        <p id="subtitleNanny" style="font-size:13px; color:#E5DEFF; font-weight:500;">Loading data...</p>
        <div class="badge-admin mt-2">
            <ion-icon name="shield-checkmark-outline" style="font-size:12px;"></ion-icon>
            <span>Admin View</span>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    <!-- FILTER KATEGORI -->
    <div class="no-scrollbar flex" style="overflow-x:auto; gap:10px;">
        <button class="filter-chip" data-kat="" onclick="setKategori(this)"
                style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #8B46D3; background:#8B46D3; font-weight:700; color:#fff; flex-shrink:0;">
            All
        </button>
        <button class="filter-chip" data-kat="makan" onclick="setKategori(this)"
                style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #EDE9FE; background:#fff; font-weight:600; color:#8B46D3; flex-shrink:0;">
            🍽 Eat
        </button>
        <button class="filter-chip" data-kat="tidur" onclick="setKategori(this)"
                style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #EDE9FE; background:#fff; font-weight:600; color:#8B46D3; flex-shrink:0;">
            😴 Sleep
        </button>
        <button class="filter-chip" data-kat="main" onclick="setKategori(this)"
                style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #EDE9FE; background:#fff; font-weight:600; color:#8B46D3; flex-shrink:0;">
            ⚽ Play
        </button>
        <button class="filter-chip" data-kat="belajar" onclick="setKategori(this)"
                style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #EDE9FE; background:#fff; font-weight:600; color:#8B46D3; flex-shrink:0;">
            📚 Study
        </button>
        <button class="filter-chip" data-kat="mandi" onclick="setKategori(this)"
                style="padding:8px 16px; border-radius:20px; font-size:13px; border:2px solid #EDE9FE; background:#fff; font-weight:600; color:#8B46D3; flex-shrink:0;">
            🛁 Bath
        </button>
    </div>

    <!-- DATE SELECTOR -->
    <div class="bg-white rounded-2xl p-4" style="border:2px solid #EDE9FE;">
        <div class="flex items-center justify-between">
            <button class="date-arrow" id="btnPrev">
                <ion-icon name="chevron-back" style="font-size:26px; color:#8B46D3;"></ion-icon>
            </button>
            <button id="btnDatePicker" class="flex flex-col items-center"
                    style="flex:1; background:transparent; cursor:pointer; border:none;">
                <span style="font-size:15px; font-weight:700; color:#1E1B2E;" id="tanggalLabel">–</span>
                <span style="font-size:12px; color:#8B86A5; margin-top:3px; font-weight:500;" id="totalLabel">0 activities</span>
            </button>
            <button class="date-arrow" id="btnNext">
                <ion-icon name="chevron-forward" style="font-size:26px; color:#8B46D3;"></ion-icon>
            </button>
        </div>
    </div>

    <!-- CONTENT -->
    <!-- Skeleton -->
    <div id="skeletonList" style="display:none;">
        @for($i = 0; $i < 4; $i++)
        <div class="flex items-center bg-white mb-3" style="border-radius:16px; padding:16px; border:2px solid #EDE9FE; gap:12px;">
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
        <div class="float-anim" style="width:110px; height:110px; border-radius:55px; background:#EDE9FE; margin-bottom:20px; display:flex; align-items:center; justify-content:center;">
            <ion-icon name="calendar-clear-outline" style="font-size:54px; color:#C4B5FD;"></ion-icon>
        </div>
        <p style="font-size:17px; font-weight:700; color:#1E1B2E; margin-bottom:6px;">No activities</p>
        <p style="font-size:13px; color:#8B86A5;" id="emptyDesc">on this date</p>
    </div>

    <!-- Error -->
    <div id="errorState" style="display:none; flex-direction:column; align-items:center; padding:40px 20px; gap:12px;">
        <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#C4B5FD;"></ion-icon>
        <p style="font-size:15px; font-weight:700; color:#1E1B2E;">Failed to load data</p>
        <button onclick="loadDiary()" style="background:#8B46D3; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Try Again</button>
    </div>

</div>

<!-- MODAL: DATE PICKER -->
<div class="modal-overlay" id="modalDatePicker">
    <div class="modal-box">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-shrink:0; padding:20px; border-bottom:2px solid #EDE9FE;">
            <span style="font-size:19px; font-weight:700; color:#1E1B2E;">Select Date</span>
            <button onclick="closeDatePicker()" style="width:32px; height:32px; border-radius:16px; background:#EDE9FE; display:flex; align-items:center; justify-content:center; cursor:pointer; border:none;">
                <ion-icon name="close" style="font-size:20px; color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div style="display:flex; flex:1; overflow:hidden; padding:10px 20px 0; gap:0;">
            <div style="display:flex; flex-direction:column; flex:1; margin:0 4px;">
                <p style="font-size:13px; font-weight:700; color:#8B46D3; text-align:center; margin-bottom:8px;">Year</p>
                <div class="picker-scroll" id="pickerYear"></div>
            </div>
            <div style="display:flex; flex-direction:column; flex:1; margin:0 4px;">
                <p style="font-size:13px; font-weight:700; color:#8B46D3; text-align:center; margin-bottom:8px;">Month</p>
                <div class="picker-scroll" id="pickerMonth"></div>
            </div>
            <div style="display:flex; flex-direction:column; flex:1; margin:0 4px;">
                <p style="font-size:13px; font-weight:700; color:#8B46D3; text-align:center; margin-bottom:8px;">Date</p>
                <div class="picker-scroll" id="pickerDay"></div>
            </div>
        </div>
        <div style="display:flex; flex-shrink:0; padding:10px 20px 20px; gap:10px;">
            <button onclick="closeDatePicker()" style="flex:1; padding:13px; border-radius:12px; background:#EDE9FE; font-size:15px; font-weight:600; color:#8B46D3; cursor:pointer; border:none;">Cancel</button>
            <button onclick="confirmDatePicker()" style="flex:1; padding:13px; border-radius:12px; background:#8B46D3; font-size:15px; font-weight:700; color:#fff; cursor:pointer; border:none;">Select</button>
        </div>
    </div>
</div>

<!-- MODAL: DETAIL AKTIVITAS -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-shrink:0; padding:20px; border-bottom:2px solid #EDE9FE;">
            <span style="font-size:19px; font-weight:700; color:#1E1B2E;">Activity Details</span>
            <button onclick="closeDetail()" style="width:32px; height:32px; border-radius:16px; background:#EDE9FE; display:flex; align-items:center; justify-content:center; cursor:pointer; border:none;">
                <ion-icon name="close" style="font-size:20px; color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="no-scrollbar" id="detailBody" style="flex:1; overflow-y:auto; padding:20px;"></div>
        <div style="flex-shrink:0; padding:0 20px 20px;">
            <button onclick="closeDetail()" style="width:100%; background:#8B46D3; padding:15px; border-radius:16px; font-size:15px; font-weight:700; color:#fff; cursor:pointer; border:none;">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Config ─────────────────────────────────────────────────────────────────
var API_BASE_URL = '{{ env("API_BASE_URL") }}';
var API_TOKEN    = '{{ session("token") }}';
var MONTHS_ID    = ['January','February','March','April','May','June','July','August','September','October','November','December'];
var KAT_COLOR    = { makan:'#FF6B6B', tidur:'#4ECDC4', main:'#FFD93D', belajar:'#6BCB77', mandi:'#95B8D1' };
var KAT_ICON     = { makan:'restaurant', tidur:'bed', main:'football', belajar:'school', mandi:'water' };

// ── Parse URL: /admin/diary/{id_nanny}/anak/{id_anak} ──────────────────────
var _parts   = window.location.pathname.split('/').filter(Boolean);
var _dIdx    = _parts.indexOf('diary');
var ID_NANNY = (_dIdx !== -1 && _parts[_dIdx + 1]) ? _parts[_dIdx + 1] : null;
var ID_ANAK  = _parts[_parts.length - 1] || null;

// ── State ──────────────────────────────────────────────────────────────────
var currentDate = new Date();
var activeKat   = '';
var tY, tM, tD;

// ── Date helpers ───────────────────────────────────────────────────────────
function fmtDate(d) {
    return String(d.getDate()).padStart(2,'0') + ' ' + MONTHS_ID[d.getMonth()] + ' ' + d.getFullYear();
}

function fmtYMD(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}

function fmtTime(dt) {
    if (!dt) return '-';
    var d = new Date(dt);
    return isNaN(d.getTime()) ? String(dt) : String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
}

function parseDurasi(durasi, jam_mulai, jam_selesai) {
    if (durasi && typeof durasi === 'object') {
        var j = durasi.jam   || 0;
        var m = durasi.menit || 0;
        if (j > 0 && m > 0) return j + ' hr ' + m + ' min';
        if (j > 0)          return j + ' hr';
        if (m > 0)          return m + ' min';
        var tot = durasi.total_menit || 0;
        return tot > 0 ? tot + ' min' : '-';
    }
    if (durasi && typeof durasi === 'string') return durasi;
    if (!jam_mulai || !jam_selesai) return '-';
    var a = new Date(jam_mulai), b = new Date(jam_selesai);
    if (isNaN(a.getTime()) || isNaN(b.getTime())) return '-';
    var diff = Math.abs(b - a);
    var h = Math.floor(diff / 3600000);
    var m2 = Math.floor((diff % 3600000) / 60000);
    return h > 0 ? (h + ' hr ' + m2 + ' min') : (m2 + ' min');
}

function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

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

// ── Update tanggal label ───────────────────────────────────────────────────
function updateDateLabel() {
    document.getElementById('tanggalLabel').textContent = fmtDate(currentDate);
}

// ── Main fetch ─────────────────────────────────────────────────────────────
async function loadDiary() {
    if (!ID_NANNY || !ID_ANAK) {
        showEl('errorState', true);
        return;
    }

    showEl('skeletonList');
    hideEl('aktList');
    hideEl('emptyState');
    hideEl('errorState');
    document.getElementById('totalLabel').textContent = '0 activities';

    try {
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
            var namaAnak = data.data.nama_anak || 'Child';
            document.getElementById('judulAnak').textContent = 'Diary ' + namaAnak;

            var groups = data.data.aktivitas_per_tanggal || [];
            var list   = [];
            groups.forEach(function (g) {
                (g.aktivitas || []).forEach(function (a) { list.push(a); });
            });

            if (list.length > 0 && list[0].nanny_name) {
                document.getElementById('subtitleNanny').textContent = 'Nanny: ' + list[0].nanny_name;
            } else {
                document.getElementById('subtitleNanny').textContent = 'Recorded by Nanny';
            }

            document.getElementById('totalLabel').textContent = list.length + ' activities';
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

// ── Render ─────────────────────────────────────────────────────────────────
function renderAktivitas(list) {
    hideEl('skeletonList');
    hideEl('errorState');

    if (list.length === 0) {
        hideEl('aktList');
        showEl('emptyState', true);
        document.getElementById('emptyDesc').textContent = activeKat
            ? 'category "' + activeKat + '" on this date'
            : 'on this date';
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
             + ' padding:16px; border-radius:16px; margin-bottom:12px; border:2px solid #EDE9FE;'
             + ' border-left:4px solid ' + bg + '; animation:slideUp .25s ease ' + (i * 0.05) + 's both; opacity:0;"'
             + ' onclick="openDetail(JSON.parse(decodeURIComponent(\'' + encoded + '\')))">'

             + '<div style="display:flex; align-items:center; flex:1; min-width:0;">'
             +   '<div style="width:50px; height:50px; border-radius:25px; background:' + bg + '20;'
             +        ' margin-right:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">'
             +     '<ion-icon name="' + ico + '" style="font-size:24px; color:' + bg + ';"></ion-icon>'
             +   '</div>'
             +   '<div style="flex:1; min-width:0;">'
             +     '<p style="font-size:15px; font-weight:700; color:#1E1B2E; margin-bottom:3px;">' + ucFirst(item.kategori) + '</p>'
             +     '<p style="font-size:13px; color:#8B46D3; font-weight:500; margin-bottom:2px;">' + mulai + ' – ' + selesai + '</p>'
             +     '<p style="font-size:12px; color:#8B86A5; font-weight:500;">Duration: ' + durasi + '</p>'
             +     (item.nanny_name ? '<p style="font-size:11px; color:#C4B5FD; font-weight:500; margin-top:2px;">👤 ' + item.nanny_name + '</p>' : '')
             +   '</div>'
             + '</div>'
             + '<div style="width:32px; height:32px; border-radius:16px; background:#EDE9FE; margin-left:8px;'
             +      ' display:flex; align-items:center; justify-content:center; flex-shrink:0;">'
             +   '<ion-icon name="chevron-forward" style="font-size:20px; color:#8B46D3;"></ion-icon>'
             + '</div>'
             + '</div>';
    }).join('');
}

// ── Kategori filter ────────────────────────────────────────────────────────
function setKategori(btn) {
    activeKat = btn.getAttribute('data-kat');
    document.querySelectorAll('.filter-chip').forEach(function (b) {
        var on = (b === btn);
        b.style.background  = on ? '#8B46D3' : '#fff';
        b.style.color       = on ? '#fff'    : '#8B46D3';
        b.style.borderColor = on ? '#8B46D3' : '#EDE9FE';
        b.style.fontWeight  = on ? '700'     : '600';
    });
    loadDiary();
}

// ── Date navigation ────────────────────────────────────────────────────────
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
    loadDiary();
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
    var sep = isLast ? '' : 'margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #EDE9FE;';
    return '<div style="display:flex; align-items:flex-start; ' + sep + '">'
         +   '<div style="width:36px; height:36px; border-radius:10px; background:#EDE9FE;'
         +        ' display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0;">'
         +     '<ion-icon name="' + iconName + '" style="font-size:18px; color:#8B46D3;"></ion-icon>'
         +   '</div>'
         +   '<div style="flex:1;">'
         +     '<p style="font-size:11px; color:#8B86A5; font-weight:600; margin-bottom:4px;">' + label + '</p>'
         +     '<p style="font-size:14px; color:#1E1B2E; font-weight:500; line-height:20px;">' + (value || '-') + '</p>'
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
        { icon: 'time-outline',          label: 'Start Time',   value: mulai   },
        { icon: 'time-outline',          label: 'End Time', value: selesai },
        { icon: 'hourglass',             label: 'Duration',        value: durasi  },
        { icon: 'person-outline',        label: 'Recorded By',  value: item.nanny_name || '-' }
    ];
    if (item.mood)      rows.push({ emoji: getMoodEmoji(item.mood), label: 'Mood',       value: ucFirst(item.mood) });
    if (item.deskripsi) rows.push({ icon: 'document-text-outline', label: 'Description',  value: item.deskripsi });

    var html = '<div style="display:flex; flex-direction:column; align-items:center; padding:20px;'
             + ' border-radius:16px; margin-bottom:20px; background:' + bg + '20;">'
             + '<ion-icon name="' + ico + '" style="font-size:40px; color:' + bg + ';"></ion-icon>'
             + '<p style="font-size:21px; font-weight:700; color:#1E1B2E; margin-top:10px;">' + ucFirst(item.kategori) + '</p>'
             + '</div>';

    rows.forEach(function (r, idx) {
        var isLast = (idx === rows.length - 1) && !item.foto_url;
        var sep    = isLast ? '' : 'margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #EDE9FE;';

        if (r.emoji) {
            html += '<div style="display:flex; align-items:flex-start; ' + sep + '">'
                  +   '<div style="width:36px; height:36px; border-radius:10px; background:#EDE9FE;'
                  +        ' display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0;">'
                  +     '<span style="font-size:20px;">' + r.emoji + '</span>'
                  +   '</div>'
                  +   '<div style="flex:1;">'
                  +     '<p style="font-size:11px; color:#8B86A5; font-weight:600; margin-bottom:4px;">' + r.label + '</p>'
                  +     '<p style="font-size:14px; color:#1E1B2E; font-weight:500;">' + (r.value || '-') + '</p>'
                  +   '</div>'
                  + '</div>';
        } else {
            html += detailRow(r.icon, r.label, r.value, isLast);
        }
    });

    if (item.foto_url) {
        html += '<div style="margin-top:10px;">'
              +   '<p style="font-size:11px; color:#8B86A5; font-weight:600; margin-bottom:8px;">PHOTO</p>'
              +   '<img src="' + item.foto_url + '" loading="lazy"'
              +        ' style="width:100%; height:200px; border-radius:16px; object-fit:cover; background:#EDE9FE; border:2px solid #EDE9FE;">'
              + '</div>';
    }

    document.getElementById('detailBody').innerHTML = html;
    document.getElementById('modalDetail').classList.add('open');
}

function closeDetail() { document.getElementById('modalDetail').classList.remove('open'); }
document.getElementById('modalDetail').addEventListener('click', function (e) {
    if (e.target.id === 'modalDetail') closeDetail();
});

// ── Init ───────────────────────────────────────────────────────────────────
updateDateLabel();
(async function () { await loadDiary(); })();
</script>
@endpush
