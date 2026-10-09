file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_header_title = """<span x-text="user ? 'Access Control Settings' : 'Bulk Editing: ' + (bulkMode === 'staff' ? 'All Staff' : 'All Admins')"></span>"""
new_header_title = """<span x-text="user ? 'Access Control Settings' : (bulkMode.includes('default') ? 'Edit Base Default: ' : 'Bulk Overwrite: ') + (bulkMode.includes('staff') ? 'Staff' : 'Admins')"></span>"""

old_header_desc = """<p class="text-xs font-semibold text-slate-500 mt-0.5" x-text="user ? user.email : 'Applying template to all users in this role'"></p>"""
new_header_desc = """<p class="text-xs font-semibold text-slate-500 mt-0.5" x-text="user ? user.email : (bulkMode.includes('default') ? 'Changes will apply to newly created accounts.' : 'Changes will overwrite all existing accounts.')"></p>"""

content = content.replace(old_header_title, new_header_title)
content = content.replace(old_header_desc, new_header_desc)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated right column header for base defaults.")
