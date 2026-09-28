@extends('layouts.app')

@section('title', 'Activity Timeline')

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('nanny-activity-timeline') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Activity Timeline</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">{{ $namaAnak }}</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">
    {{-- Child selector --}}
    <div class="flex items-center gap-2 px-1.5 mb-2 overflow-x-auto hide-scrollbar">
        <span class="text-[11px] font-bold text-[#8B86A5] shrink-0 mx-1">Child:</span>
        @foreach($anakList as $ks => $k)
        <a href="{{ route('nanny-activity-timeline-show', $k['id']) }}"
           class="shrink-0 rounded-full px-3 py-1.5 text-[11px] font-bold border
                  {{ ($k['id'] ?? 0) === $idAnak ? 'border-[#8B46D3] bg-[#8B46D3] text-white' : 'border-[#DDD6EF] bg-white text-[#8B86A5]' }}">
            {{ $k['nama'] }}
        </a>
        @endforeach
    </div>

    {{-- Filters: date + source_type --}}
    <div class="flex items-center gap-2 mt-2 px-1.5">
        <input type="date" id="tlDate" value="{{ $date }}" data-idanak="{{ $idAnak }}"
               class="text-[12px] font-bold rounded-xl px-2.5 py-1.5 bg-white border border-[#DDD6EF] text-[#8B86A5] w-40">
        <select id="tlSource" class="text-[12px] font-bold rounded-xl px-2.5 py-1.5 bg-white border border-[#DDD6EF] text-[#8B86A5]">
            <option value="" {{ empty($sourceType) ? 'selected' : '' }}>All types</option>
            <option value="diary"           {{ $sourceType === 'diary' ? 'selected' : '' }}>Diary</option>
            <option value="task_progress"   {{ $sourceType === 'task_progress' ? 'selected' : '' }}>Task Progress</option>
            <option value="assistant_note"  {{ $sourceType === 'assistant_note' ? 'selected' : '' }}>Notes</option>
            <option value="attendance"      {{ $sourceType === 'attendance' ? 'selected' : '' }}>Attendance</option>
            <option value="reminder"        {{ $sourceType === 'reminder' ? 'selected' : '' }}>Reminders</option>
        </select>
        <button type="button" onclick="tlReload()"
                class="rounded-xl px-2.5 py-1.5 text-[12px] font-bold bg-[#8B46D3] text-white">Filter</button>
    </div>

    {{-- Timeline list --}}
    <div id="tlList" class="mt-3">
        @include('nanny.activity-timeline._timeline', [
            'idAnak' => $idAnak, 'records' => $records, 'pagination' => $pagination
        ])
    </div>
</div>
@endsection

@push('scripts')
<script>
    function tlQueryParams() {
        var q = new URLSearchParams();
        var d = document.getElementById('tlDate').value;
        var s = document.getElementById('tlSource').value;
        if (d) q.set('date', d);
        if (s) q.set('source_type', s);
        return q.toString();
    }
    document.getElementById('tlDate').addEventListener('change', tlReload);
    document.getElementById('tlSource').addEventListener('change', tlReload);
    function tlReload() {
        var idAnak = document.getElementById('tlDate').dataset.idanak;
        window.location.href = "{{ route('nanny-activity-timeline-show', '__ID__') }}".replace('__ID__', idAnak) + '?' + tlQueryParams();
    }
    function tlGoToPage(p) {
        var idAnak = document.getElementById('tlDate').dataset.idanak;
        var q = tlQueryParams();
        if (q) q += '&';
        window.location.href = "{{ route('nanny-activity-timeline-show', '__ID__') }}".replace('__ID__', idAnak) + '?' + q + 'page=' + p;
    }
</script>
@endpush