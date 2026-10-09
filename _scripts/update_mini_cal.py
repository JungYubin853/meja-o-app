file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update day of week logic
content = content.replace("$sDow = $sMonth->dayOfWeekIso;", "$sDow = $sMonth->dayOfWeek;")

# 2. Update header array
content = content.replace("['M','T','W','T','F','S','S']", "['S','M','T','W','T','F','S']")

# 3. Update padding loop
content = content.replace("for($i = 1; $i < $sDow; $i++) { $cells[] = null; }", "for($i = 0; $i < $sDow; $i++) { $cells[] = null; }")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated database.blade.php calendar for Sunday-first!")
