file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Mini Calendar Width
content = content.replace("md:w-[280px]", "md:w-[230px]")

# 2. Selected Date / Classification Card Padding & Sizes
content = content.replace("shadow-soft-xs p-3.5 flex", "shadow-soft-xs p-2.5 flex")
content = content.replace("w-10 h-10 rounded-full", "w-8 h-8 rounded-full")
content = content.replace("w-10 h-10 shrink-0 rounded-full", "w-8 h-8 shrink-0 rounded-full")
content = content.replace("w-5 h-5", "w-4 h-4") # Icon sizes
content = content.replace("text-[10px] font-black", "text-[9px] font-black")
content = content.replace("text-lg font-extrabold", "text-base font-extrabold")
content = content.replace("w-[145px]", "w-[125px]") # Date input width
content = content.replace("text-base font-extrabold", "text-sm font-extrabold")

# 3. Gantt graph container: In database.blade.php, it's near the end. Let's make sure it matches the table.
# Currently Gantt has: <div class="bg-white rounded-xl shadow-soft-xs border border-slate-200/80 p-5 mt-4">
content = content.replace("p-5 mt-4", "p-4 mt-3")

# 4. Gantt graph bars thinner. Right now they might have `h-10` or `h-8`. Let's replace the track height.
# I need to see what the Gantt graph HTML looks like.
with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated basic Customer Habits styling")
