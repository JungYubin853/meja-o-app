import os
import re

views_dir = r"C:\Users\LEGION\Herd\meja-o-app\resources\views"
for root, _, files in os.walk(views_dir):
    for file in files:
        if file.endswith(".blade.php"):
            path = os.path.join(root, file)
            with open(path, "r", encoding="utf-8") as f:
                content = f.read()
            
            # Keep fixing nested wraps until there are no more
            old_content = ""
            while content != old_content:
                old_content = content
                content = re.sub(r"\{\{\s*__\('\{\{\s*__\('(.*?)'\)\s*\}\}'\)\s*\}\}", r"{{ __('\1') }}", content)
            
            with open(path, "w", encoding="utf-8") as f:
                f.write(content)

print("Unwrapped double translations.")
