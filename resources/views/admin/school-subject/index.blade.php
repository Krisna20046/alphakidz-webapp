@extends('layouts.app')

@section('title', 'School Subjects')

@push('styles')
<style>
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

    .subject-card { transition:transform .15s ease,box-shadow .15s ease; }
    .subject-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(139,70,211,.12); }

    @keyframes spin { to{transform:rotate(360deg);} }
    .page-loader { animation:spin .6s linear infinite; }

    .modal-backdrop {
        position:fixed; inset:0; background:rgba(0,0,0,.55);
        display:flex; align-items:flex-end; justify-content:center;
        z-index:50; opacity:0; pointer-events:none;
        transition:opacity .25s ease;
    }
    .modal-backdrop.open { opacity:1; pointer-events:all; }
    .modal-sheet {
        background: #fff;
        width: 100%;
        max-width: 390px;
        border-radius: 24px 24px 0 0;
        transform: translateY(100%);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
        display: flex;
        flex-direction: column;
        max-height: 85vh;
    }
    .modal-backdrop.open .modal-sheet { transform: translateY(0); }
    .handle-container {
        flex-shrink: 0;
        background: white;
        border-radius: 24px 24px 0 0;
    }
    .scrollable-content {
        overflow-y: auto;
        flex: 1;
        -webkit-overflow-scrolling: touch;
    }
    .handle-container,
    .scrollable-content { background: white; }

    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }

    body.modal-open { overflow:hidden; }

    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }

    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(.92); }

    .fab-wrap {
        position: fixed;
        bottom: 80px;
        right: calc(50% - 175px);
        z-index: 30;
    }
    @media (max-width: 639px) {
        .fab-wrap { right: 20px; }
    }
    @keyframes fabIn {
        0%   { transform:scale(0) rotate(-20deg); opacity:0; }
        70%  { transform:scale(1.1) rotate(5deg); }
        100% { transform:scale(1) rotate(0); opacity:1; }
    }
    .fab-in { animation:fabIn .5s cubic-bezier(.34,1.56,.64,1) .3s forwards; opacity:0; }

    .search-wrapper:focus-within {
        border-color: #8B46D3;
        box-shadow: 0 0 0 3px rgba(139,70,211,0.14);
    }

    .subject-icon {
        width:48px; height:48px; border-radius:14px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
        color:#fff; font-size:22px;
    }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">School Subjects</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">
                @if($pagination)
                    {{ $pagination['total'] }} subjects
                @else
                    {{ count($subjects) }} subjects
                @endif
            </p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Flash Toast --}}
    @if(session('success') || session('error'))
    <div id="toast" class="toast rounded-2xl px-4 py-3 flex items-center gap-3
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

    {{-- Search --}}
    <div class="anim delay-2">
        <div class="search-wrapper flex items-center gap-2 bg-white rounded-full px-4 py-2.5 border border-[#DDD6EF]">
            <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
            <input id="searchInput" type="text" placeholder="Search subject name..."
                class="flex-1 bg-transparent text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] outline-none"
                oninput="filterSubjects()">
            <button id="clearSearch" onclick="clearSearch()" class="hidden">
                <ion-icon name="close-circle" style="font-size:16px;color:#8B86A5;"></ion-icon>
            </button>
        </div>
    </div>

    {{-- Result count --}}
    <div class="flex items-center justify-between">
        <p class="text-xs font-bold text-[#8B86A5]">
            Showing <span id="resultCount" class="text-[#8B46D3]">{{ count($subjects) }}</span> subjects
        </p>
        @if($pagination)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button id="pagePrev" onclick="goToPage({{ $pagination['current_page'] - 1 }})"
                class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : 'hover:border-[#8B46D3] hover:text-[#8B46D3]' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]">
                <span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}
            </span>
            <button id="pageNext" onclick="goToPage({{ $pagination['current_page'] + 1 }})"
                class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : 'hover:border-[#8B46D3] hover:text-[#8B46D3]' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
        @endif
    </div>

    {{-- Subject List --}}
    <div id="subjectList" class="space-y-3">

        {{-- Empty state --}}
        <div id="emptyState" class="{{ count($subjects) > 0 ? 'hidden' : '' }} flex flex-col items-center py-16">
            <div class="float-anim w-20 h-20 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
                <ion-icon name="book-outline" style="font-size:36px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-extrabold text-base mb-1">No subjects yet</h3>
            <p class="text-[#8B86A5] text-xs text-center">Add a subject to start filling in the school schedule</p>
            <a href="{{ route('admin-school-subject.create') }}"
               class="mt-4 px-5 py-2 rounded-xl bg-[#8B46D3] text-white text-sm font-bold">
                Add School Subject
            </a>
        </div>

        {{-- Subject Cards --}}
        <div id="cardsContainer" class="space-y-3">
        @foreach($subjects as $subject)
        @php
            $initial = strtoupper(substr($subject['name'] ?? '?', 0, 1));
            $color   = !empty($subject['color']) ? $subject['color'] : '#8B46D3';
        @endphp

        <div class="subject-card bg-white rounded-2xl p-4 border border-[#DDD6EF]"
             data-name="{{ strtolower($subject['name'] ?? '') }}">

            <div class="flex items-center gap-3">
                <div class="subject-icon" style="background:{{ $color }};">
                    @if(!empty($subject['icon']) && str_starts_with($subject['icon'], 'http'))
                        <img src="{{ $subject['icon'] }}" alt="icon" class="w-6 h-6 object-contain">
                    @elseif(!empty($subject['icon']))
                        <ion-icon name="{{ $subject['icon'] }}" style="font-size:22px;"></ion-icon>
                    @else
                        <span>{{ $initial }}</span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-[#1E1B2E] font-bold text-sm truncate">{{ $subject['name'] }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if(!empty($subject['icon']))
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#EDE9FE] text-[#8B46D3]">{{ $subject['icon'] }}</span>
                        @endif
                        <span class="flex items-center gap-1 text-[10px] font-bold" style="color:{{ $color }};">
                            <span class="w-2 h-2 rounded-full inline-block" style="background:{{ $color }};"></span>
                            {{ $color }}
                        </span>
                    </div>
                </div>

                <button type="button" onclick="openDetail({{ json_encode([
                    'id'    => $subject['id'],
                    'name'  => $subject['name'],
                    'icon'  => $subject['icon'] ?? '',
                    'color' => $color,
                    'editUrl' => route('admin-school-subject.edit', $subject['id'])
                ]) }})"
                    class="act-btn flex items-center gap-1 py-2 px-3 rounded-xl bg-[#EDE9FE] text-[#8B46D3] text-xs font-bold">
                    <ion-icon name="eye-outline" style="font-size:14px;"></ion-icon>
                    Detail
                </button>
            </div>
        </div>
        @endforeach
        </div>
    </div>

</div>{{-- /rounded-top section --}}

{{-- Page loading overlay --}}
<div id="pageLoader" class="hidden fixed inset-0 z-50 bg-white/60 flex items-center justify-center">
    <div class="flex items-center gap-3 bg-white rounded-2xl px-6 py-4 shadow-xl border border-[#EDE9FE]">
        <div class="page-loader w-5 h-5 border-2 border-[#EDE9FE] border-t-[#8B46D3] rounded-full"></div>
        <span class="text-sm font-bold text-[#8B46D3]">Loading...</span>
    </div>
</div>

{{-- FAB --}}
<div class="fab-wrap fab-in">
    <a href="{{ route('admin-school-subject.create') }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>

{{-- DETAIL MODAL --}}
<div id="detailModal" class="modal-backdrop">
    <div class="modal-sheet" id="detailSheet">
        <div class="handle-container">
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#EDE9FE]">
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">Subject Details</h2>
                <button onclick="closeDetail()" class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </button>
            </div>
        </div>

        <div class="scrollable-content">
            <div class="px-5 pt-5 pb-4 flex items-center gap-4 border-b border-[#EDE9FE]/50">
                <div id="dIcon" class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 text-white text-3xl font-bold"></div>
                <div class="flex-1 min-w-0">
                    <p id="dName" class="text-[#1E1B2E] font-extrabold text-lg leading-tight truncate"></p>
                    <p id="dColorText" class="text-[#8B86A5] text-sm mt-0.5 flex items-center gap-1.5">
                        <span id="dColorDot" class="w-3 h-3 rounded-full inline-block"></span>
                        <span id="dColorHex"></span>
                    </p>
                </div>
            </div>

            <div class="px-5 py-4 space-y-5">
                <div>
                    <p class="text-[#8B46D3] text-xs font-bold uppercase tracking-wider mb-3">Icon Information</p>
                    <div class="flex items-start gap-3">
                        <ion-icon name="color-palette-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Ikon</p>
                            <p id="dIconVal" class="text-[#1E1B2E] text-sm font-bold mt-0.5">-</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[#1B46D3] text-xs font-bold uppercase tracking-wider mb-3">Color Information</p>
                    <div class="flex items-start gap-3">
                        <ion-icon name="earth-outline" style="font-size:16px;color:#8B46D3;flex-shrink:0;margin-top:2px;"></ion-icon>
                        <div>
                            <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Color Code</p>
                            <p id="dColorHex2" class="text-sm text-[#1E1B2E] font-bold mt-0.5">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-5 pb-20">
                <a id="dEditBtn" href="#"
                   class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-[#8B46D3] text-white font-bold text-sm shadow-lg shadow-[#8B46D3]/30">
                    <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                    Edit Subject Data
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Search ──────────────────────────────────────────────────────────────
function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearch').classList.add('hidden');
    filterSubjects();
}

function filterSubjects() {
    const query    = document.getElementById('searchInput').value.toLowerCase().trim();
    const cards    = document.querySelectorAll('#cardsContainer .subject-card');
    const clearBtn = document.getElementById('clearSearch');
    clearBtn.classList.toggle('hidden', !query);

    let visible = 0;
    cards.forEach(card => {
        const match = !query || (card.dataset.name || '').includes(query);
        card.classList.toggle('hidden', !match);
        if (match) visible++;
    });

    document.getElementById('resultCount').textContent = visible;
    document.getElementById('emptyState').classList.toggle('hidden', visible > 0);
}

// ── Detail Modal ───────────────────────────────────────────────────────────
function openDetail(s) {
    const icon = document.getElementById('dIcon');
    icon.style.background = s.color;
    if (s.name) icon.innerHTML = '<ion-icon name="book-outline" style="font-size:34px;"></ion-icon>';

    document.getElementById('dName').textContent = s.name;
    document.getElementById('dColorDot').style.background = s.color;
    document.getElementById('dColorHex').textContent = s.color;
    document.getElementById('dIconVal').textContent = s.icon || 'None';
    document.getElementById('dColorHex2').textContent = s.color;
    document.getElementById('dEditBtn').href = s.editUrl;

    document.getElementById('detailModal').classList.add('open');
    document.body.classList.add('modal-open');
}

function closeDetail() {
    document.getElementById('detailModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});

// ── Pagination ──────────────────────────────────────────────────────────
function goToPage(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    document.getElementById('pageLoader').classList.remove('hidden');
    window.location.href = url.toString();
}

// Toast auto-dismiss
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush