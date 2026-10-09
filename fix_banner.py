file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will match the entire classification banner block.
# It starts with `<div class="px-4 py-3 sm:px-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">`
# And ends with the matching `</div>`.

pattern = r"(<div class=\"px-4 py-3 sm:px-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white\">\s*<div class=\"text-xs font-bold text-slate-500 uppercase tracking-widest\">Date Classification</div>\s*<div class=\"\{\{ \$bgClass \}\} rounded-xl border border-\{\{ \$classColor \}\}-200 shadow-soft-xs px-3 py-1\.5 flex items-center gap-3 shrink-0\">\s*<div class=\"truncate text-right\">\s*<div class=\"text-\[9px\] font-black text-\{\{ \$classColor \}\}-600 uppercase tracking-widest mb-0\.5\">\{\{ \$classification \}\}</div>\s*<div class=\"text-xs font-extrabold text-\{\{ \$classColor \}\}-900 leading-tight truncate\" title=\"\{\{ \$displayName \}\}\">\{\{ \$displayName \}\}</div>\s*</div>\s*<div class=\"w-8 h-8 shrink-0 rounded-full.*?</svg>\s*@endif\s*</div>\s*</div>\s*</div>)"

# Replace with the new wide layout without icon and without DATE CLASSIFICATION
replacement = r"""<div class="px-4 py-4 sm:px-5 border-b border-slate-100 bg-white">
                        <div class="{{ $bgClass }} w-full rounded-xl border border-{{ $classColor }}-200 shadow-soft-xs px-4 py-3 flex flex-col items-center justify-center text-center">
                            <div class="text-[10px] font-black text-{{ $classColor }}-600 uppercase tracking-widest mb-1">{{ $classification }}</div>
                            <div class="text-[15px] font-extrabold text-{{ $classColor }}-900 leading-tight truncate" title="{{ $displayName }}">{{ $displayName }}</div>
                        </div>
                    </div>"""

if re.search(pattern, content, re.DOTALL):
    new_content = re.sub(pattern, replacement, content, flags=re.DOTALL)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(new_content)
    print("Updated classification banner layout!")
else:
    print("Could not find the exact pattern. Doing substring replacement.")
    # Fallback to precise substring extraction since regex with lots of specific HTML can fail easily
