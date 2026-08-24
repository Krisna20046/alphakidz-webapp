@php
    $roleId = session('user')['id_role'] ?? null;
    $isNexus = ($roleId == 5);
    $userName = session('user')['name'] ?? 'User';
@endphp

@extends('layouts.app')

@php $activeNav = 'home' @endphp

@section('title', $isNexus ? 'Nexus Dashboard' : 'My Questions')

@push('styles')
<style>
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

    .search-input:focus { outline: none; }
    .q-item { transition: transform .15s ease; }
    .q-item:active { transform: scale(0.98); }

    .tab-btn {
        border-radius: 12px;
        padding: 8px 14px;
        color: #7C7893;
        font-size: 13px;
        font-weight: 800;
        line-height: 1;
        transition: all .15s ease;
        background: white;
        border: none;
        cursor: pointer;
    }
    .tab-btn.active {
        background: linear-gradient(to right, #7C3AED, #8B46D3);
        color: white;
        box-shadow: 0 6px 14px rgba(139,70,211,0.28);
    }

    .badge-open { background: #FFF3E0; color: #E65100; }
    .badge-claimed { background: #E8F5E9; color: #2E7D32; }
    .badge-answered { background: #E3F2FD; color: #1565C0; }
    .badge-closed { background: #F3E5F5; color: #7B1FA2; }

    .q-card {
        border-radius: 14px;
        padding: 14px 16px;
        border: 1.5px solid #F0ECF9;
        transition: all .15s ease;
        cursor: default;
        position: relative;
    }
    .q-card.clickable { cursor: pointer; }
    .q-card:active.clickable { transform: scale(0.98); }
    /* Status strip */
    .q-card .status-strip {
        position: absolute;
        left: 0; top: 8px; bottom: 8px; width: 4px;
        border-radius: 0 4px 4px 0;
    }
    .q-card .status-strip.open { background: #FF9800; }
    .q-card .status-strip.claimed { background: #4CAF50; }
    .q-card .status-strip.answered { background: #2196F3; }
    .q-card .status-strip.closed { background: #9C27B0; }

    .fab-add {
        position: fixed; bottom: 80px; right: 20px; z-index: 100;
        width: 56px; height: 56px; border-radius: 50%;
        background: #8B46D3; color: white; border: none;
        font-size: 28px; box-shadow: 0 4px 16px rgba(139,70,211,.4);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: transform .12s;
    }
    .fab-add:active { transform: scale(.9); }

    /* Modal */
    #waitModal { backdrop-filter: blur(4px); }
    @keyframes modalIn { from { opacity:0; transform:scale(.85) translateY(20px); } to { opacity:1; transform:scale(1) translateY(0); } }
    .animate-modal-in { animation: modalIn .25s ease-out; }
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
            <span class="text-white text-[17px] font-extrabold tracking-wide">
                @if($isNexus) Nexus Dashboard @else My Questions @endif
            </span>
            <p id="headerSubtitle" class="text-white/70 text-xs font-semibold mt-0.5">Loading...</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    {{-- Search + Filter Card --}}
    <div class="anim delay-2 bg-white rounded-[20px] p-4 shadow-[0_2px_12px_rgba(0,0,0,0.08)]">
        <div class="flex items-center bg-[#F4F4F4] rounded-[10px] border border-[#DDD6EF] px-3 py-2.5 mb-3">
            <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
            <input type="text" id="searchInput" placeholder="Search questions..."
                   class="search-input flex-1 text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] bg-transparent ml-2"
                   oninput="applyFilters()">
        </div>

        <div class="border border-[#DDD6EF] rounded-[10px] p-1 grid gap-1" id="filterTabs">
            {{-- Filter tabs di-render oleh JS berdasarkan role --}}
        </div>
    </div>

    {{-- Question List Area --}}
    <div id="qListArea" class="pt-3 pb-2">
        {{-- Skeleton --}}
        <div id="skeletonLoader" class="space-y-3">
            @for($i=0;$i<5;$i++)
            <div class="bg-white rounded-[14px] p-3.5 flex items-center gap-3 animate-pulse">
                <div class="w-[44px] h-[44px] rounded-[8px] bg-[#ECE8FA] shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3.5 bg-[#ECE8FA] rounded-full w-3/4"></div>
                    <div class="h-3 bg-[#ECE8FA] rounded-full w-1/2"></div>
                </div>
                <div class="h-5 w-14 bg-[#ECE8FA] rounded-full"></div>
            </div>
            @endfor
        </div>

        {{-- Real list --}}
        <div id="qList" class="space-y-3 hidden"></div>

        {{-- Empty state --}}
        <div id="emptyState" class="hidden flex flex-col items-center pt-16 pb-8 px-8">
            <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                <ion-icon id="emptyIcon" name="chatbubble-ellipses-outline" style="font-size:48px;color:#C4B5FD;"></ion-icon>
            </div>
            <h3 id="emptyTitle" class="text-[#1E1B2E] font-bold text-lg mb-2 text-center">No questions yet</h3>
            <p id="emptyDesc" class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                @if($isNexus)
                    No questions have come in yet
                @else
                    Ask a new question with the + button below
                @endif
            </p>
        </div>
    </div>
</div>

@if(!$isNexus)
    <a href="{{ route('nexus.create') }}" class="fab-add">+</a>
@endif

{{-- Modal peringatan untuk status open yang belum di-claim --}}
<div id="waitModal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/40" style="display:none;">
    <div class="animate-modal-in bg-white rounded-[24px] px-6 py-8 mx-6 max-w-sm w-full shadow-2xl text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#FFF3E0] flex items-center justify-center">
            <ion-icon name="hourglass-outline" style="font-size:36px;color:#E65100;"></ion-icon>
        </div>
        <h3 class="text-[#1E1B2E] text-lg font-extrabold mb-2">Waiting for Nexus</h3>
        <p class="text-[#7C7893] text-sm font-semibold leading-relaxed mb-6">
            This question is still waiting for Nexus to take your turn. Please wait a moment.
        </p>
        <button onclick="closeWaitModal()"
                class="bg-[#8B46D3] text-white font-extrabold text-sm px-8 py-3 rounded-[14px] border-none cursor-pointer transition-transform active:scale-95 w-full">
            Got It
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = "{{ csrf_token() }}";
const API_BASE = '{{ rtrim(config("services.api.base_url", env("API_BASE_URL", "")), "/") }}';
const isNexus = {{ $isNexus ? 'true' : 'false' }};

// ── Data ──
let allQuestions = [];
let currentFilter = 'all';
const isNexusMode = isNexus; // true = filter by role; false = filter by status

// ── Init filter tabs based on role ──
(function initFilters() {
    const container = document.getElementById('filterTabs');
    if (isNexusMode) {
        // Nexus: filter by role pengirim
        const tabs = [
            { key: 'all', label: 'All' },
            { key: 'Majikan', label: 'Employer' },
            { key: 'Nanny', label: 'Nanny' },
            { key: 'Konsultan', label: 'Consultant' },
        ];
        container.className = 'border border-[#DDD6EF] rounded-[10px] p-1 grid grid-cols-4 gap-1';
        container.innerHTML = tabs.map(t =>
            `<button class="tab-btn ${t.key === 'all' ? 'active' : ''}" data-tab="${t.key}" onclick="setFilter('${t.key}')">${t.label}</button>`
        ).join('');
    } else {
        // Non-Nexus: filter by status (open, claimed, answered, closed)
        const tabs = [
            { key: 'all', label: 'All' },
            { key: 'open', label: 'Open' },
            { key: 'claimed', label: 'In Progress' },
            { key: 'answered', label: 'Answered' },
            { key: 'closed', label: 'Closed' },
        ];
        container.className = 'border border-[#DDD6EF] rounded-[10px] p-1 grid grid-cols-5 gap-1';
        container.innerHTML = tabs.map(t =>
            `<button class="tab-btn ${t.key === 'all' ? 'active' : ''}" data-tab="${t.key}" onclick="setFilter('${t.key}')">${t.label}</button>`
        ).join('');
    }
})();

// ── Helpers ──
function statusBadge(s) {
    const map = { open:'badge-open', claimed:'badge-claimed', answered:'badge-answered', closed:'badge-closed' };
    const lbl = { open:'Open', claimed:'In Progress', answered:'Answered', closed:'Closed' };
    return `<span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-[.5px] ${map[s]||'badge-open'}">${lbl[s]||s}</span>`;
}

function formatTime(ts) {
    const d = new Date(ts);
    const now = new Date();
    const diff = Math.floor((now - d) / (1000 * 60));
    if (diff < 1) return 'just now';
    if (diff < 60) return diff + 'm';
    const jam = Math.floor(diff / 60);
    if (jam < 24) return jam + 'h';
    const hari = Math.floor(jam / 24);
    return hari + 'd';
}

function initials(name) {
    return (name || '?').charAt(0).toUpperCase();
}

// ── Render ──
function buildCardHTML(q, idx) {
    const link = `{{ route("nexus.show", "") }}/${q.id}`;
    const isUnclaimedOpen = q.status === 'open' && !q.claimed_by;
    const isClickable = isUnclaimedOpen || (q.status !== 'open' && q.status !== 'closed');
    const cardClass = isClickable ? 'q-card clickable' : 'q-card bg-white shadow-[0_1px_4px_rgba(0,0,0,.06)]';

    let onclick;
    if (isUnclaimedOpen) {
        onclick = `onclick="showWaitModal(event)"`;
    } else if (isClickable) {
        onclick = `onclick="window.location='${link}'"`;
    } else {
        onclick = '';
    }

    const canClaim = isNexus && q.status === 'open' && !q.claimed_by;
    const askerName = q.asked_by?.name || 'User';
    const askerRole = q.asked_by?.role?.nama || '';
    const msgCount = q.messages_count || 0;

    return `
    <div class="${cardClass}" ${onclick} style="animation: slideUp .35s ease ${idx * 0.04}s both; opacity:0;">
        <div class="status-strip ${q.status}"></div>
        <div class="flex items-start gap-3" style="position:relative;">
            <div class="w-[44px] h-[44px] rounded-[8px] flex items-center justify-center text-[#8B46D3] font-extrabold text-base bg-[#F3F0FD] shrink-0">${initials(askerName)}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-[#1E1B2E] text-[14px] font-extrabold truncate">${q.judul}</p>
                    <span class="text-[#A8A2C2] text-[11px] font-semibold shrink-0 whitespace-nowrap">${formatTime(q.created_at)}</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    ${statusBadge(q.status)}
                    ${q.kategori ? `<span class="text-[10px] font-bold text-[#8B46D3] bg-[#EDE9FE] px-2.5 py-0.5 rounded-full">${q.kategori}</span>` : ''}
                    <span class="text-[11px] text-[#A8A2C2] font-semibold">· ${askerName}</span>
                    ${msgCount > 0 ? `<span class="text-[11px] text-[#A8A2C2] font-semibold">· ${msgCount} messages</span>` : ''}
                </div>
                ${q.claimed_by ? `<div class="mt-1.5 text-[11px] text-[#7B52AB] font-semibold">👤 ${q.claimed_by?.name || 'Nexus'}</div>` : ''}
                ${canClaim ? `<div class="mt-2"><button class="inline-block bg-[#8B46D3] text-white text-[11px] font-extrabold px-4 py-1.5 rounded-[10px] border-none cursor-pointer transition-transform active:scale-95" onclick="event.stopPropagation();claimQuestion(${q.id})">Take</button></div>` : ''}
            </div>
        </div>
    </div>`;
}

function renderQuestions(items) {
    const list = document.getElementById('qList');
    const empty = document.getElementById('emptyState');
    const skeleton = document.getElementById('skeletonLoader');

    const total = items.length;
    document.getElementById('headerSubtitle').textContent =
        isNexus ? `${total} questions` : `${total} questions`;

    skeleton.style.display = 'none';
    list.classList.remove('hidden');

    if (total === 0) {
        list.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }

    empty.classList.add('hidden');
    list.innerHTML = items
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .map((q, idx) => buildCardHTML(q, idx))
        .join('');
}

// ── Filters ──
function setFilter(tab) {
    currentFilter = tab;
    document.querySelectorAll('.tab-btn').forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    applyFilters();
}

function getRoleFromAsker(q) {
    return q.asked_by?.role?.nama || q.asked_by?.name || '';
}

function applyFilters() {
    const q = (document.getElementById('searchInput').value || '').toLowerCase().trim();
    let filtered = [...allQuestions];

    if (currentFilter !== 'all') {
        if (isNexusMode) {
            // Filter by role pengirim
            filtered = filtered.filter(f =>
                getRoleFromAsker(f).toLowerCase() === currentFilter.toLowerCase()
            );
        } else {
            // Filter by status pertanyaan
            filtered = filtered.filter(f => f.status === currentFilter);
        }
    }

    if (q) {
        filtered = filtered.filter(f =>
            (f.judul || '').toLowerCase().includes(q) ||
            (f.asked_by?.name || '').toLowerCase().includes(q)
        );
    }

    if (filtered.length === 0) {
        const isSearch = !!q;
        document.getElementById('emptyIcon').setAttribute('name', isSearch ? 'search-outline' : 'chatbubble-ellipses-outline');
        document.getElementById('emptyTitle').textContent = isSearch ? 'Question not found' : 'No questions yet';
        document.getElementById('emptyDesc').textContent = isSearch ? `Nothing matches "${q}"` : (isNexus ? 'No questions have come in yet' : 'Ask a new question with the + button below');
        renderQuestions([]);
    } else {
        renderQuestions(filtered);
    }
}

// ── Fetch ──
async function loadQuestions() {
    try {
        const res = await fetch(`${API_BASE}/nexus`, {
            headers: { 'Authorization': 'Bearer {{ session("token") }}', 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Failed to load');
        const json = await res.json();

        // For Nexus: data is grouped by role. Flatten it.
        if (isNexus && Array.isArray(json.data)) {
            allQuestions = json.data.flatMap(g => g.questions || []);
        } else if (Array.isArray(json.data)) {
            allQuestions = json.data;
        } else {
            allQuestions = [];
        }
        applyFilters();
    } catch (e) {
        document.getElementById('skeletonLoader').style.display = 'none';
        document.getElementById('qList').classList.add('hidden');
        document.getElementById('emptyIcon').setAttribute('name', 'alert-circle-outline');
        document.getElementById('emptyTitle').textContent = 'Failed to load';
        document.getElementById('emptyDesc').textContent = 'Try refreshing the page';
        document.getElementById('emptyState').classList.remove('hidden');
    }
}

loadQuestions();

// ── Modal wait ──
function showWaitModal(e) {
    if (e) e.stopPropagation();
    const modal = document.getElementById('waitModal');
    modal.style.display = 'flex';
}
function closeWaitModal() {
    document.getElementById('waitModal').style.display = 'none';
}
// Tutup modal kalau klik backdrop
document.addEventListener('click', function(e) {
    const modal = document.getElementById('waitModal');
    if (e.target === modal) closeWaitModal();
});

// ── Claim ──
async function claimQuestion(id) {
    try {
        const res = await fetch(`${API_BASE}/nexus/${id}/claim`, {
            method: 'POST',
            headers: { 'Authorization': 'Bearer {{ session("token") }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (!res.ok) { showAppAlert(json.message || 'Failed to claim'); return; }
        loadQuestions();
    } catch (e) {
        showAppAlert('Failed to claim the question');
    }
}
</script>
@endpush