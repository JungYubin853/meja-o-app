with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Remove the <style> block
content = re.sub(r"<style>\s*body\.is-dragging \.placed-element \{ pointer-events: none !important; \}\s*</style>\n", "", content)

# 2. Remove document.body.classList.add/remove('is-dragging'); from all @dragstart and @dragend
content = re.sub(r"document\.body\.classList\.add\('is-dragging'\);\s*", "", content)
content = re.sub(r"\s*@dragend=\"document\.body\.classList\.remove\('is-dragging'\);\"", "", content)
content = re.sub(r"@dragend=\"document\.body\.classList\.remove\('is-dragging'\);\"", "", content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done removing is-dragging hack")
