import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

pattern = r"\$selectedOutletId = \$outletId;\s*if \(\$user && \$user->isSuperAdmin\(\)\) \{\s*\$selectedOutletId = \$request->input\('outlet_id'\) \?: \(\\App\\Models\\Outlet::first\(\)->id \?\? null\);\s*\}"

replacement = """$selectedOutletId = $outletId;
        if ($user && $user->isSuperAdmin()) {
            $sessionOutletId = session('selected_outlet_id');
            $requestedOutletId = $request->input('outlet_id');
            if ($requestedOutletId) {
                $selectedOutletId = $requestedOutletId;
                session(['selected_outlet_id' => $requestedOutletId]);
            } elseif ($sessionOutletId && \\App\\Models\\Outlet::find($sessionOutletId)) {
                $selectedOutletId = $sessionOutletId;
            } else {
                $firstOutlet = \\App\\Models\\Outlet::first();
                $selectedOutletId = $firstOutlet ? $firstOutlet->id : null;
                if ($selectedOutletId) {
                    session(['selected_outlet_id' => $selectedOutletId]);
                }
            }
        }"""

m = re.search(pattern, content)
if m:
    content = content.replace(m.group(0), replacement)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Updated ReportController session logic!")
else:
    print("Pattern not found in ReportController.")
