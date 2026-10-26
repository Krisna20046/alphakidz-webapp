@extends('layouts.app')

@php $activeNav = $adminView ? 'home' : 'profil'; @endphp
@section('title', 'Feedback & Bug Report')

@push('styles')
<style>
    .fb-text { white-space: pre-line; word-break: break-word; }
    .fb-chip { padding: 3px 10px; border-radius: 999px; font-size: 10px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .fb-chip.bug { background: #FEF2F2; color: #DC2626; }
    .fb-chip.feed { background: #E0F2FE; color: #0284C7; }
    .fb-chip.pending { background: #EEF2FF; color: #4F46E5; }
    .fb-chip.open { background: #FEF3C7; color: #D97706; }
    .fb-chip.resolved { background: #F0FDF4; color: #16A34A; }
    .fb-chip.closed { background: #E5E7EB; color: #6B7280; }
</style>
@endpush

@section('content')
<!-- PURPLE HEADER -->
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route($adminView ? 'dashboard' : 'profil.index') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Feedback & Bug Report</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $adminView ? 'Laporan masuk dari pengguna' : 'Kirim masukan atau kendala aplikasi' }}</p>
        </div>
    </div>
</div>

<!-- WHITE BODY -->
<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    @if(session('success'))
    <div class="anim delay-2 flex items-center gap-3 bg-white rounded-2xl border border-[#D1FAE5] p-4 mb-4">
        <div class="w-9 h-9 rounded-xl bg-[#F0FDF4] flex items-center justify-center shrink-0">
            <ion-icon name="checkmark-circle" style="font-size:17px;color:#16A34A;"></ion-icon>
        </div>
        <p class="text-[13px] font-bold text-[#16A34A]">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error') || !empty($error))
    <div class="anim delay-2 flex items-center gap-3 bg-white rounded-2xl border border-[#FEE2E2] p-4 mb-4">
        <div class="w-9 h-9 rounded-xl bg-[#FEF2F2] flex items-center justify-center shrink-0">
            <ion-icon name="alert-circle" style="font-size:17px;color:#DC2626;"></ion-icon>
        </div>
        <p class="text-[13px] font-bold text-[#DC2626]">{{ session('error') ?: $error }}</p>
    </div>
    @endif
    @if($errors->any())
    <div class="anim delay-2 flex items-start gap-3 bg-white rounded-2xl border border-[#FEE2E2] p-4 mb-4">
        <div class="w-9 h-9 rounded-xl bg-[#FEF2F2] flex items-center justify-center shrink-0">
            <ion-icon name="alert-circle" style="font-size:17px;color:#DC2626;"></ion-icon>
        </div>
        <div class="flex-1">
            @foreach($errors->all() as $fbError)
            <p class="text-[12px] font-bold text-[#DC2626]">{{ $fbError }}</p>
            @endforeach
        </div>
    </div>
    @endif

    @if(!$adminView)
    {{-- ── FORM KIRIM LAPORAN (semua role) ── --}}
    <form method="POST" action="{{ route('feedback.store') }}" enctype="multipart/form-data" class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        @csrf
        <div class="flex items-center gap-2 mb-4">
            <div class="w-9 h-9 rounded-xl bg-[#EDE9FE] flex items-center justify-center shrink-0">
                <ion-icon name="create-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[15px] font-extrabold text-[#1E1B2E] block">Laporan Baru</span>
                <span class="text-[11px] font-semibold text-[#8B86A5]">Dibaca langsung oleh admin</span>
            </div>
        </div>

        <label class="text-[11px] font-extrabold text-[#9CA3AF] uppercase tracking-[0.08em] block mb-1.5">Tipe Laporan</label>
        <select name="type" required
            class="w-full appearance-none rounded-xl border border-[#DDD6EF] bg-white px-3 py-2.5 text-sm font-bold text-[#1E1B2E] outline-none focus:border-[#8B46D3] mb-3">
            <option value="feedback" @selected(old('type') === 'feedback')>Feedback — masukan & saran</option>
            <option value="bug_report" @selected(old('type') === 'bug_report')>Bug Report — kendala/error</option>
        </select>

        <label class="text-[11px] font-extrabold text-[#9CA3AF] uppercase tracking-[0.08em] block mb-1.5">Judul</label>
        <input type="text" name="title" required maxlength="255" value="{{ old('title') }}"
               placeholder="Contoh: Aplikasi error saat check-in"
               class="w-full rounded-xl border border-[#DDD6EF] bg-white px-3 py-2.5 text-sm font-bold text-[#1E1B2E] placeholder-[#C4B5FD] placeholder:font-semibold outline-none focus:border-[#8B46D3] mb-3">

        <label class="text-[11px] font-extrabold text-[#9CA3AF] uppercase tracking-[0.08em] block mb-1.5">Deskripsi</label>
        <textarea name="description" required maxlength="5000" rows="6"
                  placeholder="Jelaskan masukan Anda atau langkah saat kendala terjadi…"
                  class="w-full rounded-xl border border-[#DDD6EF] bg-white px-3 py-2.5 text-sm font-semibold text-[#1E1B2E] placeholder-[#C4B5FD] outline-none focus:border-[#8B46D3] resize-y mb-3">{{ old('description') }}</textarea>

        <label class="text-[11px] font-extrabold text-[#9CA3AF] uppercase tracking-[0.08em] block mb-1.5">Screenshot Bukti <span class="normal-case font-semibold text-[#8B86A5]">(opsional, maks 10 MB)</span></label>
        <input type="file" name="screenshot" accept="image/jpeg,image/png,image/webp"
               onchange="fbPreviewImage(this)"
               class="w-full rounded-xl border border-[#DDD6EF] bg-white px-3 py-2.5 text-xs font-bold text-[#1E1B2E] file:mr-3 file:rounded-lg file:border-0 file:bg-[#EDE9FE] file:px-3 file:py-1.5 file:text-[11px] file:font-extrabold file:text-[#8B46D3] outline-none focus:border-[#8B46D3] mb-3">
        <img id="fbPreview" src="#" alt="" class="hidden w-full rounded-xl border border-[#DDD6EF] mb-3 max-h-56 object-contain bg-[#F8F7FF]">

        <button type="submit"
            class="w-full py-3 rounded-2xl bg-[#8B46D3] text-white text-[13px] font-extrabold flex items-center justify-center gap-2 active:scale-[0.99] transition-transform">
            <ion-icon name="send-outline" style="font-size:16px;"></ion-icon>
            Kirim Laporan
        </button>
    </form>

    <div class="anim delay-3 flex items-start gap-3 rounded-2xl border border-[#EAE6F5] bg-white/60 p-3.5">
        <ion-icon name="shield-checkmark-outline" style="font-size:18px;color:#8B46D3;flex-shrink:0;margin-top:1px;"></ion-icon>
        <p class="text-[11px] font-semibold text-[#8B86A5] leading-relaxed">
            Laporan Anda diteruskan ke admin Alpha Kids. Tindak lanjut dapat dikirim melalui chat atau notifikasi.
        </p>
    </div>
    @else
    {{-- ── INBOX ADMIN ── --}}
    <div class="anim delay-2 flex items-center justify-between mb-3">
        <span class="text-[17px] font-extrabold text-[#1E1B2E]">Laporan Masuk</span>
        @if(isset($pagination['total']))
        <span class="bg-[#EDE9FE] px-3 py-1 rounded-xl text-[12px] font-extrabold text-[#8B46D3]">{{ $pagination['total'] }}</span>
        @endif
    </div>

    @forelse($allFeedbacks as $fb)
    <div class="bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-3">
        <div class="flex items-center gap-2 mb-2">
            <span class="fb-chip {{ ($fb['type'] ?? '') === 'bug_report' ? 'bug' : 'feed' }}">
                {{ ($fb['type'] ?? '') === 'bug_report' ? 'Bug' : 'Feedback' }}
            </span>
            <span class="fb-chip {{ $fb['status'] ?? 'pending' }}">{{ strtoupper($fb['status'] ?? 'pending') }}</span>
        </div>
        <p class="text-[14px] font-extrabold text-[#1E1B2E] leading-snug break-words">{{ $fb['title'] }}</p>
        <p class="fb-text text-[12px] font-semibold text-[#4B4763] leading-relaxed mt-1.5">{{ $fb['description'] }}</p>
        @if(!empty($fb['screenshot_url']))
        <img src="{{ $fb['screenshot_url'] }}" alt="Screenshot bukti" loading="lazy"
             onclick="fbViewFull(this.src)"
             class="w-full rounded-xl border border-[#DDD6EF] mt-2.5 max-h-64 object-contain bg-[#F8F7FF] cursor-zoom-in">
        @endif
        <div class="flex items-center gap-1.5 mt-2.5">
            <ion-icon name="person-circle-outline" style="font-size:13px;color:#8B86A5;"></ion-icon>
            <span class="text-[10px] font-bold text-[#8B86A5]">
                {{ $fb['created_by'] ?? 'Pengguna' }} · {{ \Carbon\Carbon::parse($fb['created_at'])->translatedFormat('d M Y H:i') }}
            </span>
        </div>
        <form method="POST" action="{{ route('admin-feedback.resolve', $fb['id']) }}" class="mt-3">
            @csrf
            <input type="hidden" name="action" value="{{ !empty($fb['is_resolved']) ? 'reopen' : 'resolved' }}">
            <button type="submit"
                class="w-full py-2.5 rounded-xl text-[12px] font-extrabold flex items-center justify-center gap-1.5 active:scale-[0.99] transition-transform
                    {{ !empty($fb['is_resolved']) ? 'bg-[#FEF3C7] text-[#D97706]' : 'bg-[#F0FDF4] text-[#16A34A]' }}">
                <ion-icon name="{{ !empty($fb['is_resolved']) ? 'refresh-outline' : 'checkmark-circle-outline' }}" style="font-size:14px;"></ion-icon>
                {{ !empty($fb['is_resolved']) ? 'Buka Kembali' : 'Tandai Selesai' }}
            </button>
        </form>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-16">
        <div class="w-[100px] h-[100px] rounded-full bg-[#EDE9FE] flex items-center justify-center mb-4">
            <ion-icon name="chatbubbles-outline" style="font-size:48px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[16px] font-extrabold text-[#1E1B2E] mb-1">{{ empty($error) ? 'Belum ada laporan' : 'Gagal memuat' }}</p>
        <p class="text-[13px] font-semibold text-[#9CA3AF] text-center px-8">{{ empty($error) ? 'Laporan feedback & bug dari pengguna akan muncul di sini.' : 'Coba muat ulang halaman.' }}</p>
    </div>
    @endforelse

    @php $page = (int) ($pagination['current_page'] ?? 1); $last = (int) ($pagination['last_page'] ?? 1); @endphp
    @if($last > 1)
    <nav aria-label="Halaman laporan" class="flex items-center justify-between gap-3 mt-2">
        @if($page > 1)
        <a href="{{ route('admin-feedback', ['page' => $page - 1]) }}"
           class="flex-1 py-2.5 rounded-xl bg-white border border-[#DDD6EF] text-[12px] font-extrabold text-[#8B46D3] flex items-center justify-center gap-1">
            <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon> Sebelumnya
        </a>
        @else<span class="flex-1"></span>@endif
        <span class="text-[11px] font-bold text-[#8B86A5]">{{ $page }} / {{ $last }}</span>
        @if($page < $last)
        <a href="{{ route('admin-feedback', ['page' => $page + 1]) }}"
           class="flex-1 py-2.5 rounded-xl bg-white border border-[#DDD6EF] text-[12px] font-extrabold text-[#8B46D3] flex items-center justify-center gap-1">
            Berikutnya <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
        </a>
        @else<span class="flex-1"></span>@endif
    </nav>
    @endif
    @endif
</div>

{{-- Modal lihat screenshot full --}}
<div id="fbViewModal" class="hidden fixed inset-0 z-[9999] bg-black/80 items-center justify-center p-4" onclick="if(event.target===this)fbViewClose()">
    <button type="button" onclick="fbViewClose()" aria-label="Tutup"
            class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
        <ion-icon name="close" style="font-size:20px;"></ion-icon>
    </button>
    <img id="fbViewFullImg" src="#" alt="Screenshot" class="max-w-full max-h-[85vh] rounded-2xl object-contain">
</div>
@endsection

@push('scripts')
<script>
function fbPreviewImage(input) {
    const img = document.getElementById('fbPreview');
    if (!img) return;
    if (input.files && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        img.classList.remove('hidden');
    } else {
        img.classList.add('hidden');
        img.src = '#';
    }
}
function fbViewFull(src) {
    const modal = document.getElementById('fbViewModal');
    document.getElementById('fbViewFullImg').src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function fbViewClose() {
    const modal = document.getElementById('fbViewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') fbViewClose(); });
</script>
@endpush
