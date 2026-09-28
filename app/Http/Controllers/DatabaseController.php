<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\VisitorLog; // Or your corresponding Log model

class DatabaseController extends Controller
{
    public function export(Request $request)
    {
        $type = $request->query('type', 'excel'); // 'excel' or 'pdf'
        $view = $request->query('view', 'hourly');
        $date = $request->query('date', now()->toDateString());
        $month = $request->query('month', now()->format('Y-m'));
        $year = $request->query('year', now()->year);

        // Fetch logs matching the selected report view
        $query = VisitorLog::query();

        if ($view === 'hourly') {
            $query->whereDate('started_at', $date);
            $filename = "Report-Daily-{$date}";
        } elseif ($view === 'daily') {
            $query->whereYear('started_at', substr($month, 0, 4))
                  ->whereMonth('started_at', substr($month, 5, 2));
            $filename = "Report-Monthly-{$month}";
        } elseif ($view === 'monthly') {
            $query->whereYear('started_at', $year);
            $filename = "Report-Yearly-{$year}";
        } else {
            $filename = "Report-Database";
        }

        $logs = $query->orderBy('started_at', 'asc')->get();

        // 1. EXPORT AS EXCEL (Clean CSV Compatible with Microsoft Excel)
        if ($type === 'excel') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($logs) {
                $file = fopen('php://output', 'w');
                // Output UTF-8 BOM so Excel displays special characters properly
                fputs($file, "\xEF\xBB\xBF");

                // CSV Header Row
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

        // 2. EXPORT AS PRINTABLE PDF VIEW (Or via Barryvdh DomPDF)
        return view('reports.export-pdf', compact('logs', 'view', 'date', 'month', 'year', 'filename'));
    }
}