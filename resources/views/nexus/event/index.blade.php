@extends('layouts.app')

@section('title', 'Event & Info')

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px);} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    .ev-modal.open { display:flex !important; }
    #evFormBody { max-height:65vh; overflow-y:auto; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'megaphone-outline', 'color' => '#8B46D3', 'title' => 'Apa itu Event & Info?',
         'body' => '<p>Halaman ini mengelola <b>konten fleksibel</b> dari tim Nexus: event, pengumuman, info, promo — apa saja.</p>'
                 . '<p>Event tampil sebagai <b>slider di halaman home</b> semua role (Majikan, Nanny, Konsultan).</p>'],
        ['icon' => 'add-circle-outline', 'color' => '#16A34A', 'title' => 'Tambah / Ubah',
         'body' => '<p>Hanya <b>Judul</b> yang wajib. Isi kolom lain sesuai kebutuhan (banner, deskripsi, jadwal, tautan, kontak, harga, kuota).</p>'
                 . '<p>Gunakan <b>Tayang</b> (publish_at) &amp; <b>Kedaluwarsa</b> (expired_at) untuk atur jendela tampil di slider; matikan <b>Aktif</b> untuk sembunyikan sementara.</p>'],
        ['icon' => 'trash-outline', 'color' => '#DC2626', 'title' => 'Hapus',
         'body' => '<p>Tombol <b>Hapus</b> menghapus event beserta file banner-nya. Tidak ada notifikasi FCM — user melihat event lewat slider home.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Event &amp; Info</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">Konten slider home semua role</p>
        </div>
        <button type="button" onclick="evTutorialOpen()"
            class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0"
            aria-label="Panduan">
            <ion-icon name="help-circle" class="text-white" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    @if(session('success') || session('error'))
    <div id="toast" class="toast rounded-2xl px-4 py-3 flex items-center gap-3 mb-4
        {{ session('success') ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center
            {{ session('success') ? 'bg-green-100' : 'bg-red-100' }}">
            <ion-icon name="{{ session('success') ? 'checkmark-circle' : 'close-circle' }}"
                style="font-size:18px;color:{{ session('success') ? '#4CAF50' : '#F44336' }};"></ion-icon>
        </div>
        <p class="text-sm font-bold {{ session('success') ? 'text-green-800' : 'text-red-800' }} flex-1">
            {{ session('success') ?? session('error') }}
        </p>
        <button onclick="document.getElementById('toast').remove()">
            <ion-icon name="close" style="font-size:16px;color:#999;"></ion-icon>
        </button>
    </div>
    @endif

    {{-- Tambah event --}}
    <button type="button" onclick="evFormOpen(null)"
        class="anim delay-2 w-full py-3.5 mb-4 rounded-2xl bg-[#8B46D3] text-white text-[14px] font-extrabold flex items-center justify-center gap-2 shadow-[0_4px_16px_rgba(139,70,211,0.35)] active:scale-[0.98] transition-transform">
        <ion-icon name="add-circle-outline" style="font-size:18px;"></ion-icon>
        Tambah Event
    </button>

    {{-- Filter type + status --}}
    <form method="GET" action="{{ route('nexus-events') }}" id="evFilterForm"
          class="anim delay-2 bg-white rounded-2xl border-2 {{ ($filterType || $filterActive !== null) ? 'border-[#8B46D3]' : 'border-[#EAE6F5]' }} p-3 mb-4 transition-colors">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ ($filterType || $filterActive !== null) ? 'bg-[#8B46D3]' : 'bg-[#EDE9FE]' }} flex items-center justify-center shrink-0 transition-colors">
                <ion-icon name="funnel-outline" style="font-size:18px;color:{{ ($filterType || $filterActive !== null) ? '#fff' : '#8B46D3' }};"></ion-icon>
            </div>
            <div class="flex-1 min-w-0 grid grid-cols-2 gap-2">
                <select name="type" onchange="this.form.submit()"
                    class="bg-white border border-[#DDD6EF] rounded-xl px-2 py-2 text-[12px] font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    <option value="">Kategori</option>
                    <option value="event" {{ ($filterType ?? '') === 'event' ? 'selected' : '' }}>Event</option>
                    <option value="pengumuman" {{ ($filterType ?? '') === 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                    <option value="promo" {{ ($filterType ?? '') === 'promo' ? 'selected' : '' }}>Promo</option>
                </select>
                <select name="is_active" onchange="this.form.submit()"
                    class="bg-white border border-[#DDD6EF] rounded-xl px-2 py-2 text-[12px] font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    <option value="">Status</option>
                    <option value="1" {{ ($filterActive ?? '') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ ($filterActive ?? '') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <a href="{{ route('nexus-events') }}" title="Reset filter"
               class="shrink-0 px-3 py-2 rounded-xl border text-[12px] font-extrabold transition-colors
                      {{ ($filterType || $filterActive !== null)
                          ? 'border-[#FECACA] bg-[#FEF2F2] text-[#DC2626]'
                          : 'border-[#EAE6F5] bg-[#FAFAFC] text-[#C4B5FD] opacity-50' }}">
                Reset
            </a>
        </div>
    </form>

    {{-- List event --}}
    @if(count($records) === 0)
    <div class="flex flex-col items-center justify-center" style="padding:60px 40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#EDE9FE;margin-bottom:24px;">
            <ion-icon name="megaphone-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-center" style="font-size:18px;font-weight:700;color:#1E1B2E;margin-bottom:8px;">Belum ada event</p>
        <p class="text-center" style="font-size:14px;color:#9CA3AF;line-height:20px;">Tekan <b>Tambah Event</b> di atas untuk buat konten slider home.</p>
    </div>
    @else
    <div class="flex flex-col gap-3 anim delay-3">
        @foreach($records as $i => $r)
        <div class="bg-white rounded-[16px] border border-[#EAE6F5] p-4"
             style="animation:slideUp 0.3s ease {{ $i*0.05 }}s both;opacity:0;">
            <div class="flex items-start gap-3">
                @if(!empty($r['banner_url']))
                <img src="{{ $r['banner_url'] }}" alt="" class="w-16 h-12 rounded-xl object-cover shrink-0 border border-[#EAE6F5]">
                @else
                <div class="w-16 h-12 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="image-outline" style="font-size:20px;color:#C4B5FD;"></ion-icon>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="truncate text-[13px] font-extrabold text-[#1E1B2E]">{{ $r['title'] ?? '-' }}</p>
                        @if(!empty($r['type']))
                        <span class="text-[10px] font-bold text-[#8B46D3] shrink-0">{{ $r['type'] }}</span>
                        @endif
                    </div>
                    @if(!empty($r['subtitle']))
                    <p class="text-[11px] font-semibold text-[#4B4763] mt-0.5 truncate">{{ $r['subtitle'] }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                        @if(!empty($r['is_active']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F0FDF4] text-[#16A34A]">
                            <ion-icon name="checkmark-circle" style="font-size:10px;"></ion-icon> Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F3F4F6] text-[#6B7280]">
                            <ion-icon name="pause-circle" style="font-size:10px;"></ion-icon> Nonaktif
                        </span>
                        @endif
                        @if(!empty($r['event_start_at']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EDE9FE] text-[#8B46D3]">
                            <ion-icon name="calendar-outline" style="font-size:10px;"></ion-icon>
                            {{ \Carbon\Carbon::parse($r['event_start_at'])->format('d M Y') }}
                        </span>
                        @endif
                        @if(!empty($r['location']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF3C7] text-[#D97706]">
                            <ion-icon name="location-outline" style="font-size:10px;"></ion-icon>
                            {{ $r['location'] }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-3">
                <button type="button" onclick="evFormOpen('{{ $r['id'] }}')"
                   class="h-[38px] rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[12px] font-extrabold flex items-center justify-center gap-1.5 active:scale-[0.98] transition-transform">
                    <ion-icon name="create-outline" style="font-size:15px;"></ion-icon> Ubah
                </button>
                <button type="button" onclick="evDeleteOpen('{{ $r['id'] }}')"
                   class="h-[38px] rounded-xl bg-white border border-[#FECACA] text-[#DC2626] text-[12px] font-extrabold flex items-center justify-center gap-1.5 active:scale-[0.98] transition-transform">
                    <ion-icon name="trash-outline" style="font-size:14px;"></ion-icon> Hapus
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination (full reload) --}}
    @if($meta && ($meta['last_page'] ?? 1) > 1)
    <div class="flex items-center justify-center gap-2 mt-4">
        @php
            $qsArr = array_filter(['type' => $filterType, 'is_active' => $filterActive]);
            $qs = $qsArr ? '?' . http_build_query($qsArr) . '&' : '?';
        @endphp
        <a href="{{ route('nexus-events') . $qs . 'page=' . max(1, ($meta['current_page']-1)) }}"
           class="px-3 py-1.5 rounded-lg bg-white border border-[#DDD6EF] text-[#8B46D3] text-xs font-bold {{ ($meta['current_page'] ?? 1) <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
            <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
        </a>
        <span class="px-2 text-xs font-bold text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $meta['current_page'] }}</span>/{{ $meta['last_page'] }}</span>
        <a href="{{ route('nexus-events') . $qs . 'page=' . min($meta['last_page'], ($meta['current_page']+1)) }}"
           class="px-3 py-1.5 rounded-lg bg-white border border-[#DDD6EF] text-[#8B46D3] text-xs font-bold {{ ($meta['current_page'] ?? 1) >= ($meta['last_page'] ?? 1) ? 'opacity-30 pointer-events-none' : '' }}">
            <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
        </a>
    </div>
    @endif
    @endif
</div>

{{-- Modal form (tambah/ubah) --}}
<div id="evFormModal" class="ev-modal hidden fixed inset-0 z-[60] items-center justify-center p-4">
    <div class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm" onclick="evFormClose()"></div>
    <div class="relative w-full max-w-md bg-white rounded-[24px] overflow-hidden shadow-2xl flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#EAE6F5] shrink-0">
            <span class="text-[15px] font-extrabold text-[#1E1B2E] flex items-center gap-2">
                <ion-icon name="megaphone-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                <span id="evFormTitle">Tambah Event</span>
            </span>
            <button type="button" onclick="evFormClose()"
                class="w-8 h-8 rounded-full bg-[#F3F0FD] flex items-center justify-center shrink-0">
                <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </button>
        </div>

        <form id="evForm" method="POST" action="{{ route('nexus-events.store') }}" enctype="multipart/form-data" class="flex-1 flex flex-col min-h-0">
            @csrf
            <div id="evFormBody" class="p-5 flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Judul <span class="text-[#DC2626]">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" maxlength="255" required
                        placeholder="cth: Workshop Parenting Gratis"
                        class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    @error('title')<p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Subtitle</label>
                        <input type="text" name="subtitle" value="{{ old('subtitle') }}" maxlength="255"
                            placeholder="cth: Sesi tanya jawab bersama ahli"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Kategori</label>
                        <select name="type" id="evTypeSelect"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                            <option value="event">Event</option>
                            <option value="pengumuman">Pengumuman</option>
                            <option value="promo">Promo</option>
                        </select>
                        <p class="text-[10px] font-semibold text-[#8B86A5] mt-1">Form di bawah menyesuaikan kategori</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" maxlength="10000"
                        class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3] resize-none"
                        placeholder="Isi konten sesuai kebutuhan…">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Banner <span class="text-[#8B86A5] font-semibold">(opsional, maks 10 MB)</span></label>
                    <div class="border-2 border-dashed border-[#D6CCEA] rounded-xl p-3 text-center bg-[#F8F6FF]">
                        <img id="evBannerPreview" src="" alt="" class="hidden mx-auto mb-2 rounded-lg max-h-24 object-cover">
                        <input type="file" name="banner_image" accept="image/jpeg,image/png,image/webp"
                            class="text-[12px] font-bold text-[#4B4763] w-full">
                        <p id="evBannerHint" class="text-[10px] font-semibold text-[#8B86A5] mt-1">Saat ubah: biarkan kosong untuk memakai banner lama</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3" data-cats="event">
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Lokasi</label>
                        <input type="text" name="location" value="{{ old('location') }}" maxlength="255"
                            placeholder="cth: Aula Alpha Kids, Jakarta"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">URL Peta</label>
                        <input type="url" name="map_url" value="{{ old('map_url') }}" maxlength="500" placeholder="https://…"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                </div>

                <div data-cats="event">
                    <p class="text-[11px] font-extrabold text-[#8B46D3] mb-1.5">Jadwal acara (info peserta — tidak mengatur tampil)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Mulai</label>
                            <input type="datetime-local" name="event_start_at" value="{{ old('event_start_at') }}"
                                class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[12px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                        </div>
                        <div>
                            <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Selesai</label>
                            <input type="datetime-local" name="event_end_at" value="{{ old('event_end_at') }}"
                                class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[12px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[11px] font-extrabold text-[#8B46D3] mb-1.5">Tampil di home (atur kapan event muncul di home)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Tayang</label>
                            <input type="datetime-local" name="publish_at" value="{{ old('publish_at') }}"
                                class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[12px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                            <p class="text-[10px] font-semibold text-[#8B86A5] mt-1">Kosong = tampil sejak dibuat</p>
                        </div>
                        <div>
                            <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Kedaluwarsa</label>
                            <input type="datetime-local" name="expired_at" value="{{ old('expired_at') }}"
                                class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[12px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                            <p class="text-[10px] font-semibold text-[#8B86A5] mt-1">Kosong = tampil terus</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">URL Tautan</label>
                        <input type="url" name="link_url" value="{{ old('link_url') }}" maxlength="500" placeholder="https://…"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Label Tautan</label>
                        <input type="text" name="link_label" value="{{ old('link_label') }}" maxlength="100" placeholder="Daftar…"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3" data-cats="event">
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Kontak (nama)</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}" maxlength="255"
                            placeholder="cth: Admin Alpha Kids"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Kontak (HP)</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" maxlength="50"
                            placeholder="cth: 081234567890"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div data-cats="event,promo">
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Harga</label>
                        <input type="number" name="price" value="{{ old('price') }}" min="0"
                            placeholder="0 = gratis"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                    <div data-cats="event">
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Kuota</label>
                        <input type="number" name="quota" value="{{ old('quota') }}" min="1"
                            placeholder="cth: 50"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Urutan</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                            placeholder="0 = paling awal"
                            class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    </div>
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                        class="w-5 h-5 rounded accent-[#8B46D3]">
                    <span class="text-[13px] font-extrabold text-[#1E1B2E]">Aktif (tampil di slider home)</span>
                </label>
            </div>

            <div class="p-5 pt-3 border-t border-[#EAE6F5] shrink-0">
                <button type="submit" id="evFormSubmit"
                    class="w-full py-3 rounded-xl bg-[#8B46D3] text-white text-[14px] font-extrabold flex items-center justify-center gap-2 active:scale-[0.98] transition-transform">
                    <ion-icon name="save-outline" style="font-size:17px;"></ion-icon>
                    <span id="evFormSubmitLabel">Simpan Event</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal konfirmasi hapus (pola nanny-detail confirmModal) --}}
<div id="evDelModal" class="hidden fixed inset-0 z-[65] flex items-end justify-center sm:items-center" style="padding-bottom: 80px;">
    <div class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm" onclick="evDeleteClose()"></div>
    <div class="relative bg-white rounded-[24px] mx-5 p-6 w-full max-w-sm shadow-[0_20px_60px_rgba(0,0,0,0.2)] z-10">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-[68px] h-[68px] rounded-full bg-[#FEF2F2] flex items-center justify-center mb-4">
                <ion-icon name="trash" style="font-size:36px;color:#DC2626;"></ion-icon>
            </div>
            <h3 class="text-[#1E1B2E] font-extrabold text-[18px] mb-2">Hapus Event?</h3>
            <p class="text-[#8B86A5] text-[13px] leading-relaxed">
                Event <span class="font-extrabold text-[#1E1B2E]" id="evDelTitle">—</span>
                beserta file banner-nya akan dihapus permanen.
            </p>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="evDeleteClose()"
                    class="flex-1 h-[48px] rounded-2xl border border-[#ECEAF4] bg-[#F8F8FB] text-[#8B86A5] font-extrabold text-[13px] active:scale-[0.97] transition-transform">
                Batal
            </button>
            <form id="evDelForm" method="POST" action="" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full h-[48px] rounded-2xl bg-[#DC2626] text-white font-extrabold text-[13px] shadow-[0_4px_12px_rgba(220,38,38,0.35)] active:scale-[0.97] transition-transform">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Tutorial (pola shared _tutorial: wizard langkah, header ungu, dots) --}}
@include('event._tutorial', ['steps' => $steps])

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);

// Data record utk prefill form ubah (keyed by id)
const EV_DATA = @json(collect($records ?? [])->keyBy('id'));
const EV_STORE_URL = @json(route('nexus-events.store'));
const EV_UPDATE_URLS = @json(collect($records ?? [])->mapWithKeys(fn ($r) => [$r['id'] => route('nexus-events.update', $r['id'])]));

const evModal  = document.getElementById('evFormModal');
const evForm   = document.getElementById('evForm');
const evFields = ['title','subtitle','description','type','location','map_url','event_start_at','event_end_at','publish_at','expired_at','link_url','link_label','contact_name','contact_phone','price','quota','sort_order'];

function evToLocal(v) {
    if (!v) return '';
    // 'Y-m-d H:i:s' → 'Y-m-dTH:i'
    return String(v).replace(' ', 'T').slice(0, 16);
}

// ── Kategori adaptif ──────────────────────────────────────────────
// Field ber-`data-cats="event,promo"` hanya tampil utk kategori tsb;
// `data-cats="event"` = hanya kategori Event; tanpa data-cats = semua kategori.
// reset=true: bersihkan value field tersembunyi (ganti kategori) — data
// kategori lama tidak ikut tersimpan; prefill edit tidak di-reset.
function evApplyCats(reset) {
    const sel = document.getElementById('evTypeSelect');
    if (!sel) return;
    const cat = sel.value || 'event';
    evForm.querySelectorAll('[data-cats]').forEach(el => {
        const allowed = el.dataset.cats.split(',').map(s => s.trim());
        const show = allowed.includes(cat);
        el.style.display = show ? '' : 'none';
        if (!show && reset) {
            el.querySelectorAll('input, textarea').forEach(inp => {
                if (inp.type === 'hidden') return;
                if (inp.type === 'checkbox') { inp.checked = false; return; }
                inp.value = '';
            });
        }
    });
}
evForm.querySelector('#evTypeSelect')?.addEventListener('change', () => evApplyCats(true));


function evFormOpen(id) {
    const rec = id ? EV_DATA[id] : null;
    document.getElementById('evFormTitle').textContent = rec ? 'Ubah Event' : 'Tambah Event';
    document.getElementById('evFormSubmitLabel').textContent = rec ? 'Simpan Perubahan' : 'Simpan Event';

    // reset ke nilai blade (old saat create-error), lalu isi utk edit
    evForm.querySelectorAll('input:not([type=hidden]):not([type=file]), textarea').forEach(el => {
        if (el.name === 'is_active') return;
        const ov = el.getAttribute('value');
        el.value = ov !== null ? ov : '';
    });
    document.querySelectorAll('#evForm input[name="is_active"][type="checkbox"]').forEach(el => { el.checked = true; });

    // Mapping type lama → kategori baru (info/workshop/… → pengumuman/event)
    const catSel = document.getElementById('evTypeSelect');
    const catMap = { info: 'pengumuman', announcement: 'pengumuman', pengumuman: 'pengumuman', promo: 'promo', discount: 'promo', sale: 'promo' };
    const mapCat = (raw) => catMap[String(raw || '').toLowerCase()] || 'event';
    if (catSel) catSel.value = mapCat(rec ? rec.type : @json(old('type')));
    evApplyCats(false);

    const preview = document.getElementById('evBannerPreview');
    const hint    = document.getElementById('evBannerHint');
    const fileInp = evForm.querySelector('input[name="banner_image"]');
    if (preview) { preview.src = ''; preview.classList.add('hidden'); }
    if (hint) hint.classList.remove('hidden');
    if (fileInp) fileInp.value = '';

    if (rec) {
        evFields.forEach(f => {
            const el = evForm.querySelector('[name="' + f + '"]');
            if (!el) return;
            const v = rec[f];
            if (el.tagName === 'SELECT') {
                el.value = v ?? '';
            } else if (el.type === 'datetime-local') {
                el.value = evToLocal(v);
            } else if (v !== null && v !== undefined) {
                el.value = v;
            } else {
                el.value = '';
            }
        });
        const cb = evForm.querySelector('input[name="is_active"][type="checkbox"]');
        if (cb) cb.checked = !!rec.is_active;
        // Loop prefill menimpa select dgn value mentah (mis 'info' tak ada
        // di opsi) → set ulang hasil mapping kategori, baru terapkan tampilan
        if (catSel) catSel.value = mapCat(rec.type);
        if (preview && rec.banner_url) { preview.src = rec.banner_url; preview.classList.remove('hidden'); }
        evForm.action = EV_UPDATE_URLS[id] || EV_STORE_URL;
        // Prefill selesai → terapkan visibilitas field sesuai kategorinya
        evApplyCats(false);
    } else {
        evForm.action = EV_STORE_URL;
        // Terapkan visibilitas sesuai kategori old() (create gagal validasi)
        evApplyCats(false);
        // restore old() utk create gagal validasi
        @if($errors->any())
            @foreach(old() as $k => $v)
                @if($k !== 'banner_image' && $k !== 'is_active')
                { const el = evForm.querySelector('[name="{{ $k }}"]');
                  if (el) { if (el.type === 'datetime-local') el.value = evToLocal(@json((string)$v)); else el.value = @json((string)$v); } }
                @endif
            @endforeach
            const cbOld = @json(old('is_active', '1')) === '1';
            const cb0 = evForm.querySelector('input[name="is_active"][type="checkbox"]');
            if (cb0) cb0.checked = cbOld;
        @endif
    }

    evModal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function evFormClose() {
    evModal.classList.remove('open');
    document.body.style.overflow = '';
}

// Preview banner saat pilih file
const evFile = evForm.querySelector('input[name="banner_image"]');
if (evFile) evFile.addEventListener('change', function () {
    const preview = document.getElementById('evBannerPreview');
    if (this.files && this.files[0] && preview) {
        preview.src = URL.createObjectURL(this.files[0]);
        preview.classList.remove('hidden');
    }
});

// Tutorial: evTutorialOpen/Close/Go dari include event._tutorial (pola shared).

// ── Konfirmasi hapus (modal, bukan native confirm) ───────────────
const evDelModal = document.getElementById('evDelModal');
const evDelForm  = document.getElementById('evDelForm');
const evDelTitle = document.getElementById('evDelTitle');
const EV_DELETE_URLS = @json(collect($records ?? [])->mapWithKeys(fn ($r) => [$r['id'] => route('nexus-events.destroy', $r['id'])]));

function evDeleteOpen(id) {
    if (evDelForm) evDelForm.action = EV_DELETE_URLS[id] || '';
    if (evDelTitle) evDelTitle.textContent = (EV_DATA[id] && EV_DATA[id].title) || 'ini';
    if (evDelModal) {
        evDelModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}
function evDeleteClose() {
    if (evDelModal) evDelModal.classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (evDelModal && !evDelModal.classList.contains('hidden')) evDeleteClose();
    if (evModal && evModal.classList.contains('open')) evFormClose();
});
</script>
@endpush
