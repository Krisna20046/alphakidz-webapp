@extends('layouts.app')

@section('title', 'Nanny Diary Recap')

@push('styles')
<style>
    * { -webkit-tap-highlight-color: transparent; }

    /* Skeleton shimmer */
    @keyframes shimmer {
        0%   { background-position: -400px 0; }
        100% { background-position:  400px 0; }
    }
    .skeleton {
        background: linear-gradient(90deg, #f0dcea 25%, #fce8f5 50%, #f0dcea 75%);
        background-size: 400px 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 12px;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-up { animation: slideUp 0.35s ease forwards; }
    .anim-up.d1 { animation-delay: 0.05s; opacity: 0; }
    .anim-up.d2 { animation-delay: 0.12s; opacity: 0; }
    .anim-up.d3 { animation-delay: 0.20s; opacity: 0; }
    .anim-up.d4 { animation-delay: 0.28s; opacity: 0; }

    .card-press { transition: transform .15s ease, box-shadow .15s ease; }
    .card-press:active { transform: scale(0.97); }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    @keyframes floatAnim {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-6px); }
    }
    .float-anim { animation: floatAnim 3s ease-in-out infinite; }

    @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
    .fade-in { animation: fadeIn .3s ease forwards; }

    .tab-btn { transition: all .2s ease; }
    .tab-btn.active {
        background: #7B1E5A;
        color: #fff;
        box-shadow: 0 4px 12px rgba(123,30,90,0.3);
    }

    @keyframes progressFill { from { width: 0%; } }
    .progress-bar { animation: progressFill .6s ease forwards; }

    @keyframes dlPulse {
        0%,100% { opacity:1; }
        50% { opacity:.6; }
    }
    .dl-pulse { animation: dlPulse 1s ease-in-out infinite; }

    @keyframes modalSlideUp {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .modal-slide { animation: modalSlideUp .3s cubic-bezier(.4,0,.2,1); }

    .picker-col { overflow-y: auto; max-height: 200px; scroll-snap-type: y mandatory; }
    .picker-col::-webkit-scrollbar { display: none; }
    .picker-item { scroll-snap-align: start; }

    @keyframes badgePop {
        0%   { transform: scale(0); }
        80%  { transform: scale(1.15); }
        100% { transform: scale(1); }
    }
    .badge-pop { animation: badgePop .3s ease forwards; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .shell-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(248,247,255,0.98) 58%, rgba(212,186,239,0.48) 100%);
        border-radius: 50px 50px 0 0;
        box-shadow: 0 -10px 30px rgba(139, 70, 211, 0.08);
    }
    .field-card {
        background: rgba(255,255,255,0.86);
        border: 1px solid #D8CAEF;
        border-radius: 10px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .field-card:focus-within, .field-card.active {
        border-color: #8B46D3;
        box-shadow: 0 0 0 3px rgba(139, 70, 211, 0.10);
    }
    .nanny-card { transition: transform .15s ease; }
    .nanny-card:active { transform: scale(0.98); }
    .category-chip {
        border: 1.5px solid transparent;
        transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
    }
    .category-chip:active { transform: scale(0.96); }
    .category-chip.active {
        border-color: #8B46D3;
        box-shadow: 0 6px 16px rgba(139,70,211,0.14);
    }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center px-[24px] pt-[55px] pb-[72px] before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-start gap-3 relative z-10">
        <a href="{{ url()->previous() }}"
           class="mt-0.5 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Diary Recap</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Generate Nanny Diary Report</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar" id="mainBody">

    {{-- STEP 1: PILIH NANNY --}}
    <div id="step1" class="pb-4">

        <div class="flex items-center justify-between mb-3 anim-up d1">
            <h2 class="text-[#5A556E] text-[18px] font-extrabold">Nanny's Assignment</h2>
            <div class="bg-[#EDE9FE] px-3 py-1 rounded-full">
                <span id="nannyCountBadge" class="hidden text-[#8B46D3] text-xs font-bold badge-pop"></span>
            </div>
        </div>

        <div class="anim-up d2 mb-4">
            <div class="flex items-center bg-[#F4F4F4] rounded-full px-4 py-2.5 border border-[#DDD6EF] gap-2 transition-all focus-within:border-[#8B46D3] focus-within:shadow-[0_0_0_3px_rgba(139,70,211,0.14)]">
                <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                <input id="searchInput"
                       type="text"
                       placeholder="Search nanny...."
                       class="flex-1 text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] bg-transparent outline-none"
                       oninput="filterNannies(this.value)"
                />
            </div>
        </div>

        <div id="nannyList" class="anim-up d3 flex flex-col gap-2"></div>

    </div>

    {{-- STEP 2: FILTER & GENERATE --}}
    <div id="step2" class="hidden pb-16">

        <div class="mb-3">
            <button onclick="backToStep1()" class="inline-flex items-center gap-1.5 text-[#8B46D3] text-[12px] font-extrabold">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
                Choose another nanny
            </button>
        </div>

        <div id="selectedNannyCard" class="bg-white/95 rounded-[14px] px-4 pt-5 pb-4 mb-4 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5]">
            <div class="flex flex-col items-center text-center">
                <div id="selAvatar" class="w-[78px] h-[78px] rounded-full bg-[#F3EEFC] flex items-center justify-center overflow-hidden border-[3px] border-[#D8CAEF] shadow-[0_4px_10px_rgba(139,70,211,0.16)]">
                    <ion-icon name="person" style="font-size:30px;color:#8B46D3;"></ion-icon>
                </div>
                <p id="selName" class="text-[#1E1B2E] text-[15px] font-extrabold mt-3 truncate max-w-full">-</p>
                <span class="mt-1 inline-flex items-center rounded-full bg-[#DCFCE7] px-2 py-0.5 text-[9px] font-black text-[#166534]">ACTIVE</span>
                <div class="h-px w-full bg-[#E7DCF8] my-4"></div>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-[5px] bg-[#EDE9FE] flex items-center justify-center flex-shrink-0"><ion-icon name="card-outline" style="font-size:15px;color:#8B46D3;"></ion-icon></div>
                    <div class="min-w-0"><p class="text-[#B39BCF] text-[9px] font-black uppercase tracking-[1px]">Assignment ID</p><p id="selAssignmentId" class="text-[#1E1B2E] text-[12px] font-extrabold break-words">-</p></div>
                </div>
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-[5px] bg-[#FDE7EF] flex items-center justify-center flex-shrink-0"><ion-icon name="mail-outline" style="font-size:15px;color:#EC4899;"></ion-icon></div>
                    <div class="min-w-0"><p class="text-[#B39BCF] text-[9px] font-black uppercase tracking-[1px]">Email</p><p id="selEmail" class="text-[#1E1B2E] text-[12px] font-extrabold break-words">-</p></div>
                </div>
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-[5px] bg-[#E8ECFF] flex items-center justify-center flex-shrink-0"><ion-icon name="call-outline" style="font-size:15px;color:#4F46E5;"></ion-icon></div>
                    <div class="min-w-0"><p class="text-[#B39BCF] text-[9px] font-black uppercase tracking-[1px]">Phone Number</p><p id="selPhone" class="text-[#1E1B2E] text-[12px] font-extrabold break-words">-</p></div>
                </div>
            </div>
        </div>

        <div class="bg-white/95 rounded-[14px] border border-[#EAE6F5] px-4 py-4 mb-4 shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-1.5 mb-3">
                <ion-icon name="options-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                <p class="text-[#1E1B2E] font-extrabold text-[13px]">Filter</p>
            </div>

            <div class="mb-4">
                <label class="block text-[#1E1B2E] text-[12px] font-bold mb-1.5">Start Date</label>
                <div id="startDateWrapper" class="field-card flex items-center gap-2 px-3 py-2.5">
                    <input id="startDate" type="text" readonly
                           placeholder="YYYY-MM-DD"
                           class="flex-1 text-[12px] text-[#7C748F] bg-transparent outline-none font-extrabold cursor-pointer"
                           onclick="openDatePicker('start')"
                    />
                    <ion-icon name="calendar-outline" style="font-size:15px;color:#7C748F;" onclick="openDatePicker('start')" class="cursor-pointer"></ion-icon>
                </div>
                <p id="errStart" class="hidden text-red-500 text-xs mt-1 font-medium">Start date is required</p>
            </div>

            <div class="mb-4">
                <label class="block text-[#1E1B2E] text-[12px] font-bold mb-1.5">End Date</label>
                <div id="endDateWrapper" class="field-card flex items-center gap-2 px-3 py-2.5">
                    <input id="endDate" type="text" readonly
                           placeholder="YYYY-MM-DD"
                           class="flex-1 text-[12px] text-[#7C748F] bg-transparent outline-none font-extrabold cursor-pointer"
                           onclick="openDatePicker('end')"
                    />
                    <ion-icon name="calendar-outline" style="font-size:15px;color:#7C748F;" onclick="openDatePicker('end')" class="cursor-pointer"></ion-icon>
                </div>
                <p id="errEnd" class="hidden text-red-500 text-xs mt-1 font-medium">End date is required</p>
            </div>

            <div class="mb-5">
                <label class="block text-[#1E1B2E] text-[12px] font-bold mb-2">Activity Categories</label>
                <div id="categoryChips" class="flex flex-wrap gap-2"></div>
                <span id="kategoriLabel" class="hidden">All Categories</span>
            </div>
        </div>

        <div class="bg-[#FFF7ED] rounded-[10px] border border-[#FED7AA] p-4 mb-5">
            <div class="flex items-center gap-2 mb-3">
                <ion-icon name="information-circle-outline" style="font-size:16px;color:#F59E0B;"></ion-icon>
                <p class="text-[#D97706] font-extrabold text-[11px] uppercase tracking-wide">Information</p>
            </div>
            <ul class="list-disc pl-5 space-y-0.5 text-[11px] text-[#7C748F] font-semibold leading-snug">
                <li class="flex gap-2"><span class="text-[#F59E0B] flex-shrink-0">•</span> The report includes all diary entries in the selected period</li>
                <li class="flex gap-2"><span class="text-[#F59E0B] flex-shrink-0">•</span> Data can be filtered by activity category</li>
                <li class="flex gap-2"><span class="text-[#F59E0B] flex-shrink-0">•</span> Make sure the date period is correct before generating</li>
                <li class="flex gap-2"><span class="text-[#F59E0B] flex-shrink-0">•</span> The Excel file is ready for further analysis</li>
            </ul>
        </div>

        <button id="generateBtn" onclick="handleGenerate()"
                class="w-full flex items-center justify-center gap-2 h-[44px] bg-[#8B46D3] text-white rounded-[8px] font-extrabold text-[13px] shadow-[0_8px_18px_rgba(139,70,211,0.32)] active:scale-[0.98] transition-all mb-6">
            <ion-icon name="download-outline" style="font-size:16px;"></ion-icon>
            <span>Generate &amp; Download Now</span>
        </button>

    </div>

</div>
@endsection

@push('modals')
{{-- Date Picker Modal --}}
<div id="datePickerModal"
     class="fixed inset-0 z-50 flex flex-col justify-end items-center bg-black/50 hidden"
     onclick="closeDatePickerOnOverlay(event)">
    <div class="modal-slide w-full sm:max-w-[390px] bg-white rounded-t-3xl shadow-2xl overflow-hidden">

        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full bg-[#EDE9FE]"></div>
        </div>

        <div class="flex items-center justify-between px-5 py-4 border-b-2 border-[#EDE9FE]">
            <div class="flex items-center gap-2">
                <ion-icon name="calendar" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                <p id="dpTitle" class="text-[#1E1B2E] font-bold text-lg">Select Date</p>
            </div>
            <button onclick="closeDatePicker()" class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>

        <div class="mx-5 mt-4 mb-2 bg-[#EDE9FE] rounded-2xl py-3 px-4 flex items-center justify-center gap-2">
            <ion-icon name="calendar-number-outline" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            <span id="dpPreview" class="text-[#8B46D3] font-bold text-base">-</span>
        </div>

        <div class="flex gap-2 px-5 pt-2 pb-2">
            <div class="flex-1">
                <p class="text-[#8B86A5] text-xs font-bold text-center mb-2 uppercase tracking-wider">Year</p>
                <div id="yearCol" class="picker-col bg-[#F8F7FF] rounded-2xl border-2 border-[#EDE9FE]"></div>
            </div>
            <div class="flex-1">
                <p class="text-[#8B86A5] text-xs font-bold text-center mb-2 uppercase tracking-wider">Month</p>
                <div id="monthCol" class="picker-col bg-[#F8F7FF] rounded-2xl border-2 border-[#EDE9FE]"></div>
            </div>
            <div class="flex-1">
                <p class="text-[#8B86A5] text-xs font-bold text-center mb-2 uppercase tracking-wider">Day</p>
                <div id="dayCol" class="picker-col bg-[#F8F7FF] rounded-2xl border-2 border-[#EDE9FE]"></div>
            </div>
        </div>

        <div class="flex gap-3 px-5 py-4 border-t-2 border-[#EDE9FE]">
            <button onclick="closeDatePicker()"
                    class="flex-1 py-3.5 rounded-xl bg-[#EDE9FE] text-[#8B46D3] font-bold text-sm hover:bg-[#8B46D3]/10 transition-colors">
                Cancel
            </button>
            <button onclick="confirmDatePicker()"
                    class="flex-1 py-3.5 rounded-xl bg-[#8B46D3] text-white font-bold text-sm flex items-center justify-center gap-2 hover:bg-[#9F58F8] transition-colors shadow-lg shadow-[#8B46D3]/30">
                <ion-icon name="checkmark" style="font-size:18px;"></ion-icon>
                Select
            </button>
        </div>
    </div>
</div>

{{-- Kategori Modal --}}
<div id="kategoriModal"
     class="fixed inset-0 z-50 flex flex-col justify-end items-center bg-black/50 hidden"
     onclick="closeKategoriOnOverlay(event)">
    <div class="modal-slide w-full sm:max-w-[390px] bg-white rounded-t-3xl shadow-2xl overflow-hidden">
        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full bg-[#EDE9FE]"></div>
        </div>
        <div class="flex items-center justify-between px-5 py-4 border-b-2 border-[#EDE9FE]">
            <div class="flex items-center gap-2">
                <ion-icon name="filter" style="font-size:22px;color:#7B1E5A;"></ion-icon>
                <p class="text-[#1E1B2E] font-bold text-lg">Select Category</p>
            </div>
            <button onclick="closeKategoriModal()" class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="close" style="font-size:18px;color:#7B1E5A;"></ion-icon>
            </button>
        </div>
        <div id="kategoriList" class="px-4 py-4 space-y-2 overflow-y-auto max-h-72"></div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="fixed inset-0 z-[60] hidden flex-col items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-3xl p-8 w-72 flex flex-col items-center shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
            <ion-icon name="document-text" style="font-size:32px;color:#7B1E5A;" class="dl-pulse"></ion-icon>
        </div>
        <p id="loadingTitle" class="text-[#1E1B2E] font-bold text-lg mb-1">Generating...</p>
        <p id="loadingSubtitle" class="text-[#8B86A5] text-sm mb-5 text-center">Please wait, processing the report</p>
        <div class="w-full bg-[#EDE9FE] rounded-full h-2 overflow-hidden">
            <div id="progressBar" class="h-full bg-[#8B46D3] rounded-full progress-bar" style="width:0%"></div>
        </div>
        <p id="progressText" class="text-[#8B46D3] text-xs font-bold mt-2">0%</p>
    </div>
</div>

{{-- Toast Notification --}}
<div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-[70] hidden max-w-xs w-[calc(100%-2rem)]">
    <div id="toastInner" class="flex items-start gap-3 px-4 py-3.5 rounded-2xl shadow-xl">
        <ion-icon id="toastIcon" name="checkmark-circle" style="font-size:20px;" class="flex-shrink-0 mt-0.5"></ion-icon>
        <div>
            <p id="toastTitle" class="font-bold text-sm"></p>
            <p id="toastMsg" class="text-xs mt-0.5 opacity-80"></p>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
// ── Config ────────────────────────────────────────────────────────────────────
const AUTH_TOKEN = "{{ session('token') }}";
const API_BASE   = "{{ rtrim(env('API_BASE_URL', ''), '/') }}";

// ── State ─────────────────────────────────────────────────────────────────────
let allNannies    = [];
let selectedNanny = null;
let selectedKategori = '';
let dpTarget      = 'start';
let dpYear, dpMonth, dpDay;

const MONTHS_ID = ['January','February','March','April','May','June',
                   'July','August','September','October','November','December'];
const KATEGORI_OPTIONS = [
    { value: '',        label: 'All',         short: 'All',       icon: 'apps-outline',            bg: '#EDE9FE', color: '#8B46D3' },
    { value: 'makan',   label: 'Eat',         short: 'Eat',       icon: 'restaurant-outline',      bg: '#EDE9FE', color: '#8B46D3' },
    { value: 'tidur',   label: 'Sleep',       short: 'Sleep',     icon: 'moon-outline',            bg: '#FDE7EF', color: '#EC4899' },
    { value: 'main',    label: 'Play',        short: 'Play',      icon: 'game-controller-outline', bg: '#E8ECFF', color: '#4F46E5' },
    { value: 'belajar', label: 'Study',       short: 'Study',     icon: 'book-outline',            bg: '#FEF3C7', color: '#D97706' },
    { value: 'mandi',   label: 'Take Bath',   short: 'Take Bath', icon: 'water-outline',           bg: '#DCFCE7', color: '#16A34A' },
];

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(type, title, msg) {
    const toast    = document.getElementById('toast');
    const inner    = document.getElementById('toastInner');
    const iconEl   = document.getElementById('toastIcon');
    const titleEl  = document.getElementById('toastTitle');
    const msgEl    = document.getElementById('toastMsg');

    const cfg = {
        success: { bg: '#f0fdf4', border: '#bbf7d0', text: '#166534', icon: 'checkmark-circle', iconColor: '#16a34a' },
        error:   { bg: '#fef2f2', border: '#fecaca', text: '#991b1b', icon: 'close-circle',     iconColor: '#dc2626' },
        info:    { bg: '#eff6ff', border: '#bfdbfe', text: '#1e40af', icon: 'information-circle',iconColor: '#2563eb' },
    };
    const c = cfg[type] || cfg.info;

    inner.style.cssText = `background:${c.bg};border:2px solid ${c.border};color:${c.text};`;
    iconEl.name         = c.icon;
    iconEl.style.color  = c.iconColor;
    titleEl.textContent = title;
    msgEl.textContent   = msg || '';

    toast.classList.remove('hidden');
    toast.classList.add('fade-in');

    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.add('hidden');
        toast.classList.remove('fade-in');
    }, 3500);
}

// ── Loading overlay ───────────────────────────────────────────────────────────
function showLoading(title='Generating...', subtitle='Processing the report') {
    document.getElementById('loadingTitle').textContent    = title;
    document.getElementById('loadingSubtitle').textContent = subtitle;
    document.getElementById('progressBar').style.width     = '20%';
    document.getElementById('progressText').textContent    = '0%';
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('loadingOverlay').classList.add('flex');
}
function setProgress(pct) {
    document.getElementById('progressBar').style.width  = pct + '%';
    document.getElementById('progressText').textContent = pct + '%';
}
function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
    document.getElementById('loadingOverlay').classList.remove('flex');
}

// ── Fetch Nannies ─────────────────────────────────────────────────────────────
async function fetchNannies() {
    renderNannySkeleton();
    try {
        const res  = await fetch(`${API_BASE}/konsultan-nanny`, {
            headers: {
                'Accept':        'application/json',
                'Content-Type':  'application/json',
                'Authorization': `Bearer ${AUTH_TOKEN}`,
            }
        });
        const data = await res.json();

        if (data.status === 'success' && Array.isArray(data.data)) {
            allNannies = data.data.filter(n => n.is_assigned && n.assignment_status === 'active');
        } else {
            allNannies = [];
        }
    } catch (e) {
        console.error('Fetch nannies error:', e);
        allNannies = [];
        showToast('error', 'Failed to load', 'Unable to connect to the server');
    }

    renderNannies(allNannies);
    updateCountBadge();
}

function renderNannySkeleton() {
    const list = document.getElementById('nannyList');
    list.innerHTML = Array.from({length: 3}).map(() => `
        <div class="bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5] flex items-center gap-3">
            <div class="skeleton w-[50px] h-[50px] rounded-[8px] flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
                <div class="skeleton h-4 w-32 rounded"></div>
                <div class="skeleton h-3 w-44 rounded"></div>
                <div class="skeleton h-3 w-20 rounded"></div>
            </div>
        </div>
    `).join('');
}

function renderNannies(list) {
    const container = document.getElementById('nannyList');

    if (list.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center py-12 text-center">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
                    <ion-icon name="person-outline" style="font-size:44px;color:#C4B5FD;"></ion-icon>
                </div>
                <p class="text-[#1E1B2E] font-extrabold text-lg mb-2">No active nannies</p>
                <p class="text-[#9CA3AF] text-sm">Active nannies on duty will appear here</p>
            </div>`;
        return;
    }

    container.innerHTML = list.map((item, i) => `
        <button onclick="selectNanny(${item.id})"
                class="nanny-card w-full text-left bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5] fade-in"
                style="animation-delay:${i * 0.06}s">
            <div class="flex items-center gap-3">
                <div class="w-[50px] h-[50px] rounded-[8px] overflow-hidden flex-shrink-0 bg-[#F3F0FD] flex items-center justify-center">
                    ${item.foto
                        ? `<img src="${item.foto}" class="w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
                           <div style="display:none" class="w-full h-full items-center justify-center"><ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon></div>`
                        : `<ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">${item.name || '-'}</p>
                        <span class="bg-[#DCFCE7] text-[#166534] text-[10px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0">ACTIVE</span>
                    </div>
                    <div class="flex items-center gap-1 mt-0.5">
                        <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;"></ion-icon>
                        <span class="text-[#1E1B2E] text-[12px] font-extrabold truncate">Nanny</span>
                    </div>
                    <p class="text-[#8B86A5] text-[11px] italic font-semibold mt-0.5 truncate">"${item.email || 'Ready to create a diary recap'}"</p>
                </div>
            </div>
        </button>
    `).join('');
}

function filterNannies(query) {
    const q = query.toLowerCase().trim();
    const filtered = q ? allNannies.filter(n =>
        String(n.name || '').toLowerCase().includes(q) || String(n.email || '').toLowerCase().includes(q)
    ) : allNannies;
    renderNannies(filtered);
    updateCountBadge(filtered.length);
}

function updateCountBadge(count) {
    const badge = document.getElementById('nannyCountBadge');
    const n = count !== undefined ? count : allNannies.length;
    if (n > 0) {
        badge.textContent = n + ' Nanny';
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function selectNanny(id) {
    selectedNanny = allNannies.find(n => n.id === id);
    if (!selectedNanny) return;
    showStep2();
}

// ── Step Navigation ───────────────────────────────────────────────────────────
function showStep1() {
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('mainBody').scrollTop = 0;
}

function showStep2() {
    document.getElementById('selName').textContent  = selectedNanny.name;
    document.getElementById('selEmail').textContent = selectedNanny.email || '-';
    document.getElementById('selAssignmentId').textContent = selectedNanny.assignment_id || selectedNanny.id_assignment || selectedNanny.id || '-';
    document.getElementById('selPhone').textContent = selectedNanny.no_hp || selectedNanny.phone || selectedNanny.nanny_no_hp || selectedNanny.nomor_hp || '-';

    const avatarEl = document.getElementById('selAvatar');
    if (selectedNanny.foto) {
        avatarEl.innerHTML = `<img src="${selectedNanny.foto}" class="w-full h-full object-cover" />`;
    } else {
        avatarEl.innerHTML = `<ion-icon name="person" style="font-size:30px;color:#8B46D3;"></ion-icon>`;
    }

    const now   = new Date();
    const start = new Date(); start.setDate(start.getDate() - 30);
    document.getElementById('startDate').value = formatDate(start);
    document.getElementById('endDate').value   = formatDate(now);
    renderCategoryChips();

    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
    document.getElementById('mainBody').scrollTop = 0;
}

function backToStep1() {
    selectedNanny = null;
    showStep1();
    document.getElementById('searchInput').value = '';
    filterNannies('');
}

// ── Date Utils ────────────────────────────────────────────────────────────────
function formatDate(d) {
    const y  = d.getFullYear();
    const mo = String(d.getMonth() + 1).padStart(2,'0');
    const da = String(d.getDate()).padStart(2,'0');
    return `${y}-${mo}-${da}`;
}

function getDaysInMonth(year, month) {
    return new Date(year, month + 1, 0).getDate();
}

// ── Date Picker ───────────────────────────────────────────────────────────────
function openDatePicker(target) {
    dpTarget = target;
    const val = document.getElementById(target === 'start' ? 'startDate' : 'endDate').value;
    const d   = val ? new Date(val + 'T00:00:00') : new Date();
    dpYear    = d.getFullYear();
    dpMonth   = d.getMonth();
    dpDay     = d.getDate();

    document.getElementById('dpTitle').textContent = target === 'start' ? 'Start Date' : 'End Date';

    buildDateCols();
    updateDpPreview();

    document.getElementById('datePickerModal').classList.remove('hidden');
    document.getElementById('datePickerModal').classList.add('flex');
}

function closeDatePicker() {
    document.getElementById('datePickerModal').classList.add('hidden');
    document.getElementById('datePickerModal').classList.remove('flex');
}

function closeDatePickerOnOverlay(e) {
    if (e.target === document.getElementById('datePickerModal')) closeDatePicker();
}

function confirmDatePicker() {
    const str = `${dpYear}-${String(dpMonth+1).padStart(2,'0')}-${String(dpDay).padStart(2,'0')}`;
    if (dpTarget === 'start') {
        document.getElementById('startDate').value = str;
        document.getElementById('errStart').classList.add('hidden');
        document.getElementById('startDateWrapper').classList.remove('border-red-400');
    } else {
        document.getElementById('endDate').value = str;
        document.getElementById('errEnd').classList.add('hidden');
        document.getElementById('endDateWrapper').classList.remove('border-red-400');
    }
    closeDatePicker();
}

function buildDateCols() {
    const yearCol = document.getElementById('yearCol');
    yearCol.innerHTML = '';
    const curYear = new Date().getFullYear();
    for (let y = curYear; y >= 2000; y--) {
        const btn = document.createElement('button');
        btn.className = `picker-item w-full py-2.5 text-sm font-semibold text-center transition-colors ${y === dpYear ? 'bg-[#8B46D3] text-white rounded-xl mx-1' : 'text-[#1E1B2E] hover:bg-[#EDE9FE]'}`;
        btn.textContent = y;
        btn.onclick = () => { dpYear = y; buildDateCols(); updateDpPreview(); };
        yearCol.appendChild(btn);
        if (y === dpYear) setTimeout(() => btn.scrollIntoView({ block: 'center', behavior: 'smooth' }), 50);
    }

    const monthCol = document.getElementById('monthCol');
    monthCol.innerHTML = '';
    MONTHS_ID.forEach((m, i) => {
        const btn = document.createElement('button');
        btn.className = `picker-item w-full py-2.5 text-xs font-semibold text-center transition-colors ${i === dpMonth ? 'bg-[#8B46D3] text-white rounded-xl mx-1' : 'text-[#1E1B2E] hover:bg-[#EDE9FE]'}`;
        btn.textContent = m;
        btn.onclick = () => {
            dpMonth = i;
            const maxD = getDaysInMonth(dpYear, dpMonth);
            if (dpDay > maxD) dpDay = maxD;
            buildDateCols(); updateDpPreview();
        };
        monthCol.appendChild(btn);
        if (i === dpMonth) setTimeout(() => btn.scrollIntoView({ block: 'center', behavior: 'smooth' }), 50);
    });

    const dayCol = document.getElementById('dayCol');
    dayCol.innerHTML = '';
    const maxDay = getDaysInMonth(dpYear, dpMonth);
    for (let d = 1; d <= maxDay; d++) {
        const btn = document.createElement('button');
        btn.className = `picker-item w-full py-2.5 text-sm font-semibold text-center transition-colors ${d === dpDay ? 'bg-[#8B46D3] text-white rounded-xl mx-1' : 'text-[#1E1B2E] hover:bg-[#EDE9FE]'}`;
        btn.textContent = d;
        btn.onclick = () => { dpDay = d; buildDateCols(); updateDpPreview(); };
        dayCol.appendChild(btn);
        if (d === dpDay) setTimeout(() => btn.scrollIntoView({ block: 'center', behavior: 'smooth' }), 50);
    }
}

function updateDpPreview() {
    document.getElementById('dpPreview').textContent =
        `${String(dpDay).padStart(2,'0')} ${MONTHS_ID[dpMonth]} ${dpYear}`;
}

// ── Kategori Chips ────────────────────────────────────────────────────────────
function renderCategoryChips() {
    const wrap = document.getElementById('categoryChips');
    if (!wrap) return;

    wrap.innerHTML = KATEGORI_OPTIONS.map(opt => {
        const active = selectedKategori === opt.value;
        return `
            <button type="button"
                    onclick="selectKategori('${opt.value}')"
                    class="category-chip ${active ? 'active' : ''} inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-extrabold"
                    style="background:${opt.bg};color:${opt.color};">
                <ion-icon name="${opt.icon}" style="font-size:11px;"></ion-icon>
                <span>${opt.short}</span>
            </button>
        `;
    }).join('');
}

function openKategoriModal() {
    const list = document.getElementById('kategoriList');
    list.innerHTML = KATEGORI_OPTIONS.map(opt => `
        <button onclick="selectKategori('${opt.value}')"
                class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl transition-all ${selectedKategori === opt.value ? 'bg-[#8B46D3] text-white shadow-lg shadow-[#8B46D3]/25' : 'bg-[#F8F7FF] hover:bg-[#EDE9FE] text-[#1E1B2E]'}">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl ${selectedKategori === opt.value ? 'bg-white/20' : 'bg-white'} flex items-center justify-center">
                    <ion-icon name="${opt.icon}" style="font-size:16px;color:${selectedKategori === opt.value ? '#fff' : '#7B1E5A'};"></ion-icon>
                </div>
                <span class="font-semibold text-sm">${opt.label}</span>
            </div>
            ${selectedKategori === opt.value
                ? `<div class="w-6 h-6 rounded-full bg-white/25 flex items-center justify-center">
                       <ion-icon name="checkmark" style="font-size:14px;color:#fff;"></ion-icon>
                   </div>`
                : ''}
        </button>
    `).join('');

    document.getElementById('kategoriModal').classList.remove('hidden');
    document.getElementById('kategoriModal').classList.add('flex');
}

function closeKategoriModal() {
    document.getElementById('kategoriModal').classList.add('hidden');
    document.getElementById('kategoriModal').classList.remove('flex');
}

function closeKategoriOnOverlay(e) {
    if (e.target === document.getElementById('kategoriModal')) closeKategoriModal();
}

function selectKategori(val) {
    selectedKategori = val;
    const opt = KATEGORI_OPTIONS.find(o => o.value === val);
    document.getElementById('kategoriLabel').textContent = opt ? opt.label : 'All Categories';
    document.getElementById('kategoriLabel').className = 'hidden';
    renderCategoryChips();
    closeKategoriModal();
}

// ── Validate ──────────────────────────────────────────────────────────────────
function validateForm() {
    let valid = true;
    const start = document.getElementById('startDate').value;
    const end   = document.getElementById('endDate').value;

    if (!start) {
        document.getElementById('errStart').classList.remove('hidden');
        document.getElementById('startDateWrapper').classList.add('border-red-400');
        valid = false;
    } else {
        document.getElementById('errStart').classList.add('hidden');
        document.getElementById('startDateWrapper').classList.remove('border-red-400');
    }

    if (!end) {
        document.getElementById('errEnd').classList.remove('hidden');
        document.getElementById('endDateWrapper').classList.add('border-red-400');
        valid = false;
    } else {
        document.getElementById('errEnd').classList.add('hidden');
        document.getElementById('endDateWrapper').classList.remove('border-red-400');
    }

    if (start && end && new Date(start) > new Date(end)) {
        document.getElementById('errEnd').textContent = 'End date must be after start date';
        document.getElementById('errEnd').classList.remove('hidden');
        document.getElementById('endDateWrapper').classList.add('border-red-400');
        valid = false;
    }

    return valid;
}

// ── Generate Report ───────────────────────────────────────────────────────────
async function handleGenerate() {
    if (!validateForm()) {
        showToast('error', 'Incomplete form', 'Please check the date fields');
        return;
    }

    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    showLoading('Generating Report...', 'Processing nanny diary data');

    let prog = 20;
    const progInterval = setInterval(() => {
        prog = Math.min(prog + Math.random() * 15, 85);
        setProgress(Math.round(prog));
    }, 500);

    try {
        const fd = new FormData();
        fd.append('id_nanny',       selectedNanny.id);
        fd.append('tanggal_mulai',  document.getElementById('startDate').value);
        fd.append('tanggal_selesai',document.getElementById('endDate').value);
        fd.append('export',         'excel');
        if (selectedKategori) fd.append('kategori', selectedKategori);

        const res  = await fetch(`${API_BASE}/diary-for-konsultan`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${AUTH_TOKEN}`, 'Accept': 'application/json' },
            body: fd,
        });
        const data = await res.json();

        clearInterval(progInterval);
        setProgress(100);

        if (data.status === 'success' && data.data?.download_url) {
            setTimeout(() => {
                hideLoading();
                downloadFile(data.data.download_url, data.data.filename || 'rekap-diary.xlsx');
            }, 500);
        } else {
            hideLoading();
            showToast('error', 'Generation failed', data.message || 'An error occurred on the server');
        }
    } catch (e) {
        clearInterval(progInterval);
        hideLoading();
        console.error(e);
        showToast('error', 'Connection failed', 'Unable to connect to the server');
    } finally {
        btn.disabled = false;
    }
}

function downloadFile(url, filename) {
    showLoading('Downloading File...', 'The Excel file is being downloaded');

    const anchor = document.createElement('a');
    anchor.href     = url;
    anchor.download = filename;
    anchor.target   = '_blank';
    document.body.appendChild(anchor);
    anchor.click();
    document.body.removeChild(anchor);

    setTimeout(() => {
        hideLoading();
        showToast('success', 'Success!', `File "${filename}" downloaded successfully`);
    }, 1200);
}

// ── Init ──────────────────────────────────────────────────────────────────────
fetchNannies();
</script>
@endpush
