file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

pattern = r"<div>\s*<h2 class=\"text-sm font-bold text-slate-800\">Access Control Settings</h2>\s*<p class=\"text-xs font-semibold text-slate-500 mt-0\.5\" x-text=\"user\.email\"></p>\s*</div>"

new_header = r"""<div>
                                <h2 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                                    <span x-text="user ? 'Access Control Settings' : 'Bulk Editing: ' + (bulkMode === 'staff' ? 'All Staff' : 'All Admins')"></span>
                                </h2>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5" x-text="user ? user.email : 'Applying template to all users in this role'"></p>
                            </div>"""

content = re.sub(pattern, new_header, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Header replaced using regex.")
