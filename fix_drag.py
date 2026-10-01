with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Update the tray unplaced tables @dragstart
old_tray_drag = r"@dragstart=\"\$event\.dataTransfer\.setData\('text/plain', '\{\{ \$table->id \}\}'\)\""
new_tray_drag = """@dragstart="
document.body.classList.add('is-dragging-table');
$event.dataTransfer.setData('text/plain', '{{ $table->id }}');
$event.dataTransfer.setData('w', '{{ $table->width }}');
$event.dataTransfer.setData('h', '{{ $table->height }}');
$event.dataTransfer.setData('ox', '0');
$event.dataTransfer.setData('oy', '0');"
@dragend="document.body.classList.remove('is-dragging-table');\""""
content = re.sub(old_tray_drag, new_tray_drag, content)

# 2. Update the placed tables @dragstart
old_placed_drag = r"@dragstart=\"\$event\.dataTransfer\.setData\('text/plain', '\{\{ \$table->id \}\}'\)\""
new_placed_drag = """@dragstart="
document.body.classList.add('is-dragging-table');
let rect = $event.currentTarget.getBoundingClientRect();
let zoom = zoom || 1;
let cellW = 40 * zoom;
let cx = $event.clientX - rect.left;
let cy = $event.clientY - rect.top;
$event.dataTransfer.setData('ox', Math.floor(cx / cellW));
$event.dataTransfer.setData('oy', Math.floor(cy / cellW));
$event.dataTransfer.setData('text/plain', '{{ $table->id }}');
$event.dataTransfer.setData('w', '{{ $tableSpanW }}');
$event.dataTransfer.setData('h', '{{ $tableSpanH }}');"
@dragend="document.body.classList.remove('is-dragging-table');\""""
# Note: Since the tray replacement already matched all identical strings, if the placed table used the EXACT same string, 
# it was already replaced by new_tray_drag.
