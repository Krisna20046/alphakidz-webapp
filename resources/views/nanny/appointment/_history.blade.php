@php
    $typeMeta = function ($t) {
        return match ($t) {
            'extracurricular' => ['Aktivitas Ekstra', '#8B46D3', '#EDE9FE', 'trophy-outline'],
            'doctor'          => ['Konsultasi Dokter', '#DC2626', '#FEE2E2', 'medical-outline'],
            'therapy'         => ['Terapi', '#0E9F6E', '#D1FAE5', 'fitness-outline'],
            'school_event'    => ['Event Sekolah', '#D97706', '#FEF3C7', 'school-outline'],
            default           => ['Appointment', '#8B46D3', '#EDE9FE', 'calendar-outline'],
        };
    };
@endphp

<div id="appointmentList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} appointments
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="apGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="apGoToPage({{ $pagination['current_page'] + 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
        @endif
    </div>

    @if(count($records) === 0)
    <div class="flex flex-col items-center py-8">
        <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
            <ion-icon name="calendar-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No appointments yet.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        @php
            [$tLabel, $tColor, $tBg, $tIcon] = $typeMeta($r['type'] ?? '');
        @endphp
        <div class="rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $tBg }};">
                    <ion-icon name="{{ $tIcon }}" style="font-size:16px;color:{{ $tColor }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:{{ $tBg }};color:{{ $tColor }};">
                            {{ $tLabel }}
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">{{ $r['created_at'] }}</span>
                    </div>

                    <p class="text-[14px] font-extrabold text-[#1E1B2E] mt-1.5">{{ $r['title'] }}</p>

                    <div class="mt-1 flex items-center gap-1.5">
                        <ion-icon name="time-outline" style="font-size:12px;color:#8B46D3;flex-shrink:0;"></ion-icon>
                        <span class="text-[12px] font-bold text-[#4B4763]">{{ $r['schedule_at'] }}</span>
                    </div>

                    @if(!empty($r['location']))
                    <div class="mt-1 flex items-center gap-1.5">
                        <ion-icon name="location-outline" style="font-size:12px;color:#8B86A5;flex-shrink:0;"></ion-icon>
                        <span class="text-[12px] font-semibold text-[#8B86A5]">{{ $r['location'] }}</span>
                    </div>
                    @endif

                    @if(!empty($r['notes']))
                    <p class="mt-1.5 text-[12px] font-medium text-[#8B86A5] leading-relaxed">{{ $r['notes'] }}</p>
                    @endif

                    @if(isset($canEdit) && $canEdit)
                    <div class="flex items-center gap-2 mt-2">
                        <a href="{{ route('nanny-appointment-edit', [$idAnak, $r['id']]) }}"
                           class="inline-flex items-center gap-1.5 rounded-xl bg-[#EDE9FE] px-3 py-1.5 text-[11px] font-extrabold text-[#8B46D3]">
                            <ion-icon name="create-outline" style="font-size:12px;"></ion-icon>
                            Edit
                        </a>
                        <button type="button" onclick="apDeleteConfirm('{{ route('nanny-appointment-destroy', $r['id']) }}')"
                           aria-label="Hapus appointment" class="inline-flex items-center gap-1.5 rounded-xl bg-[#FEF2F2] px-3 py-1.5 text-[11px] font-extrabold text-[#DC2626]">
                            <ion-icon name="trash-outline" style="font-size:12px;"></ion-icon>
                            Delete
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>