<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waitlist; // Ensure this is imported
use App\Models\VisitorLog;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->input('view', 'waitlist');
        $dateFilter = $request->input('date', now()->format('Y-m-d'));
        $monthFilter = $request->input('month', now()->format('Y-m'));
        $yearFilter = $request->input('year', now()->format('Y'));

        // Filter waitlists by selected date
        $waitlists = Waitlist::whereDate('created_at', $dateFilter)
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter visitor logs by selected date
        $logs = VisitorLog::whereDate('started_at', $dateFilter)
            ->orderBy('started_at', 'desc')
            ->get();

        // Calculations for charts/totals...
        $dailyTotal = VisitorLog::whereDate('started_at', $dateFilter)->sum('pax');
        $monthlyTotal = VisitorLog::whereMonth('started_at', \Carbon\Carbon::parse($monthFilter)->month)
            ->whereYear('started_at', \Carbon\Carbon::parse($monthFilter)->year)
            ->sum('pax');

        $hourlyData = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyData[$h] = VisitorLog::whereDate('started_at', $dateFilter)
                ->whereRaw('HOUR(started_at) = ?', [$h])
                ->sum('pax');
        }

        $dailyReportData = [];
        $daysInMonth = \Carbon\Carbon::parse($monthFilter)->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currDate = $monthFilter . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
            $dailyReportData[$currDate] = VisitorLog::whereDate('started_at', $currDate)->sum('pax');
        }

        $yearlyReportData = [];
        for ($m = 1; $m <= 12; $m++) {
            $mStr = str_pad($m, 2, '0', STR_PAD_LEFT);
            $yearlyReportData[$mStr] = VisitorLog::whereYear('started_at', $yearFilter)
                ->whereMonth('started_at', $m)
                ->sum('pax');
        }

        return view('database', compact(
            'logs',
            'dailyTotal',
            'monthlyTotal',
            'hourlyData',
            'dateFilter',
            'monthFilter',
            'yearFilter',
            'viewMode',
            'dailyReportData',
            'yearlyReportData',
            'waitlists'
        ));
    }
}