@extends('layouts.app')

@section('title', 'School Schedule')

@push('styles')
<style>
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    @keyframes spin { to{transform:rotate(360deg);} }
    .page-loader { animation:spin .6s linear infinite; }

    .modal-backdrop {
        position:fixed; inset:0; background:rgba(0,0,0,.55);
        display:flex; align-items:flex-end; justify-content:center;
        z-index:50; opacity:0; pointer-events:none; transition:opacity .25s ease;
    }
    .modal-backdrop.open { opacity:1; pointer-events:all; }
    .modal-sheet {
        background:#fff; width:100%; max-width:390px;
        border-radius:24px 24px 0 0; transform:translateY(100%);
        transition:transform .3s cubic-bezier(.4,0,.2,1);
        display:flex; flex-direction:column; max-height:85vh;
    }
    .modal-backdrop.open .modal-sheet { transform:translateY(0); }
    .handle-container { flex-shrink:0; background:white; border-radius:24px 24px 0 0; }
    .scrollable-content { overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch; }
    body.modal-open { overflow:hidden; }

    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }

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
</style>
@endpush

@php
    $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">School Schedule</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">
                @if($pagination) {{ $pagination['total'] }} schedules @else {{ count($schedules) }} schedules @endif
            </p>
        </div>
            <button type='button' onclick='openPreview()'
                class='ml-auto shrink-0 h-10 px-4 rounded-full bg-white text-[#8B46D3] text-xs font-extrabold flex items-center gap-1.5 shadow-lg'>
                <ion-icon name='calendar-number-outline' style='font-size:16px;'></ion-icon>
                Preview
            </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-4">

    {{-- Flash Toast --}}
    @if(session('success') || session('error'))
    <div id="toast" class="toast rounded-2xl px-4 py-3 flex items-center gap-3
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

    {{-- Schedule list + filter + pagination (AJAX-swappable) --}}
    @include('nanny.school-schedule._list', [
        'schedules'  => $schedules,
        'pagination' => $pagination,
        'childNames' => $childNames,
        'activeDay'  => $activeDay,
    ])

</div>

{{-- Page loading overlay --}}
<div id="pageLoader" class="hidden fixed inset-0 z-50 bg-white/60 flex items-center justify-center">
    <div class="flex items-center gap-3 bg-white rounded-2xl px-6 py-4 shadow-xl border border-[#EDE9FE]">
        <div class="page-loader w-5 h-5 border-2 border-[#EDE9FE] border-t-[#8B46D3] rounded-full"></div>
        <span class="text-sm font-bold text-[#8B46D3]">Loading...</span>
    </div>
</div>

{{-- FAB --}}
<div class="fixed bottom-[80px] right-[20px] sm:right-[calc(50%-175px)] z-30">
    <a href="{{ route('school-schedule.create') }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>

{{-- PREVIEW MODAL --}}
<div id='previewModal' class='preview-backdrop'>
    <div class='preview-sheet'>
        <div class='flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#EDE9FE] shrink-0'>
            <div>
                <h2 class='text-[#1E1B2E] text-lg font-extrabold'>Weekly Schedule</h2>
                <p class='text-[#8B86A5] text-xs font-bold'>Monday - Sunday</p>
            </div>
            <div class='flex items-center gap-2'>
                <button type='button' id='downloadBtn' onclick='downloadPreview()' class='h-9 px-3.5 rounded-xl bg-[#8B46D3] text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-[#8B46D3]/30 disabled:opacity-60 disabled:cursor-not-allowed' style='width:110px;'>
                    <span id='downloadLabel' class='flex items-center gap-1.5'>
                        <ion-icon name='download-outline' style='font-size:14px;'></ion-icon>
                        Download
                    </span>
                </button>
                <button type='button' onclick='closePreview()' class='w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center'>
                    <ion-icon name='close' style='font-size:16px;color:#8B46D3;'></ion-icon>
                </button>
            </div>
        </div>
        <div class='scrollable-content px-5 py-4'>
            <div id='previewCard' class='rounded-[28px] p-4 shadow-lg' style='background:linear-gradient(160deg,#FFF6FB 0%,#F3ECFF 55%,#E4F7FF 100%);'>
                @php
                    $previewChildren = collect($previewSchedules)->pluck('id_anak')->unique()->values()->toArray();

                    // Collect all unique time slots (start-end), sorted so the
                    // earliest start_time always appears in the leftmost column.
                    $timeSlots = collect($previewSchedules)
                        ->map(function ($s) {
                            $start = substr($s['start_time'] ?? '00:00:00', 0, 5);
                            $end = substr($s['end_time'] ?? '00:00:00', 0, 5);
                            return [
                                'slot' => $start . '-' . $end,
                                'sortKey' => (int) str_replace(':', '', $start),
                            ];
                        })
                        ->unique('slot')
                        ->sortBy('sortKey')
                        ->pluck('slot')
                        ->values()
                        ->toArray();

                    // Build grid: [day][timeSlot] = schedule.
                    // day_of_week is normalized to lowercase since $days is lowercase too.
                    $grid = [];
                    foreach ($previewSchedules as $ws) {
                        $slot = substr($ws['start_time'] ?? '', 0, 5) . '-' . substr($ws['end_time'] ?? '', 0, 5);
                        $dayKey = strtolower($ws['day_of_week'] ?? '');
                        $grid[$dayKey][$slot] = $ws;
                    }
                @endphp
                <div class='text-center mb-3'>
                    <h3 class='text-[#1E1B2E] text-lg font-extrabold'>Weekly Schedule</h3>
                    <p class='text-[11px] font-bold text-[#8B86A5]'>Monday - Sunday</p>
                </div>
                @if(empty($previewChildren))
                <p class='text-center text-[#8B86A5] text-sm font-bold py-8'>No schedule yet</p>
                @else
                <div class='mb-3'>
                    <p class='text-[10px] font-bold text-[#8B86A5] uppercase tracking-wider mb-1'>Child</p>
                    <div class='flex flex-wrap gap-1.5'>
                        @foreach($previewChildren as $cid)
                        <span class='px-3 py-1 rounded-full text-xs font-bold' style='background:#8B46D3;color:#fff;'>{{ $childNames[$cid] ?? 'Child' }}</span>
                        @endforeach
                    </div>
                </div>

                @if(empty($timeSlots))
                <p class='text-center text-[#8B86A5] text-sm font-bold py-8'>No schedule yet</p>
                @else
                <div class="overflow-x-auto">
                <table class='border-separate' style='border-spacing:2px; min-width:100%;'>
                    <thead>
                        <tr>
                            <th rowspan="2" class='text-[10px] font-extrabold text-[#8B46D3] border border-[#8B46D3] rounded-tl-xl px-2 py-2 align-middle bg-white'>Day</th>
                            <th colspan="{{ count($timeSlots) }}" class='text-[10px] font-extrabold text-[#8B46D3] border border-[#8B46D3] rounded-tr-xl py-1.5 bg-white'>Time</th>
                        </tr>
                        <tr>
                            @foreach($timeSlots as $slot)
                            <th class='text-[9px] font-extrabold text-[#8B46D3] border border-[#8B46D3] py-1.5 px-2 whitespace-nowrap bg-white'>{{ $slot }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $d)
                        <tr>
                            <td class='text-[10px] font-extrabold text-[#8B46D3] border border-[#8B46D3] text-center py-2 px-2 whitespace-nowrap bg-white'>{{ ucfirst($d) }}</td>
                            @foreach($timeSlots as $slot)
                            @php $cs = $grid[$d][$slot] ?? null; @endphp
                            <td class='text-[10px] font-bold border border-[#8B46D3] text-center py-2 px-2 whitespace-nowrap'>
                                @if($cs)
                                <div class='flex flex-col items-center justify-center gap-0.5'>
                                    <span class='w-2 h-2 rounded-full shrink-0' style='background:{{ $cs['subject']['color'] ?? '#8B46D3' }};'></span>
                                    <span class='text-[10px] font-extrabold' style='color:{{ $cs['subject']['color'] ?? '#1E1B2E' }};'>{{ $cs['subject']['name'] ?? 'Subject' }}</span>
                                    @if(!empty($cs['teacher_name']))
                                    <span class='text-[9px] font-bold text-[#8B86A5]'>{{ $cs['teacher_name'] }}</span>
                                    @endif
                                </div>
                                @else
                                <span class='text-[10px] font-bold text-[#C4B5FD]'>-</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div id="deleteConfirmModal" class="modal-backdrop">
    <div class="modal-sheet">
        <div class="handle-container">
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1.5 rounded-full bg-gray-200"></div></div>
        </div>
        <div class="scrollable-content">
            <div class="px-6 pt-2 pb-8 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                    <ion-icon name="trash-outline" style="font-size:26px;color:#F44336;"></ion-icon>
                </div>
                <h3 class="text-[#1E1B2E] text-lg font-extrabold mb-1">Delete Schedule?</h3>
                <p class="text-[#8B86A5] text-sm font-semibold mb-6">This schedule will be permanently removed.</p>
                <div class="flex gap-3 pb-16">
                    <button type="button" onclick="closeDeleteConfirm()"
                        class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold">Cancel</button>
                    <button type="button" onclick="doDelete()"
                        class="act-btn flex-1 py-4 rounded-2xl bg-red-500 text-white text-sm font-bold shadow-lg shadow-red-500/30">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL MODAL --}}
<div id="detailModal" class="modal-backdrop">
    <div class="modal-sheet">
        <div class="handle-container">
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1.5 rounded-full bg-gray-200"></div></div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#EDE9FE]">
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">Schedule Details</h2>
                <button onclick="closeDetail()" class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="close" style="font-size:18px;color:#8B46D3;"></ion-icon>
                </button>
            </div>
        </div>
        <div class="scrollable-content">
            <div class="px-5 py-4 space-y-4">
                <div class="flex items-center gap-4">
                    <div id="dIcon" class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p id="dSubject" class="text-[#1E1B2E] font-extrabold text-lg leading-tight"></p>
                        <p id="dChild" class="text-[#8B86A5] text-sm mt-0.5"></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="calendar-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div><p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Day</p>
                        <p id="dDay" class="text-[#1E1B2E] text-sm font-bold"></p></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="time-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div><p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Time</p>
                        <p id="dTime" class="text-[#1E1B2E] text-sm font-bold"></p></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EDE9FE] flex items-center justify-center shrink-0">
                            <ion-icon name="person-outline" style="font-size:15px;color:#8B46D3;"></ion-icon>
                        </div>
                        <div><p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider">Teacher</p>
                        <p id="dTeacher" class="text-[#1E1B2E] text-sm font-bold"></p></div>
                    </div>
                    <div class="p-3 rounded-2xl bg-[#F8F7FF] border border-[#EDE9FE]">
                        <p class="text-[10px] text-[#8B86A5] font-bold uppercase tracking-wider mb-1">Notes</p>
                        <p id="dNotes" class="text-[#1E1B2E] text-sm font-semibold"></p>
                    </div>
                </div>
            </div>
            <div class="px-5 pb-20 space-y-3">
                <a id="dEditBtn" href="#"
                   class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-[#8B46D3] text-white font-bold text-sm shadow-lg shadow-[#8B46D3]/30">
                    <ion-icon name="create-outline" style="font-size:18px;"></ion-icon>
                    Edit Schedule
                </a>
                <button type="button" onclick="confirmDelete()"
                   class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 font-bold text-sm">
                    <ion-icon name="trash-outline" style="font-size:18px;"></ion-icon>
                    Delete Schedule
                </button>
                <form id="deleteForm" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Detail modal ─────────────────────────────────────────────────────────
function openDetail(s) {
    document.getElementById('dIcon').style.background = s.color;
    const icon = document.getElementById('dIcon');
    icon.innerHTML = '<ion-icon name="book-outline" style="font-size:30px;"></ion-icon>';
    document.getElementById('dSubject').textContent = s.subject;
    document.getElementById('dChild').textContent = 'Child: ' + s.child;
    document.getElementById('dDay').textContent = s.day;
    document.getElementById('dTime').textContent = (s.start ? s.start.slice(0,5) : '') + ' – ' + (s.end ? s.end.slice(0,5) : '');
    document.getElementById('dTeacher').textContent = s.teacher || '-';
    document.getElementById('dNotes').textContent = s.notes || '-';
    document.getElementById('dEditBtn').href = s.editUrl;
    document.getElementById('deleteForm').action = s.deleteUrl;
    document.getElementById('detailModal').classList.add('open');
    document.body.classList.add('modal-open');
}
function confirmDelete() {
    const form = document.getElementById('deleteForm');
    if (!form.action) return;
    // Close the detail modal first so the confirm sheet shows on its own.
    document.getElementById('detailModal').classList.remove('open');
    document.getElementById('deleteConfirmModal').classList.add('open');
    document.body.classList.add('modal-open');
}
function doDelete() {
    document.getElementById('deleteConfirmModal').classList.remove('open');
    document.getElementById('deleteForm').submit();
}
function closeDeleteConfirm() {
    document.getElementById('deleteConfirmModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}
document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteConfirm();
});
function closeDetail() {
    document.getElementById('detailModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});

// ── Preview modal ────────────────────────────────────────────────────────
function openPreview() {
    document.getElementById('previewModal').classList.add('open');
    document.body.classList.add('modal-open');
}
function closePreview() {
    document.getElementById('previewModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}
function downloadPreview() {
    const btn = document.getElementById('downloadBtn');
    const label = document.getElementById('downloadLabel');
    if (btn && btn.disabled) return;
    const setLoading = (on) => {
        if (!btn) return;
        btn.disabled = on;
        if (label) label.innerHTML = on
            ? '<span class="w-3.5 h-3.5 border-2 border-white/40 border-t-white rounded-full" style="animation:spin .6s linear infinite;"></span> Downloading...'
            : '<ion-icon name="download-outline" style="font-size:14px;"></ion-icon> Download';
    };
    setLoading(true);
    const capture = () => {
        const done = () => setLoading(false);
        const src = document.getElementById('previewCard');
        const clone = src.cloneNode(true);
        clone.style.cssText += ';position:fixed;left:0;top:0;z-index:-100;width:max-content;min-width:430px;';
        clone.style.maxWidth = 'none';
        clone.querySelectorAll('.overflow-x-auto').forEach(n => { n.style.overflow = 'visible'; });
        document.body.appendChild(clone);

        // Double rAF so the clone is fully laid out before measuring.
        requestAnimationFrame(() => requestAnimationFrame(() => {
            const w = clone.scrollWidth, h = clone.scrollHeight;
            html2canvas(clone, {
                backgroundColor: '#fff',
                scale: 2,
                width: w,
                height: h,
                windowWidth: w,
                windowHeight: h,
            }).then(canvas => {
                clone.remove();
                done();
                const a = document.createElement('a');
                a.download = 'weekly-schedule.png';
                a.href = canvas.toDataURL('image/png');
                a.click();
            });
        }));
    };
    if (window.html2canvas) { capture(); return; }
    const scr = document.createElement('script');
    scr.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
    scr.onload = capture;
    scr.onerror = () => { done(); showAppAlert('Failed to load the library needed to download the image.'); };
    document.head.appendChild(scr);
}
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

// ── Toast ─────────────────────────────────────────────────────────────────
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);

// ── Day filter dropdown ──────────────────────────────────────────────────
function toggleDayFilter() {
    document.getElementById('dayFilterMenu').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const menu = document.getElementById('dayFilterMenu');
    if (!menu) return;
    const btn = e.target.closest('button[onclick="toggleDayFilter()"]');
    if (!menu.contains(e.target) && !btn) {
        menu.classList.add('hidden');
    }
});

// ── AJAX filter + pagination (no full page reload) ──────────────────────
let currentDay = '{{ $activeDay }}';

function filterByDay(e, day) {
    e.preventDefault();
    currentDay = day;
    loadSchedules(day, 1);
    document.getElementById('dayFilterMenu').classList.add('hidden');
}

function goToPage(page) {
    if (page < 1) return;
    loadSchedules(currentDay, page);
}

function loadSchedules(day, page) {
    const params = new URLSearchParams();
    if (day) params.set('day', day);
    params.set('page', page);

    showPageLoader(true);

    fetch(`{{ route('school-schedule.index') }}?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if (!res.ok) throw new Error('Request failed');
        return res.text();
    })
    .then(html => {
        const container = document.getElementById('listWrapper');
        if (container) {
            container.outerHTML = html;
        }
        // update URL without reload
        const url = new URL(window.location);
        if (day) { url.searchParams.set('day', day); } else { url.searchParams.delete('day'); }
        url.searchParams.set('page', page);
        window.history.pushState({}, '', url);
    })
    .catch(() => {
        showAppAlert('Failed to load schedules.');
    })
    .finally(() => showPageLoader(false));
}

function showPageLoader(show) {
    const el = document.getElementById('pageLoader');
    if (!el) return;
    el.classList.toggle('hidden', !show);
}

// Support browser back/forward buttons
window.addEventListener('popstate', function() {
    const url = new URL(window.location);
    const day = url.searchParams.get('day') || '';
    const page = parseInt(url.searchParams.get('page') || '1', 10);
    currentDay = day;
    loadSchedules(day, page);
});
</script>
@endpush