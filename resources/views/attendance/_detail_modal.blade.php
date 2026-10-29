{{-- Shared detail modal for attendance records. Requires window.atOpenDetail(data). --}}
<div id="atDetailModal" class="hidden fixed inset-0 z-[90] items-end justify-center sm:items-center">
    <div id="atDetailBackdrop" class="absolute inset-0 bg-black/50" onclick="atDetailClose()"></div>
    <div id="atDetailCard" class="relative w-full sm:max-w-sm max-h-[92vh] overflow-y-auto rounded-t-[28px] sm:rounded-[24px] bg-white hide-scrollbar"></div>
</div>

<script>
(function () {
    const STATUS = {
        present: 'Present', late: 'Late', absent: 'Absent'
    };
    const STYLE = {
        present: { color: '#16A34A', bg: '#F0FDF4' },
        late:    { color: '#D97706', bg: '#FEF3C7' },
        absent:  { color: '#DC2626', bg: '#FEF2F2' },
    };

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
    }
    function pad(n) { return String(n).padStart(2, '0'); }
    function fmt(s) {
        if (!s) return '—';
        const d = new Date(s.replace(' ', 'T'));
        return pad(d.getHours()) + ':' + pad(d.getMinutes());
    }
    function fmtDate(s) {
        if (!s) return '';
        const d = new Date(s.replace(' ', 'T'));
        const mo = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][d.getMonth()];
        return pad(d.getDate()) + ' ' + mo + ' ' + d.getFullYear();
    }

    function photoBlock(url, label) {
        return url
            ? `<div><img src="${esc(url)}" alt="${label}" class="w-full h-44 object-cover rounded-2xl border border-[#EAE6F5]" onclick="atOpenImage('${esc(url)}')"></div>`
            : `<div class="w-full h-28 rounded-2xl bg-[#F0EDF8] flex flex-col items-center justify-center text-[#8B86A5]">
                    <ion-icon name="image-outline" style="font-size:24px"></ion-icon>
                    <span class="text-[11px] font-bold mt-1">${label} — none</span>
               </div>`;
    }

    window.atOpenDetail = function (r) {
        const st = STYLE[r.status] || { color: '#8B46D3', bg: '#EDE9FE' };
        const label = STATUS[r.status] || (r.status ? r.status.charAt(0).toUpperCase() + r.status.slice(1) : 'Record');

        const rows = [
            { icon: 'log-in-outline',  color: '#D97706', t: 'Check-in',  v: fmt(r.checkin_time) },
            { icon: 'log-out-outline', color: '#16A34A', t: 'Check-out', v: fmt(r.checkout_time) },
        ];
        const meta = rows.map((x, i) => `
            <div class="flex-1 rounded-xl bg-[#F5F3FA] p-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wide flex items-center gap-1" style="color:${x.color}">
                    <ion-icon name="${x.icon}" style="font-size:11px"></ion-icon> ${x.t}
                </span>
                <p class="text-[16px] font-extrabold text-[#1E1B2E] mt-0.5">${x.v}</p>
            </div>`).join('');

        const fidelity = `
            <div class="mt-3 rounded-xl bg-[#F9FAFB] border border-[#E5E7EB] p-3">
                ${ (r.notes ? `
                    <div class="flex items-start gap-2 ${ (r.lat && r.lng) ? 'mb-2' : '' }">
                        <ion-icon name="chatbox-ellipses-outline" style="font-size:14px;color:#8B86A5" class="mt-0.5 shrink-0"></ion-icon>
                        <p class="text-[12px] font-semibold text-[#4B4763] leading-relaxed">${esc(r.notes) || ''}</p>
                    </div>` : '') }
                ${ (r.lat && r.lng) ? `
                    <div class="flex items-center gap-2">
                        <ion-icon name="location" style="font-size:14px;color:#8B46D3" class="shrink-0"></ion-icon>
                        <span class="text-[12px] font-bold text-[#4B4763]">${Number(r.lat).toFixed(4)}, ${Number(r.lng).toFixed(4)}</span>
                    </div>` : '' }
                ${ (!r.notes && !(r.lat && r.lng)) ? `<span class="text-[12px] font-semibold text-[#9CA3AF]">No additional details.</span>` : '' }
            </div>`;

        const card = document.getElementById('atDetailCard');
        card.innerHTML = `
            <div class="sticky top-0 bg-white/90 backdrop-blur px-5 py-4 border-b border-[#F0EDF8] flex items-center justify-between rounded-t-[28px] sm:rounded-t-[24px]">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:${st.bg}">
                        <ion-icon name="${st.icon || 'time-outline'}" style="font-size:15px;color:${st.color}"></ion-icon>
                    </div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wide" style="color:${st.color}">${label}</span>
                </div>
                <button onclick="atDetailClose()" class="w-8 h-8 rounded-full bg-[#F3F4F6] flex items-center justify-center" aria-label="Tutup">
                    <ion-icon name="close" style="font-size:16px;color:#6B7280"></ion-icon>
                </button>
            </div>

            <div class="px-5 pb-5">
                <div class="mb-4 mt-4 space-y-2">
                    ${ photoBlock(r.photo_checkin_url || r.photo_url, 'Check-in photo') }
                    ${ photoBlock(r.photo_checkout_url, 'Check-out photo') }
                </div>

                <p class="text-[12px] font-bold text-[#8B86A5] text-center mb-3" style="color:${st.color}">${fmtDate(r.checkin_time || r.created_at)}</p>

                <div class="flex gap-2">${meta}</div>
                ${fidelity}
            </div>`;

        const modal = document.getElementById('atDetailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.atDetailClose = function () {
        const modal = document.getElementById('atDetailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.atOpenImage = function (url) {
        window.open(url, '_blank');
    };
})();
</script>