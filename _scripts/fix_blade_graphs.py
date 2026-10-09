with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Find the insertion point inside the habits block
marker = """                    @endforeach
                </div>
            </div>"""

new_graphs = """                    @endforeach
                </div>
            </div>

            <!-- New Graphs for Day of Week and Time of Day -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                
                <!-- Weekly Graph -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-800">Average Stay Duration by Day of Week</h3>
                    <div class="h-56 flex items-end gap-1.5 pt-8 pb-3 border-b border-slate-200 overflow-x-auto touch-scroll relative justify-between">
                        @foreach ($habitsDayOfWeek as $day => $minutes)
                            @php
                                $heightPercent = ($minutes / max($maxHabitsDay, 1)) * 100;
                            @endphp
                            <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[28px]"
                                title="{{ $day }} &mdash; {{ $minutes }} min avg">
                                <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                    {{ $minutes }} min
                                </div>
                                <div class="w-full bg-emerald-500 group-hover:bg-emerald-400 rounded-t-md transition-all cursor-pointer"
                                    style="height: {{ $heightPercent }}%;"></div>
                                <span class="text-[10px] sm:text-xs font-semibold text-slate-500 mt-2 truncate">{{ substr($day, 0, 3) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Hourly Graph -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-800">Average Stay Duration by Hour of Day</h3>
                    <div class="h-56 flex items-end gap-1 pt-8 pb-3 border-b border-slate-200 overflow-x-auto touch-scroll relative">
                        @foreach ($habitsHourly as $hour => $minutes)
                            @php
                                $heightPercent = ($minutes / max($maxHabitsHour, 1)) * 100;
                            @endphp
                            <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[16px]"
                                title="{{ $hour }}:00 &mdash; {{ $minutes }} min avg">
                                <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                    {{ $minutes }} min
                                </div>
                                <div class="w-full bg-amber-500 group-hover:bg-amber-400 rounded-t-md transition-all cursor-pointer"
                                    style="height: {{ $heightPercent }}%;"></div>
                                <span class="text-[8px] sm:text-[9px] font-semibold text-slate-400 mt-2 rotate-45 origin-left">{{ $hour }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>"""

content = content.replace(marker, new_graphs)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
