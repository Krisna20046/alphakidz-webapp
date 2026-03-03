{{--
    resources/views/partials/bottom-nav.blade.php
    Include di setiap halaman: @include('partials.bottom-nav', ['active' => 'home'])
    Nilai active: 'home' | 'artikel' | 'profil'
--}}

@php $active = $active ?? 'home'; @endphp

<nav class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-plum-soft/60 px-2">
    <div class="flex items-center justify-around h-[62px]">

        {{-- Beranda --}}
        <a href="{{ route('dashboard') }}"
           class="tab-item flex flex-col items-center justify-center gap-0.5 flex-1 h-full relative
                  {{ $active === 'home' ? 'tab-active' : 'tab-inactive' }}">
            @if($active === 'home')
                <div class="tab-indicator"></div>
            @endif
            <div class="tab-icon-wrap {{ $active === 'home' ? 'bg-plum-soft' : '' }}">
                <ion-icon name="{{ $active === 'home' ? 'home' : 'home-outline' }}"
                          style="font-size:20px; color:{{ $active === 'home' ? '#7B1E5A' : '#B895C8' }};"></ion-icon>
            </div>
            <span class="text-[10px] font-{{ $active === 'home' ? 'bold' : 'medium' }}"
                  style="color:{{ $active === 'home' ? '#7B1E5A' : '#B895C8' }};">
                Beranda
            </span>
        </a>

        {{-- Artikel --}}
        <a href="{{ route('artikel.index') }}"
           class="tab-item flex flex-col items-center justify-center gap-0.5 flex-1 h-full relative
                  {{ $active === 'artikel' ? 'tab-active' : 'tab-inactive' }}">
            @if($active === 'artikel')
                <div class="tab-indicator"></div>
            @endif
            <div class="tab-icon-wrap {{ $active === 'artikel' ? 'bg-plum-soft' : '' }}">
                <ion-icon name="{{ $active === 'artikel' ? 'book' : 'book-outline' }}"
                          style="font-size:20px; color:{{ $active === 'artikel' ? '#7B1E5A' : '#B895C8' }};"></ion-icon>
            </div>
            <span class="text-[10px] font-{{ $active === 'artikel' ? 'bold' : 'medium' }}"
                  style="color:{{ $active === 'artikel' ? '#7B1E5A' : '#B895C8' }};">
                Artikel
            </span>
        </a>

        {{-- Profil --}}
        <a href="{{ route('profil.index') }}"
           class="tab-item flex flex-col items-center justify-center gap-0.5 flex-1 h-full relative
                  {{ $active === 'profil' ? 'tab-active' : 'tab-inactive' }}">
            @if($active === 'profil')
                <div class="tab-indicator"></div>
            @endif
            <div class="tab-icon-wrap {{ $active === 'profil' ? 'bg-plum-soft' : '' }}">
                <ion-icon name="{{ $active === 'profil' ? 'person' : 'person-outline' }}"
                          style="font-size:20px; color:{{ $active === 'profil' ? '#7B1E5A' : '#B895C8' }};"></ion-icon>
            </div>
            <span class="text-[10px] font-{{ $active === 'profil' ? 'bold' : 'medium' }}"
                  style="color:{{ $active === 'profil' ? '#7B1E5A' : '#B895C8' }};">
                Profil
            </span>
        </a>

    </div>
</nav>

<style>
    /* Safe area bottom untuk notch iPhone */
    .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }

    .tab-item { transition: opacity 0.15s ease; }
    .tab-item:active { opacity: 0.7; }

    .tab-icon-wrap {
        width: 36px; height: 28px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px;
        transition: background 0.2s ease;
    }

    /* Active indicator pill di atas icon */
    .tab-indicator {
        position: absolute;
        top: 0; left: 50%;
        transform: translateX(-50%);
        width: 24px; height: 3px;
        background: #7B1E5A;
        border-radius: 0 0 4px 4px;
    }

    /* Animasi tap */
    .tab-active .tab-icon-wrap {
        animation: tabPop 0.25s cubic-bezier(0.34,1.56,0.64,1);
    }
    @keyframes tabPop {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
</style>