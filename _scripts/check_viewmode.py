with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "Visitor Log Entries & Dining" in line:
        print(f"Line {i}: {line.strip()}")
        # print previous 30 lines to see the @if condition
        for j in range(max(0, i-30), i):
            print(f"  {j}: {lines[j].strip()}")
