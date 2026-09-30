with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re
matches = re.findall(r"<!-- CUSTOMER HABITS VIEW -->", content)
print("Customer Habits View blocks:", len(matches))
