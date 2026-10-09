file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will replace the bar div: <div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"
old_bar = r'<div class="w-full bg-slate-800 group-hover:bg-slate-900 rounded-t-md transition-all cursor-pointer"'
new_bar = r'<div class="w-full max-w-[36px] mx-auto bg-gradient-to-t from-slate-800 to-slate-700 group-hover:from-slate-900 group-hover:to-slate-800 rounded-t-md transition-all cursor-pointer shadow-sm"'

content = re.sub(old_bar, new_bar, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated HTML Charts for consistent thickness")
