with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Extract everything before eventTimeFormat
start = content.split('eventTimeFormat: { hour: "numeric", minute: "2-digit", meridiem: "short" },')[0]
end = """eventTimeFormat: { hour: "numeric", minute: "2-digit", meridiem: "short" },
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
                  }
              });
              calendar.render();
          });
      </script>
  </x-app-layout>"""

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(start + end)
print("Done")
