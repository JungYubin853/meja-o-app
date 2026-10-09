file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()
    print("div opening count:", content.count("<div"))
    print("div closing count:", content.count("</div"))
