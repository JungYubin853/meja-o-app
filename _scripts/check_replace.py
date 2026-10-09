import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

start_str = "<!-- 1. Top Bar (One long container) -->"
# Find the start of the table so we can replace everything above it
end_str = "<!-- TABLE VIEW -->"

start_idx = content.find(start_str)
end_idx = content.find(end_str)

if start_idx != -1 and end_idx != -1:
    print("Found section to replace.")
else:
    print("Could not find boundaries.")
