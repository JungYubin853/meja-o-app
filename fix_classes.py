with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Remove the incorrectly injected class="placed-element" next to id
content = content.replace("id=\"table-{{ $table->id }}\" class=\"placed-element\"", "id=\"table-{{ $table->id }}\"")

# 2. Add 'placed-element' to the correct class attribute for tables
content = content.replace("class=\"absolute border select-none transition-all duration-300", "class=\"placed-element absolute border select-none transition-all duration-300")

# 3. Fix the $data.zoom in dragstart for tables and sections
# Placed tables
pattern = r"let z = window\.Alpine \? \$data\.zoom : 1;"
replacement = "let z = typeof zoom !== 'undefined' ? zoom : 1;"
content = re.sub(pattern, replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing duplicate class and zoom scope")
