file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update the Right Column wrapper
old_right = '<div class="order-1 lg:order-2 flex-1 min-w-0 space-y-3">'
new_right = '<div class="order-1 lg:order-2 flex-1 min-w-0 w-full max-w-[100vw] sm:max-w-full space-y-3 overflow-x-hidden sm:overflow-visible">'
content = content.replace(old_right, new_right)

# 2. Update the main flex wrapper to make sure it doesn't blow out
old_main = '<div class="flex flex-col lg:flex-row gap-6 items-start w-full">'
new_main = '<div class="flex flex-col lg:flex-row gap-6 items-start w-full max-w-full">'
content = content.replace(old_main, new_main)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated dashboard mobile width constraints.")
