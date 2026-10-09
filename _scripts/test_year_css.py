with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

css_addition = """
        /* Year view (multiMonth) specific styling */
        .fc .fc-multimonth-daygrid {
            background-color: #ffffff;
        }
        .fc .fc-multimonth-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #334155;
            padding: 12px 0;
            text-align: center;
        }
        /* Make dots slightly bigger in year view */
        .fc-daygrid-event-dot {
            border-width: 4px !important;
        }
        /* In year view, hide the event titles completely if they appear, keeping only dots */
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
        }
"""
content = content.replace("</style>", css_addition + "</style>")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
