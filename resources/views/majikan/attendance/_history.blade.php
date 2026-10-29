@php
    $statusMeta = [
        'present' => ['label' => 'Present', 'icon' => 'checkmark-circle', 'color' => '#16A34A', 'bg' => '#F0FDF4'],
        'late'    => ['label' => 'Late',    'icon' => 'time-outline',     'color' => '#D97706', 'bg' => '#FEF3C7'],
        'absent'  => ['label' => 'Absent',  'icon' => 'close-circle',     'color' => '#DC2626', 'bg' => '#FEF2F2'],
    ];
@endphp

<div id="historyList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} attendance record{{ ($pagination ? $pagination['total'] : count($records)) === 1 ? '' : 's' }}
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="atGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="atGoToPage({{ $pagination['current_page'] + 1 }})"
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
            <ion-icon name="time-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No attendance records yet.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        @php
            $st = $statusMeta[$r['status'] ?? ''] ?? ['label' => ucfirst($r['status'] ?? ''), 'icon' => 'time-outline', 'color' => '#8B46D3', 'bg' => '#EDE9FE'];
            $in  = !empty($r['checkin_time'])  ? \Carbon\Carbon::parse($r['checkin_time'])  : null;
            $out = !empty($r['checkout_time']) ? \Carbon\Carbon::parse($r['checkout_time']) : null;
        @endphp
        <button type="button" onclick="atOpenDetail({{ json_encode($r) }})"
            class="rounded-2xl border border-[#EAE6F5] p-3 bg-white text-left active:scale-[0.99] transition-transform">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $st['bg'] }};">
                    <ion-icon name="{{ $st['icon'] }}" style="font-size:16px;color:{{ $st['color'] }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:{{ $st['bg'] }};color:{{ $st['color'] }};">
                            {{ $st['label'] }}
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">
                            @if($in)
                                {{ $in->translatedFormat('d M Y') }}
                            @else
                                {{ $r['created_at'] }}
                            @endif
                        </span>
                    </div>

                    {{-- Times --}}
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <div class="rounded-xl bg-[#F3F0FD] p-2.5">
                            <span class="text-[10px] font-extrabold text-[#8B46D3] uppercase tracking-wide flex items-center gap-1">
                                <ion-icon name="log-in-outline" style="font-size:11px;"></ion-icon> Check-in
                            </span>
                            <p class="text-[12px] font-bold text-[#1E1B2E] mt-0.5">
                                {{ $in ? $in->format('H:i') : '—' }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-[#F3F0FD] p-2.5">
                            <span class="text-[10px] font-extrabold text-[#8B46D3] uppercase tracking-wide flex items-center gap-1">
                                <ion-icon name="log-out-outline" style="font-size:11px;"></ion-icon> Check-out
                            </span>
                            <p class="text-[12px] font-bold text-[#1E1B2E] mt-0.5">
                                {{ $out ? $out->format('H:i') : '—' }}
                            </p>
                        </div>
                    </div>

                    @if(!empty($r['lat']) && !empty($r['lng']))
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <ion-icon name="location" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[10px] font-bold text-[#4B4763]" data-geo="{{ $r['lat'] }},{{ $r['lng'] }}">{{ round($r['lat'], 4) }}, {{ round($r['lng'], 4) }}</span>
                    </div>
                    @endif

                    @if(!empty($r['notes']))
                    <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed mt-1.5 line-clamp-2">{{ $r['notes'] }}</p>
                    @endif
                </div>
                <div class="shrink-0 self-center text-[#C9C3DA]">
                    <ion-icon name="chevron-forward" style="font-size:16px;"></ion-icon>
                </div>
            </div>
        </button>
        @endforeach
    </div>
    @endif
</div>