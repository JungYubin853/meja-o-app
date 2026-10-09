file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Make the mini-calendar container fixed 240px and centered inside the sidebar card
old_str = """@endphp
                        <div class="w-full bg-white">
                            <div class="flex justify-between items-center py-1 px-1 mb-2">"""
new_str = """@endphp
                        <div class="w-[240px] mx-auto bg-white">
                            <div class="flex justify-between items-center py-1 px-1 mb-2">"""

content = content.replace(old_str, new_str)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Locked mini calendar width inside sidebar.")
