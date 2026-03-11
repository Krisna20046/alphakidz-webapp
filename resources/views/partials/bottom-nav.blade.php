{{--
    resources/views/partials/bottom-nav.blade.php
    Usage: @include('partials.bottom-nav', ['active' => 'home'])
    Active values: 'home' | 'artikel' | 'profil'
--}}

@php $active = $active ?? 'home'; @endphp

<nav class="bottom-nav">

    {{-- Home --}}
    <a href="{{ route('dashboard') }}"
       class="nav-item {{ $active === 'home' ? 'nav-active' : '' }}">
        @if($active === 'home')
            <div class="nav-icon-bg">
                <ion-icon name="home" style="font-size:22px;color:white;"></ion-icon>
            </div>
            <div class="nav-indicator"></div>
        @else
            <div class="nav-icon-normal">
                <ion-icon name="home-outline" style="font-size:22px;color:#9CA3AF;"></ion-icon>
            </div>
        @endif
        <span class="{{ $active === 'home' ? 'nav-label-active' : 'nav-label-normal' }}">
            Home
        </span>
    </a>

    {{-- Artikel --}}
    <a href="{{ route('artikel.index') }}"
       class="nav-item {{ $active === 'artikel' ? 'nav-active' : '' }}">
        @if($active === 'artikel')
            <div class="nav-icon-bg">
                <ion-icon name="book" style="font-size:22px;color:white;"></ion-icon>
            </div>
            <div class="nav-indicator"></div>
        @else
            <div class="nav-icon-normal">
                <ion-icon name="book-outline" style="font-size:22px;color:#9CA3AF;"></ion-icon>
            </div>
        @endif
        <span class="{{ $active === 'artikel' ? 'nav-label-active' : 'nav-label-normal' }}">
            Article
        </span>
    </a>

    {{-- Profil --}}
    <a href="{{ route('profil.index') }}"
       class="nav-item {{ $active === 'profil' ? 'nav-active' : '' }}">
        @if($active === 'profil')
            <div class="nav-icon-bg">
                <ion-icon name="person" style="font-size:22px;color:white;"></ion-icon>
            </div>
            <div class="nav-indicator"></div>
        @else
            <div class="nav-icon-normal">
                <ion-icon name="person-outline" style="font-size:22px;color:#9CA3AF;"></ion-icon>
            </div>
        @endif
        <span class="{{ $active === 'profil' ? 'nav-label-active' : 'nav-label-normal' }}">
            Profile
        </span>
    </a>

</nav>

<style>
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0; right: 0;
        background: #FFFFFF;
        border-top: 1px solid #F0EAFF;
        display: flex;
        align-items: center;
        height: 64px;
        z-index: 50;
        box-shadow: 0 -4px 20px rgba(124, 58, 237, 0.08);
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }

    /* On desktop phone frame, constrain to frame width */
    @media (min-width: 640px) {
        .bottom-nav {
            width: 390px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 0 0 44px 44px;
        }
    }

    .nav-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        text-decoration: none;
        position: relative;
        height: 100%;
        cursor: pointer;
        transition: opacity 0.15s ease;
    }
    .nav-item:active { opacity: 0.7; }

    /* Active floating circle */
    .nav-icon-bg {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: #7C3AED;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.4);
        position: absolute;
        top: -16px;
        animation: navPop 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes navPop {
        0%   { transform: scale(0.8); }
        60%  { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* Active underline indicator */
    .nav-indicator {
        position: absolute;
        bottom: 0;
        width: 24px; height: 3px;
        background: #7C3AED;
        border-radius: 3px 3px 0 0;
    }

    /* Inactive icon wrap */
    .nav-icon-normal {
        width: 24px; height: 24px;
        display: flex; align-items: center; justify-content: center;
    }

    /* Labels */
    .nav-label-active {
        font-family: 'Nunito', sans-serif;
        font-size: 10px;
        font-weight: 800;
        color: #7C3AED;
        margin-top: 30px; /* push down below the floating circle */
    }

    .nav-label-normal {
        font-family: 'Nunito', sans-serif;
        font-size: 10px;
        font-weight: 600;
        color: #9CA3AF;
    }
</style>