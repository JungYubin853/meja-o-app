with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

# Make outlet selector keep the date filter for habits
content = content.replace(
    "@if ($viewMode === 'hourly') <input type=\"hidden\" name=\"date\" value=\"{{ $dateFilter }}\"> @endif",
    "@if ($viewMode === 'hourly' || $viewMode === 'habits') <input type=\"hidden\" name=\"date\" value=\"{{ $dateFilter }}\"> @endif"
)

# Unhide the date filter form for habits
content = content.replace(
    "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')",
    "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist')"
)

# Make the date form render the hourly picker (daily date picker) for habits
content = content.replace(
    "@elseif ($viewMode === 'hourly')",
    "@elseif ($viewMode === 'hourly' || $viewMode === 'habits')"
)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing habits date forms")
