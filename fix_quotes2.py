with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("@drop.prevent=\\\"handleGridDrop($event)\\\"", "@drop.prevent=\"handleGridDrop($event)\"")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing quotes")
