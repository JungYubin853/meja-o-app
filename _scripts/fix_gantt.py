import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Fix Gantt activeTab logic
content = content.replace("activeTab === 'graph'", "activeTab === 'gantt'")
content = content.replace("activeTab = 'graph'", "activeTab = 'gantt'")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed gantt logic.")
