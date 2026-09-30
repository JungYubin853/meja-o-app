<!-- Immediate Session Redirect: If user accessed /database with no query parameter, redirect to their last active tab immediately -->
<script>
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('view')) {
            const savedUrl = localStorage.getItem('meja_last_database_url');
            if (savedUrl && savedUrl !== window.location.href) {
                window.location.replace(savedUrl);
                return;
            }
        }
        // Save the full current URL (view, month, year, date)
        localStorage.setItem('meja_last_database_url', window.location.href);
    })();
</script>

<x-app-layout title="Meja-O | Visitor Analytics & Database">

    <main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4" x-data="{ exportMenuOpen: false }">

        <!-- Navigation Tabs: Overall, Daily Report, Monthly Report, Yearly Report, and Waitlist -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 touch-scroll scrollbar-none">
            @if (Auth::user()->hasPermission('rep_overall'))
                <a href="/database?view=overall"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'overall' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    Overall
                </a>
            @endif

            @if (Auth::user()->hasPermission('rep_daily'))
                <a href="/database?view=hourly&date={{ $dateFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'hourly' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    Daily Report
                </a>
            @endif
            
            @if (Auth::user()->hasPermission('rep_monthly'))
                <a href="/database?view=daily&month={{ $monthFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'daily' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    Monthly Report
                </a>
            @endif
            
            @if (Auth::user()->hasPermission('rep_yearly'))
                <a href="/database?view=monthly&year={{ $yearFilter }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'monthly' ? 'bg-slate-900 text-white shadow-soft-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    Yearly Report
                </a>
            @endif

            
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

        </div>

        <!-- Filter & Actions Bar: Perfectly Aligned 1-Row Layout -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">
                    @if ($viewMode === 'overall')
                        Overall Analytics & Metrics
                    @elseif ($viewMode === 'waitlist')
                        Waitlist Customer Records
                    @elseif ($viewMode === 'hourly')
                        Daily Visitor Report (Hourly Breakdown)
                    @elseif ($viewMode === 'daily')
                        Monthly Visitor Report (Daily Breakdown)
                    @elseif ($viewMode === 'monthly')
                        Yearly Visitor Report (Monthly Breakdown)
                    @endif
                </h2>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Analyze customer volume and dining traffic patterns.</p>
            </div>

            <!-- Controls: Filter & Export (Desktop: 1 row side-by-side; Mobile: Row 1 = Date + Filter, Row 2 = Export) -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">

                @if (auth()->check() && auth()->user()->isSuperAdmin() && $viewMode !== 'overall')
                    <form method="GET" action="/database" class="flex items-center m-0 p-0 w-full sm:w-auto">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if ($viewMode === 'hourly') <input type="hidden" name="date" value="{{ $dateFilter }}"> @endif
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

                @if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')
                    <form method="GET" action="/database" class="flex items-center gap-2 m-0 p-0 w-full sm:w-auto">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if (isset($selectedOutletId)) <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}"> @endif

                        @if ($viewMode === 'monthly')
                            @php
                                $startYear = 2026;
                                $currentYear = max(2026, (int) date('Y'));
                            @endphp
                            <select name="year"
                                class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                                @for ($y = $startYear; $y <= $currentYear; $y++)
                                    <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                            <button type="submit"
                                class="h-10 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 rounded-xl transition shadow-soft-xs flex items-center justify-center shrink-0 box-border">
                                Filter
                            </button>
                        @elseif ($viewMode === 'daily')
                            <input type="month" name="month" value="{{ $monthFilter }}"
                                class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                            <button type="submit"
                                class="h-10 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 rounded-xl transition shadow-soft-xs flex items-center justify-center shrink-0 box-border">
                                Filter
                            </button>
                        @elseif ($viewMode === 'hourly')
                            <input type="date" name="date" value="{{ $dateFilter }}"
                                class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                            <button type="submit"
                                class="h-10 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 rounded-xl transition shadow-soft-xs flex items-center justify-center shrink-0 box-border">
                                Filter
                            </button>
                        @endif
                    </form>
                @endif

                @if ($viewMode !== 'waitlist' && $viewMode !== 'overall')
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
                            class="absolute right-0 top-full mt-2 w-full sm:w-44 bg-white rounded-2xl shadow-soft-xl border border-slate-100 py-1.5 z-30"
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
                                <span>Export Excel (CSV)</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- OVERALL VIEW -->
        @if ($viewMode === 'overall' && Auth::user()->hasPermission('rep_overall'))
            <div class="space-y-4">
                @if(count($overallStats) > 0)
                      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                          <div class="overflow-x-auto touch-scroll">
                              <table class="w-full text-left border-collapse">
                                  <thead>
                                      <tr class="bg-slate-50 border-b border-slate-200/80">
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Outlet Name</th>
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="Recorded on {{ $dateFilter }}">Day Visitors</th>
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="{{ \Carbon\Carbon::parse($monthFilter)->format('F Y') }}">Month Visitors</th>
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="Year {{ $yearFilter }}">Year Visitors</th>
                                      </tr>
                                  </thead>
                                  <tbody class="divide-y divide-slate-100">
                                      @foreach ($overallStats as $stat)
                                          <tr class="hover:bg-slate-50/50 transition">
                                              <td class="p-3 sm:p-4">
                                                  <span class="text-xs sm:text-sm font-bold text-slate-900">{{ $stat['outlet']->name }}</span>
                                              </td>
                                              <td class="p-3 sm:p-4 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['daily'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">Pax</span>
                                              </td>
                                              <td class="p-3 sm:p-4 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['monthly'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">Pax</span>
                                              </td>
                                              <td class="p-3 sm:p-4 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['yearly'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">Pax</span>
                                              </td>
                                          </tr>
                                      @endforeach
                                  </tbody>
                              </table>
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
                            <div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[9px] font-semibold text-slate-500 mt-2">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}h</span>
                            <span class="text-[10px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $count }}</span>
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
                            <div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"
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
                            <div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[10px] font-semibold text-slate-500 mt-2">{{ $mLabel }}</span>
                            <span class="text-[10px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- WAITLIST DATABASE RECORDS -->
        @elseif ($viewMode === 'waitlist' && Auth::user()->hasPermission('rep_waitlist'))
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Waitlist Records Database</h3>
                    <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full border border-slate-200">
                        {{ count($waitlists) }} Records
                    </span>
                </div>
                <div class="overflow-x-auto touch-scroll">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="p-3.5 sm:p-4">ID</th>
                                <th class="p-3.5 sm:p-4">Customer Name</th>
                                <th class="p-3.5 sm:p-4">Phone</th>
                                <th class="p-3.5 sm:p-4">Guests</th>
                                <th class="p-3.5 sm:p-4">Status</th>
                                <th class="p-3.5 sm:p-4">Created At</th>
                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <th class="p-3.5 sm:p-4">Staff</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($waitlists as $w)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="p-3.5 sm:p-4 font-bold text-slate-900">#{{ $w->id }}</td>
                                    <td class="p-3.5 sm:p-4 font-semibold text-slate-900">
                                        {{ $w->customer_name ?? 'Walk-in Guest' }}</td>
                                    <td class="p-3.5 sm:p-4 text-slate-600">{{ $w->phone ?? '-' }}</td>
                                    <td class="p-3.5 sm:p-4 font-bold text-slate-800">{{ $w->pax }} Pax</td>
                                    <td class="p-3.5 sm:p-4">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $w->status == 'waiting' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ ucfirst($w->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 sm:p-4 text-slate-500 tabular-nums">
                                        {{ $w->created_at ? $w->created_at->format('d M Y, H:i') : '-' }}</td>
                                    @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                        <td class="p-3.5 sm:p-4 text-slate-600">{{ $w->created_by ?? 'System' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) ? 7 : 6 }}"
                                        class="text-center py-10 text-slate-400 font-medium">
                                        No waitlist records found in the database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif


        <!-- CUSTOMER HABITS VIEW -->
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

        @if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')
                    <form method="GET" action="/database" class="flex items-center gap-2 m-0 p-0 w-full sm:w-auto">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        @if (isset($selectedOutletId)) <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}"> @endif

                        @if ($viewMode === 'monthly')
                            @php
                                $startYear = 2026;
                                $currentYear = max(2026, (int) date('Y'));
                            @endphp
                            <select name="year"
                                class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                                @for ($y = $startYear; $y <= $currentYear; $y++)
                                    <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                            <button type="submit"
                                class="h-10 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 rounded-xl transition shadow-soft-xs flex items-center justify-center shrink-0 box-border">
                                Filter
                            </button>
                        @elseif ($viewMode === 'daily')
                            <input type="month" name="month" value="{{ $monthFilter }}"
                                class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                            <button type="submit"
                                class="h-10 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 rounded-xl transition shadow-soft-xs flex items-center justify-center shrink-0 box-border">
                                Filter
                            </button>
                        @elseif ($viewMode === 'hourly')
                            <input type="date" name="date" value="{{ $dateFilter }}"
                                class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto">
                            <button type="submit"
                                class="h-10 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 rounded-xl transition shadow-soft-xs flex items-center justify-center shrink-0 box-border">
                                Filter
                            </button>
                        @endif
                    </form>
                @endif

                @if ($viewMode !== 'waitlist' && $viewMode !== 'overall')
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
                            class="absolute right-0 top-full mt-2 w-full sm:w-44 bg-white rounded-2xl shadow-soft-xl border border-slate-100 py-1.5 z-30"
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
                                <span>Export Excel (CSV)</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- OVERALL VIEW -->
        @if ($viewMode === 'overall' && Auth::user()->hasPermission('rep_overall'))
            <div class="space-y-4">
                @if(count($overallStats) > 0)
                      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                          <div class="overflow-x-auto touch-scroll">
                              <table class="w-full text-left border-collapse">
                                  <thead>
                                      <tr class="bg-slate-50 border-b border-slate-200/80">
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Outlet Name</th>
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="Recorded on {{ $dateFilter }}">Day Visitors</th>
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="{{ \Carbon\Carbon::parse($monthFilter)->format('F Y') }}">Month Visitors</th>
                                          <th class="p-3 sm:p-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right" title="Year {{ $yearFilter }}">Year Visitors</th>
                                      </tr>
                                  </thead>
                                  <tbody class="divide-y divide-slate-100">
                                      @foreach ($overallStats as $stat)
                                          <tr class="hover:bg-slate-50/50 transition">
                                              <td class="p-3 sm:p-4">
                                                  <span class="text-xs sm:text-sm font-bold text-slate-900">{{ $stat['outlet']->name }}</span>
                                              </td>
                                              <td class="p-3 sm:p-4 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['daily'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">Pax</span>
                                              </td>
                                              <td class="p-3 sm:p-4 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['monthly'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">Pax</span>
                                              </td>
                                              <td class="p-3 sm:p-4 whitespace-nowrap text-right">
                                                  <span class="text-sm font-extrabold text-slate-900 tabular-nums">{{ $stat['yearly'] }}</span>
                                                  <span class="text-[10px] text-slate-400 font-semibold ml-0.5">Pax</span>
                                              </td>
                                          </tr>
                                      @endforeach
                                  </tbody>
                              </table>
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
                            <div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[9px] font-semibold text-slate-500 mt-2">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}h</span>
                            <span class="text-[10px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $count }}</span>
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
                            <div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"
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
                            <div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[10px] font-semibold text-slate-500 mt-2">{{ $mLabel }}</span>
                            <span class="text-[10px] font-extrabold text-slate-800 mt-0.5 tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- WAITLIST DATABASE RECORDS -->
        @elseif ($viewMode === 'waitlist' && Auth::user()->hasPermission('rep_waitlist'))
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Waitlist Records Database</h3>
                    <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full border border-slate-200">
                        {{ count($waitlists) }} Records
                    </span>
                </div>
                <div class="overflow-x-auto touch-scroll">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="p-3.5 sm:p-4">ID</th>
                                <th class="p-3.5 sm:p-4">Customer Name</th>
                                <th class="p-3.5 sm:p-4">Phone</th>
                                <th class="p-3.5 sm:p-4">Guests</th>
                                <th class="p-3.5 sm:p-4">Status</th>
                                <th class="p-3.5 sm:p-4">Created At</th>
                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <th class="p-3.5 sm:p-4">Staff</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($waitlists as $w)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="p-3.5 sm:p-4 font-bold text-slate-900">#{{ $w->id }}</td>
                                    <td class="p-3.5 sm:p-4 font-semibold text-slate-900">
                                        {{ $w->customer_name ?? 'Walk-in Guest' }}</td>
                                    <td class="p-3.5 sm:p-4 text-slate-600">{{ $w->phone ?? '-' }}</td>
                                    <td class="p-3.5 sm:p-4 font-bold text-slate-800">{{ $w->pax }} Pax</td>
                                    <td class="p-3.5 sm:p-4">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $w->status == 'waiting' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ ucfirst($w->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 sm:p-4 text-slate-500 tabular-nums">
                                        {{ $w->created_at ? $w->created_at->format('d M Y, H:i') : '-' }}</td>
                                    @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                        <td class="p-3.5 sm:p-4 text-slate-600">{{ $w->created_by ?? 'System' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) ? 7 : 6 }}"
                                        class="text-center py-10 text-slate-400 font-medium">
                                        No waitlist records found in the database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif


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
        <!-- VISITOR DINING LOG ENTRIES (With Dropdown Per-Page 10, 25, 50, 100 & Page Navigator) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden"
            x-data="{
                perPage: 10,
                currentPage: 1,
                totalRecords: {{ count($logs) }},
                get totalPages() {
                    return Math.max(1, Math.ceil(this.totalRecords / this.perPage));
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },
                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                }
            }">

            <!-- Table Header: Title, Records Badge, Rows Dropdown, and 1/10 Pagination Stepper -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Visitor Log Entries & Dining Durations</h3>
                    <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full border border-slate-200 tabular-nums">
                        {{ count($logs) }} Records
                    </span>
                </div>

                <!-- Right Controls: Rows Dropdown (10, 25, 50, 100) & Page Navigator -->
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <!-- Dropdown: 10, 25, 50, 100 -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] text-slate-500 font-semibold">Show:</span>
                        <select x-model.number="perPage" @change="currentPage = 1"
                            class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </div>

                    <!-- Page Stepper: ‹ 1 / 10 › -->
                    <div class="flex items-center gap-1 bg-slate-50 p-0.5 rounded-lg border border-slate-200">
                        <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed text-slate-400' :
                                'hover:bg-white text-slate-800 active:scale-90 shadow-2xs'"
                            class="w-7 h-7 flex items-center justify-center font-black text-sm rounded-md transition"
                            title="Previous Page">
                            ‹
                        </button>
                        <span class="text-xs font-extrabold text-slate-800 px-2 tabular-nums select-none">
                            <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                        </span>
                        <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                            :class="currentPage >= totalPages ? 'opacity-40 cursor-not-allowed text-slate-400' :
                                'hover:bg-white text-slate-800 active:scale-90 shadow-2xs'"
                            class="w-7 h-7 flex items-center justify-center font-black text-sm rounded-md transition"
                            title="Next Page">
                            ›
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Rows Filtered Dynamically by Current Page & Rows Per Page -->
            <div class="overflow-x-auto touch-scroll">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                            <th class="p-3.5 sm:p-4">Log ID</th>
                            <th class="p-3.5 sm:p-4">Customer Name</th>
                            <th class="p-3.5 sm:p-4">Phone</th>
                            <th class="p-3.5 sm:p-4">Guests</th>
                            <th class="p-3.5 sm:p-4">Started</th>
                            <th class="p-3.5 sm:p-4">Ended</th>
                            @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                <th class="p-3.5 sm:p-4">Elapsed</th>
                                <th class="p-3.5 sm:p-4">Staff</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/60 transition"
                                x-show="({{ $loop->iteration }} > (currentPage - 1) * perPage) && ({{ $loop->iteration }} <= currentPage * perPage)"
                                x-cloak>
                                <td class="p-3.5 sm:p-4 font-bold text-slate-900">#{{ $log->id }}</td>
                                <td class="p-3.5 sm:p-4 font-semibold text-slate-900">
                                    {{ $log->customer_name ?? 'Walk-in Guest' }}</td>
                                <td class="p-3.5 sm:p-4 text-slate-600">{{ $log->phone ?? '-' }}</td>
                                <td class="p-3.5 sm:p-4 font-bold text-slate-800">{{ $log->pax }} Pax</td>
                                <td class="p-3.5 sm:p-4 text-slate-500 tabular-nums">
                                    {{ $log->started_at ? \Carbon\Carbon::parse($log->started_at)->format('d M, H:i') : '-' }}
                                </td>
                                <td class="p-3.5 sm:p-4 text-slate-500 tabular-nums">
                                    {{ $log->ended_at ? \Carbon\Carbon::parse($log->ended_at)->format('d M, H:i') : 'In Progress' }}
                                </td>
                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <td class="p-3.5 sm:p-4 font-medium text-slate-700">
                                        {{ $log->time_elapsed ?? '-' }}</td>
                                    <td class="p-3.5 sm:p-4 text-slate-600">{{ $log->created_by ?? '-' }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) ? 8 : 6 }}"
                                    class="text-center py-10 text-slate-400 font-medium">
                                    No visitor log records found for this filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer: "Showing X to Y of Z records" with Secondary Stepper -->
            @if (count($logs) > 0)
                <div class="p-3.5 sm:p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                    <span>
                        Showing <strong class="text-slate-800"
                            x-text="totalRecords > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></strong>
                        to <strong class="text-slate-800"
                            x-text="Math.min(currentPage * perPage, totalRecords)"></strong>
                        of <strong class="text-slate-800" x-text="totalRecords"></strong> records
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Previous
                        </button>
                        <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Next
                        </button>
                    </div>
                </div>
            @endif
        </div>
            @endif

    </main>
</x-app-layout>