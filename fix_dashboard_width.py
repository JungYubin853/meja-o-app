file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update the main wrapper
old_wrapper = '<div class="grid grid-cols-1 lg:grid-cols-7 gap-4 sm:gap-5 items-start">'
new_wrapper = '<div class="flex flex-col lg:flex-row gap-6 items-start w-full">'
content = content.replace(old_wrapper, new_wrapper)

# 2. Update Left Column
old_left = """                <!-- ADMIN INVENTORY: Order-2 on Mobile, 2 Columns on Desktop -->
                @if (Auth::user()->hasPermission('dash_create_table'))
                    <div class="order-2 lg:order-1 lg:col-span-2 space-y-4">"""
new_left = """                <!-- ADMIN INVENTORY: Order-2 on Mobile, Fixed Width on Desktop -->
                @if (Auth::user()->hasPermission('dash_create_table'))
                    <div class="order-2 lg:order-1 w-full lg:w-[320px] shrink-0 space-y-4">"""
content = content.replace(old_left, new_left)

# 3. Update Right Column
old_right = """                <!-- 2D FLOOR PLAN CANVAS -->
                @if (Auth::user()->hasPermission('dash_floor_canvas'))
                    <div
                        class="order-1 lg:order-2 {{ Auth::user()->hasPermission('dash_create_table') ? 'lg:col-span-5' : 'lg:col-span-7' }} space-y-3">"""
new_right = """                <!-- 2D FLOOR PLAN CANVAS -->
                @if (Auth::user()->hasPermission('dash_floor_canvas'))
                    <div class="order-1 lg:order-2 flex-1 min-w-0 space-y-3">"""
content = content.replace(old_right, new_right)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated dashboard width synchronization.")
