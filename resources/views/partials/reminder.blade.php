{{-- resources/views/partials/reminder.blade.php --}}
{{-- Usage: @include('partials.reminder') di dalam <body> halaman home --}}

<style>
    #reminderOverlay {
        position: fixed; inset: 0; z-index: 99999;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none;
        transition: opacity 0.4s ease; background: transparent;
    }
    #reminderOverlay.visible { opacity: 1; pointer-events: all; }

    #reminderCard {
        position: absolute; inset: 0;
        background: linear-gradient(160deg, #f0ecff 0%, #e8e0ff 40%, #ddd5ff 100%);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 40px 32px 48px;
        transform: scale(0.92) translateY(24px); opacity: 0;
        transition: transform .55s cubic-bezier(0.34,1.26,0.64,1), opacity .4s ease;
    }
    #reminderOverlay.visible #reminderCard { transform: scale(1) translateY(0); opacity: 1; }

    .rem-dot { position: absolute; border-radius: 50%; pointer-events: none; }
    .rem-dot-1 { width:14px;height:14px;background:#FF4D6D;top:14%;right:18%;animation:remFloat 3s ease-in-out infinite; }
    .rem-dot-2 { width:14px;height:14px;background:#FF8C42;top:38%;left:12%;animation:remFloat 3.4s .7s ease-in-out infinite; }
    .rem-dot-3 { width:14px;height:14px;background:#6246EA;bottom:22%;right:10%;animation:remFloat 2.8s 1.3s ease-in-out infinite; }
    .rem-dashed { position:absolute;top:10%;right:12%;width:80px;height:80px;border:2.5px dashed #C4B5FD;border-radius:50%;animation:remSpin 10s linear infinite; }
    .rem-corner-tl { position:absolute;top:48px;left:24px;width:64px;height:64px;border:3px solid #C4B5FD;border-radius:18px;opacity:.45;transform:rotate(-12deg); }
    .rem-corner-br { position:absolute;bottom:100px;right:20px;width:44px;height:44px;border:3px solid #C4B5FD;border-radius:12px;opacity:.3;transform:rotate(18deg); }

    @keyframes remFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
    @keyframes remSpin  { to{transform:rotate(360deg)} }

    .rem-bell-wrap { position:relative;width:160px;height:160px;display:flex;align-items:center;justify-content:center;margin-bottom:8px; }
    .rem-bell-pulse { position:absolute;inset:0;border-radius:50%;background:rgba(108,63,197,.12);animation:remPulse 2.2s ease-in-out infinite; }
    .rem-bell-pulse-2 { position:absolute;inset:-14px;border-radius:50%;background:rgba(108,63,197,.06);animation:remPulse 2.2s .5s ease-in-out infinite; }
    @keyframes remPulse { 0%,100%{transform:scale(1);opacity:.7} 50%{transform:scale(1.12);opacity:0} }

    .rem-bell-svg {
        animation: remBellPop .6s cubic-bezier(0.34,1.56,0.64,1) forwards, remBellWiggle 2.4s .7s ease-in-out infinite;
        transform-origin: top center; opacity: 0; position: relative; z-index: 1;
    }
    @keyframes remBellPop { 0%{opacity:0;transform:scale(.5) translateY(16px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
    @keyframes remBellWiggle {
        0%,100%{transform:rotate(0deg)} 10%{transform:rotate(14deg)} 20%{transform:rotate(-12deg)}
        30%{transform:rotate(10deg)} 40%{transform:rotate(-7deg)} 50%{transform:rotate(4deg)} 60%{transform:rotate(0deg)}
    }

    .rem-eyebrow { font-family:'Nunito',sans-serif;font-size:11px;font-weight:800;letter-spacing:.15em;color:#8B46D3;text-transform:uppercase;margin-bottom:10px;text-align:center; }
    .rem-title   { font-family:'Nunito',sans-serif;font-size:28px;font-weight:900;color:#1A0A2E;text-align:center;line-height:1.2;margin-bottom:14px;word-break:break-word;max-width:300px; }
    .rem-desc    { font-family:'Nunito',sans-serif;font-size:15px;font-weight:600;color:#6C5E8A;text-align:center;line-height:1.6;margin-bottom:32px;max-width:280px; }
    .rem-desc strong { font-weight:800;color:#3D1D7A; }

    .rem-type-badge {
        display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:50px;
        font-family:'Nunito',sans-serif;font-size:12px;font-weight:800;letter-spacing:.05em;
        text-transform:uppercase;margin-bottom:18px;
    }
    .rem-type-badge.today   { background:#EDE9FB;color:#6C3FC5; }
    .rem-type-badge.at_time { background:#FEF3C7;color:#B45309; }
    .rem-type-badge.missed  { background:#FEE2E2;color:#B91C1C; }

    .rem-btn-main {
        display:flex;align-items:center;justify-content:center;gap:8px;
        width:100%;max-width:320px;padding:18px 24px;
        background:linear-gradient(135deg,#6C2BD9,#9B46D3);color:#fff;
        font-family:'Nunito',sans-serif;font-size:16px;font-weight:800;
        border:none;border-radius:50px;cursor:pointer;
        box-shadow:0 12px 32px rgba(108,43,217,.38);
        transition:transform .15s,box-shadow .15s;margin-bottom:16px;text-decoration:none;
    }
    .rem-btn-main:active { transform:scale(.97);box-shadow:0 4px 16px rgba(108,43,217,.3); }

    .rem-auto-badge { display:flex;align-items:center;gap:8px;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;color:#8B7AAA;letter-spacing:.04em; }
    .rem-auto-dot   { width:10px;height:10px;border-radius:50%;background:#22C55E;animation:remDotBlink 1s ease-in-out infinite;flex-shrink:0; }
    @keyframes remDotBlink { 0%,100%{opacity:1} 50%{opacity:.35} }
</style>

<div id="reminderOverlay">
    <div id="reminderCard">
        <div class="rem-dashed"></div>
        <div class="rem-dot rem-dot-1"></div>
        <div class="rem-dot rem-dot-2"></div>
        <div class="rem-dot rem-dot-3"></div>
        <div class="rem-corner-tl"></div>
        <div class="rem-corner-br"></div>

        <div class="rem-bell-wrap">
            <div class="rem-bell-pulse-2"></div>
            <div class="rem-bell-pulse"></div>
            <svg class="rem-bell-svg" width="110" height="120" viewBox="0 0 130 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M65 18C65 18 40 28 40 70V98L28 108H102L90 98V70C90 28 65 18 65 18Z" fill="#F9C03B"/>
                <path d="M55 30C52 38 50 50 50 64" stroke="#FDE68A" stroke-width="5" stroke-linecap="round" opacity="0.7"/>
                <rect x="57" y="14" width="16" height="10" rx="5" fill="#E8A82A"/>
                <rect x="26" y="104" width="78" height="10" rx="5" fill="#E8A82A"/>
                <circle cx="65" cy="125" r="10" fill="#FF4D6D"/>
                <path d="M36 72 C30 62 30 50 36 40" stroke="#F9C03B" stroke-width="4" stroke-linecap="round" opacity="0.45" fill="none"/>
                <path d="M94 72 C100 62 100 50 94 40" stroke="#F9C03B" stroke-width="4" stroke-linecap="round" opacity="0.45" fill="none"/>
            </svg>
        </div>

        <div class="rem-type-badge today" id="remTypeBadge">Reminder Today</div>
        <p class="rem-eyebrow">TODAY'S EVENT</p>
        <h1 class="rem-title" id="remTitle">Loading...</h1>
        <p class="rem-desc" id="remDesc">Preparing your reminder...</p>

        <a href="/" class="rem-btn-main" id="remBrowseBtn">Browse Home →</a>

        <div class="rem-auto-badge">
            <div class="rem-auto-dot"></div>
            <span id="remCountdownText">AUTO REDIRECT IN 5s</span>
        </div>
    </div>
</div>

<script>
(function () {
    const API_BASE  = 'https://api.alpha-kidz.com/api';
    @php
        $resolvedUserId = session('user_id') ?: data_get(session('user'), 'id_user');
    @endphp
    const USER_ID   = @json($resolvedUserId);
    const API_TOKEN = '{{ session("token") }}';

    let reminders       = [];
    let isShowing       = false;
    let countdownHandle = null;

    /* ─── LOAD ─────────────────────────────────────────────── */
    async function loadReminders() {
        if (!USER_ID || !API_TOKEN) return;
        try {
            const res  = await fetch(`${API_BASE}/reminders/${USER_ID}`, {
                headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const fresh = (data.data || []).filter(r => r.is_active !== false);

            // Merge: pertahankan flag lokal yang sudah di-set true (notified)
            // agar reload 10 menit tidak menghapus status "sudah ditampilkan"
            fresh.forEach(f => {
                const old = reminders.find(e => e.id === f.id);
                f._today   = old ? (old._today   || !!f.notified_today)   : !!f.notified_today;
                f._at_time = old ? (old._at_time || !!f.notified_at_time) : !!f.notified_at_time;
                f._missed  = old ? (old._missed  || !!f.notified_missed)  : !!f.notified_missed;

                // Parse repeat_days — bisa berupa array atau JSON string
                if (typeof f.repeat_days === 'string') {
                    try { f.repeat_days = JSON.parse(f.repeat_days); } catch { f.repeat_days = []; }
                }
                if (!Array.isArray(f.repeat_days)) f.repeat_days = [];
            });
            reminders = fresh;
        } catch (e) {
            console.warn('[Reminder] Load failed:', e);
        }
    }

    /* ─── MARK API ──────────────────────────────────────────── */
    async function markReminder(id, type) {
        try {
            await fetch(`${API_BASE}/reminders/${id}/mark`, {
                method: 'PATCH',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ type })
            });
        } catch (e) {
            console.warn('[Reminder] Mark failed:', e);
        }
    }

    /* ─── CEK AKTIF HARI INI ────────────────────────────────── */
    // Untuk repeat_days: 0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat
    // Contoh data JSON: repeat_days:[0,1,2,3,4,5,6] → aktif setiap hari
    function isActiveToday(r, todayStr, todayDow) {
        if (r.is_repeat_weekly) {
            return r.repeat_days.includes(todayDow);
        }
        return (r.date || '').split('T')[0] === todayStr;
    }

    /* ─── TICK (tiap 1 menit) ───────────────────────────────── */
    function tick() {
        if (isShowing || reminders.length === 0) return;

        const now      = new Date();
        const todayStr = toDateStr(now);
        const todayDow = now.getDay();                    // 0=Sun .. 6=Sat
        const nowMin   = now.getHours() * 60 + now.getMinutes();

        for (const r of reminders) {
            if (!isActiveToday(r, todayStr, todayDow)) continue;

            const parts = (r.time || '00:00:00').split(':');
            const rMin  = parseInt(parts[0]) * 60 + parseInt(parts[1]);
            const diff  = nowMin - rMin;   // negatif = belum sampai; positif = sudah lewat

            /* 1. AT_TIME — tepat pada menit alarm (toleransi ±1 menit) */
            if (diff >= 0 && diff < 1 && !r._at_time) {
                r._at_time = true;
                showReminder(r, 'at_time');
                return;
            }

            /* 2. TODAY — alarm masih di depan, belum pernah dinotif today */
            if (diff < 0 && !r._today) {
                r._today = true;
                showReminder(r, 'today');
                return;
            }

            /* 3. MISSED — 30–90 menit lewat, belum at_time & missed */
            if (diff >= 30 && diff < 90 && !r._at_time && !r._missed) {
                r._at_time = true;
                r._missed  = true;
                showReminder(r, 'missed');
                return;
            }
        }
    }

    /* ─── TAMPILKAN POPUP ───────────────────────────────────── */
    function showReminder(r, type) {
        if (isShowing) return;
        isShowing = true;

        const label = r.label || 'Reminder';

        const badgeMap = {
            today:   { text: 'Reminder Today', cls: 'today'   },
            at_time: { text: "It's Time!",      cls: 'at_time' },
            missed:  { text: 'Missed Reminder', cls: 'missed'  },
        };
        const b = badgeMap[type] || badgeMap.today;
        const badge = document.getElementById('remTypeBadge');
        badge.textContent = b.text;
        badge.className   = 'rem-type-badge ' + b.cls;

        document.getElementById('remTitle').textContent = label + '!';
        document.getElementById('remDesc').innerHTML    =
            'Now is your time for <strong>' + escHtml(label) + '</strong>';

        document.getElementById('reminderOverlay').classList.add('visible');

        markReminder(r.id, type);

        // Countdown 5s
        let sec = 5;
        setCountdown(sec);
        countdownHandle = setInterval(() => {
            sec--;
            if (sec <= 0) { clearInterval(countdownHandle); closeReminder(true); }
            else           { setCountdown(sec); }
        }, 1000);

        document.getElementById('remBrowseBtn').onclick = function (e) {
            e.preventDefault();
            clearInterval(countdownHandle);
            closeReminder(true);
        };
    }

    function setCountdown(n) {
        document.getElementById('remCountdownText').textContent = 'AUTO REDIRECT IN ' + n + 's';
    }

    function closeReminder(redirect) {
        document.getElementById('reminderOverlay').classList.remove('visible');
        isShowing = false;
        if (redirect) setTimeout(() => { window.location.href = '/'; }, 350);
    }

    /* ─── HELPERS ───────────────────────────────────────────── */
    function toDateStr(d) {
        return d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
    }
    function escHtml(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ─── INIT ──────────────────────────────────────────────── */
    async function init() {
        await loadReminders();
        tick();                                      // cek langsung saat halaman dibuka
        setInterval(tick, 60 * 1000);               // polling tiap 1 menit
        setInterval(loadReminders, 10 * 60 * 1000); // refresh data tiap 10 menit
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>