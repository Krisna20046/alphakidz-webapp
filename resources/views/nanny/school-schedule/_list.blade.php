@php
    $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
@endphp

<div id="listWrapper" data-day="{{ $activeDay }}" data-page="{{ $pagination['current_page'] ?? 1 }}">

    {{-- Result count + day filter button --}}
    <div class="flex items-center justify-between anim delay-2 relative z-10">
        <p class="text-xs font-bold text-[#8B86A5]">
            Showing <span class="text-[#8B46D3]">{{ count($schedules) }}</span> schedules
        </p>

        <div class="relative">
            <button type="button" onclick="toggleDayFilter()"
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-xs font-bold">
                <ion-icon name="filter-outline" style="font-size:14px;"></ion-icon>
                {{ $activeDay === '' ? 'All Days' : ucfirst($activeDay) }}
                <ion-icon name="chevron-down-outline" style="font-size:12px;"></ion-icon>
            </button>
            <div id="dayFilterMenu" class="hidden absolute right-0 top-[calc(100%+6px)] w-40 bg-white rounded-2xl border border-[#EDE9FE] shadow-xl z-30 py-1.5 max-h-64 overflow-y-auto">
                <a href="#" data-day=""
                   onclick="filterByDay(event,'')"
                   class="block px-4 py-2 text-xs font-bold {{ $activeDay === '' ? 'text-[#8B46D3] bg-[#F8F7FF]' : 'text-[#8B86A5]' }}">
                    All Days
                </a>
                @foreach($days as $d)
                <a href="#" data-day="{{ $d }}"
                   onclick="filterByDay(event,'{{ $d }}')"
                   class="block px-4 py-2 text-xs font-bold {{ $activeDay === $d ? 'text-[#8B46D3] bg-[#F8F7FF]' : 'text-[#8B86A5]' }}">
                    {{ ucfirst($d) }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($pagination)
    <div class="flex items-center justify-end anim delay-2 mt-3">
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="goToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="goToPage({{ $pagination['current_page'] + 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
    </div>
    @endif

    {{-- Schedule list --}}
    @if(count($schedules) === 0)
    <div class="flex flex-col items-center py-16 anim delay-3">
        <div class="float-anim w-20 h-20 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
            <ion-icon name="calendar-outline" style="font-size:36px;color:#C4B5FD;"></ion-icon>
        </div>
        <h3 class="text-[#1E1B2E] font-extrabold text-base mb-1">No schedules yet</h3>
        <p class="text-[#8B86A5] text-xs text-center">Add a school schedule for your child</p>
        <a href="{{ route('school-schedule.create') }}"
           class="mt-4 px-5 py-2 rounded-xl bg-[#8B46D3] text-white text-sm font-bold">Add Schedule</a>
    </div>
    @else
    <div class="space-y-3 anim delay-3 mt-3">
        @foreach($schedules as $s)
        @php
            $color = $s['subject']['color'] ?? '#8B46D3';
            $subjName = $s['subject']['name'] ?? 'Subject';
            $subjIcon = $s['subject']['icon'] ?? '';
            $childName = $childNames[$s['id_anak']] ?? 'Child';
        @endphp
        <div class="bg-white rounded-2xl p-4 border border-[#DDD6EF]">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 text-white text-xl"
                     style="background:{{ $color }};">
                    @if(!empty($subjIcon) && !str_starts_with($subjIcon, 'http'))
                        <ion-icon name="{{ $subjIcon }}" style="font-size:20px;"></ion-icon>
                    @else
                        {{ strtoupper(substr($subjName,0,1)) }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[#1E1B2E] font-bold text-sm truncate">{{ $subjName }}</p>
                    <p class="text-[#8B86A5] text-xs font-semibold mt-0.5">{{ $childName }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[#8B46D3] font-extrabold text-sm">{{ substr($s['start_time'] ?? '',0,5) }}–{{ substr($s['end_time'] ?? '',0,5) }}</p>
                    <span class="text-[10px] font-bold text-[#8B86A5]">{{ ucfirst($s['day_of_week']) }}</span>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#EDE9FE]">
                @if(!empty($s['teacher_name']))
                <span class="flex items-center gap-1 text-[11px] font-bold text-[#8B86A5]">
                    <ion-icon name="person-outline" style="font-size:13px;"></ion-icon>{{ $s['teacher_name'] }}
                </span>
                @else
                <span></span>
                @endif
                <button type="button" onclick="openDetail({{ json_encode([
                    'id'=>$s['id'],'subject'=>$subjName,'color'=>$color,'child'=>$childName,
                    'day'=>ucfirst($s['day_of_week']),'start'=>$s['start_time']??'','end'=>$s['end_time']??'',
                    'teacher'=>$s['teacher_name']??'','notes'=>$s['notes']??'',
                    'editUrl'=>route('school-schedule.edit',$s['id'])
                ]) }})"
                    class="px-3 py-1.5 rounded-xl bg-[#EDE9FE] text-[#8B46D3] text-xs font-bold">
                    Details
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>