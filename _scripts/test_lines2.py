with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()

start_idx = -1
for i, line in enumerate(lines):
    if "<!-- CUSTOMER HABITS VIEW -->" in line:
        start_idx = i

print(f"Start: {start_idx}")
for i in range(start_idx, len(lines)):
    if "@endif" in lines[i] and i > len(lines) - 20:
        print(f"Line {i}: {lines[i].rstrip()}")
