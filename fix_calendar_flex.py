with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace('class="flex-1 min-h-0"', 'class="w-full"')
content = content.replace('class="h-full flex flex-col p-4 sm:p-6 lg:p-8"', 'class="min-h-[calc(100vh-64px)] flex flex-col p-4 sm:p-6 lg:p-8"')

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
