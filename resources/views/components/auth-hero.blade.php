@props([
    'title' => '',
    'description' => '',
    'showBack' => true,
    'backRoute' => null,
    'backUrl' => null,
])
<div class="anim d1 mb-8">
    @if($showBack)
    <button onclick="history.back()"
            class="w-10 h-10 rounded-full bg-white/20 border border-white/30 flex items-center justify-center">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    @endif
</div>

<div class="anim d2">
    <h1 class="text-white text-[32px] font-extrabold leading-tight mb-3">{{ $title }}</h1>
    @if($description)
    <p class="text-white/80 text-[14px] font-semibold leading-relaxed max-w-[270px]">{{ $description }}</p>
    @endif
</div>
