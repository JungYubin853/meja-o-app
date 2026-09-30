with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Change height: '100%' to height: 'auto'
content = content.replace("height: '100%',", "height: 'auto',")

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\calendar.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
