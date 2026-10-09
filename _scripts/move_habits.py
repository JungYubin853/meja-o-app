with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Find the block
pattern = r"<!-- CUSTOMER HABITS VIEW -->.*?@endif\s*(?=@if \(\$viewMode !== 'overall' && \$viewMode !== 'waitlist'\))"
match = re.search(pattern, content, flags=re.DOTALL)
if match:
    habits_block = match.group(0)
    # Remove from current location
    content = content.replace(habits_block, "")
    
    # Insert right before <!-- OVERALL VIEW -->
    content = content.replace("<!-- OVERALL VIEW -->", habits_block + "\n\n        <!-- OVERALL VIEW -->")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done moving habits view")
