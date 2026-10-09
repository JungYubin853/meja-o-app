file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Remove the standalone row
pattern_row = r"(\s*)<div class=\"px-4 py-4 sm:px-5 border-b border-slate-100 bg-white\">\s*<div class=\"\{\{ \$bgClass \}\} w-full rounded-xl border border-\{\{ \$classColor \}\}-200 shadow-soft-xs px-4 py-3 flex flex-col items-center justify-center text-center\">\s*<div class=\"text-\[10px\] font-black text-\{\{ \$classColor \}\}-600 uppercase tracking-widest mb-1\">\{\{ \$classification \}\}</div>\s*<div class=\"text-\[15px\] font-extrabold text-\{\{ \$classColor \}\}-900 leading-tight truncate\" title=\"\{\{ \$displayName \}\}\">\{\{ \$displayName \}\}</div>\s*</div>\s*</div>"
content = re.sub(pattern_row, "", content, flags=re.DOTALL)

# 2. Insert the compact badge between Tabs and Pagination
pattern_insert = r"(<!-- Tabs -->.*?</div>\s*)(<!-- Pagination Controls -->)"
replacement_insert = r"""\1
                        <!-- Classification Badge (Compact) -->
                        <div class="{{ $bgClass }} rounded-xl border border-{{ $classColor }}-200 shadow-soft-xs px-4 py-1.5 shrink-0 w-full lg:w-auto text-center flex flex-col justify-center">
                            <div class="text-[9px] font-black text-{{ $classColor }}-600 uppercase tracking-widest leading-none mb-1">{{ $classification }}</div>
                            <div class="text-xs font-extrabold text-{{ $classColor }}-900 leading-none truncate" title="{{ $displayName }}">{{ $displayName }}</div>
                        </div>

                        \2"""
content = re.sub(pattern_insert, replacement_insert, content, flags=re.DOTALL)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Moved badge between tabs and pagination!")
