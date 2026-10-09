file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    ">Canvas Size<": ">{{ __('Canvas Size') }}<",
    ">Available<": ">{{ __('Available') }}<",
    ">Occupied<": ">{{ __('Occupied') }}<"
}
for k, v in replacements.items():
    content = content.replace(k, v)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
