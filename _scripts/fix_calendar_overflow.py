with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace('class="flex-1 bg-white rounded-2xl shadow-soft-sm border border-slate-200/80 p-5 overflow-hidden flex flex-col"', 'class="flex-1 bg-white rounded-2xl shadow-soft-sm border border-slate-200/80 p-5"')

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
