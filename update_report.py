with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Add imports
imports = """use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;"""
content = content.replace("use Illuminate\Support\Facades\Auth;", imports)

# Add fallback view mode for habits
content = content.replace("} elseif ($user->hasPermission('rep_waitlist')) {", "} elseif ($user->hasPermission('rep_habits')) {\n                $viewMode = 'habits';\n            } elseif ($user->hasPermission('rep_waitlist')) {")

# Add habits logic inside index()
habits_logic = """
        // 4.5 Customer Habits
        $habitsData = [];
        $weekdayWeekendData = [];
        
        if ($viewMode === 'habits') {
            $completedLogs = (clone $baseLogQuery)->whereNotNull('ended_at')->get();
            
            $dayOfWeekAvg = [
                'Monday' => ['total' => 0, 'count' => 0],
                'Tuesday' => ['total' => 0, 'count' => 0],
                'Wednesday' => ['total' => 0, 'count' => 0],
                'Thursday' => ['total' => 0, 'count' => 0],
                'Friday' => ['total' => 0, 'count' => 0],
                'Saturday' => ['total' => 0, 'count' => 0],
                'Sunday' => ['total' => 0, 'count' => 0],
            ];
            
            $weekdayCount = 0;
            $weekendHolidayCount = 0;

            $year = date('Y');
            $cacheKey = "holidays_{$year}";
            $holidaysData = Cache::remember($cacheKey, 86400, function () use ($year) {
                $response = Http::get("https://tanggalmerah.upset.dev/api/holidays?year={$year}");
                return $response->successful() ? $response->json() : null;
            });
            $holidayDates = [];
            if ($holidaysData && $holidaysData['success']) {
                foreach ($holidaysData['data'] as $item) {
                    $holidayDates[] = $item['date'];
                }
            }
            
            foreach ($completedLogs as $log) {
                $start = \Carbon\Carbon::parse($log->started_at);
                $end = \Carbon\Carbon::parse($log->ended_at);
                $diffInMinutes = $start->diffInMinutes($end);
                
                $dayName = $start->format('l'); // Monday, Tuesday...
                $dayOfWeekAvg[$dayName]['total'] += $diffInMinutes;
                $dayOfWeekAvg[$dayName]['count']++;
                
                $dateStr = $start->format('Y-m-d');
                if ($start->isWeekend() || in_array($dateStr, $holidayDates)) {
                    $weekendHolidayCount++;
                } else {
                    $weekdayCount++;
                }
            }
            
            foreach ($dayOfWeekAvg as $day => $data) {
                $habitsData[$day] = $data['count'] > 0 ? round($data['total'] / $data['count']) : 0;
            }
            
            $weekdayWeekendData = [
                'Weekday' => $weekdayCount,
                'Weekend/Holiday' => $weekendHolidayCount
            ];
        }
"""

content = content.replace("// 5. Initialize chart containers", habits_logic + "\n        // 5. Initialize chart containers")

# Pass habitsData and weekdayWeekendData to view
content = content.replace("'yearlyReportData',", "'yearlyReportData',\n            'habitsData',\n            'weekdayWeekendData',")

# Handle export permission for habits (if needed)
content = content.replace("'monthly'  => 'rep_yearly',", "'monthly'  => 'rep_yearly',\n            'habits'   => 'rep_habits',")

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done ReportController")
