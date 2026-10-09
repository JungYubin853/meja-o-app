file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_button = """<button @click="savePermissions" :disabled="saving"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs shrink-0">"""
new_button = """<button type="submit" :disabled="saving"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs shrink-0">"""

content = content.replace(old_button, new_button)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed form submit button.")
