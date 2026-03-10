{{-- resources/views/admin/diary-nanny-list.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin – Diary Nanny</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        plum: { DEFAULT:'#7B1E5A', light:'#9B2E72', dark:'#4A0E35', pale:'#FFF9FB', soft:'#F3E6FA', muted:'#A2397B', accent:'#B895C8' }
                    },
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

        .header-bg { background: linear-gradient(135deg, #7B1E5A 0%, #9B2E72 100%); }

        @keyframes slideUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
        .anim-up    { animation: slideUp 0.35s ease forwards; }
        .anim-up.d1 { animation-delay:0.05s; opacity:0; }
        .anim-up.d2 { animation-delay:0.10s; opacity:0; }
        .anim-up.d3 { animation-delay:0.15s; opacity:0; }

        @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
        .skeleton { background: linear-gradient(90deg,#f0dcea 25%,#fce8f5 50%,#f0dcea 75%); background-size:400px 100%; animation:shimmer 1.4s infinite; border-radius:12px; }

        .nanny-card { transition: transform .15s ease, opacity .15s ease; cursor:pointer; }
        .nanny-card:hover  { opacity: .88; }
        .nanny-card:active { transform: scale(0.97); opacity: .7; }

        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }

        .search-box { transition: box-shadow .2s ease; }
        .search-box:focus-within { box-shadow: 0 0 0 3px rgba(123,30,90,.2); }

        @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

        .badge { display:inline-flex; align-items:center; justify-content:center; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-active   { background:#dcfce7; color:#16a34a; }
        .badge-inactive { background:#fee2e2; color:#dc2626; }
        .badge-pending  { background:#fef9c3; color:#ca8a04; }
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
         style="padding:50px 20px 28px; border-bottom-left-radius:24px; border-bottom-right-radius:24px;">
        <div class="absolute top-0 right-0 w-36 h-36 rounded-full bg-white/5 -translate-y-8 translate-x-8 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 translate-y-5 -translate-x-5 pointer-events-none"></div>

        <a href="{{ route('dashboard') }}"
           class="absolute flex items-center justify-center bg-white/20 hover:bg-white/30 transition-colors rounded-full"
           style="top:54px; left:20px; width:40px; height:40px; z-index:10;">
            <ion-icon name="arrow-back" style="font-size:20px; color:#fff;"></ion-icon>
        </a>

        <div class="flex flex-col items-center anim-up d1">
            <div class="flex items-center justify-center bg-white rounded-full mb-3 shadow-lg" style="width:64px; height:64px;">
                <ion-icon name="book" style="font-size:30px; color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="font-extrabold text-white mb-1" style="font-size:22px; letter-spacing:.4px;">Diary Nanny</h1>
            <p style="font-size:13px; color:#F3E6FA; font-weight:500;">Pilih nanny untuk melihat diary</p>
        </div>

        <!-- Search -->
        <div class="search-box flex items-center bg-white rounded-2xl mt-5 px-4" style="height:46px; gap:10px;">
            <ion-icon name="search-outline" style="font-size:18px; color:#A2397B; flex-shrink:0;"></ion-icon>
            <input id="searchInput" type="text" placeholder="Cari nanny..."
                   class="flex-1 bg-transparent outline-none text-sm font-medium text-plum-dark placeholder-plum-accent"
                   style="color:#4A0E35;"
                   oninput="filterNanny(this.value)">
            <button id="btnClearSearch" onclick="clearSearch()" class="hidden flex-shrink-0">
                <ion-icon name="close-circle" style="font-size:18px; color:#A2397B;"></ion-icon>
            </button>
        </div>
    </div>

    <!-- LIST HEADER -->
    <div class="flex items-center justify-between flex-shrink-0 anim-up d2 p-4">
        <div>
            <span style="font-size:17px; font-weight:700; color:#4A0E35;">Daftar Nanny</span>
            <span id="nannyCount" class="ml-2" style="background:#F3E6FA; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:700; color:#7B1E5A;">–</span>
        </div>
        <button onclick="fetchNanny()" class="flex items-center gap-1" style="background:#F3E6FA; padding:6px 12px; border-radius:12px; border:none; cursor:pointer;">
            <ion-icon name="refresh-outline" style="font-size:14px; color:#7B1E5A;"></ion-icon>
            <span style="font-size:12px; font-weight:600; color:#7B1E5A;">Refresh</span>
        </button>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 overflow-y-auto no-scrollbar anim-up d3 p-4 pb-16">

        <!-- Skeleton -->
        <div id="skeletonList">
            @for($i = 0; $i < 5; $i++)
            <div class="flex items-center bg-white mb-3" style="border-radius:16px; padding:16px; border:2px solid #F3E6FA; gap:12px;">
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
            <div class="float-anim flex items-center justify-center" style="width:110px; height:110px; border-radius:55px; background:#F3E6FA; margin-bottom:20px;">
                <ion-icon name="people-outline" style="font-size:54px; color:#B895C8;"></ion-icon>
            </div>
            <p style="font-size:17px; font-weight:700; color:#4A0E35; margin-bottom:6px;" id="emptyTitle">Tidak ada nanny</p>
            <p style="font-size:13px; color:#A2397B; text-align:center;" id="emptyDesc">Belum ada data nanny tersedia</p>
        </div>

        <!-- Error -->
        <div id="errorState" class="hidden flex flex-col items-center" style="padding:40px 20px; gap:12px;">
            <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#B895C8;"></ion-icon>
            <p style="font-size:15px; font-weight:700; color:#4A0E35;">Gagal memuat data</p>
            <button onclick="fetchNanny()" style="background:#7B1E5A; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Coba Lagi</button>
        </div>

        <div style="height:16px;"></div>
    </div>

    @include('partials.bottom-nav', ['active' => 'diary'])
</div>
</div>

<script>
const API_BASE_URL = '{{ env("API_BASE_URL") }}';
const API_TOKEN    = '{{ session("token") }}';

let allNanny = [];

// Clock
function updateClock() {
    const now = new Date(), el = document.getElementById('statusTime');
    if (el) el.textContent = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
}
updateClock(); setInterval(updateClock, 30000);

// Fetch
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

        let badgeClass = 'badge-pending', badgeLabel = 'Menunggu';
        if (statusRaw === 'active' || statusRaw === 'aktif') { badgeClass='badge-active'; badgeLabel='Aktif'; }
        else if (statusRaw === 'inactive' || statusRaw === 'nonaktif') { badgeClass='badge-inactive'; badgeLabel='Nonaktif'; }

        const avatarHtml = foto
            ? `<img src="${foto}" alt="${nama}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                    style="width:56px;height:56px;border-radius:28px;border:3px solid #F3E6FA;object-fit:cover;">
               <div class="items-center justify-center hidden" style="width:56px;height:56px;border-radius:28px;background:#F3E6FA;border:3px solid #F3E6FA;flex-shrink:0;">
                   <ion-icon name="person" style="font-size:24px;color:#7B1E5A;"></ion-icon>
               </div>`
            : `<div class="flex items-center justify-center" style="width:56px;height:56px;border-radius:28px;background:#F3E6FA;border:3px solid #F3E6FA;flex-shrink:0;">
                   <ion-icon name="person" style="font-size:24px;color:#7B1E5A;"></ion-icon>
               </div>`;

        const infoExtra = phone
            ? `<div class="flex items-center" style="gap:4px;margin-top:4px;">
                   <ion-icon name="call-outline" style="font-size:12px;color:#A2397B;flex-shrink:0;"></ion-icon>
                   <span style="font-size:12px;color:#A2397B;font-weight:500;">${phone}</span>
               </div>`
            : email
            ? `<div class="flex items-center" style="gap:4px;margin-top:4px;">
                   <ion-icon name="mail-outline" style="font-size:12px;color:#A2397B;flex-shrink:0;"></ion-icon>
                   <span style="font-size:12px;color:#A2397B;font-weight:500;">${email}</span>
               </div>`
            : '';

        return `<a href="{{ url('admin/diary') }}/${id}/anak"
                   class="nanny-card flex items-center bg-white"
                   style="border-radius:16px;margin-bottom:12px;border:2px solid #F3E6FA;padding:16px;gap:12px;
                          text-decoration:none;animation:slideUp .3s ease ${i*0.05}s both;opacity:0;">
            <div style="flex-shrink:0;">${avatarHtml}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center" style="gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <p class="line-clamp-1" style="font-size:15px;font-weight:700;color:#4A0E35;">${nama}</p>
                    <span class="badge ${badgeClass}">${badgeLabel}</span>
                </div>
                ${infoExtra}
            </div>
            <div class="flex items-center justify-center flex-shrink-0"
                 style="width:32px;height:32px;border-radius:16px;background:#F3E6FA;">
                <ion-icon name="chevron-forward" style="font-size:20px;color:#B895C8;"></ion-icon>
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
        document.getElementById('emptyTitle').textContent = 'Nanny tidak ditemukan';
        document.getElementById('emptyDesc').textContent  = `Tidak ada hasil untuk "${q}"`;
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

// Init
fetchNanny();
</script>

@include('partials.auth-guard')
</body>
</html>