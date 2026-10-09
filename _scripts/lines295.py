with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()
for i in range(295, 310):
    print(f"{i}: {lines[i].rstrip()}")
