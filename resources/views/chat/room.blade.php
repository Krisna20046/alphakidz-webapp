@extends('layouts.app')

@php $activeNav = 'home'; $hideBottomNav = true @endphp

@section('title', 'Chat')

@push('styles')
<style>
    html, body { margin:0; height:100%; overflow:hidden; }

    /* Chat area */
    #chatMessages {
        flex:1; overflow-y:auto; padding:16px; padding-bottom:8px;
        display:flex; flex-direction:column; gap:10px;
        background: linear-gradient(to bottom, #F8F7FF 0%, #F3F0FC 100%);
    }
    #chatMessages::-webkit-scrollbar { width:4px; }
    #chatMessages::-webkit-scrollbar-thumb { background:#D6C8F6; border-radius:4px; }

    /* Bubbles */
    .bubble-wrap { display:flex; align-items:flex-end; gap:8px; }
    .bubble-wrap.sent { flex-direction:row-reverse; }

    .bubble {
        max-width:72%; padding:10px 14px;
        border-radius:12px; line-height:1.45;
        word-break:break-word;
        animation:bubbleIn .2s ease both;
        border: 1.5px solid #D7BFF1;
        background: transparent;
        color: #111827;
    }
    @keyframes bubbleIn { from{opacity:0;transform:translateY(6px) scale(0.96)} to{opacity:1;transform:none} }

    .bubble.recv { border-bottom-left-radius:4px; }
    .bubble.sent { border-bottom-right-radius:4px; }
    .bubble.sending { opacity:.55; }

    .bubble-time {
        font-size:10px; font-weight:500; margin-top:4px; display:block;
    }
    .bubble.recv .bubble-time { color:#8B46D3; text-align:left; }
    .bubble.sent .bubble-time { color:#8B46D3; text-align:right; }

    /* Avatar small */
    .chat-avatar-ph {
        width:36px; height:36px; border-radius:50%;
        background:#F3F0FD; color:#8B46D3; border:2px solid #EDE9FE;
        display:flex; align-items:center; justify-content:center;
        font-size:13px; font-weight:700; flex-shrink:0;
    }

    /* Date separator */
    .date-sep {
        text-align:center; font-size:11px; font-weight:800;
        color:#4F46E5; padding:6px 0;
        display:flex; align-items:center; justify-content:center;
    }
    .date-sep::before,.date-sep::after { content:none; }
    .date-sep span {
        background:#DDE4FF;
        border-radius:999px;
        padding:4px 12px;
        text-transform:uppercase;
        letter-spacing:.6px;
    }

    /* Load more */
    #loadMoreBtn {
        align-self:center; font-size:12px; font-weight:600;
        color:#8B46D3; background:#EFE9FB; border:none; outline:none;
        padding:6px 16px; border-radius:20px; cursor:pointer;
        transition:background .15s; display:none;
    }
    #loadMoreBtn:active { background:#E0D2F7; }
    #loadMoreBtn.visible { display:block; }

    #loadMoreSpinner {
        align-self:center; display:none; align-items:center; gap:8px;
        font-size:12px; font-weight:700; color:#8B46D3; padding:6px 0;
    }
    #loadMoreSpinner.visible { display:flex; }
    @keyframes spin { to{transform:rotate(360deg)} }
    .spinner-ring {
        width:16px; height:16px; border:2px solid #EDE9FE;
        border-top-color:#8B46D3; border-radius:50%;
        animation:spin .7s linear infinite;
    }

    /* Input bar */
    #inputBar {
        background: linear-gradient(to bottom, #F3F0FC 0%, #F3F0FC 100%);
        border-top:1px solid #D7BFF1;
        padding:12px 16px;
        padding-bottom:max(12px, env(safe-area-inset-bottom));
        flex-shrink:0;
    }
    #msgInput {
        min-height: 56px;
        flex:1; outline:none;
        font-family:'Nunito',sans-serif;
        font-size:15px; font-weight:800;
        color:#8B46D3; background:transparent;
        border:1.5px solid #D7BFF1; border-radius:12px;
        padding:10px 16px;
        resize:none; max-height:120px; overflow-y:auto;
        line-height:1.4;
        transition:border-color .2s;
    }
    #msgInput:focus { border-color:#8B46D3; }
    #msgInput::placeholder { color:#8B46D3; font-weight:800; padding-top:6px;}

    #sendBtn {
        width:64px; height:56px; border-radius:12px;
        background:transparent;
        border:1.5px solid #D7BFF1; outline:none; cursor:pointer; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        transition:opacity .15s, transform .15s;
    }
    #sendBtn:active { transform:scale(0.92); }
    #sendBtn:disabled { opacity:.45; cursor:not-allowed; transform:none; }

    /* Online dot */
    .dot-online { background:#22C55E; }
    .dot-offline { background:#A8A2C2; }

    /* Toast */
    #toast {
        position:absolute; top:16px; left:50%;
        transform:translateX(-50%) translateY(-120%); z-index:100;
        font-size:12px; font-weight:600; padding:8px 18px;
        border-radius:20px; white-space:nowrap; transition:transform .3s ease;
        color:#fff;
    }
    #toast.show { transform:translateX(-50%) translateY(0); }

    /* Empty state */
    #emptyState {
        flex:1; display:none; flex-direction:column;
        align-items:center; justify-content:center;
        padding:40px 20px; gap:16px;
    }
    #emptyState.visible { display:flex; }
    .empty-icon-circle {
        width:100px; height:100px; border-radius:50%;
        background:#D9C8ED;
        display:flex; align-items:center; justify-content:center;
    }

    /* Skeleton */
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    .skeleton { animation:pulse 1.5s ease-in-out infinite; }
</style>
@endpush

@section('content')
<!-- HEADER CHAT -->
<div class="flex gap-2 items-center anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <a href="{{ route('chat.list') }}"
       class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
        <ion-icon name="arrow-back" style="font-size:18px;color:white;"></ion-icon>
    </a>

    <!-- Avatar -->
    <div class="relative shrink-0 ml-2">
        <div class="w-12 h-12 rounded-full bg-[#F3F0FD] flex items-center justify-center text-[#8B46D3] font-extrabold text-base border-2 border-white/80">
            {{ strtoupper(substr($namaPenerima ?? '?', 0, 1)) }}
        </div>
        <div id="onlineDot" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dot-offline"></div>
    </div>

    <!-- Name & status -->
    <div class="flex-1 min-w-0">
        <p class="text-white font-semibold text-[20px] leading-none truncate">{{ $namaPenerima ?? 'Chat' }}</p>
        <div class="flex items-center gap-1.5 mt-0.5">
            <span id="statusText" class="text-white/80 text-[14px] leading-none font-semibold">Online now</span>
        </div>
    </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<!-- MESSAGES AREA -->
<div id="chatMessages" class="rounded-t-[34px] -mt-[50px] relative z-20 flex-1 overflow-y-auto">

    <!-- Load more -->
    <div id="loadMoreSpinner"><div class="spinner-ring"></div>Memuat pesan lama...</div>
    <button id="loadMoreBtn" onclick="loadMore()">↑ Muat pesan lama</button>

    <!-- Skeleton -->
    <div id="msgSkeleton" class="flex flex-col gap-3 py-4 skeleton">
        @for($i=0;$i<5;$i++)
        <div class="flex {{ $i%2==0 ? '' : 'flex-row-reverse' }} items-end gap-2">
            <div class="w-8 h-8 rounded-full bg-[#EDE9FE] shrink-0"></div>
            <div class="h-10 bg-[#EDE9FE] rounded-2xl {{ $i%2==0 ? 'rounded-bl-sm' : 'rounded-br-sm' }}"
                 style="width:{{ 120+($i*30) }}px;"></div>
        </div>
        @endfor
    </div>

    <!-- Empty state -->
    <div id="emptyState">
        <div class="empty-icon-circle">
            <ion-icon name="chatbox-ellipses" style="font-size:52px;color:#8B46D3;"></ion-icon>
        </div>
        <p class="text-[#030712] font-extrabold text-[16px] leading-none">No Messages Yet</p>
        <p class="text-[#111827] text-[13px] text-center leading-[1.35]">
            Start The Conversation By<br>Sending The First Message
        </p>
    </div>
</div>

<!-- INPUT BAR -->
<div id="inputBar" class="flex items-end gap-2">
    <textarea id="msgInput" rows="1"
              placeholder="Message..."
              oninput="autoGrow(this); toggleSendBtn()"
              onkeydown="handleKey(event)"></textarea>
    <button id="sendBtn" onclick="sendMessage()" disabled>
        <ion-icon name="send" style="font-size:20px;color:#8B46D3;margin-left:2px;"></ion-icon>
    </button>
</div>
@endsection

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endpush

@push('scripts')
<script>
// ── Config ────────────────────────────────────────────────────────────────────
@php
    $resolvedUserId = session('user_id') ?: data_get(session('user'), 'id_user');
@endphp
const USER_ID        = @json($resolvedUserId);
const ID_PENERIMA    = {{ $idPenerima ?? 'null' }};
const NAMA_PENERIMA  = "{{ addslashes($namaPenerima ?? '') }}";
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const CHAT_API       = "{{ url('/api/chat') }}";

// ── State ─────────────────────────────────────────────────────────────────────
let messages      = [];
let hasMore       = false;
let page          = 1;
let isLoadingMore = false;

// ── Helpers ───────────────────────────────────────────────────────────────────
const ensureNum = v => typeof v === 'number' ? v : Number(v);

function fmtTime(ts){ return new Date(ts).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}); }
function fmtDate(ts){ return new Date(ts).toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long'}); }
function escHtml(s){ const d=document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }

function showToast(msg, type='error'){
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = type==='success' ? '#3BB273' : type==='info' ? '#4A0E35' : '#E84855';
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 2500);
}

function autoGrow(el){ el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,120)+'px'; }
function toggleSendBtn(){ document.getElementById('sendBtn').disabled = !document.getElementById('msgInput').value.trim(); }
function handleKey(e){ if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); } }

function scrollToBottom(smooth=false){
    const area = document.getElementById('chatMessages');
    area.scrollTo({top: area.scrollHeight, behavior: smooth ? 'smooth' : 'instant'});
}

// ── Build bubble HTML ─────────────────────────────────────────────────────────
function buildBubble(msg){
    const isSent = ensureNum(msg.id_pengirim) === ensureNum(USER_ID);
    return `
    <div class="bubble-wrap ${isSent?'sent':''}" data-msgid="${msg.id}">
        ${!isSent ? `<div class="chat-avatar-ph">${escHtml(NAMA_PENERIMA.charAt(0).toUpperCase())}</div>` : ''}
        <div class="bubble ${isSent?'sent':'recv'}${msg._temp?' sending':''}">
            <span class="text-sm font-medium">${escHtml(msg.pesan)}</span>
            <span class="bubble-time">${fmtTime(msg.created_at)}${isSent&&!msg._temp?' ✓':''}</span>
        </div>
    </div>`;
}

function buildDateSep(ts){
    const el = document.createElement('div');
    el.className   = 'date-sep';
    el.dataset.sep = fmtDate(ts);
    const dateLabel = fmtDate(ts);
    const todayLabel = new Date(ts).toDateString() === new Date().toDateString() ? 'TODAY' : dateLabel;
    el.innerHTML = `<span>${escHtml(todayLabel)}</span>`;
    return el;
}

function renderAll(){
    const area = document.getElementById('chatMessages');

    const loadMoreBtn     = document.getElementById('loadMoreBtn');
    const loadMoreSpinner = document.getElementById('loadMoreSpinner');

    [...area.children].forEach(el => {
        if(el.id==='loadMoreBtn'||el.id==='loadMoreSpinner'||el.id==='msgSkeleton'||el.id==='emptyState') return;
        el.remove();
    });

    if(messages.length === 0){
        document.getElementById('emptyState').classList.add('visible');
        return;
    }
    document.getElementById('emptyState').classList.remove('visible');

    const sorted = [...messages].reverse();
    const frag   = document.createDocumentFragment();
    let prevDate = '';

    sorted.forEach(msg => {
        const dateStr = fmtDate(msg.created_at);
        if(dateStr !== prevDate){
            prevDate = dateStr;
            frag.appendChild(buildDateSep(msg.created_at));
        }
        const wrap = document.createElement('div');
        wrap.innerHTML = buildBubble(msg);
        frag.appendChild(wrap.firstElementChild);
    });

    area.appendChild(frag);
    updateLoadMoreUI();
}

function prependOlderMessages(olderMsgs){
    const area     = document.getElementById('chatMessages');
    const loadMoreBtn = document.getElementById('loadMoreBtn');

    const prevH   = area.scrollHeight;
    const prevTop = area.scrollTop;

    let insertRef = loadMoreBtn.nextSibling;

    const firstExistingSep = [...area.children].find(el =>
        el.classList?.contains('date-sep') &&
        el !== loadMoreBtn &&
        el !== document.getElementById('loadMoreSpinner')
    );
    const firstExistingDate = firstExistingSep?.dataset.sep ?? null;

    let prevDate = '';
    olderMsgs.forEach((msg, i) => {
        const dateStr = fmtDate(msg.created_at);
        const isLast  = i === olderMsgs.length - 1;

        if(dateStr !== prevDate){
            prevDate = dateStr;
            if(!(isLast && dateStr === firstExistingDate)){
                area.insertBefore(buildDateSep(msg.created_at), insertRef);
            }
        }
        const wrap = document.createElement('div');
        wrap.innerHTML = buildBubble(msg);
        area.insertBefore(wrap.firstElementChild, insertRef);
    });

    requestAnimationFrame(()=>{
        area.scrollTop = prevTop + (area.scrollHeight - prevH);
    });

    updateLoadMoreUI();
}

function updateLoadMoreUI(){
    const btn     = document.getElementById('loadMoreBtn');
    const spinner = document.getElementById('loadMoreSpinner');
    spinner.classList.remove('visible');
    if(hasMore){
        btn.classList.add('visible');
        btn.textContent = '↑ Muat pesan lama';
    } else {
        btn.classList.remove('visible');
    }
}

async function fetchChat(targetPage=1, replace=false){
    try {
        if(!AUTH_TOKEN || !ID_PENERIMA) return;

        const res  = await fetch(`${CHAT_API}?id_penerima=${ID_PENERIMA}&page=${targetPage}`, {
            headers:{'Accept':'application/json','Authorization':`Bearer ${AUTH_TOKEN}`}
        });
        const data = await res.json();

        document.getElementById('msgSkeleton')?.remove();

        if(data.status==='success' && Array.isArray(data.data)){
            hasMore = data.has_more ?? false;
            page    = targetPage;

            const reversed = [...data.data].reverse();

            if(replace){
                messages = reversed;
                renderAll();
                scrollToBottom();
            } else {
                const olderForDOM = [...data.data];
                messages = [...messages, ...reversed];
                prependOlderMessages(olderForDOM);
            }
        }
    } catch(e){
        console.error('fetchChat error', e);
        document.getElementById('msgSkeleton')?.remove();
        showToast('Gagal memuat pesan.');
    }
}

async function loadMore(){
    if(isLoadingMore || !hasMore) return;
    isLoadingMore = true;

    const btn     = document.getElementById('loadMoreBtn');
    const spinner = document.getElementById('loadMoreSpinner');
    btn.classList.remove('visible');
    spinner.classList.add('visible');

    try {
        await fetchChat(page + 1, false);
    } finally {
        isLoadingMore = false;
        setTimeout(()=>{ isLoadingMore = false; }, 500);
    }
}

function initScrollListener(){
    const area = document.getElementById('chatMessages');
    let ticking = false;
    area.addEventListener('scroll', ()=>{
        if(ticking) return;
        ticking = true;
        requestAnimationFrame(()=>{
            if(area.scrollTop < 120 && hasMore && !isLoadingMore){
                loadMore();
            }
            ticking = false;
        });
    });
}

async function sendMessage(){
    const input = document.getElementById('msgInput');
    const text  = input.value.trim();
    if(!text || !ID_PENERIMA) return;

    input.value=''; input.style.height='auto'; toggleSendBtn();

    const tempId  = `temp-${Date.now()}`;
    const tempMsg = {
        id:        tempId,
        id_pengirim: USER_ID,
        id_penerima: ID_PENERIMA,
        pesan:     text,
        created_at: new Date().toISOString(),
        _temp:     true,
    };

    messages = [tempMsg, ...messages];

    const area   = document.getElementById('chatMessages');
    const emptyState = document.getElementById('emptyState');
    emptyState.classList.remove('visible');

    const lastSep   = [...area.querySelectorAll('.date-sep')].pop();
    const todayStr  = fmtDate(tempMsg.created_at);
    if(!lastSep || lastSep.dataset.sep !== todayStr){
        area.appendChild(buildDateSep(tempMsg.created_at));
    }
    const wrap = document.createElement('div');
    wrap.innerHTML = buildBubble(tempMsg);
    area.appendChild(wrap.firstElementChild);
    scrollToBottom(true);

    try {
        const res  = await fetch(CHAT_API, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'Authorization':`Bearer ${AUTH_TOKEN}`,
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({
                id_pengirim: ensureNum(USER_ID),
                id_penerima: ensureNum(ID_PENERIMA),
                pesan: text,
                is_read: 0,
            })
        });
        const data = await res.json();

        if(data.status==='success' && data.chat){
            const hasTempMsg = messages.some(m => m.id === tempId);
            const hasRealMsg = messages.some(m => m.id === data.chat.id);

            if(hasTempMsg && !hasRealMsg){
                messages = messages.map(m => m.id===tempId ? data.chat : m);
                const tempEl = document.querySelector(`[data-msgid="${tempId}"]`);
                if(tempEl){
                    const w2 = document.createElement('div');
                    w2.innerHTML = buildBubble(data.chat);
                    tempEl.replaceWith(w2.firstElementChild);
                }
            } else if(hasTempMsg && hasRealMsg){
                messages = messages.filter(m => m.id !== tempId);
                document.querySelector(`[data-msgid="${tempId}"]`)?.remove();
            }
        } else {
            messages = messages.filter(m => m.id !== tempId);
            document.querySelector(`[data-msgid="${tempId}"]`)?.remove();
            showToast(data.message || 'Pesan gagal dikirim.');
            if(messages.length===0) document.getElementById('emptyState').classList.add('visible');
        }
    } catch(e){
        messages = messages.filter(m => m.id !== tempId);
        document.querySelector(`[data-msgid="${tempId}"]`)?.remove();
        showToast('Terjadi kesalahan. Coba lagi.');
        if(messages.length===0) document.getElementById('emptyState').classList.add('visible');
    }
}

// ── Pusher Real-time ──────────────────────────────────────────────────────────
(function initPusher(){
    if(!USER_ID || !PUSHER_KEY) return;

    const pusher  = new Pusher(PUSHER_KEY, {
        cluster:      PUSHER_CLUSTER,
        forceTLS:     true,
        authEndpoint: PUSHER_AUTH_EP,
        auth: { headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'} }
    });

    const channel = pusher.subscribe(`private-chat.${USER_ID}`);

    channel.bind('pusher:subscription_succeeded', ()=>{
        document.getElementById('onlineDot').className =
            'absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dot-online';
        document.getElementById('statusText').textContent = 'Online now';
    });

    channel.bind('chat.new', (event)=>{
        const chat = event?.chat;
        if(!chat) return;

        const senderId   = ensureNum(chat.id_pengirim);
        const receiverId = ensureNum(chat.id_penerima);

        const isFromCurrentPartner =
            senderId   === ensureNum(ID_PENERIMA) &&
            receiverId === ensureNum(USER_ID);
        if(!isFromCurrentPartner) return;

        const exists = messages.some(m => m.id === chat.id);
        if(exists) return;
        if(document.querySelector(`[data-msgid="${chat.id}"]`)) return;

        messages = [chat, ...messages];

        const area       = document.getElementById('chatMessages');
        const emptyState = document.getElementById('emptyState');
        emptyState.classList.remove('visible');

        const lastSep  = [...area.querySelectorAll('.date-sep')].pop();
        const dateStr  = fmtDate(chat.created_at);
        if(!lastSep || lastSep.dataset.sep !== dateStr){
            area.appendChild(buildDateSep(chat.created_at));
        }
        const wrap = document.createElement('div');
        wrap.innerHTML = buildBubble(chat);
        area.appendChild(wrap.firstElementChild);
        scrollToBottom(true);
    });

    pusher.connection.bind('disconnected', ()=>{
        document.getElementById('onlineDot').className =
            'absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dot-offline';
        document.getElementById('statusText').textContent = 'Offline';
    });
})();

// ── VisualViewport handler (mobile keyboard support) ──────────────────────────
(function initViewport(){
    if(!window.visualViewport) return;

    const phoneFrame = document.getElementById('phoneFrame');

    function adjustViewport(){
        const vv = window.visualViewport;
        const isMobile = window.innerWidth < 640;
        if(!isMobile) return;
        phoneFrame.style.height = vv.height + 'px';
    }

    window.visualViewport.addEventListener('resize', () => {
        adjustViewport();
        requestAnimationFrame(() => scrollToBottom());
    });

    adjustViewport();
})();

// ── Init ──────────────────────────────────────────────────────────────────────
(async function init(){
    await fetchChat(1, true);
    initScrollListener();
    setTimeout(() => scrollToBottom(), 300);
})();
</script>
@endpush
