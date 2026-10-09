with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()

start_idx = -1
for i, line in enumerate(lines):
    if "<!-- CUSTOMER HABITS VIEW -->" in line:
        start_idx = i
        break

print(lines[start_idx:start_idx+10])
