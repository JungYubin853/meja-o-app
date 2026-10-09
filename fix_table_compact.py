file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_title = """                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900">User List</h3>
                            <p class="text-xs text-slate-500 font-medium">Read, oversee, and delete staff accounts.</p>
                        </div>"""
new_title = """                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold text-slate-800">User List</h3>
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full border border-slate-200">
                                {{ count($allUsers) }} Users
                            </span>
                        </div>"""

content = content.replace(old_title, new_title)

# The form text inputs are already fixed to text-xs p-2.5 and uppercase tracking-wider labels.
# Wait, let's fix the table text formatting to be more compact.
old_th = 'class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200"'
new_th = 'class="bg-slate-50/80 text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-200"'
content = content.replace(old_th, new_th)

old_td = 'class="p-3 font-bold text-slate-900"'
new_td = 'class="p-3 text-xs font-bold text-slate-800"'
content = content.replace(old_td, new_td)
content = content.replace('class="p-3 text-slate-600"', 'class="p-3 text-xs font-semibold text-slate-500"')
content = content.replace('class="p-3 font-medium text-slate-800"', 'class="p-3 text-xs font-bold text-slate-700"')

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated table texts to be more compact.")
