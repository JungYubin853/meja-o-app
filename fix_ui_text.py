file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix Tabs: add whitespace-nowrap
content = content.replace(
    "class=\"flex-1 sm:flex-none px-4 py-1.5 text-xs font-bold rounded-md transition flex items-center justify-center gap-1.5\"",
    "class=\"flex-1 sm:flex-none px-4 py-1.5 text-xs font-bold whitespace-nowrap rounded-md transition flex items-center justify-center gap-1.5\""
)

# Fix Rows per page: add whitespace-nowrap
content = content.replace(
    "<span class=\"text-xs font-semibold text-slate-500\">Rows per page:</span>",
    "<span class=\"text-xs font-semibold text-slate-500 whitespace-nowrap\">Rows per page:</span>"
)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
    print("Fixed squished UI texts.")
