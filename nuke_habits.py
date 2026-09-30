with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Remove ALL Customer Habits blocks and anything between them and the next @if ($viewMode !== 'overall'...)
# Actually, the safest way is to just find all chunks starting with <!-- CUSTOMER HABITS VIEW -->
# up to the next @if ($viewMode !== 'overall'...)

pattern = r"<!-- CUSTOMER HABITS VIEW -->.*?@if \(\$viewMode !== 'overall' && \$viewMode !== 'waitlist'\)"
content = re.sub(pattern, "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist')", content, flags=re.DOTALL)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done stripping all old customer habits blocks")
