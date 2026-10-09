file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re
matches = re.finditer(r"new Chart.*?\}", content, re.DOTALL)
for match in matches:
    print(match.group(0)[:300])
