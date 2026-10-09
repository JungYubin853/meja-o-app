file_path1 = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php"
with open(file_path1, "r", encoding="utf-8") as f:
    content1 = f.read()

import re

# Fix the single curly braces
content1 = re.sub(r"\{ __\('(.*?)'\) \}", r"{{ __('\1') }}", content1)

with open(file_path1, "w", encoding="utf-8") as f:
    f.write(content1)

file_path2 = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path2, "r", encoding="utf-8") as f:
    content2 = f.read()

content2 = re.sub(r"\{ __\('(.*?)'\) \}", r"{{ __('\1') }}", content2)

with open(file_path2, "w", encoding="utf-8") as f:
    f.write(content2)

print("Fixed Blade syntax for translations.")
