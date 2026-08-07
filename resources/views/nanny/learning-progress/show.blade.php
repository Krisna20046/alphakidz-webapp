@extends('layouts.app')

@section('title', 'Learning Progress - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .cat-item { transition: opacity .15s ease, transform .15s ease; }
    .cat-item:active { transform: scale(0.98); }
    .mini-bar { transition: width .6s ease; }
</style>
@endpush

@php
    $catMeta = [
        'reading'       => ['label' => 'Reading',        'icon' => 'book-outline',            'color' => '#8B46D3'],
        'math'          => ['label' => 'Math',           'icon' => 'calculator-outline',      'color' => '#4F46E5'],
        'science'       => ['label' => 'Science',        'icon' => 'flask-outline',           'color' => '#0891B2'],
        'language'      => ['label' => 'Language',       'icon' => 'chatbubbles-outline',     'color' => '#0E9F6E'],
        'focus'         => ['label' => 'Focus',          'icon' => 'eye-outline',             'color' => '#F59E0B'],
        'communication' => ['label' => 'Communication',  'icon' => 'people-outline',          'color' => '#E11D48'],
    ];
    $attentionMeta = [
        'ok'                => ['label' => 'On track',        'color' => '#16A34A', 'bg' => '#F0FDF4'],
        'decline'           => ['label' => 'Sharp decline',   'color' => '#DC2626', 'bg' => '#FEF2F2'],
        'insufficient_data' => ['label' => 'Need more data',  'color' => '#F59E0B', 'bg' => '#FFFBEB'],
        'no_data'           => ['label' => 'Not recorded',    'color' => '#9CA3AF', 'bg' => '#F3F4F6'],
    ];
    $steps = [
        ['icon' => 'school-outline', 'color' => '#8B46D3', 'title' => 'Apa itu Learning Progress?',
         'body' => '<p>Halaman ini untuk mencatat dan memantau perkembangan belajar anak per <b>kategori</b>.</p>'
                 . '<p>Ada 6 kategori: <b>Reading, Math, Science, Language, Focus, Communication</b>.</p>'],
        ['icon' => 'speedometer-outline', 'color' => '#4F46E5', 'title' => 'Skor & Rubrik 5 Tingkat',
         'body' => '<p>Setiap catatan diberi skor <b>0–100</b>. Rubrik 5 tingkat membantu konsistensi penilaian:</p>'
                 . '<ul><li><b>0–20</b> Belum</li><li><b>21–40</b> Perlu Bantuan</li>'
                 . '<li><b>41–60</b> Berkembang</li><li><b>61–80</b> Mahir</li><li><b>81–100</b> Menguasai</li></ul>'],
        ['icon' => 'add-circle-outline', 'color' => '#0E9F6E', 'title' => 'Cara Mencatat',
         'body' => '<p>Tekan tombol <b>+</b> (kanan bawah) untuk menambah skor.</p>'
                 . '<p>Pilih <b>kategori</b>, geser <b>slider skor</b>, isi <b>tanggal</b> dan <b>catatan</b>, lalu simpan.</p>'
                 . '<p>Skor terakhir tiap kategori tampil sebagai pembanding agar penilaian konsisten.</p>'],
        ['icon' => 'trending-up-outline', 'color' => '#F59E0B', 'title' => 'Skor Mingguan (Rata-rata)',
         'body' => '<p>Semua catatan dalam <b>satu minggu yang sama</b> digabung menjadi <b>1 titik rata-rata</b>.</p>'
                 . '<p>Karena itu menambah banyak skor di minggu yang sama tidak mengubah grafik.</p>'
                 . '<p>Untuk melihat tren naik/turun, butuh catatan di <b>2 minggu berbeda</b>.</p>'],
        ['icon' => 'flag-outline', 'color' => '#E11D48', 'title' => 'Badge Status',
         'body' => '<p><b>On track</b> — tren normal.</p>'
                 . '<p><b>Sharp decline</b> — skor turun ≥ 20 poin.</p>'
                 . '<p><b>Need more data</b> — data baru mencakup 1 minggu; catat di minggu lain agar tren muncul.</p>'
                 . '<p><b>Not recorded</b> — belum ada catatan kategori ini.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-learning') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-white text-[17px] font-extrabold tracking-wide">Learning Progress</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
        @if(count($anakList) > 1)
        <div class="relative shrink-0">
            <select onchange="if(this.value) window.location=this.value"
                class="appearance-none bg-white/20 border border-white/30 text-white text-xs font-bold rounded-full pl-3 pr-7 py-2 outline-none">
                <option value="" class="text-[#1E1B2E]" disabled selected>{{ $namaAnak }}</option>
                @foreach($anakList as $anak)
                @if((int)$anak['id'] !== (int)$idAnak)
                <option value="{{ route('nanny-learning-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
                @endif
                @endforeach
            </select>
            <ion-icon name="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-white pointer-events-none" style="font-size:14px;"></ion-icon>
        </div>
        @endif
        <button type="button" onclick="lpTutorialOpen()"
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

    {{-- Rubric legend --}}
    @if(!empty($chart['rubric']))
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <p class="text-[13px] font-extrabold text-[#1E1B2E] mb-2">Scoring rubric</p>
        <div class="flex flex-wrap gap-1.5">
            @foreach($chart['rubric'] as $r)
            <span class="px-2 py-1 rounded-full text-[9px] font-bold" style="background:#F3F0FD;color:#8B46D3;">
                {{ $r['min'] }}-{{ $r['max'] }}: {{ $r['label'] }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Development chart --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Development Overview</span>
            <span class="text-[10px] font-bold text-[#8B86A5]">weekly average</span>
        </div>

        @if(empty($chart['categories']))
        <div class="flex flex-col items-center py-8">
            <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
                <ion-icon name="analytics-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-xs font-semibold">No learning progress yet.</p>
        </div>
        @else
        <div class="flex flex-col gap-4">
            @foreach($chart['categories'] as $cat => $data)
            @php
                $meta = $catMeta[$cat] ?? ['label'=>ucfirst($cat),'icon'=>'star-outline','color'=>'#8B46D3'];
                $current = $data['current'] ?? null;
                $pct = $current['avg_score'] ?? 0;
                $delta = $data['delta'] ?? null;
                $att = $attentionMeta[$data['attention']] ?? $attentionMeta['ok'];
            @endphp
            <div class="cat-item rounded-2xl border border-[#EAE6F5] p-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-white"
                         style="background:{{ $meta['color'] }};">
                        <ion-icon name="{{ $meta['icon'] }}" style="font-size:17px;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-extrabold text-[#1E1B2E]">{{ $meta['label'] }}</span>
                            <span class="text-[13px] font-extrabold" style="color:{{ $meta['color'] }};">{{ $pct }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1.5">
                            <div class="flex-1 h-2 bg-[#EDE9FE] rounded-full overflow-hidden">
                                <div class="h-full rounded-full mini-bar" style="width:{{ $pct }}%;background:{{ $meta['color'] }};"></div>
                            </div>
                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold shrink-0" style="background:{{ $att['bg'] }};color:{{ $att['color'] }};">
                                {{ $att['label'] }}
                            </span>
                        </div>
                        @if($delta !== null)
                        <div class="flex items-center gap-1 mt-1.5">
                            <ion-icon name="{{ $delta >= 0 ? 'trending-up' : 'trending-down' }}" style="font-size:12px;color:{{ $delta >= 0 ? '#16A34A' : '#DC2626' }};"></ion-icon>
                            <span class="text-[10px] font-bold {{ $delta >= 0 ? 'text-[#16A34A]' : 'text-[#DC2626]' }}">
                                {{ $delta >= 0 ? '+' : '' }}{{ $delta }} vs last week
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- History (paginated, via partial) --}}
    <div class="anim delay-3 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">History</span>
            <a href="{{ route('nanny-learning-create', $idAnak) }}"
               class="text-[10px] font-bold text-[#8B46D3]">+ Add score</a>
        </div>
        @include('nanny.learning-progress._history', ['idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination])
    </div>

</div>

{{-- FAB --}}
<div class="fixed bottom-[80px] right-[20px] sm:right-[calc(50%-175px)] z-30">
    <a href="{{ route('nanny-learning-create', $idAnak) }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>

@include('learning-progress._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>

<script>
async function lpGoToPage(page) {
    const url = "{{ route('nanny-learning-history', $idAnak) }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    document.getElementById('historyList').outerHTML = html;
}
</script>
@endpush
