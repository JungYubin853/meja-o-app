with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace(":class=\"[\n                                              getSectionColorClass(sec.color),", ":class=\"['placed-element',\n                                              getSectionColorClass(sec.color),")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing placed sections")
