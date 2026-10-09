import os
import re

# FIX DASHBOARD
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("'0 {{ __('Pax') }}'", "'0 ' . __('Pax')")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

# FIX DATABASE
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("'{{ __('Walk-in Guest') }}'", "__('Walk-in Guest')")
content = content.replace("title=\"{{ __('{{ __('Previous') }} Page') }}\"", "title=\"{{ __('Previous Page') }}\"")
content = content.replace("title=\"{{ __('{{ __('Next') }} Page') }}\"", "title=\"{{ __('Next Page') }}\"")
content = content.replace("'{{ __('In Progress') }}'", "__('In Progress')")
content = content.replace("{{ __('No visitor log {{ __('records') }} found for this filter criteria.') }}", "{{ __('No visitor log records found for this filter criteria.') }}")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

# FIX TUTORIAL
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\tutorial.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# I will replace the whole mangled string in tutorial
old_mangled = "{{ __('To add tables {{ __('or') }} col{{ __('or') }}ed sections to your flo{{ __('or') }} plan, you must first create them in the left-hand invent{{ __('or') }}y panel using the') }}"
new_clean = "{{ __('To add tables or colored sections to your floor plan, you must first create them in the left-hand inventory panel using the') }}"
content = content.replace(old_mangled, new_clean)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

# FIX WAITLIST
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\waitlist.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("{{ __('Guests ({{ __('Pax') }})') }}", "{{ __('Guests (Pax)') }}")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Fixed syntax errors.")
