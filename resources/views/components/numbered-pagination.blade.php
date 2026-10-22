@props(['paginator'])

@php
    $total = $paginator->lastPage();
    $current = $paginator->currentPage();
    $delta = 2;
    $range = [];

    if ($total <= 7) {
        $range = range(1, max(1, $total));
    } else {
        $left = max(2, $current - $delta);
        $right = min($total - 1, $current + $delta);

        $range[] = 1;
        if ($left > 2) {
            $range[] = '...';
        }
        for ($i = $left; $i <= $right; $i++) {
            $range[] = $i;
        }
        if ($right < $total - 1) {
            $range[] = '...';
        }
        $range[] = $total;
    }
@endphp

@if ($total > 1)
<div class="flex items-center gap-1 overflow-x-auto py-1">
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
        <span class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-300 cursor-not-allowed text-xs font-bold shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 transition text-xs font-bold shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
    @endif

    <!-- Page Numbers & Ellipses -->
    @foreach ($range as $page)
        @if ($page === '...')
            <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
        @elseif ($page == $current)
            <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold bg-slate-900 text-white shadow-2xs shrink-0">
                {{ $page }}
            </span>
        @else
            <a href="{{ $paginator->url($page) }}"
               class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-semibold bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 transition shrink-0">
                {{ $page }}
            </a>
        @endif
    @endforeach

    <!-- Next Page Link -->
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 transition text-xs font-bold shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <span class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-300 cursor-not-allowed text-xs font-bold shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </span>
    @endif
</div>
@endif
