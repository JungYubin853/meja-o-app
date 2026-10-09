with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will inject @dragover.prevent and @drop.prevent="handleGridDrop($event)" into the inline-grid div
pattern = r"(<div class=\"inline-grid border border-slate-800 relative select-none\")"
replacement = r"\1 @dragover.prevent @drop.prevent=\"handleGridDrop($event)\""
content = re.sub(pattern, replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing drop handlers")
