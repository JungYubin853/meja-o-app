with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Insert multiMonthMaxColumns: 4 and multiMonthMinWidth: 200 to force 4x3 grid
replacement = """
                multiMonthMaxColumns: 4,
                multiMonthMinWidth: 200,
                events: '/api/calendar/holidays',"""

content = re.sub(r"events: '/api/calendar/holidays',", replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
