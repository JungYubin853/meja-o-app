with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

# 1. Add id="grid-container" to the inline-grid div and attach handleGridDrop
pattern1 = r"(<div [^>]*class=\"[^\"]*inline-grid[^\"]*\".*?:style=\".*?grid-auto-rows: 0px;'\">)"
replacement1 = r"""\1
                                  @dragover.prevent
                                  @drop.prevent="handleGridDrop($event)\""""
import re
content = re.sub(pattern1, replacement1, content)

# 2. Remove the individual grid cell @drop handlers
# The grid cells are inside @for ($col = 1; $col <= $width; $col++)
# Currently they have @dragover.prevent and @drop.prevent="..."
# Let's find the div inside the nested loop and strip its drop handler
pattern2 = r"<div @dragover\.prevent\s*@drop\.prevent=\"[\s\S]*?\"\s*class=\"border border-slate-800/80"
replacement2 = r"""<div class="border border-slate-800/80"""
content = re.sub(pattern2, replacement2, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done grid replacement")
