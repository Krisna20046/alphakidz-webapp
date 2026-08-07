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

    {{-- Table --}}
    @if(count($records) === 0)
    <div class="flex flex-col items-center py-8">
        <div class="w-14 h-14 rounded-full bg-[#EDE9FE] flex items-center justify-center mb-3">
            <ion-icon name="document-text-outline" style="font-size:26px;color:#C4B5FD;"></ion-icon>
        </div>
        <p class="text-[#8B86A5] text-xs font-semibold">No records yet.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] uppercase tracking-wider text-[#8B86A5] border-b border-[#EDE9FE]">
                    <th class="py-2 font-extrabold">Date</th>
                    <th class="py-2 font-extrabold">Category</th>
                    <th class="py-2 font-extrabold text-right">Score</th>
                    <th class="py-2 font-extrabold">Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                @php
                    $meta = $catMeta[$r['category']] ?? ['label'=>ucfirst($r['category'] ?? ''),'icon'=>'star-outline','color'=>'#8B46D3'];
                    $rubric = $r['rubric'] ?? null;
                @endphp
                <tr class="border-b border-[#F3F0FD]">
                    <td class="py-2.5 text-[12px] font-semibold text-[#8B86A5]">{{ $r['recorded_date'] }}</td>
                    <td class="py-2.5">
                        <span class="flex items-center gap-1.5 text-[12px] font-bold text-[#1E1B2E]">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $meta['color'] }};"></span>
                            {{ $meta['label'] }}
                        </span>
                    </td>
                    <td class="py-2.5 text-[12px] font-extrabold text-right" style="color:{{ $meta['color'] }};">{{ $r['score'] }}</td>
                    <td class="py-2.5 text-[11px] font-bold text-[#8B86A5]">{{ $rubric['label'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
