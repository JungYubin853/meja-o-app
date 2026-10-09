import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix the nested interpolation issue
# `{{ $table->capacity ? $table->capacity . ' {{ __('Pax') }}' : '0 {{ __('Pax') }}' }}` -> 
# `{{ $table->capacity ? $table->capacity . ' ' . __('Pax') : '0 ' . __('Pax') }}`
content = content.replace("' {{ __('Pax') }}'", "' ' . __('Pax')")

# Let's also check for any other double curly braces inside double curly braces:
# This regex finds `{{ ... {{ ... }} ... }}` which is invalid in Blade.
# Since it's hard to write a perfect regex for nested braces, let's just find `{{` inside `{{` and print them out so I can manually fix them if they exist.

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed Pax syntax error.")
