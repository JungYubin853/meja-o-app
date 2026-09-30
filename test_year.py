with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Update headerToolbar to include multiMonthYear
content = content.replace("right: 'dayGridMonth,timeGridWeek'", "right: 'multiMonthYear,dayGridMonth,timeGridWeek'")

# Update buttonText for cleaner labels
buttonTextConfig = """
                buttonText: {
                    multiMonthYear: 'Year',
                    dayGridMonth: 'Month',
                    timeGridWeek: 'Week'
                },"""
content = re.sub(r'(initialView:.*?,)', r'\1' + buttonTextConfig, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
