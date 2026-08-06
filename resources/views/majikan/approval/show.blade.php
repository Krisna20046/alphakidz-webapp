@extends('layouts.app')

@section('title', 'Approval - ' . $namaAnak)

@push('styles')
<style>
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
    .scrollable-content { overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch; }
    body.modal-open { overflow:hidden; }
    .task-card { cursor:pointer; transition:transform .1s ease; }
    .task-card:active { transform:scale(0.985); }
    .lightbox-backdrop {
        position:fixed; inset:0; background:rgba(0,0,0,.92);
        display:flex; align-items:center; justify-content:center;
        z-index:100; opacity:0; pointer-events:none; transition:opacity .2s ease; padding:20px;
    }
    .lightbox-backdrop.open { opacity:1; pointer-events:all; }
    .lightbox-backdrop img { max-width:100%; max-height:90vh; border-radius:16px; object-fit:contain; }
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
    $approvalMeta = [
        'approved' => ['label' => 'Approved', 'bg' => 'bg-[#F0FDF4]', 'text' => 'text-[#16A34A]', 'color' => '#22C55E', 'icon' => 'checkmark'],
        'rejected' => ['label' => 'Rejected', 'bg' => 'bg-[#FEF2F2]', 'text' => 'text-[#DC2626]', 'color' => '#EF4444', 'icon' => 'close'],
        'pending'  => ['label' => 'Awaiting', 'bg' => 'bg-[#FFF7ED]', 'text' => 'text-[#EA580C]', 'color' => '#F59E0B', 'icon' => 'time'],
        'comment'  => ['label' => 'Comment',  'bg' => 'bg-[#EEF2FF]', 'text' => 'text-[#4F46E5]', 'color' => '#6366F1', 'icon' => 'chatbubble'],
    ];
    $typeLabels = ['homework' => 'Homework', 'project' => 'Project', 'exam' => 'Exam'];
    $progressByTask = collect($tasks)->mapWithKeys(function ($t) {
        $list = collect($t['progress'] ?? [])->map(function ($p) {
            return [
                'pct'   => $p['progress_percentage'] ?? 0,
                'note'  => $p['note'] ?? '',
                'photo' => $p['photo'] ?? '',
                'at'    => !empty($p['created_at']) ? date('d M Y, H:i', strtotime($p['created_at'])) : '',
            ];
        })->values()->toArray();
        return [(int) $t['id'] => $list];
    })->toArray();
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('majikan-approval') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Approval</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @if(count($anakList) > 1)
        <div class="relative shrink-0">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('majikan-approval-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @endif
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar flex flex-col gap-5">

    {{-- Tasks to review --}}
    <div class="anim delay-2">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">Tasks</span>
            <span class="text-xs font-bold text-[#8B86A5]">{{ count($tasks) }} tasks</span>
        </div>

        @if(count($tasks) === 0)
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

                // Status approval task ini — gabungkan status task + riwayat terbaru
                $review = $historyByTask[(int) $t['id']] ?? null;
                $lastAction = $review['action'] ?? null;

                if (($t['status'] ?? '') === 'completed') {
                    // Task selesai: menunggu approval kecuali sudah di-approve terakhir
                    $decision = ($lastAction === 'approve') ? 'approved' : 'pending';
                } else {
                    // Task belum selesai: pending, atau 'rejected' bila pernah ditolak (sedang direvisi nanny)
                    $decision = ($lastAction === 'reject') ? 'rejected' : 'pending';
                }
                $ap = $approvalMeta[$decision] ?? $approvalMeta['pending'];
            @endphp
            <div class="task-card bg-white rounded-2xl p-4 border border-[#DDD6EF]" onclick="openProgress({{ (int) $t['id'] }})">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 text-white text-lg"
                         style="background:{{ $color }};">
                        <ion-icon name="{{ $t['type'] === 'exam' ? 'document-text-outline' : ($t['type'] === 'project' ? 'construct-outline' : 'book-outline') }}" style="font-size:19px;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[#1E1B2E] font-bold text-sm truncate">{{ $t['title'] }}</p>
                        <p class="text-[#8B86A5] text-xs font-semibold mt-0.5">{{ $subj }} · {{ $typeLabels[$t['type']] ?? $t['type'] }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 {{ $ap['bg'] }} {{ $ap['text'] }}">{{ $ap['label'] }}</span>
                </div>

                @if($t['status'] !== 'completed')
                <div class="text-[11px] font-bold text-[#8B86A5] mt-2">
                    Status: <span class="{{ $st['text'] }}">{{ $st['label'] }}</span> — tunggu nanny menyelesaikan tugas
                </div>
                @else
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
                        <button type="button" onclick="event.stopPropagation();openLightbox('{{ $t['attachment'] }}')"
                           class="w-6 h-6 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                            <ion-icon name="image-outline" style="font-size:13px;color:#8B46D3;"></ion-icon>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Review actions --}}
                @if($decision === 'approved')
                <div class="mt-3 flex items-center gap-1 text-[11px] font-bold text-[#16A34A]">
                    <ion-icon name="checkmark-circle" style="font-size:14px;"></ion-icon>
                    Anda menyetujui tugas ini
                </div>
                @elseif($decision === 'rejected')
                <div class="mt-3 flex items-start gap-1 text-[11px] font-bold text-[#DC2626]">
                    <ion-icon name="close-circle" style="font-size:14px;flex-shrink:0;margin-top:1px;"></ion-icon>
                    <span>Ditolak: {{ $review['comment'] ?? '' }}</span>
                </div>
                @else
                <div class="flex items-center gap-2 mt-3">
                    <button type="button" onclick="event.stopPropagation();openApprove({{ $t['id'] }})"
                        class="flex-1 h-10 rounded-full bg-[#F0FDF4] border border-[#16A34A] text-[#16A34A] text-xs font-extrabold flex items-center justify-center gap-1">
                        <ion-icon name="checkmark" style="font-size:15px;"></ion-icon> Approve
                    </button>
                    <button type="button" onclick="event.stopPropagation();openReject({{ $t['id'] }})"
                        class="flex-1 h-10 rounded-full bg-[#FEF2F2] border border-[#DC2626] text-[#DC2626] text-xs font-extrabold flex items-center justify-center gap-1">
                        <ion-icon name="close" style="font-size:15px;"></ion-icon> Reject
                    </button>
                </div>
                @endif
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Approval History --}}
    <div class="anim delay-3">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">History</span>
            <span class="text-xs font-bold text-[#8B86A5]">{{ count($history) }} entries</span>
        </div>

        @if(count($history) === 0)
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-8 flex flex-col items-center">
            <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
                <ion-icon name="time-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-xs font-semibold">No approval activity yet.</p>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 space-y-3">
            @foreach($history as $h)
            @php
                $d = $h['decision'] ?? 'comment';
                $ap = $approvalMeta[$d] ?? $approvalMeta['comment'];
            @endphp
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $ap['bg'] }} {{ $ap['text'] }}">
                    <ion-icon name="{{ $ap['icon'] }}-outline" style="font-size:15px;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-extrabold text-[#1E1B2E]">{{ $ap['label'] }}</span>
                        @if(!empty($h['creator_name']))
                        <span class="text-[10px] font-bold text-[#8B86A5]">by {{ $h['creator_name'] }}</span>
                        @endif
                    </div>
                    @if(!empty($h['comment']))
                    <p class="text-xs text-[#4B4563] font-medium mt-0.5">{{ $h['comment'] }}</p>
                    @endif
                    <p class="text-[10px] font-bold text-[#C4B5FD] mt-0.5">
                        {{ isset($h['created_at']) ? date('d M Y, H:i', strtotime($h['created_at'])) : '' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- Approve modal --}}
<div id="approveModal" class="preview-backdrop">
    <div class="preview-sheet">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#EDE9FE] shrink-0">
            <div>
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">Approve Task</h2>
                <p class="text-[#8B86A5] text-xs font-bold">Setujui tugas akademik anak</p>
            </div>
            <button type="button" onclick="closeModal('approveModal')" class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="scrollable-content px-5 py-4">
            <form method="POST" action="{{ route('majikan-approval-approve') }}">
                @csrf
                <input type="hidden" name="id_anak" value="{{ $idAnak }}">
                <input type="hidden" name="task_id" id="approveTaskId" value="">
                <label class="text-xs font-bold text-[#1E1B2E] block mb-1">Comment (optional)</label>
                <textarea name="comment" rows="3"
                    class="w-full rounded-2xl border border-[#DDD6EF] p-3 text-sm outline-none focus:border-[#8B46D3]"
                    placeholder="Tambah catatan (opsional)"></textarea>
                <button type="submit"
                    class="mt-4 w-full h-12 rounded-full bg-[#16A34A] text-white font-extrabold flex items-center justify-center gap-2 shadow-lg shadow-[#16A34A]/30">
                    <ion-icon name="checkmark" style="font-size:18px;"></ion-icon> Approve
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="preview-backdrop">
    <div class="preview-sheet">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#EDE9FE] shrink-0">
            <div>
                <h2 class="text-[#1E1B2E] text-lg font-extrabold">Reject Task</h2>
                <p class="text-[#8B86A5] text-xs font-bold">Alasan wajib diisi</p>
            </div>
            <button type="button" onclick="closeModal('rejectModal')" class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="scrollable-content px-5 py-4">
            <form method="POST" action="{{ route('majikan-approval-reject') }}">
                @csrf
                <input type="hidden" name="id_anak" value="{{ $idAnak }}">
                <input type="hidden" name="task_id" id="rejectTaskId" value="">
                <div class="text-xs font-bold text-[#1E1B2E] block mb-1">Alasan penolakan <span class="text-[#DC2626]">*</span></div>
                <textarea name="comment" id="rejectComment" rows="3" required
                    class="w-full rounded-2xl border border-[#DDD6EF] p-3 text-sm outline-none focus:border-[#DC2626]"
                    placeholder="Jelaskan alasan penolakan"></textarea>
                <button type="submit"
                    class="mt-4 w-full h-12 rounded-full bg-[#DC2626] text-white font-extrabold flex items-center justify-center gap-2 shadow-lg shadow-[#DC2626]/30">
                    <ion-icon name="close" style="font-size:18px;"></ion-icon> Reject
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Task Progress Detail Modal --}}
<div id="progressModal" class="preview-backdrop">
    <div class="preview-sheet">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#EDE9FE] shrink-0">
            <div>
                <h2 id="progressTitle" class="text-[#1E1B2E] text-lg font-extrabold">Progress</h2>
                <p id="progressSub" class="text-[#8B86A5] text-xs font-bold">Perkembangan tugas anak</p>
            </div>
            <button type="button" onclick="closeProgress()" class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </button>
        </div>
        <div class="scrollable-content px-5 py-4">
            <div id="progressEmpty" class="hidden text-center py-8">
                <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3 mx-auto">
                    <ion-icon name="time-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
                </div>
                <p class="text-[#8B86A5] text-xs font-semibold">No progress recorded for this task yet.</p>
            </div>
            <div id="progressList" class="space-y-3"></div>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" class="lightbox-backdrop" onclick="closeLightbox()">
    <img id="lightboxImg" src="" alt="">
</div>
@endsection

@push('scripts')
<script>
function openApprove(id) { document.getElementById('approveTaskId').value = id; openModal('approveModal'); }
function openReject(id)  { document.getElementById('rejectTaskId').value = id; openModal('rejectModal'); }
function openModal(id) { document.getElementById(id).classList.add('open'); document.body.classList.add('modal-open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.classList.remove('modal-open'); }
['approveModal','rejectModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e){ if(e.target===this) closeModal(id); });
});

const PROGRESS_BY_TASK = @json($progressByTask);
function openProgress(id) {
    const entries = PROGRESS_BY_TASK[id] || [];
    const list = document.getElementById('progressList');
    const empty = document.getElementById('progressEmpty');
    list.innerHTML = '';
    empty.classList.add('hidden');
    list.classList.remove('hidden');
    if (!entries.length) {
        list.classList.add('hidden');
        empty.classList.remove('hidden');
    }
    entries.forEach(p => {
        const row = document.createElement('div');
        row.className = 'bg-white rounded-2xl p-4 border border-[#DDD6EF] flex items-start gap-3';
        let media;
        if (p.photo) {
            media = '<img src="' + p.photo + '" alt="progress" onclick="event.stopPropagation();openLightbox(this.src)" class="w-16 h-16 rounded-xl object-cover shrink-0 border border-[#EDE9FE] cursor-zoom-in">';
        } else {
            media = '<div class="w-16 h-16 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0"><ion-icon name="image-outline" style="font-size:22px;color:#C4B5FD;"></ion-icon></div>';
        }
        row.innerHTML =
            media +
            '<div class="flex-1 min-w-0">' +
                '<div class="flex items-center gap-2">' +
                    '<div class="flex-1 h-2 bg-[#EDE9FE] rounded-full overflow-hidden">' +
                        '<div class="h-full rounded-full" style="width:' + p.pct + '%;background:linear-gradient(90deg,#8B46D3,#C084FC);"></div>' +
                    '</div>' +
                    '<p class="text-[11px] font-extrabold text-[#8B46D3]">' + p.pct + '%</p>' +
                '</div>' +
                (p.note ? '<p class="text-[#8B86A5] text-xs font-semibold mt-1.5">' + p.note + '</p>' : '') +
                (p.at ? '<p class="text-[10px] text-[#C4B5FD] font-bold mt-1">' + p.at + '</p>' : '') +
            '</div>';
        list.appendChild(row);
    });
    document.getElementById('progressModal').classList.add('open');
    document.body.classList.add('modal-open');
}
function closeProgress() {
    document.getElementById('progressModal').classList.remove('open');
    document.body.classList.remove('modal-open');
}
document.getElementById('progressModal').addEventListener('click', function(e){ if(e.target===this) closeProgress(); });

function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('open');
}
function closeLightbox() { document.getElementById('lightbox').classList.remove('open'); }

const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush