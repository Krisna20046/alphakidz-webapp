@extends('layouts.app')

@section('title', 'Daily Checklist - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    /* data-on visual states */
    [data-on="1"].dc-toggle { background:#F4EEFD; }
    [data-on="0"].dc-toggle { background:#FAF9FE; }
    [data-on="1"].dc-check { background:#8B46D3; border-color:#8B46D3; }
    [data-on="0"].dc-check { background:#fff; border-color:#DDD6EF; }
    [data-on="0"] .dc-check-icon { display:none; }
    [data-on="1"] .dc-item-text { color:#8B86A5; text-decoration:line-through; }
    [data-on="0"] .dc-item-text { color:#1E1B2E; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-daily-checklists') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        @if(count($anakList) > 1)
        <div class="relative">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('nanny-daily-checklists-show', ['id_anak' => $anak['id'], 'day' => $day]) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @else
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Daily Checklist</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Status indicator: "Menyimpan..." / "Tersimpan" --}}
<div id="dcStatusIndicator" class="fixed top-16 left-1/2 -translate-x-1/2 z-50 hidden">
    <div class="bg-[#8B46D3] text-white px-4 py-2 rounded-full shadow-lg flex items-center gap-2">
        <span id="dcStatusText">Menyimpan...</span>
        <span id="dcSpinner" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    @if(session('success') || session('error'))
    <div id="toast" class="toast rounded-2xl px-4 py-3 flex items-center gap-3 mb-4
        {{ session('success') ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center
            {{ session('success') ? 'bg-green-100' : 'bg-red-100' }}">
            <ion-icon name="{{ session('success') ? 'checkmark-circle' : 'close-circle' }}"
                style="font-size:18px;color:{{ session('success') ? '#4CAF50' : '#F44336' }};"></ion-icon>
        </div>
        <p class="text-sm font-bold {{ session('success') ? 'text-green-800' : 'text-red-800' }} flex-1">
            {{ session('success') ?? session('error') }}
        </p>
        <button onclick="document.getElementById('toast').remove()">
            <ion-icon name="close" style="font-size:16px;color:#999;"></ion-icon>
        </button>
    </div>
    @endif

    {{-- Pilih hari: Senin–Minggu. Tiap hari punya list-nya sendiri, tanpa tanggal. --}}
    <div class="anim delay-2 flex gap-2 overflow-x-auto pb-1 mb-4 hide-scrollbar">
        @foreach($dayNames as $num => $nama)
        <a href="{{ route('nanny-daily-checklists-show', ['id_anak' => $idAnak, 'day' => $num]) }}"
           class="shrink-0 px-3.5 py-2 rounded-xl text-[12px] font-extrabold border transition-colors
                  {{ (int)$day === $num
                        ? 'bg-[#8B46D3] border-[#8B46D3] text-white'
                        : 'bg-white border-[#DDD6EF] text-[#8B86A5]' }}">
            {{ $nama }}
            @if((int)date('N') === $num)
            <span class="ml-1 text-[9px] font-bold {{ (int)$day === $num ? 'text-white/70' : 'text-[#C4B5FD]' }}">HARI INI</span>
            @endif
        </a>
        @endforeach
    </div>

    @include('nanny.daily-checklist._tab', ['idAnak' => $idAnak, 'day' => $day, 'dayNames' => $dayNames, 'weekly' => $weekly])

</div>

{{-- FAB: tambah list untuk hari yang sedang dilihat --}}
<div class="fixed bottom-[80px] right-[20px] sm:right-[calc(50%-175px)] z-30">
    <a href="{{ route('nanny-daily-checklists-create', ['id_anak' => $idAnak, 'day' => $day]) }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>

{{-- Modal konfirmasi hapus --}}
<div id="dcDeleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6">
    <div id="dcDeleteBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-sm bg-white rounded-[28px] p-6 text-center shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <ion-icon name="trash-outline" style="font-size:26px;color:#DC2626;"></ion-icon>
        </div>
        <p class="text-[16px] font-extrabold text-[#1E1B2E] mb-1">Hapus checklist hari ini?</p>
        <p class="text-[12px] font-semibold text-[#8B86A5] leading-relaxed mb-5">
            Checklist beserta item-nya tidak bisa dikembalikan setelah dihapus.
        </p>
        <form id="dcDeleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="dcDeleteClose()"
                    class="flex-1 py-3 rounded-2xl border border-[#DDD6EF] text-[#8B46D3] text-[13px] font-extrabold">Batal</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#DC2626] text-white text-[13px] font-extrabold">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>

<script>
const dcDeleteModal = document.getElementById('dcDeleteModal');
const dcDeleteForm  = document.getElementById('dcDeleteForm');
if (dcDeleteModal) {
    document.getElementById('dcDeleteBackdrop').addEventListener('click', dcDeleteClose);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !dcDeleteModal.classList.contains('hidden')) dcDeleteClose();
    });
}
function dcDeleteConfirm(url) {
    if (!dcDeleteModal) return false;
    dcDeleteForm.action = url;
    dcDeleteModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    return false;
}
function dcDeleteClose() {
    dcDeleteModal.classList.add('hidden');
    document.body.style.overflow = '';
}
</script>

{{-- Daily Checklist: instant toggle + debounce autosave (1.8s idle → PUT full state) --}}
<script>
(function() {
    const API_BASE  = @json(config('services.api.base_url', env('API_BASE_URL', 'http://localhost:8000/api')));
    const API_TOKEN = '{{ session("token") }}';
    const DAY       = @json((int) $day);
    const DEBOUNCE_MS = 1800;

    let state = {};   // [cid][iid] = { name, completed }  — mirror of server state
    let dirty = {};   // [cid] = true — checklist needs a PUT
    let good  = {};   // [cid][iid] = { name, completed } — snapshot at first touch (revert)
    let busy = false, queued = false, timer = null;

    function setStatus(mode) {
        const el = document.getElementById('dcStatusIndicator');
        if (!el) return;
        if (mode === 'saving') {
            el.classList.remove('hidden');
            document.getElementById('dcStatusText').textContent = 'Menyimpan...';
            document.getElementById('dcSpinner').classList.remove('hidden');
        } else if (mode === 'saved') {
            document.getElementById('dcStatusText').textContent = 'Tersimpan';
            document.getElementById('dcSpinner').classList.add('hidden');
            setTimeout(() => el.classList.add('hidden'), 2000);
        } else if (mode === 'off') {
            el.classList.add('hidden');
        }
    }

    function apply(cid, iid, completed) {
        const btn = document.querySelector(`[data-checklist-id="${cid}"][data-item-id="${iid}"].dc-toggle`);
        if (!btn) return;
        const chk = btn.querySelector('.dc-check');
        const txt = btn.querySelector('.dc-item-text');
        const onVal = completed ? '1' : '0';
        btn.dataset.on = onVal;
        chk.dataset.on = onVal;
        txt.dataset.on = onVal;
        updateCounters(cid);
    }

    function updateCounters(cid) {
        const card = document.querySelector(`[data-checklist-id="${cid}"]`);
        if (!card) return;
        const btns = card.querySelectorAll('.dc-toggle');
        const total = btns.length;
        let done = 0;
        btns.forEach(b => { if (b.dataset.on === '1') done++; });
        const pct = total ? Math.round((done / total) * 100) : 0;
        const cEl = card.querySelector('.dc-completed-count');
        const tEl = card.querySelector('.dc-total-count');
        const pEl = card.querySelector('.dc-progress-pct');
        const bar = card.querySelector('.dc-progress-bar');
        if (cEl) cEl.textContent = done;
        if (tEl) tEl.textContent = total;
        if (pEl) pEl.textContent = pct;
        if (bar) bar.style.width = pct + '%';
    }

    function dctoggle(btn) {
        const cid = +btn.dataset.checklistId;
        const iid = +btn.dataset.itemId;
        if (!cid || !iid) return;
        const s = (state[cid] || (state[cid] = {}))[iid] || (
            (state[cid][iid] = { name: btn.dataset.itemName, completed: btn.dataset.on === '1' })
        );
        if (!(good[cid] || (good[cid] = {}))[iid]) {
            good[cid][iid] = { name: s.name, completed: s.completed }; // snapshot before flip
        }
        s.completed = !s.completed;
        dirty[cid] = true;
        apply(cid, iid, s.completed);
        setStatus('saving');
        clearTimeout(timer);
        timer = setTimeout(flush, DEBOUNCE_MS);
    }

    async function flush() {
        clearTimeout(timer);
        if (busy) { queued = true; return; }
        busy = true;
        for (const cid of Object.keys(dirty)) {
            try {
                const card = document.querySelector(`[data-checklist-id="${cid}"]`);
                const items = Object.keys(state[cid]).map(iid => ({
                    id: +iid,
                    item_name: state[cid][iid].name,
                    is_completed: state[cid][iid].completed,
                }));
                const res = await fetch(`${API_BASE}/daily-checklists/${cid}`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        title: card ? card.dataset.checklistTitle : '',
                        day_of_week: DAY,
                        items: items,
                    }),
                });
                if (!res.ok) { revert(cid); continue; }
                delete dirty[cid];
                delete good[cid];
            } catch (e) {
                revert(cid);
            }
        }
        busy = false;
        if (queued) { queued = false; flush(); return; }
        if (Object.keys(dirty).length === 0) setStatus('saved');
        else setStatus('saving');
    }

    function revert(cid) {
        const g = good[cid];
        if (!g) return;
        Object.keys(g).forEach(iid => {
            state[cid][iid].completed = g[iid].completed;
            apply(+cid, +iid, g[iid].completed);
        });
        delete good[cid];
        // toast error
        showDcError('Gagal menyimpan perubahan. Di-ulang sapata.');
    }

    function showDcError(msg) {
        const old = document.getElementById('dcErrToast');
        if (old) old.remove();
        const t = document.createElement('div');
        t.id = 'dcErrToast';
        t.className = 'fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 rounded-2xl shadow-lg';
        t.style.cssText = 'max-width:85%;text-align:center;';
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 4000);
    }

    // wire up
    document.addEventListener('click', e => {
        const btn = e.target.closest('.dc-toggle');
        if (btn) dctoggle(btn);
    });
})();
</script>
@endpush