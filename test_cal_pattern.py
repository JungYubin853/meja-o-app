file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We are going to replace the Mini Calendar Widget entirely.
pattern = r"(<!-- Mini Calendar Sidebar -->\s*<div class=\"w-full md:w-\[220px\] shrink-0\">\s*@php.*?<div class=\"bg-white border border-\[\#e2e8f0\] w-full\".*?</table>\s*</div>\s*</div>)"

# Let's extract everything inside the Mini Calendar Sidebar and rewrite it.
