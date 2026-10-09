with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will completely replace the $viewMode === 'habits' logic again!
# First let's find the start of the block.
start_marker = "if ($viewMode === 'habits') {"
end_marker = "// 5. Initialize chart containers"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

new_logic = """if ($viewMode === 'habits') {
            $allOutlets = \\App\\Models\\Outlet::orderBy('name')->get();
            
            // To normalize all mini-charts
            $globalMaxWeekly = 0;
            $globalMaxHourly = 0;
            
            foreach ($allOutlets as $outlet) {
                $logs = VisitorLog::where('outlet_id', $outlet->id)->whereNotNull('ended_at')->get();
                $totalMinutes = 0;
                
                $outletWeeklyTracker = [
                    'Mon' => ['total' => 0, 'count' => 0],
                    'Tue' => ['total' => 0, 'count' => 0],
                    'Wed' => ['total' => 0, 'count' => 0],
                    'Thu' => ['total' => 0, 'count' => 0],
                    'Fri' => ['total' => 0, 'count' => 0],
                    'Sat' => ['total' => 0, 'count' => 0],
                    'Sun' => ['total' => 0, 'count' => 0],
                ];
                $outletHourlyTracker = [];
                for ($i=0; $i<24; $i++) {
                    $outletHourlyTracker[str_pad($i, 2, '0', STR_PAD_LEFT)] = ['total' => 0, 'count' => 0];
                }
                
                foreach ($logs as $log) {
                    $start = \\Carbon\\Carbon::parse($log->started_at);
                    $end = \\Carbon\\Carbon::parse($log->ended_at);
                    $diffInMinutes = $start->diffInMinutes($end);
                    
                    $totalMinutes += $diffInMinutes;
                    
                    $dayStr = substr($start->format('l'), 0, 3);
                    $hourStr = $start->format('H');
                    
                    $outletWeeklyTracker[$dayStr]['total'] += $diffInMinutes;
                    $outletWeeklyTracker[$dayStr]['count']++;
                    
                    $outletHourlyTracker[$hourStr]['total'] += $diffInMinutes;
                    $outletHourlyTracker[$hourStr]['count']++;
                }
                
                $avg = $logs->count() > 0 ? round($totalMinutes / $logs->count()) : 0;
                if ($avg > $maxHabitsTime) {
                    $maxHabitsTime = $avg;
                }
                
                $weeklyAvg = [];
                foreach ($outletWeeklyTracker as $day => $d) {
                    $wAvg = $d['count'] > 0 ? round($d['total'] / $d['count']) : 0;
                    $weeklyAvg[$day] = $wAvg;
                    if ($wAvg > $globalMaxWeekly) $globalMaxWeekly = $wAvg;
                }
                
                $hourlyAvg = [];
                foreach ($outletHourlyTracker as $hour => $d) {
                    $hAvg = $d['count'] > 0 ? round($d['total'] / $d['count']) : 0;
                    $hourlyAvg[$hour] = $hAvg;
                    if ($hAvg > $globalMaxHourly) $globalMaxHourly = $hAvg;
                }
                
                $habitsData[] = [
                    'outlet' => $outlet->name,
                    'avg_minutes' => $avg,
                    'weekly' => $weeklyAvg,
                    'hourly' => $hourlyAvg
                ];
            }
            
            $maxHabitsDay = $globalMaxWeekly;
            $maxHabitsHour = $globalMaxHourly;
        }

        """

content = content[:start_idx] + new_logic + content[end_idx:]

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
