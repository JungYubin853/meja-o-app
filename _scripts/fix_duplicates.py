import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

pattern = r"(\s*)<!-- Main Content Column -->.*?</div>\s*</div>\s*</div>\s*(<!-- 2\. Main Content Box \(Tabbed\) -->)"
replacement = r"\n\1\2"

new_content = re.sub(pattern, replacement, content, flags=re.DOTALL)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(new_content)
    print("Deleted duplicate cards.")
