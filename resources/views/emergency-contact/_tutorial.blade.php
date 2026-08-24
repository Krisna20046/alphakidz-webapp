{{-- Modal tutorial Emergency Contact (dipakai Nanny & Majikan).
     Parameter: $steps = [ ['icon'=>.., 'color'=>.., 'title'=>.., 'body'=>html], ... ] --}}
@if(!empty($steps))
<div id="ecTutorialModal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center">
    <div id="ecTutorialBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm"></div>

    <div class="relative bg-white rounded-t-[28px] sm:rounded-[28px] w-full max-w-md max-h-[88vh] flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="relative bg-[#8B46D3] px-5 pt-5 pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-white text-[15px] font-extrabold">Panduan Kontakti Kurgensi</span>
                    <p id="ecTutorialCount" class="text-white/60 text-[11px] font-bold mt-0.5">Langkah 1 dari {{ count($steps) }}</p>
                </div>
                <button type="button" onclick="ecTutorialClose()"
                    class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <ion-icon name="close" class="text-white" style="font-size:16px;"></ion-icon>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 hide-scrollbar">
            @foreach($steps as $i => $s)
            <div class="ec-step" data-step="{{ $i }}" @if($i !== 0) style="display:none" @endif>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0"
                         style="background:{{ $s['color'] ?? '#8B46D3' }}">
                        <ion-icon name="{{ $s['icon'] ?? 'help-circle-outline' }}" style="font-size:19px;"></ion-icon>
                    </div>
                    <span class="text-[15px] font-extrabold text-[#1E1B2E]">{{ $s['title'] }}</span>
                </div>
                <div class="tutorial-body text-[12px] leading-relaxed text-[#4B4763] font-semibold space-y-2">
                    {!! $s['body'] !!}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Progress dots --}}
        <div class="flex items-center justify-center gap-1.5 pt-1">
            @foreach($steps as $i => $s)
            <span class="ec-dot w-1.5 h-1.5 rounded-full transition-all" data-step="{{ $i }}"
                  style="background:{{ $i === 0 ? '#8B46D3' : '#E5E0F3' }}"></span>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="flex items-center gap-3 p-4 pb-24">
            <button type="button" onclick="ecTutorialGo(-1)" id="ecTutorialPrev"
                class="flex-1 py-3 rounded-2xl border border-[#DDD6EF] text-[#8B46D3] text-[13px] font-extrabold opacity-30 pointer-events-none">Sebelumnya</button>
            <button type="button" onclick="ecTutorialGo(1)" id="ecTutorialNext"
                class="flex-1 py-3 rounded-2xl bg-[#8B46D3] text-white text-[13px] font-extrabold">Berikutnya</button>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__ecTutorialBound) return;
    window.__ecTutorialBound = true;

    const modal = document.getElementById('ecTutorialModal');
    const total = {{ count($steps) }};
    let cur = 0;

    const stepEls = () => Array.from(modal.querySelectorAll('.ec-step'));
    const dotEls  = () => Array.from(modal.querySelectorAll('.ec-dot'));
    const countEl = document.getElementById('ecTutorialCount');
    const prevBtn = document.getElementById('ecTutorialPrev');
    const nextBtn = document.getElementById('ecTutorialNext');

    function render() {
        stepEls().forEach((el, i) => { el.style.display = i === cur ? '' : 'none'; });
        dotEls().forEach((el, i) => { el.style.background = i === cur ? '#8B46D3' : '#E5E0F3'; });
        if (countEl) countEl.textContent = 'Langkah ' + (cur + 1) + ' dari ' + total;
        prevBtn.classList.toggle('opacity-30', cur === 0);
        prevBtn.classList.toggle('pointer-events-none', cur === 0);
        nextBtn.textContent = cur === total - 1 ? 'Selesai' : 'Berikutnya';
    }

    window.ecTutorialOpen = function () {
        cur = 0; render();
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.ecTutorialClose = function () {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    window.ecTutorialGo = function (d) {
        const n = cur + d;
        if (n < 0) return;
        if (n >= total) { ecTutorialClose(); return; }
        cur = n; render();
    };

    modal.querySelector('#ecTutorialBackdrop').addEventListener('click', ecTutorialClose);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) ecTutorialClose();
    });
})();
</script>
@endif