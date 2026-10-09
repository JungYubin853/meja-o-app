file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We want to insert the indicator inside the span, but after the text.
# For example: <span class="...">Dashboard <span x-show="..." class="...">(!)</span></span>
# Or just right after the closing </span>
# Let's insert it right after the closing </span>:
# replacement: \1 <span x-show="user && permissions.\3 !== defaultPerms.\3" class="text-amber-500 font-bold ml-1 text-[11px]" title="Differs from default template">(!)</span>\2

pattern = r'(<span class="[^"]+">.*?</span>)(\s*<input type="checkbox" x-model="permissions\.([^"]+)")'
def replacer(match):
    span_tag = match.group(1)
    whitespace_and_input = match.group(2)
    key = match.group(3)
    
    indicator = f'\n                                          <span x-cloak x-show="user && permissions.{key} !== defaultPerms.{key}" class="text-amber-500 font-bold ml-1 text-[11px] shrink-0" title="Differs from default template">(!)</span>'
    
    # Actually, if we put it after the span, it might mess up flex layout (justify-between). 
    # Because <label class="flex items-center justify-between"> expects exactly 2 children for perfect spacing!
    # Let's insert the indicator INSIDE the span tag, right before the closing </span>.
    
    # span_tag looks like `<span class="...">TEXT</span>`
    span_start = span_tag[:-7] # everything except </span>
    span_end = '</span>'
    
    new_span = f'{span_start}<span x-cloak x-show="user && permissions.{key} !== defaultPerms.{key}" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span>{span_end}'
    
    return f'{new_span}{whitespace_and_input}'

new_content = re.sub(pattern, replacer, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(new_content)
print("Injected divergence indicators.")
