<div id="historyList" data-idanak="{{ $idAnak }}">

    {{-- Count + pagination --}}
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold text-[#8B86A5]">
            {{ $pagination ? $pagination['total'] : count($records) }} report{{ $pagination ? ($pagination['total'] === 1 ? '' : 's') : (count($records) === 1 ? '' : 's') }}
        </span>
        @if($pagination && $pagination['last_page'] > 1)
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" onclick="wrGoToPage({{ $pagination['current_page'] - 1 }})"
               class="px-2.5 py-1 rounded-lg bg-white border border-[#DDD6EF] text-[#8B86A5] {{ $pagination['current_page'] <= 1 ? 'opacity-30 pointer-events-none' : '' }}">
                <ion-icon name="chevron-back" style="font-size:14px;"></ion-icon>
            </button>
            <span class="px-2 text-[#8B86A5]"><span class="text-[#8B46D3]">{{ $pagination['current_page'] }}</span>/{{ $pagination['last_page'] }}</span>
            <button type="button" onclick="wrGoToPage({{ $pagination['current_page'] + 1 }})"
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
        <p class="text-[#8B86A5] text-xs font-semibold">No report yet. Generate one above.</p>
    </div>
    @else
    <div class="flex flex-col gap-2">
        @foreach($records as $r)
        @php
            $hasPdf = !empty($r['pdf_path']);
            $weekStart = $r['week_start'] ?? '';
            $weekEnd   = $r['week_end'] ?? '';
        @endphp
        <div class="rounded-2xl border border-[#EAE6F5] p-3 bg-white">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-[#EDE9FE]">
                    <ion-icon name="document-text-outline" style="font-size:16px;color:#8B46D3;"></ion-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[12px] font-extrabold text-[#1E1B2E]">
                            @if($weekStart && $weekEnd)
                                {{ \Carbon\Carbon::parse($weekStart)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($weekEnd)->translatedFormat('d M Y') }}
                            @else
                                {{ $weekStart ?: '—' }}
                            @endif
                        </span>
                        <span class="text-[10px] font-bold text-[#8B86A5]">
                            @if(!empty($r['generated_at']))
                                {{ \Carbon\Carbon::parse($r['generated_at'])->translatedFormat('d M Y H:i') }}
                            @endif
                        </span>
                    </div>

                    @if(!empty($r['summary']))
                    <p class="text-[11px] font-semibold text-[#4B4763] leading-relaxed mt-1.5 line-clamp-3">{{ $r['summary'] }}</p>
                    @endif

                    {{-- Status --}}
                    <div class="mt-2">
                        @if($hasPdf)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F0FDF4] text-[#16A34A]">
                            <ion-icon name="checkmark-circle" style="font-size:11px;"></ion-icon>
                            Ready · PDF tersedia
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF3C7] text-[#D97706]">
                            <ion-icon name="time-outline" style="font-size:11px;"></ion-icon>
                            No data — ringkasan kosong
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 mt-2">
                @if($hasPdf)
                <button type="button" onclick="wrPdfOpen('{{ route('nanny-weekly-report-view', $r['id']) }}')"
                   class="flex-1 py-2 rounded-xl bg-white border border-[#DDD6EF] text-[#8B46D3] text-[12px] font-extrabold flex items-center justify-center gap-1.5 active:scale-[0.98] transition-transform">
                    <ion-icon name="eye-outline" style="font-size:15px;"></ion-icon>
                    Lihat
                </button>
                <a href="{{ route('nanny-weekly-report-download', $r['id']) }}"
                   class="flex-1 py-2 rounded-xl bg-[#8B46D3] text-white text-[12px] font-extrabold flex items-center justify-center gap-1.5">
                    <ion-icon name="download-outline" style="font-size:15px;"></ion-icon>
                    Download
                </a>
                @else
                <span class="flex-1 py-2 rounded-xl border border-dashed border-[#D6CCEA] text-[#8B86A5] text-[12px] font-bold text-center">
                    PDF akan muncul setelah ada data
                </span>
                @endif

                @if($r['id'])
                <form method="POST" action="{{ route('nanny-weekly-report-regenerate', $r['id']) }}"
                      class="shrink-0"
                      onsubmit="event.preventDefault(); wrConfirmRegenerate('{{ route('nanny-weekly-report-regenerate', $r['id']) }}'); return false;">
                    @csrf
                    <input type="hidden" name="id_anak" value="{{ $idAnak }}">
                    <button type="submit" aria-label="Regenerate"
                        class="w-9 h-9 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                        <ion-icon name="refresh-outline" style="font-size:17px;color:#8B46D3;"></ion-icon>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>