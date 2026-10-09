import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Fix mobile flex stretch bug
# The parent flex item needs `min-w-0` to prevent its content (the 800px gantt chart) from expanding the entire container off-screen.
content = content.replace(
    '<div class="flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">',
    '<div class="flex-1 min-w-0 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">'
)

# 2. Fix the padding/alignment in the Gantt Chart labels
# Change "w-32 shrink-0 pr-4 text-right truncate" to "w-32 shrink-0 pl-1 pr-2 text-left truncate"
content = content.replace(
    '<div class="w-32 shrink-0 pr-4 text-right truncate">',
    '<div class="w-32 shrink-0 pl-2 pr-2 text-left truncate">'
)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Applied fixes for mobile stretch and Gantt padding.")
