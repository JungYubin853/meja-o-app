with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

import re
pattern = r"'habitsData',\s*'maxHabitsTime',\s*'habitsDayOfWeek',\s*'maxHabitsDay',\s*'habitsHourly',\s*'maxHabitsHour',"
replacement = "'habitsDateLog', 'habitsHourly', 'habitsWeekly', 'habitsYearly', 'holidayName', 'holidayType',"
content = re.sub(pattern, replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
