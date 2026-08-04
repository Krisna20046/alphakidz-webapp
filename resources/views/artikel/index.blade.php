@extends('layouts.app')

@php $activeNav = 'artikel' @endphp

@section('title', 'Articles')

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

    .wp-excerpt p { margin: 0; }

    /* Pagination custom */
    .pagination {
        display: flex;
        align-items: center;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        color: #6B7280;
        background: #F3F0FF;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .pagination li a:hover {
        background: #EDE9FE;
        color: #8B46D3;
    }
    .pagination li.active span {
        background: #8B46D3;
        color: #fff;
    }
    .pagination li.disabled span {
        opacity: 0.4;
        cursor: default;
    }
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
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Tips &amp; Parenting Information</p>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
            bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
            rounded-t-[50px] -mt-[50px] relative z-20
            hide-scrollbar space-y-4">

    {{-- KATEGORI --}}
    {{-- <div class="anim delay-2">
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
    </div> --}}

    {{-- SEMUA ARTIKEL DARI WP API --}}
    <div class="anim delay-3">
        <div class="flex items-center gap-2 mb-3 px-1">
            <ion-icon name="layers-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">All Articles</span>
        </div>

        @if($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator && $paginator->count() > 0)
            <div class="space-y-3">
                @foreach($paginator as $i => $post)
                @php
                    $title = $post['title']['rendered'] ?? '';
                    $excerpt = strip_tags($post['excerpt']['rendered'] ?? '');
                    $date = \Carbon\Carbon::parse($post['date'] ?? now())->translatedFormat('d F Y');
                    $link = $post['link'] ?? '#';
                    $thumbnail = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
                @endphp
                <a href="{{ route('artikel.show', $post['id']) }}"
                   class="block anim section-card p-4 flex items-center gap-3 relative overflow-hidden"
                   style="animation-delay: {{ 0.21 + $i * 0.06 }}s;">
                    @if($thumbnail)
                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0">
                        <img src="{{ $thumbnail }}"
                             alt="{{ $title }}"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    @else
                    <div class="w-16 h-16 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                        <ion-icon name="newspaper-outline" style="font-size:24px;color:#8B46D3;"></ion-icon>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-[#1E1B2E] font-bold text-xs leading-snug line-clamp-2 mb-1">
                            {{ $title }}
                        </p>
                        <p class="text-[#9CA3AF] text-[10px] leading-relaxed line-clamp-2 wp-excerpt">
                            {!! $excerpt !!}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="flex items-center gap-[3px] text-[9px] text-[#9CA3AF] font-semibold">
                                <ion-icon name="time-outline" style="font-size:10px;"></ion-icon>
                                {{ $date }}
                            </span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward" style="font-size:14px;color:#C4B5FD;flex-shrink:0;"></ion-icon>
                </a>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            <div class="flex justify-center pt-4">
                @php
                    $paginator->onEachSide(1);
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                @endphp
                <ul class="pagination">
                    {{-- FIRST --}}
                    @if($currentPage > 2)
                    <li>
                        <a href="{{ $paginator->url(1) }}" aria-label="First">
                            <ion-icon name="play-skip-back" style="font-size:12px;"></ion-icon>
                        </a>
                    </li>
                    @else
                    <li class="disabled">
                        <span><ion-icon name="play-skip-back" style="font-size:12px;"></ion-icon></span>
                    </li>
                    @endif

                    {{-- PREV --}}
                    @if($paginator->onFirstPage())
                    <li class="disabled"><span><ion-icon name="chevron-back" style="font-size:12px;"></ion-icon></span></li>
                    @else
                    <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev"><ion-icon name="chevron-back" style="font-size:12px;"></ion-icon></a></li>
                    @endif

                    {{-- PAGE NUMBERS --}}
                    @foreach($paginator->getUrlRange(max(1, $currentPage - 1), min($lastPage, $currentPage + 1)) as $pageNum => $url)
                    <li class="{{ $pageNum == $currentPage ? 'active' : '' }}">
                        @if($pageNum == $currentPage)
                            <span>{{ $pageNum }}</span>
                        @else
                            <a href="{{ $url }}">{{ $pageNum }}</a>
                        @endif
                    </li>
                    @endforeach

                    {{-- NEXT --}}
                    @if($paginator->hasMorePages())
                    <li><a href="{{ $paginator->nextPageUrl() }}" rel="next"><ion-icon name="chevron-forward" style="font-size:12px;"></ion-icon></a></li>
                    @else
                    <li class="disabled"><span><ion-icon name="chevron-forward" style="font-size:12px;"></ion-icon></span></li>
                    @endif

                    {{-- LAST --}}
                    @if($currentPage < $lastPage - 1)
                    <li>
                        <a href="{{ $paginator->url($lastPage) }}" aria-label="Last">
                            <ion-icon name="play-skip-forward" style="font-size:12px;"></ion-icon>
                        </a>
                    </li>
                    @else
                    <li class="disabled">
                        <span><ion-icon name="play-skip-forward" style="font-size:12px;"></ion-icon></span>
                    </li>
                    @endif
                </ul>
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
