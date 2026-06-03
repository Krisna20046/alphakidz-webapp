@props([
    'title' => '',
    'description' => '',
    'icon' => null,
])

<div class="flex flex-col items-center pt-16 pb-10 px-8">
    @if($icon)
    <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-5">
        <ion-icon name="{{ $icon }}" style="font-size:44px;color:#C4B5FD;"></ion-icon>
    </div>
    @else
    {{ $iconSlot ?? '' }}
    @endif
    @if($title)
    <h3 class="text-[#1E1B2E] font-bold text-lg mb-2">{{ $title }}</h3>
    @endif
    @if($description)
    <p class="text-[#9CA3AF] text-sm text-center leading-relaxed">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
