<x-app-layout title="Meja-O | Visitor Analytics & Database" x-data="{ activeTab: '{{ $viewMode }}' }">

    <!-- Header Custom Actions (Back to Dashboard link) -->
    <x-slot name="headerActions">
        <a href="/" title="Back to Dashboard"
            class="bg-slate-100 hover:bg-slate-200 text-slate-700 p-2 rounded-xl transition flex items-center justify-center border border-slate-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
        </a>
    </x-slot>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        <!-- Tabbed Navigation Bar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="/database?view=waitlist"
                class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'waitlist' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                Waitlist
            </a>

            @if (auth()->check() && auth()->user()->role === 'admin')
                <a href="/database?view=hourly&date={{ $dateFilter }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'hourly' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                    Daily Report
                </a>
                <a href="/database?view=daily&month={{ $monthFilter }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'daily' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                    Monthly Report
                </a>
                <a href="/database?view=monthly&year={{ $yearFilter }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'monthly' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                    Yearly Report
                </a>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    @if ($viewMode === 'waitlist')
                        Waitlist Customers Database Records
                    @elseif ($viewMode === 'hourly')
                        Hourly Visitor (Per Day)
                    @elseif($viewMode === 'daily')
                        Daily Visitor (Per Month)
                    @elseif($viewMode === 'monthly')
                        Yearly Visitor (Per Year)
                    @endif
                </h2>
                <p class="text-xs text-slate-500">Analyze visitor patterns and filter records by date.</p>
            </div>

            <!-- Date Filter Form -->
            <form method="GET" action="/database" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                @if ($viewMode === 'monthly')
                    <select name="year" class="text-xs sm:text-sm p-2 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 font-bold">
                        @for ($y = 2024; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-xs">Filter Year</button>
                @elseif($viewMode === 'daily')
                    <input type="month" name="month" value="{{ $monthFilter }}" class="text-xs sm:text-sm p-2 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-xs">Filter Month</button>
                @else
                    <input type="date" name="date" value="{{ $dateFilter }}" class="text-xs sm:text-sm p-2 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-xs">Filter Date</button>
                @endif
            </form>
        </div>

        @if (auth()->check() && auth()->user()->role === 'admin' && $viewMode === 'hourly')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Daily Visitors</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $dailyTotal }} <span class="text-xs font-normal text-slate-500">Pax</span></h3>
                        <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Recorded on {{ $dateFilter }}</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Monthly Visitors</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $monthlyTotal }} <span class="text-xs font-normal text-slate-500">Pax</span></h3>
                        <p class="text-[11px] text-amber-600 font-medium mt-0.5">Accumulated for this month</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Customer Traffic Graph (Hourly Visits)</h3>
                <div class="h-52 flex items-end gap-1 sm:gap-2 pt-8 pb-3 border-b border-slate-200 overflow-x-auto relative">
                    @php $maxHourCount = max(array_merge($hourlyData, [1])); @endphp
                    @foreach ($hourlyData as $hour => $count)
                        @php
                            $heightPercent = ($count / max($maxHourCount, 1)) * 100;
                            $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[28px]" title="{{ $formattedHour }} — {{ $count }} visitors">
                            <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full bg-amber-500 group-hover:bg-amber-600 rounded-t-md transition-all cursor-pointer" style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[9px] font-semibold text-slate-500 mt-2">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}h</span>
                            <span class="text-[10px] font-extrabold text-slate-700 mt-0.5">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif(auth()->check() && auth()->user()->role === 'admin' && $viewMode === 'daily')
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Daily Visitor Report for {{ $monthFilter }}</h3>
                <p class="text-xs text-slate-500">Total visitor counts per day throughout the selected month.</p>
                <div class="h-56 flex items-end gap-1.5 pt-8 pb-3 border-b border-slate-200 overflow-x-auto relative">
                    @php $maxDayCount = max(array_merge(array_values($dailyReportData), [1])); @endphp
                    @foreach ($dailyReportData as $dayDate => $count)
                        @php
                            $heightPercent = ($count / max($maxDayCount, 1)) * 100;
                            $dayNum = Carbon\Carbon::parse($dayDate)->format('d');
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[26px]" title="{{ $dayDate }}: {{ $count }} pax">
                            <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full bg-emerald-500 group-hover:bg-emerald-600 rounded-t-md transition-all cursor-pointer" style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[9px] font-semibold text-slate-500 mt-2">{{ $dayNum }}</span>
                            <span class="text-[9px] font-extrabold text-slate-700 mt-0.5">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif(auth()->check() && auth()->user()->role === 'admin' && $viewMode === 'monthly')
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Yearly Visitor Report for {{ $yearFilter }} (Monthly Breakdown)</h3>
                <p class="text-xs text-slate-500">Total visitor traffic aggregated month by month.</p>
                <div class="h-56 flex items-end gap-2 pt-8 pb-3 border-b border-slate-200 overflow-x-auto relative">
                    @php
                        $maxMonthCount = max(array_merge(array_values($yearlyReportData), [1]));
                        $monthNames = ['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
                    @endphp
                    @foreach ($yearlyReportData as $mNum => $count)
                        @php
                            $heightPercent = ($count / max($maxMonthCount, 1)) * 100;
                            $mLabel = $monthNames[$mNum];
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[36px]" title="{{ $mLabel }} {{ $yearFilter }}: {{ $count }} pax">
                            <div class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full bg-indigo-500 group-hover:bg-indigo-600 rounded-t-md transition-all cursor-pointer" style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[10px] font-semibold text-slate-500 mt-2">{{ $mLabel }}</span>
                            <span class="text-[10px] font-extrabold text-slate-700 mt-0.5">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- WAITLIST DATABASE TABLE VIEW -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">Waitlist Records Database</h3>
                    <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full">{{ count($waitlists) }} Records</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-900 font-semibold border-b border-slate-200">
                                <th class="p-4">ID</th>
                                <th class="p-4">Customer Name</th>
                                <th class="p-4">Phone Number</th>
                                <th class="p-4">Pax</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Created At</th>
                                @if (auth()->check() && auth()->user()->role === 'admin')
                                    <th class="p-4">Made By</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($waitlists as $w)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4 text-slate-900">#{{ $w->id }}</td>
                                    <td class="p-4 text-slate-900">{{ $w->customer_name ?? 'Walk-in Guest' }}</td>
                                    <td class="p-4 text-slate-900">{{ $w->phone ?? '-' }}</td>
                                    <td class="p-4 text-slate-900">{{ $w->pax }} Persons</td>
                                    <td class="p-4 text-slate-900 uppercase text-xs">{{ ucfirst($w->status) }}</td>
                                    <td class="p-4 text-slate-900">{{ $w->created_at ? $w->created_at->format('d M Y, H:i:s') : '-' }}</td>
                                    @if (auth()->check() && auth()->user()->role === 'admin')
                                        <td class="p-4 text-slate-900">{{ $w->created_by ?? 'System' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->check() && auth()->user()->role === 'admin' ? 7 : 6 }}" class="text-center py-12 text-slate-400">
                                        No waitlist records found in the database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Visitor Log Entries -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-base">Visitor Log Entries & Dining Durations</h3>
                <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full">{{ count($logs) }} Records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-900 font-semibold border-b border-slate-200">
                            <th class="p-4">Log ID</th>
                            <th class="p-4">Customer Name</th>
                            <th class="p-4">Phone Number</th>
                            <th class="p-4">Visitor Pax</th>
                            <th class="p-4">Started Session</th>
                            <th class="p-4">Ended Session</th>
                            @if (auth()->check() && auth()->user()->role === 'admin')
                                <th class="p-4">Time Elapsed</th>
                                <th class="p-4">Made By</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 text-slate-900">#{{ $log->id }}</td>
                                <td class="p-4 text-slate-900">{{ $log->customer_name ?? 'Walk-in Guest' }}</td>
                                <td class="p-4 text-slate-900">{{ $log->phone ?? '-' }}</td>
                                <td class="p-4 text-slate-900">{{ $log->pax }} Persons</td>
                                <td class="p-4 text-slate-900">{{ $log->started_at ? \Carbon\Carbon::parse($log->started_at)->format('d M Y, H:i:s') : '-' }}</td>
                                <td class="p-4 text-slate-900">{{ $log->ended_at ? \Carbon\Carbon::parse($log->ended_at)->format('d M Y, H:i:s') : 'In Progress' }}</td>
                                @if (auth()->check() && auth()->user()->role === 'admin')
                                    <td class="p-4 text-slate-900">{{ $log->time_elapsed ?? '-' }}</td>
                                    <td class="p-4 text-slate-900">{{ $log->created_by ?? '-' }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && auth()->user()->role === 'admin' ? 8 : 6 }}" class="text-center py-12 text-slate-400">
                                    No visitor records found for this filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</x-app-layout>