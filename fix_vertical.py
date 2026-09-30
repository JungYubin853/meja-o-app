with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Remove min-h-[600px] to allow it to perfectly fit without scroll
content = content.replace('class="flex-1 min-h-[600px]"', 'class="flex-1 min-h-0"')

# Reduce padding on year view day headers to save vertical space
css_addition = """
        /* Reduce padding in multi-month year view day headers to prevent scrolling */
        .fc-multimonth-daygrid .fc-col-header-cell {
            padding: 4px 0 !important;
            font-size: 0.75rem;
        }
        .fc-multimonth-daygrid .fc-daygrid-day-number {
            padding: 4px !important;
            font-size: 0.75rem;
        }
        .fc .fc-multimonth-title {
            padding: 8px 0;
            font-size: 1rem;
        }
"""
content = content.replace("</style>", css_addition + "</style>")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
