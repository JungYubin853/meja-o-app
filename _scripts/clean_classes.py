with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

# Fix the table class back to its original (remove placed-element)
content = content.replace("class=\"placed-element absolute border select-none transition-all duration-300", "class=\"absolute border select-none transition-all duration-300")

# Fix the section :class back to its original (remove 'placed-element')
content = content.replace(":class=\"['placed-element',\n                                              getSectionColorClass(sec.color),\n", ":class=\"[\n                                              getSectionColorClass(sec.color),\n")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done restoring clean classes")
