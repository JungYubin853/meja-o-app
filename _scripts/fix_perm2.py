file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    ">Overall<": ">{{ __('Overall') }}<",
    ">Yearly Report<": ">{{ __('Yearly Report') }}<",
    ">Customer Habits<": ">{{ __('Customer Habits') }}<"
}
for k, v in replacements.items():
    content = content.replace(k, v)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Permission fixed.")
