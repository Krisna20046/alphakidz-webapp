<div id="historyList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} notes
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="tnGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="tnGoToPage({{ $pagination['current_page'] + 1 }})"
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
            <ion-icon name="school-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No teacher notes recorded by the nanny yet.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        <div class="rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-[#EDE9FE]">
                    <ion-icon name="school-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                              style="background:{{ !empty($r['subject']['color']) ? $r['subject']['color'] . '1A' : '#EDE9FE' }};color:{{ $r['subject']['color'] ?? '#8B46D3' }};">
                            {{ $r['subject']['name'] ?? 'General' }}
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">
                            {{ $r['created_at'] }}
                            @if(!empty($r['creator_name']))
                            · {{ $r['creator_name'] }}
                            @endif
                        </span>
                    </div>

                    @if(!empty($r['teacher_name']))
                    <div class="flex items-center gap-1.5 mt-2">
                        <ion-icon name="person-outline" style="font-size:12px;color:#F59E0B;flex-shrink:0;"></ion-icon>
                        <span class="text-[11px] font-bold text-[#B45309] line-clamp-1">{{ $r['teacher_name'] }}</span>
                    </div>
                    @endif

                    @if(!empty($r['note']))
                    <div class="mt-2 rounded-xl bg-[#F3F0FD] p-2.5">
                        <span class="text-[10px] font-extrabold text-[#8B46D3] uppercase tracking-wide">Note</span>
                        <p class="text-[11px] font-semibold text-[#1E1B2E] mt-0.5 whitespace-pre-line">{{ $r['note'] }}</p>
                    </div>
                    @endif

                    @if(!empty($r['attachment']))
                    <a href="{{ $r['attachment'] }}" target="_blank" rel="noreferrer"
                       class="mt-2 inline-flex items-center gap-1.5 rounded-xl bg-[#F0FDF4] px-2.5 py-2">
                        <ion-icon name="image-outline" style="font-size:13px;color:#16A34A;flex-shrink:0;"></ion-icon>
                        <span class="text-[11px] font-bold text-[#16A34A]">Lihat lampiran</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>