with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Remove my CSS hack completely
css_to_remove = """        /* Force events to act as backgrounds in multiMonthYear view */
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
        }"""
content = content.replace(css_to_remove, """
        /* Hide the +1 more links completely so they don't cause clutter */
        .fc-multimonth-daygrid .fc-more-link { display: none !important; }
        .fc-multimonth-daygrid .fc-daygrid-more-link { display: none !important; }
        /* Hide the default dots in year view because we will color the cell background directly */
        .fc-multimonth-daygrid .fc-daygrid-event-dot { display: none !important; }
        .fc-multimonth-daygrid .fc-event-title { display: none !important; }
""")

# Now add eventDidMount hook to color the TD
event_did_mount = """
                  eventDidMount: function(info) {
                      info.el.title = info.event.title;
                      
                      // For year view, color the entire cell background
                      if (info.view.type === 'multiMonthYear') {
                          var cell = info.el.closest('.fc-day');
                          if (cell) {
                              var type = info.event.extendedProps.type;
                              if (type === 'holiday') {
                                  cell.style.backgroundColor = '#fee2e2'; // Light red
                              } else {
                                  cell.style.backgroundColor = '#fef08a'; // Light yellow
                              }
                          }
                      }
                  }"""

# Find the old eventTimeFormat and replace/insert eventDidMount
content = re.sub(r'eventTimeFormat: \{.*?\}(,)?', r'eventTimeFormat: { hour: "numeric", minute: "2-digit", meridiem: "short" },' + event_did_mount, content, flags=re.DOTALL)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
