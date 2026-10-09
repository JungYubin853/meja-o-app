with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Fix desktop sidebar
# Replace the extra </div></div> before Logout Button
pattern1 = re.compile(r'(</a>\s+@endif\s+)</div>\s+</div>\s+</div>\s+<!-- Logout Button -->', re.DOTALL)
content = pattern1.sub(r'\1</div>\n\n                <!-- Logout Button -->', content)

# Fix mobile drawer
# The structure for mobile drawer is:
# <div class="w-[300px] ... flex flex-col px-5 pb-5 h-full ...">
#    <div class="flex flex-col flex-1 min-h-0">
#        <div class="flex items-center ...">Header</div>
#        <div class="space-y-1.5">Links</div>
#    </div>
#    <div class="pt-4 shrink-0 mt-auto px-5">Logout</div> <!-- Wait, if parent has px-5, child shouldn't have px-5! -->
# </div>

pattern2 = re.compile(r'(<!-- Bottom Logout Trigger Button -->\s+<div class="pt-4 shrink-0 border-t border-slate-100 mt-auto )px-5(">)')
content = pattern2.sub(r'\1\2', content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
