with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()

with open("replacement.html", "r", encoding="utf-8") as rf:
    replacement = rf.read()

# Replace from 195 to 294
del lines[195:295]
lines.insert(195, replacement + "\n")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.writelines(lines)

print("Replaced lines")
