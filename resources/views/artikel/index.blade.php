@extends('layouts.app')

@php $activeNav = 'artikel' @endphp

@section('title', 'Artikel')

@push('styles')
<style>
    .section-card {
        background: #FFFFFF;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.09);
    }

    @keyframes floatEmpty {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-6px); }
    }
    .float-anim { animation: floatEmpty 3s ease-in-out infinite; }
</style>
@endpush

@section('content')
{{-- HEADER --}}
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-start gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Articles</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Tips & Parenting Information</p>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
            bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
            rounded-t-[50px] -mt-[50px] relative z-20
            hide-scrollbar space-y-4">

    {{-- KATEGORI --}}
    <div class="anim delay-2">
        <div class="flex items-center gap-2 mb-3 px-1">
            <ion-icon name="grid-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">Category</span>
        </div>
        <div class="flex gap-2 flex-wrap">
            @php
            $categories = [
                ['icon'=>'leaf-outline',        'label'=>'Nutrisi',      'bg'=>'#EDE9FE', 'text'=>'#8B46D3'],
                ['icon'=>'fitness-outline',      'label'=>'Kesehatan',    'bg'=>'#FDE8EF', 'text'=>'#EC4899'],
                ['icon'=>'bulb-outline',         'label'=>'Edukasi',      'bg'=>'#FFFBEB', 'text'=>'#F59E0B'],
                ['icon'=>'game-controller-outline','label'=>'Aktivitas',  'bg'=>'#EDE9FE', 'text'=>'#8B46D3'],
                ['icon'=>'heart-outline',        'label'=>'Parenting',    'bg'=>'#FDF2F8', 'text'=>'#EC4899'],
                ['icon'=>'medkit-outline',       'label'=>'Imunisasi',    'bg'=>'#EEF2FF', 'text'=>'#4F46E5'],
            ];
            @endphp
            @foreach($categories as $cat)
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold opacity-70"
                 style="background:{{ $cat['bg'] }};color:{{ $cat['text'] }};">
                <ion-icon name="{{ $cat['icon'] }}" style="font-size:13px;"></ion-icon>
                {{ $cat['label'] }}
            </div>
            @endforeach
        </div>
    </div>

    {{-- ARTIKEL POPULER --}}
    <div class="anim delay-3">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <ion-icon name="trending-up-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
                <span class="text-[#1E1B2E] text-[15px] font-extrabold">Popular Articles</span>
            </div>
            <span class="text-[#9CA3AF] text-[10px] font-bold bg-[#EDE9FE] px-2.5 py-1 rounded-full">Soon</span>
        </div>

        <div class="space-y-3">
            @php
            $articles = [
                ['title'=>'Cara Tepat Memperkenalkan Makanan Padat pada Bayi',    'cat'=>'Nutrisi',   'icon'=>'leaf-outline',     'time'=>'5 min read', 'bg'=>'bg-[#EDE9FE]', 'iconColor'=>'#8B46D3'],
                ['title'=>'7 Aktivitas Stimulasi Terbaik untuk Balita 1-3 Tahun', 'cat'=>'Aktivitas', 'icon'=>'game-controller-outline', 'time'=>'7 min read', 'bg'=>'bg-[#FDF2F8]', 'iconColor'=>'#EC4899'],
                ['title'=>'Jadwal Imunisasi Lengkap yang Wajib Diketahui Orang Tua','cat'=>'Imunisasi','icon'=>'medkit-outline',  'time'=>'4 min read', 'bg'=>'bg-[#EEF2FF]', 'iconColor'=>'#4F46E5'],
            ];
            @endphp
            @foreach($articles as $i => $art)
            <div class="anim section-card p-4 flex items-center gap-3 relative overflow-hidden"
                 style="animation-delay: {{ 0.21 + $i * 0.08 }}s;">
                <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10 rounded-[18px]">
                    <div class="flex items-center gap-1.5 bg-[#EDE9FE] rounded-full px-3 py-1">
                        <ion-icon name="lock-closed" style="font-size:11px;color:#8B46D3;"></ion-icon>
                        <span class="text-[#8B46D3] text-[10px] font-bold">Coming Soon</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $art['bg'] }} flex items-center justify-center shrink-0">
                    <ion-icon name="{{ $art['icon'] }}" style="font-size:22px;color:{{ $art['iconColor'] }};"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[#1E1B2E] font-bold text-xs leading-snug line-clamp-2 mb-1">{{ $art['title'] }}</p>
                    <div class="flex items-center gap-2">
                        <span class="text-[#9CA3AF] text-[10px] font-semibold">{{ $art['cat'] }}</span>
                        <span class="text-[#E5E1F0] text-[10px]">•</span>
                        <span class="text-[#9CA3AF] text-[10px]">{{ $art['time'] }}</span>
                    </div>
                </div>
                <ion-icon name="chevron-forward" style="font-size:14px;color:#C4B5FD;flex-shrink:0;"></ion-icon>
            </div>
            @endforeach
        </div>
    </div>

    {{-- SEMUA ARTIKEL --}}
    <div class="anim delay-4">
        <div class="flex items-center gap-2 mb-3 px-1">
            <ion-icon name="layers-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">All Articles</span>
        </div>

        @if(isset($artikels) && count($artikels) > 0)
            <div class="space-y-3">
                @foreach($artikels as $i => $artikel)
                <div class="anim section-card p-4 flex items-center gap-3 relative overflow-hidden"
                     style="animation-delay: {{ 0.29 + $i * 0.06 }}s;">
                    <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10 rounded-[18px]">
                        <div class="flex items-center gap-1.5 bg-[#EDE9FE] rounded-full px-3 py-1">
                            <ion-icon name="lock-closed" style="font-size:11px;color:#8B46D3;"></ion-icon>
                            <span class="text-[#8B46D3] text-[10px] font-bold">Coming Soon</span>
                        </div>
                    </div>
                    @if(!empty($artikel['thumbnail']))
                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-10">
                        <img src="{{ $artikel['thumbnail'] }}" alt="" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="w-16 h-16 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="newspaper-outline" style="font-size:24px;color:#8B46D3;"></ion-icon>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <span class="inline-block px-2 py-[2px] rounded-full bg-[#FDF2F8] text-[#EC4899] text-[9px] font-bold mb-1.5">{{ $artikel['kategori'] ?? 'Artikel' }}</span>
                        <p class="text-[#1E1B2E] font-bold text-xs leading-snug line-clamp-2">{{ $artikel['judul'] }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                <ion-icon name="time-outline" style="font-size:10px;"></ion-icon>
                                {{ $artikel['read_time'] ?? '5' }} min
                            </span>
                            <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                <ion-icon name="eye-outline" style="font-size:10px;"></ion-icon>
                                {{ $artikel['views'] ?? '0' }}
                            </span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward" style="font-size:14px;color:#C4B5FD;flex-shrink:0;"></ion-icon>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 px-8">
                <div class="float-anim w-24 h-24 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
                    <ion-icon name="newspaper-outline" style="font-size:48px;color:#C4B5FD;"></ion-icon>
                </div>
                <h2 class="text-[#1E1B2E] font-extrabold text-lg mb-1 text-center">No Articles Yet</h2>
                <p class="text-[#9CA3AF] text-xs text-center leading-relaxed">Articles are being prepared.<br>Come back later!</p>
            </div>
        @endif
    </div>

    <div class="h-4"></div>
</div>
@endsection

@push('modals')
    @include('partials.permission-modals')
@endpush
