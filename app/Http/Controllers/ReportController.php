<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waitlist;
use App\Models\VisitorLog;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $viewMode     = $request->input('view', 'waitlist');
        $dateFilter   = $request->input('date', now()->format('Y-m-d'));
        $monthFilter  = $request->input('month', now()->format('Y-m'));
        $yearFilter   = $request->input('year', now()->format('Y'));

        $parsedMonth  = Carbon::parse($monthFilter);

        // Fetch logs and waitlists for the selected date
        $waitlists = Waitlist::whereDate('created_at', $dateFilter)->latest()->get();
        $logs      = VisitorLog::whereDate('started_at', $dateFilter)->latest('started_at')->get();

        // Totals
        $dailyTotal   = VisitorLog::whereDate('started_at', $dateFilter)->sum('pax');
        $monthlyTotal = VisitorLog::whereYear('started_at', $parsedMonth->year)
            ->whereMonth('started_at', $parsedMonth->month)
            ->sum('pax');

        // Hourly Breakdown for Selected Date
        $hourlyData = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyData[$h] = VisitorLog::whereDate('started_at', $dateFilter)
                ->whereRaw('HOUR(started_at) = ?', [$h])
                ->sum('pax');
        }

        // Daily Breakdown for Selected Month
        $dailyReportData = [];
        for ($d = 1; $d <= $parsedMonth->daysInMonth; $d++) {
            $currDate = $monthFilter . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
            $dailyReportData[$currDate] = VisitorLog::whereDate('started_at', $currDate)->sum('pax');
        }

        // Monthly Breakdown for Selected Year
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