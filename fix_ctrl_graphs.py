with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will inject the initialization of the new arrays right before `foreach ($allOutlets as $outlet) {`
init_logic = """$dayOfWeekData = [
                'Monday' => ['total' => 0, 'count' => 0],
                'Tuesday' => ['total' => 0, 'count' => 0],
                'Wednesday' => ['total' => 0, 'count' => 0],
                'Thursday' => ['total' => 0, 'count' => 0],
                'Friday' => ['total' => 0, 'count' => 0],
                'Saturday' => ['total' => 0, 'count' => 0],
                'Sunday' => ['total' => 0, 'count' => 0],
            ];
            $hourlyDataTracker = [];
            for ($i=0; $i<24; $i++) {
                $hourlyDataTracker[str_pad($i, 2, '0', STR_PAD_LEFT)] = ['total' => 0, 'count' => 0];
            }
            """
content = content.replace("foreach ($allOutlets as $outlet) {", init_logic + "\n            foreach ($allOutlets as $outlet) {")


# We will inject the accumulation logic inside the log loop
accumulation = """$totalMinutes += $start->diffInMinutes($end);
                    
                    $dayStr = $start->format('l');
                    $hourStr = $start->format('H');
                    
                    $dayOfWeekData[$dayStr]['total'] += $start->diffInMinutes($end);
                    $dayOfWeekData[$dayStr]['count']++;
                    
                    $hourlyDataTracker[$hourStr]['total'] += $start->diffInMinutes($end);
                    $hourlyDataTracker[$hourStr]['count']++;
"""
content = content.replace("$totalMinutes += $start->diffInMinutes($end);", accumulation)


# We will finalize the averages after the outlet loop
finalization = """
            $habitsDayOfWeek = [];
            $maxHabitsDay = 0;
            foreach ($dayOfWeekData as $day => $d) {
                $avg = $d['count'] > 0 ? round($d['total'] / $d['count']) : 0;
                $habitsDayOfWeek[$day] = $avg;
                if ($avg > $maxHabitsDay) $maxHabitsDay = $avg;
            }

            $habitsHourly = [];
            $maxHabitsHour = 0;
            foreach ($hourlyDataTracker as $hour => $d) {
                $avg = $d['count'] > 0 ? round($d['total'] / $d['count']) : 0;
                $habitsHourly[$hour] = $avg;
                if ($avg > $maxHabitsHour) $maxHabitsHour = $avg;
            }
        }
"""
content = content.replace("        }\n\n        // 5. Initialize chart containers", finalization + "\n        // 5. Initialize chart containers")

# Pass the variables to view
compact_replacement = """'habitsData',
            'maxHabitsTime',
            'habitsDayOfWeek',
            'maxHabitsDay',
            'habitsHourly',
            'maxHabitsHour',"""
content = content.replace("'habitsData',\n            'maxHabitsTime',", compact_replacement)


with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
