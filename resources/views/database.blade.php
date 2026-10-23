<!-- Immediate Session Redirect: If user accessed /database with no query parameter, redirect to their last active URL -->
<script>
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('view')) {
            const savedUrl = localStorage.getItem('meja_last_database_url');
            if (savedUrl && savedUrl !== window.location.href && savedUrl.includes('/database?view=')) {
                window.location.replace(savedUrl);
                return;
            }
        }
        localStorage.setItem('meja_last_database_url', window.location.href);
    })();
</script>

<x-app-layout title="Meja-O | Visitor Analytics & Database">

    <main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4" x-data="{ exportMenuOpen: false }">

        <!-- Navigation Tabs: Overall, Daily Report, Monthly Report, Yearly Report, and Waitlist -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 touch-scroll scrollbar-none">
            @if (Auth::user()->hasPermission('rep_overall'))
                <a href="/database?view=overall&date={{ $dateFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'overall' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">{{ __('Overall') }}</a>
            @endif

            @if (Auth::user()->hasPermission('rep_daily'))
                <a href="/database?view=hourly&date={{ $dateFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'hourly' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">{{ __('Daily Report') }}</a>
            @endif

            @if (Auth::user()->hasPermission('rep_weekly'))
                <a href="/database?view=weekly&date={{ $dateFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'weekly' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">{{ __('Weekly Report') }}</a>
            @endif
            
            @if (Auth::user()->hasPermission('rep_monthly'))
                <a href="/database?view=daily&month={{ $monthFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'daily' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">{{ __('Monthly Report') }}</a>
            @endif
            
            @if (Auth::user()->hasPermission('rep_yearly'))
                <a href="/database?view=monthly&year={{ $yearFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'monthly' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">{{ __('Yearly Report') }}</a>
            @endif

            
            @if (Auth::user()->hasPermission('rep_waitlist'))
                <a href="/database?view=waitlist"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'waitlist' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">{{ __('Waitlist') }}</a>
            @endif

            @if (Auth::user()->hasPermission('rep_habits'))
            <a href="/database?view=habits&date={{ $dateFilter }}"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'habits' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">{{ __('Customer Habits') }}</a>
            @endif

        </div>

        @if ($viewMode !== 'habits')
<!-- Filter & Actions Bar: Perfectly Aligned 1-Row Layout -->
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-center justify-between gap-3">
            

            <!-- Controls: Filter & Export (Desktop: 1 row side-by-side; Mobile: Row 1 = Date + Filter, Row 2 = Export) -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">

                @if (auth()->check() && auth()->user()->isSuperAdmin() && $viewMode !== 'overall')
                    <form method="GET" action="/database" class="flex items-center m-0 p-0 w-full sm:w-auto">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if ($viewMode === 'hourly' || $viewMode === 'habits' || $viewMode === 'weekly') <input type="hidden" name="date" value="{{ $dateFilter }}"> @endif
                        @if ($viewMode === 'daily') <input type="hidden" name="month" value="{{ $monthFilter }}"> @endif
                        @if ($viewMode === 'monthly') <input type="hidden" name="year" value="{{ $yearFilter }}"> @endif
                        
                        <select name="outlet_id" onchange="this.form.submit()" class="h-10 text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-soft-xs cursor-pointer w-full sm:w-auto">
                            @foreach ($outlets ?? [] as $outlet)
                                <option value="{{ $outlet->id }}" {{ ($selectedOutletId ?? '') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if ($viewMode !== 'waitlist')
                    <form method="GET" action="/database" class="flex items-center gap-2 m-0 p-0 w-full sm:w-auto">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if (isset($selectedOutletId)) <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}"> @endif

                        @if ($viewMode === 'monthly')
                            @php
                                $startYear = 2026;
                                $currentYear = max(2026, (int) date('Y'));
                            @endphp
                            <select name="year" onchange="this.form.submit()" class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                                @for ($y = $startYear; $y <= $currentYear; $y++)
                                    <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                            
                        @elseif ($viewMode === 'daily')
                            <input type="month" name="month" value="{{ $monthFilter }}" onchange="this.form.submit()" class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                            
                        @elseif ($viewMode === 'weekly')
                            <div class="flex items-center gap-2 flex-1 sm:w-auto">
                                <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                                @if (isset($startOfWeek) && isset($endOfWeek))
                                    <span class="text-xs font-semibold text-slate-500 whitespace-nowrap hidden md:inline">
                                        ({{ $startOfWeek->format('d M') }} &ndash; {{ $endOfWeek->format('d M Y') }})
                                    </span>
                                @endif
                            </div>

                        @elseif ($viewMode === 'hourly' || $viewMode === 'habits' || $viewMode === 'overall')
                            <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                            
                        @endif
                    </form>
                @endif

                @if ($viewMode !== 'waitlist')
                    <div class="relative flex items-center w-full sm:w-auto shrink-0" @click.away="exportMenuOpen = false">
                        <button type="button" @click="exportMenuOpen = !exportMenuOpen"
                            class="h-10 w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold px-3.5 rounded-xl transition flex items-center justify-center gap-1.5 shadow-soft-xs box-border">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Export</span>
                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="exportMenuOpen" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 top-full mt-2 w-full sm:w-44 bg-white rounded-2xl shadow-soft-xl border border-slate-100 py-1 z-30"
                            x-cloak>

                            <a href="/database/export?type=pdf&view={{ $viewMode }}&date={{ $dateFilter }}&month={{ $monthFilter }}&year={{ $yearFilter }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}"
                                target="_blank"
                                class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-rose-600 transition">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>Export PDF</span>
                            </a>

                            <a href="/database/export?type=excel&view={{ $viewMode }}&date={{ $dateFilter }}&month={{ $monthFilter }}&year={{ $yearFilter }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}"
                                class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span>{{ __('Export Excel (CSV)') }}</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

          
@endif
<!-- CUSTOMER HABITS VIEW -->
        @if ($viewMode === 'habits')
            <div x-data="{ 
                activeTab: localStorage.getItem('meja_habits_tab') || 'table',
                perPage: parseInt(localStorage.getItem('meja_db_per_page_habits')) || 5,
                currentPage: (function() {
                    const p = parseInt(localStorage.getItem('meja_db_page_habits'));
                    return (p && p > 0) ? p : 1;
                })(),
                totalRecords: {{ count($habitsDateLog) }},
                get totalPages() { return Math.max(1, Math.ceil(this.totalRecords / this.perPage)); },
                init() {
                    if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                    this.$watch('currentPage', val => localStorage.setItem('meja_db_page_habits', val));
                    this.$watch('perPage', val => {
                        localStorage.setItem('meja_db_per_page_habits', val);
                        if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                    });
                    this.$watch('activeTab', val => localStorage.setItem('meja_habits_tab', val));
                },
                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },
                goToPage(p) { if (p >= 1 && p <= this.totalPages) this.currentPage = p; },
                get pageRange() {
                    const total = this.totalPages;
                    const current = this.currentPage;
                    const delta = 2;
                    if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
                    const range = [];
                    const left = Math.max(2, current - delta);
                    const right = Math.min(total - 1, current + delta);
                    range.push(1);
                    if (left > 2) range.push('...');
                    for (let i = left; i <= right; i++) range.push(i);
                    if (right < total - 1) range.push('...');
                    range.push(total);
                    return range;
                }
            }" class="flex flex-col gap-4">

                <!-- 1. Top Bar (One long container) -->
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
  
                  <div class="flex flex-col lg:flex-row gap-4 lg:items-start">
                      <!-- Sidebar: Date Picker, Calendar, Classification -->
                      <div class="w-full lg:w-[280px] shrink-0 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs p-4 flex flex-col gap-4">
                          
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
                          <div class="flex flex-col items-start justify-center px-1 py-1">
                              <div class="text-[10px] font-black text-{{ $classColor }}-500 uppercase tracking-widest leading-none mb-1.5 text-left">{{ $classification }}</div>
                              <div class="text-sm font-extrabold text-slate-800 text-left leading-none">{{ $displayName }}</div>
                          </div>

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
                          <div class="w-[240px] mx-auto bg-white">
                              <div class="flex justify-between items-center py-1 mb-2">
                                    <div class="flex items-center">
                                        <select onchange="window.location='?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->format('Y') }}-' + this.value" class="text-[0.95rem] font-[800] text-[#334155] tracking-wide bg-transparent border-none px-1 py-1 focus:ring-0 cursor-pointer hover:bg-slate-100 rounded-md transition-colors appearance-none" style="background-image: none;">
                                            @foreach(['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'] as $num => $name)
                                                <option value="{{ $num }}" {{ $cDate->format('m') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <select onchange="window.location='?view=habits&date={{ $dateFilter }}&calendar_month=' + this.value + '-{{ $cDate->format('m') }}'" class="text-[0.95rem] font-[800] text-[#334155] tracking-wide bg-transparent border-none px-1 py-1 focus:ring-0 cursor-pointer hover:bg-slate-100 rounded-md transition-colors appearance-none" style="background-image: none;">
                                            @for($y = 2023; $y <= 2030; $y++)
                                                <option value="{{ $y }}" {{ $cDate->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="flex items-center shrink-0">
                                        <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->subMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                        </a>
                                        <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->addMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </div>
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
                                                              class="flex items-center justify-center w-full h-full text-[12px] font-[600] text-[#334155] {{ $bgClassCal }} {{ $isSel ? 'ring-inset ring-2 ring-slate-700 font-[800]' : '' }}">
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
  
                          
                      </div>
                      
                      <!-- 2. Main Content Box (Tabbed) -->
                      <div class="flex-1 min-w-0 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">
                          <!-- Main Content Header (Tabs & Pagination) -->
                          <div class="flex flex-col lg:flex-row items-center justify-between border-b border-slate-100 bg-slate-50/50 p-2 lg:p-4 gap-4">
                              
                              <!-- Tabs -->
                              <div class="flex items-center gap-1 bg-slate-100/80 p-1 rounded-xl shrink-0 w-full lg:w-auto overflow-x-auto">
                                  <button @click="activeTab = 'table'" 
                                          :class="activeTab === 'table' ? 'bg-white text-slate-800 shadow-sm ring-1 ring-slate-200/50 font-bold' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-semibold'"
                                          class="px-4 py-2 text-xs rounded-lg transition-all duration-200 flex items-center gap-2 flex-1 lg:flex-none justify-center whitespace-nowrap">
                                      <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                      {{ __('Visit Logs') }}
                                  </button>
                                  <button @click="activeTab = 'gantt'" 
                                          :class="activeTab === 'gantt' ? 'bg-white text-slate-800 shadow-sm ring-1 ring-slate-200/50 font-bold' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-semibold'"
                                          class="px-4 py-2 text-xs rounded-lg transition-all duration-200 flex items-center gap-2 flex-1 lg:flex-none justify-center whitespace-nowrap">
                                      <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                      {{ __('Gantt Graph') }}
                                  </button>
                              </div>
  
                              <!-- Pagination Controls -->
                              <div class="flex items-center justify-between lg:justify-end w-full lg:w-auto gap-3 flex-wrap">
                                  <div class="flex items-center gap-1.5">
                                      <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Rows per page:') }}</span>
                                      <select x-model.number="perPage" @change="currentPage = 1" class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                                          <option :value="5">5</option>
                                          <option :value="10">10</option>
                                          <option :value="25">25</option>
                                          <option :value="50">50</option>
                                          <option :value="100">100</option>
                                      </select>
                                  </div>
                                  <template x-if="totalPages > 1">
                                      <div class="flex items-center gap-1 overflow-x-auto py-1">
                                          <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                              :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                              class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                          </button>

                                          <template x-for="(page, idx) in pageRange" :key="idx">
                                              <span>
                                                  <template x-if="page === '...'">
                                                      <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                                  </template>
                                                  <template x-if="page !== '...'">
                                                      <button type="button" @click="goToPage(page)"
                                                          :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                                          class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                                          x-text="page">
                                                      </button>
                                                  </template>
                                              </span>
                                          </template>

                                          <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                              :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                              class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                          </button>
                                      </div>
                                  </template>
                              </div>
                          </div>
  <!-- TABLE VIEW -->
                    <div x-show="activeTab === 'table'" class="overflow-x-auto overflow-y-auto">
                        <table class="w-full text-left border-collapse min-h-[300px]">
                            <thead>
                                <tr class="border-b border-slate-200 bg-white text-[10px] font-extrabold text-slate-400 uppercase tracking-wider sticky top-0 shadow-sm z-10">
                                    <th class="px-4 py-3 bg-white">{{ __('Log ID') }} (Customer)</th>
                                    <th class="px-4 py-3 bg-white">{{ __('Start Session') }}</th>
                                    <th class="px-4 py-3 bg-white">{{ __('Time Elapsed') }}</th>
                                    <th class="px-4 py-3 bg-white">{{ __('Table Number') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-[11px] font-semibold text-slate-700">
                                @forelse($habitsDateLog as $index => $log)
                                    <tr class="hover:bg-slate-50 transition" x-show="currentPage === Math.ceil(({{ $index }} + 1) / perPage)">
                                        <td class="px-4 py-3">
                                            <span class="text-slate-400 font-bold mr-1">#{{ $index + 1 }}</span> 
                                            {{ ($log->customer_name && !in_array($log->customer_name, ['Walk-in Guest', 'Walk-In Guest', 'Walk-in', 'Walk-In'])) ? $log->customer_name : __('Guest') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($log->started_at)->format('H:i') }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $log->time_elapsed ?? \Carbon\Carbon::parse($log->started_at)->diffInMinutes(\Carbon\Carbon::parse($log->ended_at)) . ' min' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if(!empty($log->table_number))
                                                <span class="inline-flex items-center px-1.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 rounded text-[10px] font-bold">
                                                    {{ $log->table_number }}
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

                    <!-- Habits Table Footer Bar (Universal Bottom Bar) -->
                    @if (count($habitsDateLog) > 0)
                        <div x-show="activeTab === 'table'" class="p-3 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                            <span>
                                {{ __('Showing') }} <strong class="text-slate-800"
                                    x-text="totalRecords > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></strong>
                                {{ __('to') }} <strong class="text-slate-800"
                                    x-text="Math.min(currentPage * perPage, totalRecords)"></strong>
                                {{ __('of') }} <strong class="text-slate-800" x-text="totalRecords"></strong> {{ __('records') }}
                            </span>

                            <template x-if="totalPages > 1">
                                <div class="flex items-center gap-1 overflow-x-auto py-1">
                                    <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                        :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>

                                    <template x-for="(page, idx) in pageRange" :key="idx">
                                        <span>
                                            <template x-if="page === '...'">
                                                <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                            </template>
                                            <template x-if="page !== '...'">
                                                <button type="button" @click="goToPage(page)"
                                                    :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                                    x-text="page">
                                                </button>
                                            </template>
                                        </span>
                                    </template>

                                    <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                        :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    @endif

                    <!-- GANTT VIEW -->
                    <div x-show="activeTab === 'gantt'" class="p-3 sm:p-4 bg-slate-50/20" style="display: none;">
                        @if(count($habitsDateLog) > 0)
                            <div class="relative w-full max-h-[500px] sm:max-h-[600px] overflow-auto touch-scroll border border-slate-200/80 rounded-xl bg-white shadow-soft-xs">
                                <div class="min-w-[840px] relative">
                                    <!-- Sticky Top Header (Time Axis 00:00 to 24:00) -->
                                    <div class="sticky top-0 z-30 bg-white border-b border-slate-200 flex items-stretch h-9 select-none">
                                        <!-- Top-Left Corner: Sticky on both axes (top & left) with Diagonal Split (GUEST bottom-left, TIME top-right) -->
                                        <div class="sticky left-0 z-40 bg-slate-50 w-[72px] shrink-0 border-r border-slate-200 relative select-none overflow-hidden before:content-[''] before:absolute before:right-full before:top-0 before:bottom-0 before:w-8 before:bg-slate-50">
                                            <svg class="absolute inset-0 w-full h-full pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100">
                                                <line x1="0" y1="0" x2="100" y2="100" stroke="#cbd5e1" stroke-width="1.2" vector-effect="non-scaling-stroke" />
                                            </svg>
                                            <span class="absolute top-1 right-1.5 text-[8.5px] font-black text-slate-600 tracking-wider uppercase leading-none">TIME</span>
                                            <span class="absolute bottom-1 left-1.5 text-[8.5px] font-black text-slate-600 tracking-wider uppercase leading-none">GUEST</span>
                                        </div>
                                        <!-- Time Track (Sticky at top) -->
                                        <div class="flex-1 relative h-full flex items-end bg-slate-50/60">
                                            @for($h = 0; $h <= 24; $h += 2)
                                                <div class="absolute text-[10px] font-bold text-slate-500 {{ $h == 0 ? 'translate-x-1' : ($h == 24 ? '-translate-x-full -ml-1' : '-translate-x-1/2') }} bottom-1.5 tabular-nums whitespace-nowrap" style="left: {{ ($h / 24) * 100 }}%">
                                                    {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00
                                                </div>
                                                @if($h < 24)
                                                    <div class="absolute h-2 border-l border-slate-300 bottom-0" style="left: {{ ($h / 24) * 100 }}%"></div>
                                                @endif
                                            @endfor
                                            <div class="absolute h-2 border-l border-slate-300 bottom-0" style="left: 100%"></div>
                                        </div>
                                    </div>

                                    <!-- Gantt Rows -->
                                    <div class="relative divide-y divide-slate-100">
                                        <!-- Grid lines for background (starts after left sticky column w-[72px]) -->
                                        <div class="absolute inset-0 left-[72px] pointer-events-none flex">
                                            @for($h = 0; $h < 24; $h += 2)
                                                <div class="flex-1 border-l border-dashed border-slate-100"></div>
                                            @endfor
                                            <div class="border-l border-dashed border-slate-100 h-full"></div>
                                        </div>

                                        @foreach($habitsDateLog as $index => $log)
                                            @php 
                                                $start = \Carbon\Carbon::parse($log->started_at);
                                                $end = $log->ended_at ? \Carbon\Carbon::parse($log->ended_at) : now();
                                                $startMinOfDay = ($start->hour * 60) + $start->minute;
                                                $endMinOfDay = ($end->hour * 60) + $end->minute;
                                                if ($endMinOfDay < $startMinOfDay) $endMinOfDay = 1440;
                                                
                                                $diffInMinutes = max(1, $start->diffInMinutes($end));
                                                $hours = intdiv($diffInMinutes, 60);
                                                $minutes = $diffInMinutes % 60;
                                                $durationText = $log->time_elapsed ?: (($hours > 0 ? "{$hours}h " : "") . "{$minutes}m");
                                                $isOverOneHour = ($diffInMinutes >= 60);
                                                
                                                $leftPct = ($startMinOfDay / 1440) * 100;
                                                $widthPct = (($endMinOfDay - $startMinOfDay) / 1440) * 100;
                                                $isNearRightEdge = (($leftPct + $widthPct) > 92);
                                                $displayName = ($log->customer_name && !in_array($log->customer_name, ['Walk-in Guest', 'Walk-In Guest', 'Walk-in', 'Walk-In'])) ? $log->customer_name : __('Guest');
                                            @endphp
                                            <div class="flex items-center group relative hover:bg-slate-50/50 transition" x-show="currentPage === Math.ceil(({{ $index }} + 1) / perPage)">
                                                <!-- Left Sticky Column (Customer Name & Table Number: Left-Aligned & Crisp Black) -->
                                                <div class="sticky left-0 z-20 bg-white group-hover:bg-slate-50 transition w-[72px] shrink-0 px-2 py-1 text-left truncate border-r border-slate-200 shadow-[1px_0_0_0_#e2e8f0] before:content-[''] before:absolute before:right-full before:top-0 before:bottom-0 before:w-8 before:bg-white group-hover:before:bg-slate-50">
                                                    <div class="text-[11px] font-bold text-slate-900 truncate leading-tight" title="{{ $displayName }}">
                                                        {{ $displayName }}
                                                    </div>
                                                    <div class="text-[10px] font-bold text-slate-900 truncate leading-tight mt-0.5">
                                                        {{ $log->table_number ?: '-' }}
                                                    </div>
                                                </div>
                                                
                                                <!-- Gantt Bar Track (Scrollable) -->
                                                <div class="flex-1 h-8 sm:h-9 relative px-0 py-1">
                                                    <!-- Bar Wrapper positioned along the timeline -->
                                                    <div class="absolute top-1 bottom-1 sm:top-1.5 sm:bottom-1.5 flex items-center"
                                                         style="left: {{ $leftPct }}%; width: {{ max($widthPct, 0.6) }}%;">

                                                        <!-- Outside Label on LEFT (if short bar < 1h AND near 24:00 right edge) -->
                                                        @if(!$isOverOneHour && $isNearRightEdge)
                                                            <span class="absolute right-full mr-1.5 text-[10px] font-extrabold text-slate-800 tabular-nums whitespace-nowrap select-none pointer-events-none">
                                                                {{ $durationText }}
                                                            </span>
                                                        @endif

                                                        <!-- The Colored Bar -->
                                                        <div class="w-full h-full bg-slate-800 hover:bg-slate-900 rounded-md border border-slate-900 shadow-xs flex items-center justify-center overflow-hidden transition cursor-pointer relative"
                                                             title="Start: {{ $start->format('H:i') }} | End: {{ $end->format('H:i') }} | {{ __('Elapsed') }}: {{ $durationText }}">
                                                            <!-- Inside label if >= 1 hour -->
                                                            @if($isOverOneHour)
                                                                <span class="text-[10px] font-extrabold text-white truncate px-1.5 tabular-nums whitespace-nowrap">
                                                                    {{ $durationText }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Outside Label on RIGHT (if short bar < 1h AND NOT near right edge) -->
                                                        @if(!$isOverOneHour && !$isNearRightEdge)
                                                            <span class="absolute left-full ml-1.5 text-[10px] font-extrabold text-slate-800 tabular-nums whitespace-nowrap select-none pointer-events-none">
                                                                {{ $durationText }}
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
                                <p class="text-sm text-slate-400 font-semibold">No visits recorded {{ __('to') }} graph.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            </div>
        @endif
        
        <!-- OVERALL VIEW -->
        @if ($viewMode === 'overall' && Auth::user()->hasPermission('rep_overall'))
            <div class="space-y-4">
                @if(count($overallStats) > 0)
                      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden"
                          x-data="{
                              perPage: parseInt(localStorage.getItem('meja_db_per_page_overall')) || 5,
                              currentPage: (function() {
                                  const p = parseInt(localStorage.getItem('meja_db_page_overall'));
                                  return (p && p > 0) ? p : 1;
                              })(),
                              totalRecords: {{ count($overallStats) }},
                              get totalPages() {
                                  return Math.max(1, Math.ceil(this.totalRecords / this.perPage));
                              },
                              init() {
                                  if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                                  this.$watch('currentPage', val => localStorage.setItem('meja_db_page_overall', val));
                                  this.$watch('perPage', val => {
                                      localStorage.setItem('meja_db_per_page_overall', val);
                                      if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                                  });
                              },
                              nextPage() {
                                  if (this.currentPage < this.totalPages) this.currentPage++;
                              },
                              prevPage() {
                                  if (this.currentPage > 1) this.currentPage--;
                              },
                              goToPage(p) {
                                  if (p >= 1 && p <= this.totalPages) this.currentPage = p;
                              },
                              get pageRange() {
                                  const total = this.totalPages;
                                  const current = this.currentPage;
                                  const delta = 2;
                                  if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
                                  const range = [];
                                  const left = Math.max(2, current - delta);
                                  const right = Math.min(total - 1, current + delta);
                                  range.push(1);
                                  if (left > 2) range.push('...');
                                  for (let i = left; i <= right; i++) range.push(i);
                                  if (right < total - 1) range.push('...');
                                  range.push(total);
                                  return range;
                              }
                          }">

                          <!-- Overall Header Controls Bar (Universal Top Bar) -->
                          <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                              <div class="flex items-center gap-2.5">
                                  <h3 class="font-bold text-slate-800 text-sm">{{ __('Overall Outlets Visitor Statistics') }}</h3>
                                  <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200 tabular-nums">
                                      {{ count($overallStats) }} {{ __('Outlets') }}
                                  </span>
                              </div>

                              <!-- Right Controls: Rows Dropdown (10, 25, 50, 100) & Top Numbered Pagination -->
                              <div class="flex items-center gap-3 self-end sm:self-auto flex-wrap">
                                  <div class="flex items-center gap-1.5">
                                      <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Rows per page:') }}</span>
                                      <select x-model.number="perPage" @change="currentPage = 1"
                                          class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                                          <option :value="5">5</option>
                                          <option :value="10">10</option>
                                          <option :value="25">25</option>
                                          <option :value="50">50</option>
                                          <option :value="100">100</option>
                                      </select>
                                  </div>

                                  <template x-if="totalPages > 1">
                                      <div class="flex items-center gap-1 overflow-x-auto py-1">
                                          <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                              :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                              class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                          </button>

                                          <template x-for="(page, idx) in pageRange" :key="idx">
                                              <span>
                                                  <template x-if="page === '...'">
                                                      <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                                  </template>
                                                  <template x-if="page !== '...'">
                                                      <button type="button" @click="goToPage(page)"
                                                          :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                                          class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                                          x-text="page">
                                                      </button>
                                                  </template>
                                              </span>
                                          </template>

                                          <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                              :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                              class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                          </button>
                                      </div>
                                  </template>
                              </div>
                          </div>

                          <div class="overflow-x-auto touch-scroll">
                              <table class="w-full text-left border-collapse">
                                  <thead>
                                      <tr class="bg-slate-50 border-b border-slate-200/80">
                                          <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Outlet Name</th>
                                          <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="Recorded on {{ $dateFilter }}">Day Visitors ({{ \Carbon\Carbon::parse($dateFilter)->format('d M Y') }})</th>
                                          <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="{{ \Carbon\Carbon::parse($dateFilter)->format('F Y') }}">Month Visitors ({{ \Carbon\Carbon::parse($dateFilter)->format('M Y') }})</th>
                                          <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="Year {{ \Carbon\Carbon::parse($dateFilter)->format('Y') }}">Year Visitors ({{ \Carbon\Carbon::parse($dateFilter)->format('Y') }})</th>
                                      </tr>
                                  </thead>
                                  <tbody class="divide-y divide-slate-100">
                                      @foreach ($overallStats as $stat)
                                          <tr class="hover:bg-slate-50/50 transition"
                                              x-show="({{ $loop->iteration }} > (currentPage - 1) * perPage) && ({{ $loop->iteration }} <= currentPage * perPage)"
                                              x-cloak>
                                              <td class="px-3 py-2">
                                                  <span class="text-xs sm:text-sm font-bold text-slate-900">{{ $stat['outlet']->name }}</span>
                                              </td>
                                              <td class="px-3 py-2 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['daily'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">{{ __('Pax') }}</span>
                                              </td>
                                              <td class="px-3 py-2 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['monthly'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">{{ __('Pax') }}</span>
                                              </td>
                                              <td class="px-3 py-2 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['yearly'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">{{ __('Pax') }}</span>
                                              </td>
                                          </tr>
                                      @endforeach
                                  </tbody>
                              </table>
                          </div>

                          <!-- Overall Table Footer Bar (Universal Bottom Bar) -->
                          <div class="p-3 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                              <span>
                                  {{ __('Showing') }} <strong class="text-slate-800"
                                      x-text="totalRecords > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></strong>
                                  {{ __('to') }} <strong class="text-slate-800"
                                      x-text="Math.min(currentPage * perPage, totalRecords)"></strong>
                                  {{ __('of') }} <strong class="text-slate-800" x-text="totalRecords"></strong> {{ __('outlets') }}
                              </span>

                              <template x-if="totalPages > 1">
                                  <div class="flex items-center gap-1 overflow-x-auto py-1">
                                      <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                          :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                          class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                      </button>

                                      <template x-for="(page, idx) in pageRange" :key="idx">
                                          <span>
                                              <template x-if="page === '...'">
                                                  <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                              </template>
                                              <template x-if="page !== '...'">
                                                  <button type="button" @click="goToPage(page)"
                                                      :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                                      class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                                      x-text="page">
                                                  </button>
                                              </template>
                                          </span>
                                      </template>

                                      <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                          :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                          class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                      </button>
                                  </div>
                              </template>
                          </div>
                      </div>
                  @else
                      <div class="text-center py-12 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs">
                          <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                          </div>
                          <p class="text-sm font-bold text-slate-900">No overall data found.</p>
                      </div>
                  @endif
            </div>

        <!-- HOURLY (DAILY) REPORT VIEW -->
        @elseif (Auth::user()->hasPermission('rep_daily') && $viewMode === 'hourly')
            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                <div class="h-56 flex items-end gap-1.5 pt-8 pb-3 border-b border-slate-200 overflow-x-auto touch-scroll relative">
                    @php $maxHourCount = max(array_merge($hourlyData, [1])); @endphp
                    @foreach ($hourlyData as $hour => $count)
                        @php
                            $heightPercent = ($count / max($maxHourCount, 1)) * 100;
                            $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[28px]"
                            title="{{ $formattedHour }} — {{ $count }} pax">
                            <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full max-w-[36px] mx-auto bg-gradient-to-t from-slate-800 to-slate-700 group-hover:from-slate-900 group-hover:to-slate-800 rounded-t-md transition-all cursor-pointer shadow-sm"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[9px] font-semibold text-slate-500 mt-2">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}h</span>
                            <span class="text-[10px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- WEEKLY REPORT VIEW (Monday to Sunday) -->
        @elseif (Auth::user()->hasPermission('rep_weekly') && $viewMode === 'weekly')
            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Weekly Visitor Breakdown') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">
                            @if(isset($startOfWeek) && isset($endOfWeek))
                                {{ $startOfWeek->format('l, d M Y') }} &ndash; {{ $endOfWeek->format('l, d M Y') }}
                            @endif
                        </p>
                    </div>
                    @if(isset($weeklyTotal))
                        <div class="text-right">
                            <span class="text-xs text-slate-500 font-semibold">{{ __('Total Weekly Pax:') }}</span>
                            <span class="text-base font-extrabold text-slate-900 ml-1.5 tabular-nums">{{ $weeklyTotal }}</span>
                        </div>
                    @endif
                </div>

                <div class="h-56 flex items-end gap-2 sm:gap-4 pt-8 pb-3 border-b border-slate-200 overflow-x-auto touch-scroll relative">
                    @php
                        $weeklyPaxes = array_map(fn($d) => $d['pax'], $weeklyReportData ?? []);
                        $maxWeekCount = max(array_merge($weeklyPaxes, [1]));
                    @endphp
                    @foreach ($weeklyReportData ?? [] as $dayInfo)
                        @php
                            $heightPercent = ($dayInfo['pax'] / max($maxWeekCount, 1)) * 100;
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[36px]"
                            title="{{ $dayInfo['day_full'] }} ({{ $dayInfo['date'] }}): {{ $dayInfo['pax'] }} pax">
                            <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-2 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $dayInfo['pax'] }} pax
                            </div>
                            <div class="w-full max-w-[48px] mx-auto bg-gradient-to-t from-slate-800 to-slate-700 group-hover:from-slate-900 group-hover:to-slate-800 rounded-t-md transition-all cursor-pointer shadow-sm"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[11px] font-bold text-slate-700 mt-2">{{ __($dayInfo['day_name']) }}</span>
                            <span class="text-[9px] font-medium text-slate-400">{{ $dayInfo['date'] }}</span>
                            <span class="text-[10px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $dayInfo['pax'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- MONTHLY REPORT VIEW -->
        @elseif(Auth::user()->hasPermission('rep_monthly') && $viewMode === 'daily')
            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                <div class="h-56 flex items-end gap-1.5 pt-8 pb-3 border-b border-slate-200 overflow-x-auto touch-scroll relative">
                    @php $maxDayCount = max(array_merge(array_values($dailyReportData), [1])); @endphp
                    @foreach ($dailyReportData as $dayDate => $count)
                        @php
                            $heightPercent = ($count / max($maxDayCount, 1)) * 100;
                            $dayNum = Carbon\Carbon::parse($dayDate)->format('d');
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[24px]"
                            title="{{ $dayDate }}: {{ $count }} pax">
                            <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full max-w-[36px] mx-auto bg-gradient-to-t from-slate-800 to-slate-700 group-hover:from-slate-900 group-hover:to-slate-800 rounded-t-md transition-all cursor-pointer shadow-sm"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[9px] font-semibold text-slate-500 mt-2">{{ $dayNum }}</span>
                            <span class="text-[9px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- YEARLY REPORT VIEW -->
        @elseif(Auth::user()->hasPermission('rep_yearly') && $viewMode === 'monthly')
            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-4">
                <div class="h-56 flex items-end gap-2 pt-8 pb-3 border-b border-slate-200 overflow-x-auto touch-scroll relative">
                    @php
                        $maxMonthCount = max(array_merge(array_values($yearlyReportData), [1]));
                        $monthNames = [
                            '01' => 'Jan',
                            '02' => 'Feb',
                            '03' => 'Mar',
                            '04' => 'Apr',
                            '05' => 'May',
                            '06' => 'Jun',
                            '07' => 'Jul',
                            '08' => 'Aug',
                            '09' => 'Sep',
                            '10' => 'Oct',
                            '11' => 'Nov',
                            '12' => 'Dec',
                        ];
                    @endphp
                    @foreach ($yearlyReportData as $mNum => $count)
                        @php
                            $heightPercent = ($count / max($maxMonthCount, 1)) * 100;
                            $mLabel = $monthNames[$mNum];
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[36px]"
                            title="{{ $mLabel }} {{ $yearFilter }}: {{ $count }} pax">
                            <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full max-w-[36px] mx-auto bg-gradient-to-t from-slate-800 to-slate-700 group-hover:from-slate-900 group-hover:to-slate-800 rounded-t-md transition-all cursor-pointer shadow-sm"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[10px] font-semibold text-slate-500 mt-2">{{ $mLabel }}</span>
                            <span class="text-[10px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- WAITLIST DATABASE RECORDS -->
        @elseif ($viewMode === 'waitlist' && Auth::user()->hasPermission('rep_waitlist'))
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden"
                x-data="{
                    perPage: parseInt(localStorage.getItem('meja_db_per_page_waitlist')) || 5,
                    currentPage: (function() {
                        const p = parseInt(localStorage.getItem('meja_db_page_waitlist'));
                        return (p && p > 0) ? p : 1;
                    })(),
                    totalRecords: {{ count($waitlists) }},
                    get totalPages() {
                        return Math.max(1, Math.ceil(this.totalRecords / this.perPage));
                    },
                    init() {
                        if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                        this.$watch('currentPage', val => localStorage.setItem('meja_db_page_waitlist', val));
                        this.$watch('perPage', val => {
                            localStorage.setItem('meja_db_per_page_waitlist', val);
                            if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                        });
                    },
                    nextPage() {
                        if (this.currentPage < this.totalPages) this.currentPage++;
                    },
                    prevPage() {
                        if (this.currentPage > 1) this.currentPage--;
                    },
                    goToPage(p) {
                        if (p >= 1 && p <= this.totalPages) this.currentPage = p;
                    },
                    get pageRange() {
                        const total = this.totalPages;
                        const current = this.currentPage;
                        const delta = 2;
                        if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
                        const range = [];
                        const left = Math.max(2, current - delta);
                        const right = Math.min(total - 1, current + delta);
                        range.push(1);
                        if (left > 2) range.push('...');
                        for (let i = left; i <= right; i++) range.push(i);
                        if (right < total - 1) range.push('...');
                        range.push(total);
                        return range;
                    }
                }">
                <!-- Table Header Controls Bar (Universal Top Bar) -->
                <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <h3 class="font-bold text-slate-800 text-sm">{{ __('Waitlist Records Database') }}</h3>
                        <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200 tabular-nums">
                            {{ count($waitlists) }} {{ __('Records') }}
                        </span>
                    </div>

                    <!-- Right Controls: Rows Dropdown (10, 25, 50, 100) & Top Numbered Pagination -->
                    <div class="flex items-center gap-3 self-end sm:self-auto flex-wrap">
                        <!-- Dropdown: 10, 25, 50, 100 -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Rows per page:') }}</span>
                            <select x-model.number="perPage" @change="currentPage = 1"
                                class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                                <option :value="5">5</option>
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                            </select>
                        </div>

                        <!-- Top Numbered Pagination Stepper -->
                        <template x-if="totalPages > 1">
                            <div class="flex items-center gap-1 overflow-x-auto py-1">
                                <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                    :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                    class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>

                                <template x-for="(page, idx) in pageRange" :key="idx">
                                    <span>
                                        <template x-if="page === '...'">
                                            <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                        </template>
                                        <template x-if="page !== '...'">
                                            <button type="button" @click="goToPage(page)"
                                                :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                                x-text="page">
                                            </button>
                                        </template>
                                    </span>
                                </template>

                                <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                    :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                    class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="overflow-x-auto touch-scroll">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-2.5 px-3 sm:px-4">ID</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Customer Name') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Phone') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Guests') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Status') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">Created At</th>
                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <th class="py-2.5 px-3 sm:px-4">{{ __('Staff') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($waitlists as $w)
                                <tr class="hover:bg-slate-50/60 transition"
                                    x-show="({{ $loop->iteration }} > (currentPage - 1) * perPage) && ({{ $loop->iteration }} <= currentPage * perPage)"
                                    x-cloak>
                                    <td class="py-2.5 px-3 sm:px-4 font-bold text-slate-900">#{{ $w->id }}</td>
                                    <td class="py-2.5 px-3 sm:px-4 font-semibold text-slate-900">
                                        {{ ($w->customer_name && !in_array($w->customer_name, ['Walk-in Guest', 'Walk-In Guest', 'Walk-in', 'Walk-In'])) ? $w->customer_name : __('Guest') }}</td>
                                    <td class="py-2.5 px-3 sm:px-4 text-slate-600">{{ $w->phone ?? '-' }}</td>
                                    <td class="py-2.5 px-3 sm:px-4 font-bold text-slate-800">{{ $w->pax }} Pax</td>
                                    <td class="py-2.5 px-3 sm:px-4">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $w->status == 'waiting' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ ucfirst($w->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4 text-slate-500 tabular-nums">
                                        {{ $w->created_at ? $w->created_at->format('d M Y, H:i') : '-' }}</td>
                                    @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                        <td class="py-2.5 px-3 sm:px-4 text-slate-600">{{ $w->created_by ?? 'System' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) ? 7 : 6 }}"
                                        class="text-center py-10 text-slate-400 font-medium">
                                        No waitlist {{ __('records') }} found in the database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Waitlist Table Footer Bar (Universal Bottom Bar) -->
                @if (count($waitlists) > 0)
                    <div class="p-3 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                        <span>
                            {{ __('Showing') }} <strong class="text-slate-800"
                                x-text="totalRecords > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></strong>
                            {{ __('to') }} <strong class="text-slate-800"
                                x-text="Math.min(currentPage * perPage, totalRecords)"></strong>
                            {{ __('of') }} <strong class="text-slate-800" x-text="totalRecords"></strong> {{ __('records') }}
                        </span>

                        <template x-if="totalPages > 1">
                            <div class="flex items-center gap-1 overflow-x-auto py-1">
                                <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                    :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                    class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>

                                <template x-for="(page, idx) in pageRange" :key="idx">
                                    <span>
                                        <template x-if="page === '...'">
                                            <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                        </template>
                                        <template x-if="page !== '...'">
                                            <button type="button" @click="goToPage(page)"
                                                :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                                x-text="page">
                                            </button>
                                        </template>
                                    </span>
                                </template>

                                <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                    :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                    class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                @endif
            </div>
        @endif


        @if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')
        <!-- VISITOR DINING LOG ENTRIES (With Dropdown Per-Page 10, 25, 50, 100 & Numbered Pagination) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden"
            x-data="{
                perPage: parseInt(localStorage.getItem('meja_db_per_page_{{ $viewMode }}')) || 5,
                currentPage: (function() {
                    const p = parseInt(localStorage.getItem('meja_db_page_{{ $viewMode }}'));
                    return (p && p > 0) ? p : 1;
                })(),
                totalRecords: {{ count($logs) }},
                get totalPages() {
                    return Math.max(1, Math.ceil(this.totalRecords / this.perPage));
                },
                init() {
                    if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                    this.$watch('currentPage', val => localStorage.setItem('meja_db_page_{{ $viewMode }}', val));
                    this.$watch('perPage', val => {
                        localStorage.setItem('meja_db_per_page_{{ $viewMode }}', val);
                        if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                    });
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },
                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },
                goToPage(p) {
                    if (p >= 1 && p <= this.totalPages) this.currentPage = p;
                },
                get pageRange() {
                    const total = this.totalPages;
                    const current = this.currentPage;
                    const delta = 2;
                    if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
                    const range = [];
                    const left = Math.max(2, current - delta);
                    const right = Math.min(total - 1, current + delta);
                    range.push(1);
                    if (left > 2) range.push('...');
                    for (let i = left; i <= right; i++) range.push(i);
                    if (right < total - 1) range.push('...');
                    range.push(total);
                    return range;
                }
            }">

            <!-- Table Header Controls Bar (Universal Top Bar) -->
            <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <h3 class="font-bold text-slate-800 text-sm">{{ __('Visitor Log Entries & Dining Durations') }}</h3>
                    <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200 tabular-nums">
                        {{ count($logs) }} {{ __('Records') }}
                    </span>
                </div>

                <!-- Right Controls: Rows Dropdown (10, 25, 50, 100) & Top Numbered Pagination -->
                <div class="flex items-center gap-3 self-end sm:self-auto flex-wrap">
                    <!-- Dropdown: 10, 25, 50, 100 -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Rows per page:') }}</span>
                        <select x-model.number="perPage" @change="currentPage = 1"
                            class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </div>

                    <!-- Top Numbered Pagination Stepper -->
                    <template x-if="totalPages > 1">
                        <div class="flex items-center gap-1 overflow-x-auto py-1">
                            <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>

                            <template x-for="(page, idx) in pageRange" :key="idx">
                                <span>
                                    <template x-if="page === '...'">
                                        <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                    </template>
                                    <template x-if="page !== '...'">
                                        <button type="button" @click="goToPage(page)"
                                            :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                            x-text="page">
                                        </button>
                                    </template>
                                </span>
                            </template>

                            <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Table Rows Filtered Dynamically by Current Page & Rows Per Page -->
            <div class="overflow-x-auto touch-scroll">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-2.5 px-3 sm:px-4">{{ __('Log ID') }}</th>
                            <th class="py-2.5 px-3 sm:px-4">{{ __('Customer Name') }}</th>
                            <th class="py-2.5 px-3 sm:px-4">{{ __('Phone') }}</th>
                            <th class="py-2.5 px-3 sm:px-4">{{ __('Guests') }}</th>
                            <th class="py-2.5 px-3 sm:px-4">{{ __('Started') }}</th>
                            <th class="py-2.5 px-3 sm:px-4">{{ __('Ended') }}</th>
                            @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Elapsed') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Staff') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/60 transition"
                                x-show="({{ $loop->iteration }} > (currentPage - 1) * perPage) && ({{ $loop->iteration }} <= currentPage * perPage)"
                                x-cloak>
                                <td class="py-2.5 px-3 sm:px-4 font-bold text-slate-900">#{{ $log->id }}</td>
                                <td class="py-2.5 px-3 sm:px-4 font-semibold text-slate-900">
                                    {{ ($log->customer_name && !in_array($log->customer_name, ['Walk-in Guest', 'Walk-In Guest', 'Walk-in', 'Walk-In'])) ? $log->customer_name : __('Guest') }}</td>
                                <td class="py-2.5 px-3 sm:px-4 text-slate-600">{{ $log->phone ?? '-' }}</td>
                                <td class="py-2.5 px-3 sm:px-4 font-bold text-slate-800">{{ $log->pax }} Pax</td>
                                <td class="py-2.5 px-3 sm:px-4 text-slate-500 tabular-nums">
                                    {{ $log->started_at ? \Carbon\Carbon::parse($log->started_at)->format('d M, H:i') : '-' }}
                                </td>
                                <td class="py-2.5 px-3 sm:px-4 text-slate-500 tabular-nums">
                                    {{ $log->ended_at ? \Carbon\Carbon::parse($log->ended_at)->format('d M, H:i') : __('In Progress') }}
                                </td>
                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <td class="py-2.5 px-3 sm:px-4 font-medium text-slate-700">
                                        {{ $log->time_elapsed ?? '-' }}</td>
                                    <td class="py-2.5 px-3 sm:px-4 text-slate-600">{{ $log->created_by ?? '-' }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) ? 8 : 6 }}"
                                    class="text-center py-10 text-slate-400 font-medium">
                                    {{ __('No visitor log records found for this filter criteria.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Bar (Universal Bottom Bar) -->
            @if (count($logs) > 0)
                <div class="p-3 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                    <span>
                        {{ __('Showing') }} <strong class="text-slate-800"
                            x-text="totalRecords > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></strong>
                        {{ __('to') }} <strong class="text-slate-800"
                            x-text="Math.min(currentPage * perPage, totalRecords)"></strong>
                        {{ __('of') }} <strong class="text-slate-800" x-text="totalRecords"></strong> {{ __('records') }}
                    </span>

                    <template x-if="totalPages > 1">
                        <div class="flex items-center gap-1 overflow-x-auto py-1">
                            <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>

                            <template x-for="(page, idx) in pageRange" :key="idx">
                                <span>
                                    <template x-if="page === '...'">
                                        <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                    </template>
                                    <template x-if="page !== '...'">
                                        <button type="button" @click="goToPage(page)"
                                            :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                            x-text="page">
                                        </button>
                                    </template>
                                </span>
                            </template>

                            <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            @endif
        </div>
            @endif

    </main>
</x-app-layout>