with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re
# Regex to match the old block
pattern = r"<!-- New Graphs for Day of Week and Time of Day -->.*?</div>\s*</div>\s*</div>"
content = re.sub(pattern, "", content, flags=re.DOTALL)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done removing old graphs")
