{{-- resources/views/partials/nanny-gps-sharer.blade.php --}}
{{-- Nanny GPS Location Sharing — berjalan di SEMUA halaman via auth-guard --}}
@php
    $nannyRole = session('user')['id_role'] ?? null;
    $nannyUserId = session('user_id') ?: data_get(session('user'), 'id_user');
@endphp

@if($nannyRole == '3')
<script>
(function() {
    'use strict';

    const STORAGE_KEY  = 'nanny_gps_sharing_active';
    const DEVICE_KEY   = 'nanny_gps_device_id';
    const NANNY_ID     = @json($nannyUserId);
    const NANNY_TOKEN  = "{{ session('token') }}";
    const NANNY_API    = "{{ rtrim(config('services.api.base_url', env('API_BASE_URL', 'https://api.alpha-kidz.com/api')), '/') }}";
    const NANNY_CSRF   = "{{ csrf_token() }}";

    // ID perangkat stabil (persisten di browser ini) — identitas untuk backend
    let _deviceId = null;
    try { _deviceId = localStorage.getItem(DEVICE_KEY); } catch(e) {}
    if (!_deviceId) {
        _deviceId = 'web-' + Math.random().toString(36).slice(2, 10) + '-' + Date.now();
        try { localStorage.setItem(DEVICE_KEY, _deviceId); } catch(e) {}
    }

    let _interval = null;
    let _active   = false;

    /* ── Start sharing ── */
    // forceStart=true hanya saat user menyalakan toggle (berniat take over).
    // Auto-resume dari localStorage tidak memaksa take over.
    window.startNannyGps = async function(forceStart) {
        if (_active) return;
        _active = true;
        try { localStorage.setItem(STORAGE_KEY, '1'); } catch(e) {}

        await _sendLocation(!!forceStart);
        _interval = setInterval(_sendLocation, 60000);
    };

    /* ── Stop sharing ── */
    window.stopNannyGps = async function(sendOffline) {
        _active = false;
        if (_interval) { clearInterval(_interval); _interval = null; }
        try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}

        if (sendOffline !== false) {
            try {
                await fetch(NANNY_API + '/nanny/update-location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + NANNY_TOKEN },
                    body: JSON.stringify({ latitude: null, longitude: null, is_online: false, device_id: _deviceId }),
                });
            } catch(e) {}
        }
    };

    /* ── Kirim lokasi ── */
    async function _sendLocation(forceStart) {
        if (!_active) return;
        try {
            const pos = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true, timeout: 10000, maximumAge: 30000,
                });
            });
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            // Update UI elemen di halaman manapun yang punya ID ini
            _updateUi(lat, lng);

            const res = await fetch(NANNY_API + '/nanny/update-location', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + NANNY_TOKEN },
                body: JSON.stringify({ latitude: lat, longitude: lng, is_online: true, device_id: _deviceId, start: !!forceStart }),
            });
            const json = await res.json().catch(() => null);

            // Perangkat lain menyalakan share lebih dulu → akhiri sharing di perangkat ini
            if (json && json.status === 'inactive') {
                await window.stopNannyGps(false);
                _forceUiOff();
            }
        } catch(err) {
            if (err.code === 1) { // Permission denied
                await window.stopNannyGps(false);
            }
        }
    }

    /* ── Reset UI toggle ke OFF (dipanggil saat auto-off karena device lain aktif) ── */
    function _forceUiOff() {
        const toggle = document.getElementById('nannyGpsToggle');
        if (toggle) toggle.checked = false;
        const dot = document.getElementById('nannyGpsDot');
        if (dot) dot.className = 'w-12 h-12 rounded-full bg-[#D1D5DB] flex items-center justify-center';
        const statusLabel = document.getElementById('nannyGpsStatusLabel');
        if (statusLabel) { statusLabel.textContent = 'Location sharing is OFF'; statusLabel.className = 'text-[#9CA3AF] text-[12px] font-bold'; }
        const nannyStatus = document.getElementById('nannyGpsStatus');
        if (nannyStatus) nannyStatus.textContent = 'Share lokasi dimatikan, perangkat lain aktif';
        document.getElementById('nannyGpsCoords')?.classList.add('hidden');
        document.getElementById('nannyGpsLastUpdate')?.classList.add('hidden');
        document.getElementById('nannyGpsAddress')?.classList.add('hidden');
    }

    /* ── Update UI components if they exist on current page ── */
    function _updateUi(lat, lng) {
        // Home page — nannyGpsDot
        const dot = document.getElementById('nannyGpsDot');
        if (dot) dot.className = 'w-12 h-12 rounded-full bg-[#22C55E] flex items-center justify-center';

        // Update koordinat
        const coordsEl = document.getElementById('nannyGpsCoords');
        if (coordsEl) {
            coordsEl.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
            coordsEl.classList.remove('hidden');
        }

        // Update last update time
        const lastUpdateEl = document.getElementById('nannyGpsLastUpdate');
        if (lastUpdateEl) {
            lastUpdateEl.textContent = 'Last update: ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            lastUpdateEl.classList.remove('hidden');
        }

        // Reverse geocode address
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=16', {
            headers: { 'Accept-Language': 'id' }
        }).then(r => r.json()).then(geo => {
            const el = document.getElementById('nannyGpsAddress');
            if (el && geo.display_name) {
                const span = el.querySelector('span');
                if (span) span.textContent = geo.display_name.substring(0, 80);
                el.classList.remove('hidden');
            }
        }).catch(function(){});
    }

    /* ── Cek state tersimpan, start ulang jika perlu ── */
    try {
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            window.startNannyGps(false);
        }
    } catch(e) {}

    /* ── Expose status checker untuk UI ── */
    window.isNannyGpsActive = function() { return _active; };

    /* ── Sync UI toggle di halaman yang punya nannyGpsToggle (home) ── */
    function syncUi() {
        if (!_active) return;
        const toggle = document.getElementById('nannyGpsToggle');
        if (!toggle) return;
        toggle.checked = true;
        const dot = document.getElementById('nannyGpsDot');
        if (dot) dot.className = 'w-12 h-12 rounded-full bg-[#22C55E] flex items-center justify-center';
        const statusLabel = document.getElementById('nannyGpsStatusLabel');
        if (statusLabel) { statusLabel.textContent = 'Location sharing is ON'; statusLabel.className = 'text-[#166534] text-[12px] font-bold'; }
        const nannyStatus = document.getElementById('nannyGpsStatus');
        if (nannyStatus) nannyStatus.textContent = 'Location active';
        document.getElementById('nannyGpsCoords')?.classList.remove('hidden');
        document.getElementById('nannyGpsLastUpdate')?.classList.remove('hidden');
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncUi);
    } else {
        syncUi();
    }

})();
</script>
@endif
