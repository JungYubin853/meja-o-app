with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re
# 1. Unplaced tables
pattern1 = r"(<div draggable=\"true\"\s*)@dragstart=\"\$event\.dataTransfer\.setData\('text/plain', '\{\{ \$table->id \}\}'\)\""
replacement1 = r"""\1@dragstart="document.body.classList.add('is-dragging'); $event.dataTransfer.setData('text/plain', '{{ $table->id }}'); $event.dataTransfer.setData('w', '{{ $table->width }}'); $event.dataTransfer.setData('h', '{{ $table->height }}'); $event.dataTransfer.setData('ox', '0'); $event.dataTransfer.setData('oy', '0');" @dragend="document.body.classList.remove('is-dragging');\""""
content = re.sub(pattern1, replacement1, content)

# 2. Placed tables
pattern2 = r"(@dragstart=\")\$event\.dataTransfer\.setData\('text/plain', '\{\{ \$table->id \}\}'\)(\" @endif)"
replacement2 = r"""\1document.body.classList.add('is-dragging'); let r = $event.currentTarget.getBoundingClientRect(); let z = window.Alpine ? $data.zoom : 1; let c = 40 * z; $event.dataTransfer.setData('ox', Math.floor(($event.clientX - r.left)/c)); $event.dataTransfer.setData('oy', Math.floor(($event.clientY - r.top)/c)); $event.dataTransfer.setData('text/plain', '{{ $table->id }}'); $event.dataTransfer.setData('w', '{{ $tableSpanW }}'); $event.dataTransfer.setData('h', '{{ $tableSpanH }}');\2 @dragend="document.body.classList.remove('is-dragging');\""""
content = re.sub(pattern2, replacement2, content)

# 3. Unplaced sections
pattern3 = r"(@dragstart=\")if\(editMode === 'section'\) \{ \$event\.dataTransfer\.setData\('templateIdx', idx\); \}(\")"
replacement3 = r"""\1if(editMode === 'section') { document.body.classList.add('is-dragging'); $event.dataTransfer.setData('templateIdx', idx); $event.dataTransfer.setData('w', sec.w); $event.dataTransfer.setData('h', sec.h); $event.dataTransfer.setData('ox', '0'); $event.dataTransfer.setData('oy', '0'); }\2 @dragend="document.body.classList.remove('is-dragging');\""""
content = re.sub(pattern3, replacement3, content)

# 4. Placed sections
pattern4 = r"(@dragstart=\")if\(editMode === 'section'\) \{ \$event\.dataTransfer\.setData\('sectionIdx', idx\); \}(\")"
replacement4 = r"""\1if(editMode === 'section') { document.body.classList.add('is-dragging'); let r = $event.currentTarget.getBoundingClientRect(); let z = window.Alpine ? $data.zoom : 1; let c = 40 * z; $event.dataTransfer.setData('ox', Math.floor(($event.clientX - r.left)/c)); $event.dataTransfer.setData('oy', Math.floor(($event.clientY - r.top)/c)); $event.dataTransfer.setData('sectionIdx', idx); $event.dataTransfer.setData('w', sec.w); $event.dataTransfer.setData('h', sec.h); }\2 @dragend="document.body.classList.remove('is-dragging');\""""
content = re.sub(pattern4, replacement4, content)

# Add CSS to the top of the file
css = """<style>
body.is-dragging .placed-element { pointer-events: none !important; }
</style>
"""
if "body.is-dragging" not in content:
    content = css + content

# Make sure placed tables and sections have the 'placed-element' class
content = content.replace("id=\"table-{{ $table->id }}\"", "id=\"table-{{ $table->id }}\" class=\"placed-element\"")
# Placed sections
content = content.replace(":class=\"[\n                                              getSectionColorClass(sec.color),\n", ":class=\"['placed-element',\n                                              getSectionColorClass(sec.color),\n")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done all replacements")
