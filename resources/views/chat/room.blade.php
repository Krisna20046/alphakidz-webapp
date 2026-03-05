<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Chat</title>
    @include('partials.pwa-head')
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
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#FFF9FB; margin:0; overflow:hidden; }

        @media (min-width:640px) {
            .phone-wrapper { display:flex; align-items:flex-start; justify-content:center; min-height:100vh; padding:32px 0; background:linear-gradient(135deg,#f8e8f3,#ede0f0,#e8d5ee); }
            .phone-frame   { width:390px; height:844px; border-radius:44px; box-shadow:0 40px 80px rgba(123,30,90,0.25),0 0 0 8px #1a0d14,0 0 0 10px #2d1020; overflow:hidden; display:flex; flex-direction:column; }
        }
        @media (max-width:639px) {
            .phone-frame { height:100dvh; display:flex; flex-direction:column; }
        }

        /* Chat area */
        #chatMessages {
            flex:1; overflow-y:auto; padding:16px; padding-bottom:8px;
            display:flex; flex-direction:column; gap:10px;
            /* Smooth scroll for programmatic scrollToBottom */
        }
        #chatMessages::-webkit-scrollbar { width:4px; }
        #chatMessages::-webkit-scrollbar-thumb { background:#E0BBE4; border-radius:4px; }

        /* Bubbles */
        .bubble-wrap { display:flex; align-items:flex-end; gap:8px; }
        .bubble-wrap.sent { flex-direction:row-reverse; }

        .bubble {
            max-width:72%; padding:10px 14px;
            border-radius:18px; line-height:1.45;
            word-break:break-word;
            animation:bubbleIn .2s ease both;
        }
        @keyframes bubbleIn { from{opacity:0;transform:translateY(6px) scale(0.96)} to{opacity:1;transform:none} }

        .bubble.recv {
            background:#FFFFFF;
            border:2px solid #F3E6FA;
            border-bottom-left-radius:4px;
            color:#4A0E35;
        }
        .bubble.sent {
            background:linear-gradient(135deg,#7B1E5A,#9B2E72);
            border-bottom-right-radius:4px;
            color:#FFFFFF;
        }
        .bubble.sending { opacity:.55; }

        .bubble-time {
            font-size:10px; font-weight:500; margin-top:4px; display:block;
        }
        .bubble.recv .bubble-time { color:#A2397B; text-align:left; }
        .bubble.sent .bubble-time { color:rgba(255,255,255,.6); text-align:right; }

        /* Avatar small */
        .chat-avatar-ph {
            width:32px; height:32px; border-radius:50%;
            background:#7B1E5A; color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; flex-shrink:0;
        }

        /* Date separator */
        .date-sep {
            text-align:center; font-size:11px; font-weight:600;
            color:#A2397B; padding:6px 0;
            display:flex; align-items:center; gap:8px;
        }
        .date-sep::before,.date-sep::after { content:''; flex:1; height:1px; background:#F3E6FA; }

        /* Load more */
        #loadMoreBtn {
            align-self:center; font-size:12px; font-weight:600;
            color:#7B1E5A; background:#F3E6FA; border:none; outline:none;
            padding:6px 16px; border-radius:20px; cursor:pointer;
            transition:background .15s; display:none;
        }
        #loadMoreBtn:active { background:#E0BBE4; }
        #loadMoreBtn.visible { display:block; }

        /* Load more spinner */
        #loadMoreSpinner {
            align-self:center; display:none; align-items:center; gap:8px;
            font-size:12px; font-weight:600; color:#A2397B; padding:6px 0;
        }
        #loadMoreSpinner.visible { display:flex; }
        @keyframes spin { to{transform:rotate(360deg)} }
        .spinner-ring {
            width:16px; height:16px; border:2px solid #F3E6FA;
            border-top-color:#7B1E5A; border-radius:50%;
            animation:spin .7s linear infinite;
        }

        /* Input bar */
        #inputBar {
            background:#FFFFFF;
            border-top:2px solid #F3E6FA;
            padding:12px 16px;
            padding-bottom:max(12px, env(safe-area-inset-bottom));
            flex-shrink:0;
        }
        #msgInput {
            flex:1; outline:none;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:15px; font-weight:500;
            color:#4A0E35; background:#FFF9FB;
            border:2px solid #F3E6FA; border-radius:20px;
            padding:10px 16px;
            resize:none; max-height:120px; overflow-y:auto;
            line-height:1.4;
            transition:border-color .2s;
        }
        #msgInput:focus { border-color:#7B1E5A; }
        #msgInput::placeholder { color:#B895C8; }

        #sendBtn {
            width:44px; height:44px; border-radius:22px;
            background:linear-gradient(135deg,#7B1E5A,#9B2E72);
            border:none; outline:none; cursor:pointer; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            transition:opacity .15s, transform .15s;
        }
        #sendBtn:active { transform:scale(0.92); }
        #sendBtn:disabled { opacity:.45; cursor:not-allowed; transform:none; background:#B895C8; }

        /* Online dot */
        .dot-online { background:#4CAF50; }
        .dot-offline { background:#FF9800; }

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
            background:#F3E6FA;
            display:flex; align-items:center; justify-content:center;
        }

        /* Skeleton */
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
        .skeleton { animation:pulse 1.5s ease-in-out infinite; }
    </style>
</head>
<body>
<div class="phone-wrapper">
<div class="phone-frame bg-plum-pale" style="position:relative;">

    <!-- STATUS BAR -->
    <div class="hidden sm:flex items-center justify-between px-8 pt-4 pb-1 bg-plum shrink-0">
        <span class="text-xs font-semibold text-white/80" id="statusTime">9:41</span>
        <div class="flex gap-1 items-center">
            <svg class="w-4 h-3" viewBox="0 0 17 12" fill="white" opacity="0.8">
                <rect x="0" y="3" width="3" height="9" rx="0.5"/>
                <rect x="4.5" y="2" width="3" height="10" rx="0.5"/>
                <rect x="9" y="0.5" width="3" height="11.5" rx="0.5"/>
            </svg>
            <div class="flex items-center">
                <div class="w-6 h-3 border border-white/70 rounded-sm p-px flex items-stretch">
                    <div class="bg-white flex-1"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- HEADER CHAT -->
    <div class="bg-gradient-to-r from-plum to-plum-light flex items-center gap-3 px-4 py-3.5 shrink-0 shadow-lg">
        <a href="{{ route('chat.list') }}"
           class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" style="font-size:20px;color:white;"></ion-icon>
        </a>

        <!-- Avatar -->
        <div class="relative shrink-0">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-base border-2 border-white/30">
                {{ strtoupper(substr($namaPenerima ?? '?', 0, 1)) }}
            </div>
            <div id="onlineDot" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-plum dot-offline"></div>
        </div>

        <!-- Name & status -->
        <div class="flex-1 min-w-0">
            <p class="text-white font-extrabold text-sm truncate">{{ $namaPenerima ?? 'Chat' }}</p>
            <div class="flex items-center gap-1.5 mt-0.5">
                <span id="statusText" class="text-white/60 text-[11px] font-medium">Aktif</span>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div id="toast"></div>

    <!-- MESSAGES AREA -->
    <div id="chatMessages">

        <!-- Load more (tampil di atas seperti React Native ListFooterComponent) -->
        <div id="loadMoreSpinner"><div class="spinner-ring"></div>Memuat pesan lama...</div>
        <button id="loadMoreBtn" onclick="loadMore()">↑ Muat pesan lama</button>

        <!-- Skeleton placeholder -->
        <div id="msgSkeleton" class="flex flex-col gap-3 py-4 skeleton">
            @for($i=0;$i<5;$i++)
            <div class="flex {{ $i%2==0 ? '' : 'flex-row-reverse' }} items-end gap-2">
                <div class="w-8 h-8 rounded-full bg-plum-soft shrink-0"></div>
                <div class="h-10 bg-plum-soft rounded-2xl {{ $i%2==0 ? 'rounded-bl-sm' : 'rounded-br-sm' }}"
                     style="width:{{ 120+($i*30) }}px;"></div>
            </div>
            @endfor
        </div>

        <!-- Empty state -->
        <div id="emptyState">
            <div class="empty-icon-circle">
                <ion-icon name="chatbubbles-outline" style="font-size:52px;color:#B895C8;"></ion-icon>
            </div>
            <p class="text-plum-dark font-bold text-lg">Belum ada pesan</p>
            <p class="text-plum-muted text-sm text-center leading-relaxed">
                Mulai percakapan dengan mengirim<br>pesan pertama
            </p>
        </div>
    </div>

    <!-- INPUT BAR -->
    <div id="inputBar" class="flex items-end gap-2">
        <textarea id="msgInput" rows="1"
                  placeholder="Ketik pesan..."
                  oninput="autoGrow(this); toggleSendBtn()"
                  onkeydown="handleKey(event)"></textarea>
        <button id="sendBtn" onclick="sendMessage()" disabled>
            <ion-icon name="send" style="font-size:18px;color:white;margin-left:2px;"></ion-icon>
        </button>
    </div>

</div>
</div>

<script>
// ── Config ────────────────────────────────────────────────────────────────────
const USER_ID        = {{ session('user')['id'] ?? 'null' }};
const ID_PENERIMA    = {{ $idPenerima ?? 'null' }};
const NAMA_PENERIMA  = "{{ addslashes($namaPenerima ?? '') }}";
const AUTH_TOKEN     = "{{ session('token') }}";
const PUSHER_KEY     = "{{ config('services.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('services.pusher.options.cluster', 'ap1') }}";
const PUSHER_AUTH_EP = "{{ url('/broadcasting/auth') }}";
const CSRF           = "{{ csrf_token() }}";
const CHAT_API       = "{{ url('/api/chat') }}";

// ── State (sejajar dengan React Native) ──────────────────────────────────────
let messages      = [];   // urutan: terbaru di index 0 (sama seperti RN inverted)
let hasMore       = false;
let page          = 1;
let isLoadingMore = false;

// ── Clock ─────────────────────────────────────────────────────────────────────
function updateClock(){
    const el = document.getElementById('statusTime');
    if (!el) return;
    const n = new Date();
    el.textContent = `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
}
updateClock(); setInterval(updateClock, 30000);

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
    el.textContent = fmtDate(ts);
    return el;
}

// ── Render list dari scratch (sama seperti RN setMessages + FlatList inverted)
// messages[] urutan terbaru di 0; kita render dari belakang ke depan supaya
// DOM urutan lama→baru dari atas ke bawah (scroll anchor di bawah)
// ─────────────────────────────────────────────────────────────────────────────
function renderAll(){
    const area = document.getElementById('chatMessages');

    // Hapus semua bubble & separator lama (tapi jaga loadMoreBtn & spinner)
    const loadMoreBtn     = document.getElementById('loadMoreBtn');
    const loadMoreSpinner = document.getElementById('loadMoreSpinner');

    // Hapus semua node kecuali btn & spinner & skeleton
    [...area.children].forEach(el => {
        if(el.id==='loadMoreBtn'||el.id==='loadMoreSpinner'||el.id==='msgSkeleton'||el.id==='emptyState') return;
        el.remove();
    });

    if(messages.length === 0){
        document.getElementById('emptyState').classList.add('visible');
        return;
    }
    document.getElementById('emptyState').classList.remove('visible');

    // messages[0] = paling baru; render terbalik (index tinggi ke rendah) = lama→baru di DOM
    const sorted = [...messages].reverse(); // lama di depan
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

// ── Prepend pesan lama ke DOM (load more) tanpa re-render semua ──────────────
function prependOlderMessages(olderMsgs){
    const area     = document.getElementById('chatMessages');
    const loadMoreBtn = document.getElementById('loadMoreBtn');

    // prevH untuk preserve scroll position
    const prevH   = area.scrollHeight;
    const prevTop = area.scrollTop;

    // olderMsgs urutan lama→baru; insert setelah loadMoreBtn
    let insertRef = loadMoreBtn.nextSibling;

    // Cari date separator paling atas yang sudah ada
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
            // Jangan duplikat separator dengan bubble pertama yang sudah ada
            if(!(isLast && dateStr === firstExistingDate)){
                area.insertBefore(buildDateSep(msg.created_at), insertRef);
            }
        }
        const wrap = document.createElement('div');
        wrap.innerHTML = buildBubble(msg);
        area.insertBefore(wrap.firstElementChild, insertRef);
    });

    // Preserve scroll position supaya tidak lompat ke atas
    requestAnimationFrame(()=>{
        area.scrollTop = prevTop + (area.scrollHeight - prevH);
    });

    updateLoadMoreUI();
}

// ── Update tombol load more ───────────────────────────────────────────────────
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

// ── Fetch chat (server-side pagination, sama persis RN) ───────────────────────
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

            // API mengembalikan lama→baru; balik seperti RN (.reverse())
            const reversed = [...data.data].reverse();

            if(replace){
                // setMessages(reversed) — re-render penuh
                messages = reversed;
                renderAll();
                scrollToBottom();
            } else {
                // Load more: append di belakang array (= pesan lama, tampil di atas)
                // reversed = urutan terbaru→lama; untuk prepend DOM kita perlu lama→baru
                const olderForDOM = [...data.data]; // lama→baru sudah
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

// ── Load more (dipanggil tombol & scroll ke atas) ─────────────────────────────
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
        // jeda 500ms sebelum boleh load lagi (sama dengan RN setTimeout 500)
        setTimeout(()=>{ isLoadingMore = false; }, 500);
    }
}

// ── Scroll listener: trigger load when near top (onEndReached inverted) ───────
function initScrollListener(){
    const area = document.getElementById('chatMessages');
    let ticking = false;
    area.addEventListener('scroll', ()=>{
        if(ticking) return;
        ticking = true;
        requestAnimationFrame(()=>{
            // onEndReachedThreshold:0.3 (RN) → kita pake 120px dari atas
            if(area.scrollTop < 120 && hasMore && !isLoadingMore){
                loadMore();
            }
            ticking = false;
        });
    });
}

// ── Send Message ──────────────────────────────────────────────────────────────
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

    // Prepend ke array (index 0 = terbaru, sesuai RN)
    messages = [tempMsg, ...messages];

    // Render bubble baru langsung (append ke DOM, sudah ada di messages)
    const area   = document.getElementById('chatMessages');
    const emptyState = document.getElementById('emptyState');
    emptyState.classList.remove('visible');

    // Cek apakah perlu date separator
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
                'X-CSRF-TOKEN': CSRF,
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
                // Normal: ganti temp dengan real (sama persis RN)
                messages = messages.map(m => m.id===tempId ? data.chat : m);
                const tempEl = document.querySelector(`[data-msgid="${tempId}"]`);
                if(tempEl){
                    const w2 = document.createElement('div');
                    w2.innerHTML = buildBubble(data.chat);
                    tempEl.replaceWith(w2.firstElementChild);
                }
            } else if(hasTempMsg && hasRealMsg){
                // Pusher lebih cepat: hapus temp saja (sama persis RN)
                messages = messages.filter(m => m.id !== tempId);
                document.querySelector(`[data-msgid="${tempId}"]`)?.remove();
            }
        } else {
            // Gagal: hapus temp (sama persis RN)
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

// ── Pusher Real-time (sama persis RN echoService) ────────────────────────────
(function initPusher(){
    if(!USER_ID || !PUSHER_KEY) return;

    const pusher  = new Pusher(PUSHER_KEY, {
        cluster:      PUSHER_CLUSTER,
        forceTLS:     true,
        authEndpoint: PUSHER_AUTH_EP,
        auth: { headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} }
    });

    const channel = pusher.subscribe(`private-chat.${USER_ID}`);

    channel.bind('pusher:subscription_succeeded', ()=>{
        document.getElementById('onlineDot').className =
            'absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-plum dot-online';
        document.getElementById('statusText').textContent = 'Online';
    });

    channel.bind('chat.new', (event)=>{
        const chat = event?.chat;
        if(!chat) return;

        const senderId   = ensureNum(chat.id_pengirim);
        const receiverId = ensureNum(chat.id_penerima);

        // Hanya terima pesan dari partner saat ini (sama persis RN)
        const isFromCurrentPartner =
            senderId   === ensureNum(ID_PENERIMA) &&
            receiverId === ensureNum(USER_ID);
        if(!isFromCurrentPartner) return;

        // Deduplication (sama persis RN)
        const exists = messages.some(m => m.id === chat.id);
        if(exists) return;
        if(document.querySelector(`[data-msgid="${chat.id}"]`)) return;

        // Prepend ke array (terbaru di index 0)
        messages = [chat, ...messages];

        // Append bubble ke DOM
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
            'absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-plum dot-offline';
        document.getElementById('statusText').textContent = 'Aktif';
    });
})();

// ── Init ──────────────────────────────────────────────────────────────────────
(async function init(){
    await fetchChat(1, true);
    initScrollListener();
})();
</script>
@include('partials.auth-guard')
</body>
</html>