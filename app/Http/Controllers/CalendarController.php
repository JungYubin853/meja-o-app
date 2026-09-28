<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

    public function getHolidays(Request $request)
    {
        // FullCalendar sends 'start' and 'end' as ISO8601 strings
        $start = $request->query('start');
        $end = $request->query('end');

        if ($start && $end) {
            $startYear = (int) substr($start, 0, 4);
            $endYear = (int) substr($end, 0, 4);
            $years = array_unique([$startYear, $endYear]);
        } else {
            $years = [date('Y')];
        }

        $events = [];

        foreach ($years as $year) {
            $cacheKey = "holidays_{$year}";
            $data = Cache::remember($cacheKey, 86400, function () use ($year) {
                $response = Http::get("https://tanggalmerah.upset.dev/api/holidays?year={$year}");
                return $response->successful() ? $response->json() : null;
            });

            if ($data && $data['success']) {
                foreach ($data['data'] as $item) {
                    $color = $item['type'] === 'holiday' ? '#ef4444' : '#eab308';
                    $events[] = [
                        'title' => $item['name'],
                        'start' => $item['date'],
                        'allDay' => true,
                        'backgroundColor' => $color,
                        'borderColor' => $color,
                        'type' => $item['type']
                    ];
                }
            }
        }

        return response()->json($events);
    }
}
