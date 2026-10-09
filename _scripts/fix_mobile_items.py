file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace(
    '<div class="flex flex-col lg:flex-row gap-4 items-start">',
    '<div class="flex flex-col lg:flex-row gap-4 lg:items-start">'
)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed items-start bug for mobile layout.")
