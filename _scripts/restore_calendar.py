import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# I want to extract from git history:
import subprocess
git_content = subprocess.check_output(["git", "show", "HEAD~1:resources/views/database.blade.php"]).decode("utf-8")

match = re.search(r"<!-- Mini Visual Calendar Widget.*?</div>\s*</div>\s*</div>", git_content, re.DOTALL)
if match:
    calendar_code = match.group(0)
    
    # We need to wrap the Main Content Box with the flex-row layout and insert the calendar before it.
    main_box_pattern = r"<!-- 2\. Main Content Box \(Tabbed\) -->\s*<div class=\"bg-white rounded-2xl border"
    
    new_layout = f"""<div class="flex flex-col md:flex-row gap-4">
                <!-- Mini Calendar Sidebar -->
                <div class="w-full md:w-[220px] shrink-0">
                    {calendar_code}
                </div>

                <!-- 2. Main Content Box (Tabbed) -->
                <div class="flex-1 bg-white rounded-2xl border"""
                
    content = re.sub(main_box_pattern, new_layout, content)
    
    # We also need to add the closing </div> for the `flex flex-col md:flex-row gap-4` container.
    # Where does the Tabbed box end? Right before `<!-- OVERALL VIEW -->` or before `@endif`.
    end_pattern = r"(</div>\s*</div>\s*)@endif\s*<!-- OVERALL VIEW -->"
    end_replacement = r"\1    </div>\n        @endif\n        \n        <!-- OVERALL VIEW -->"
    content = re.sub(end_pattern, end_replacement, content)
    
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Successfully re-inserted the Mini Calendar!")
else:
    print("Could not find the Calendar code in Git history!")
