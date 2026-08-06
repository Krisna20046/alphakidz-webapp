@extends('layouts.app')

@section('title', 'Tracking - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }

    .preview-backdrop {
        position:fixed; inset:0; background:rgba(0,0,0,.55);
        display:flex; align-items:center; justify-content:center;
        z-index:60; opacity:0; pointer-events:none; transition:opacity .25s ease; padding:20px;
    }
    .preview-backdrop.open { opacity:1; pointer-events:all; }
    .preview-sheet {
        background:#fff; width:100%; max-width:430px; max-height:88vh;
        border-radius:28px; display:flex; flex-direction:column; overflow:hidden;
        transform:scale(.92); transition:transform .25s ease;
    }
    .preview-backdrop.open .preview-sheet { transform:scale(1); }
    .scrollable-content { overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch; }
    body.modal-open { overflow:hidden; }
</style>
@endpush

@php
    $statusMeta = [
        'pending'     => ['label' => 'Pending',     'bg' => 'bg-[#FFF7ED]', 'text' => 'text-[#EA580C]', 'color' => '#F59E0B'],
        'in_progress' => ['label' => 'In Progress', 'bg' => 'bg-[#EEF2FF]', 'text' => 'text-[#4F46E5]', 'color' => '#6366F1'],
        'completed'   => ['label' => 'Completed',   'bg' => 'bg-[#F0FDF4]', 'text' => 'text-[#16A34A]', 'color' => '#22C55E'],
        'overdue'     => ['label' => 'Overdue',     'bg' => 'bg-[#FEF2F2]', 'text' => 'text-[#DC2626]', 'color' => '#EF4444'],
        'cancelled'   => ['label' => 'Cancelled',   'bg' => 'bg-[#F3F4F6]', 'text' => 'text-[#6B7280]', 'color' => '#9CA3AF'],
    ];
    $typeLabels = ['homework' => 'Homework', 'project' => 'Project', 'exam' => 'Exam'];
    $dayLabels = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday',
                  'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday'];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('majikan-tracking') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Tracking</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        {{-- Child selector dropdown --}}
        @if(count($anakList) > 1)
        <div class="relative shrink-0">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('majikan-tracking-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @endif
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-5">

    {{-- Stat cards --}}
    <div class="grid grid-cols-4 gap-2 anim delay-2">
        @php
            $stats = [
                ['label' => 'Total', 'value' => $total, 'bg' => 'bg-white', 'text' => 'text-[#8B46D3]', 'icon' => 'list'],
                ['label' => 'Doing', 'value' => $doing, 'bg' => 'bg-[#EEF2FF]', 'text' => 'text-[#4F46E5]', 'icon' => 'time'],
                ['label' => 'Done', 'value' => $done, 'bg' => 'bg-[#F0FDF4]', 'text' => 'text-[#16A34A]', 'icon' => 'checkmark'],
                ['label' => 'Overdue', 'value' => $overdue, 'bg' => 'bg-[#FEF2F2]', 'text' => 'text-[#DC2626]', 'icon' => 'alert'],
            ];
        @endphp
        @foreach($stats as $st)
        <div class="rounded-2xl border border-[#DDD6EF] p-3 flex flex-col items-center gap-1 {{ $st['bg'] }}">
            <ion-icon name="{{ $st['icon'] }}-outline" class="{{ $st['text'] }}" style="font-size:18px;"></ion-icon>
            <p class="text-lg font-extrabold {{ $st['text'] }}">{{ $st['value'] }}</p>
            <p class="text-[9px] font-bold text-[#8B86A5] uppercase tracking-wider">{{ $st['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Today's Schedule --}}
    <div class="anim delay-3">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">School Schedule</span>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-[#8B86A5]">{{ count($schedules) }} total</span>
                @if(count($schedules) > 0)
                <button type="button" onclick="openPreview()"
                    class="shrink-0 h-8 px-3 rounded-full bg-[#8B46D3] text-white text-[11px] font-extrabold flex items-center gap-1 shadow-md shadow-[#8B46D3]/30">
                    <ion-icon name="calendar-number-outline" style="font-size:14px;"></ion-icon>
                    Preview
                </button>
                @endif
            </div>
        </div>

        @php $totalSlots = collect($schedules)->count(); @endphp
        @if($totalSlots === 0)
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-8 flex flex-col items-center">
            <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
                <ion-icon name="calendar-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-xs font-semibold">No schedule recorded by nanny yet.</p>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4">
            <div class="space-y-1.5">
                @foreach($daysOrder as $day)
                @php $list = $scheduleByDay[$day] ?? []; @endphp
                @if(count($list) > 0)
                <div class="flex items-center gap-3 py-1.5">
                    <span class="w-20 shrink-0 text-[11px] font-extrabold text-[#8B46D3]">{{ $dayLabels[$day] }}</span>
                    <div class="flex flex-1 flex-wrap gap-1.5">
                        @foreach($list as $sch)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold"
                              style="background:{{ $sch['subject']['color'] ?? '#EDE9FE' }}20;border:1px solid {{ $sch['subject']['color'] ?? '#EDE9FE' }};color:{{ $sch['subject']['color'] ?? '#1E1B2E' }};">
                            {{ $sch['subject']['name'] ?? 'Subject' }}
                            · {{ substr($sch['start_time'] ?? '',0,5) }}-{{ substr($sch['end_time'] ?? '',0,5) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Academic Tasks --}}
    <div class="anim delay-3">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">Academic Tasks</span>
            <span class="text-xs font-bold text-[#8B86A5]">{{ $total }} tasks</span>
        </div>

        @if($total === 0)
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-8 flex flex-col items-center">
            <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
                <ion-icon name="clipboard-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-xs font-semibold">No tasks recorded by nanny yet.</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($tasks as $t)
            @php
                $st = $statusMeta[$t['status']] ?? $statusMeta['pending'];
                $subj = $t['subject']['name'] ?? 'Subject';
                $color = $t['subject']['color'] ?? '#8B46D3';
                $deadline = $t['deadline'] ? date('d M Y, H:i', strtotime($t['deadline'])) : 'No deadline';
                $progress = collect($t['progress'] ?? [])->sortByDesc('created_at')->first();
                $pct = $progress['progress_percentage'] ?? ($t['status'] === 'completed' ? 100 : 0);
            @endphp
            <div class="bg-white rounded-2xl p-4 border border-[#DDD6EF]">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 text-white text-lg"
                         style="background:{{ $color }};">
                        <ion-icon name="{{ $t['type'] === 'exam' ? 'document-text-outline' : ($t['type'] === 'project' ? 'construct-outline' : 'book-outline') }}" style="font-size:19px;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#1E1B2E] font-bold text-sm truncate">{{ $t['title'] }}</p>
                        <p class="text-[#8B86A5] text-xs font-semibold mt-0.5">{{ $subj }} · {{ $typeLabels[$t['type']] ?? $t['type'] }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 {{ $st['bg'] }} {{ $st['text'] }}">{{ $st['label'] }}</span>
                </div>

                {{-- Progress bar --}}
                <div class="flex items-center gap-2 mt-3">
                    <div class="flex-1 h-2 bg-[#EDE9FE] rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%;background:linear-gradient(90deg,#8B46D3,#C084FC);"></div>
                    </div>
                    <span class="text-[11px] font-extrabold text-[#8B46D3]">{{ $pct }}%</span>
                </div>

                <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#EDE9FE]">
                    <span class="flex items-center gap-1 text-[11px] font-bold text-[#8B86A5]">
                        <ion-icon name="calendar-outline" style="font-size:13px;"></ion-icon>{{ $deadline }}
                    </span>
                    <div class="flex items-center gap-1">
                        @if($t['score'] !== null)
                        <span class="flex items-center gap-1 text-[11px] font-extrabold text-[#F59E0B]">
                            <ion-icon name="star" style="font-size:13px;"></ion-icon>{{ $t['score'] }}
                        </span>
                        @endif
                        @if(!empty($t['attachment']))
                        <a href="{{ $t['attachment'] }}" target="_blank"
                           class="w-6 h-6 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                            <ion-icon name="image-outline" style="font-size:13px;color:#8B46D3;"></ion-icon>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- PREVIEW MODAL (tabel jadwal mingguan) --}}
<div id="previewModal" class="preview-backdrop">
    <div class="preview-sheet">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#EDE9FE] shrink-0">
            <div>
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">Weekly Schedule</h2>
                <p class="text-[#8B86A5] text-xs font-bold">Monday - Sunday</p>
            </div>
            <button type="button" onclick="closePreview()" class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="scrollable-content px-5 py-4">
            <div id="previewCard" class="rounded-[28px] p-4 shadow-lg" style="background:linear-gradient(160deg,#FFF6FB 0%,#F3ECFF 55%,#E4F7FF 100%);">
                @php
                    // All unique time slots (start-end), sorted so the earliest start_time is leftmost.
                    $previewTimeSlots = collect($schedules)
                        ->map(function ($s) {
                            $start = substr($s['start_time'] ?? '00:00:00', 0, 5);
                            $end   = substr($s['end_time'] ?? '00:00:00', 0, 5);
                            return [
                                'slot'    => $start . '-' . $end,
                                'sortKey' => (int) str_replace(':', '', $start),
                            ];
                        })
                        ->unique('slot')
                        ->sortBy('sortKey')
                        ->pluck('slot')
                        ->values()
                        ->toArray();

                    // Build grid: [day][timeSlot] = schedule.
                    $previewGrid = [];
                    foreach ($schedules as $ws) {
                        $slot = substr($ws['start_time'] ?? '', 0, 5) . '-' . substr($ws['end_time'] ?? '', 0, 5);
                        $dayKey = strtolower($ws['day_of_week'] ?? '');
                        $previewGrid[$dayKey][$slot] = $ws;
                    }
                @endphp
                <div class="text-center mb-3">
                    <h3 class="text-[#1E1B2E] text-lg font-extrabold">{{ $namaAnak }}</h3>
                    <p class="text-[11px] font-bold text-[#8B86A5]">Weekly Schedule</p>
                </div>
                @if(empty($previewTimeSlots))
                <p class="text-center text-[#8B86A5] text-sm font-bold py-8">No schedule yet</p>
                @else
                <div class="overflow-x-auto">
                    <table class="border-separate" style="border-spacing:2px; min-width:100%;">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-[10px] font-extrabold text-[#8B46D3] border border-[#8B46D3] rounded-tl-xl px-2 py-2 align-middle bg-white">Day</th>
                                <th colspan="{{ count($previewTimeSlots) }}" class="text-[10px] font-extrabold text-[#8B46D3] border border-[#8B46D3] rounded-tr-xl py-1.5 bg-white">Time</th>
                            </tr>
                            <tr>
                                @foreach($previewTimeSlots as $slot)
                                <th class="text-[9px] font-extrabold text-[#8B46D3] border border-[#8B46D3] py-1.5 px-2 whitespace-nowrap bg-white">{{ $slot }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daysOrder as $d)
                            <tr>
                                <td class="text-[10px] font-extrabold text-[#8B46D3] border border-[#8B46D3] text-center py-2 px-2 whitespace-nowrap bg-white">{{ $dayLabels[$d] }}</td>
                                @foreach($previewTimeSlots as $slot)
                                @php $cs = $previewGrid[$d][$slot] ?? null; @endphp
                                <td class="text-[10px] font-bold border border-[#8B46D3] text-center py-2 px-2 whitespace-nowrap">
                                    @if($cs)
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $cs['subject']['color'] ?? '#8B46D3' }};"></span>
                                        <span class="text-[10px] font-extrabold" style="color:{{ $cs['subject']['color'] ?? '#1E1B2E' }};">{{ $cs['subject']['name'] ?? 'Subject' }}</span>
                                        @if(!empty($cs['teacher_name']))
                                        <span class="text-[9px] font-bold text-[#8B86A5]">{{ $cs['teacher_name'] }}</span>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-[10px] font-bold text-[#C4B5FD]">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPreview() {
    document.getElementById('previewModal').classList.add('open');
    document.body.classList.add('modal-open');
}
function closePreview() {
    document.getElementById('previewModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush
