@extends('layouts.app')

@section('title', 'Add Learning Progress')

@push('styles')
<style>
    .inp {
        width:100%; background:#F8F7FF; border:1.5px solid #DDD6EF;
        border-radius:12px; padding:12px 16px; font-size:14px;
        color:#1E1B2E; outline:none; transition:border-color .2s;
        font-family:'Nunito',sans-serif; font-weight:600;
    }
    .inp:focus { border-color:#8B46D3; }
    .cat-opt {
        flex:1; padding:12px 8px; border-radius:14px; background:#F8F7FF;
        border:2px solid #DDD6EF; display:flex; flex-direction:column;
        align-items:center; gap:4px; cursor:pointer; transition:all .15s;
        color:#8B86A5;
    }
    .cat-opt.sel { border-color:#8B46D3; background:#EDE9FE; color:#8B46D3; }
    .cat-opt:active { transform:scale(.96); }
    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.96); }
</style>
@endpush

@php
    $catMeta = [
        'reading'       => ['label' => 'Reading',        'icon' => 'book-outline'],
        'math'          => ['label' => 'Math',           'icon' => 'calculator-outline'],
        'science'       => ['label' => 'Science',        'icon' => 'flask-outline'],
        'language'      => ['label' => 'Language',       'icon' => 'chatbubbles-outline'],
        'focus'         => ['label' => 'Focus',          'icon' => 'eye-outline'],
        'communication' => ['label' => 'Communication',  'icon' => 'people-outline'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-learning-show', $idAnak) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Add Score</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    @if(session('error'))
    <div class="p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-2 mb-4">
        <ion-icon name="close-circle" style="font-size:18px;color:#F44336;flex-shrink:0;"></ion-icon>
        <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <form action="{{ route('nanny-learning-store') }}" method="POST" class="space-y-4 anim delay-2">
        @csrf
        <input type="hidden" name="id_anak" value="{{ $idAnak }}">

        {{-- Child (readonly) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child</label>
            <input type="text" value="{{ $namaAnak }}" class="inp" disabled>
        </div>

        {{-- Category --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-3">Category <span class="text-red-400">*</span></label>
            <input type="hidden" name="category" id="categoryInput" value="{{ old('category', 'reading') }}">
            <div class="grid grid-cols-3 gap-2.5">
                @foreach($catMeta as $key => $opt)
                <button type="button" onclick="selectCat('{{ $key }}', this)"
                    class="cat-opt {{ old('category', 'reading') === $key ? 'sel' : '' }}">
                    <ion-icon name="{{ $opt['icon'] }}" style="font-size:20px;"></ion-icon>
                    <span class="text-[10px] font-extrabold text-center">{{ $opt['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Score --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-bold text-[#1E1B2E]">Score (0-100) <span class="text-red-400">*</span></label>
                <span id="scorePreview" class="text-lg font-extrabold text-[#8B46D3]">--</span>
            </div>
            <input type="range" name="score" id="scoreInput" min="0" max="100" value="{{ old('score', 50) }}"
                   class="w-full" oninput="updateScore()" style="accent-color:#8B46D3;">
            <div class="flex justify-between text-[10px] font-bold text-[#8B86A5] mt-1">
                <span>0</span><span>25</span><span>50</span><span>75</span><span>100</span>
            </div>

            {{-- Last score as comparison (keep nanny consistent) --}}
            <div id="lastScoreBox" class="hidden mt-3 rounded-xl bg-[#F3F0FD] p-3 text-center">
                <p class="text-[11px] font-bold text-[#8B46D3]" id="lastScoreText"></p>
            </div>
        </div>

        {{-- Rubric hint for score --}}
        <div class="bg-white rounded-2xl p-4 border border-[#DDD6EF]">
            <p class="text-[11px] font-bold text-[#8B86A5]" id="rubricHint">Move the slider to see the level.</p>
        </div>

        {{-- Date --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Date <span class="text-red-400">*</span></label>
            <input type="date" name="recorded_date" value="{{ old('recorded_date', date('Y-m-d')) }}" class="inp" required>
        </div>

        {{-- Note --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Note</label>
            <textarea name="note" rows="3" placeholder="e.g. Sekarang sudah bisa membaca suku kata sederhana" class="inp resize-none">{{ old('note') }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pb-2">
            <a href="{{ route('nanny-learning-show', $idAnak) }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Score</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Rubrik backend (5 tingkat) + kategori meta skor terakhir, disuntikkan dari controller.
const RUBRIC = @json($rubric ?? []);
const LAST_SCORES = @json($lastScores);
const CAT_META = @json($catMeta);
const EMPTY_RUBRIC = { label: '', min: 0, max: 0 };

function rubricFor(score) {
    let level = EMPTY_RUBRIC;
    for (const r of (RUBRIC || [])) {
        if (score >= r.min && score <= r.max) { level = r; break; }
    }
    return level;
}

function updateScore() {
    const score = parseInt(document.getElementById('scoreInput').value, 10);
    document.getElementById('scorePreview').textContent = score;
    const level = rubricFor(score);
    document.getElementById('rubricHint').textContent = (level.label && level.max)
        ? `Level: ${level.label} (${level.min}-${level.max})`
        : 'Move the slider to see the level.';
}

function selectCat(key, btn) {
    document.getElementById('categoryInput').value = key;
    document.querySelectorAll('.cat-opt').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');

    // Tampilkan skor terakhir kategori ini sebagai pembanding
    const last = LAST_SCORES[key];
    const box = document.getElementById('lastScoreBox');
    if (last && last.score !== null) {
        document.getElementById('lastScoreText').textContent =
            `Last ${CAT_META[key]?.label}: ${last.score} on ${last.date}`;
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
    }
}

// Init
updateScore();
selectCat(document.getElementById('categoryInput').value, document.querySelector('.cat-opt.sel'));
</script>
@endpush
