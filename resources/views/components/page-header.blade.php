@props([
    'title' => '',
    'subtitle' => '',
    'backRoute' => null,
    'backUrl' => null,
])

<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        @if($backRoute || $backUrl)
        <a href="{{ $backUrl ?? route($backRoute) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        @endif
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">{{ $title }}</span>
            @if($subtitle)
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</div>
