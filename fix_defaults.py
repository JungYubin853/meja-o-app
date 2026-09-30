with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

import re
pattern = r"\$habitsData = \[\];\s*\$maxHabitsTime = 0;\s*\$habitsDayOfWeek = \[\];\s*\$maxHabitsDay = 0;\s*\$habitsHourly = \[\];\s*\$maxHabitsHour = 0;"
replacement = """$habitsDateLog = [];
        $habitsHourly = [];
        $habitsWeekly = [];
        $habitsYearly = [];
        $holidayName = null;
        $holidayType = null;"""

content = re.sub(pattern, replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
