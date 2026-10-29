@extends('layouts.app')

@section('title', 'Learning Progress - ' . $namaAnak)

@push('styles')
<style>
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
    .tip { pointer-events:none; }
    .series-line { fill:none; stroke-width:2; stroke-linejoin:round; stroke-linecap:round; }
    .dot { r:4; stroke-width:2; }
    .dot-hit { fill:transparent; cursor:pointer; }
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
         'body' => '<p>Halaman ini menampilkan pemantauan perkembangan belajar anak per <b>kategori</b>, diisi oleh nanny.</p>'
                 . '<p>Ada 6 kategori: <b>Reading, Math, Science, Language, Focus, Communication</b>.</p>'
                 . '<p>Halaman ini <b>read-only</b> — hanya untuk melihat.</p>'],
        ['icon' => 'speedometer-outline', 'color' => '#4F46E5', 'title' => 'Skor & Rubrik 5 Tingkat',
         'body' => '<p>Skor <b>0–100</b> dinilai dari rubrik 5 tingkat yang dipakai nanny:</p>'
                 . '<ul><li><b>0–20</b> Belum</li><li><b>21–40</b> Perlu Bantuan</li>'
                 . '<li><b>41–60</b> Berkembang</li><li><b>61–80</b> Mahir</li><li><b>81–100</b> Menguasai</li></ul>'],
        ['icon' => 'trending-up-outline', 'color' => '#F59E0B', 'title' => 'Membaca Grafik Tren',
         'body' => '<p>Setiap kategori punya <b>grafik rata-rata skor per minggu</b> (tren naik/turun).</p>'
                 . '<p>Bila data baru mencakup <b>1 minggu</b>, grafik belum bisa ditarik (butuh 2 minggu berbeda).</p>'
                 . '<p>Selisih <b>delta</b> menunjukkan perubahan vs minggu sebelumnya.</p>'],
        ['icon' => 'flag-outline', 'color' => '#E11D48', 'title' => 'Membaca Badge Status',
         'body' => '<p><b>On track</b> — perkembangan normal.</p>'
                 . '<p><b>Sharp decline</b> — skor turun ≥ 20 poin; layak diperhatikan.</p>'
                 . '<p><b>Need more data</b> — data baru 1 minggu; tunggu minggu berikutnya.</p>'
                 . '<p><b>Not recorded</b> — nanny belum mencatat kategori ini.</p>'],
        ['icon' => 'document-text-outline', 'color' => '#0E9F6E', 'title' => 'Riwayat & Detail',
         'body' => '<p>Bagian <b>History</b> menampilkan detail skor, level, dan tanggal setiap catatan.</p>'
                 . '<p>Gunakan tombol panah untuk berpindah halaman riwayat.</p>'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('majikan-learning') }}"
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
                <option value="{{ route('majikan-learning-show', $anak['id']) }}" class="text-[#1E1B2E]">{{ $anak['nama'] }}</option>
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

    {{-- Development trend --}}
    <div class="anim delay-2 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">Development Trend</span>
            <span class="text-[10px] font-bold text-[#8B86A5]">weekly average</span>
        </div>

        @if(empty($chart['categories']))
        <div class="flex flex-col items-center py-8">
            <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
                <ion-icon name="analytics-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
            </div>
            <p class="text-[#8B86A5] text-xs font-semibold">No learning progress recorded by nanny yet.</p>
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
                $series = $data['series'] ?? [];
            @endphp
            <div class="rounded-2xl border border-[#EAE6F5] p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-white"
                         style="background:{{ $meta['color'] }};">
                        <ion-icon name="{{ $meta['icon'] }}" style="font-size:17px;"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[13px] font-extrabold text-[#1E1B2E]">{{ $meta['label'] }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold" style="background:{{ $att['bg'] }};color:{{ $att['color'] }};">
                                {{ $att['label'] }}
                            </span>
                        </div>
                        <p class="text-[11px] font-semibold text-[#8B86A5] mt-0.5">
                            Current: <span class="font-extrabold" style="color:{{ $meta['color'] }};">{{ $pct }}</span>
                            @if($delta !== null)
                            · <span class="{{ $delta >= 0 ? 'text-[#16A34A]' : 'text-[#DC2626]' }} font-extrabold">
                                {{ $delta >= 0 ? '+' : '' }}{{ $delta }} vs last week
                            </span>
                            @endif
                        </p>
                    </div>
                </div>
                @if(count($series) >= 2)
                <div class="mt-1">
                    <div class="trend-chart" data-color="{{ $meta['color'] }}" data-label="{{ $meta['label'] }}"
                         data-series='{{ json_encode($series) }}'></div>
                </div>
                @elseif(count($series) === 1)
                <div class="mt-2 flex items-center gap-2">
                    <div class="h-2.5 rounded-full" style="width:{{ $pct }}%;max-width:100%;background:{{ $meta['color'] }};"></div>
                    <span class="text-[11px] font-extrabold" style="color:{{ $meta['color'] }};">{{ $pct }}</span>
                </div>
                @else
                <p class="text-[11px] font-semibold text-[#B0ACCA] mt-1">No trend data yet — record at least two dates.</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- History table (paginated, via partial) --}}
    <div class="anim delay-3 bg-white rounded-2xl border border-[#DDD6EF] p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[15px] font-extrabold text-[#1E1B2E]">History</span>
        </div>
        @include('majikan.learning-progress._history', ['idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination])
    </div>

</div>

@include('learning-progress._tutorial', ['steps' => $steps])
@endsection

@push('scripts')
<script>
// Render single-series trend line chart per category card (small multiples).
// Single series per chart → no legend needed; the card title names the series.
function renderTrend(el) {
    const series = JSON.parse(el.dataset.series || '[]');
    if (series.length < 2) return;
    const color = el.dataset.color || '#8B46D3';
    const label = el.dataset.label || '';

    const W = 290, H = 90, PAD = 6;
    const xs = series.map((s, i) => (series.length === 1 ? W/2 : PAD + (i * (W - 2*PAD)) / (series.length - 1)));
    const sc = series.map(s => s.avg_score);
    const min = Math.min(0, ...sc) - 5;
    const max = Math.max(100, ...sc) + 5;
    const span = (max - min) || 1;
    const ys = sc.map(v => H - PAD - ((v - min) / span) * (H - 2*PAD));

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('width', '100%');
    svg.style.display = 'block';

    // Gridlines (recessive)
    [0.25, 0.5, 0.75].forEach(f => {
        const y = PAD + f * (H - 2*PAD);
        const g = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        g.setAttribute('x1', PAD); g.setAttribute('x2', W-PAD);
        g.setAttribute('y1', y); g.setAttribute('y2', y);
        g.setAttribute('stroke', '#EDE9FE'); g.setAttribute('stroke-width', '1');
        svg.appendChild(g);
    });

    // Area wash
    if (series.length >= 2) {
        const area = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        area.setAttribute('d', `M ${xs[0]} ${ys[0]} ${xs.map((x,i)=>`L ${x} ${ys[i]}`).join(' ')} L ${xs[xs.length-1]} ${H-PAD} L ${xs[0]} ${H-PAD} Z`);
        area.setAttribute('fill', color); area.setAttribute('opacity', '0.10');
        svg.appendChild(area);
    }

    // Line
    const line = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    line.setAttribute('class', 'series-line');
    line.setAttribute('stroke', color);
    line.setAttribute('d', xs.map((x,i)=>`${i===0?'M':'L'} ${x} ${ys[i]}`).join(' '));
    svg.appendChild(line);

    // Last-end label (selective direct label) — value at the end, text never wears data color
    const end = series[series.length-1];
    const txt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    txt.setAttribute('x', xs[xs.length-1]-6);
    txt.setAttribute('y', ys[ys.length-1]-8);
    txt.setAttribute('text-anchor', 'end');
    txt.setAttribute('fill', '#8B86A5');
    txt.setAttribute('font-size', '9');
    txt.setAttribute('font-weight', 'bold');
    txt.textContent = end.avg_score;
    svg.appendChild(txt);

    // Dots + hit targets + tooltip
    const tip = document.createElement('div');
    tip.className = 'tip absolute hidden bg-[#1E1B2E] text-white text-[10px] font-bold rounded-lg px-2 py-1 z-20';
    el.style.position = 'relative';
    tip.textContent = '';
    el.appendChild(tip);

    xs.forEach((x, i) => {
        const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        dot.setAttribute('class', 'dot');
        dot.setAttribute('cx', x); dot.setAttribute('cy', ys[i]);
        dot.setAttribute('fill', color); dot.setAttribute('stroke', '#fff');
        svg.appendChild(dot);

        const hit = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        hit.setAttribute('class', 'dot-hit');
        hit.setAttribute('cx', x); hit.setAttribute('cy', ys[i]);
        hit.setAttribute('r', '14');
        hit.addEventListener('mousemove', e => {
            const rect = el.getBoundingClientRect();
            tip.style.left = (e.clientX - rect.left + 10) + 'px';
            tip.style.top = (e.clientY - rect.top - 30) + 'px';
            tip.textContent = `${label}: ${series[i].avg_score} (${series[i].period})`;
            tip.classList.remove('hidden');
        });
        hit.addEventListener('mouseleave', () => tip.classList.add('hidden'));
        svg.appendChild(hit);
    });

    el.appendChild(svg);
}

document.querySelectorAll('.trend-chart').forEach(renderTrend);

const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>

<script>
async function lpGoToPage(page) {
    const url = "{{ route('majikan-learning-history', $idAnak) }}?page=" + page;
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const html = await res.text();
    document.getElementById('historyList').outerHTML = html;
}
</script>
@endpush
