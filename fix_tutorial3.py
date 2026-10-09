import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\tutorial.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix all the messed up strings manually
content = content.replace("Tut{{ __('or') }}ial & Guidelines", "{{ __('Tutorial & Guidelines') }}")
content = content.replace("Flo{{ __('or') }} Plan & Tables", "{{ __('Floor Plan & Tables') }}")
content = content.replace("Admin & Rep{{ __('or') }}ts", "{{ __('Admin & Reports') }}")
content = content.replace("Interactive Flo{{ __('or') }} Plan Builder", "{{ __('Interactive Floor Plan Builder') }}")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Tutorial fixed again.")
