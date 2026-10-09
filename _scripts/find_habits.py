with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will remove the CUSTOMER HABITS VIEW block
pattern = r"<!-- CUSTOMER HABITS VIEW -->.*?@endif"
# But we must be careful with nested @if and @endif!
# The block looks like this:
# <!-- CUSTOMER HABITS VIEW -->
# @elseif ($viewMode === 'habits') ... @endif (or @if ... @endif)
# Let's just find the exact text block manually.
