file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Extract the PHP block and the badge
pattern = r"(\s*)(@php.*?@endphp)(\s*)(<div class=\"{{ \$bgClass }} rounded-xl border border-{{ \$classColor }}-200 shadow-soft-xs px-3 py-1\.5 flex items-center gap-3 shrink-0\">.*?</div>\s*</div>\s*</div>)"

match = re.search(pattern, content, re.DOTALL)
if match:
    php_block = match.group(2)
    badge_html = match.group(4)
    # The badge_html actually matched up to the closing tags of the Top Bar.
    # Let's be more precise.
