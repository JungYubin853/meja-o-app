file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We target the Filter & Actions Bar
pattern = r"<!-- Filter & Actions Bar: Perfectly Aligned 1-Row Layout -->\s*<div class=\"bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4\">"
replacement = r"<!-- Filter & Actions Bar: Perfectly Aligned 1-Row Layout -->\n        <div class=\"bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-center justify-between gap-3\">"

if re.search(pattern, content):
    content = re.sub(pattern, replacement, content)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Made the Filter & Actions Bar even more compact.")
else:
    print("Could not find the target container!")
