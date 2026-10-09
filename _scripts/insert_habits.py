with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

new_view = """<!-- CUSTOMER HABITS VIEW -->
        @if ($viewMode === 'habits')
            
            @if ($holidayName)
                <div class="mb-4 bg-{{ $holidayType == 'Collective Leave' ? 'amber' : 'rose' }}-50 border border-{{ $holidayType == 'Collective Leave' ? 'amber' : 'rose' }}-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="p-2 bg-white rounded-lg shadow-sm">
                        <svg class="w-5 h-5 text-{{ $holidayType == 'Collective Leave' ? 'amber' : 'rose' }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-{{ $holidayType == 'Collective Leave' ? 'amber' : 'rose' }}-900">{{ $holidayType }}</h4>
                        <p class="text-sm font-medium text-{{ $holidayType == 'Collective Leave' ? 'amber' : 'rose' }}-700">{{ $holidayName }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-4">
                
                <!-- Complete Daily Log Timeline -->
                <div class="xl:col-span-3 bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Customer Visit Log ({{ \Carbon\Carbon::parse($dateFilter)->format('d M Y') }})</h3>
                    @if(count($habitsDateLog) > 0)
                        <div class="flex flex-col gap-3 max-h-64 overflow-y-auto pr-2 touch-scroll">
                            @foreach($habitsDateLog as $log)
                                <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <div class="w-16 shrink-0 text-center">
                                        <div class="text-xs font-bold text-slate-900">{{ \Carbon\Carbon::parse($log->started_at)->format('H:i') }}</div>
                                        <div class="text-[10px] font-semibold text-slate-400">{{ \Carbon\Carbon::parse($log->ended_at)->format('H:i') }}</div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-bold text-slate-700">{{ $log->customer_name ?? 'Walk-in Guest' }} ({{ $log->pax }} pax)</span>
                                            <span class="text-xs font-extrabold text-indigo-600">{{ $log->time_elapsed }}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-1.5">
                                            @php 
                                                $startMin = (\Carbon\Carbon::parse($log->started_at)->hour * 60) + \Carbon\Carbon::parse($log->started_at)->minute;
                                                $endMin = (\Carbon\Carbon::parse($log->ended_at)->hour * 60) + \Carbon\Carbon::parse($log->ended_at)->minute;
                                                $leftPct = ($startMin / 1440) * 100;
                                                $widthPct = (($endMin - $startMin) / 1440) * 100;
                                            @endphp
                                            <div class="bg-indigo-500 h-1.5 rounded-full" style="margin-left: {{ $leftPct }}%; width: {{ $widthPct }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 font-semibold text-center py-6">No completed visits on this date.</p>
                    @endif
                </div>

                <!-- Hourly Distribution -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs">
                    <h3 class="text-sm font-bold text-slate-800 mb-6">Hourly Average Stay (Selected Date)</h3>
                    <div class="h-40 flex items-end justify-between gap-0.5 relative">
                        @php $maxHourly = max(array_merge(array_values($habitsHourly), [1])); @endphp
                        @foreach($habitsHourly as $hour => $min)
                            <div class="flex-1 bg-amber-100 hover:bg-amber-500 rounded-t transition-all relative group" style="height: {{ $maxHourly > 0 ? ($min / $maxHourly) * 100 : 0 }}%">
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] font-bold py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 z-10">{{ $min }}m</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-[9px] text-slate-400 font-bold mt-2">
                        <span>00:00</span><span>06:00</span><span>12:00</span><span>18:00</span><span>23:00</span>
                    </div>
                </div>

                <!-- Weekly Distribution -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs">
                    <h3 class="text-sm font-bold text-slate-800 mb-6">Weekly Average Stay (Selected Year)</h3>
                    <div class="h-40 flex items-end justify-between gap-1.5 relative">
                        @php $maxWeekly = max(array_merge(array_values($habitsWeekly), [1])); @endphp
                        @foreach($habitsWeekly as $day => $min)
                            <div class="flex-1 bg-emerald-100 hover:bg-emerald-500 rounded-t transition-all relative group" style="height: {{ $maxWeekly > 0 ? ($min / $maxWeekly) * 100 : 0 }}%">
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] font-bold py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 z-10">{{ $min }}m</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-[9px] text-slate-400 font-bold mt-2 uppercase">
                        <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                    </div>
                </div>

                <!-- Yearly Distribution -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs">
                    <h3 class="text-sm font-bold text-slate-800 mb-6">Yearly Average Stay (Selected Year)</h3>
                    <div class="h-40 flex items-end justify-between gap-1 relative">
                        @php $maxYearly = max(array_merge(array_values($habitsYearly), [1])); @endphp
                        @foreach($habitsYearly as $month => $min)
                            <div class="flex-1 bg-sky-100 hover:bg-sky-500 rounded-t transition-all relative group" style="height: {{ $maxYearly > 0 ? ($min / $maxYearly) * 100 : 0 }}%">
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] font-bold py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 z-10">{{ $min }}m</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-[9px] text-slate-400 font-bold mt-2 uppercase">
                        <span>Jan</span><span>Apr</span><span>Jul</span><span>Oct</span><span>Dec</span>
                    </div>
                </div>
            </div>
        @endif
"""

target = "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist')"
# I'll replace the first occurrence of the target with the new_view + target
# (assuming there's only 1 such block for the log entries)
content = content.replace(target, new_view + "\n\n        " + target, 1)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done inserting new habits view")
