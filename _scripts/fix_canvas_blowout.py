file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Revert previous right column change
content = content.replace(
    '<div class="order-1 lg:order-2 flex-1 min-w-0 w-full max-w-[100vw] sm:max-w-full space-y-3 overflow-x-hidden sm:overflow-visible">',
    '<div class="order-1 lg:order-2 flex-1 min-w-0 w-full space-y-3">'
)

# Update the canvas wrapper so it is strictly bound
old_canvas = 'class="bg-slate-950 -mx-3 sm:mx-0 rounded-none sm:rounded-2xl border-t border-b-0 border-x-0 sm:border border-slate-800 overflow-auto p-2 sm:p-4 relative max-h-[70vh] sm:max-h-[600px] h-auto shadow-soft-xl touch-scroll"'

new_canvas = 'class="bg-slate-950 -mx-3 sm:mx-0 rounded-none sm:rounded-2xl border-t border-b-0 border-x-0 sm:border border-slate-800 overflow-auto p-2 sm:p-4 relative max-h-[70vh] sm:max-h-[600px] h-auto shadow-soft-xl touch-scroll w-[calc(100%+1.5rem)] sm:w-full max-w-[100vw] sm:max-w-full"'

content = content.replace(old_canvas, new_canvas)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated canvas wrapper constraints.")
