file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update main tag width
content = content.replace(
    '<main class="w-full max-w-6xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 pt-20 sm:pt-24 min-h-screen">',
    '<main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 pt-20 sm:pt-24 min-h-screen">'
)

# 2. Remove the header
header_block = """        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Account Management</h1>
            <p class="text-sm text-slate-500 font-medium">Create and oversee system accounts.</p>
        </div>"""
content = content.replace(header_block, "")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Account Management UI")
