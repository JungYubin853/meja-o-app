file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace the classColor logic for Weekends/Weekdays
old_logic = """                      $isWeekend = $dateObj->isWeekend();
                      $classification = $isWeekend ? 'Weekend' : 'Weekday';
                      $classColor = $isWeekend ? 'indigo' : 'emerald';"""
new_logic = """                      $isWeekend = $dateObj->isWeekend();
                      $classification = $isWeekend ? 'Weekend' : 'Weekday';
                      $classColor = 'slate';"""

if old_logic in content:
    content = content.replace(old_logic, new_logic)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Updated classColor to slate for Weekdays and Weekends!")
else:
    print("Could not find the exact old logic.")
