with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

start_marker = "<!-- CUSTOMER HABITS VIEW -->"
end_marker = "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

new_habits_view = """<!-- CUSTOMER HABITS VIEW -->
        @if ($viewMode === 'habits')
            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800">Average Dine-In Duration</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Comparing how long customers stay across all outlets.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-5">
                    @foreach ($habitsData as $data)
                        <div class="flex flex-col gap-1.5 group">
                            <div class="flex justify-between items-end text-xs">
                                <span class="font-bold text-slate-600 truncate" title="{{ $data['outlet'] }}">{{ $data['outlet'] }}</span>
                                <span class="font-extrabold text-slate-900">{{ $data['avg_minutes'] }} <span class="text-[9px] text-slate-400">min</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                                <div class="bg-indigo-500 group-hover:bg-indigo-400 h-full rounded-full transition-all duration-500" 
                                    style="width: {{ $maxHabitsTime > 0 ? ($data['avg_minutes'] / $maxHabitsTime * 100) : 0 }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        """

content = content[:start_idx] + new_habits_view + content[end_idx:]

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
