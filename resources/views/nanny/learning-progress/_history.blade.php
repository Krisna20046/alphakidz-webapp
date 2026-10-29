@php
    $catMeta = [
        'reading'       => ['label' => 'Reading',        'icon' => 'book-outline',            'color' => '#8B46D3'],
        'math'          => ['label' => 'Math',           'icon' => 'calculator-outline',      'color' => '#4F46E5'],
        'science'       => ['label' => 'Science',        'icon' => 'flask-outline',           'color' => '#0891B2'],
        'language'      => ['label' => 'Language',       'icon' => 'chatbubbles-outline',     'color' => '#0E9F6E'],
        'focus'         => ['label' => 'Focus',          'icon' => 'eye-outline',             'color' => '#F59E0B'],
        'communication' => ['label' => 'Communication',  'icon' => 'people-outline',          'color' => '#E11D48'],
    ];
@endphp

<div id="historyList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} records
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="lpGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="lpGoToPage({{ $pagination['current_page'] + 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] >= $pagination['last_page'] ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-forward" style="font-size:14px;"></ion-icon>
            </button>
        </div>
        @endif
    </div>

    {{-- List --}}
    @if(count($records) === 0)
    <div class="flex flex-col items-center py-8">
        <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
            <ion-icon name="document-text-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No records yet. Add your first score.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        @php
            $meta = $catMeta[$r['category']] ?? ['label'=>ucfirst($r['category'] ?? ''),'icon'=>'star-outline','color'=>'#8B46D3'];
            $rubric = $r['rubric'] ?? null;
        @endphp
        <div class="flex items-start gap-3 rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-white"
                 style="background:{{ $meta['color'] }};">
                <ion-icon name="{{ $meta['icon'] }}" style="font-size:16px;"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <span class="text-[13px] font-extrabold text-[#1E1B2E]">{{ $meta['label'] }}</span>
                    <span class="text-[13px] font-extrabold" style="color:{{ $meta['color'] }};">{{ $r['score'] }}</span>
                </div>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] font-bold text-[#8B86A5]">
                        {{ $r['recorded_date'] }}
                        @if($rubric)
                        · <span class="text-[#8B46D3]">{{ $rubric['label'] }}</span>
                        @endif
                    </span>
                </div>
                @if(!empty($r['note']))
                <p class="text-[11px] font-semibold text-[#6B7280] mt-1.5">{{ $r['note'] }}</p>
                @endif
            </div>
            <form action="{{ route('nanny-learning-destroy', $r['id']) }}" method="POST"
                  onsubmit="return confirm('Delete this record?');" class="shrink-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-7 h-7 rounded-lg bg-[#FEF2F2] flex items-center justify-center">
                    <ion-icon name="trash-outline" style="font-size:14px;color:#DC2626;"></ion-icon>
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endif
</div>
