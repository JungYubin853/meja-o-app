file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re
# Find anything that looks like a graph container
for match in re.finditer(r'<div[^>]*height[^>]*>', content):
    print(match.group(0))
