with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will replace `<div class="space-y-1.5">` inside the mobile drawer.
# Specifically the one right after `<!-- Menu Navigation Links -->` in the slide-over
pattern = re.compile(r'(<!-- Menu Navigation Links -->\s+<div class=")space-y-1.5(">)')
content = pattern.sub(r'\1space-y-1.5 flex-1 overflow-y-auto pb-4\2', content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
