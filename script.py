file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will locate the exact `@php ... @endphp` block
php_pattern = r"(\s*)(@php.*?@endphp)"
php_match = re.search(php_pattern, content, re.DOTALL)
if php_match:
    php_code = php_match.group(2)
    
    # We will locate the badge div
    badge_pattern = r"(<div class=\"\{\{ \$bgClass \}\} rounded-xl border border-\{\{ \$classColor \}\}-200 shadow-soft-xs px-3 py-1\.5 flex items-center gap-3 shrink-0\">.*?</div>\s*</div>)"
    # Wait, the badge div only has 1 closing </div>? 
    # Let's verify by just using a simpler approach. We know what the badge html looks like.
