file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "Filter" in line and "button" in line.lower():
        print(f"Line {i}: {line.strip()}")
        for j in range(i-5, i+2):
            print(f"  {j}: {lines[j].strip()}")
