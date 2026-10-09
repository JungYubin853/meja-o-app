file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "group-hover:bg-slate-900" in line:
        print("MATCH at line", i)
        for j in range(i-20, i+20):
            print(lines[j].rstrip())
        break
