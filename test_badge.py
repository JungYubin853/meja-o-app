file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Find the Classification Badge block
badge_pattern = r"(<div class=\"{{ \$bgClass }} rounded-xl border border-{{ \$classColor }}-200 shadow-soft-xs px-3 py-1\.5 flex items-center gap-3 shrink-0\">.*?</div>\s*</div>)"

badge_match = re.search(badge_pattern, content, re.DOTALL)
if badge_match:
    badge_html = badge_match.group(1)
    
    # 2. Remove it from the Top Bar
    content = content.replace(badge_html, "")
    
    # We might have left a trailing `</div>` from the top bar flex container inside badge_match or outside.
    # Wait, let's look at the badge_html carefully.
    # The badge_html ends with `</div>\n                    </div>`. I need to ensure I don't break the top bar closing tags!
