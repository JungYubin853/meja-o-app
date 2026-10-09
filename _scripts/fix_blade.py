import re

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

old_str = "$isCollective = (stripos($h['name'], 'Cuti Bersama') !== false);"
new_str = "$isCollective = (isset($h['type']) && $h['type'] === 'leave');"

if old_str in content:
    content = content.replace(old_str, new_str)
    with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
        f.write(content)
    print("Fixed Blade logic!")
else:
    print("Could not find old string in Blade.")
