file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("<div class=\\\"bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-center justify-between gap-3\\\">", 
                          "<div class=\"bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-center justify-between gap-3\">")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed escaped quotes.")
