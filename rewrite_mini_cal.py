file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will match the entire Mini Visual Calendar Widget HTML block.
start_str = "<!-- Mini Visual Calendar Widget (FullCalendar Style) -->"
end_str = "<!-- 2. Main Content Box (Tabbed) -->"

start_idx = content.find(start_str)
end_idx = content.find(end_str)

if start_idx != -1 and end_idx != -1:
    old_block = content[start_idx:end_idx]
    
    # We will build the new HTML block
    new_block = """<!-- Mini Visual Calendar Widget (FullCalendar Style) -->
                            @php
                                $calReq = request('calendar_month');
                                $cDate = $calReq ? \Carbon\Carbon::parse($calReq . '-01') : \Carbon\Carbon::parse($dateFilter)->startOfMonth();
                                $sMonth = $cDate->copy()->startOfMonth();
                                $eMonth = $cDate->copy()->endOfMonth();
                                $sDow = $sMonth->dayOfWeek; 
                                $dim = $eMonth->day;
                                
                                // Fetch Holidays for Calendar coloring
                                $year = $cDate->year;
                                $holidaysData = \Illuminate\Support\Facades\Cache::get("holidays_{$year}");
                                $holidayMap = [];
                                if ($holidaysData && isset($holidaysData['data']) && is_array($holidaysData['data'])) {
                                    foreach($holidaysData['data'] as $h) {
                                        $hDt = $h['date'] ?? null;
                                        if($hDt) {
                                            $isCollective = (isset($h['type']) && $h['type'] === 'leave');
                                            $holidayMap[$hDt] = $isCollective ? 'collective' : 'holiday';
                                        }
                                    }
                                }
                            @endphp
                            <div class="w-full bg-white">
                                <div class="flex justify-between items-center py-2 px-1">
                                    <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->subMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </a>
                                    <div class="text-[1.1rem] font-[800] text-[#334155] tracking-wide">{{ $cDate->format('F') }}</div>
                                    <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->addMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                                <table class="w-full border-collapse table-fixed">
                                    <thead>
                                        <tr>
                                            @foreach(['S','M','T','W','T','F','S'] as $dayName)
                                                <th class="border border-[#e2e8f0] bg-[#f8fafc] text-[#475569] text-[0.75rem] font-[700] py-1 text-center uppercase">{{ $dayName }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cells = [];
                                            for($i = 0; $i < $sDow; $i++) { $cells[] = null; }
                                            for($day = 1; $day <= $dim; $day++) { $cells[] = $day; }
                                            while(count($cells) < 42) { $cells[] = null; }
                                        @endphp
                                        @foreach(array_chunk($cells, 7) as $row)
                                            <tr>
                                                @foreach($row as $day)
                                                    @if($day)
                                                        @php
                                                            $tDate = $cDate->copy()->day($day)->format('Y-m-d');
                                                            $isSel = $tDate === $dateFilter;
                                                            $isToday = $tDate === now()->format('Y-m-d');
                                                            $hType = $holidayMap[$tDate] ?? null;
                                                            
                                                            $bgClassCal = 'bg-white hover:bg-slate-50';
                                                            if ($hType === 'holiday') {
                                                                $bgClassCal = 'bg-[#fee2e2] hover:bg-[#fecaca]';
                                                            } elseif ($hType === 'collective') {
                                                                $bgClassCal = 'bg-[#fef08a] hover:bg-[#fde047]';
                                                            } elseif ($isToday) {
                                                                $bgClassCal = 'bg-[#fefce8] hover:bg-[#fef08a]';
                                                            }
                                                        @endphp
                                                        <td class="p-0 border border-[#e2e8f0] h-8 relative group text-center align-middle">
                                                            <a href="?view=habits&date={{ $tDate }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}"
                                                               class="flex items-center justify-center w-full h-full text-[13px] font-[600] text-[#1d4ed8] {{ $bgClassCal }} {{ $isSel ? 'ring-inset ring-2 ring-indigo-600 font-[800]' : '' }}">
                                                                {{ $day }}
                                                            </a>
                                                        </td>
                                                    @else
                                                        <td class="border border-[#e2e8f0] bg-[#f1f5f9] h-8"></td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        """
    content = content.replace(old_block, new_block)
    
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Replaced Mini Calendar Widget with FullCalendar style clone!")
else:
    print("Could not find boundaries.")
