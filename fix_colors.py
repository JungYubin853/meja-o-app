import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Mini-calendar border (ring)
content = content.replace("ring-indigo-600", "ring-slate-700")

# 2. Gantt chart bar color
content = content.replace("bg-indigo-500 rounded-md border border-indigo-600 shadow-sm flex items-center justify-center overflow-hidden group-hover:bg-indigo-600", "bg-slate-700 rounded-md border border-slate-800 shadow-sm flex items-center justify-center overflow-hidden group-hover:bg-slate-800")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated colors to slate/black.")
