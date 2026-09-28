with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Remove px-5 pb-5 from the wrapper
content = re.sub(
    r'class="w-\[300px\] bg-white shadow-soft-xl flex flex-col px-5 pb-5 border-l border-slate-200 h-full overflow-hidden"',
    r'class="w-[300px] bg-white shadow-soft-xl flex flex-col border-l border-slate-200 h-full overflow-hidden"',
    content
)

# 2. Add px-5 to the header (and fix the h-16 box-border)
content = re.sub(
    r'<!-- Header with Title & Close Button \(X\) -->\s*<div class="h-16 box-border flex items-center justify-between border-b border-slate-100 shrink-0 mb-6">',
    r'<!-- Header with Title & Close Button (X) -->\n                        <div class="h-16 box-border flex items-center justify-between border-b border-slate-200/80 shrink-0 px-5 mb-6">',
    content
)

# 3. Add px-5 pb-4 to the links wrapper
content = re.sub(
    r'<div class="space-y-1.5 flex-1 overflow-y-auto pb-4">',
    r'<div class="space-y-1.5 flex-1 overflow-y-auto px-5 pb-4">',
    content
)

# 4. Add px-5 pb-5 to the logout button wrapper
content = re.sub(
    r'<!-- Bottom Logout Trigger Button -->\s*<div class="pt-4 shrink-0 border-t border-slate-100 mt-auto ">',
    r'<!-- Bottom Logout Trigger Button -->\n                      <div class="pt-4 shrink-0 border-t border-slate-100 mt-auto px-5 pb-5">',
    content
)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
