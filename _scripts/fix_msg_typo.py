file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace(
    'class="p-3 mx-5 mt-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold flex items-center gap-2"',
    'class="p-3 mx-5 mt-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold flex items-center gap-2"'
)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated success msg typo.")
