import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = re.sub(r'(\@endphp\s*)<div class="w-full bg-white">', r'\1<div class="w-[240px] mx-auto bg-white">', content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Replaced to 240px.")
