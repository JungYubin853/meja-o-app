file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Update year select
content = re.sub(r'<select name="year"\s*class="h-10 text-xs', r'<select name="year" onchange="this.form.submit()" class="h-10 text-xs', content)
content = re.sub(r'<button type="submit"[\s\S]*?Filter\s*</button>', '', content)

# 2. Update month input
content = re.sub(r'<input type="month" name="month" value="\{\{ \$monthFilter \}\}"\s*class="h-10 text-xs', r'<input type="month" name="month" value="{{ $monthFilter }}" onchange="this.form.submit()" class="h-10 text-xs', content)

# 3. Update date input
content = re.sub(r'<input type="date" name="date" value="\{\{ \$dateFilter \}\}"\s*class="h-10 text-xs', r'<input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="h-10 text-xs', content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Filter Buttons (Task 2)")
