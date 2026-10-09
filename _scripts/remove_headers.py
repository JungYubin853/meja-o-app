file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# Remove the title and subtitle block
pattern = r"<div>\s*<h2 class=\"text-base sm:text-lg font-bold text-slate-900\">.*?</h2>\s*<p class=\"text-\[11px\] text-slate-500 font-medium mt-0\.5\">Analyze customer volume and dining traffic patterns\.</p>\s*</div>"
new_content = re.sub(pattern, "", content, flags=re.DOTALL)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(new_content)
print("Removed titles and subtitles.")
