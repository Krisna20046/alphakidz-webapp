@php
$day   = (int) ($day ?? date('N'));
$dayNm = $dayNames[$day] ?? 'Hari ini';
@endphp

@if(empty($weekly))
<div class="flex flex-col items-center py-10">
    <div class="w-16 h-16 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
        <ion-icon name="repeat-outline" style="font-size:32px;color:#C4B5FD;"></ion-icon>
    </div>
    <p class="text-[#1E1B2E] text-sm font-extrabold mb-1">Belum ada Daily Rutin {{ $dayNm }}</p>
    <p class="text-[#8B86A5] text-xs font-semibold mb-4">Buat rutinitas untuk {{ $dayNm }} yang bisa diulang tiap minggu.</p>
    <a href="{{ route('nanny-daily-checklists-create', [$idAnak, 'day' => $day]) }}"
       class="inline-flex items-center gap-1.5 rounded-xl bg-[#8B46D3] px-4 py-2.5 text-xs font-extrabold text-white">
        <ion-icon name="add" style="font-size:14px;"></ion-icon> Buat Daily Rutin {{ $dayNm }}
    </a>
</div>
@else
<div class="flex flex-col gap-3">
    @foreach($weekly as $routine)
    <div class="rounded-2xl border border-[#EAE6F5] bg-white p-4"
         data-checklist-id="{{ $routine['id'] }}"
         data-checklist-title="{{ $routine['title'] }}">
        <div class="flex items-start justify-between mb-2">
            <div class="min-w-0">
                <p class="text-[14px] font-extrabold text-[#1E1B2E] line-clamp-1">{{ $routine['title'] }}</p>
                <p class="text-[10px] font-bold text-[#8B86A5] mt-0.5">
                    <span class="dc-completed-count">{{ $routine['completed_items'] ?? 0 }}</span>/<span class="dc-total-count">{{ $routine['total_items'] ?? 0 }}</span> selesai • <span class="dc-progress-pct">{{ intval($routine['progress_percent'] ?? 0) }}</span>%
                </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('nanny-daily-checklists-edit', [$idAnak, $routine['id']]) }}"
                   class="w-8 h-8 rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="create-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
                </a>
                <button type="button" onclick="dcDeleteConfirm('{{ route('nanny-daily-checklists-destroy', $routine['id']) }}')"
                   aria-label="Hapus checklist" class="w-8 h-8 rounded-xl bg-[#FEF2F2] flex items-center justify-center">
                    <ion-icon name="trash-outline" style="font-size:14px;color:#DC2626;"></ion-icon>
                </button>
            </div>
        </div>

        <div class="progress h-1.5 rounded-full bg-[#F1EEFA] overflow-hidden mb-3">
            <div class="h-full rounded-full bg-[#8B46D3] dc-progress-bar" style="width:{{ intval($routine['progress_percent'] ?? 0) }}%;"></div>
        </div>

        <div class="flex flex-col gap-1.5">
            @foreach(($routine['items'] ?? []) as $item)
            @php $done = !empty($item['is_completed']); @endphp
            <button type="button"
                class="dc-toggle w-full flex items-start gap-2.5 rounded-xl px-2.5 py-2 text-left transition-colors"
                data-item-id="{{ $item['id'] }}"
                data-checklist-id="{{ $routine['id'] }}"
                data-item-name="{{ $item['item_name'] }}"
                data-on="{{ $done ? '1' : '0' }}">
                <span class="dc-check w-5 h-5 rounded-md flex items-center justify-center shrink-0 mt-0.5 border-2"
                      data-on="{{ $done ? '1' : '0' }}">
                    <ion-icon name="checkmark" class="dc-check-icon" style="font-size:12px;color:#fff;"></ion-icon>
                </span>
                <span class="dc-item-text text-[13px] font-bold flex-1"
                      data-on="{{ $done ? '1' : '0' }}">
                    {{ $item['item_name'] }}
                </span>
            </button>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif

<p class="text-[10px] font-bold text-[#8B86A5] mt-3 text-center">
    Semua checklist di-<span class="underline">reset otomatis</span> saat ganti minggu.
</p>