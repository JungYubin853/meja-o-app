file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_placeholder_start = '<template x-if="!user && !loading">'
new_placeholder_start = '<template x-if="!user && !bulkMode && !loading">'
content = content.replace(old_placeholder_start, new_placeholder_start)

old_placeholder_title = """<h3 class="text-sm font-bold text-slate-700 mb-1" x-text="error ? 'No User Found!' : 'No User Selected'"></h3>"""
new_placeholder_title = """<h3 class="text-sm font-bold text-slate-700 mb-1" x-text="error ? 'Error Encountered!' : 'No Target Selected'"></h3>"""
content = content.replace(old_placeholder_title, new_placeholder_title)

old_placeholder_text = """<p class="text-xs text-slate-500 max-w-xs mx-auto" x-text="error ? 'We couldn\\'t find an account matching that email address.' : 'Select a user from the list on the left or search by email to edit their granular access permissions.'"></p>"""
new_placeholder_text = """<p class="text-xs text-slate-500 max-w-xs mx-auto" x-text="error ? error : 'Choose a bulk template on the left, or select a specific user from Account Management to edit their granular access permissions.'"></p>"""
content = content.replace(old_placeholder_text, new_placeholder_text)


with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated placeholder text.")
