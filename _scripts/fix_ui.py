html_block = """                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Sidebar: Outlet & Calendar -->
                        <div class="w-full md:w-64 shrink-0 flex flex-col gap-3">
                            <!-- Outlet Dropdown -->
                            @if (auth()->check() && auth()->user()->isSuperAdmin())
                                <form method="GET" action="/database">
                                    <input type="hidden" name="view" value="habits">
                                    <input type="hidden" name="date" value="{{ $dateFilter }}">
                                    <select name="outlet_id" onchange="this.form.submit()" class="w-full text-xs font-bold text-slate-700 bg-white border border-slate-200 shadow-soft-xs rounded-xl focus:ring-2 focus:ring-slate-900 p-2 cursor-pointer hover:bg-slate-50 transition">
                                        @foreach ($outlets ?? [] as $outlet)
                                            <option value="{{ $outlet->id }}" {{ ($selectedOutletId ?? '') == $outlet->id ? 'selected' : '' }}>
                                                {{ $outlet->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif

                            <!-- Mini Visual Calendar Widget -->
                            @php
                                $cDate = \Carbon\Carbon::parse($dateFilter);
                                $sMonth = $cDate->copy()->startOfMonth();
                                $eMonth = $cDate->copy()->endOfMonth();
                                $sDow = $sMonth->dayOfWeekIso; 
                                $dim = $eMonth->day;
                                $tCells = $sDow - 1 + $dim;
                                $ePad = $tCells > 35 ? 42 - $tCells : 35 - $tCells;
                            @endphp
                            <div class="bg-white rounded-xl shadow-soft-xs border border-slate-200/80 p-3 w-full">
                                <div class="flex justify-between items-center mb-3 px-1">
                                    <a href="?view=habits&date={{ $cDate->copy()->subMonth()->format('Y-m-d') }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}" class="text-slate-400 hover:text-slate-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </a>
                                    <h3 class="text-sm font-black text-slate-800">{{ $cDate->format('F Y') }}</h3>
                                    <a href="?view=habits&date={{ $cDate->copy()->addMonth()->format('Y-m-d') }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}" class="text-slate-400 hover:text-slate-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                                <div class="grid grid-cols-7 gap-0.5 text-center mb-1">
                                    @foreach(['M','T','W','T','F','S','S'] as $dayName)
                                        <div class="text-[10px] font-extrabold text-slate-500 py-1">{{ $dayName }}</div>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-7 gap-0.5 text-center">
                                    @for($i = 1; $i < $sDow; $i++)
                                        <div class="text-xs text-slate-300 py-1.5 font-semibold bg-slate-50/50 rounded-md">
                                            {{ $sMonth->copy()->subDays($sDow - $i)->day }}
                                        </div>
                                    @endfor
                                    @for($day = 1; $day <= $dim; $day++)
                                        @php 
                                            $tDate = $cDate->copy()->day($day)->format('Y-m-d');
                                            $isSel = $tDate === $dateFilter;
                                        @endphp
                                        <a href="?view=habits&date={{ $tDate }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}" 
                                           class="text-xs py-1.5 font-bold rounded-md transition border {{ $isSel ? 'bg-amber-100 text-amber-900 border-amber-200' : 'text-slate-700 bg-white border-transparent hover:border-slate-200 hover:bg-slate-50' }}">
                                            {{ $day }}
                                        </a>
                                    @endfor
                                    @for($i = 1; $i <= $ePad; $i++)
                                        <div class="text-xs text-slate-300 py-1.5 font-semibold bg-slate-50/50 rounded-md">
                                            {{ $i }}
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Main Content Column -->
                        <div class="flex-1 flex flex-col gap-4">
                            <!-- Top Row: Compact Stats -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Selected Date -->
                                <div class="bg-white rounded-xl border border-slate-200/80 shadow-soft-xs p-3.5 flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Selected Date</div>
                                        <div class="text-lg font-extrabold text-slate-800">{{ $formattedDate }}</div>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                </div>
                                
                                <!-- Classification -->
                                <div class="bg-gradient-to-br from-{{ $classColor }}-50 to-white rounded-xl border border-{{ $classColor }}-200 shadow-soft-xs p-3.5 flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] font-black text-{{ $classColor }}-600 uppercase tracking-widest mb-0.5">{{ $classification }}</div>
                                        <div class="text-base font-extrabold text-{{ $classColor }}-900 leading-tight line-clamp-1" title="{{ $displayName }}">{{ $displayName }}</div>
                                    </div>
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-{{ $classColor }}-100 flex items-center justify-center text-{{ $classColor }}-500 border border-{{ $classColor }}-200">
                                        @if($classification === 'National Holiday')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                        @elseif($classification === 'Collective Leave')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @elseif($classification === 'Weekend')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- 5. Database Table (Moved inside the flex col) -->
                            <div class="bg-white rounded-xl shadow-soft-xs border border-slate-200/80 overflow-hidden flex-1 flex flex-col min-h-[250px]">
                                <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Visit Logs</h3>
                                </div>
                                <div class="overflow-x-auto overflow-y-auto flex-1">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="border-b border-slate-200 bg-white text-[10px] font-extrabold text-slate-400 uppercase tracking-wider sticky top-0 shadow-sm">
                                                <th class="px-4 py-2.5">Log ID (Customer)</th>
                                                <th class="px-4 py-2.5">Start Session</th>
                                                <th class="px-4 py-2.5">Time Elapsed</th>
                                                <th class="px-4 py-2.5">Table Number</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-[11px] font-semibold text-slate-700">
                                            @forelse($habitsDateLog as $log)
                                                <tr class="hover:bg-slate-50 transition">
                                                    <td class="px-4 py-3">
                                                        <span class="text-slate-400 font-bold mr-1">#{{ $log->id }}</span> 
                                                        {{ $log->customer_name ?? 'Walk-in Guest' }} 
                                                        <span class="text-slate-500 font-medium ml-1">({{ $log->pax }} pax)</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-indigo-600 font-bold">
                                                        {{ \Carbon\Carbon::parse($log->started_at)->format('H:i') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-emerald-600 font-bold">
                                                        {{ $log->time_elapsed ?? \Carbon\Carbon::parse($log->started_at)->diffInMinutes(\Carbon\Carbon::parse($log->ended_at)) . ' min' }}
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
                                                    <td colspan="4" class="p-8 text-center text-slate-400 font-semibold text-xs">No visits recorded on this date.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>"""

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will replace from `<div class="grid grid-cols-1 md:grid-cols-3 gap-6">` up to `<!-- 6. Gantt Graph -->`
# But we need to ensure we don't accidentally match anything else. 
# It's better to use exact string matching.

start_str = """                    <!-- 1. Yearly Calendar / Date Picker & 2. Current Date & 3/4. Classification -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">"""

end_str = """                    <!-- 6. Gantt Graph -->"""

start_idx = content.find(start_str)
end_idx = content.find(end_str)

if start_idx != -1 and end_idx != -1:
    new_content = content[:start_idx] + html_block + "\n\n" + content[end_idx:]
    with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
        f.write(new_content)
    print("Replaced UI with compact layout and visual calendar")
else:
    print("Could not find start or end str")
