file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We find the start and end of the Habits block
start_marker = r"<!-- CUSTOMER HABITS VIEW -->"
end_marker = r"<!-- OVERALL VIEW -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx == -1 or end_idx == -1:
    print("Could not find markers")
else:
    # Build replacement
    replacement = """<!-- CUSTOMER HABITS VIEW -->
        @if ($viewMode === 'habits')
            <div x-data="{ 
                activeTab: 'table',
                perPage: 10,
                currentPage: 1,
                totalRecords: {{ count($habitsDateLog) }},
                get totalPages() { return Math.max(1, Math.ceil(this.totalRecords / this.perPage)); },
                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; }
            }" class="flex flex-col gap-4">

                <!-- 1. Top Bar (One long container) -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                        @if (auth()->check() && auth()->user()->isSuperAdmin())
                            <form method="GET" action="/database" class="m-0 p-0 w-full sm:w-auto">
                                <input type="hidden" name="view" value="habits">
                                <input type="hidden" name="date" value="{{ $dateFilter }}">
                                <select name="outlet_id" onchange="this.form.submit()" class="h-10 w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-soft-xs cursor-pointer sm:w-auto">
                                    @foreach ($outlets ?? [] as $outlet)
                                        <option value="{{ $outlet->id }}" {{ ($selectedOutletId ?? '') == $outlet->id ? 'selected' : '' }}>
                                            {{ $outlet->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        <form method="GET" action="/database" class="flex items-center gap-2 m-0 p-0 w-full sm:w-auto">
                            <input type="hidden" name="view" value="habits">
                            @if (isset($selectedOutletId)) <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}"> @endif
                            <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                        </form>
                    </div>

                    @php
                        $dateObj = \\Carbon\\Carbon::parse($dateFilter);
                        $dayName = $dateObj->format('l');
                        
                        // Holidays lookup logic
                        $year = $dateObj->year;
                        $holidaysData = \\Illuminate\\Support\\Facades\\Cache::get("holidays_{$year}");
                        $holidayName = null;
                        $holidayType = null;
                        if ($holidaysData && isset($holidaysData['data']) && is_array($holidaysData['data'])) {
                            foreach($holidaysData['data'] as $h) {
                                if(isset($h['date']) && $h['date'] === $dateFilter) {
                                    $holidayName = $h['name'];
                                    $holidayType = (isset($h['type']) && $h['type'] === 'leave') ? 'Collective Leave' : 'National Holiday';
                                    break;
                                }
                            }
                        }

                        if ($holidayName) {
                            $classification = $holidayType;
                            $classColor = $holidayType == 'Collective Leave' ? 'amber' : 'rose';
                            $displayName = $dayName . ', ' . $holidayName;
                            $bgClass = 'bg-gradient-to-br from-' . $classColor . '-50 to-white';
                        } else {
                            $isWeekend = $dateObj->isWeekend();
                            $classification = $isWeekend ? 'Weekend' : 'Weekday';
                            $classColor = 'slate';
                            $displayName = $dayName;
                            $bgClass = 'bg-white';
                        }
                    @endphp

                    <div class="{{ $bgClass }} rounded-xl border border-{{ $classColor }}-200 shadow-soft-xs px-3 py-1.5 flex items-center gap-3 shrink-0">
                        <div class="truncate text-right">
                            <div class="text-[9px] font-black text-{{ $classColor }}-600 uppercase tracking-widest mb-0.5">{{ $classification }}</div>
                            <div class="text-xs font-extrabold text-{{ $classColor }}-900 leading-tight truncate" title="{{ $displayName }}">{{ $displayName }}</div>
                        </div>
                        <div class="w-8 h-8 shrink-0 rounded-full bg-{{ $classColor }}-100 flex items-center justify-center text-{{ $classColor }}-500 border border-{{ $classColor }}-200">
                            @if($classification === 'National Holiday')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            @elseif($classification === 'Collective Leave')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @elseif($classification === 'Weekend')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. Main Content Box (Tabbed) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">
                    
                    <!-- Header with Tabs and Pagination -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-slate-50/50">
                        <!-- Tabs -->
                        <div class="flex bg-slate-200/50 p-1 rounded-lg border border-slate-200/60 w-full sm:w-auto">
                            <button @click="activeTab = 'table'; currentPage = 1" :class="activeTab === 'table' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700'" class="flex-1 sm:flex-none px-4 py-1.5 text-xs font-bold rounded-md transition flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                Visit Logs
                            </button>
                            <button @click="activeTab = 'gantt'; currentPage = 1" :class="activeTab === 'gantt' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700'" class="flex-1 sm:flex-none px-4 py-1.5 text-xs font-bold rounded-md transition flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                Gantt Graph
                            </button>
                        </div>

                        <!-- Pagination Controls -->
                        <div class="flex items-center justify-between w-full lg:w-auto gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-500">Rows per page:</span>
                                <select x-model.number="perPage" @change="currentPage = 1" class="h-8 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg px-2 py-0 focus:ring-0 focus:border-slate-300">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="prevPage()" :disabled="currentPage === 1" :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-200'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 border border-slate-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <span class="text-xs font-bold text-slate-700 w-16 text-center">
                                    <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                                </span>
                                <button @click="nextPage()" :disabled="currentPage === totalPages" :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-200'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 border border-slate-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE VIEW -->
                    <div x-show="activeTab === 'table'" class="overflow-x-auto overflow-y-auto">
                        <table class="w-full text-left border-collapse min-h-[300px]">
                            <thead>
                                <tr class="border-b border-slate-200 bg-white text-[10px] font-extrabold text-slate-400 uppercase tracking-wider sticky top-0 shadow-sm z-10">
                                    <th class="px-4 py-3 bg-white">Log ID (Customer)</th>
                                    <th class="px-4 py-3 bg-white">Start Session</th>
                                    <th class="px-4 py-3 bg-white">Time Elapsed</th>
                                    <th class="px-4 py-3 bg-white">Table Number</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-[11px] font-semibold text-slate-700">
                                @forelse($habitsDateLog as $index => $log)
                                    <tr class="hover:bg-slate-50 transition" x-show="currentPage === Math.ceil(({{ $index }} + 1) / perPage)">
                                        <td class="px-4 py-3">
                                            <span class="text-slate-400 font-bold mr-1">#{{ $log->id }}</span> 
                                            {{ $log->customer_name ?? 'Walk-in Guest' }} 
                                            <span class="text-slate-500 font-medium ml-1">({{ $log->pax }} pax)</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="font-bold text-slate-800">{{ \\Carbon\\Carbon::parse($log->started_at)->format('H:i') }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $log->time_elapsed ?? \\Carbon\\Carbon::parse($log->started_at)->diffInMinutes(\\Carbon\\Carbon::parse($log->ended_at)) . ' min' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($log->table)
                                                <span class="inline-flex items-center px-1.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 rounded text-[10px] font-bold">
                                                    {{ $log->table->table_number ?? 'Table ' . $log->table->id }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-12 text-center text-slate-400 font-semibold text-xs">No visits recorded on this date.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- GANTT VIEW -->
                    <div x-show="activeTab === 'gantt'" class="p-4 bg-slate-50/20" style="display: none;">
                        @if(count($habitsDateLog) > 0)
                            <div class="relative w-full overflow-x-auto touch-scroll pb-4">
                                <div class="min-w-[800px]">
                                    <!-- Gantt Header (Time Axis 00:00 to 24:00) -->
                                    <div class="flex items-end h-8 mb-2 border-b border-slate-200 relative ml-32">
                                        @for($h = 0; $h <= 24; $h += 2)
                                            <div class="absolute text-[10px] font-bold text-slate-400 -translate-x-1/2" style="left: {{ ($h / 24) * 100 }}%">
                                                {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00
                                            </div>
                                            @if($h < 24)
                                                <div class="absolute h-2 border-l border-slate-200 bottom-0" style="left: {{ ($h / 24) * 100 }}%"></div>
                                            @endif
                                        @endfor
                                        <div class="absolute h-2 border-l border-slate-200 bottom-0" style="left: 100%"></div>
                                    </div>

                                    <!-- Gantt Rows -->
                                    <div class="flex flex-col gap-1.5 relative">
                                        <!-- Grid lines for background -->
                                        <div class="absolute inset-0 ml-32 pointer-events-none flex">
                                            @for($h = 0; $h < 24; $h += 2)
                                                <div class="flex-1 border-l border-dashed border-slate-100"></div>
                                            @endfor
                                            <div class="border-l border-dashed border-slate-100 h-full"></div>
                                        </div>

                                        @foreach($habitsDateLog as $index => $log)
                                            @php 
                                                $start = \\Carbon\\Carbon::parse($log->started_at);
                                                $end = $log->ended_at ? \\Carbon\\Carbon::parse($log->ended_at) : now();
                                                $startMinOfDay = ($start->hour * 60) + $start->minute;
                                                $endMinOfDay = ($end->hour * 60) + $end->minute;
                                                if ($endMinOfDay < $startMinOfDay) $endMinOfDay = 1440;
                                                
                                                $leftPct = ($startMinOfDay / 1440) * 100;
                                                $widthPct = (($endMinOfDay - $startMinOfDay) / 1440) * 100;
                                            @endphp
                                            <div class="flex items-center group relative" x-show="currentPage === Math.ceil(({{ $index }} + 1) / perPage)">
                                                <div class="w-32 shrink-0 pr-4 text-right truncate">
                                                    <div class="text-xs font-bold text-slate-700 truncate" title="{{ $log->customer_name }}">{{ $log->customer_name ?? 'Walk-in' }}</div>
                                                    <div class="text-[10px] font-semibold text-slate-400">
                                                        @if($log->table) Tbl {{ $log->table->table_number ?? $log->table->id }} @else - @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="flex-1 h-8 relative bg-slate-50/50 rounded-lg hover:bg-slate-50 transition">
                                                    <div class="absolute top-1.5 bottom-1.5 bg-indigo-500 rounded-md border border-indigo-600 shadow-sm flex items-center justify-center overflow-hidden group-hover:bg-indigo-600 transition cursor-pointer"
                                                         style="left: {{ $leftPct }}%; width: {{ max($widthPct, 0.5) }}%;"
                                                         title="Start: {{ $start->format('H:i') }} | End: {{ $end->format('H:i') }} | Elapsed: {{ $log->time_elapsed ?? $start->diffInMinutes($end).' min' }}">
                                                        @if($widthPct > 5)
                                                            <span class="text-[10px] font-extrabold text-white truncate px-1">
                                                                {{ $log->time_elapsed ?? $start->diffInMinutes($end).' min' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-10">
                                <svg class="w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8l-8 8 8 8"></path></svg>
                                <p class="text-sm text-slate-400 font-semibold">No visits recorded to graph.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        @endif
        
        <!-- OVERALL VIEW -->"""

    new_content = content[:start_idx] + replacement + content[end_idx + len(end_marker):]
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(new_content)
    print("Replaced CUSTOMER HABITS block perfectly!")
