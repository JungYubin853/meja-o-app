file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("border-slate-200 mt-2", "border-slate-200")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Removed extra margin.")
