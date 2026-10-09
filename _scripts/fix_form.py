with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

# 1. Unhide the form for habits
content = content.replace(
    "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')",
    "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist')"
)

# 2. Make the date picker show for habits
# The current blade probably has `@if ($viewMode === 'hourly') <input type="date" name="date" ...>`
# Let's see what it is exactly:
import re
match = re.search(r'<form method="GET".*?</form>', content, re.DOTALL)
if match:
    form_html = match.group(0)
    # If the form uses `@if ($viewMode === 'hourly')`, change it to `@if ($viewMode === 'hourly' || $viewMode === 'habits')`
    new_form_html = form_html.replace("@if ($viewMode === 'hourly')", "@if ($viewMode === 'hourly' || $viewMode === 'habits')")
    content = content.replace(form_html, new_form_html)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing form visibility")
