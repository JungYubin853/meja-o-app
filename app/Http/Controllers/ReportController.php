<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waitlist;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('nav_reports')) {
            abort(403);
        }

        $viewMode = $request->input('view');

        // If no view specified, pick highest allowed default
        if (!$viewMode) {
            if ($user->hasPermission('rep_overall')) {
                $viewMode = 'overall';
            } elseif ($user->hasPermission('rep_daily')) {
                $viewMode = 'hourly';
            } elseif ($user->hasPermission('rep_habits')) {
                $viewMode = 'habits';
            } elseif ($user->hasPermission('rep_waitlist')) {
                $viewMode = 'waitlist';
            } else {
                abort(403, 'No report views available.');
            }
        }

        // Enforce permissions for the chosen viewMode
        $permissionMap = [
            'overall'  => 'rep_overall',
            'hourly'   => 'rep_daily',
            'daily'    => 'rep_monthly',
            'monthly'  => 'rep_yearly',
            'habits'   => 'rep_habits',
            'waitlist' => 'rep_waitlist'
        ];

        if (isset($permissionMap[$viewMode]) && !$user->hasPermission($permissionMap[$viewMode])) {
            // Redirect to a safe allowed view if they typed a URL they lack permission for
            if ($user->hasPermission('rep_daily')) {
                return redirect('/database?view=hourly');
            } elseif ($user->hasPermission('rep_habits')) {
                $viewMode = 'habits';
            } elseif ($user->hasPermission('rep_waitlist')) {
                return redirect('/database?view=waitlist');
            }
            abort(403);
        }

        $dateFilter   = $request->input('date', now()->format('Y-m-d'));
        $monthFilter  = $request->input('month', now()->format('Y-m'));
        $yearFilter   = $request->input('year', now()->format('Y'));

        $parsedMonth  = Carbon::parse($monthFilter);

        $outletId = $user ? $user->outlet_id : null;
        $selectedOutletId = $outletId;
        if ($user && $user->isSuperAdmin()) {
            $selectedOutletId = $request->input('outlet_id') ?: (\App\Models\Outlet::first()->id ?? null);
        }
        $outlets = \App\Models\Outlet::all();

        // 2. Filter Visitor Logs dynamically based on active tab view
        $baseLogQuery = VisitorLog::query();
        if ($selectedOutletId) {
            $baseLogQuery->where('outlet_id', $selectedOutletId);
        }

        $logQuery = clone $baseLogQuery;

        if ($viewMode === 'hourly') {
            $logQuery->whereDate('started_at', $dateFilter);
        } elseif ($viewMode === 'daily') {
            $logQuery->whereYear('started_at', $parsedMonth->year)
                     ->whereMonth('started_at', $parsedMonth->month);
        } elseif ($viewMode === 'monthly') {
            $logQuery->whereYear('started_at', $yearFilter);
        } else {
            // Waitlist view: show today's logs by default
            $logQuery->whereDate('started_at', $dateFilter);
        }

        $logs = $logQuery->latest('started_at')->get();

        // 3. Waitlist Query
        $waitlistQuery = Waitlist::whereDate('created_at', $dateFilter);
        if ($selectedOutletId) {
            $waitlistQuery->where('outlet_id', $selectedOutletId);
        }
        $waitlists = $waitlistQuery->latest()->get();

        // 4. Totals for Cards
        $overallStats = [];
        if ($viewMode === 'overall') {
            $dateParsed = Carbon::parse($dateFilter);
            
            $targetOutlets = ($user && $user->isSuperAdmin()) ? \App\Models\Outlet::all() : \App\Models\Outlet::where('id', $outletId)->get();
            
            foreach ($targetOutlets as $out) {
                $overallStats[] = [
                    'outlet' => $out,
                    'daily' => VisitorLog::where('outlet_id', $out->id)->whereDate('started_at', $dateFilter)->sum('pax'),
                    'monthly' => VisitorLog::where('outlet_id', $out->id)
                                ->whereYear('started_at', $dateParsed->year)
                                ->whereMonth('started_at', $dateParsed->month)->sum('pax'),
                    'yearly' => VisitorLog::where('outlet_id', $out->id)
                                ->whereYear('started_at', $dateParsed->year)->sum('pax'),
                ];
            }
        }

        if ($viewMode === 'hourly') {
            $parsedDate   = Carbon::parse($dateFilter);
            $dailyTotal   = (clone $baseLogQuery)->whereDate('started_at', $dateFilter)->sum('pax');
            $monthlyTotal = (clone $baseLogQuery)->whereYear('started_at', $parsedDate->year)
                ->whereMonth('started_at', $parsedDate->month)
                ->sum('pax');
            $yearlyTotal  = (clone $baseLogQuery)->whereYear('started_at', $parsedDate->year)->sum('pax');
        } elseif ($viewMode === 'daily') {
            $dailyTotal   = (clone $baseLogQuery)->whereDate('started_at', $dateFilter)->sum('pax');
            $monthlyTotal = (clone $baseLogQuery)->whereYear('started_at', $parsedMonth->year)
                ->whereMonth('started_at', $parsedMonth->month)
                ->sum('pax');
            $yearlyTotal  = (clone $baseLogQuery)->whereYear('started_at', $parsedMonth->year)->sum('pax');
        } else {
            $dailyTotal   = (clone $baseLogQuery)->whereDate('started_at', $dateFilter)->sum('pax');
            $monthlyTotal = (clone $baseLogQuery)->whereYear('started_at', $parsedMonth->year)
                ->whereMonth('started_at', $parsedMonth->month)
                ->sum('pax');
            $yearlyTotal  = (clone $baseLogQuery)->whereYear('started_at', $yearFilter)->sum('pax');
        }

        
        // 4.5 Customer Habits
        $habitsDateLog = [];
        $habitsHourly = [];
        $habitsWeekly = [];
        $habitsYearly = [];
        $holidayName = null;
        $holidayType = null;
        
        if ($viewMode === 'habits') {
            // Default to first outlet if none selected
            if (!$selectedOutletId) {
                $firstOutlet = \App\Models\Outlet::orderBy('name')->first();
                $selectedOutletId = $firstOutlet ? $firstOutlet->id : null;
            }

            $dateParsed = \Carbon\Carbon::parse($dateFilter);
            $year = $dateParsed->year;

            // 1. Fetch Holiday Data
            $cacheKey = "holidays_{$year}";
            $holidaysData = Cache::remember($cacheKey, 86400, function () use ($year) {
                $response = \Illuminate\Support\Facades\Http::get("https://tanggalmerah.upset.dev/api/holidays?year={$year}");
                return $response->successful() ? $response->json() : null;
            });
            
            if ($holidaysData && $holidaysData['success']) {
                foreach ($holidaysData['data'] as $item) {
                    if ($item['date'] === $dateFilter) {
                        $holidayName = $item['name'];
                        // the API usually doesn't have type 'collective_leave' but lets assume if it contains 'Cuti Bersama'
                        $holidayType = (stripos($item['name'], 'Cuti Bersama') !== false) ? 'Collective Leave' : 'National Holiday';
                        break;
                    }
                }
            }

            // 2. Fetch all completed logs for the selected outlet for the given year
            $yearLogs = VisitorLog::with('table')->where('outlet_id', $selectedOutletId)
                                  ->whereYear('started_at', $year)
                                  ->whereNotNull('ended_at')
                                  ->get();

            // 3. Process logs
            $hourlyTracker = array_fill(0, 24, ['total' => 0, 'count' => 0]);
            $weeklyTracker = [
                'Monday' => ['total' => 0, 'count' => 0],
                'Tuesday' => ['total' => 0, 'count' => 0],
                'Wednesday' => ['total' => 0, 'count' => 0],
                'Thursday' => ['total' => 0, 'count' => 0],
                'Friday' => ['total' => 0, 'count' => 0],
                'Saturday' => ['total' => 0, 'count' => 0],
                'Sunday' => ['total' => 0, 'count' => 0],
            ];
            $yearlyTracker = array_fill(1, 12, ['total' => 0, 'count' => 0]);

            foreach ($yearLogs as $log) {
                $start = \Carbon\Carbon::parse($log->started_at);
                $end = \Carbon\Carbon::parse($log->ended_at);
                $diffInMinutes = $start->diffInMinutes($end);
                
                // Yearly
                $yearlyTracker[$start->month]['total'] += $diffInMinutes;
                $yearlyTracker[$start->month]['count']++;

                // Weekly
                $dayName = $start->format('l');
                $weeklyTracker[$dayName]['total'] += $diffInMinutes;
                $weeklyTracker[$dayName]['count']++;

                // If log is from the SPECIFIC date
                if ($start->format('Y-m-d') === $dateFilter) {
                    $habitsDateLog[] = $log; // For the complete visit log timeline
                    
                    $hour = (int) $start->format('H');
                    $hourlyTracker[$hour]['total'] += $diffInMinutes;
                    $hourlyTracker[$hour]['count']++;
                }
            }

            // 4. Calculate Averages
            for ($i = 0; $i < 24; $i++) {
                $habitsHourly[str_pad($i, 2, '0', STR_PAD_LEFT)] = $hourlyTracker[$i]['count'] > 0 ? round($hourlyTracker[$i]['total'] / $hourlyTracker[$i]['count']) : 0;
            }
            foreach ($weeklyTracker as $day => $data) {
                $habitsWeekly[$day] = $data['count'] > 0 ? round($data['total'] / $data['count']) : 0;
            }
            for ($i = 1; $i <= 12; $i++) {
                $monthName = \Carbon\Carbon::create()->month($i)->format('M');
                $habitsYearly[$monthName] = $yearlyTracker[$i]['count'] > 0 ? round($yearlyTracker[$i]['total'] / $yearlyTracker[$i]['count']) : 0;
            }
        }

        // 5. Initialize chart containers
        $hourlyData       = [];
        $dailyReportData  = [];
        $yearlyReportData = [];

        // 6. Only calculate the graph data needed for the active view (Performance Boost)
        if ($viewMode === 'hourly') {
            for ($h = 0; $h < 24; $h++) {
                $hourlyData[$h] = (int) (clone $baseLogQuery)->whereDate('started_at', $dateFilter)
                    ->whereRaw('HOUR(started_at) = ?', [$h])
                    ->sum('pax');
            }
        } elseif ($viewMode === 'daily') {
            for ($d = 1; $d <= $parsedMonth->daysInMonth; $d++) {
                $currDate = $monthFilter . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                $dailyReportData[$currDate] = (int) (clone $baseLogQuery)->whereDate('started_at', $currDate)->sum('pax');
            }
        } elseif ($viewMode === 'monthly') {
            for ($m = 1; $m <= 12; $m++) {
                $mStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                $yearlyReportData[$mStr] = (int) (clone $baseLogQuery)->whereYear('started_at', $yearFilter)
                    ->whereMonth('started_at', $m)
                    ->sum('pax');
            }
        }

        return view('database', compact(
            'logs',
            'dailyTotal',
            'monthlyTotal',
            'yearlyTotal',
            'hourlyData',
            'dateFilter',
            'monthFilter',
            'yearFilter',
            'viewMode',
            'dailyReportData',
            'yearlyReportData',
            'habitsDateLog', 'habitsHourly', 'habitsWeekly', 'habitsYearly', 'holidayName', 'holidayType',
            'waitlists',
            'overallStats',
            'outlets',
            'selectedOutletId'
        ));
    }

    /**
     * Export Daily, Monthly, or Yearly report to Excel (CSV) or printable PDF
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('nav_reports')) {
            abort(403);
        }

        $type  = $request->query('type', 'excel'); // 'excel' or 'pdf'
        $view  = $request->query('view', 'hourly');
        
        $permissionMap = [
            'overall'  => 'rep_overall',
            'hourly'   => 'rep_daily',
            'daily'    => 'rep_monthly',
            'monthly'  => 'rep_yearly',
            'habits'   => 'rep_habits',
            'waitlist' => 'rep_waitlist'
        ];

        if (isset($permissionMap[$view]) && !$user->hasPermission($permissionMap[$view])) {
            abort(403, 'Unauthorized export request.');
        }

        $date  = $request->query('date', now()->format('Y-m-d'));
        $month = $request->query('month', now()->format('Y-m'));
        $year  = $request->query('year', now()->format('Y'));

        $outletId = $user ? $user->outlet_id : null;
        $query = VisitorLog::query();
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        if ($view === 'hourly') {
            $query->whereDate('started_at', $date);
            $filename = "Report-Daily-{$date}";
            $reportTitle = "Daily Visitor Report ({$date})";
        } elseif ($view === 'daily') {
            $parsedMonth = Carbon::parse($month);
            $query->whereYear('started_at', $parsedMonth->year)
                  ->whereMonth('started_at', $parsedMonth->month);
            $filename = "Report-Monthly-{$month}";
            $reportTitle = "Monthly Visitor Report ({$month})";
        } elseif ($view === 'monthly') {
            $query->whereYear('started_at', $year);
            $filename = "Report-Yearly-{$year}";
            $reportTitle = "Yearly Visitor Report ({$year})";
        } else {
            $filename = "Report-Database";
            $reportTitle = "Visitor Database Report";
        }

        $logs = $query->orderBy('started_at', 'asc')->get();

        // 1. EXCEL EXPORT (CSV format natively opened by Microsoft Excel)
        if ($type === 'excel') {
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0'
            ];

            $callback = function () use ($logs) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM so Excel displays characters correctly
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, ['Log ID', 'Customer Name', 'Phone', 'Pax', 'Session Started', 'Session Ended', 'Time Elapsed', 'Created By']);

                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->customer_name ?? 'Walk-in Guest',
                        $log->phone ?? '-',
                        $log->pax,
                        $log->started_at ? Carbon::parse($log->started_at)->format('d-m-Y H:i:s') : '-',
                        $log->ended_at ? Carbon::parse($log->ended_at)->format('d-m-Y H:i:s') : 'In Progress',
                        $log->time_elapsed ?? '-',
                        $log->created_by ?? 'System'
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // 2. PDF / PRINT VIEW
        return view('reports.export-pdf', compact('logs', 'view', 'date', 'month', 'year', 'filename', 'reportTitle'));
    }
}