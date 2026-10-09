file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace Create New Table inside tags
content = content.replace(">Create New Table<", ">{{ __('Create New Table') }}<")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated dashboard.blade.php translations.")
