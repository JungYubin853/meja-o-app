file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace overall table padding to make it compact
import re
pattern = r"<!-- OVERALL VIEW -->[\s\S]*?@endif"

match = re.search(pattern, content)
if match:
    overall_block = match.group(0)
    # Replace padding p-3 sm:p-4 with px-3 py-2, text-[11px] to text-[10px]
    overall_block = overall_block.replace('p-3 sm:p-4', 'px-3 py-2')
    overall_block = overall_block.replace('p-4 sm:p-5', 'px-3 py-2.5')
    overall_block = overall_block.replace('p-4', 'px-3 py-2')
    overall_block = overall_block.replace('text-sm sm:text-base', 'text-xs sm:text-sm')
    overall_block = overall_block.replace('text-[11px]', 'text-[10px]')
    content = content.replace(match.group(0), overall_block)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Updated Overall Table UI (Task 1)")
else:
    print("Could not find OVERALL VIEW block")
