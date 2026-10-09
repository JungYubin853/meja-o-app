import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = re.sub(r'class="flex flex-col items-center justify-center p-3 bg-slate-50 rounded-xl border border-slate-200"', r'class="flex flex-col items-start justify-center px-1 py-1"', content)
content = re.sub(r'text-center">\{\{ \$classification \}\}', r'text-left">{{ $classification }}', content)
content = re.sub(r'text-center leading-none">\{\{ \$displayName \}\}', r'text-left leading-none">{{ $displayName }}', content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Regex replacements completed.")
