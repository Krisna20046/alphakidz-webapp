@extends('layouts.app')

@section('title', 'Add Assistant Note')

@push('styles')
<style>
    .inp {
        width:100%; background:#F8F7FF; border:1.5px solid #DDD6EF;
        border-radius:12px; padding:12px 16px; font-size:14px;
        color:#1E1B2E; outline:none; transition:border-color .2s;
        font-family:'Nunito',sans-serif; font-weight:600;
    }
    .inp:focus { border-color:#8B46D3; }
    .mood-opt {
        flex:1; padding:12px 6px; border-radius:14px; background:#F8F7FF;
        border:2px solid #DDD6EF; display:flex; flex-direction:column;
        align-items:center; gap:4px; cursor:pointer; transition:all .15s;
        color:#8B86A5;
    }
    .mood-opt.sel { border-color:#8B46D3; background:#EDE9FE; color:#8B46D3; }
    .mood-opt:active { transform:scale(.96); }
    .act-btn { transition:transform .1s ease; }
    .act-btn:active { transform:scale(0.96); }
</style>
@endpush

@php
    $moodMeta = [
        'senang' => ['label' => 'Senang', 'icon' => 'happy-outline',      'color' => '#16A34A'],
        'sedih'  => ['label' => 'Sedih',  'icon' => 'sad-outline',        'color' => '#3B82F6'],
        'marah'  => ['label' => 'Marah',  'icon' => 'angry-outline',      'color' => '#DC2626'],
        'biasa'  => ['label' => 'Biasa',  'icon' => 'remove-circle-outline', 'color' => '#6B7280'],
    ];
@endphp

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-notes-show', $idAnak) }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Add Note</span>
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

    <form action="{{ route('nanny-notes-store') }}" method="POST" class="space-y-4 anim delay-2">
        @csrf
        <input type="hidden" name="id_anak" value="{{ $idAnak }}">

        {{-- Child (readonly) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Child</label>
            <input type="text" value="{{ $namaAnak }}" class="inp" disabled>
        </div>

        {{-- Mood --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-3">Mood <span class="text-red-400">*</span></label>
            <input type="hidden" name="mood" id="moodInput" value="{{ old('mood', 'biasa') }}">
            <div class="grid grid-cols-4 gap-2.5">
                @foreach($moodMeta as $key => $opt)
                <button type="button" onclick="selectMood('{{ $key }}', this)"
                    class="mood-opt {{ old('mood', 'biasa') === $key ? 'sel' : '' }}">
                    <ion-icon name="{{ $opt['icon'] }}" style="font-size:22px;color:{{ $opt['color'] }};"></ion-icon>
                    <span class="text-[10px] font-extrabold text-center">{{ $opt['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Related task (optional) --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Related Task <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <select name="task_id" class="inp appearance-none">
                <option value="">— None —</option>
                @foreach($tasks as $t)
                <option value="{{ $t['id'] }}" {{ old('task_id') == $t['id'] ? 'selected' : '' }}>{{ $t['title'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Highlight --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Highlight <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <textarea name="highlight" rows="3" placeholder="e.g. Belajar membaca suku kata, sangat antusias" class="inp resize-none">{{ old('highlight') }}</textarea>
        </div>

        {{-- Concern --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Concern <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <textarea name="concern" rows="3" placeholder="e.g. Masih kesulitan fokus setelah jam makan siang" class="inp resize-none">{{ old('concern') }}</textarea>
        </div>

        {{-- Recommendation --}}
        <div class="bg-white rounded-2xl p-5 border border-[#DDD6EF]">
            <label class="block text-sm font-bold text-[#1E1B2E] mb-2">Rekomendasi <span class="text-[#8B86A5] font-semibold">(optional)</span></label>
            <textarea name="recommendation" rows="3" placeholder="e.g. Kurangi screen time sebelum tidur" class="inp resize-none">{{ old('recommendation') }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pb-2">
            <a href="{{ route('nanny-notes-show', $idAnak) }}"
               class="act-btn flex-1 py-4 rounded-2xl bg-[#EDE9FE] text-[#8B46D3] text-sm font-bold text-center">Cancel</a>
            <button type="submit" class="act-btn flex-1 py-4 rounded-2xl bg-[#8B46D3] text-white text-sm font-bold shadow-lg shadow-[#8B46D3]/30">Save Note</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectMood(key, btn) {
    document.getElementById('moodInput').value = key;
    document.querySelectorAll('.mood-opt').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
}
</script>
@endpush
