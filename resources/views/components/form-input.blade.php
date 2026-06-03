@props([
    'type' => 'text',
    'name' => '',
    'placeholder' => '',
    'value' => '',
    'icon' => null,
    'iconColor' => '#8B46D3',
    'required' => false,
    'autocomplete' => null,
    'extraClass' => '',
    'showPasswordToggle' => false,
])

<div class="anim d3 relative">
    @if($icon)
    <div class="absolute left-[18px] top-1/2 -translate-y-1/2 z-10">
        <svg class="w-[18px] h-[18px] text-[{{ $iconColor }}]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    </div>
    @endif

    @if($type === 'select')
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="pill-input {{ $extraClass }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
        {{ $slot }}
    </select>
    @else
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        autocomplete="{{ $autocomplete ?? $name }}"
        class="pill-input @if($showPasswordToggle) pr-14 @endif {{ $extraClass }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    />
    @endif

    @if($showPasswordToggle)
    <button type="button" id="togglePassword"
            class="absolute right-[18px] top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#8B46D3] transition-colors">
        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        </svg>
        <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
    </button>
    @endif
</div>

@push('scripts')
@if($showPasswordToggle)
<script>
(function() {
    const toggleBtn  = document.getElementById('togglePassword');
    const passwordEl = document.getElementById('{{ $name }}');
    const eyeIcon    = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');
    if (toggleBtn && passwordEl) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = passwordEl.type === 'password';
            passwordEl.type = isHidden ? 'text' : 'password';
            if (eyeIcon && eyeOffIcon) {
                eyeIcon.classList.toggle('hidden', isHidden);
                eyeOffIcon.classList.toggle('hidden', !isHidden);
            }
        });
    }
})();
</script>
@endif
@endpush
