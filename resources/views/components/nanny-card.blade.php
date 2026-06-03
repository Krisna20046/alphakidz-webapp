@props([
    'id' => '',
    'nanny' => [],
    'index' => 0,
    'detailRoute' => null,
])

@php
    $isHired = !is_null($nanny['status_penugasan'] ?? null) && strtolower($nanny['status_penugasan']) === 'active';
    $badgeClass = $isHired ? 'badge-hired' : 'badge-available';
    $badgeText = $isHired ? 'HIRED' : 'AVAILABLE';
    $rating = $nanny['rating'] ?? '4.9';
    $reviews = $nanny['reviews'] ?? 42;
    $experience = ($nanny['pengalaman'] ?? 0) . ' years experience';
    $detailUrl = $detailRoute ? route($detailRoute, $nanny['id']) : '#';
@endphp

<a href="{{ $detailUrl }}"
   class="nanny-card block bg-white rounded-[14px] px-3 py-2.5 shadow-[0_2px_10px_rgba(0,0,0,0.10)] border border-[#EAE6F5]"
   style="animation: slideUp 0.35s ease {{ $index * 0.05 }}s both; opacity:0;">
    <div class="flex items-center gap-3">
        @if(!empty($nanny['foto']))
        <img src="{{ $nanny['foto'] }}"
             alt="{{ $nanny['name'] }}"
             class="w-[50px] h-[50px] rounded-[8px] object-cover bg-[#F3F0FD]"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
        <div class="w-[50px] h-[50px] rounded-[8px] items-center justify-center hidden bg-[#F3F0FD]">
            <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
        </div>
        @else
        <div class="w-[50px] h-[50px] rounded-[8px] flex items-center justify-center bg-[#F3F0FD]">
            <ion-icon name="person" style="font-size:24px;color:#8B46D3;"></ion-icon>
        </div>
        @endif

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <p class="text-[#1E1B2E] font-extrabold text-[15px] truncate">{{ $nanny['name'] }}</p>
                <span class="{{ $badgeClass }} text-[10px] font-extrabold px-2 py-1 rounded-full leading-none shrink-0">
                    {{ $badgeText }}
                </span>
            </div>
            <p class="text-[#8B86A5] text-[11px] italic font-semibold mt-0.5 truncate">"{{ $experience }}"</p>
        </div>
    </div>
</a>
