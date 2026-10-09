file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Search Bar Header
content = content.replace(
    '<label class="block text-sm font-bold text-slate-900">Search User by Email</label>',
    '<label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Search User by Email</label>'
)
content = content.replace(
    'class="flex-1 text-xs p-3 bg-slate-50 border border-slate-300 rounded-xl font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900"',
    'class="flex-1 text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900"'
)
content = content.replace(
    'class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs"',
    'class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs"'
)

# 2. User List Heading
content = content.replace(
    'class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50"',
    'class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-4"'
)
old_list_title = """                        <div>
                            <h3 class="text-sm font-bold text-slate-900">User List</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Click a user to configure.</p>
                        </div>"""
new_list_title = """                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold text-slate-800">User List</h3>
                        </div>"""
content = content.replace(old_list_title, new_list_title)
content = content.replace(
    '<span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-1 rounded-full border border-slate-200" x-text="allUsersList.length + \' Users\'"></span>',
    '<span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full border border-slate-200" x-text="allUsersList.length + \' Users\'"></span>'
)

# 3. User List Table
content = content.replace(
    '<thead class="sticky top-0 bg-slate-50 z-10 shadow-sm border-b border-slate-200">',
    '<thead class="sticky top-0 bg-slate-50/80 z-10 shadow-sm border-b border-slate-200">'
)
content = content.replace(
    '<tr class="text-slate-700 font-semibold">',
    '<tr class="text-slate-500 text-[10px] uppercase tracking-wider font-bold">'
)
content = content.replace('<div class="font-bold text-slate-900 text-sm" x-text="u.name"></div>', '<div class="font-bold text-slate-800 text-xs" x-text="u.name"></div>')

# 4. Access Control Settings Title
content = content.replace(
    '<h2 class="text-lg font-bold text-slate-900">Access Control Settings</h2>',
    '<h2 class="text-sm font-bold text-slate-800">Access Control Settings</h2>'
)
content = content.replace(
    '<p class="text-sm font-semibold text-slate-500 mt-0.5" x-text="user.email"></p>',
    '<p class="text-xs font-semibold text-slate-500 mt-0.5" x-text="user.email"></p>'
)

# 5. Save Button
content = content.replace(
    'class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-6 py-2.5 rounded-xl transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-sm shrink-0"',
    'class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs shrink-0"'
)

# 6. Checkboxes Labels
content = content.replace(
    '<span class="text-sm font-semibold text-slate-700">',
    '<span class="text-xs font-semibold text-slate-700">'
)
content = content.replace(
    '<span class="font-bold text-slate-800">',
    '<span class="text-sm font-bold text-slate-800">'
)


with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated permission.blade.php typography.")
