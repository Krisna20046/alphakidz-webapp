@props([
    'id' => '',
    'show' => false,
    'maxWidth' => 'sm',
    'title' => '',
])

@php
    $maxWidthClass = match($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        default => 'max-w-sm',
    };
@endphp

<div id="{{ $id }}"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 hidden opacity-0 px-5 pb-8 sm:pb-0 transition-opacity duration-200 ease"
     {{ $attributes->wire('key') }}>
    <div id="{{ $id }}Box"
         class="w-full {{ $maxWidthClass }} bg-white rounded-[28px] p-6 shadow-2xl scale-90 transition-transform duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]">
        {{ $slot }}
    </div>
</div>
