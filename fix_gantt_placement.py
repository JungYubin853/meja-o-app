file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# I need to move the closing </div> of the Main Content Column and the Compact Main Layout 
# to be AFTER the Gantt Graph.
import re
pattern = r"                            </div>\s*</div>\s*</div>\s*<!-- 6\. Gantt Graph -->\s*<div class=\"bg-white rounded-2xl shadow-soft-sm border border-slate-200/80 p-4\">"
replacement = r"""                            </div>
                            
                            <!-- 6. Gantt Graph (Moved inside Main Column) -->
                            <div class="bg-white rounded-xl shadow-soft-xs border border-slate-200/80 p-4 mt-1">"""
content = re.sub(pattern, replacement, content)

# And now I need to add those two </div> that I removed to the very bottom, after the Gantt Graph.
# The Gantt graph ends around line 913:
#                     </div>
#                 </div>
#             @endif
#         </main>
# 
end_pattern = r"                    </div>\s*</div>\s*@endif\s*</main>"
end_replacement = r"""                    </div>
                        </div>
                    </div>
                </div>
            @endif

        </main>"""
content = re.sub(end_pattern, end_replacement, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Moved Gantt Graph into right column (Task 4)")
