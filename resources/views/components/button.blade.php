@props([
    'type' => 'submit',
    'variant' => 'primary',
    'loading' => false,
    'fullWidth' => true,
    'icon' => null,
    'iconPosition' => 'left',
    'id' => null,
])

@php
    $btnClass = match($variant) {
        'primary' => 'btn-primary',
        'google' => 'btn-google',
        'danger' => 'btn-danger',
        'outline' => 'btn-outline',
        default => 'btn-primary',
    };
    $widthClass = $fullWidth ? 'w-full' : '';
@endphp

<button
    type="{{ $type }}"
    id="{{ $id }}"
    class="{{ $btnClass }} {{ $widthClass }} flex items-center justify-center gap-2"
    {{ $attributes }}
>
    @if($loading)
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span>{{ $slot->isEmpty() ? 'Loading...' : $slot }}</span>
    @else
        @if($icon && $iconPosition === 'left')
            @if(str_contains($icon, 'ion-icon'))
                {!! $icon !!}
            @else
                <ion-icon name="{{ $icon }}" style="font-size:16px;"></ion-icon>
            @endif
        @endif
        <span>{{ $slot }}</span>
        @if($icon && $iconPosition === 'right')
            @if(str_contains($icon, 'ion-icon'))
                {!! $icon !!}
            @else
                <ion-icon name="{{ $icon }}" style="font-size:16px;"></ion-icon>
            @endif
        @endif
    @endif
</button>

@push('styles')
<style>
    .btn-primary {
        background: linear-gradient(135deg, #7B2FBE 0%, #9B46D3 100%);
        border-radius: 50px;
        color: #fff;
        font-weight: 800;
        font-size: 16px;
        padding: 17px;
        transition: transform 0.15s, box-shadow 0.2s;
        box-shadow: 0 8px 24px rgba(123,47,190,0.40);
    }
    .btn-primary:active { transform: scale(0.97); }
    .btn-primary:disabled { opacity: 0.75; cursor: not-allowed; }

    .btn-google {
        background: #F0EFEF;
        border-radius: 50px;
        color: #444;
        font-weight: 700;
        font-size: 15px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background 0.15s, opacity 0.15s;
    }
    .btn-google:active { background: #E4E2E2; }
    .btn-google:disabled { opacity: 0.7; cursor: not-allowed; }

    .btn-danger {
        background: #EF4444;
        border-radius: 50px;
        color: #fff;
        font-weight: 800;
        font-size: 16px;
        padding: 17px;
        transition: transform 0.15s;
    }
    .btn-danger:active { transform: scale(0.97); }

    .btn-outline {
        background: transparent;
        border: 2px solid #EDE9FE;
        border-radius: 50px;
        color: #8B46D3;
        font-weight: 700;
        font-size: 15px;
        padding: 15px;
        transition: background 0.15s;
    }
    .btn-outline:active { background: #EDE9FE; }
</style>
@endpush
