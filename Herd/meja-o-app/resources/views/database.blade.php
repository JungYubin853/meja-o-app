<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Visitor Analytics & Database</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen font-sans antialiased" x-data="{ activeTab: '{{ $viewMode }}' }">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">My Kopi-O Group</h1>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium">Table Management System</p>
                </div>
            </div>
            <div>
                <a href="/"
                    class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-xs">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        <!-- Tabbed Navigation Bar (Waitlist First) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="/database?view=waitlist"
                class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ $viewMode === 'waitlist' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                Waitlist
            </a>
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
        </div>

        <div
            class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
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
                <p class="text-xs text-slate-500">Analyze visitor patterns and multi-tier operational trends.</p>
            </div>

            @if ($viewMode === 'hourly')
                <form method="GET" action="/database" class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="hidden" name="view" value="hourly">
                    <input type="date" name="date" value="{{ $dateFilter }}"
                        class="text-xs sm:text-sm p-2 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-xs">Filter</button>
                </form>
            @elseif($viewMode === 'daily')
                <form method="GET" action="/database" class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="hidden" name="view" value="daily">
                    <input type="month" name="month" value="{{ $monthFilter }}"
                        class="text-xs sm:text-sm p-2 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-xs">Filter
                        Month</button>
                </form>
            @elseif($viewMode === 'monthly')
                <form method="GET" action="/database" class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="hidden" name="view" value="monthly">
                    <select name="year"
                        class="text-xs sm:text-sm p-2 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 font-bold">
                        @for ($y = 2024; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-xs">Filter
                        Year</button>
                </form>
            @endif
        </div>

        @if ($viewMode === 'hourly')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Daily
                            Visitors</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $dailyTotal }} <span
                                class="text-xs font-normal text-slate-500">Pax</span></h3>
                        <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Recorded on {{ $dateFilter }}</p>
                    </div>
                </div>
                <div
                    class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Monthly
                            Visitors</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $monthlyTotal }} <span
                                class="text-xs font-normal text-slate-500">Pax</span></h3>
                        <p class="text-[11px] text-amber-600 font-medium mt-0.5">Accumulated for this month</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Customer Traffic Graph (Hourly Visits)</h3>
                <div
                    class="h-52 flex items-end gap-1 sm:gap-2 pt-8 pb-3 border-b border-slate-200 overflow-x-auto relative">
                    @php $maxHourCount = max(array_merge($hourlyData, [1])); @endphp
                    @foreach ($hourlyData as $hour => $count)
                        @php
                            $heightPercent = ($count / max($maxHourCount, 1)) * 100;
                            $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[28px]"
                            title="{{ $formattedHour }} — {{ $count }} visitors">
                            <div
                                class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full bg-amber-500 group-hover:bg-amber-600 rounded-t-md transition-all cursor-pointer"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span
                                class="text-[9px] font-semibold text-slate-500 mt-2">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}h</span>
                            <span class="text-[10px] font-extrabold text-slate-700 mt-0.5">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($viewMode === 'daily')
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
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative min-w-[26px]"
                            title="{{ $dayDate }}: {{ $count }} pax">
                            <div
                                class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full bg-emerald-500 group-hover:bg-emerald-600 rounded-t-md transition-all cursor-pointer"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
                            <span class="text-[9px] font-semibold text-slate-500 mt-2">{{ $dayNum }}</span>
                            <span class="text-[9px] font-extrabold text-slate-700 mt-0.5">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($viewMode === 'monthly')
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Yearly Visitor Report for {{ $yearFilter }} (Monthly
                    Breakdown)</h3>
                <p class="text-xs text-slate-500">Total visitor traffic aggregated month by month.</p>

                <div class="h-56 flex items-end gap-2 pt-8 pb-3 border-b border-slate-200 overflow-x-auto relative">
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
                            <div
                                class="absolute -top-7 bg-slate-900 text-white text-[10px] font-bold py-1 px-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-md pointer-events-none whitespace-nowrap z-10">
                                {{ $count }} pax
                            </div>
                            <div class="w-full bg-indigo-500 group-hover:bg-indigo-600 rounded-t-md transition-all cursor-pointer"
                                style="height: {{ max($heightPercent, 4) }}%;"></div>
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
                    <span
                        class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full">{{ count($waitlists) }}
                        Records</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                                <th class="p-4">ID</th>
                                <th class="p-4">Customer Name</th>
                                <th class="p-4">Phone Number</th>
                                <th class="p-4">Pax</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($waitlists as $w)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4 font-bold text-slate-700">#{{ $w->id }}</td>
                                    <td class="p-4 font-bold text-slate-900">{{ $w->customer_name }}</td>
                                    <td class="p-4 text-slate-600">{{ $w->phone ?? '-' }}</td>
                                    <td class="p-4 font-extrabold text-slate-900">{{ $w->pax }} Persons</td>
                                    <td class="p-4 font-bold uppercase text-xs text-slate-900">
                                        {{ ucfirst($w->status) }}
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        {{ $w->created_at ? $w->created_at->format('d M Y, H:i:s') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-slate-400">
                                        No waitlist records found in the database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-base">Visitor Log Entries & Dining Durations</h3>
                <span
                    class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full">{{ count($logs) }}
                    Records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                            <th class="p-4">Log ID</th>
                            <th class="p-4">Customer Name</th>
                            <th class="p-4">Phone Number</th>
                            <th class="p-4">Visitor Pax</th>
                            <th class="p-4">Started Session</th>
                            <th class="p-4">Ended Session</th>
                            <th class="p-4">Time Elapsed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 font-bold text-slate-700">#{{ $log->id }}</td>
                                <td class="p-4 font-bold text-slate-900">{{ $log->customer_name ?? 'Walk-in Guest' }}
                                </td>
                                <td class="p-4 text-slate-600">{{ $log->phone ?? '-' }}</td>
                                <td class="p-4 font-extrabold text-slate-900">{{ $log->pax }} Persons</td>
                                <td class="p-4 text-slate-600">
                                    {{ $log->started_at ? \Carbon\Carbon::parse($log->started_at)->format('d M Y, H:i:s') : '-' }}
                                </td>
                                <td class="p-4 text-slate-600">
                                    @if ($log->ended_at)
                                        {{ \Carbon\Carbon::parse($log->ended_at)->format('d M Y, H:i:s') }}
                                    @else
                                        In Progress
                                    @endif
                                </td>
                                <td class="p-4 font-bold text-slate-800">
                                    @if ($log->time_elapsed)
                                        <span
                                            class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg">{{ $log->time_elapsed }}</span>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    No visitor records found for this filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>

</html>
