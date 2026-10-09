file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_str = '<div class="flex flex-col lg:flex-row gap-4">'
new_str = '<div class="flex flex-col lg:flex-row gap-4 items-start">'

if old_str in content:
    content = content.replace(old_str, new_str)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Added items-start to the flex container.")
else:
    print("Could not find the exact string.")
