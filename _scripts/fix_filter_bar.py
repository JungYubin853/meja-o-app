with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# Find the start of the filter bar
pattern_start = "<!-- Filter & Actions Bar: Perfectly Aligned 1-Row Layout -->"
start_idx = content.find(pattern_start)

# The filter bar ends right before <!-- CUSTOMER HABITS VIEW -->
# Let's find exactly where it ends.
# We can look for the closing </div> of the filter bar.
# Actually, the entire block ends right before <!-- CUSTOMER HABITS VIEW -->
end_idx = content.find("<!-- CUSTOMER HABITS VIEW -->")

if start_idx != -1 and end_idx != -1:
    block = content[start_idx:end_idx]
    
    # Wrap it
    wrapped_block = "@if ($viewMode !== 'habits')\n" + block + "\n@endif\n"
    
    new_content = content[:start_idx] + wrapped_block + content[end_idx:]
    with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
        f.write(new_content)
    print("Wrapped Filter & Actions Bar with @if ($viewMode !== 'habits')")
else:
    print("Could not find bounds")
