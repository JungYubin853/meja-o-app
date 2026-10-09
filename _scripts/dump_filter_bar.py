file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

start_idx = -1
for i, line in enumerate(lines):
    if "<!-- Filter & Actions Bar" in line:
        start_idx = i
        break

if start_idx != -1:
    print("".join(lines[start_idx:start_idx+80]))
