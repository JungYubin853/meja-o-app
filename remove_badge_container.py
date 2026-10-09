file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

pattern = r"(<!-- Classification Badge \(Compact\) -->\s*)<div class=\"\{\{ \$bgClass \}\} rounded-xl border border-\{\{ \$classColor \}\}-200 shadow-soft-xs px-4 py-1\.5 shrink-0 w-full lg:w-auto text-center flex flex-col justify-center\">\s*<div class=\"text-\[9px\] font-black text-\{\{ \$classColor \}\}-600 uppercase tracking-widest leading-none mb-1\">\{\{ \$classification \}\}</div>\s*<div class=\"text-xs font-extrabold text-\{\{ \$classColor \}\}-900 leading-none truncate\" title=\"\{\{ \$displayName \}\}\">\{\{ \$displayName \}\}</div>\s*</div>"

replacement = r"""\1<div class="px-2 py-1 shrink-0 w-full lg:w-auto text-center flex flex-col justify-center">
                              <div class="text-[10px] font-black text-{{ $classColor }}-500 uppercase tracking-widest leading-none mb-1">{{ $classification }}</div>
                              <div class="text-[13px] font-extrabold text-{{ $classColor }}-900 leading-none truncate" title="{{ $displayName }}">{{ $displayName }}</div>
                          </div>"""

if re.search(pattern, content):
    new_content = re.sub(pattern, replacement, content)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(new_content)
    print("Removed the container styling!")
else:
    print("Could not find the exact pattern.")
