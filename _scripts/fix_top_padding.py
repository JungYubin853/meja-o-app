file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace(
    '<main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 pt-20 sm:pt-24 min-h-screen">',
    '<main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 min-h-screen">'
)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Removed extra top padding.")
