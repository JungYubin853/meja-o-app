file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix the right column container blowout by adding w-full
content = content.replace(
    '<div class="order-1 lg:order-2 flex-1 min-w-0 space-y-3">',
    '<div class="order-1 lg:order-2 flex-1 min-w-0 w-full max-w-full space-y-3 overflow-hidden">'
)

# And fix the canvas wrapper blowout by adding w-full, but removing -mx-3 to prevent scroll bar issues
content = content.replace(
    '<div\n                        class="bg-slate-950 -mx-3 sm:mx-0',
    '<div\n                        class="bg-slate-950 w-full sm:mx-0'
)
# Wait, let's just make it a single line replace to be safe.
