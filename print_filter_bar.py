file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re
match = re.search(r"<!-- Controls: Filter & Export.*?</div>\s*</div>", content, re.DOTALL)
if match:
    print(match.group(0))
