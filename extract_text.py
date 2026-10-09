import os
import re
import json

views_dir = r"C:\Users\LEGION\Herd\meja-o-app\resources\views"
files = [
    "dashboard.blade.php",
    "waitlist.blade.php",
    "database.blade.php",
    "tutorial.blade.php",
    "calendar.blade.php",
    "profile.blade.php",
    "account-management.blade.php",
    "permission.blade.php",
    r"components\app-layout.blade.php"
]

texts = set()

# Regex to match text between HTML tags
tag_pattern = re.compile(r'>\s*([^<>{@]+?)\s*<')
# Regex to match placeholders and titles
attr_pattern = re.compile(r'(?:placeholder|title)="([^"]*[a-zA-Z][^"]*)"')

for file in files:
    path = os.path.join(views_dir, file)
    if os.path.exists(path):
        with open(path, "r", encoding="utf-8") as f:
            content = f.read()
            
            # Find in tags
            for match in tag_pattern.findall(content):
                text = match.strip()
                if re.search(r'[a-zA-Z]', text) and not text.startswith('{{') and not text.startswith('x-'):
                    texts.add(text)
            
            # Find in attributes
            for match in attr_pattern.findall(content):
                text = match.strip()
                if re.search(r'[a-zA-Z]', text):
                    texts.add(text)

print(f"Found {len(texts)} unique text strings.")
with open("extracted_texts.json", "w", encoding="utf-8") as f:
    json.dump(list(texts), f, indent=4)
