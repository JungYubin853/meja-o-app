file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

pattern = r"// Fetch all users for the list view\s+\$allUsers\s*=\s*App\\Models\\User::select\('id', 'name', 'email', 'role', 'outlet_id'\)\s*->orderBy\('name'\)\s*->get\(\);\s*return view\('permission', compact\('allUsers'\)\);"

replacement = r"return view('permission');"

content = re.sub(pattern, replacement, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Cleaned up web.php")
