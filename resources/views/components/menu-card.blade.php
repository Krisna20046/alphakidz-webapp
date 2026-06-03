@props([
    'id' => '',
    'title' => '',
    'icon' => null,
    'iconColor' => '#8B46D3',
    'iconBg' => '#EDE9FE',
    'label' => '',
    'subtitle' => '',
    'href' => '#',
    'delay' => 0,
    'badge' => null,
    'badgeColor' => null,
])

@php
    $transitionDelay = $delay * 0.05;
@endphp

<a href="{{ $href }}"
   class="menu-card block"
   style="animation: slideUp 0.35s ease {{ $transitionDelay }}s both; opacity:0;">
    <div class="bg-[#FAFAFA] rounded-[14px] flex items-center gap-[14px] px-[14px] py-[13px] shadow-[0_2px_10px_rgba(0,0,0,0.08)] border border-[#EAE6F5] {{ $attributes->get('class') }}">
        @if($icon)
        <div class="w-[48px] h-[48px] rounded-[14px] flex items-center justify-center shrink-0"
             style="background:{{ $iconBg }};">
            <ion-icon name="{{ $icon }}" style="font-size:22px;color:{{ $iconColor }};"></ion-icon>
        </div>
        @endif
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <p class="text-[#1E1B2E] text-[14px] font-bold leading-tight">{{ $label }}</p>
                @if($badge)
                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $badgeColor ?? 'bg-[#EDE9FE] text-[#8B46D3]' }}">{{ $badge }}</span>
                @endif
            </div>
            @if($subtitle)
            <p class="text-[#9CA3AF] text-[11px] font-medium mt-[3px] leading-snug">{{ $subtitle }}</p>
            @endif
        </div>
        <ion-icon name="chevron-forward" style="font-size:16px;color:#C4B5FD;" class="shrink-0"></ion-icon>
    </div>
</a>
