import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_block = """<div class="flex flex-col items-center justify-center p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="text-[10px] font-black text-{{ $classColor }}-500 uppercase tracking-widest leading-none mb-1.5 text-center">{{ $classification }}</div>
                                <div class="text-sm font-extrabold text-slate-800 text-center leading-none">{{ $displayName }}</div>
                            </div>"""

new_block = """<div class="flex flex-col items-start justify-center px-1 py-1">
                                <div class="text-[10px] font-black text-{{ $classColor }}-500 uppercase tracking-widest leading-none mb-1.5 text-left">{{ $classification }}</div>
                                <div class="text-sm font-extrabold text-slate-800 text-left leading-none">{{ $displayName }}</div>
                            </div>"""

if old_block in content:
    content = content.replace(old_block, new_block)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Replaced classification badge styling.")
else:
    print("Could not find exact block. Attempting regex...")
