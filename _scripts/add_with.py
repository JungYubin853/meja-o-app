with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("VisitorLog::where('outlet_id', $selectedOutletId)", "VisitorLog::with('table')->where('outlet_id', $selectedOutletId)")

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Added with(table)")
