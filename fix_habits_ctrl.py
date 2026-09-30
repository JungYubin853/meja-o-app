with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

start_marker = "$habitsData = [];"
end_marker = "// 5. Initialize chart containers"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

new_habits_logic = """$habitsData = [];
        $maxHabitsTime = 0;
        
        if ($viewMode === 'habits') {
            $allOutlets = \\App\\Models\\Outlet::orderBy('name')->get();
            foreach ($allOutlets as $outlet) {
                $logs = VisitorLog::where('outlet_id', $outlet->id)->whereNotNull('ended_at')->get();
                $totalMinutes = 0;
                foreach ($logs as $log) {
                    $start = \\Carbon\\Carbon::parse($log->started_at);
                    $end = \\Carbon\\Carbon::parse($log->ended_at);
                    $totalMinutes += $start->diffInMinutes($end);
                }
                $avg = $logs->count() > 0 ? round($totalMinutes / $logs->count()) : 0;
                
                $habitsData[] = [
                    'outlet' => $outlet->name,
                    'avg_minutes' => $avg
                ];
                
                if ($avg > $maxHabitsTime) {
                    $maxHabitsTime = $avg;
                }
            }
        }

        """

content = content[:start_idx] + new_habits_logic + content[end_idx:]
content = content.replace("'weekdayWeekendData',", "'maxHabitsTime',")

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
