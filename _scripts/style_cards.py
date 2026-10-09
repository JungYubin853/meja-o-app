with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

# Replace the inner outlet card
old_card = """<div class="flex flex-col gap-1.5 group">
                            <div class="flex justify-between items-end text-xs">
                                <span class="font-bold text-slate-600 truncate" title="{{ $data['outlet'] }}">{{ $data['outlet'] }}</span>
                                <span class="font-extrabold text-slate-900">{{ $data['avg_minutes'] }} <span class="text-[9px] text-slate-400">min</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                                <div class="bg-indigo-500 group-hover:bg-indigo-400 h-full rounded-full transition-all duration-500" 
                                    style="width: {{ $maxHabitsTime > 0 ? ($data['avg_minutes'] / $maxHabitsTime * 100) : 0 }}%;"></div>
                            </div>
                        </div>"""

new_card = """<div class="flex flex-col gap-1.5 group p-4 border border-slate-100 rounded-xl hover:border-indigo-100 hover:shadow-soft-xs transition bg-slate-50/50 hover:bg-white">
                            <div class="flex justify-between items-end text-xs mb-1">
                                <span class="font-bold text-slate-700 truncate" title="{{ $data['outlet'] }}">{{ $data['outlet'] }}</span>
                                <span class="font-extrabold text-slate-900">{{ $data['avg_minutes'] }} <span class="text-[9px] text-slate-400 font-semibold">min avg</span></span>
                            </div>
                            <div class="w-full bg-slate-200/60 rounded-full h-2 overflow-hidden shadow-inner mb-3">
                                <div class="bg-indigo-500 group-hover:bg-indigo-400 h-full rounded-full transition-all duration-500" 
                                    style="width: {{ $maxHabitsTime > 0 ? ($data['avg_minutes'] / $maxHabitsTime * 100) : 0 }}%;"></div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Mini Weekly Chart -->
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-slate-500 mb-2">Day of Week</span>
                                    <div class="flex items-end justify-between h-8 gap-0.5" title="Weekly Breakdown">
                                        @foreach($data['weekly'] as $day => $min)
                                            <div class="flex-1 bg-emerald-200 hover:bg-emerald-500 rounded-t-sm transition-all relative group/bar" 
                                                style="height: {{ max(10, $maxHabitsDay > 0 ? ($min / $maxHabitsDay * 100) : 0) }}%">
                                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] font-bold py-0.5 px-1.5 rounded opacity-0 group-hover/bar:opacity-100 pointer-events-none z-10 hidden md:block shadow-md">{{ $min }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-between text-[7px] text-slate-400 font-extrabold px-1 mt-1 uppercase">
                                        <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
                                    </div>
                                </div>

                                <!-- Mini Hourly Chart -->
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-slate-500 mb-2">Hour of Day</span>
                                    <div class="flex items-end justify-between h-8 gap-[1px]" title="Hourly Breakdown">
                                        @foreach($data['hourly'] as $hour => $min)
                                            <div class="flex-1 bg-amber-200 hover:bg-amber-500 rounded-t-sm transition-all relative group/bar" 
                                                style="height: {{ max(10, $maxHabitsHour > 0 ? ($min / $maxHabitsHour * 100) : 0) }}%">
                                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] font-bold py-0.5 px-1.5 rounded opacity-0 group-hover/bar:opacity-100 pointer-events-none z-10 hidden md:block shadow-md">{{ $min }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-between text-[7px] text-slate-300 font-extrabold px-0.5 mt-1">
                                        <span>0</span><span>6</span><span>12</span><span>18</span><span>23</span>
                                    </div>
                                </div>
                            </div>
                        </div>"""

content = content.replace(old_card, new_card)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done styling cards")
