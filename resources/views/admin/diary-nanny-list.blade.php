@extends('layouts.app')

@section('title', 'Admin – Diary Nanny')

@push('styles')
<style>
    @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
    .skeleton { background:linear-gradient(90deg,#f0dcea 25%,#fce8f5 50%,#f0dcea 75%); background-size:400px 100%; animation:shimmer 1.4s infinite; border-radius:12px; }

    .nanny-card { transition: transform .15s ease, opacity .15s ease; cursor:pointer; }
    .nanny-card:hover  { opacity: .88; }
    .nanny-card:active { transform: scale(0.97); opacity: .7; }

    .search-box { transition: box-shadow .2s ease; }
    .search-box:focus-within { box-shadow: 0 0 0 3px rgba(139,70,211,.2); }

    @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    .badge { display:inline-flex; align-items:center; justify-content:center; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700; }
    .badge-active   { background:#dcfce7; color:#16a34a; }
    .badge-inactive { background:#fee2e2; color:#dc2626; }
    .badge-pending  { background:#fef9c3; color:#ca8a04; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex flex-col items-center relative z-10">
        <a href="{{ route('dashboard') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:0; left:0; width:40px; height:40px;">
            <ion-icon name="arrow-back" style="font-size:20px; color:#fff;"></ion-icon>
        </a>
        <div class="flex items-center justify-center bg-white rounded-full mb-3 shadow-lg" style="width:64px; height:64px;">
            <ion-icon name="book" style="font-size:30px; color:#8B46D3;"></ion-icon>
        </div>
        <h1 class="font-extrabold text-white mb-1" style="font-size:22px; letter-spacing:.4px;">Diary Nanny</h1>
        <p style="font-size:13px; color:#E5DEFF; font-weight:500;">Select a nanny to view the diary</p>

        <!-- Search -->
        <div class="search-box flex items-center bg-white rounded-2xl mt-5 px-4" style="height:46px; gap:10px; width:100%;">
            <ion-icon name="search-outline" style="font-size:18px; color:#8B86A5; flex-shrink:0;"></ion-icon>
            <input id="searchInput" type="text" placeholder="Search nanny..."
                   class="flex-1 bg-transparent outline-none text-sm font-bold text-[#1E1B2E] placeholder-[#8B86A5]"
                   oninput="filterNanny(this.value)">
            <button id="btnClearSearch" onclick="clearSearch()" class="hidden flex-shrink-0">
                <ion-icon name="close-circle" style="font-size:18px; color:#8B86A5;"></ion-icon>
            </button>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    <div class="flex items-center justify-between anim delay-2">
        <div class="flex items-center gap-2">
            <span style="font-size:17px; font-weight:700; color:#1E1B2E;">Nanny List</span>
            <span id="nannyCount" style="background:#EDE9FE; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:700; color:#8B46D3;">–</span>
        </div>
        <button onclick="fetchNanny()" class="flex items-center gap-1 bg-[#EDE9FE] py-2 px-3 rounded-xl border-none cursor-pointer">
            <ion-icon name="refresh-outline" style="font-size:14px; color:#8B46D3;"></ion-icon>
            <span style="font-size:12px; font-weight:600; color:#8B46D3;">Refresh</span>
        </button>
    </div>

    <!-- Skeleton -->
    <div id="skeletonList">
        @for($i = 0; $i < 5; $i++)
        <div class="flex items-center bg-white mb-3" style="border-radius:16px; padding:16px; border:2px solid #EDE9FE; gap:12px;">
            <div class="skeleton flex-shrink-0" style="width:56px; height:56px; border-radius:28px;"></div>
            <div class="flex-1 flex flex-col gap-2">
                <div class="skeleton" style="height:14px; width:70%;"></div>
                <div class="skeleton" style="height:12px; width:50%;"></div>
            </div>
            <div class="skeleton flex-shrink-0" style="width:32px; height:32px; border-radius:16px;"></div>
        </div>
        @endfor
    </div>

    <!-- List -->
    <div id="nannyList" class="hidden"></div>

    <!-- Empty -->
    <div id="emptyState" class="hidden flex-col items-center justify-center" style="padding:60px 20px;">
        <div class="float-anim flex items-center justify-center" style="width:110px; height:110px; border-radius:55px; background:#EDE9FE; margin-bottom:20px;">
            <ion-icon name="people-outline" style="font-size:54px; color:#C4B5FD;"></ion-icon>
        </div>
        <p style="font-size:17px; font-weight:700; color:#1E1B2E; margin-bottom:6px;" id="emptyTitle">No nannies</p>
        <p style="font-size:13px; color:#8B86A5; text-align:center;" id="emptyDesc">No nanny data available</p>
    </div>

    <!-- Error -->
    <div id="errorState" class="hidden flex flex-col items-center" style="padding:40px 20px; gap:12px;">
        <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#C4B5FD;"></ion-icon>
        <p style="font-size:15px; font-weight:700; color:#1E1B2E;">Failed to load data</p>
        <button onclick="fetchNanny()" style="background:#8B46D3; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Try Again</button>
    </div>

</div>
@endsection

@push('scripts')
<script>
const API_BASE_URL = '{{ env("API_BASE_URL") }}';
const API_TOKEN    = '{{ session("token") }}';
let allNanny = [];

async function fetchNanny() {
    showSkeleton();
    try {
        const res = await fetch(`${API_BASE_URL}/user-nanny`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        });
        const data = await res.json();
        if (data.status === 'success' || data.data) {
            allNanny = data.data || [];
            renderNanny(allNanny);
        } else {
            showError();
        }
    } catch(e) {
        showError();
    }
}

function showSkeleton() {
    document.getElementById('skeletonList').classList.remove('hidden');
    document.getElementById('nannyList').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
}

function showError() {
    document.getElementById('skeletonList').classList.add('hidden');
    document.getElementById('nannyList').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
}

function renderNanny(list) {
    document.getElementById('skeletonList').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('nannyCount').textContent = list.length;

    if (list.length === 0) {
        document.getElementById('nannyList').classList.add('hidden');
        const empty = document.getElementById('emptyState');
        empty.classList.remove('hidden');
        empty.classList.add('flex');
        return;
    }

    document.getElementById('emptyState').classList.add('hidden');
    const container = document.getElementById('nannyList');
    container.classList.remove('hidden');

    container.innerHTML = list.map((nanny, i) => {
        const foto = nanny.foto || nanny.photo || nanny.avatar || '';
        const nama = nanny.name || nanny.nama || 'Nanny';
        const id   = nanny.id || nanny.id_user;
        const email = nanny.email || '';
        const phone = nanny.no_hp || nanny.phone || '';
        const statusRaw = (nanny.status || '').toLowerCase();

        let badgeClass = 'badge-pending', badgeLabel = 'Pending';
        if (statusRaw === 'active' || statusRaw === 'aktif') { badgeClass='badge-active'; badgeLabel='Active'; }
        else if (statusRaw === 'inactive' || statusRaw === 'nonaktif') { badgeClass='badge-inactive'; badgeLabel='Inactive'; }

        const avatarHtml = foto
            ? `<img src="${foto}" alt="${nama}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                    style="width:56px;height:56px;border-radius:28px;border:3px solid #EDE9FE;object-fit:cover;">
               <div class="items-center justify-center hidden" style="width:56px;height:56px;border-radius:28px;background:#EDE9FE;border:3px solid #EDE9FE;flex-shrink:0;">
                   <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
               </div>`
            : `<div class="flex items-center justify-center" style="width:56px;height:56px;border-radius:28px;background:#EDE9FE;border:3px solid #EDE9FE;flex-shrink:0;">
                   <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
               </div>`;

        const infoExtra = phone
            ? `<div class="flex items-center" style="gap:4px;margin-top:4px;">
                   <ion-icon name="call-outline" style="font-size:12px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                   <span style="font-size:12px;color:#8B86A5;font-weight:500;">${phone}</span>
               </div>`
            : email
            ? `<div class="flex items-center" style="gap:4px;margin-top:4px;">
                   <ion-icon name="mail-outline" style="font-size:12px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                   <span style="font-size:12px;color:#8B86A5;font-weight:500;">${email}</span>
               </div>`
            : '';

        return `<a href="{{ url('admin/diary') }}/${id}/anak"
                   class="nanny-card flex items-center bg-white"
                   style="border-radius:16px;margin-bottom:12px;border:2px solid #EDE9FE;padding:16px;gap:12px;
                          text-decoration:none;animation:slideUp .3s ease ${i*0.05}s both;opacity:0;">
            <div style="flex-shrink:0;">${avatarHtml}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center" style="gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <p class="line-clamp-1" style="font-size:15px;font-weight:700;color:#1E1B2E;">${nama}</p>
                    <span class="badge ${badgeClass}">${badgeLabel}</span>
                </div>
                ${infoExtra}
            </div>
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:32px;height:32px;border-radius:16px;background:#EDE9FE;">
                <ion-icon name="chevron-forward" style="font-size:20px;color:#C4B5FD;"></ion-icon>
            </div>
        </a>`;
    }).join('');
}

function filterNanny(q) {
    const btn = document.getElementById('btnClearSearch');
    btn.classList.toggle('hidden', q.length === 0);
    const filtered = allNanny.filter(n => {
        const nama = (n.name || n.nama || '').toLowerCase();
        const email = (n.email || '').toLowerCase();
        return nama.includes(q.toLowerCase()) || email.includes(q.toLowerCase());
    });

    const empty = document.getElementById('emptyState');
    if (filtered.length === 0 && q.length > 0) {
        document.getElementById('nannyList').classList.add('hidden');
        empty.classList.remove('hidden');
        empty.classList.add('flex');
        document.getElementById('emptyTitle').textContent = 'Nanny not found';
        document.getElementById('emptyDesc').textContent  = `No results for "${q}"`;
    } else {
        renderNanny(filtered);
        document.getElementById('emptyTitle').textContent = 'Tidak ada nanny';
        document.getElementById('emptyDesc').textContent  = 'Belum ada data nanny tersedia';
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('btnClearSearch').classList.add('hidden');
    renderNanny(allNanny);
}

fetchNanny();
</script>
@endpush
