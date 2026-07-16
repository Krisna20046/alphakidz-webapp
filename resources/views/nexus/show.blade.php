@php
    $roleId = session('user')['id_role'] ?? null;
    $userId = session('user')['id'] ?? null;
@endphp

@extends('layouts.app')

@php $activeNav = 'home'; $hideBottomNav = true @endphp

@section('title', 'Pertanyaan')

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

    /* Info card (Nexus question context) */
    .q-info {
        background: white;
        border-radius:14px; padding:14px 16px;
        margin:0 0 12px;
        border:1.5px solid #D7BFF1;
        box-shadow:0 1px 4px rgba(0,0,0,.06);
    }
    .q-info .q-title {
        font-size:15px; font-weight:900; color:#1E1B2E;
        margin-bottom:6px; line-height:1.3;
    }
    .q-info .q-meta {
        display:flex; flex-wrap:wrap; align-items:center; gap:6px; font-size:12px;
    }
    .q-info .q-nexus-info {
        margin-top:8px; padding-top:8px; border-top:1px solid #EDE9FE;
        font-size:12px; color:#6B6589;
    }

    .badge-open { background: #FFF3E0; color: #E65100; }
    .badge-claimed { background: #E8F5E9; color: #2E7D32; }
    .badge-answered { background: #E3F2FD; color: #1565C0; }
    .badge-closed { background: #F3E5F5; color: #7B1FA2; }

    /* Input bar */
    #inputBar {
        background: linear-gradient(to bottom, #F3F0FC 0%, #F3F0FC 100%);
        border-top:1px solid #D7BFF1;
        padding:12px 16px;
        padding-bottom:max(30px, env(safe-area-inset-bottom));
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

    /* Close button in header */
    .header-close-btn {
        background:rgba(255,255,255,.2); border:1.5px solid rgba(255,255,255,.35);
        color:white; border-radius:10px; padding:4px 14px;
        font-size:11px; font-weight:700; cursor:pointer;
        transition:background .15s;
    }
    .header-close-btn:active { background:rgba(255,255,255,.35); }

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

    /* Waiting Nexus state (before claimed) */
    #waitingState {
        flex:1; display:none; flex-direction:column;
        align-items:center; justify-content:center;
        padding:40px 20px; gap:12px;
    }
    #waitingState.visible { display:flex; }

    /* Skeleton */
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    .skeleton { animation:pulse 1.5s ease-in-out infinite; }

    /* Closed banner */
    .closed-banner {
        text-align:center; padding:14px; margin:8px 0;
        background:#F3E5F5; border-radius:12px;
        font-size:13px; font-weight:700; color:#7B1FA2;
    }

    #phoneFrame {
        height: 100vh;
        height: 100dvh;
        min-height: unset;
    }
</style>
@endpush

@section('content')
<!-- HEADER -->
<div class="flex gap-2 items-center anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <a href="{{ route('nexus.nexus-index') }}"
       class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
        <ion-icon name="arrow-back" style="font-size:18px;color:white;"></ion-icon>
    </a>

    <!-- Avatar initial -->
    <div class="relative shrink-0 ml-2">
        <div class="w-12 h-12 rounded-full bg-[#F3F0FD] flex items-center justify-center text-[#8B46D3] font-extrabold text-base border-2 border-white/80">
            N
        </div>
    </div>

    <!-- Title & status -->
    <div class="flex-1 min-w-0">
        <p id="qTitle" class="text-white font-semibold text-[20px] leading-none truncate">Memuat...</p>
        <div class="flex items-center gap-2 mt-0.5">
            <span id="qStatus" class="text-white/80 text-[14px] leading-none font-semibold"></span>
            <button id="btnClose" class="header-close-btn" style="display:none;" onclick="closeQuestion()">Tutup</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<!-- MESSAGES AREA -->
<div id="chatMessages" class="rounded-t-[34px] -mt-[50px] relative z-20 flex-1 overflow-y-auto">

    <!-- Skeleton -->
    <div id="msgSkeleton" class="flex flex-col gap-3 py-4 skeleton">
        @for($i=0;$i<4;$i++)
        <div class="flex {{ $i%2==0 ? '' : 'flex-row-reverse' }} items-end gap-2">
            <div class="w-8 h-8 rounded-full bg-[#EDE9FE] shrink-0"></div>
            <div class="h-10 bg-[#EDE9FE] rounded-2xl {{ $i%2==0 ? 'rounded-bl-sm' : 'rounded-br-sm' }}"
                 style="width:{{ 120+($i*30) }}px;"></div>
        </div>
        @endfor
    </div>

    <!-- Waiting state (before claimed) -->
    <div id="waitingState">
        <div class="empty-icon-circle">
            <ion-icon name="time-outline" style="font-size:52px;color:#8B46D3;"></ion-icon>
        </div>
        <p class="text-[#030712] font-extrabold text-[16px]">Menunggu Nexus</p>
        <p class="text-[#111827] text-[13px] text-center leading-[1.35]">
            Pertanyaanmu akan segera dijawab<br>oleh tim Nexus
        </p>
    </div>

    <!-- Empty state (no messages yet, but claimed) -->
    <div id="emptyState">
        <div class="empty-icon-circle">
            <ion-icon name="chatbox-ellipses" style="font-size:52px;color:#8B46D3;"></ion-icon>
        </div>
        <p class="text-[#030712] font-extrabold text-[16px]">Belum Ada Pesan</p>
        <p class="text-[#111827] text-[13px] text-center leading-[1.35]">
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
        <ion-icon name="send" style="font-size:20px;color:#8B46D3;margin-left:2px;"></ion-icon>
    </button>
</div>
@endsection

@push('scripts')
<script>
const CSRF = "{{ csrf_token() }}";
const API_BASE = '{{ rtrim(config("services.api.base_url", env("API_BASE_URL", "")), "/") }}';
const USER_ID = {{ $userId ?? 0 }};
const QUESTION_ID = {{ $id ?? 0 }};
const ROLE_ID = {{ $roleId ?? 0 }};

// ── State ──
let questionData = null;
let messages = [];

// ── Helpers ──
const ensureNum = v => typeof v === 'number' ? v : Number(v);

function fmtTime(ts) { return new Date(ts).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}); }
function fmtDate(ts) { return new Date(ts).toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long'}); }
function escHtml(s) { const d=document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }

function statusBadge(s) {
    const map = { open:'badge-open', claimed:'badge-claimed', answered:'badge-answered', closed:'badge-closed' };
    const lbl = { open:'Open', claimed:'Diklaim', answered:'Terjawab', closed:'Selesai' };
    return `<span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-[.5px] ${map[s]||'badge-open'}">${lbl[s]||s}</span>`;
}

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

// ── Build bubble HTML (matching chat/room.blade.php) ──
function buildBubble(msg){
    const isSent = ensureNum(msg.id_pengirim) === ensureNum(USER_ID);
    const senderName = msg.pengirim?.name || 'User';
    return `
    <div class="bubble-wrap ${isSent?'sent':''}" data-msgid="${msg.id}">
        ${!isSent ? `<div class="chat-avatar-ph">${escHtml(senderName.charAt(0).toUpperCase())}</div>` : ''}
        <div class="bubble ${isSent?'sent':'recv'}${msg._temp?' sending':''}">
            <span class="text-sm font-medium">${escHtml(msg.pesan)}</span>
            <span class="bubble-time">${fmtTime(msg.created_at)}${isSent&&!msg._temp?' ✓':''}</span>
        </div>
    </div>`;
}

function buildDateSep(ts){
    const el = document.createElement('div');
    el.className   = 'date-sep';
    const dateLabel = fmtDate(ts);
    const todayLabel = new Date(ts).toDateString() === new Date().toDateString() ? 'TODAY' : dateLabel.toUpperCase();
    el.innerHTML = `<span>${escHtml(todayLabel)}</span>`;
    return el;
}

// ─── Render ───
function renderMessages(){
    const area = document.getElementById('chatMessages');

    // Remove existing messages (keep skeleton, empty, waiting)
    [...area.children].forEach(el => {
        if(el.id==='msgSkeleton'||el.id==='emptyState'||el.id==='waitingState') return;
        el.remove();
    });

    if(messages.length === 0 && questionData?.claimed_by) {
        document.getElementById('emptyState').classList.add('visible');
        document.getElementById('waitingState').classList.remove('visible');
    } else if(messages.length === 0 && !questionData?.claimed_by) {
        document.getElementById('waitingState').classList.add('visible');
        document.getElementById('emptyState').classList.remove('visible');
    } else {
        document.getElementById('emptyState').classList.remove('visible');
        document.getElementById('waitingState').classList.remove('visible');
    }

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
}

// ─── Load Question Detail ───
async function loadDetail() {
    try {
        const res = await fetch(`${API_BASE}/nexus/${QUESTION_ID}`, {
            headers: { 'Authorization': 'Bearer {{ session("token") }}', 'Accept': 'application/json' }
        });
        if (!res.ok) { showError(); return; }
        const json = await res.json();
        questionData = json.data;
        renderDetail(json.data);
    } catch(e) { showError(); }
}

function showError() {
    document.getElementById('msgSkeleton')?.remove();
    showToast('Gagal memuat percakapan');
    document.getElementById('waitingState').classList.remove('visible');
    document.getElementById('emptyState').classList.add('visible');
    document.getElementById('emptyState').querySelector('p:first-of-type').textContent = 'Gagal Memuat';
    document.getElementById('emptyState').querySelector('p:last-of-type').textContent = 'Coba refresh halaman';
}

function renderDetail(q) {
    document.getElementById('msgSkeleton')?.remove();

    // Header
    document.getElementById('qTitle').textContent = q.judul;
    document.getElementById('qStatus').innerHTML = statusBadge(q.status) + ' · oleh ' + (q.asked_by?.name || '-');

    // Close button
    const isClaimer = q.claimed_by?.id === USER_ID;
    const isAdmin = ROLE_ID === 1;
    if ((isClaimer || isAdmin) && q.status !== 'closed') {
        document.getElementById('btnClose').style.display = 'inline-block';
    }

    // Messages
    if (q.messages && q.messages.length > 0) {
        messages = q.messages.map(m => ({
            ...m,
            id_pengirim: ensureNum(m.id_pengirim),
            id_penerima: ensureNum(m.id_penerima),
        }));
    } else {
        messages = [];
    }

    renderMessages();
    if (messages.length > 0) scrollToBottom();
}

// ─── Send Message ───
async function sendMessage() {
    const input = document.getElementById('msgInput');
    const text  = input.value.trim();
    if (!text || !questionData?.claimed_by) return;

    input.value = ''; input.style.height = 'auto'; toggleSendBtn();

    const tempId  = `temp-${Date.now()}`;
    const tempMsg = {
        id: tempId,
        id_pengirim: USER_ID,
        pesan: text,
        created_at: new Date().toISOString(),
        _temp: true,
    };

    messages = [tempMsg, ...messages];

    const area = document.getElementById('chatMessages');
    document.getElementById('emptyState').classList.remove('visible');
    document.getElementById('waitingState').classList.remove('visible');

    const lastSep  = [...area.querySelectorAll('.date-sep')].pop();
    const todayStr = fmtDate(tempMsg.created_at);
    if (!lastSep || lastSep.dataset?.sep !== todayStr) {
        area.appendChild(buildDateSep(tempMsg.created_at));
    }
    const wrap = document.createElement('div');
    wrap.innerHTML = buildBubble(tempMsg);
    area.appendChild(wrap.firstElementChild);
    scrollToBottom(true);

    try {
        const res = await fetch(`${API_BASE}/nexus/${QUESTION_ID}/chat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer {{ session("token") }}',
            },
            body: JSON.stringify({ pesan: text })
        });
        const data = await res.json();

        if (res.ok && data.data) {
            const realMsg = data.data;
            realMsg.id_pengirim = ensureNum(realMsg.id_pengirim);
            realMsg.id_penerima = ensureNum(realMsg.id_penerima);

            const hasTemp = messages.some(m => m.id === tempId);
            const hasReal = messages.some(m => m.id === realMsg.id);

            if (hasTemp && !hasReal) {
                messages = messages.map(m => m.id === tempId ? realMsg : m);
                const tempEl = document.querySelector(`[data-msgid="${tempId}"]`);
                if (tempEl) {
                    const w2 = document.createElement('div');
                    w2.innerHTML = buildBubble(realMsg);
                    tempEl.replaceWith(w2.firstElementChild);
                }
            } else if (hasTemp && hasReal) {
                messages = messages.filter(m => m.id !== tempId);
                document.querySelector(`[data-msgid="${tempId}"]`)?.remove();
            }
        } else {
            messages = messages.filter(m => m.id !== tempId);
            document.querySelector(`[data-msgid="${tempId}"]`)?.remove();
            showToast(data.message || 'Pesan gagal dikirim.');
            if (messages.length === 0) renderMessages();
        }
    } catch(e) {
        messages = messages.filter(m => m.id !== tempId);
        document.querySelector(`[data-msgid="${tempId}"]`)?.remove();
        showToast('Terjadi kesalahan. Coba lagi.');
        if (messages.length === 0) renderMessages();
    }
}

// ─── Close Question ───
async function closeQuestion() {
    if (!confirm('Tutup pertanyaan ini?')) return;
    try {
        const res = await fetch(`${API_BASE}/nexus/${QUESTION_ID}/close`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer {{ session("token") }}',
                'Accept': 'application/json',
            }
        });
        const json = await res.json();
        if (!res.ok) { showToast(json.message || 'Gagal'); return; }
        loadDetail();
    } catch(e) { showToast('Gagal menutup pertanyaan'); }
}

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

// ─── Init ───
loadDetail();
</script>
@endpush