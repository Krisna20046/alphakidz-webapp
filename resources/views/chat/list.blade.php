<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pesan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { plum:{DEFAULT:'#7B1E5A',light:'#9B2E72',dark:'#4A0E35',pale:'#FFF9FB',soft:'#F3E6FA',muted:'#A2397B'} },
                fontFamily: { sans:['Plus Jakarta Sans','sans-serif'] }
            }}
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color:transparent; box-sizing:border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#FFF9FB; margin:0; }

        @media (min-width:640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3,#ede0f0,#e8d5ee); }
            .phone-frame   { width:390px; min-height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,0.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; }
        }

        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }

        @keyframes slideUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim-up { animation:slideUp .35s ease both; }

        @keyframes fadeIn { from{opacity:0} to{opacity:1} }
        .fade-in { animation:fadeIn .3s ease both; }

        @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

        /* Role group accordion */
        .group-content { overflow:hidden; transition:max-height .3s ease; }
        .group-chevron  { transition:transform .3s ease; }
        .group-chevron.open { transform:rotate(90deg); }

        /* Chat item hover */
        .chat-item { transition:background .15s ease, transform .15s ease; }
        .chat-item:active { transform:scale(0.98); background:#FFF9FB; }

        /* Unread badge pulse */
        @keyframes badgePop { 0%,100%{transform:scale(1)} 50%{transform:scale(1.2)} }
        .badge-pop { animation:badgePop 1.8s ease-in-out infinite; }

        /* Role color map via CSS vars */
        .role-konsultan { --rc:#2D7DD2; --rb:#EBF4FF; }
        .role-nanny     { --rc:#E84855; --rb:#FFF0F1; }
        .role-majikan   { --rc:#3BB273; --rb:#EDFAF3; }
        .role-default   { --rc:#7B1E5A; --rb:#F3E6FA; }

        .status-bar-icons svg { display:inline-block; }
        .pb-safe { padding-bottom:env(safe-area-inset-bottom,0px); }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale flex flex-col" style="min-height:100vh;">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum shrink-0">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center text-white">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8"><rect x="0" y="3" width="3" height="9" rx="0.5"/><rect x="4.5" y="2" width="3" height="10" rx="0.5"/><rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/><rect x="13.5" y="0" width="3" height="12" rx="0.5" opacity="0.3"/></svg>
            <div class="flex items-center"><div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch"><div class="bg-white flex-1"></div></div></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="bg-gradient-to-br from-plum to-plum-light shrink-0 px-5 pt-10 pb-14 relative" style="border-radius:0 0 28px 28px;">
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-10 translate-x-10 pointer-events-none"></div>
        <a href="{{ route('dashboard') }}"
           class="absolute top-10 left-5 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
            <ion-icon name="arrow-back" style="font-size:20px;color:white;"></ion-icon>
        </a>
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-3 shadow-lg">
                <ion-icon name="chatbubbles" style="font-size:30px;color:#7B1E5A;"></ion-icon>
            </div>
            <h1 class="text-white text-2xl font-extrabold">Pesan</h1>
            <p id="headerSubtitle" class="text-white/60 text-sm mt-1">Memuat percakapan...</p>
        </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="px-4 -mt-5 z-10 shrink-0">
        <div class="bg-white rounded-2xl shadow-lg shadow-plum/10 border-2 border-plum-soft/60 flex items-center gap-3 px-4 py-3.5">
            <ion-icon name="search-outline" style="font-size:18px;color:#A2397B;flex-shrink:0;"></ion-icon>
            <input type="text" id="searchInput" placeholder="Cari percakapan..."
                   class="flex-1 text-sm font-medium text-plum-dark placeholder-plum-muted/50 outline-none bg-transparent"
                   oninput="filterChats(this.value)"/>
            <button id="clearSearch" onclick="clearSearch()" class="hidden">
                <ion-icon name="close-circle" style="font-size:18px;color:#B895C8;"></ion-icon>
            </button>
        </div>
    </div>

    <!-- CHAT LIST -->
    <div id="chatListArea" class="flex-1 overflow-y-auto no-scrollbar px-4 pt-4 pb-24">
        <!-- Skeleton loader -->
        <div id="skeletonLoader" class="space-y-3">
            @for($i=0;$i<4;$i++)
            <div class="bg-white rounded-2xl p-4 flex items-center gap-3 animate-pulse">
                <div class="w-12 h-12 rounded-full bg-plum-soft shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3.5 bg-plum-soft rounded-full w-3/4"></div>
                    <div class="h-3 bg-plum-soft/60 rounded-full w-1/2"></div>
                </div>
                <div class="h-3 w-10 bg-plum-soft/40 rounded-full"></div>
            </div>
            @endfor
        </div>

        <!-- Actual chat groups (rendered by JS) -->
        <div id="chatGroups" class="space-y-3 hidden"></div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden flex flex-col items-center pt-16 pb-8 px-8">
            <div class="float-anim w-28 h-28 rounded-full bg-plum-soft flex items-center justify-center mb-6">
                <ion-icon id="emptyIcon" name="chatbubble-ellipses-outline" style="font-size:52px;color:#E0BBE4;"></ion-icon>
            </div>
            <h3 id="emptyTitle" class="text-plum-dark font-bold text-lg mb-2 text-center">Belum ada percakapan</h3>
            <p id="emptyDesc" class="text-plum-muted text-sm text-center leading-relaxed">
                Mulai percakapan baru dengan mengirim pesan pertama
            </p>
        </div>
    </div>

    <!-- BOTTOM NAV -->
    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<script>
// ── Config ────────────────────────────────────────────────────────────────────
const USER_ID        = {{ session('user')['id_user'] ?? 'null' }};
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const CSRF           = "{{ csrf_token() }}";

// ── Helpers ───────────────────────────────────────────────────────────────────
function updateClock(){const el=document.getElementById('statusTime');if(el){const n=new Date();el.textContent=`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;}}
updateClock();setInterval(updateClock,30000);

const roleConfig = {
    konsultan : { color:'#2D7DD2', bg:'#EBF4FF', icon:'person-circle-outline' },
    nanny     : { color:'#E84855', bg:'#FFF0F1', icon:'heart-outline' },
    pengasuh  : { color:'#E84855', bg:'#FFF0F1', icon:'heart-outline' },
    majikan   : { color:'#3BB273', bg:'#EDFAF3', icon:'briefcase-outline' },
};

function getRoleConfig(role) {
    const r = (role||'').toLowerCase();
    for (const [key, cfg] of Object.entries(roleConfig)) {
        if (r.includes(key)) return cfg;
    }
    return { color:'#7B1E5A', bg:'#F3E6FA', icon:'people-outline' };
}

function formatTime(ts) {
    const d    = new Date(ts);
    const now  = new Date();
    const diff = (now - d) / 3600000;
    if (diff < 24)   return d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
    if (diff < 168)  return d.toLocaleDateString('id-ID',{weekday:'short'});
    return d.toLocaleDateString('id-ID',{day:'numeric',month:'short'});
}

function initials(name) {
    return (name||'?').charAt(0).toUpperCase();
}

// ── Chat Data ─────────────────────────────────────────────────────────────────
let allChats = [];

function processChatData(raw) {
    return raw.map(c => ({
        id          : c.id,
        otherUserId : c.id_penerima,
        name        : c.nama_penerima || 'Pengguna',
        role        : c.role_penerima || 'Lainnya',
        avatar      : c.foto || null,
        lastMessage : c.pesan_terakhir || '',
        timestamp   : c.created_at,
        unread      : c.unread_count || 0,
    }));
}

function groupByRole(chats) {
    const groups = {};
    chats.forEach(c => {
        const r = c.role || 'Lainnya';
        if (!groups[r]) groups[r] = [];
        groups[r].push(c);
    });
    return Object.entries(groups).sort(([a],[b]) => {
        if (a==='Lainnya') return 1;
        if (b==='Lainnya') return -1;
        return a.localeCompare(b);
    });
}

// ── Render ────────────────────────────────────────────────────────────────────
function buildChatItemHTML(chat, roleColor) {
    const av = chat.avatar
        ? `<img src="${chat.avatar}" class="w-12 h-12 rounded-full object-cover border-2 border-white" alt="${chat.name}"/>`
        : `<div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shrink-0" style="background:${roleColor}">${initials(chat.name)}</div>`;

    const badge = chat.unread > 0
        ? `<span class="badge-pop ml-2 min-w-[20px] h-5 rounded-full text-white text-[10px] font-bold flex items-center justify-center px-1.5" style="background:${roleColor}">${chat.unread>99?'99+':chat.unread}</span>`
        : '';

    const unreadDot = chat.unread > 0
        ? `<span class="w-2 h-2 rounded-full ml-1 shrink-0" style="background:${roleColor}"></span>`
        : '';

    const msgStyle = chat.unread > 0
        ? 'color:#4A0E35;font-weight:600;'
        : 'color:#A2397B;font-weight:400;';

    return `
    <a href="/chat/${chat.otherUserId}?nama=${encodeURIComponent(chat.name)}"
       class="chat-item flex items-center gap-3 px-4 py-3.5 border-b border-plum-soft/30 last:border-b-0">
        <div class="relative shrink-0">
            ${av}
            ${chat.unread > 0 ? `<span class="absolute -top-1 -right-1 w-4 h-4 rounded-full border-2 border-white" style="background:#FF4757;"></span>` : ''}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
                <span class="text-plum-dark font-bold text-sm truncate flex-1">${chat.name}</span>
                <div class="flex items-center gap-1 ml-2 shrink-0">
                    ${badge}
                    <span class="text-plum-muted/70 text-[10px] font-medium">${formatTime(chat.timestamp)}</span>
                </div>
            </div>
            <div class="flex items-center">
                <span class="text-sm truncate flex-1" style="${msgStyle}">${chat.lastMessage||'Belum ada pesan'}</span>
                ${unreadDot}
            </div>
        </div>
        <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0" style="background:${roleColor}20">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M4 2L8 6L4 10" stroke="${roleColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
    </a>`;
}

function buildGroupHTML(role, chats, idx) {
    const cfg        = getRoleConfig(role);
    const totalUnread = chats.reduce((s,c)=>s+c.unread,0);
    const unreadBadge = totalUnread > 0
        ? `<span class="ml-2 px-2 py-0.5 rounded-full text-white text-[10px] font-bold" style="background:${cfg.color}">${totalUnread>99?'99+':totalUnread}</span>`
        : '';
    const groupId = `group-${idx}`;
    const contentId = `content-${idx}`;
    const chevronId = `chevron-${idx}`;

    return `
    <div class="anim-up bg-white rounded-3xl shadow-sm overflow-hidden border-2 border-plum-soft/40" style="animation-delay:${idx*0.07}s; border-left:4px solid ${cfg.color};">
        <!-- Group Header -->
        <button type="button" onclick="toggleGroup('${contentId}','${chevronId}')"
                class="w-full flex items-center gap-3 px-4 py-3.5 text-left"
                style="background:${cfg.bg};">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:${cfg.color}20;">
                <ion-icon name="${cfg.icon}" style="font-size:18px;color:${cfg.color};"></ion-icon>
            </div>
            <div class="flex-1">
                <p class="font-extrabold text-sm" style="color:${cfg.color};">${role}</p>
                <p class="text-[11px] text-plum-muted font-medium">${chats.length} percakapan</p>
            </div>
            ${unreadBadge}
            <ion-icon id="${chevronId}" name="chevron-forward" class="group-chevron open" style="font-size:18px;color:${cfg.color};flex-shrink:0;"></ion-icon>
        </button>

        <!-- Chat Items -->
        <div id="${contentId}" class="group-content border-t border-plum-soft/40">
            ${chats.map(c=>buildChatItemHTML(c, cfg.color)).join('')}
        </div>
    </div>`;
}

function renderChats(chats) {
    const groups  = groupByRole(chats);
    const total   = chats.length;
    const unread  = chats.reduce((s,c)=>s+c.unread,0);

    document.getElementById('headerSubtitle').textContent =
        `${total} percakapan${unread>0?' · '+unread+' belum dibaca':''}`;

    const container = document.getElementById('chatGroups');
    const empty     = document.getElementById('emptyState');
    const skeleton  = document.getElementById('skeletonLoader');

    skeleton.style.display = 'none';
    container.classList.remove('hidden');

    if (groups.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }

    empty.classList.add('hidden');
    container.innerHTML = groups.map(([r,c],i)=>buildGroupHTML(r,c,i)).join('');
}

function filterChats(q) {
    const btn = document.getElementById('clearSearch');
    btn.classList.toggle('hidden', !q);
    if (!q) { renderChats(allChats); return; }

    const filtered = allChats.filter(c =>
        c.name.toLowerCase().includes(q.toLowerCase()) ||
        c.lastMessage.toLowerCase().includes(q.toLowerCase())
    );
    renderChats(filtered);

    if (filtered.length === 0) {
        document.getElementById('emptyIcon').setAttribute('name','search-outline');
        document.getElementById('emptyTitle').textContent = 'Percakapan tidak ditemukan';
        document.getElementById('emptyDesc').textContent  = `Tidak ada yang cocok dengan "${q}"`;
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearch').classList.add('hidden');
    renderChats(allChats);
}

function toggleGroup(contentId, chevronId) {
    const content = document.getElementById(contentId);
    const chevron = document.getElementById(chevronId);
    const isOpen  = chevron.classList.contains('open');
    if (isOpen) {
        content.style.maxHeight = content.scrollHeight + 'px';
        requestAnimationFrame(() => { content.style.maxHeight = '0'; });
        chevron.classList.remove('open');
    } else {
        content.style.maxHeight = content.scrollHeight + 'px';
        setTimeout(() => { content.style.maxHeight = 'none'; }, 300);
        chevron.classList.add('open');
    }
}

// ── Fetch Chat List ───────────────────────────────────────────────────────────
async function fetchChatList() {
    try {
        const res  = await fetch('{{ url("/api/chat-list") }}', {
            headers:{ 'Accept':'application/json', 'Authorization':`Bearer ${AUTH_TOKEN}` }
        });
        const data = await res.json();
        if (data.success && Array.isArray(data.data)) {
            allChats = processChatData(data.data);
            renderChats(allChats);
        } else {
            renderChats([]);
        }
    } catch(e) {
        renderChats([]);
    }
}

fetchChatList();

// ── Pusher: update unread badge in list realtime ──────────────────────────────
(function initPusher() {
    if (!USER_ID || !AUTH_TOKEN || !PUSHER_KEY) return;
    const pusher  = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_CLUSTER, forceTLS: true,
        authEndpoint: PUSHER_AUTH_EP,
        auth: { headers: { 'X-CSRF-TOKEN': CSRF, 'Authorization': `Bearer ${AUTH_TOKEN}` } }
    });
    const channel = pusher.subscribe(`private-chat.${USER_ID}`);
    channel.bind('chat.new', (event) => {
        const chat = event?.chat;
        if (!chat || chat.id_penerima != USER_ID) return;
        // Update local unread count & re-render
        const existing = allChats.find(c => c.otherUserId == chat.id_pengirim);
        if (existing) {
            existing.unread++;
            existing.lastMessage = chat.pesan;
            existing.timestamp   = chat.created_at;
        } else {
            allChats.unshift({
                id: chat.id, otherUserId: chat.id_pengirim,
                name: chat.nama_pengirim || 'Pengguna',
                role: chat.role_pengirim || 'Lainnya',
                avatar: chat.foto_pengirim || null,
                lastMessage: chat.pesan,
                timestamp: chat.created_at,
                unread: 1,
            });
        }
        // Re-render if not searching
        const q = document.getElementById('searchInput').value;
        if (!q) renderChats(allChats);
    });
})();
</script>
</body>
</html>