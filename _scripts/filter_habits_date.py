with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

# Replace the logs query in habits to use $dateFilter
old_query = "VisitorLog::where('outlet_id', $outlet->id)->whereNotNull('ended_at')->get();"
new_query = "VisitorLog::where('outlet_id', $outlet->id)->whereDate('started_at', $dateFilter)->whereNotNull('ended_at')->get();"

content = content.replace(old_query, new_query)

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
