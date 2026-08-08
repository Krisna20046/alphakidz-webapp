@php
    $moodMeta = [
        'senang' => ['label' => 'Senang',    'icon' => 'happy-outline',    'color' => '#16A34A', 'bg' => '#F0FDF4'],
        'sedih'  => ['label' => 'Sedih',     'icon' => 'sad-outline',      'color' => '#3B82F6', 'bg' => '#EFF6FF'],
        'marah'  => ['label' => 'Marah',     'icon' => 'flame-outline',    'color' => '#DC2626', 'bg' => '#FEF2F2'],
        'biasa'  => ['label' => 'Biasa',     'icon' => 'remove-circle-outline', 'color' => '#6B7280', 'bg' => '#F3F4F6'],
    ];
@endphp

<div id="historyList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} notes
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="anGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="anGoToPage({{ $pagination['current_page'] + 1 }})"
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
            <ion-icon name="reader-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No notes recorded by the nanny yet.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        @php
            $mood = $moodMeta[$r['mood'] ?? ''] ?? ['label'=>ucfirst($r['mood'] ?? ''),'icon'=>'happy-outline','color'=>'#8B46D3','bg'=>'#EDE9FE'];
        @endphp
        <div class="rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $mood['bg'] }};">
                    <ion-icon name="{{ $mood['icon'] }}" style="font-size:16px;color:{{ $mood['color'] }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:{{ $mood['bg'] }};color:{{ $mood['color'] }};">
                            {{ $mood['label'] }}
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">
                            {{ $r['created_at'] }}
                            @if(!empty($r['creator_name']))
                            · {{ $r['creator_name'] }}
                            @endif
                        </span>
                    </div>

                    @if(!empty($r['task_title']))
                    <div class="flex items-center gap-1.5 mt-2">
                        <ion-icon name="briefcase-outline" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[11px] font-bold text-[#8B46D3] line-clamp-1">{{ $r['task_title'] }}</span>
                    </div>
                    @endif

                    @if(!empty($r['highlight']))
                    <div class="mt-2 rounded-xl bg-[#F3F0FD] p-2.5">
                        <span class="text-[10px] font-extrabold text-[#8B46D3] uppercase tracking-wide">Highlight</span>
                        <p class="text-[11px] font-semibold text-[#1E1B2E] mt-0.5">{{ $r['highlight'] }}</p>
                    </div>
                    @endif

                    @if(!empty($r['concern']))
                    <div class="mt-2 rounded-xl bg-[#FEF2F2] p-2.5">
                        <span class="text-[10px] font-extrabold text-[#DC2626] uppercase tracking-wide">Concern</span>
                        <p class="text-[11px] font-semibold text-[#1E1B2E] mt-0.5">{{ $r['concern'] }}</p>
                    </div>
                    @endif

                    @if(!empty($r['recommendation']))
                    <div class="mt-2 rounded-xl bg-[#F0FDF4] p-2.5">
                        <span class="text-[10px] font-extrabold text-[#16A34A] uppercase tracking-wide">Rekomendasi</span>
                        <p class="text-[11px] font-semibold text-[#1E1B2E] mt-0.5">{{ $r['recommendation'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
