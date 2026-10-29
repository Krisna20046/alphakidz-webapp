<div id="historyList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} summary{{ ($pagination ? $pagination['total'] : count($records)) === 1 ? '' : 's' }}
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="bsGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="bsGoToPage({{ $pagination['current_page'] + 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
        @endif
    </div>

    {{-- List --}}
    @if(count($records) === 0)
    <div class="flex flex-col items-center py-8">
        <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
            <ion-icon name="analytics-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No summary yet. Generate one above.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        <div class="rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <a href="{{ $r['id'] ? route('nanny-behavior-summaries-detail', $r['id']) : '#' }}"
               class="flex items-start gap-3 {{ $r['id'] ? 'active:opacity-70' : 'pointer-events-none' }}">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-[#EDE9FE]">
                    <ion-icon name="analytics-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[12px] font-extrabold text-[#1E1B2E]">
                            @if(!empty($r['nama_anak']))
                                {{ $r['nama_anak'] }}
                            @else
                                Behavior Summary
                            @endif
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">
                            @if(!empty($r['summary_date']))
                                {{ \Carbon\Carbon::parse($r['summary_date'])->translatedFormat('d M Y') }}
                            @endif
                        </span>
                    </div>

                    @if(!empty($r['behavior_summary']))
                    <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed mt-1.5 line-clamp-3">{{ $r['behavior_summary'] }}</p>
                    @endif

                    {{-- Mood & focus badges --}}
                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                        @if(!empty($r['mood_pattern']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FDF2F8] text-[#DB2777]">
                            <ion-icon name="happy-outline" style="font-size:11px;"></ion-icon>
                            Mood
                        </span>
                        @endif
                        @if(!empty($r['focus_pattern']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#4F46E5]">
                            <ion-icon name="eye-outline" style="font-size:11px;"></ion-icon>
                            Fokus
                        </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F0FDF4] text-[#16A34A]">
                            <ion-icon name="checkmark-circle" style="font-size:11px;"></ion-icon>
                            Tersedia
                        </span>
                    </div>
                </div>
            </a>

            {{-- Actions --}}
            @if(!empty($r['recommendation']))
            <div class="mt-2 rounded-xl bg-[#F8F6FF] border border-[#EAE6F5] p-2.5">
                <span class="text-[10px] font-extrabold text-[#8B46D3] block mb-0.5 flex items-center gap-1">
                    <ion-icon name="bulb-outline" style="font-size:11px;"></ion-icon> Rekomendasi
                </span>
                <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed line-clamp-2">{{ $r['recommendation'] }}</p>
            </div>
            @endif

            @if($r['id'])
            <div class="flex items-center gap-2 mt-2">
                <span class="flex-1 py-2 rounded-xl border border-dashed border-[#D6CCEA] text-[#8B86A5] text-[12px] font-bold text-center">
                    Analysis AI · {{ !empty($r['behavior_summary']) ? 'ringkasan naratif' : 'belum ada data' }}
                </span>
                <form method="POST" action="{{ route('nanny-behavior-summaries-regenerate', $r['id']) }}"
                      class="shrink-0"
                      onsubmit="event.preventDefault(); bsConfirmRegenerate('{{ route('nanny-behavior-summaries-regenerate', $r['id']) }}'); return false;">
                    @csrf
                    <input type="hidden" name="id_anak" value="{{ $idAnak }}">
                    <button type="submit" aria-label="Regenerate"
                        class="w-9 h-9 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                        <ion-icon name="refresh-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>