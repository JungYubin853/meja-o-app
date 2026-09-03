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
        $viewMode = $request->input('view', 'waitlist'); // waitlist, hourly, daily, monthly
        $dateFilter = $request->input('date', now()->format('Y-m-d'));
        $monthFilter = $request->input('month', now()->format('Y-m'));
        $yearFilter = $request->input('year', now()->format('Y'));

        // --- HOURLY DATA ---
        $logs = VisitorLog::whereDate('started_at', $dateFilter)->orderBy('started_at', 'desc')->get();
        $dailyTotal = $logs->sum('pax');

        $parsedDate = Carbon::parse($dateFilter);
        $monthlyTotal = VisitorLog::whereYear('started_at', $parsedDate->year)
            ->whereMonth('started_at', $parsedDate->month)
            ->sum('pax');

        $hourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourlyData[$hour] = VisitorLog::whereDate('started_at', $dateFilter)
                ->whereRaw('HOUR(started_at) = ?', [$hour])
                ->sum('pax');
        }

        // --- DAILY DATA (For selected month) ---
        $parsedMonth = Carbon::parse($monthFilter);
        $daysInMonth = $parsedMonth->daysInMonth;
        $dailyReportData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDayStr = $parsedMonth->copy()->day($day)->format('Y-m-d');
            $dailyReportData[$currentDayStr] = VisitorLog::whereDate('started_at', $currentDayStr)->sum('pax');
        }

        // --- MONTHLY DATA (For selected year - Yearly Report) ---
        $yearlyReportData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
            $yearlyReportData[$monthStr] = VisitorLog::whereYear('started_at', $yearFilter)
                ->whereMonth('started_at', $m)
                ->sum('pax');
        }

        $waitlists = Waitlist::orderBy('created_at', 'desc')->get();

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