@extends('layouts.app')

@section('title', 'Nexus – Notifikasi')

@push('styles')
<style>
    @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
    .skeleton { background:linear-gradient(90deg,#f0dcea 25%,#fce8f5 50%,#f0dcea 75%); background-size:400px 100%; animation:shimmer 1.4s infinite; border-radius:12px; }

    .notif-card { transition: transform .15s ease, opacity .15s ease; cursor:pointer; }
    .notif-card:hover  { opacity: .92; }
    .notif-card:active { transform: scale(0.98); opacity: .7; }

    @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }

    .chip { display:inline-flex; align-items:center; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:700; border:2px solid #EDE9FE; background:#fff; color:#8B86A5; cursor:pointer; white-space:nowrap; }
    .chip.active { background:#8B46D3; border-color:#8B46D3; color:#fff; }

    .unread-dot { width:10px; height:10px; border-radius:5px; background:#8B46D3; flex-shrink:0; }
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
            <ion-icon name="notifications" style="font-size:30px; color:#8B46D3;"></ion-icon>
        </div>
        <h1 class="font-extrabold text-white mb-1" style="font-size:22px; letter-spacing:.4px;">Notifikasi</h1>
        <p style="font-size:13px; color:#E5DEFF; font-weight:500;">Diary & aktivitas terbaru dari nanny</p>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    <div class="flex items-center justify-between anim delay-2">
        <div class="flex items-center gap-2">
            <span style="font-size:17px; font-weight:700; color:#1E1B2E;">Aktivitas</span>
            <span id="notifCount" style="background:#EDE9FE; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:700; color:#8B46D3;">–</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="markAllRead()" class="flex items-center gap-1 bg-[#EDE9FE] py-2 px-3 rounded-xl border-none cursor-pointer">
                <ion-icon name="checkmark-done-outline" style="font-size:14px; color:#8B46D3;"></ion-icon>
                <span style="font-size:12px; font-weight:600; color:#8B46D3;">Baca semua</span>
            </button>
            <button onclick="fetchNotif()" class="flex items-center gap-1 bg-[#EDE9FE] py-2 px-3 rounded-xl border-none cursor-pointer">
                <ion-icon name="refresh-outline" style="font-size:14px; color:#8B46D3;"></ion-icon>
                <span style="font-size:12px; font-weight:600; color:#8B46D3;">Refresh</span>
            </button>
        </div>
    </div>

    <!-- Filter chips -->
    <div class="flex gap-2 overflow-x-auto hide-scrollbar anim delay-2" id="filterChips" style="padding-bottom:2px;">
        <button class="chip active" data-type="" onclick="setFilter('', this)">Semua</button>
        <button class="chip" data-type="diary_created" onclick="setFilter('diary_created', this)">Diary Baru</button>
        <button class="chip" data-type="diary_updated" onclick="setFilter('diary_updated', this)">Diary Diubah</button>
        <button class="chip" data-type="attendance" onclick="setFilter('attendance', this)">Kehadiran</button>
        <button class="chip" data-type="task_progress" onclick="setFilter('task_progress', this)">Tugas</button>
        <button class="chip" data-type="assistant_note" onclick="setFilter('assistant_note', this)">Catatan</button>
        <button class="chip" data-type="appointment" onclick="setFilter('appointment', this)">Janji</button>
    </div>

    <!-- Skeleton -->
    <div id="skeletonList">
        @for($i = 0; $i < 5; $i++)
        <div class="flex items-center bg-white mb-3" style="border-radius:16px; padding:16px; border:2px solid #EDE9FE; gap:12px;">
            <div class="skeleton flex-shrink-0" style="width:44px; height:44px; border-radius:12px;"></div>
            <div class="flex-1 flex flex-col gap-2">
                <div class="skeleton" style="height:14px; width:70%;"></div>
                <div class="skeleton" style="height:12px; width:50%;"></div>
            </div>
            <div class="skeleton flex-shrink-0" style="width:10px; height:10px; border-radius:5px;"></div>
        </div>
        @endfor
    </div>

    <!-- List -->
    <div id="notifList" class="hidden flex flex-col gap-3"></div>

    <!-- Pagination -->
    <div id="pagination" class="hidden items-center justify-center gap-2 py-2">
        <button id="btnPrev" onclick="goPage(-1)" class="px-4 py-2 rounded-xl bg-white border border-[#DDD6EF] text-[#8B86A5] text-xs font-bold">‹ Prev</button>
        <span id="pageInfo" class="text-xs font-bold text-[#8B86A5]"></span>
        <button id="btnNext" onclick="goPage(1)" class="px-4 py-2 rounded-xl bg-white border border-[#DDD6EF] text-[#8B86A5] text-xs font-bold">Next ›</button>
    </div>

    <!-- Empty -->
    <div id="emptyState" class="hidden flex-col items-center justify-center" style="padding:60px 20px;">
        <div class="float-anim flex items-center justify-center" style="width:110px; height:110px; border-radius:55px; background:#EDE9FE; margin-bottom:20px;">
            <ion-icon name="notifications-off-outline" style="font-size:54px; color:#C4B5FD;"></ion-icon>
        </div>
        <p style="font-size:17px; font-weight:700; color:#1E1B2E; margin-bottom:6px;">Belum ada notifikasi</p>
        <p style="font-size:13px; color:#8B86A5; text-align:center;">Aktivitas nanny akan muncul di sini</p>
    </div>

    <!-- Error -->
    <div id="errorState" class="hidden flex flex-col items-center" style="padding:40px 20px; gap:12px;">
        <ion-icon name="cloud-offline-outline" style="font-size:48px; color:#C4B5FD;"></ion-icon>
        <p style="font-size:15px; font-weight:700; color:#1E1B2E;">Gagal memuat notifikasi</p>
        <button onclick="fetchNotif()" style="background:#8B46D3; color:#fff; padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; border:none; cursor:pointer;">Coba Lagi</button>
    </div>

</div>
@endsection

@push('scripts')
<script>
const API_BASE_URL = '{{ env("API_BASE_URL") }}';
const API_TOKEN    = '{{ session("token") }}';

const TYPE_META = {
    diary_created:  { label: 'Diary Baru',   icon: 'book-outline',           color: '#8B46D3', bg: '#EDE9FE' },
    diary_updated:  { label: 'Diary Diubah', icon: 'create-outline',         color: '#7B2FBE', bg: '#EDE9FE' },
    attendance:     { label: 'Kehadiran',    icon: 'time-outline',           color: '#F59E0B', bg: '#FEF3C7' },
    task_progress:  { label: 'Tugas',        icon: 'checkmark-done-outline', color: '#2563EB', bg: '#EFF6FF' },
    assistant_note: { label: 'Catatan',      icon: 'reader-outline',         color: '#16A34A', bg: '#F0FDF4' },
    appointment:    { label: 'Janji',        icon: 'calendar-outline',       color: '#DC2626', bg: '#FEF2F2' },
};

let activeType = '';
let currentPage = 1;
let lastPage = 1;

const authHeaders = () => ({
    'Authorization': `Bearer ${API_TOKEN}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json',
});

function metaFor(type) {
    return TYPE_META[type] || { label: type, icon: 'ellipse-outline', color: '#8B86A5', bg: '#F3F4F6' };
}

function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function timeAgo(iso) {
    const diff = Math.floor((Date.now() - new Date(iso).getTime()) / 1000);
    if (diff < 60) return 'baru saja';
    if (diff < 3600) return Math.floor(diff / 60) + ' mnt lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
    if (diff < 86400 * 7) return Math.floor(diff / 86400) + ' hari lalu';
    return new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function showSkeleton() {
    document.getElementById('skeletonList').classList.remove('hidden');
    document.getElementById('notifList').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('pagination').classList.add('hidden');
}

function showError() {
    document.getElementById('skeletonList').classList.add('hidden');
    document.getElementById('notifList').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('errorState').classList.add('flex');
    document.getElementById('pagination').classList.add('hidden');
}

async function fetchNotif() {
    showSkeleton();
    try {
        const params = new URLSearchParams({ page: currentPage, per_page: 20 });
        if (activeType) params.append('type', activeType);
        const res = await fetch(`${API_BASE_URL}/notifications?${params}`, { headers: authHeaders() });
        const json = await res.json();
        if (json.status === 'success' || json.data) {
            renderNotif(json.data.notifications || [], json.data);
        } else {
            showError();
        }
    } catch (e) {
        showError();
    }
}

function renderNotif(list, meta) {
    document.getElementById('skeletonList').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('flex');

    document.getElementById('notifCount').textContent = meta?.total ?? list.length;

    if (list.length === 0) {
        document.getElementById('notifList').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');
        const empty = document.getElementById('emptyState');
        empty.classList.remove('hidden');
        empty.classList.add('flex');
        return;
    }

    const container = document.getElementById('notifList');
    container.classList.remove('hidden');
    container.innerHTML = list.map((n, i) => {
        const m = metaFor(n.type);
        const url = (n.data && n.data.url) ? n.data.url : '';
        return `<div class="notif-card flex items-start bg-white"
                    style="border-radius:16px; padding:14px 16px; gap:12px; border:2px solid ${n.is_read ? '#EAE6F5' : '#C4B5FD'}; animation:slideUp .3s ease ${i * 0.04}s both; opacity:0;"
                    onclick="openNotif(${n.id}, '${url}')">
            <div class="flex items-center justify-center flex-shrink-0" style="width:44px; height:44px; border-radius:12px; background:${m.bg};">
                <ion-icon name="${m.icon}" style="font-size:20px; color:${m.color};"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center" style="gap:6px; margin-bottom:4px; flex-wrap:wrap;">
                    <span style="background:${m.bg}; color:${m.color}; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:800;">${esc(m.label)}</span>
                    <span style="font-size:11px; color:#8B86A5; font-weight:600;">${timeAgo(n.created_at)}</span>
                </div>
                <p class="line-clamp-1" style="font-size:14px; font-weight:${n.is_read ? '600' : '800'}; color:#1E1B2E;">${esc(n.title)}</p>
                <p class="line-clamp-2" style="font-size:12px; color:#8B86A5; font-weight:500; margin-top:2px;">${esc(n.body)}</p>
            </div>
            ${n.is_read ? '' : '<div class="unread-dot" style="margin-top:6px;"></div>'}
        </div>`;
    }).join('');

    // Pagination
    currentPage = meta?.current_page || 1;
    lastPage = meta?.last_page || 1;
    const pag = document.getElementById('pagination');
    if (lastPage > 1) {
        pag.classList.remove('hidden');
        pag.classList.add('flex');
        document.getElementById('pageInfo').textContent = `${currentPage}/${lastPage}`;
        document.getElementById('btnPrev').style.opacity = currentPage <= 1 ? '.4' : '1';
        document.getElementById('btnNext').style.opacity = currentPage >= lastPage ? '.4' : '1';
    } else {
        pag.classList.add('hidden');
        pag.classList.remove('flex');
    }
}

function setFilter(type, el) {
    activeType = type;
    currentPage = 1;
    document.querySelectorAll('#filterChips .chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    fetchNotif();
}

function goPage(delta) {
    const next = currentPage + delta;
    if (next < 1 || next > lastPage) return;
    currentPage = next;
    fetchNotif();
}

async function openNotif(id, url) {
    try {
        await fetch(`${API_BASE_URL}/notifications/${id}/read`, { method: 'POST', headers: authHeaders() });
    } catch (e) { /* tetap lanjut walau mark-read gagal */ }
    if (url) window.location.href = url;
    else fetchNotif();
}

async function markAllRead() {
    try {
        await fetch(`${API_BASE_URL}/notifications/read-all`, { method: 'POST', headers: authHeaders() });
        showAppAlert('Semua notifikasi ditandai dibaca', 'ok');
        fetchNotif();
    } catch (e) {
        showAppAlert('Gagal menandai notifikasi', 'error');
    }
}

fetchNotif();
</script>
@endpush
