file_path = r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will replace the simple assignments with the session logic
pattern = r"\$dateFilter\s*=\s*\$request->input\('date',\s*now\(\)->format\('Y-m-d'\)\);\s*\$monthFilter\s*=\s*\$request->input\('month',\s*now\(\)->format\('Y-m'\)\);\s*\$yearFilter\s*=\s*\$request->input\('year',\s*now\(\)->format\('Y'\)\);"

replacement = """$dateFilter = $request->input('date');
        if ($dateFilter) {
            session(['dateFilter_'.$viewMode => $dateFilter]);
        } else {
            $dateFilter = session('dateFilter_'.$viewMode, now()->format('Y-m-d'));
        }

        $monthFilter = $request->input('month');
        if ($monthFilter) {
            session(['monthFilter_'.$viewMode => $monthFilter]);
        } else {
            $monthFilter = session('monthFilter_'.$viewMode, now()->format('Y-m'));
        }

        $yearFilter = $request->input('year');
        if ($yearFilter) {
            session(['yearFilter_'.$viewMode => $yearFilter]);
        } else {
            $yearFilter = session('yearFilter_'.$viewMode, now()->format('Y'));
        }"""

if re.search(pattern, content):
    new_content = re.sub(pattern, replacement, content)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(new_content)
    print("Updated ReportController.php with session-based date filters per view.")
else:
    print("Could not find the target code in ReportController.php")
