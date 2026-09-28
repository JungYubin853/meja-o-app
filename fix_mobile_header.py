with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will replace the mobile header div
# `<div class="flex items-center justify-between pb-3 border-b border-slate-100">`
# with `<div class="h-16 box-border flex items-center justify-between border-b border-slate-100 shrink-0 mb-6">`

pattern = re.compile(r'<!-- Header with Title & Close Button \(X\) -->\s*<div class="flex items-center justify-between pb-3 border-b border-slate-100">')
replacement = """<!-- Header with Title & Close Button (X) -->
                        <div class="h-16 box-border flex items-center justify-between border-b border-slate-100 shrink-0 mb-6">"""

content = pattern.sub(replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
