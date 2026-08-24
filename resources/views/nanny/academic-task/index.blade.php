@extends('layouts.app')

@section('title', 'Academic Tasks')

@push('styles')
<style>
    @keyframes floatEmpty { 0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)} }
    .float-anim { animation:floatEmpty 3s ease-in-out infinite; }
    @keyframes toastIn { from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);} }
    .toast { animation:toastIn .3s ease forwards; }
</style>
@endpush

@section('content')
<div class="anim delay-1 relative z-10 bg-[#8B46D3] bg-[url('/assets/bg-texture.png')] bg-cover bg-center
            px-[24px] pt-[55px] pb-[72px]
            before:content-[''] before:absolute before:inset-0 before:bg-[#8B46D3] before:opacity-60 before:-z-10">
    <div class="flex items-center gap-3 relative z-10">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-full bg-white/20 border-[1.5px] border-white/30 flex items-center justify-center shrink-0">
            <ion-icon name="arrow-back" class="text-white" style="font-size:18px;"></ion-icon>
        </a>
        <div>
            <span class="text-white text-[17px] font-extrabold tracking-wide">Academic Tasks</span>
            <p class="text-white/60 text-xs font-medium mt-0.5">
                @if($pagination) {{ $pagination['total'] }} tasks @else {{ count($tasks) }} tasks @endif
            </p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-y-auto px-[20px] pt-[24px] pb-28 bg-gradient-to-b from-[#F8F7FF] via-[#F8F7FF] to-[#D4BAEF]/50 rounded-t-[50px] -mt-[50px] relative z-20 hide-scrollbar">

    {{-- Flash Toast --}}
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

    @include('nanny.academic-task._list', [
        'tasks'      => $tasks,
        'pagination' => $pagination,
        'status'     => $status,
        'type'       => $type,
        'subject'    => $subject,
    ])

</div>

{{-- Page loading overlay --}}
<div id="pageLoader" class="hidden fixed inset-0 z-50 bg-white/60 flex items-center justify-center">
    <div class="flex items-center gap-3 bg-white rounded-2xl px-6 py-4 shadow-xl border border-[#EDE9FE]">
        <div class="w-5 h-5 border-2 border-[#EDE9FE] border-t-[#8B46D3] rounded-full" style="animation:spin .6s linear infinite;"></div>
        <span class="text-sm font-bold text-[#8B46D3]">Loading...</span>
    </div>
</div>

{{-- FAB --}}
<div class="fixed bottom-[80px] right-[20px] sm:right-[calc(50%-175px)] z-30">
    <a href="{{ route('academic-task.create') }}"
       class="w-14 h-14 rounded-2xl bg-[#8B46D3] shadow-xl shadow-[#8B46D3]/40 flex items-center justify-center block">
        <ion-icon name="add" style="font-size:26px;color:#fff;"></ion-icon>
    </a>
</div>
@endsection

@push('scripts')
<style>@keyframes spin { to{transform:rotate(360deg);} }</style>
<script>
let currentStatus = '{{ $status }}';
let currentType   = '{{ $type }}';

function toggleFilterMenu() {
    document.getElementById('filterMenu').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const menu = document.getElementById('filterMenu');
    if (!menu) return;
    const btn = e.target.closest('button[onclick="toggleFilterMenu()"]');
    if (!menu.contains(e.target) && !btn) menu.classList.add('hidden');
});

function filterByStatus(e, status) {
    e.preventDefault();
    currentStatus = status;
    loadTasks(1);
    document.getElementById('filterMenu').classList.add('hidden');
}
function filterByType(e, type) {
    e.preventDefault();
    currentType = type;
    loadTasks(1);
    document.getElementById('filterMenu').classList.add('hidden');
}
function goToPage(page) {
    if (page < 1) return;
    loadTasks(page);
}

function loadTasks(page) {
    const params = new URLSearchParams();
    if (currentStatus) params.set('status', currentStatus);
    if (currentType)   params.set('type', currentType);
    params.set('page', page);

    document.getElementById('pageLoader')?.classList.remove('hidden');

    fetch(`{{ route('academic-task.index') }}?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => { if (!res.ok) throw new Error('Request failed'); return res.text(); })
    .then(html => {
        const container = document.getElementById('listWrapper');
        if (container) container.outerHTML = html;
        const url = new URL(window.location);
        if (currentStatus) url.searchParams.set('status', currentStatus); else url.searchParams.delete('status');
        if (currentType)   url.searchParams.set('type', currentType);     else url.searchParams.delete('type');
        url.searchParams.set('page', page);
        window.history.pushState({}, '', url);
    })
    .catch(() => showAppAlert('Failed to load tasks.'))
    .finally(() => document.getElementById('pageLoader')?.classList.add('hidden'));
}

window.addEventListener('popstate', function() {
    const url = new URL(window.location);
    currentStatus = url.searchParams.get('status') || '';
    currentType   = url.searchParams.get('type') || '';
    loadTasks(parseInt(url.searchParams.get('page') || '1', 10));
});

const toastEl = document.getElementById('toast');
if (toastEl) setTimeout(() => toastEl.remove(), 4000);
</script>
@endpush
