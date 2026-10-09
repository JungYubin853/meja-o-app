with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Update unplaced tables
pattern1 = r"@dragstart=\"\$event\.dataTransfer\.setData\('text/plain', '\{\{ \$table->id \}\}'\)\""
replacement1 = """@dragstart="$event.dataTransfer.setData('text/plain', '{{ $table->id }}'); $event.dataTransfer.setData('w', '{{ $table->width }}'); $event.dataTransfer.setData('h', '{{ $table->height }}'); $event.dataTransfer.setData('ox', '0'); $event.dataTransfer.setData('oy', '0');\""""
content = re.sub(pattern1, replacement1, content)

# Update placed tables
pattern2 = r"@dragstart=\"\$event\.dataTransfer\.setData\('text/plain', '\{\{ \$table->id \}\}'\)\""
# Wait, this might match the placed tables too if they use the same exact string. Let's check how they are written.
