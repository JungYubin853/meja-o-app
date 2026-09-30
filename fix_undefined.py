with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "r", encoding="utf-8") as f:
    content = f.read()

# I need to add default initializations for these variables
defaults = """$habitsData = [];
        $maxHabitsTime = 0;
        $habitsDayOfWeek = [];
        $maxHabitsDay = 0;
        $habitsHourly = [];
        $maxHabitsHour = 0;"""

# Replace the current initialization 
content = content.replace("$habitsData = [];\n        $maxHabitsTime = 0;", defaults)

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
