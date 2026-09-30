with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will remove the second block completely
# To be safe, we will regex match the old Customer Habits block which starts with Average Stay Duration
pattern = r"<!-- CUSTOMER HABITS VIEW -->\s*@if \(\$viewMode === 'habits'\)\s*<div class=\"grid grid-cols-1 lg:grid-cols-2 gap-4\">\s*<!-- Average Time by Day -->.*?</div>\s*</div>\s*@endif\s*"

content = re.sub(pattern, "", content, flags=re.DOTALL)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
