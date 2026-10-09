with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Add the CSS for background hack
css_addition = """
        /* Force events to act as backgrounds in multiMonthYear view */
        .fc-multimonth-daygrid .fc-daygrid-day-frame {
            position: relative;
        }
        .fc-multimonth-daygrid .fc-daygrid-event-harness {
            position: absolute !important;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
        }
        .fc-multimonth-daygrid .fc-daygrid-event {
            display: block !important;
            width: 100%;
            height: 100%;
            border-radius: 0 !important;
            opacity: 0.25; /* Light color */
            background-color: var(--fc-event-bg-color, #3788d8) !important;
        }
        .fc-multimonth-daygrid .fc-daygrid-event-dot {
            display: none !important;
        }
        .fc-multimonth-daygrid .fc-event-title {
            display: none !important;
        }
        /* Completely hide any +1 more links */
        .fc-multimonth-daygrid .fc-more-link {
            display: none !important;
        }
        .fc-multimonth-daygrid .fc-daygrid-more-link {
            display: none !important;
        }
        /* Ensure the day number stays on top */
        .fc-multimonth-daygrid .fc-daygrid-day-top {
            position: relative;
            z-index: 10;
        }
"""
content = content.replace("</style>", css_addition + "</style>")

# Ensure dayMaxEvents is false globally for multiMonthYear
# Actually, if we hide .fc-more-link it doesn't matter, but let's just make sure.

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
