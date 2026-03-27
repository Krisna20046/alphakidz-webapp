<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pesan</title>
    @include('partials.pwa-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: slideUp 0.4s ease forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.13s; }
        .delay-3 { animation-delay: 0.21s; }

        @keyframes floatEmpty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .float-anim { animation:floatEmpty 3s ease-in-out infinite; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .search-input:focus { outline: none; }
        .chat-item { transition: transform .15s ease; }
        .chat-item:active { transform: scale(0.98); }

        .tab-btn {
            border-radius: 12px;
            padding: 8px 14px;
            color: #7C7893;
            font-size: 13px;
            font-weight: 800;
            line-height: 1;
            transition: all .15s ease;
        }
        .tab-btn.active {
            background: linear-gradient(to right, #7C3AED, #8B46D3);
            color: white;
            box-shadow: 0 6px 14px rgba(139,70,211,0.28);
        }
    </style>
</head>
<body class="font-['Nunito'] bg-[#E5E2F5]">
<div class="sm:flex sm:items-start sm:justify-center sm:min-h-screen sm:py-8 sm:pb-[60px]">
<div class="sm:w-[390px] sm:min-h-[844px] sm:rounded-[44px] sm:shadow-[0_40px_80px_rgba(124,58,237,0.28),0_0_0_8px_#1a1030,0_0_0_10px_#2d1a50] sm:overflow-hidden bg-[#F0EDFB] min-h-screen flex flex-col relative">

    <div class="hidden sm:flex sm:items-center sm:justify-between bg-[#8B46D3] px-6 pt-[14px] text-white text-xs font-bold">
        <span id="statusTime">9:41</span>
        <div class="flex items-center gap-1.5">
            <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                <rect x="0" y="4" width="3" height="7" rx="0.6" fill="white" opacity="0.5"/>
                <rect x="4.5" y="2.5" width="3" height="8.5" rx="0.6" fill="white" opacity="0.7"/>
                <rect x="9" y="0.5" width="3" height="10.5" rx="0.6" fill="white"/>
            </svg>
            <div class="flex items-center">
                <div class="w-[22px] h-[11px] border-[1.5px] border-white/70 rounded-[3px] p-[1.5px]">
                    <div class="bg-white rounded-[1.5px] h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
                px-[24px] pt-[55px] pb-[72px]
                before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('dashboard') }}"
               class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
                <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
            </a>
            <div>
                <span class="text-white text-[17px] font-extrabold tracking-wide">Message</span>
                <p id="headerSubtitle" class="text-white/70 text-xs font-semibold mt-0.5">Loading conversation...</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
        <div class="anim delay-2 bg-white rounded-[20px] p-4 shadow-[0_2px_12px_rgba(0,0,0,0.08)]">
            <div class="flex items-center bg-[#F4F4F4] rounded-[10px] border border-[#DDD6EF] px-3 py-2.5 mb-3">
                <ion-icon name="search-outline" style="font-size:16px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                <input type="text" id="searchInput" placeholder="Search message...."
                       class="search-input flex-1 text-[13px] font-semibold text-[#4B5563] placeholder-[#9CA3AF] bg-transparent ml-2"
                       oninput="applyFilters()">
            </div>

            <div class="border border-[#DDD6EF] rounded-[10px] p-1 grid grid-cols-3 gap-1">
                <button class="tab-btn active" data-tab="all" onclick="setRoleFilter('all')">All</button>
                <button class="tab-btn" data-tab="consultant" onclick="setRoleFilter('consultant')">Consultant</button>
                <button class="tab-btn" data-tab="nanny" onclick="setRoleFilter('nanny')">Nanny</button>
            </div>
        </div>

        <div id="chatListArea" class="pt-3 pb-2">
            <div id="skeletonLoader" class="space-y-3">
                @for($i=0;$i<6;$i++)
                <div class="bg-white rounded-[14px] p-3.5 flex items-center gap-3 animate-pulse">
                    <div class="w-[52px] h-[52px] rounded-[8px] bg-[#ECE8FA] shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 bg-[#ECE8FA] rounded-full w-2/3"></div>
                        <div class="h-3 bg-[#ECE8FA] rounded-full w-1/2"></div>
                    </div>
                    <div class="h-3 w-10 bg-[#ECE8FA] rounded-full"></div>
                </div>
                @endfor
            </div>

            <div id="chatList" class="space-y-3 hidden"></div>

            <div id="emptyState" class="hidden flex flex-col items-center pt-16 pb-8 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-6">
                    <ion-icon id="emptyIcon" name="chatbubble-ellipses-outline" style="font-size:48px;color:#C4B5FD;"></ion-icon>
                </div>
                <h3 id="emptyTitle" class="text-[#1E1B2E] font-bold text-lg mb-2 text-center">Belum ada percakapan</h3>
                <p id="emptyDesc" class="text-[#9CA3AF] text-sm text-center leading-relaxed">
                    Mulai percakapan baru dengan mengirim pesan pertama
                </p>
            </div>
        </div>
    </div>

    @include('partials.bottom-nav', ['active' => 'home'])

</div>
</div>

<script>
// ── Config ────────────────────────────────────────────────────────────────────
@php
    $resolvedUserId = session('user_id') ?: data_get(session('user'), 'id_user');
@endphp
const USER_ID        = @json($resolvedUserId);
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const CSRF           = "{{ csrf_token() }}";

// ── Helpers ───────────────────────────────────────────────────────────────────
function updateClock(){const el=document.getElementById('statusTime');if(el){const n=new Date();el.textContent=`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;}}
updateClock();setInterval(updateClock,30000);

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

function normalizeRole(role) {
    const r = (role || '').toLowerCase();
    if (r.includes('konsultan') || r.includes('consultant')) return 'consultant';
    if (r.includes('nanny') || r.includes('pengasuh')) return 'nanny';
    return 'other';
}

// ── Chat Data ─────────────────────────────────────────────────────────────────
let allChats = [];
let currentRoleFilter = 'all';

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
        roleType    : normalizeRole(c.role_penerima),
    }));
}

// ── Render ────────────────────────────────────────────────────────────────────
function buildChatItemHTML(chat, idx) {
    const av = chat.avatar
        ? `<img src="${chat.avatar}" class="w-[52px] h-[52px] rounded-[8px] object-cover bg-[#F3F0FD]" alt="${chat.name}"/>`
        : `<div class="w-[52px] h-[52px] rounded-[8px] flex items-center justify-center text-[#8B46D3] font-extrabold text-lg bg-[#F3F0FD]">${initials(chat.name)}</div>`;

    const unreadBadge = chat.unread > 0
        ? `<span class="w-5 h-5 rounded-full bg-[#8B46D3] text-white text-[10px] font-extrabold flex items-center justify-center">${chat.unread>99?'99+':chat.unread}</span>`
        : '';
    const isUnread = chat.unread > 0;
    const itemClass = isUnread
        ? 'bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5]'
        : 'bg-transparent rounded-[14px] px-3 py-2.5 border border-transparent';

    const roleDot = chat.roleType === 'consultant'
        ? '#10B981'
        : (chat.roleType === 'nanny' ? '#06B6D4' : '#A78BFA');

    const timeText = chat.unread > 0 ? formatTime(chat.timestamp) : (new Date(chat.timestamp) > (new Date(Date.now() - 24*3600000)) ? formatTime(chat.timestamp) : 'Friday');

    return `
    <a href="/chat/${chat.otherUserId}?nama=${encodeURIComponent(chat.name)}"
       class="chat-item block ${itemClass}"
       style="animation: slideUp .35s ease ${idx * 0.04}s both; opacity:0;">
       <div class="flex items-center gap-3">
        <div class="relative shrink-0">
            ${av}
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white" style="background:${roleDot};"></span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <p class="text-[#1E1B2E] text-[15px] font-extrabold truncate">${chat.name}</p>
                <span class="text-[#8B46D3] text-[12px] font-bold shrink-0">${timeText}</span>
            </div>

            <div class="flex items-center justify-between gap-2 mt-0.5">
                <p class="text-[#4B5563] text-[13px] font-semibold truncate">${chat.lastMessage||'Belum ada pesan'}</p>
                ${unreadBadge}
            </div>
        </div>
       </div>
    </a>`;
}

function renderChats(chats) {
    const total = chats.length;
    const unread = chats.reduce((s,c)=>s+c.unread,0);
    const list = document.getElementById('chatList');
    const empty = document.getElementById('emptyState');
    const skeleton = document.getElementById('skeletonLoader');

    document.getElementById('headerSubtitle').textContent =
        `${total} conversation${total > 1 ? 's' : ''}${unread>0?' · '+unread+' unread':''}`;

    skeleton.style.display = 'none';
    list.classList.remove('hidden');

    if (total === 0) {
        list.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }

    empty.classList.add('hidden');
    list.innerHTML = chats
        .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
        .map((chat, idx) => buildChatItemHTML(chat, idx))
        .join('');
}

function setRoleFilter(tab) {
    currentRoleFilter = tab;
    document.querySelectorAll('.tab-btn').forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    applyFilters();
}

function applyFilters() {
    const q = (document.getElementById('searchInput').value || '').toLowerCase().trim();
    let filtered = [...allChats];

    if (currentRoleFilter === 'consultant') {
        filtered = filtered.filter((c) => c.roleType === 'consultant');
    } else if (currentRoleFilter === 'nanny') {
        filtered = filtered.filter((c) => c.roleType === 'nanny');
    }

    if (q) {
        filtered = filtered.filter(c =>
            (c.name || '').toLowerCase().includes(q) ||
            (c.lastMessage || '').toLowerCase().includes(q)
        );
    }

    if (filtered.length === 0) {
        const isSearch = !!q;
        document.getElementById('emptyIcon').setAttribute('name', isSearch ? 'search-outline' : 'chatbubble-ellipses-outline');
        document.getElementById('emptyTitle').textContent = isSearch ? 'Percakapan tidak ditemukan' : 'Belum ada percakapan';
        document.getElementById('emptyDesc').textContent = isSearch ? `Tidak ada yang cocok dengan "${q}"` : 'Mulai percakapan baru dengan mengirim pesan pertama';
        renderChats([]);
    } else {
        renderChats(filtered);
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
            applyFilters();
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
                roleType: normalizeRole(chat.role_pengirim),
            });
        }
        applyFilters();
    });
})();
</script>
@include('partials.auth-guard')
</body>
</html>