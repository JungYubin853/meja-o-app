import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

start_str = "<!-- 1. Top Bar (One long container) -->"
end_str = "<!-- TABLE VIEW -->"

start_idx = content.find(start_str)
end_idx = content.find(end_str)

if start_idx != -1 and end_idx != -1:
    old_block = content[start_idx:end_idx]
    
    new_block = """<!-- 1. Top Bar (One long container) -->
                  <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-center justify-between gap-3">
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
                      </div>
                  </div>
  
                  <div class="flex flex-col lg:flex-row gap-4">
                      <!-- Sidebar: Date Picker, Calendar, Classification -->
                      <div class="w-full lg:w-[280px] shrink-0 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs p-4 flex flex-col gap-4">
                          
                          <!-- Native Date Picker (For quick jumping) -->
                          <form method="GET" action="/database" class="m-0 p-0 w-full">
                              <input type="hidden" name="view" value="habits">
                              @if (isset($selectedOutletId)) <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}"> @endif
                              <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="h-10 w-full text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 box-border m-0 shadow-soft-xs cursor-pointer">
                          </form>
  
                          <hr class="border-slate-100 border-t-2">
  
                          <!-- Mini Visual Calendar Widget (FullCalendar Style) -->
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
                              <div class="flex justify-between items-center py-1 px-1 mb-2">
                                  <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->subMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                  </a>
                                  <div class="text-[1rem] font-[800] text-[#334155] tracking-wide">{{ $cDate->format('F') }}</div>
                                  <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->addMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                  </a>
                              </div>
                              <table class="w-full border-collapse table-fixed">
                                  <thead>
                                      <tr>
                                          @foreach(['S','M','T','W','T','F','S'] as $dayName)
                                              <th class="border border-[#e2e8f0] bg-[#f8fafc] text-[#475569] text-[0.7rem] font-[700] py-1 text-center uppercase">{{ $dayName }}</th>
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
                                                      <td class="p-0 border border-[#e2e8f0] h-[32px] relative group text-center align-middle">
                                                          <a href="?view=habits&date={{ $tDate }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}"
                                                              class="flex items-center justify-center w-full h-full text-[12px] font-[600] text-[#334155] {{ $bgClassCal }} {{ $isSel ? 'ring-inset ring-2 ring-indigo-600 font-[800]' : '' }}">
                                                              {{ $day }}
                                                          </a>
                                                      </td>
                                                  @else
                                                      <td class="border border-[#e2e8f0] bg-[#f1f5f9] h-[32px]"></td>
                                                  @endif
                                              @endforeach
                                          </tr>
                                      @endforeach
                                  </tbody>
                              </table>
                          </div>
  
                          @php
                              $dateObj = \Carbon\Carbon::parse($dateFilter);
                              $dayName = $dateObj->format('l');
                              
                              // Holidays lookup logic
                              $year = $dateObj->year;
                              $holidaysData = \Illuminate\Support\Facades\Cache::get("holidays_{$year}");
                              $holidayName = null;
                              $holidayType = null;
                              if ($holidaysData && isset($holidaysData['data']) && is_array($holidaysData['data'])) {
                                  foreach($holidaysData['data'] as $h) {
                                      if(isset($h['date']) && $h['date'] === $dateFilter) {
                                          $holidayName = $h['name'];
                                          $isCollective = (isset($h['type']) && $h['type'] === 'leave');
                                          $holidayType = $isCollective ? 'Collective Leave' : 'National Holiday';
                                          break;
                                      }
                                  }
                              }
  
                              $isWeekend = in_array($dayName, ['Saturday', 'Sunday']);
                              if ($holidayType) {
                                  $classification = $holidayType;
                                  $displayName = $holidayName;
                                  $classColor = $holidayType === 'Collective Leave' ? 'yellow' : 'red';
                              } else {
                                  $classification = $isWeekend ? 'Weekend' : 'Weekday';
                                  $displayName = $dayName;
                                  $classColor = 'slate';
                              }
                          @endphp
                          <div class="flex flex-col items-center justify-center p-3 bg-slate-50 rounded-xl border border-slate-200 mt-2">
                              <div class="text-[10px] font-black text-{{ $classColor }}-500 uppercase tracking-widest leading-none mb-1.5 text-center">{{ $classification }}</div>
                              <div class="text-sm font-extrabold text-slate-800 text-center leading-none">{{ $displayName }}</div>
                          </div>
                      </div>
                      
                      <!-- 2. Main Content Box (Tabbed) -->
                      <div class="flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">
                          <!-- Main Content Header (Tabs & Pagination) -->
                          <div class="flex flex-col lg:flex-row items-center justify-between border-b border-slate-100 bg-slate-50/50 p-2 lg:p-4 gap-4">
                              
                              <!-- Tabs -->
                              <div class="flex items-center gap-1 bg-slate-100/80 p-1 rounded-xl shrink-0 w-full lg:w-auto overflow-x-auto">
                                  <button @click="activeTab = 'table'" 
                                          :class="activeTab === 'table' ? 'bg-white text-slate-800 shadow-sm ring-1 ring-slate-200/50 font-bold' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-semibold'"
                                          class="px-4 py-2 text-xs rounded-lg transition-all duration-200 flex items-center gap-2 flex-1 lg:flex-none justify-center whitespace-nowrap">
                                      <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                      Visit Logs
                                  </button>
                                  <button @click="activeTab = 'graph'" 
                                          :class="activeTab === 'graph' ? 'bg-white text-slate-800 shadow-sm ring-1 ring-slate-200/50 font-bold' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-semibold'"
                                          class="px-4 py-2 text-xs rounded-lg transition-all duration-200 flex items-center gap-2 flex-1 lg:flex-none justify-center whitespace-nowrap">
                                      <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                      Gantt Graph
                                  </button>
                              </div>
  
                              <!-- Pagination Controls -->
                              <div class="flex items-center justify-between lg:justify-end w-full lg:w-auto gap-4">
                                  <div class="flex items-center gap-2">
                                      <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">Rows per page:</span>
                                      <select x-model.number="perPage" @change="currentPage = 1" class="h-8 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg px-2 py-0 focus:ring-0 focus:border-slate-300">
                                          <option value="10">10</option>
                                          <option value="25">25</option>
                                          <option value="50">50</option>
                                          <option value="100">100</option>
                                      </select>
                                  </div>
                                  <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-0.5">
                                      <button @click="prevPage()" :disabled="currentPage === 1" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-white disabled:opacity-50 disabled:hover:bg-transparent transition-all">
                                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                      </button>
                                      <span class="text-xs font-bold text-slate-700 min-w-[3rem] text-center"><span x-text="currentPage"></span> / <span x-text="totalPages"></span></span>
                                      <button @click="nextPage()" :disabled="currentPage === totalPages" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-white disabled:opacity-50 disabled:hover:bg-transparent transition-all">
                                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                      </button>
                                  </div>
                              </div>
                          </div>
  """
    
    content = content.replace(old_block, new_block)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Replaced with new structured sidebar UI.")
else:
    print("Failed to find boundaries.")
