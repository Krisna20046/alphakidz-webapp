@extends('layouts.app')

@php
    $activeNav = 'artikel';
    $titlePost = $post['title']['rendered'] ?? 'Artikel';
@endphp

@section('title', $titlePost)

@push('styles')
<style>
    .section-card {
        background: #FFFFFF;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.09);
    }

    /* WP content styling */
    .wp-content h1,
    .wp-content h2,
    .wp-content h3,
    .wp-content h4 {
        color: #1E1B2E;
        font-weight: 800;
        margin-top: 1.25em;
        margin-bottom: 0.5em;
        line-height: 1.3;
    }
    .wp-content h1 { font-size: 1.25rem; }
    .wp-content h2 { font-size: 1.15rem; }
    .wp-content h3 { font-size: 1.05rem; }
    .wp-content p {
        font-size: 0.8125rem;
        line-height: 1.65;
        color: #374151;
        margin-bottom: 0.75em;
    }
    .wp-content a {
        color: #8B46D3;
        font-weight: 600;
        text-decoration: underline;
    }
    .wp-content ul,
    .wp-content ol {
        padding-left: 1.25rem;
        margin-bottom: 0.75em;
    }
    .wp-content li {
        font-size: 0.8125rem;
        line-height: 1.65;
        color: #374151;
        margin-bottom: 0.25em;
    }
    .wp-content img {
        max-width: 100%;
        height: auto;
        border-radius: 14px;
        margin: 1em 0;
    }
    .wp-content figure {
        margin: 1em 0;
    }
    .wp-content figcaption {
        font-size: 0.6875rem;
        color: #9CA3AF;
        text-align: center;
        margin-top: 0.35em;
    }
    .wp-content blockquote {
        border-left: 3px solid #8B46D3;
        padding-left: 1em;
        margin: 1em 0;
        color: #4B5563;
        font-style: italic;
    }
    .wp-content hr {
        border: none;
        height: 1px;
        background: #E5E7EB;
        margin: 1.5em 0;
    }
    .wp-content iframe {
        max-width: 100%;
        border-radius: 14px;
    }
    .wp-content table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
        margin: 1em 0;
    }
    .wp-content td,
    .wp-content th {
        border: 1px solid #E5E7EB;
        padding: 0.5em;
        text-align: left;
    }
    .wp-content th {
        background: #F8F7FF;
        font-weight: 700;
    }

    /* Related horizontal scroll */
    .related-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .related-scroll::-webkit-scrollbar { display: none; }
    .related-card {
        scroll-snap-align: start;
        min-width: 180px;
        max-width: 180px;
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
        <a href="{{ route('artikel.index') }}"
           class="mt-1 w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Article</span>
            <p class="text-white/70 text-xs font-semibold mt-0.5 leading-[1.3]">Tips &amp; Parenting Information</p>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="flex-1 overflow-y-auto px-[20px] pt-[20px] pb-28
            bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50
            rounded-t-[50px] -mt-[50px] relative z-20
            hide-scrollbar space-y-4">

    @php
        $title = $post['title']['rendered'] ?? '';
        $content = $post['content']['rendered'] ?? '';
        $date = \Carbon\Carbon::parse($post['date'] ?? now())->translatedFormat('d F Y');
        $author = $post['_embedded']['author'][0]['name'] ?? 'PNPRO';
        $thumbnail = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
        $categories = $post['_embedded']['wp:term'][0] ?? [];
    @endphp

    {{-- THUMBNAIL --}}
    <div class="anim delay-2">
        @if($thumbnail)
        <div class="w-full aspect-[16/9] rounded-2xl overflow-hidden shadow-md">
            <img src="{{ $thumbnail }}"
                 alt="{{ $title }}"
                 class="w-full h-full object-cover"
                 loading="lazy">
        </div>
        @else
        <div class="w-full aspect-[16/9] rounded-2xl bg-[#EDE9FE] flex items-center justify-center">
            <ion-icon name="newspaper-outline" style="font-size:64px;color:#C4B5FD;"></ion-icon>
        </div>
        @endif
    </div>

    {{-- CATEGORIES --}}
    @if(count($categories) > 0)
    <div class="anim delay-2 flex gap-2 flex-wrap">
        @foreach($categories as $cat)
        <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-[#EDE9FE] text-[#8B46D3]">
            {{ $cat['name'] ?? 'Uncategorized' }}
        </span>
        @endforeach
    </div>
    @endif

    {{-- META --}}
    <div class="anim delay-2 flex items-center gap-3 text-[10px] text-[#9CA3AF] font-semibold">
        <span class="flex items-center gap-1">
            <ion-icon name="person-outline" style="font-size:11px;"></ion-icon>
            {{ $author }}
        </span>
        <span class="flex items-center gap-1">
            <ion-icon name="time-outline" style="font-size:11px;"></ion-icon>
            {{ $date }}
        </span>
    </div>

    {{-- TITLE --}}
    <h1 class="anim delay-3 text-[#1E1B2E] text-xl font-extrabold leading-snug">
        {{ $title }}
    </h1>

    {{-- BODY --}}
    <div class="anim delay-3 section-card p-5">
        <div class="wp-content">
            {!! $content !!}
        </div>
    </div>

    {{-- SHARE --}}
    <div class="anim delay-3 flex items-center gap-3 pt-1">
        <span class="text-[11px] font-bold text-[#6B7280]">Share:</span>
        <a href="https://wa.me/?text={{ urlencode($title . ' - ' . ($post['link'] ?? '')) }}"
           target="_blank"
           class="w-9 h-9 rounded-full bg-[#25D366]/10 flex items-center justify-center text-[#25D366]">
            <ion-icon name="logo-whatsapp" style="font-size:16px;"></ion-icon>
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post['link'] ?? '') }}"
           target="_blank"
           class="w-9 h-9 rounded-full bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2]">
            <ion-icon name="logo-facebook" style="font-size:16px;"></ion-icon>
        </a>
        <button onclick="copyLink('{{ $post['link'] ?? '' }}')"
                class="w-9 h-9 rounded-full bg-[#8B46D3]/10 flex items-center justify-center text-[#8B46D3]"
                id="copyBtn">
            <ion-icon name="link-outline" style="font-size:16px;"></ion-icon>
        </button>
    </div>

    {{-- RELATED POSTS --}}
    @if($relatedPosts->count() > 0)
    <div class="anim delay-3 pt-2">
        <div class="flex items-center gap-2 mb-3 px-1">
            <ion-icon name="layers-outline" style="font-size:14px;color:#8B46D3;"></ion-icon>
            <span class="text-[#1E1B2E] text-[15px] font-extrabold">Related Articles</span>
        </div>
        <div class="related-scroll">
            @foreach($relatedPosts as $related)
            @php
                $rTitle = $related['title']['rendered'] ?? '';
                $rDate = \Carbon\Carbon::parse($related['date'] ?? now())->translatedFormat('d F Y');
                $rThumb = $related['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
            @endphp
            <a href="{{ route('artikel.show', $related['id']) }}"
               class="related-card section-card p-3 flex flex-col gap-2 block">
                @if($rThumb)
                <div class="w-full aspect-[4/3] rounded-xl overflow-hidden">
                    <img src="{{ $rThumb }}" alt="{{ $rTitle }}" class="w-full h-full object-cover" loading="lazy">
                </div>
                @else
                <div class="w-full aspect-[4/3] rounded-xl bg-[#EDE9FE] flex items-center justify-center">
                    <ion-icon name="newspaper-outline" style="font-size:24px;color:#C4B5FD;"></ion-icon>
                </div>
                @endif
                <p class="text-[#1E1B2E] font-bold text-[10px] leading-snug line-clamp-2">{{ $rTitle }}</p>
                <span class="text-[8px] text-[#9CA3AF] font-semibold">{{ $rDate }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="h-4"></div>
</div>
@endsection

@push('scripts')
<script>
function copyLink(url) {
    if (!url) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            showCopied();
        }).catch(() => fallbackCopy(url));
    } else {
        fallbackCopy(url);
    }
}
function fallbackCopy(url) {
    const ta = document.createElement('textarea');
    ta.value = url;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    showCopied();
}
function showCopied() {
    const btn = document.getElementById('copyBtn');
    const original = btn.innerHTML;
    btn.innerHTML = '<ion-icon name="checkmark-outline" style="font-size:16px;"></ion-icon>';
    setTimeout(() => { btn.innerHTML = original; }, 2000);
}
</script>
@endpush

@push('modals')
    @include('partials.permission-modals')
@endpush
