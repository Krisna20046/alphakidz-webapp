@php
    $typeMeta = [
        'diary'           => ['label' => 'Diary',         'icon' => 'book-outline',               'color' => '#8B46D3', 'bg' => '#EDE9FE'],
        'task_progress'   => ['label' => 'Task Progress', 'icon' => 'checkmark-done-outline',     'color' => '#2563EB', 'bg' => '#EFF6FF'],
        'assistant_note'  => ['label' => 'Note',          'icon' => 'reader-outline',             'color' => '#16A34A', 'bg' => '#F0FDF4'],
        'attendance'      => ['label' => 'Attendance',    'icon' => 'time-outline',               'color' => '#F59E0B', 'bg' => '#FEF3C7'],
        'reminder'        => ['label' => 'Reminder',      'icon' => 'notifications-outline',      'color' => '#DC2626', 'bg' => '#FEF2F2'],
    ];
@endphp

<div id="tlList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} events
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="tlGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="tlGoToPage({{ $pagination['current_page'] + 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
        @endif
    </div>

    {{-- Timeline --}}
    @if(count($records) === 0)
    <div class="flex flex-col items-center py-10">
        <div class="w-16 h-16 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
            <ion-icon name="time-outline" style="font-size:30px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No activity yet for this period.</p>
    </div>
    @else
    <div class="flex flex-col gap-2" style="position:relative;">
        <div class="absolute inset-y-0 w-[2px] bg-[#E4DEFA] left-[19px] top-0 bottom-0"></div>
        @foreach($records as $r)
        @php
            $type = $r['source_type'] ?? 'diary';
            $meta = $typeMeta[$type] ?? ['label'=>ucfirst($type),'icon'=>'ellipse-outline','color'=>'#8B86A5','bg'=>'#F3F4F6'];
        @endphp
        <div class="rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $meta['bg'] }};box-shadow:0 0 0 2px {{ $meta['bg'] }};">
                    <ion-icon name="{{ $meta['icon'] }}" style="font-size:16px;color:{{ $meta['color'] }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};">
                            {{ $meta['label'] }}
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">
                            {{ $r['event_time'] }}
                            @if(!empty($r['nama_creator']))
                            · {{ $r['nama_creator'] }}
                            @endif
                        </span>
                    </div>
                    <p class="line-clamp-2 mt-1.5" style="font-size:13px;font-weight:600;color:#1E1B2E;">
                        {{ $r['title'] }}
                    </p>
                    @if(!empty($r['description']))
                    <p class="line-clamp-2 mt-0.5 text-[11px] font-medium text-[#8B86A5]">{{ $r['description'] }}</p>
                    @endif

                    {{-- Meta detail per type --}}
                    @php $m = (array) ($r['meta'] ?? []); @endphp
                    @if($type === 'diary' && !empty($m['mood']))
                    <div class="flex items-center gap-1.5 mt-2">
                        <ion-icon name="happy-outline" style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[10px] font-bold text-[#8B46D3]">{{ ucfirst($m['mood']) }}</span>
                        @if(!empty($m['lokasi']))
                        <ion-icon name="location-outline" style="font-size:11px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[10px] font-semibold text-[#8B86A5] line-clamp-1">{{ $m['lokasi'] }}</span>
                        @endif
                    </div>
                    @endif

                    @if($type === 'task_progress' && !empty($m['progress_percentage']))
                    <div class="mt-2 h-1.5 w-full rounded-full bg-[#EDE9FE] relative">
                        <div class="absolute inset-y-0 left-0 rounded-full bg-[#2563EB]" style="width:min(100%,{{ (int) $m['progress_percentage'] }}%);"></div>
                    </div>
                    @endif

                    @if($type === 'assistant_note' && !empty($m['highlight']))
                    <div class="mt-2 rounded-xl bg-[#F0FDF4] p-2">
                        <span class="text-[10px] font-extrabold text-[#16A34A] uppercase tracking-wide">Highlight</span>
                        <p class="text-[11px] font-semibold text-[#1E1B2E] mt-0.5">{{ $m['highlight'] }}</p>
                    </div>
                    @endif

                    @if($type === 'assistant_note' && !empty($m['concern']))
                    <div class="mt-2 rounded-xl bg-[#FEF2F2] p-2">
                        <span class="text-[10px] font-extrabold text-[#DC2626] uppercase tracking-wide">Concern</span>
                        <p class="text-[11px] font-semibold text-[#1E1B2E] mt-0.5">{{ $m['concern'] }}</p>
                    </div>
                    @endif

                    @if($type === 'attendance' && !empty($m['nama_nanny']))
                    <div class="flex items-center gap-1.5 mt-2">
                        <ion-icon name="person-outline" style="font-size:11px;color:#F59E0B;flex-shrink:0;"></ion-icon>
                        <span class="text-[10px] font-bold text-[#B45309] line-clamp-1">{{ $m['nama_nanny'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>