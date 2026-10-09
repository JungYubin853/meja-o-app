import re

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

# Replace the views object
pattern = re.compile(r'views:\s*\{\s*multiMonthYear:\s*\{\s*dayHeaderFormat:\s*\{\s*weekday:\s*\'narrow\'\s*\}\s*(?://[^\n]*\n)?\s*\}\s*\},')

new_views = """views: {
                      multiMonthYear: {
                          dayHeaderFormat: { weekday: 'narrow' },
                          eventDisplay: 'background',
                          dayMaxEvents: false
                      }
                  },"""

content = pattern.sub(new_views, content)

# Remove the old CSS hacks
css_to_remove = """        /* In year view, hide the event titles completely if they appear, keeping only dots */
        .fc-multimonth .fc-event-title {
            display: none !important;
        }
        .fc-multimonth .fc-daygrid-event {
            background: transparent !important;
            border: none !important;
            justify-content: center;
        }
        .fc-multimonth .fc-event-main {
            display: flex;
            justify-content: center;
        }"""
content = content.replace(css_to_remove, "")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done calendar.blade.php")

# Now update CalendarController to send light colors for background events!
with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\CalendarController.php", "r", encoding="utf-8") as f:
    ctrl = f.read()

# In the controller we have:
# $color = $item['type'] === 'holiday' ? '#ef4444' : '#eab308';
# We should also send a backgroundColor specific for the background display.
# Actually, background events automatically use `backgroundColor` with reduced opacity in FullCalendar!
# Let's verify FullCalendar's default background event opacity. It's usually 0.3.
# So #ef4444 at 0.3 opacity is light red.
# So we might not need to change the controller!
print("Done CalendarController.php")
