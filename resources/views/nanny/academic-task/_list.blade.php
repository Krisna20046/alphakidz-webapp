@php
    $statusMeta = [
        'pending'     => ['label' => 'Pending',       'bg' => 'bg-[#FFF7ED]',   'text' => 'text-[#EA580C]',   'dot' => '#F59E0B'],
        'in_progress' => ['label' => 'In Progress',   'bg' => 'bg-[#EEF2FF]',   'text' => 'text-[#4F46E5]',   'dot' => '#6366F1'],
        'completed'   => ['label' => 'Completed',     'bg' => 'bg-[#F0FDF4]',   'text' => 'text-[#16A34A]',   'dot' => '#22C55E'],
        'overdue'     => ['label' => 'Overdue',       'bg' => 'bg-[#FEF2F2]',   'text' => 'text-[#DC2626]',   'dot' => '#EF4444'],
        'cancelled'   => ['label' => 'Cancelled',     'bg' => 'bg-[#F3F4F6]',   'text' => 'text-[#6B7280]',   'dot' => '#9CA3AF'],
    ];
    $typeMeta = [
        'homework' => ['label' => 'Homework', 'icon' => 'book-outline'],
        'project'  => ['label' => 'Project',  'icon' => 'construct-outline'],
        'exam'     => ['label' => 'Exam',     'icon' => 'document-text-outline'],
    ];
    $typeLabels = ['homework' => 'Homework', 'project' => 'Project', 'exam' => 'Exam'];
@endphp

<div id="listWrapper" data-status="{{ $status }}" data-type="{{ $type }}" data-subject="{{ $subject }}">

    {{-- Count + filter button --}}
    <div class="flex items-center justify-between anim delay-2 relative z-10">
        <p class="text-xs font-bold text-[#8B86A5]">
            Showing <span class="text-[#8B46D3]">{{ count($tasks) }}</span> tasks
        </p>
        <div class="relative">
            <button type="button" onclick="toggleFilterMenu()"
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-xs font-bold">
                <ion-icon name="filter-outline" style="font-size:14px;"></ion-icon>
                {{ $status !== '' || $type !== '' || $subject !== '' ? 'Filtered' : 'Filter' }}
                <ion-icon name="chevron-down-outline" style="font-size:12px;"></ion-icon>
            </button>
            <div id="filterMenu" class="hidden absolute right-0 top-[calc(100%+6px)] w-44 bg-white rounded-2xl border border-[#EDE9FE] shadow-xl z-30 py-2 px-3 space-y-3">
                <div>
                    <p class="text-[10px] font-extrabold text-[#8B86A5] uppercase tracking-wider mb-1.5">Status</p>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="#" data-status="" onclick="filterByStatus(event,'')"
                           class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $status === '' ? 'bg-[#8B46D3] text-white' : 'bg-[#EDE9FE] text-[#8B86A5]' }}">All</a>
                        @foreach($statusMeta as $key => $m)
                        <a href="#" data-status="{{ $key }}" onclick="filterByStatus(event,'{{ $key }}')"
                           class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $status === $key ? 'bg-[#8B46D3] text-white' : 'bg-[#EDE9FE] text-[#8B86A5]' }}">{{ $m['label'] }}</a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold text-[#8B86A5] uppercase tracking-wider mb-1.5">Type</p>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="#" data-type="" onclick="filterByType(event,'')"
                           class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $type === '' ? 'bg-[#8B46D3] text-white' : 'bg-[#EDE9FE] text-[#8B86A5]' }}">All</a>
                        @foreach($typeLabels as $key => $label)
                        <a href="#" data-type="{{ $key }}" onclick="filterByType(event,'{{ $key }}')"
                           class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $type === $key ? 'bg-[#8B46D3] text-white' : 'bg-[#EDE9FE] text-[#8B86A5]' }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
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

    {{-- List --}}
    @if(count($tasks) === 0)
    <div class="flex flex-col items-center py-16 anim delay-3">
        <div class="float-anim w-20 h-20 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
            <ion-icon name="clipboard-outline" style="font-size:36px;color:#C4B5FD;"></ion-icon>
        </div>
        <h3 class="text-[#1E1B2E] font-extrabold text-base mb-1">No tasks yet</h3>
        <p class="text-[#8B86A5] text-xs text-center">Track homework, projects & exams<br>for your child</p>
        <a href="{{ route('academic-task.create') }}"
           class="mt-4 px-5 py-2 rounded-xl bg-[#8B46D3] text-white text-sm font-bold">Add Task</a>
    </div>
    @else
    <div class="space-y-3 anim delay-3 mt-3">
        @foreach($tasks as $t)
        @php
            $st = $statusMeta[$t['status']] ?? $statusMeta['pending'];
            $ty = $typeMeta[$t['type']] ?? $typeMeta['homework'];
            $subj = $t['subject']['name'] ?? 'Subject';
            $color = $t['subject']['color'] ?? '#8B46D3';
            $deadline = $t['deadline'] ? date('d M Y', strtotime($t['deadline'])) : 'No deadline';
        @endphp
        <a href="{{ route('academic-task.show', $t['id']) }}" class="block no-underline">
        <div class="bg-white rounded-2xl p-4 border border-[#DDD6EF]">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 text-white text-xl"
                     style="background:{{ $color }};">
                    <ion-icon name="{{ $ty['icon'] }}" style="font-size:20px;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-[#1E1B2E] font-bold text-sm truncate">{{ $t['title'] }}</p>
                    </div>
                    <p class="text-[#8B86A5] text-xs font-semibold mt-0.5">{{ $subj }} · {{ $typeLabels[$t['type']] ?? $t['type'] }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 {{ $st['bg'] }} {{ $st['text'] }}">{{ $st['label'] }}</span>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#EDE9FE]">
                <span class="flex items-center gap-1 text-[11px] font-bold text-[#8B86A5]">
                    <ion-icon name="calendar-outline" style="font-size:13px;"></ion-icon>{{ $deadline }}
                </span>
                @if($t['score'] !== null)
                <span class="flex items-center gap-1 text-[11px] font-extrabold text-[#F59E0B]">
                    <ion-icon name="star" style="font-size:13px;"></ion-icon>{{ $t['score'] }}
                </span>
                @endif
            </div>
        </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
