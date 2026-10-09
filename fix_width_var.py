with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("let gw = {{ $width }};", "let gw = {{ $gridWidth > 0 ? $gridWidth : 15 }};")
content = content.replace("let gh = {{ $height }};", "let gh = {{ $gridHeight > 0 ? $gridHeight : 15 }};")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing width and height variables")
