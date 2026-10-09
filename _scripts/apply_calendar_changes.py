with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Change initial view
content = content.replace("initialView: 'dayGridMonth',", "initialView: 'multiMonthYear',")

# Change 4 columns to 6 columns, adjust minWidth
content = content.replace("multiMonthMaxColumns: 4,", "multiMonthMaxColumns: 6,")
content = content.replace("multiMonthMinWidth: 200,", "multiMonthMinWidth: 120,")

# Add view-specific configuration and navLinkDayClick
view_config = """
                  navLinkDayClick: 'timeGridWeek', // clicking a date goes to week view
                  views: {
                      multiMonthYear: {
                          dayHeaderFormat: { weekday: 'narrow' } // S, M, T, W, T, F, S
                      }
                  },"""

# Insert view_config after navLinks: true
content = re.sub(r"(navLinks: true,.*?\n)", r"\1" + view_config + "\n", content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
