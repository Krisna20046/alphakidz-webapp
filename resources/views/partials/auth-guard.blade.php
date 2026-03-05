{{-- resources/views/partials/auth-guard.blade.php --}}
{{-- 
    Include di SEMUA halaman protected:
    @include('partials.auth-guard')
    
    Letakkan SEBELUM closing </body> tag, setelah semua konten.
    
    Cara kerja:
    1. Ambil token dari session (via Blade — tidak expose ke JS global selain yang sudah ada)
    2. Panggil GET /user/detail ke API:
         - 401/403  → token dicabut / login dari perangkat lain → redirect login
         - is_filled = 0 → redirect halaman profil (kecuali halaman whitelist)
    3. Polling setiap 60 detik untuk deteksi sesi berakhir di background
    4. visibilitychange → re-check segera saat tab aktif kembali
--}}

<script>
(function AuthGuard() {
    'use strict';

    /* ── Config ─────────────────────────────────────────────────────────── */
    const TOKEN        = @json(session('token'));
    const API_BASE     = @json(rtrim(env('API_BASE_URL', ''), '/'));
    const LOGIN_URL    = @json(route('login'));
    const PROFILE_URL  = @json(route('profil.detail'));
    const CURRENT_PATH = window.location.pathname;
    const CSRF         = @json(csrf_token());

    /* Halaman yang boleh diakses meski profil belum lengkap */
    const PROFILE_WHITELIST = [
        '/logout',
    ];

    const POLL_MS = 60_000;   // polling interval
    let pollTimer   = null;
    let isChecking  = false;
    let failCount   = 0;      // consecutive network failures
    const MAX_FAILS = 3;      // setelah 3x gagal network, baru anggap offline

    /* ── Helpers ─────────────────────────────────────────────────────────── */
    function isWhitelisted() {
        return PROFILE_WHITELIST.some(p => CURRENT_PATH.startsWith(p));
    }

    /**
     * Kirim POST /logout ke server (best-effort, pakai sendBeacon)
     * agar session Laravel ikut dihapus, lalu redirect ke login.
     */
    function forceLogout(reason) {
        stopPolling();

        /* Tampilkan overlay "Sesi berakhir" sebelum redirect */
        showSessionExpiredOverlay(reason);

        /* Hapus session server-side */
        try {
            const fd = new FormData();
            fd.append('_token', CSRF);
            navigator.sendBeacon('/logout', fd);
        } catch (_) {}

        /* Simpan pesan untuk halaman login */
        try { sessionStorage.setItem('auth_flash', reason); } catch (_) {}

        setTimeout(() => window.location.replace(LOGIN_URL), 2200);
    }

    function goProfile() {
        stopPolling();
        try { sessionStorage.setItem('profile_flash', 'Lengkapi profil Anda terlebih dahulu.'); } catch (_) {}
        window.location.replace(PROFILE_URL);
    }

    /* ── Overlay "Sesi Berakhir" ─────────────────────────────────────────── */
    function showSessionExpiredOverlay(msg) {
        // Cegah duplikat
        if (document.getElementById('__authOverlay')) return;

        const el = document.createElement('div');
        el.id = '__authOverlay';
        el.innerHTML = `
            <div style="
                position:fixed;inset:0;z-index:99999;
                background:rgba(74,14,53,0.92);
                backdrop-filter:blur(6px);
                display:flex;flex-direction:column;
                align-items:center;justify-content:center;
                font-family:'Plus Jakarta Sans',sans-serif;
                animation:__fadeIn .3s ease;
            ">
                <style>
                    @keyframes __fadeIn{from{opacity:0}to{opacity:1}}
                    @keyframes __spin{to{transform:rotate(360deg)}}
                </style>
                <div style="
                    width:72px;height:72px;border-radius:50%;
                    background:rgba(255,255,255,.12);
                    display:flex;align-items:center;justify-content:center;
                    margin-bottom:20px;
                ">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </div>
                <p style="color:#fff;font-size:18px;font-weight:700;margin:0 0 8px;text-align:center;padding:0 32px;">
                    Sesi Berakhir
                </p>
                <p style="color:rgba(255,255,255,.65);font-size:13px;text-align:center;margin:0 0 24px;padding:0 40px;line-height:1.6;">
                    ${msg}
                </p>
                <div style="
                    width:32px;height:32px;border:3px solid rgba(255,255,255,.2);
                    border-top-color:#fff;border-radius:50%;
                    animation:__spin .8s linear infinite;
                "></div>
                <p style="color:rgba(255,255,255,.4);font-size:11px;margin-top:16px;">
                    Mengalihkan ke halaman login...
                </p>
            </div>
        `;
        document.body.appendChild(el);
    }

    /* ── Core Auth Check ─────────────────────────────────────────────────── */
    async function checkAuth() {
        if (isChecking) return;
        isChecking = true;

        try {
            const res = await fetch(`${API_BASE}/user-detail`, {
                method : 'GET',
                headers: {
                    'Accept'       : 'application/json',
                    'Authorization': `Bearer ${TOKEN}`,
                },
                cache: 'no-store',
            });

            /* ── Token tidak valid / dicabut ── */
            if (res.status === 401 || res.status === 403) {
                forceLogout('Akun Anda masuk dari perangkat lain atau sesi telah berakhir.');
                return;
            }

            /* ── Server error sementara — jangan redirect ── */
            if (!res.ok) {
                console.warn('[AuthGuard] Server responded:', res.status);
                failCount++;
                return;
            }

            const json = await res.json();
            failCount = 0; // reset pada sukses

            if (json.status !== 'success') {
                forceLogout('Sesi tidak valid. Silakan login kembali.');
                return;
            }

            /* ── Profile guard ── */
            const user     = json.data ?? {};
            const isFilled = Number(user.is_filled ?? 0);

            if (isFilled === 0 && !isWhitelisted()) {
                goProfile();
                return;
            }

            /* ── Opsional: update elemen nama di DOM ── */
            const nameEl = document.getElementById('__guardUserName');
            if (nameEl && user.name) nameEl.textContent = user.name;

        } catch (err) {
            /* Network error (offline) */
            failCount++;
            console.warn(`[AuthGuard] Network error (${failCount}/${MAX_FAILS}):`, err.message);

            if (failCount >= MAX_FAILS) {
                /* Kemungkinan besar offline — jangan redirect, tapi beri tahu user */
                console.warn('[AuthGuard] Repeated failures — user may be offline.');
            }
        } finally {
            isChecking = false;
        }
    }

    /* ── Polling ─────────────────────────────────────────────────────────── */
    function startPolling() {
        stopPolling();
        pollTimer = setInterval(checkAuth, POLL_MS);
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    /* ── Visibility API ──────────────────────────────────────────────────── */
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            failCount = 0;   // reset setelah tab kembali aktif
            checkAuth();
            startPolling();
        } else {
            stopPolling();   // hemat request saat tab background
        }
    });

    /* ── Bootstrap ───────────────────────────────────────────────────────── */
    if (!TOKEN) {
        forceLogout('Silakan login untuk melanjutkan.');
        return;
    }

    checkAuth();
    startPolling();

})();
</script>