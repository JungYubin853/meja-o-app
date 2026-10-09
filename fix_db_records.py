file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("{{ __('Waitlist {{ __('Records') }} Database') }}", "{{ __('Waitlist Records Database') }}")
content = content.replace("No waitlist {{ __('Records') }} found", "No waitlist records found")
content = content.replace("{{ __('No visitor log {{ __('records') }} found for this filter criteria.') }}", "{{ __('No visitor log records found for this filter criteria.') }}")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Fixed nested Records bug in database.blade.php")
