file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# Update Customer Habits Top Bar wrapper
pattern = r"<!-- 1\. Top Bar \(One long container\) -->\s*<div class=\"bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4\">"
replacement = r"<!-- 1. Top Bar (One long container) -->\n                <div class=\"bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-center justify-between gap-3\">"

if re.search(pattern, content):
    content = re.sub(pattern, replacement, content)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Made the Customer Habits top bar match the compact toolbar style.")
else:
    print("Could not find the target container!")
