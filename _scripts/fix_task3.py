file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_cond = "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist')"
new_cond = "@if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')"

# There might be multiple occurrences. The one right above "VISITOR DINING LOG ENTRIES" is the one we want.
import re
pattern = r"@if \(\$viewMode !== 'overall' && \$viewMode !== 'waitlist'\)\s*<!-- VISITOR DINING LOG ENTRIES"
replacement = r"@if ($viewMode !== 'overall' && $viewMode !== 'waitlist' && $viewMode !== 'habits')\n        <!-- VISITOR DINING LOG ENTRIES"
content = re.sub(pattern, replacement, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Blade for Task 3!")
