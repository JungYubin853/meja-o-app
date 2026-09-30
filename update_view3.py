with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Add button
button_html = """
            @if (Auth::user()->hasPermission('rep_waitlist'))
                <a href="/database?view=waitlist"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'waitlist' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Waitlist
                </a>
            @endif

            <a href="/database?view=habits"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'habits' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Customer Habits
            </a>
"""
content = re.sub(r'@if \(Auth::user\(\)->hasPermission\(\'rep_waitlist\'\)\)\s*<a href="/database\?view=waitlist".*?</a>\s*@endif', button_html, content, flags=re.DOTALL)

# Add Habits view right after <!-- HOURLY (DAILY) REPORT VIEW --> ... @endif
# Actually, let's inject it right before:
#         @if ($viewMode !== 'overall' && $viewMode !== 'waitlist')
#         <!-- VISITOR DINING LOG ENTRIES
# We will use this exact string to replace.

habits_view = """
        <!-- CUSTOMER HABITS VIEW -->
        @if ($viewMode === 'habits')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Average Time by Day -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-800">Average Stay Duration (Minutes) by Day</h3>
                    <div class="h-56 flex items-end gap-1.5 pt-8 pb-3 border-b border-slate-200 overflow-x-auto touch-scroll relative">
                        @php $maxTime = max(array_merge(array_values($habitsData), [1])); @endphp
                        @foreach ($habitsData as $day => $minutes)
                            @php
                                $heightPercent = ($minutes / max($maxTime, 1)) * 100;
                            @endphp
                            <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[28px]"
                                title="{{ $day }} &mdash; {{ $minutes }} min avg">
                                <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                    {{ $minutes }} min
                                </div>
                                <div class="w-full bg-indigo-500 group-hover:bg-indigo-600 rounded-t-md transition-all cursor-pointer"
                                    style="height: {{ $heightPercent }}%;"></div>
                                <span class="text-[10px] sm:text-xs font-semibold text-slate-500 mt-2 rotate-45 origin-left truncate">{{ substr($day, 0, 3) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Weekday vs Weekend/Holiday -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-800">Visits: Weekdays vs Weekends & Holidays</h3>
                    <div class="h-56 flex items-end gap-4 pt-8 pb-3 border-b border-slate-200 justify-center">
                        @php $maxVisits = max(array_merge(array_values($weekdayWeekendData), [1])); @endphp
                        @foreach ($weekdayWeekendData as $type => $count)
                            @php
                                $heightPercent = ($count / max($maxVisits, 1)) * 100;
                            @endphp
                            <div class="w-24 flex flex-col items-center h-full justify-end group relative"
                                title="{{ $type }} &mdash; {{ $count }} visits">
                                <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                    {{ $count }} visits
                                </div>
                                <div class="w-full {{ $type == 'Weekday' ? 'bg-emerald-500 group-hover:bg-emerald-600' : 'bg-amber-500 group-hover:bg-amber-600' }} rounded-t-md transition-all cursor-pointer"
                                    style="height: {{ $heightPercent }}%;"></div>
                                <span class="text-[11px] sm:text-xs font-bold text-slate-700 mt-2 text-center">{{ $type }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')
        <!-- VISITOR DINING LOG ENTRIES"""

content = content.replace("""        @if ($viewMode !== 'overall' && $viewMode !== 'waitlist')
        <!-- VISITOR DINING LOG ENTRIES""", habits_view)

# The form header has:
#         @if ($viewMode !== 'overall' && $viewMode !== 'waitlist')
#                     <form method="GET" action="/database"
# Let's also exclude habits from that:
content = content.replace("""        @if ($viewMode !== 'overall' && $viewMode !== 'waitlist')
                    <form method="GET" action="/database\"""", """        @if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')
                    <form method="GET" action="/database\"""")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done database.blade.php")
