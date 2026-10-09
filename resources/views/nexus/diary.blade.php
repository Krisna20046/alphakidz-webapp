@extends('layouts.app')

@section('title', 'Diary Nanny')

@push('styles')
<style>
    * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

    @keyframes slideUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim  { animation:slideUp .4s ease forwards; opacity:0; }
    .d1    { animation-delay:.05s; }
    .d2    { animation-delay:.12s; }
    .d3    { animation-delay:.19s; }
    .d4    { animation-delay:.26s; }

    @keyframes floatEmpty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

    .hide-scrollbar::-webkit-scrollbar{display:none;}
    .hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}

    /* ── Nanny avatar row ── */
    .nanny-scroll { display:flex; gap:14px; overflow-x:auto;}
    .nanny-scroll::-webkit-scrollbar { display:none; }
    .nanny-scroll { -ms-overflow-style:none; scrollbar-width:none; }
    .nanny-item { display:flex; flex-direction:column; align-items:center; gap:5px; flex-shrink:0; cursor:pointer; }
    .nanny-ring {
        width: 66px; height: 66px; border-radius: 50%;
        border: 2.5px solid rgba(255, 255, 255, 0.25);
        padding: 2px;
        transition: border-color .2s;
    }
    .nanny-item.active .nanny-ring { border-color: rgba(124, 58, 237, .28); }
    .nanny-avatar {
        width: 100%; height: 100%; border-radius: 50%;
        object-fit: cover; background: rgba(255,255,255,0.15);
        display: block;
    }
    .nanny-avatar-placeholder {
        width: 100%; height: 100%; border-radius: 50%;
        background: rgba(255,255,255,0.18);
        display: flex; align-items: center; justify-content: center;
    }
    .nanny-name {
        font-size: 12px; font-weight: 700;
        color: rgba(255,255,255,0.6);
        max-width: 72px; text-align: center;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        transition: color .2s;
    }
    .nanny-item.active .nanny-name { color: #fff; }

    /* ── Calendar ── */
    .cal-wrap {
        background: #fff;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 2px 14px rgba(139,70,211,.08);
    }
    .cal-month-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
    .cal-month-btn {
        font-size: 18px; font-weight: 900; color: #1E1B2E;
        background: none; border: none; cursor: pointer; padding: 0;
        font-family: 'Nunito', sans-serif;
    }
    .cal-month-btn:hover { color: #8B46D3; }
    .full-month-btn {
        font-size: 13px; font-weight: 800; color: #8B46D3;
        background: none; border: none; cursor: pointer; padding: 0;
        font-family: 'Nunito', sans-serif;
    }
    .weekdays { display:grid; grid-template-columns:repeat(7,1fr); text-align:center; margin-bottom:4px; }
    .weekday  { font-size:11px; font-weight:800; color: #A8A2C2; padding:2px 0; }
    .days-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
    .day-cell {
        display:flex; align-items:center; justify-content:center;
        height:34px; border-radius:10px;
        font-size:14px; font-weight:700; color:#1E1B2E;
        cursor:pointer; transition:background .12s;
        font-family: 'Nunito', sans-serif;
    }
    .day-cell:hover    { background:#EDE9FE; }
    .day-cell.today    { background:transparent; color:#1E1B2E; border: 1.5px solid #8B46D3;}
    .day-cell.selected { background:#8B46D3; color:#fff; }
    .day-cell.other-month { color:#C4B5FD; font-weight:600; }
    .day-cell { position:relative; }

    .filter-chip {
        cursor:pointer; transition:background .15s,color .15s;
        white-space:nowrap; flex-shrink:0;
        padding:7px 14px; border-radius:20px;
        font-size:13px; font-weight:700;
        font-family:'Nunito',sans-serif;
    }
    .filter-chip:active { transform:scale(0.95); }

    /* ── Timeline ── */
    .timeline-section { display:flex; flex-direction:column; gap:12px; }
    .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
    .section-title { font-size:17px; font-weight:900; color:#1E1B2E; }
    .section-date  { font-size:12px; font-weight:700; color:#A8A2C2; }

    .timeline-row { display:flex; gap:10px; }
    .tl-left { display:flex; flex-direction:column; align-items:center; width:56px; flex-shrink:0; }
    .tl-time { text-align:center; }
    .tl-time .hh { font-size:13px; font-weight:900; color:#1E1B2E; line-height:1.1; }
    .tl-time .ampm { font-size:10px; font-weight:700; color:#A8A2C2; line-height:1; }
    .tl-dot { width:13px; height:13px; border-radius:50%; margin:5px 0 2px; flex-shrink:0; }
    .tl-line { width:2px; flex:1; min-height:16px; background:linear-gradient(to bottom,rgba(139,70,211,.2),rgba(139,70,211,.04)); margin:0 auto; }
    .tl-right { flex:1; }

    /* ── Activity card ── */
    .akt-card {
        border-radius: 14px;
        padding: 14px;
        border: 1.5px solid transparent;
        position: relative;
        overflow: hidden;
    }
    .akt-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
    .akt-cat { display:flex; align-items:center; gap:7px; }
    .akt-icon { font-size:18px; }
    .akt-title { font-size:15px; font-weight:900; color:#1E1B2E; }
    .akt-desc  { font-size:13px; font-weight:600; color:#5A556E; line-height:1.5; margin-bottom:8px; }
    .akt-photo { width:100%; height:110px; object-fit:cover; border-radius:10px; margin-bottom:8px; background:#F0EDFB; display:block; cursor:pointer; transition:transform 0.2s ease, box-shadow 0.2s ease; }
    .akt-photo:hover { transform:scale(1.02); box-shadow:0 8px 20px rgba(139,70,211,0.2); }
    .akt-footer { display:flex; align-items:center; justify-content:flex-end; }
    .loc-btn {
        display:flex; align-items:center; gap:4px;
        font-size:12px; font-weight:700; color:#A8A2C2;
        background:none; border:none; cursor:pointer;
        font-family:'Nunito',sans-serif;
        transition:color .15s;
    }
    .loc-btn:hover { color:#8B46D3; }
    .tl-duration { font-size:11px; font-weight:700; color:#A8A2C2; margin-top:6px; }

    /* ── AI Summary cards ── */
    .ai-summary-card { background:#fff; border-radius:18px; padding:18px; box-shadow:0 2px 14px rgba(139,70,211,.08); }
    .ai-summary-card .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
    .ai-summary-card .section-title { font-size:15px; font-weight:900; color:#1E1B2E; }
    .ai-summary-card .section-date { font-size:12px; font-weight:700; color:rgba(30,27,46,.5); }
    .ai-summary-toggle { cursor:pointer; user-select:none; }
    .ai-summary-count { font-size:11px; font-weight:700; color:rgba(30,27,46,.4); }
    .ai-chevron { font-size:14px; color:#8B46D3; transition:transform .2s; }
    .ai-summary-body p.ai-summary-text { font-size:14px; line-height:1.65; color:#3B3652; margin:0 0 8px; }
    .ai-summary-body .ai-head { font-weight:900; color:#1E1B2E; margin-top:4px; }
    .ai-summary-body .ai-head strong { font-weight:900; }
    .ai-summary-body .ai-item { margin-left:8px; }
    .ai-summary-placeholder { font-size:13px; color:rgba(30,27,46,.45); margin:0 0 12px; }
    .ai-summary-error { font-size:13px; color:#C62828; margin:0 0 12px; }

    /* ── Modals ── */
    .modal-overlay {
        position:fixed; inset:0; background:rgba(0,0,0,.45);
        display:flex; align-items:center; justify-content:center;
        padding:24px; z-index:60;
        opacity:0; pointer-events:none;
        transition:opacity .22s ease;
    }
    .modal-overlay.open { opacity:1; pointer-events:auto; }
    .modal-box {
        background:#fff; border-radius:22px;
        width:100%; max-width:340px;
        transform:translateY(18px) scale(0.97);
        transition:transform .25s cubic-bezier(.22,1,.36,1);
        box-shadow:0 24px 60px rgba(139,70,211,.22);
    }
    .modal-overlay.open .modal-box { transform:translateY(0) scale(1); }

    .my-select {
        width:100%; padding:10px 14px; border-radius:10px;
        border:1.5px solid #EDE9FE; background:#F8F7FF;
        font-family:'Nunito',sans-serif; font-size:14px; font-weight:700; color:#1E1B2E;
        appearance:none; -webkit-appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'14\' height=\'14\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%238B46D3\' stroke-width=\'2.5\'%3E%3Cpath d=\'M6 9l6 6 6-6\'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 12px center;
        cursor:pointer;
    }
    .my-select:focus { outline:none; border-color:#8B46D3; box-shadow:0 0 0 3px rgba(139,70,211,.12); }

    .map-placeholder {
        width:100%; height:180px; border-radius:14px;
        background: linear-gradient(135deg,#e8f4e8 0%,#d4ead4 50%,#c8e0c8 100%);
        overflow:hidden; position:relative;
        display:flex; align-items:center; justify-content:center;
        font-size:13px; color:#666; font-weight:600;
    }
    .map-placeholder::before {
        content:'';
        position:absolute; inset:0;
        background-image:
            linear-gradient(rgba(0,0,0,.08) 1px,transparent 1px),
            linear-gradient(90deg,rgba(0,0,0,.08) 1px,transparent 1px),
            linear-gradient(rgba(0,0,0,.04) 1px,transparent 1px),
            linear-gradient(90deg,rgba(0,0,0,.04) 1px,transparent 1px);
        background-size:40px 40px,40px 40px,10px 10px,10px 10px;
    }

    /* ── Image Popup Modal ── */
    .image-modal-overlay {
        position: fixed; inset: 0; background: rgba(0, 0, 0, 0.85);
        display: flex; align-items: center; justify-content: center;
        padding: 24px; z-index: 100;
        opacity: 0; pointer-events: none;
        transition: opacity 0.25s ease;
        backdrop-filter: blur(8px);
    }
    .image-modal-overlay.open { opacity: 1; pointer-events: auto; }
    .image-modal-box {
        max-width: 90vw; max-height: 90vh; background: transparent;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.34, 1.2, 0.64, 1);
        position: relative;
    }
    .image-modal-overlay.open .image-modal-box { transform: scale(1); }
    .image-frame {
        position: relative;
        background: linear-gradient(135deg, #fff 0%, #f5f0ff 100%);
        padding: 20px; border-radius: 32px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .image-frame-inner {
        position: relative;
        border: 8px solid #8B46D3; border-radius: 20px; overflow: hidden;
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.3), 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    .image-frame::before,
    .image-frame::after,
    .image-frame-inner::before,
    .image-frame-inner::after {
        content: ''; position: absolute;
        width: 30px; height: 30px; pointer-events: none;
    }
    .image-frame::before {
        top: 12px; left: 12px;
        border-top: 4px solid #D4BAEF; border-left: 4px solid #D4BAEF;
        border-radius: 12px 0 0 0;
    }
    .image-frame::after {
        top: 12px; right: 12px;
        border-top: 4px solid #D4BAEF; border-right: 4px solid #D4BAEF;
        border-radius: 0 12px 0 0;
    }
    .image-frame-inner::before {
        bottom: 12px; left: 12px;
        border-bottom: 4px solid #D4BAEF; border-left: 4px solid #D4BAEF;
        border-radius: 0 0 0 12px;
    }
    .image-frame-inner::after {
        bottom: 12px; right: 12px;
        border-bottom: 4px solid #D4BAEF; border-right: 4px solid #D4BAEF;
        border-radius: 0 0 12px 0;
    }
    .image-modal-img {
        display: block; max-width: 100%; max-height: calc(90vh - 80px);
        width: auto; height: auto; object-fit: contain;
        background: #1E1B2E; cursor: pointer;
    }
    .image-modal-close {
        position: absolute; top: -40px; right: -40px;
        width: 40px; height: 40px; background: #8B46D3;
        border: none; border-radius: 50%; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 24px; font-weight: bold;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); z-index: 10;
    }
    .image-modal-close:hover { background: #6a2fb0; transform: scale(1.05); }
    @media (max-width: 639px) {
        .image-modal-close { top: -35px; right: -10px; width: 36px; height: 36px; font-size: 20px; }
        .image-frame { padding: 12px; }
        .image-frame-inner { border-width: 4px; }
    }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ url('/nexus/diary') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Diary Nanny</span>
            <p id="nannySubtitle" class="text-white/60 text-xs font-semibold mt-0.5">–</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Filter kategori --}}
    <div class="anim d2">
        <div class="hide-scrollbar flex" id="katChips" style="overflow-x:auto;gap:8px;padding-bottom:2px;"></div>
    </div>

    {{-- Calendar --}}
    <div class="cal-wrap anim d2">
        <div class="cal-month-row">
            <button class="cal-month-btn" id="btnMonthYear">–</button>
            <button class="full-month-btn" id="btnFullMonth">Full month</button>
        </div>
        <div class="weekdays">
            @foreach(['SUN','MON','TUE','WED','THU','FRI','SAT'] as $wd)
            <div class="weekday">{{ $wd }}</div>
            @endforeach
        </div>
        <div class="days-grid" id="calGrid"></div>
    </div>

    {{-- Ringkasan AI (per anak dalam assignment, tanggal aktif) --}}
    <div class="anim d2 flex flex-col" id="aiSummaryWrap" style="gap:10px;"></div>

    {{-- Timeline header --}}
    <div class="anim d3" style="padding:20px 0px 12px;">
        <div class="section-header">
            <span class="section-title">Today's Timeline</span>
            <span class="section-date" id="timelineDateLabel">–</span>
        </div>
    </div>

    {{-- Timeline items (diisi via JS) --}}
    <div class="timeline-section anim d4" id="timelineContainer">
        <div id="errorState" class="flex flex-col items-center" style="display:none; padding:40px 20px; gap:12px;">
            <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#C4B5FD;"></ion-icon>
            <p style="font-size:15px; font-weight:700; color:#1E1B2E;">Failed to load data</p>
            <p style="font-size:13px; color:#A8A2C2; text-align:center;" id="errorMsg"></p>
            <button onclick="loadDiary()" style="background:#8B46D3; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Try Again</button>
        </div>
    </div>

</div>
@endsection

@push('modals')
{{-- Modal: Set Month & Year --}}
<div class="modal-overlay" id="modalMonthYear">
    <div class="modal-box" style="padding:24px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <ion-icon name="settings" style="font-size:20px;color:#8B46D3;"></ion-icon>
            <span style="font-size:17px; font-weight:800; color:#1E1B2E;">Set Month And Year</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
            <div>
                <p style="font-size:13px;font-weight:800;color:#5A556E;margin-bottom:8px;">Select Month</p>
                <select id="myMonthSel" class="my-select">
                    <option value="0">January</option><option value="1">February</option>
                    <option value="2">March</option><option value="3">April</option>
                    <option value="4">May</option><option value="5">June</option>
                    <option value="6">July</option><option value="7">August</option>
                    <option value="8">September</option><option value="9">October</option>
                    <option value="10">November</option><option value="11">December</option>
                </select>
            </div>
            <div>
                <p style="font-size:13px;font-weight:800;color:#5A556E;margin-bottom:8px;">Select Year</p>
                <select id="myYearSel" class="my-select"></select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <button onclick="closeMonthYearModal()"
                style="flex:1; padding:10px 20px; border-radius:13px; border:2px solid #FFCACA; background:#FFF9F9;
                    font-family:'Nunito', sans-serif; font-size:14px; font-weight:800; color:#FF0000; cursor:pointer;
                    display:flex; align-items:center; justify-content:center; gap:8px;
                    transition: all 0.2s ease;">
                <ion-icon name="close-circle" style="font-size:18px;"></ion-icon>
                Cancel
            </button>
            <button onclick="applyMonthYear()"
                style="flex:1; padding:10px 20px; border-radius:13px; border:none; background:#8B46D3;
                    font-family:'Nunito', sans-serif; font-size:14px; font-weight:800; color:#fff; cursor:pointer;
                    display:flex; align-items:center; justify-content:center; gap:8px;
                    box-shadow: 0 4px 12px rgba(139, 70, 211, 0.3);
                    transition: all 0.2s ease;">
                <ion-icon name="checkmark" style="font-size:18px;"></ion-icon>
                Apply
            </button>
        </div>
    </div>
</div>

{{-- Modal: Location --}}
<div class="modal-overlay" id="modalLocation">
    <div class="modal-box" style="padding:0;overflow:hidden;">
        <div class="map-placeholder" id="mapPlaceholder"></div>
        <div style="padding:16px 18px 18px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1.5px solid #F0EDFB;">
                <div style="width:42px;height:42px;border-radius:12px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <ion-icon name="location" style="font-size:20px;color:#8B46D3;"></ion-icon>
                </div>
                <div>
                    <p id="locModalName" style="font-size:15px;font-weight:900;color:#1E1B2E;margin-bottom:2px;">Activity Location</p>
                    <p id="locModalSub"  style="font-size:12px;font-weight:700;color:#A8A2C2;">Location recorded</p>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <button onclick="closeLocationModal()"
                    style="padding:13px;border-radius:12px;border:2px solid #EDE9FE;background:#fff;
                           font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#5A556E;cursor:pointer;">
                    Close
                </button>
                <button onclick="openInGMaps()"
                    style="padding:13px;border-radius:12px;border:none;background:#8B46D3;
                           font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#fff;cursor:pointer;
                           box-shadow:0 6px 18px rgba(139,70,211,.35);">
                    Open in GMaps
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Image Popup --}}
<div class="image-modal-overlay" id="imageModal">
    <div class="image-modal-box">
        <button class="image-modal-close" onclick="closeImageModal()">&times;</button>
        <div class="image-frame">
            <div class="image-frame-inner">
                <img id="modalImage" class="image-modal-img" alt="Full size image">
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
// ── Config ──
const API_BASE_URL = '{{ env("API_BASE_URL") }}';
const API_TOKEN    = '{{ session("token") }}';
const MONTHS_EN = ['January','February','March','April','May','June',
                   'July','August','September','October','November','December'];

const KAT_META = {
    makan:   { bg:'#FFF4EC', border:'#FF9A6C', dot:'#FF9A6C', icon:'🍽️', label:'🍽️ Eat'   },
    tidur:   { bg:'#EEF4FF', border:'#7BB4F0', dot:'#7BB4F0', icon:'🌙', label:'🌙 Sleep'  },
    main:    { bg:'#FFFBEE', border:'#FFD93D', dot:'#FFD93D', icon:'⚽', label:'⚽ Play'   },
    belajar: { bg:'#F0F2FF', border:'#9BB8FF', dot:'#9BB8FF', icon:'📖', label:'📖 Study'  },
    mandi:   { bg:'#FFF0F7', border:'#FFB4D6', dot:'#FFB4D6', icon:'🛁', label:'🛁 Bath'   },
    minum:   { bg:'#EAF6FF', border:'#7FC4F5', dot:'#7FC4F5', icon:'🥛', label:'🥛 Drink'  },
    bab:     { bg:'#F6F1EA', border:'#C4A484', dot:'#C4A484', icon:'🟤', label:'🟤 Poop'   },
    bak:     { bg:'#E8F4FF', border:'#6BA8D9', dot:'#6BA8D9', icon:'💧', label:'💧 Pee'    },
};
const KAT_DEFAULT = { bg:'#F0EDFB', border:'#C4B5FD', dot:'#8B46D3', icon:'📅' };

// ── State ──
var ID_NANNY   = {{ (request()->route('id_nanny') ?? 'null') ? (int) request()->route('id_nanny') : 'null' }};
var currentDate = new Date();
var calViewDate = new Date(currentDate);
var isFullMonth = false;
var currentKat  = '';
var locLat = null, locLng = null;

function pad(n){ return String(n).padStart(2,'0'); }
function fmtYMD(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }
function escHtml(s){
    return String(s == null ? '' : s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

// ── Time helpers ──
function parseTimeParts(dt){
    if (!dt) return { hh:'08', mm:'00' };
    var d = new Date(String(dt).replace(' ', 'T'));
    if (isNaN(d.getTime())) {
        var m = String(dt).match(/(\d{1,2}):(\d{2})/);
        return { hh: pad(m ? +m[1] : 8), mm: pad(m ? +m[2] : 0) };
    }
    return { hh: pad(d.getHours()), mm: pad(d.getMinutes()) };
}

function toAmPm(parts){
    var h = parseInt(parts.hh, 10);
    var ampm = h >= 12 ? 'PM' : 'AM';
    var h12 = h > 12 ? h - 12 : (h === 0 ? 12 : h);
    return { time: pad(h12) + ':' + parts.mm, ampm: ampm };
}

function fmtDurasi(durasi){
    if (durasi && typeof durasi === 'object') {
        var j = durasi.jam || 0, m = durasi.menit || 0;
        if (j > 0 && m > 0) return j + ' hr ' + m + ' min';
        if (j > 0) return j + ' hr';
        if (m > 0) return m + ' min';
        return (durasi.total_menit || 0) + ' min';
    }
    return durasi || '-';
}

// ── Kategori chips ──
function buildKatChips(){
    var chips = [{ value:'', label:'All' }].concat(
        Object.keys(KAT_META).map(function (k) { return { value:k, label:KAT_META[k].label }; })
    );
    var el = document.getElementById('katChips');
    el.innerHTML = chips.map(function (c) {
        var on = c.value === currentKat;
        return '<a class="filter-chip" data-kat="' + c.value + '" onclick="setKategori(this)"'
             + ' style="border:2px solid ' + (on ? '#8B46D3' : '#EDE9FE') + ';'
             + ' background:' + (on ? '#8B46D3' : '#fff') + ';'
             + ' color:' + (on ? '#fff' : '#5A556E') + ';text-decoration:none;">'
             + escHtml(c.label) + '</a>';
    }).join('');
}

function setKategori(el){
    currentKat = el.getAttribute('data-kat') || '';
    buildKatChips();
    loadDiary();
}

// ── Ringkasan AI (Modul 7, per anak + tanggal) ──
function renderAiSummaries(daftarAnak) {
    var wrap = document.getElementById('aiSummaryWrap');
    if (!daftarAnak.length) { wrap.innerHTML = ''; return; }

    var multi = daftarAnak.length > 1;
    wrap.innerHTML = daftarAnak.map(function (a) {
        var id = a.id, nama = a.nama || '';
        var title = '📝 Ringkasan AI' + (multi && nama ? ' • ' + escHtml(nama) : '');
        return '<div class="ai-summary-card" id="aiCard_' + id + '">'
             +   '<div class="section-header ai-summary-toggle" onclick="toggleAiSummary(' + id + ')">'
             +     '<span class="section-title">' + title
             +       ' <span class="ai-summary-count" id="aiCount_' + id + '"></span></span>'
             +     '<span class="ai-chevron" id="aiChevron_' + id + '">▾</span>'
             +   '</div>'
             +   '<div class="ai-summary-collapse" id="aiCollapse_' + id + '">'
             +     '<div class="ai-summary-body" id="aiBody_' + id + '">'
             +       '<p class="ai-summary-placeholder">Memuat ringkasan...</p>'
             +     '</div>'
             +   '</div>'
             + '</div>';
    }).join('');

    daftarAnak.forEach(function (a) { loadAiSummary(a.id); });
}

function toggleAiSummary(id) {
    var col = document.getElementById('aiCollapse_' + id);
    var ch  = document.getElementById('aiChevron_' + id);
    if (!col) return;
    var closed = col.style.display === 'none';
    col.style.display = closed ? 'block' : 'none';
    if (ch) ch.textContent = closed ? '▾' : '▸';
}

async function loadAiSummary(idAnak) {
    var body = document.getElementById('aiBody_' + idAnak);
    if (!body) return;
    try {
        var params = new URLSearchParams({
            id_anak: idAnak,
            summary_date: fmtYMD(currentDate)
        });
        var r = await fetch(API_BASE_URL + '/daily-ai-summaries?' + params.toString(), {
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + API_TOKEN, 'Accept': 'application/json' }
        });
        var j = await r.json();
        if (j.success && Array.isArray(j.data) && j.data[0]) {
            renderAiSummary(idAnak, j.data[0].ai_summary);
            var words = (j.data[0].ai_summary || '').trim().split(/\s+/).filter(Boolean).length;
            var c = document.getElementById('aiCount_' + idAnak);
            if (c) c.textContent = words ? '(' + words + ' kata)' : '';
        } else {
            body.innerHTML = '<p class="ai-summary-placeholder">Belum ada ringkasan untuk tanggal ini.</p>';
        }
    } catch (e) {
        body.innerHTML = '<p class="ai-summary-error">Gagal memuat ringkasan.</p>';
    }
}

function mdInline(s) { return s.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\*(.+?)\*/g, '<em>$1</em>'); }

function renderAiSummary(idAnak, text) {
    var body = document.getElementById('aiBody_' + idAnak);
    if (!body || !text) return;
    var html = '';
    text.split(/\n/).forEach(function (line) {
        var l = line.trim();
        if (!l) return;
        if (/^#{1,3}\s/.test(l)) html += '<p class="ai-summary-text ai-head">' + mdInline(l.replace(/^#{1,3}\s*/, '')) + '</p>';
        else if (/^\d+[.)]\s/.test(l)) html += '<p class="ai-summary-text ai-item">' + mdInline(l.replace(/^\d+[.)]\s*/, '')) + '</p>';
        else if (/^[-*•]\s/.test(l)) html += '<p class="ai-summary-text ai-item">▪ ' + mdInline(l.replace(/^[-*•]\s*/, '')) + '</p>';
        else html += '<p class="ai-summary-text">' + mdInline(l) + '</p>';
    });
    body.innerHTML = html;
}

// ── Main fetch ──
async function loadDiary(){
    var container = document.getElementById('timelineContainer');
    var errorState = document.getElementById('errorState');
    errorState.style.display = 'none';

    if (!ID_NANNY) { showError('Nanny not found in URL.'); return; }

    try {
        var params = new URLSearchParams({ id_nanny: ID_NANNY, tanggal: fmtYMD(currentDate) });
        if (currentKat) params.set('kategori', currentKat);

        var res = await fetch(API_BASE_URL + '/diary-nanny-for-nexus?' + params.toString(), {
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + API_TOKEN, 'Accept': 'application/json' }
        });
        var data = await res.json();

        if (data.status !== 'success' || !data.data) {
            showError(data.message || 'Failed to load data.');
            return;
        }

        // Subtitle: nanny terpilih + majikan
        var d = data.data;
        document.getElementById('nannySubtitle').textContent = d.nama_majikan
            ? (d.nanny_name + ' • Majikan: ' + d.nama_majikan)
            : (d.nanny_name || 'Nanny');

        // Ringkasan AI per anak (tanggal aktif)
        renderAiSummaries(d.daftar_anak || []);

        // Flatten rekap_per_anak[].aktivitas_per_tanggal[].aktivitas[]
        var list = [];
        (data.data.rekap_per_anak || []).forEach(function (r) {
            (r.aktivitas_per_tanggal || []).forEach(function (g) {
                (g.aktivitas || []).forEach(function (a) {
                    a.nama_anak = a.nama_anak || r.nama_anak;
                    list.push(a);
                });
            });
        });
        list.sort(function (a, b) {
            return new Date(String(a.jam_mulai).replace(' ','T')) - new Date(String(b.jam_mulai).replace(' ','T'));
        });

        renderTimeline(list);
    } catch (e) {
        console.error('[loadDiary]', e);
        showError('Failed to load data.');
    }
}

function showError(msg){
    var container = document.getElementById('timelineContainer');
    // Buang semua timeline-row / empty-state lama
    Array.prototype.slice.call(container.children).forEach(function (c) {
        if (c.id !== 'errorState') c.remove();
    });
    document.getElementById('errorMsg').textContent = msg || '';
    document.getElementById('errorState').style.display = 'flex';
}

// ── Timeline render (markup identik konsultan) ──
function renderTimeline(list){
    var container = document.getElementById('timelineContainer');
    Array.prototype.slice.call(container.children).forEach(function (c) {
        if (c.id !== 'errorState') c.remove();
    });
    document.getElementById('errorState').style.display = 'none';

    if (!list.length) {
        var empty = document.createElement('div');
        empty.className = 'flex flex-col items-center justify-center';
        empty.style.cssText = 'padding:50px 20px 80px;';
        empty.innerHTML =
            '<div class="float-anim flex items-center justify-center"'
            + ' style="width:100px;height:100px;border-radius:50%;background:#EDE9FE;margin-bottom:18px;">'
            + '<ion-icon name="calendar-clear-outline" style="font-size:48px;color:#C4B5FD;"></ion-icon></div>'
            + '<p style="font-size:16px;font-weight:900;color:#1E1B2E;margin-bottom:6px;">No activities</p>'
            + '<p style="font-size:13px;font-weight:700;color:#A8A2C2;">for this date</p>';
        container.appendChild(empty);
        return;
    }

    list.forEach(function (item, i) {
        var kat   = item.kategori || 'main';
        var c     = KAT_META[kat] || KAT_DEFAULT;
        var isLast = i === list.length - 1;

        var s  = toAmPm(parseTimeParts(item.jam_mulai));
        var e  = toAmPm(parseTimeParts(item.jam_selesai));
        var durasi = fmtDurasi(item.durasi);

        var moodEmoji = ({ senang:'😊', sedih:'😢', marah:'😠', biasa:'😐' })[item.mood] || '';
        var title = kat.charAt(0).toUpperCase() + kat.slice(1)
                  + (item.nama_anak ? ' • 👶 ' + item.nama_anak : '');

        var row = document.createElement('div');
        row.className = 'timeline-row';
        row.style.cssText = 'animation:slideUp .3s ease ' + (i * 0.06) + 's both;opacity:0;';

        row.innerHTML =
            '<div class="tl-left">'
            +   '<div class="tl-time"><div class="hh">' + s.time + '</div><div class="ampm">' + s.ampm + '</div></div>'
            +   '<div class="tl-dot" style="background:' + c.dot + ';"></div>'
            +   (isLast ? '' : '<div class="tl-line"></div>')
            +   '<div class="tl-time" style="' + (isLast ? '' : 'margin-top:auto;') + '">'
            +     '<div class="hh">' + e.time + '</div><div class="ampm">' + e.ampm + '</div>'
            +   '</div>'
            + '</div>'
            + '<div class="tl-right">'
            +   '<div class="akt-card" style="background:' + c.bg + ';border-color:' + c.border + '40;">'
            +     '<div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:' + c.border + ';border-radius:4px 0 0 4px;"></div>'
            +     '<div class="akt-card-header">'
            +       '<div class="akt-cat">'
            +         '<span style="font-size:18px;">' + c.icon + '</span>'
            +         '<span class="akt-title">' + escHtml(title) + '</span>'
            +       '</div>'
            +       (moodEmoji ? '<span style="font-size:20px;">' + moodEmoji + '</span>' : '')
            +     '</div>'
            +     (item.deskripsi ? '<p class="akt-desc">' + escHtml(item.deskripsi) + '</p>' : '')
            +     (item.foto_url
                    ? '<img src="' + escHtml(item.foto_url) + '" class="akt-photo" alt="" onclick="openImageModal(\'' + escHtml(item.foto_url) + '\')">'
                    : '')
            +     '<div class="akt-footer">'
            +       '<button class="loc-btn" onclick="openLocationModal('
            +         '\'' + escHtml(item.lokasi || '').replace(/'/g, "\\'") + '\','
            +         '\'' + escHtml(item.lat || '').replace(/'/g, "\\'") + '\','
            +         '\'' + escHtml(item.lng || '').replace(/'/g, "\\'") + '\')">'
            +         '<ion-icon name="location-outline" style="font-size:14px;"></ion-icon> Location'
            +       '</button>'
            +     '</div>'
            +   '</div>'
            +   '<p class="tl-duration">Total Duration : ' + escHtml(durasi) + '</p>'
            + '</div>';

        container.appendChild(row);
    });
}

// ── Calendar ──
function daysInMonth(y,m){ return new Date(y,m+1,0).getDate(); }

function buildCalendar(){
    const y=calViewDate.getFullYear(), m=calViewDate.getMonth();
    document.getElementById('btnMonthYear').textContent=MONTHS_EN[m]+' '+y;

    const grid=document.getElementById('calGrid');
    grid.innerHTML='';
    const today=new Date();

    if(isFullMonth){
        const first=new Date(y,m,1).getDay();
        const days=daysInMonth(y,m);
        const prevDays=new Date(y,m,0).getDate();
        for(let i=first-1;i>=0;i--){
            const cell=document.createElement('div');
            cell.className='day-cell other-month';
            cell.textContent=prevDays-i;
            grid.appendChild(cell);
        }
        for(let d=1;d<=days;d++){
            const cell=makeCell(d,y,m,today);
            grid.appendChild(cell);
        }
        const total=grid.children.length;
        const rem=total%7===0?0:7-(total%7);
        for(let d=1;d<=rem;d++){
            const cell=document.createElement('div');
            cell.className='day-cell other-month';
            cell.textContent=d;
            grid.appendChild(cell);
        }
    } else {
        const sel=new Date(currentDate);
        const dow=sel.getDay();
        const weekStart=new Date(sel);
        weekStart.setDate(sel.getDate()-dow);
        for(let i=0;i<7;i++){
            const dt=new Date(weekStart);
            dt.setDate(weekStart.getDate()+i);
            const cell=makeCell(dt.getDate(),dt.getFullYear(),dt.getMonth(),today);
            if(dt.getMonth()!==m) cell.classList.add('other-month');
            grid.appendChild(cell);
        }
    }
}

function makeCell(d,y,m,today){
    const cell=document.createElement('div');
    cell.className='day-cell';
    cell.textContent=d;
    const isToday=d===today.getDate()&&m===today.getMonth()&&y===today.getFullYear();
    const isSel  =d===currentDate.getDate()&&m===currentDate.getMonth()&&y===currentDate.getFullYear();
    if(isSel)        cell.classList.add('selected');
    else if(isToday) cell.classList.add('today');
    cell.addEventListener('click',()=>{
        currentDate=new Date(y,m,d);
        calViewDate=new Date(y,m,1);
        updateTimelineLabel();
        buildCalendar();
        loadDiary();
    });
    return cell;
}

function updateTimelineLabel(){
    const el=document.getElementById('timelineDateLabel');
    if(!el) return;
    el.textContent=currentDate.toLocaleDateString('en-US',{weekday:'long',month:'long',day:'numeric'});
}

document.getElementById('btnFullMonth').addEventListener('click',()=>{
    isFullMonth=!isFullMonth;
    document.getElementById('btnFullMonth').textContent=isFullMonth?'Week view':'Full month';
    buildCalendar();
});

// ── Month/Year Modal ──
(function(){
    const yearSel=document.getElementById('myYearSel');
    const curY=new Date().getFullYear();
    for(let y=curY-5;y<=curY+2;y++){
        const o=document.createElement('option');
        o.value=y; o.textContent=y;
        yearSel.appendChild(o);
    }
})();

function openMonthYearModal(){
    const y=calViewDate.getFullYear(), m=calViewDate.getMonth();
    document.getElementById('myMonthSel').value=m;
    document.getElementById('myYearSel').value=y;
    document.getElementById('modalMonthYear').classList.add('open');
}
function closeMonthYearModal(){
    document.getElementById('modalMonthYear').classList.remove('open');
}
function applyMonthYear(){
    const m=parseInt(document.getElementById('myMonthSel').value);
    const y=parseInt(document.getElementById('myYearSel').value);
    calViewDate=new Date(y,m,1);
    currentDate=new Date(y,m,currentDate.getDate()>daysInMonth(y,m)?daysInMonth(y,m):currentDate.getDate());
    closeMonthYearModal();
    buildCalendar();
    updateTimelineLabel();
    loadDiary();
}

document.getElementById('btnMonthYear').addEventListener('click',openMonthYearModal);
document.getElementById('modalMonthYear').addEventListener('click',e=>{
    if(e.target.id==='modalMonthYear') closeMonthYearModal();
});

// ── Location Modal ──
function openLocationModal(name, lat, lng) {
    locLat = lat; locLng = lng;
    const n = name || 'Activity Location';
    document.getElementById('locModalName').textContent = n;
    document.getElementById('locModalSub').textContent = lat && lng
        ? lat + ', ' + lng
        : 'Location recorded';

    const latDec = dmsToDecimal(lat);
    const lngDec = dmsToDecimal(lng);

    const mapContainer = document.getElementById('mapPlaceholder');

    if (latDec !== null && lngDec !== null) {
        mapContainer.innerHTML = `
            <iframe
                width="100%"
                height="180"
                style="border:0;border-radius:14px;display:block;"
                loading="lazy"
                allowfullscreen
                src="https://maps.google.com/maps?q=${latDec},${lngDec}&z=16&output=embed">
            </iframe>`;
    } else {
        mapContainer.innerHTML = `
            <div style="width:100%;height:180px;border-radius:14px;background:#e8f4e8;
                        display:flex;align-items:center;justify-content:center;
                        font-size:13px;color:#666;font-weight:600;">
                Coordinates unavailable
            </div>`;
    }

    document.getElementById('modalLocation').classList.add('open');
}
function closeLocationModal(){
    document.getElementById('modalLocation').classList.remove('open');
}
function dmsToDecimal(dms) {
    if (!dms || !isNaN(parseFloat(dms)) && dms.indexOf('°') === -1) {
        return dms ? parseFloat(dms) : null;
    }
    const regex = /(\d+)[°\s]+(\d+)['\s]+(\d+\.?\d*)["″\s]*([NSEW])?/i;
    const m = dms.match(regex);
    if (!m) return null;
    let dd = parseFloat(m[1]) + parseFloat(m[2]) / 60 + parseFloat(m[3]) / 3600;
    if (m[4] && 'SW'.includes(m[4].toUpperCase())) dd = -dd;
    return dd;
}

function openInGMaps() {
    if (locLat && locLng) {
        const lat = dmsToDecimal(locLat);
        const lng = dmsToDecimal(locLng);
        if (lat !== null && lng !== null) {
            window.open('https://www.google.com/maps?q=' + lat + ',' + lng, '_blank');
        } else {
            window.open('https://maps.google.com', '_blank');
        }
    } else {
        window.open('https://maps.google.com', '_blank');
    }
}
document.getElementById('modalLocation').addEventListener('click',e=>{
    if(e.target.id==='modalLocation') closeLocationModal();
});

// ── Image Popup Modal ──
function openImageModal(imageUrl) {
    if (!imageUrl) return;
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modalImg.src = imageUrl;
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('imageModal').addEventListener('click',function(e){
    if (e.target === this) closeImageModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('imageModal').classList.contains('open')) {
        closeImageModal();
    }
});

// ── Init ──
updateTimelineLabel();
buildCalendar();
buildKatChips();
loadDiary();
</script>
@endpush
