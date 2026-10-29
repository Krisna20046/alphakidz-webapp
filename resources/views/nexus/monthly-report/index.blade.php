@extends('layouts.app')

@section('title', 'Laporan Bulanan Nexus')

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px);} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    .nmr-modal.open { display:flex !important; }
    #nmrPdfBox:fullscreen { max-width:none !important; width:100% !important; height:100% !important; border-radius:0 !important; }
    #nmrPdfBox:-webkit-full-screen { max-width:none !important; width:100% !important; height:100% !important; border-radius:0 !important; }
</style>
@endpush

@php
    $steps = [
        ['icon' => 'document-text-outline', 'color' => '#8B46D3', 'title' => 'Apa itu Laporan Bulanan?',
         'body' => '<p>Laporan berisi <b>saran dan rekap bulanan</b> nanny &amp; anak, dibuat <b>manual oleh tim Nexus</b> dalam bentuk <b>PDF</b>.</p>'
                 . '<p>Laporan ini dibaca/diunduh oleh <b>Majikan</b> dan <b>Nanny</b> melalui menu Laporan Bulanan mereka.</p>'],
        ['icon' => 'cloud-upload-outline', 'color' => '#16A34A', 'title' => 'Upload PDF',
         'body' => '<p>Tekan tombol <b>Upload Laporan</b>, lalu isi: pilih <b>anak</b>, <b>bulan</b> (YYYY-MM), judul, catatan (opsional), dan file <b>PDF</b> (maks 20 MB).</p>'
                 . '<p>Upload ulang bulan yang sama akan <b>menimpa</b> laporan lama. Notifikasi otomatis dikirim ke majikan &amp; nanny.</p>'],
        ['icon' => 'notifications-outline', 'color' => '#F59E0B', 'title' => 'Kirim Notif & Hapus',
         'body' => '<p>Tombol <b>Kirim Notif</b> mengirim ulang <b>notifikasi</b> (bukan PDF) ke majikan &amp; nanny, misal penerima belum menerima kabar ada laporan baru.</p>'
                 . '<p>Badge <b>Notif terkirim</b> menampilkan waktu terakhir notifikasi terkirim. Tombol <b>Hapus</b> menghapus laporan beserta file PDF-nya.</p>'],
        ['icon' => 'eye-outline', 'color' => '#0E9F6E', 'title' => 'Lihat, Unduh & Filter',
         'body' => '<p>Tombol <b>Lihat</b> membuka preview PDF dalam modal pop-up; ikon <b>expand</b> di header modal menampilkan <b>layar penuh</b>.</p>'
                 . '<p>Pilih <b>bulan</b> di kartu filter untuk langsung menyaring daftar (tanpa tombol Filter). Teks <b>Reset</b> mengembalikan ke semua bulan.</p>'],
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
            <span class="text-white text-[17px] font-extrabold tracking-wide">Laporan Bulanan</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">PDF rekap & saran dari tim Nexus</p>
        </div>
        <button type="button" onclick="nmrTutorialOpen()"
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

    {{-- Upload laporan (di body, bukan header) --}}
    <button type="button" onclick="nmrUploadOpen()"
        class="anim delay-2 w-full py-3.5 mb-4 rounded-2xl bg-[#8B46D3] text-white text-[14px] font-extrabold flex items-center justify-center gap-2 shadow-[0_4px_16px_rgba(139,70,211,0.35)] active:scale-[0.98] transition-transform">
        <ion-icon name="cloud-upload-outline" style="font-size:18px;"></ion-icon>
        Upload Laporan
    </button>

    {{-- Filter bulan: pilih bulan → auto filter (tanpa tombol); selalu ada Reset --}}
    <form method="GET" action="{{ route('nexus-monthly-reports') }}" id="nmrFilterForm"
          class="anim delay-2 bg-white rounded-2xl border-2 {{ $filterMonth ? 'border-[#8B46D3]' : 'border-[#EAE6F5]' }} p-3 mb-4 transition-colors">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $filterMonth ? 'bg-[#8B46D3]' : 'bg-[#EDE9FE]' }} flex items-center justify-center shrink-0 transition-colors">
                <ion-icon name="calendar-outline" style="font-size:18px;color:{{ $filterMonth ? '#fff' : '#8B46D3' }};"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-extrabold text-[#8B86A5] uppercase tracking-wide">Filter Bulan</p>
                <input type="month" name="report_month" value="{{ $filterMonth ?? '' }}"
                       onchange="this.form.submit()"
                       class="w-full bg-transparent border-0 p-0 text-[15px] font-extrabold text-[#1E1B2E] outline-none
                              [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-60"
                       placeholder="Pilih bulan…">
            </div>
            <a href="{{ route('nexus-monthly-reports') }}" title="Reset filter (tampilkan semua bulan)"
               class="shrink-0 px-3 py-2 rounded-xl border text-[12px] font-extrabold transition-colors
                      {{ $filterMonth
                          ? 'border-[#FECACA] bg-[#FEF2F2] text-[#DC2626]'
                          : 'border-[#EAE6F5] bg-[#FAFAFC] text-[#C4B5FD] opacity-50' }}">
                Reset
            </a>
        </div>
        @if($filterMonth)
        <div class="flex items-center gap-1.5 mt-2.5 pt-2.5 border-t border-[#F0ECF9]">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#EDE9FE] text-[#8B46D3]">
                <ion-icon name="funnel" style="font-size:10px;"></ion-icon>
                Menampilkan: {{ \Illuminate\Support\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }}
            </span>
        </div>
        @endif
    </form>

    {{-- List laporan --}}
    @if(count($records) === 0)
    <div class="flex flex-col items-center justify-center" style="padding:60px 40px;">
        <div class="float-anim flex items-center justify-center"
             style="width:120px;height:120px;border-radius:60px;background:#EDE9FE;margin-bottom:24px;">
            <ion-icon name="document-text-outline" style="font-size:60px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-center" style="font-size:18px;font-weight:700;color:#1E1B2E;margin-bottom:8px;">Belum ada laporan</p>
        <p class="text-center" style="font-size:14px;color:#9CA3AF;line-height:20px;">Tekan <b>Upload Laporan</b> di atas untuk kirim PDF rekap bulanan ke majikan &amp; nanny.</p>
    </div>
    @else
    <div class="flex flex-col gap-3 anim delay-3">
        @foreach($records as $i => $r)
        @php $hasPdf = !empty($r['pdf_path']); @endphp
        <div class="bg-white rounded-[16px] border border-[#EAE6F5] p-4"
             style="animation:slideUp 0.3s ease {{ $i*0.05 }}s both;opacity:0;">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                    <ion-icon name="document-text-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="truncate text-[13px] font-extrabold text-[#1E1B2E]">{{ $r['title'] ?? '-' }}</p>
                        <span class="text-[10px] font-bold text-[#8B86A5] shrink-0">{{ $r['report_month'] ?? '' }}</span>
                    </div>
                    <p class="text-[11px] font-bold text-[#8B46D3] mt-0.5">
                        {{ $r['nama_anak'] ?? '-' }}
                        @if(!empty($r['nama_nanny'])) <span class="text-[#8B86A5] font-semibold">· nanny: {{ $r['nama_nanny'] }}</span> @endif
                    </p>
                    @if(!empty($r['description']))
                    <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed mt-1.5 line-clamp-2">{{ $r['description'] }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                        @if(!empty($r['sent_at']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F0FDF4] text-[#16A34A]">
                            <ion-icon name="notifications" style="font-size:10px;"></ion-icon>
                            Notif terkirim {{ \Carbon\Carbon::parse($r['sent_at'])->format('d M H:i') }}
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF3C7] text-[#D97706]">
                            <ion-icon name="time-outline" style="font-size:10px;"></ion-icon>
                            Notif belum terkirim
                        </span>
                        @endif
                        @if($hasPdf)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EDE9FE] text-[#8B46D3]">
                            <ion-icon name="checkmark-circle" style="font-size:10px;"></ion-icon>
                            PDF tersedia
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions (grid 2×2 agar label panjang "Kirim Notif" lega) --}}
            <div class="grid grid-cols-2 gap-2 mt-3">
                @if($hasPdf)
                <button type="button" onclick="nmrPdfOpen('{{ route('nexus-monthly-reports.view', $r['id']) }}')"
                   class="py-2 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[12px] font-extrabold flex items-center justify-center gap-1.5 active:scale-[0.98] transition-transform">
                    <ion-icon name="eye-outline" style="font-size:15px;"></ion-icon> Lihat
                </button>
                <a href="{{ route('nexus-monthly-reports.download', $r['id']) }}"
                   class="py-2 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[12px] font-extrabold flex items-center justify-center gap-1.5">
                    <ion-icon name="download-outline" style="font-size:15px;"></ion-icon> Unduh
                </a>
                @else
                <span class="col-span-2 py-2 rounded-xl border border-dashed border-[#D6CCEA] text-[#8B86A5] text-[12px] font-bold text-center">
                    PDF belum tersedia
                </span>
                @endif
                <form method="POST" action="{{ route('nexus-monthly-reports.resend', $r['id']) }}">
                    @csrf
                    <button type="submit" title="Kirim ulang notifikasi ke majikan & nanny (bukan PDF)"
                       class="w-full py-2 rounded-xl bg-[#8B46D3] text-white text-[12px] font-extrabold flex items-center justify-center gap-1.5">
                        <ion-icon name="notifications-outline" style="font-size:14px;"></ion-icon> Kirim Notif
                    </button>
                </form>
                <form method="POST" action="{{ route('nexus-monthly-reports.destroy', $r['id']) }}"
                      onsubmit="return confirm('Hapus laporan ini beserta file PDF-nya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                       class="w-full py-2 rounded-xl bg-white border border-[#FECACA] text-[#DC2626] text-[12px] font-extrabold flex items-center justify-center gap-1.5">
                        <ion-icon name="trash-outline" style="font-size:14px;"></ion-icon> Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination (full reload) --}}
    @if($meta && ($meta['last_page'] ?? 1) > 1)
    <div class="flex items-center justify-center gap-2 mt-4">
        @php $qs = $filterMonth ? '?report_month=' . urlencode($filterMonth) . '&' : '?'; @endphp
        <a href="{{ route('nexus-monthly-reports') . $qs . 'page=' . max(1, ($meta['current_page']-1)) }}"
           class="px-3 py-1.5 rounded-lg bg-white border border-[#DDD6EF] text-[#8B46D3] text-xs font-bold {{ ($meta['current_page'] ?? 1) <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
            <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
        </a>
        <span class="px-2 text-xs font-bold text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $meta['current_page'] }}</span>/{{ $meta['last_page'] }}</span>
        <a href="{{ route('nexus-monthly-reports') . $qs . 'page=' . min($meta['last_page'], ($meta['current_page']+1)) }}"
           class="px-3 py-1.5 rounded-lg bg-white border border-[#DDD6EF] text-[#8B46D3] text-xs font-bold {{ ($meta['current_page'] ?? 1) >= ($meta['last_page'] ?? 1) ? 'opacity-30 pointer-events-none' : '' }}">
            <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
        </a>
    </div>
    @endif
    @endif
</div>

{{-- Modal upload --}}
<div id="nmrUploadModal" class="nmr-modal hidden fixed inset-0 z-[60] items-center justify-center p-4">
    <div class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm" onclick="nmrUploadClose()"></div>
    <div class="relative w-full max-w-md bg-white rounded-[24px] overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#EAE6F5]">
            <span class="text-[15px] font-extrabold text-[#1E1B2E] flex items-center gap-2">
                <ion-icon name="cloud-upload-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                Upload Laporan Bulanan
            </span>
            <button type="button" onclick="nmrUploadClose()"
                class="w-8 h-8 rounded-full bg-[#F3F0FD] flex items-center justify-center shrink-0">
                <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
            </button>
        </div>

        <form method="POST" action="{{ route('nexus-monthly-reports.store') }}" enctype="multipart/form-data" class="p-5 flex flex-col gap-4">
            @csrf

            <div>
                <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Anak <span class="text-[#DC2626]">*</span></label>
                <select name="id_anak" required
                    class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    <option value="" disabled selected>Pilih anak…</option>
                    @foreach($children as $c)
                    <option value="{{ $c['id'] }}" {{ old('id_anak') == $c['id'] ? 'selected' : '' }}>
                        {{ $c['nama'] }}@if(!empty($c['nama_majikan'])) — {{ $c['nama_majikan'] }}@endif
                    </option>
                    @endforeach
                </select>
                @error('id_anak')<p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Bulan <span class="text-[#DC2626]">*</span></label>
                    <input type="month" name="report_month" value="{{ old('report_month') }}" required
                        class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    @error('report_month')<p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Judul <span class="text-[#DC2626]">*</span></label>
                    <input type="text" name="title" value="{{ old('title', 'Rekap Bulanan') }}" maxlength="255" required
                        class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3]">
                    @error('title')<p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">Catatan (opsional)</label>
                <textarea name="description" rows="2" maxlength="5000"
                    class="w-full bg-white border border-[#DDD6EF] rounded-xl px-3 py-2.5 text-[13px] font-semibold text-[#1E1B2E] outline-none focus:border-[#8B46D3] resize-none"
                    placeholder="Ringkasan singkat isi laporan…">{{ old('description') }}</textarea>
                @error('description')<p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[12px] font-extrabold text-[#1E1B2E] mb-1.5">File PDF <span class="text-[#DC2626]">*</span> <span class="text-[#8B86A5] font-semibold">(maks 20 MB)</span></label>
                <div class="border-2 border-dashed border-[#D6CCEA] rounded-xl p-4 text-center bg-[#F8F6FF]">
                    <ion-icon name="document-attach-outline" style="font-size:30px;color:#8B46D3;"></ion-icon>
                    <p class="text-[11px] font-bold text-[#8B86A5] mt-1">PDF buatan tim Nexus (rekap + saran)</p>
                    <input type="file" name="pdf" accept="application/pdf" required
                        class="mt-2 text-[12px] font-bold text-[#4B4763] w-full">
                </div>
                @error('pdf')<p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full py-3 rounded-xl bg-[#8B46D3] text-white text-[14px] font-extrabold flex items-center justify-center gap-2 active:scale-[0.98] transition-transform">
                <ion-icon name="cloud-upload-outline" style="font-size:17px;"></ion-icon>
                Upload &amp; Kirim Notif
            </button>
            <p class="text-[10px] font-semibold text-[#8B86A5] text-center leading-relaxed -mt-1">
                Upload ulang bulan yang sama akan menimpa laporan lama. Notifikasi otomatis ke majikan &amp; nanny.
            </p>
        </form>
    </div>
</div>
@include('monthly-report._tutorial', ['steps' => $steps])

{{-- Modal preview PDF (pola monthly-report/show, + tombol fullscreen) --}}
<div id="nmrPdfModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div id="nmrPdfBackdrop" class="absolute inset-0 bg-[#1E1B2E]/60 backdrop-blur-sm" onclick="nmrPdfClose()"></div>
    <div id="nmrPdfBox" class="relative w-full max-w-2xl h-[85vh] bg-white rounded-[24px] overflow-hidden flex flex-col shadow-2xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#EAE6F5] bg-white">
            <span class="text-[14px] font-extrabold text-[#1E1B2E] flex items-center gap-2">
                <ion-icon name="document-text-outline" style="font-size:18px;color:#8B46D3;"></ion-icon>
                Laporan Bulanan
            </span>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="nmrPdfFullscreen()" title="Layar penuh"
                    class="w-8 h-8 rounded-full bg-[#F3F0FD] flex items-center justify-center">
                    <ion-icon name="expand-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </button>
                <button type="button" onclick="nmrPdfClose()"
                    class="w-8 h-8 rounded-full bg-[#F3F0FD] flex items-center justify-center">
                    <ion-icon name="close" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </button>
            </div>
        </div>
        <div class="flex-1 bg-[#F3F0FD] relative overflow-hidden">
            <div id="nmrPdfLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3"
                 style="background:rgba(243,240,253,0.6);">
                <div class="w-12 h-12 rounded-2xl bg-[#8B46D3] flex items-center justify-center">
                    <svg class="animate-spin" style="width:22px;height:22px;color:#fff" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
                        <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
                    </svg>
                </div>
                <p class="text-[#8B86A5] text-[12px] font-bold">Memuat PDF…</p>
            </div>
            <iframe id="nmrPdfFrame" src="about:blank" class="w-full h-full border-0" title="Preview Laporan Bulanan"></iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);

const nmrModal = document.getElementById('nmrUploadModal');
function nmrUploadOpen() { nmrModal.classList.add('open'); document.body.style.overflow = 'hidden'; }
function nmrUploadClose() { nmrModal.classList.remove('open'); document.body.style.overflow = ''; }

const nmrPdfModal = document.getElementById('nmrPdfModal');
const nmrPdfBox = document.getElementById('nmrPdfBox');
const nmrPdfFrame = document.getElementById('nmrPdfFrame');
const nmrPdfLoading = document.getElementById('nmrPdfLoading');

function nmrPdfOpen(url) {
    if (!nmrPdfModal) return;
    if (nmrPdfLoading) nmrPdfLoading.style.display = 'flex';
    nmrPdfFrame.src = url;
    nmrPdfModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function nmrPdfClose() {
    if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
    if (!nmrPdfModal) return;
    nmrPdfModal.classList.add('hidden');
    nmrPdfFrame.src = 'about:blank';
    document.body.style.overflow = '';
    if (nmrPdfLoading) nmrPdfLoading.style.display = 'flex';
}
function nmrPdfFullscreen() {
    if (!nmrPdfBox) return;
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => {});
        return;
    }
    if (nmrPdfBox.requestFullscreen) nmrPdfBox.requestFullscreen().catch(() => {});
    else if (nmrPdfBox.webkitRequestFullscreen) nmrPdfBox.webkitRequestFullscreen();
}
nmrPdfFrame.addEventListener('load', () => {
    if (nmrPdfLoading) nmrPdfLoading.style.display = 'none';
});
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    // Modal PDF aktif → tutup PDF (fullscreen exit ikut tertangani di nmrPdfClose)
    if (nmrPdfModal && !nmrPdfModal.classList.contains('hidden')) { nmrPdfClose(); return; }
    if (nmrModal && nmrModal.classList.contains('open')) nmrUploadClose();
});
</script>
@endpush
