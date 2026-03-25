{{-- resources/views/partials/permission-modals.blade.php --}}
{{-- Include di dalam <body> di halaman dashboard: @include('partials.permission-modals') --}}

<style>
    /* ── Overlay ── */
    #permOverlay {
        position: fixed;
        inset: 0;
        background: rgba(20, 8, 40, 0.55);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        z-index: 99990;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease;
    }
    #permOverlay.visible {
        opacity: 1;
        pointer-events: all;
    }

    /* ── Bottom Sheet ── */
    #permSheet {
        width: 100%;
        max-width: 480px;
        background: #fff;
        border-radius: 32px 32px 0 0;
        padding: 0 0 40px;
        transform: translateY(100%);
        transition: transform 0.45s cubic-bezier(0.34, 1.26, 0.64, 1);
        position: relative;
        overflow: hidden;
    }
    #permSheet.up {
        transform: translateY(0);
    }

    /* ── Drag Handle ── */
    .perm-handle {
        width: 40px;
        height: 4px;
        background: #E0D8F0;
        border-radius: 99px;
        margin: 14px auto 0;
    }

    /* ── Illustration area ── */
    .perm-illus-wrap {
        position: relative;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 8px 0 0;
    }

    /* Floating dots */
    .perm-dot {
        position: absolute;
        border-radius: 50%;
        animation: permFloat 3s ease-in-out infinite;
    }
    .perm-dot.red   { width:14px;height:14px;background:#FF4D6D;top:32px;right:72px;animation-delay:0s; }
    .perm-dot.orange{ width:14px;height:14px;background:#FF8C42;bottom:56px;left:56px;animation-delay:.7s; }
    .perm-dot.blue  { width:14px;height:14px;background:#6246EA;bottom:36px;right:44px;animation-delay:1.3s; }

    /* Dashed circle spinner */
    .perm-dashed {
        position: absolute;
        top: 20px;
        right: 40px;
        width: 56px;
        height: 56px;
        border: 2.5px dashed #D0C8E8;
        border-radius: 50%;
        animation: permSpin 8s linear infinite;
    }

    @keyframes permFloat {
        0%,100% { transform: translateY(0);   }
        50%      { transform: translateY(-8px); }
    }
    @keyframes permSpin {
        to { transform: rotate(360deg); }
    }

    /* ── Main SVG icon: pop + float ── */
    .perm-icon-main {
        animation: permPopIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards,
                   permIconFloat 3.5s 0.5s ease-in-out infinite;
        transform-origin: center bottom;
    }
    @keyframes permPopIn {
        0%   { opacity:0; transform: scale(0.6) translateY(20px); }
        100% { opacity:1; transform: scale(1)   translateY(0); }
    }
    @keyframes permIconFloat {
        0%,100% { transform: translateY(0);   }
        50%      { transform: translateY(-8px); }
    }

    /* ── Text ── */
    .perm-title {
        font-family: 'Nunito', sans-serif;
        font-size: 26px;
        font-weight: 900;
        color: #1A0A2E;
        text-align: center;
        margin: 0 24px 10px;
        line-height: 1.2;
    }
    .perm-desc {
        font-family: 'Nunito', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #7A6E9B;
        text-align: center;
        margin: 0 48px 32px;
        line-height: 1.6;
    }

    /* ── Buttons ── */
    .perm-btn-allow {
        display: block;
        width: calc(100% - 48px);
        margin: 0 24px 14px;
        padding: 18px;
        background: linear-gradient(135deg, #6C2BD9, #9B46D3);
        color: #fff;
        font-family: 'Nunito', sans-serif;
        font-size: 16px;
        font-weight: 800;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 0 10px 28px rgba(108,43,217,0.38);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .perm-btn-allow:active { transform: scale(0.97); box-shadow: 0 4px 14px rgba(108,43,217,0.3); }

    .perm-btn-later {
        display: block;
        width: 100%;
        padding: 10px;
        background: none;
        border: none;
        font-family: 'Nunito', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #A09BB8;
        cursor: pointer;
        text-align: center;
        letter-spacing: 0.01em;
    }
    .perm-btn-later:hover { color: #6C2BD9; }

    /* ── Slide transition between steps ── */
    .perm-content {
        display: none;
    }
    .perm-content.active {
        display: block;
    }
    .perm-content.slide-out-left {
        animation: slideOutLeft 0.3s ease forwards;
    }
    .perm-content.slide-in-right {
        animation: slideInRight 0.35s cubic-bezier(0.34,1.1,0.64,1) forwards;
    }
    @keyframes slideOutLeft {
        to { opacity:0; transform: translateX(-40px); }
    }
    @keyframes slideInRight {
        from { opacity:0; transform: translateX(40px); }
        to   { opacity:1; transform: translateX(0); }
    }

    /* Bell wiggle */
    @keyframes bellWiggle {
        0%,100% { transform: rotate(0deg); }
        15%      { transform: rotate(14deg); }
        30%      { transform: rotate(-12deg); }
        45%      { transform: rotate(10deg); }
        60%      { transform: rotate(-8deg); }
        75%      { transform: rotate(4deg); }
    }
    .bell-anim { animation: permPopIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards, bellWiggle 2.5s 0.6s ease-in-out infinite; transform-origin: top center; }

    /* Pin bounce */
    @keyframes pinBounce {
        0%,100% { transform: translateY(0); }
        40%      { transform: translateY(-12px); }
        60%      { transform: translateY(-6px); }
    }
    .pin-anim { animation: permPopIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards, pinBounce 2.2s 0.5s ease-in-out infinite; transform-origin: center bottom; }

    /* Shutter blink */
    @keyframes shutterBlink {
        0%,90%,100% { transform: scale(1); }
        95%         { transform: scale(0.88); }
    }
    .cam-anim { animation: permPopIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards, shutterBlink 2.8s 0.5s ease-in-out infinite; }
</style>

<!-- ═══════════════════════════════════════════════════════ OVERLAY + SHEET -->
<div id="permOverlay">
  <div id="permSheet">
    <div class="perm-handle"></div>

    <!-- ── STEP 1: Notification ──────────────────────────────────────────── -->
    <div class="perm-content active" id="permStep-notification">
      <div class="perm-illus-wrap">
        <!-- Dashed spinner -->
        <div class="perm-dashed"></div>
        <!-- Floating dots -->
        <div class="perm-dot red"></div>
        <div class="perm-dot orange"></div>
        <div class="perm-dot blue"></div>
        <!-- Bell SVG -->
        <svg class="bell-anim" width="130" height="140" viewBox="0 0 130 140" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Bell body -->
          <path d="M65 18C65 18 40 28 40 70V98L28 108H102L90 98V70C90 28 65 18 65 18Z" fill="#F9C03B"/>
          <!-- Bell shine -->
          <path d="M55 30C52 38 50 50 50 64" stroke="#FDE68A" stroke-width="5" stroke-linecap="round" opacity="0.7"/>
          <!-- Clapper mount -->
          <rect x="57" y="14" width="16" height="10" rx="5" fill="#E8A82A"/>
          <!-- Bell rim -->
          <rect x="26" y="104" width="78" height="10" rx="5" fill="#E8A82A"/>
          <!-- Clapper dot red -->
          <circle cx="65" cy="125" r="10" fill="#FF4D6D"/>
          <!-- Vibration arcs -->
          <path d="M36 72 C30 62 30 50 36 40" stroke="#F9C03B" stroke-width="4" stroke-linecap="round" opacity="0.45" fill="none"/>
          <path d="M94 72 C100 62 100 50 94 40" stroke="#F9C03B" stroke-width="4" stroke-linecap="round" opacity="0.45" fill="none"/>
        </svg>
      </div>
      <h2 class="perm-title">Enable Notification</h2>
      <p class="perm-desc">Please provide us access to your notification</p>
      <button class="perm-btn-allow" onclick="handlePerm('notification')">Allow</button>
      <button class="perm-btn-later" onclick="skipPerm('notification')">Maybe Later</button>
    </div>

    <!-- ── STEP 2: Location ───────────────────────────────────────────────── -->
    <div class="perm-content" id="permStep-location">
      <div class="perm-illus-wrap">
        <div class="perm-dashed"></div>
        <div class="perm-dot red"></div>
        <div class="perm-dot orange"></div>
        <div class="perm-dot blue"></div>
        <!-- Pin SVG -->
        <svg class="pin-anim" width="130" height="160" viewBox="0 0 130 160" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Shadow ellipse -->
          <ellipse cx="65" cy="148" rx="32" ry="10" fill="#C3BEF0" opacity="0.5"/>
          <!-- Pin body -->
          <path d="M65 10C45.7 10 30 25.7 30 45C30 68 65 105 65 105C65 105 100 68 100 45C100 25.7 84.3 10 65 10Z" fill="url(#pinGrad)"/>
          <!-- Inner circle -->
          <circle cx="65" cy="45" r="16" fill="white" opacity="0.9"/>
          <!-- Shine -->
          <circle cx="57" cy="36" r="6" fill="white" opacity="0.45"/>
          <defs>
            <linearGradient id="pinGrad" x1="30" y1="10" x2="100" y2="110" gradientUnits="userSpaceOnUse">
              <stop offset="0%" stop-color="#FF6BA8"/>
              <stop offset="100%" stop-color="#E8376E"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <h2 class="perm-title">Enable Location</h2>
      <p class="perm-desc">Please provide us access to your location</p>
      <button class="perm-btn-allow" onclick="handlePerm('location')">Allow</button>
      <button class="perm-btn-later" onclick="skipPerm('location')">Maybe Later</button>
    </div>

    <!-- ── STEP 3: Camera ────────────────────────────────────────────────── -->
    <div class="perm-content" id="permStep-camera">
      <div class="perm-illus-wrap">
        <div class="perm-dashed"></div>
        <div class="perm-dot red"></div>
        <div class="perm-dot orange"></div>
        <div class="perm-dot blue"></div>
        <!-- Camera SVG -->
        <svg class="cam-anim" width="160" height="130" viewBox="0 0 160 130" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Camera body -->
          <rect x="10" y="38" width="140" height="86" rx="16" fill="#8CAFC4"/>
          <!-- Top bump -->
          <rect x="20" y="28" width="40" height="20" rx="8" fill="#7A9CB0"/>
          <!-- Flash -->
          <rect x="66" y="28" width="24" height="12" rx="4" fill="#C8D8E4"/>
          <!-- Lens outer ring -->
          <circle cx="80" cy="81" r="30" fill="#3A4A5A"/>
          <!-- Lens mid ring -->
          <circle cx="80" cy="81" r="24" fill="#2A3A4A"/>
          <!-- Lens glass -->
          <circle cx="80" cy="81" r="18" fill="url(#lensGrad)"/>
          <!-- Lens shine -->
          <ellipse cx="73" cy="74" rx="7" ry="5" fill="white" opacity="0.25" transform="rotate(-20 73 74)"/>
          <!-- Shutter button -->
          <circle cx="138" cy="52" r="9" fill="#6C8CA0"/>
          <circle cx="138" cy="52" r="5" fill="#4A6A80"/>
          <defs>
            <radialGradient id="lensGrad" cx="40%" cy="35%" r="65%">
              <stop offset="0%" stop-color="#5CC8E0"/>
              <stop offset="60%" stop-color="#2A9AB8"/>
              <stop offset="100%" stop-color="#186080"/>
            </radialGradient>
          </defs>
        </svg>
      </div>
      <h2 class="perm-title">Enable Camera</h2>
      <p class="perm-desc">Please provide us access to your camera</p>
      <button class="perm-btn-allow" onclick="handlePerm('camera')">Allow</button>
      <button class="perm-btn-later" onclick="skipPerm('camera')">Maybe Later</button>
    </div>

  </div>
</div>

<script>
(function () {
    // ── Config: urutan step ──────────────────────────────────────────────
    const STEPS = ['notification', 'location', 'camera'];

    let currentIndex = -1;
    let queue = [];

    // ── Cek langsung via Browser Permission API ───────────────────────────
    async function buildQueue() {
        const result = [];

        for (const step of STEPS) {

            if (step === 'notification') {
                // Notification API pakai 'default' (bukan 'prompt' seperti Permissions API)
                if ('Notification' in window && Notification.permission === 'default') {
                    result.push(step);
                }
            }

            if (step === 'location') {
                if ('geolocation' in navigator) {
                    try {
                        const status = await navigator.permissions.query({ name: 'geolocation' });
                        if (status.state === 'prompt') result.push(step);
                    } catch (e) {
                        // Browser tidak support permissions.query → tampilkan saja
                        result.push(step);
                    }
                }
            }

            if (step === 'camera') {
                if (navigator.mediaDevices) {
                    try {
                        const status = await navigator.permissions.query({ name: 'camera' });
                        if (status.state === 'prompt') result.push(step);
                    } catch (e) {
                        // Browser tidak support permissions.query → tampilkan saja
                        result.push(step);
                    }
                }
            }
        }

        return result;
    }

    // ── Tampilkan overlay + sheet ─────────────────────────────────────────
    function showOverlay() {
        const overlay = document.getElementById('permOverlay');
        const sheet   = document.getElementById('permSheet');
        overlay.classList.add('visible');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => { sheet.classList.add('up'); });
        });
    }

    // ── Sembunyikan overlay ───────────────────────────────────────────────
    function hideOverlay() {
        const overlay = document.getElementById('permOverlay');
        const sheet   = document.getElementById('permSheet');
        sheet.classList.remove('up');
        setTimeout(() => overlay.classList.remove('visible'), 400);
    }

    // ── Transisi ke step berikutnya ───────────────────────────────────────
    function goToNextStep() {
        const currentStep = queue[currentIndex];
        const nextIndex   = currentIndex + 1;

        if (nextIndex >= queue.length) {
            hideOverlay();
            return;
        }

        const currentEl = document.getElementById('permStep-' + currentStep);
        const nextStep  = queue[nextIndex];
        const nextEl    = document.getElementById('permStep-' + nextStep);

        // Slide out current
        currentEl.classList.add('slide-out-left');
        setTimeout(() => {
            currentEl.classList.remove('active', 'slide-out-left');
            nextEl.classList.add('active', 'slide-in-right');
            setTimeout(() => nextEl.classList.remove('slide-in-right'), 400);
            currentIndex = nextIndex;
        }, 280);
    }

    // ── Handler tombol Allow ──────────────────────────────────────────────
    window.handlePerm = async function(step) {
        if (step === 'notification') {
            if ('Notification' in window && Notification.permission === 'default') {
                try {
                    const result = await Notification.requestPermission();
                    // Jika user grant, langsung init FCM agar token langsung didapat
                    if (result === 'granted' && typeof initFcm === 'function') {
                        initFcm();
                    }
                } catch(e) {}
            }
        }

        if (step === 'location') {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    () => {},
                    () => {},
                    { timeout: 5000 }
                );
            }
        }

        if (step === 'camera') {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    stream.getTracks().forEach(t => t.stop());
                } catch(e) {}
            }
        }

        goToNextStep();
    };

    // ── Handler tombol Maybe Later ────────────────────────────────────────
    // Tidak simpan apapun — next visit cek ulang via Permission API
    window.skipPerm = function() {
        goToNextStep();
    };

    // ── Init ─────────────────────────────────────────────────────────────
    async function init() {
        queue = await buildQueue();
        if (queue.length === 0) return;

        // Sembunyikan semua step dulu
        STEPS.forEach(s => {
            const el = document.getElementById('permStep-' + s);
            if (el) el.classList.remove('active');
        });

        // Tampilkan step pertama dari queue
        currentIndex = 0;
        const firstEl = document.getElementById('permStep-' + queue[0]);
        if (firstEl) firstEl.classList.add('active');

        // Tunda sedikit agar halaman selesai render dulu
        setTimeout(showOverlay, 1200);
    }

    // Jalankan setelah DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>