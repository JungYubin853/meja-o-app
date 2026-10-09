file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re
pattern = r"(\s*)(@endif\s*<!-- OVERALL VIEW -->)"
replacement = r"\1    </div>\n\1</div>\n\1\2"
content = re.sub(pattern, replacement, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Restored missing divs")
