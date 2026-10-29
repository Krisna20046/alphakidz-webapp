@php
    $priorityColor = function ($p) {
        return match ((int) $p) {
            1 => ['#DC2626', '#FEF2F2'],
            2 => ['#EA580C', '#FEF4E6'],
            3 => ['#D97706', '#FEF3C7'],
            4 => ['#0E9F6E', '#F0FDF4'],
            5 => ['#6B7280', '#F3F4F6'],
            default => ['#8B46D3', '#EDE9FE'],
        };
    };
@endphp

<div id="contactList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} contacts
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="ecGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="ecGoToPage({{ $pagination['current_page'] + 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
        @endif
    </div>

    @if(count($records) === 0)
    <div class="flex flex-col items-center py-8">
        <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
            <ion-icon name="call-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No emergency contacts yet.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        @php
            [$pColor, $pBg] = $priorityColor($r['priority_order'] ?? 1);
        @endphp
        <div class="rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $pBg }};">
                    <ion-icon name="{{ $r['priority_order'] == 1 ? 'warning-outline' : 'person-outline' }}" style="font-size:16px;color:{{ $pColor }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:{{ $pBg }};color:{{ $pColor }};">
                            {{ $r['priority_order'] ? 'Priority ' . $r['priority_order'] : 'Priority' }}
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">{{ $r['created_at'] }}</span>
                    </div>

                    <p class="text-[14px] font-extrabold text-[#1E1B2E] mt-1.5">{{ $r['name'] }}</p>

                    @if(!empty($r['relationship']))
                    <p class="text-[11px] font-semibold text-[#8B86A5]">{{ $r['relationship'] }}</p>
                    @endif

                    @if(!empty($r['phone']))
                    <div class="mt-1 flex items-center gap-1.5">
                        <ion-icon name="call-outline" style="font-size:12px;color:#16A34A;flex-shrink:0;"></ion-icon>
                        <a href="tel:{{ $r['phone'] }}" class="text-[12px] font-bold text-[#16A34A]">{{ $r['phone'] }}</a>
                    </div>
                    @endif

                    @if(!empty($r['id']))
                    {{-- Quick call shortcut --}}
                    <a href="tel:{{ $r['phone'] }}"
                       class="mt-2 inline-flex items-center gap-1.5 rounded-xl bg-[#16A34A] px-3 py-1.5 text-[11px] font-extrabold text-white">
                        <ion-icon name="call" style="font-size:12px;"></ion-icon>
                        Call Now
                    </a>
                    @if($canEdit)
                    <a href="{{ route('nanny-emergency-contacts-edit', [$idAnak, $r['id']]) }}"
                       class="mt-2 inline-flex items-center gap-1.5 rounded-xl bg-[#EDE9FE] px-3 py-1.5 text-[11px] font-extrabold text-[#8B46D3]">
                        <ion-icon name="create-outline" style="font-size:12px;"></ion-icon>
                        Edit
                    </a>
                    <button type="button" onclick="ecDeleteConfirm('{{ route('nanny-emergency-contacts-destroy', $r['id']) }}')"
                       aria-label="Hapus kontak" class="mt-2 inline-flex items-center gap-1.5 rounded-xl bg-[#FEF2F2] px-3 py-1.5 text-[11px] font-extrabold text-[#DC2626]">
                        <ion-icon name="trash-outline" style="font-size:12px;"></ion-icon>
                        Delete
                    </button>
                    @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>