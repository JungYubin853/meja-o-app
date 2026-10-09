import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_block = """<span class="text-slate-400 font-bold mr-1">#{{ $log->id }}</span> 
                                            {{ $log->customer_name ?? 'Walk-in Guest' }} 
                                            <span class="text-slate-500 font-medium ml-1">({{ $log->pax }} pax)</span>"""

new_block = """<span class="text-slate-400 font-bold mr-1">#{{ $index + 1 }}</span> 
                                            {{ $log->customer_name ?? 'Walk-in Guest' }}"""

if old_block in content:
    content = content.replace(old_block, new_block)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Replaced ID and removed pax display.")
else:
    print("Could not find the exact string. Let's use regex.")
    
    # regex fallback
    pattern = r'<span class="text-slate-400 font-bold mr-1">#\{\{ \$log->id \}\}<\/span>\s*\{\{ \$log->customer_name \?\? \'Walk-in Guest\' \}\}\s*<span class="text-slate-500 font-medium ml-1">\(\{\{ \$log->pax \}\} pax\)<\/span>'
    replacement = r'<span class="text-slate-400 font-bold mr-1">#{{ $index + 1 }}</span>\n                                            {{ $log->customer_name ?? \'Walk-in Guest\' }}'
    content = re.sub(pattern, replacement, content)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Replaced ID and removed pax display via regex.")
