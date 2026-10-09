import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import subprocess
git_content = subprocess.check_output(["git", "show", "HEAD~1:resources/views/database.blade.php"]).decode("utf-8")

match = re.search(r"<!-- Mini Visual Calendar Widget.*?</div>\s*</div>\s*</div>", git_content, re.DOTALL)
if match:
    calendar_code = match.group(0)
    
    main_box_pattern = r"<!-- 2\. Main Content Box \(Tabbed\) -->\s*<div class=\"bg-white rounded-2xl border"
    m_main = re.search(main_box_pattern, content)
    
    if m_main:
        new_layout = f"""<div class="flex flex-col md:flex-row gap-4">
                <!-- Mini Calendar Sidebar -->
                <div class="w-full md:w-[220px] shrink-0">
                    {calendar_code}
                </div>

                <!-- 2. Main Content Box (Tabbed) -->
                <div class="flex-1 bg-white rounded-2xl border"""
        content = content.replace(m_main.group(0), new_layout)
        
        end_pattern = r"</div>\n            </div>\n        @endif\n        \n        <!-- OVERALL VIEW -->"
        
        # Wait, the end part of the habits section looks like this:
        #             </div>
        #         </div>
        #     </div>
        # @endif
        # 
        # <!-- OVERALL VIEW -->
        # I need to add one more </div> right before @endif.
        
        # We can find "@endif\n        \n        <!-- OVERALL VIEW -->"
        end_idx = content.find("@endif\n        \n        <!-- OVERALL VIEW -->")
        if end_idx != -1:
            content = content[:end_idx] + "    </div>\n        " + content[end_idx:]
            
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(content)
        print("Successfully re-inserted the Mini Calendar!")
    else:
        print("Could not find Main Content Box!")
else:
    print("Could not find the Calendar code in Git history!")
