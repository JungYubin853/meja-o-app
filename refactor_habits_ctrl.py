with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

start_idx = content.find("// 4.5 Customer Habits")
end_idx = content.find("// 5. Initialize chart containers")

if start_idx != -1 and end_idx != -1:
    new_logic = """// 4.5 Customer Habits
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
            $yearLogs = VisitorLog::where('outlet_id', $selectedOutletId)
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

        """
    content = content[:start_idx] + new_logic + content[end_idx:]

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
