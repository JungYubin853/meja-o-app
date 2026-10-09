file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Change firstDay: 1 to firstDay: 0
content = content.replace("firstDay: 1,", "firstDay: 0,")

# 2. Fix the header cell padding/aspect ratio to be a perfect square
css_old = """.fc-multimonth-daygrid .fc-col-header-cell {
            padding: 4px 0 !important;
            font-size: 0.75rem;
        }"""
css_new = """.fc-multimonth-daygrid .fc-col-header-cell {
            padding: 0 !important;
            font-size: 0.75rem;
            aspect-ratio: 1 / 1;
            vertical-align: middle;
        }
        .fc-multimonth-daygrid .fc-col-header-cell-cushion {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 100% !important;
            width: 100% !important;
        }"""
content = content.replace(css_old, css_new)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated calendar.blade.php!")
