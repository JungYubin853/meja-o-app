file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Make the aspect ratio bulletproof by putting it in a block wrapper if needed, or just using h-8
# Wait, h-8 is 32px. 240px / 7 is ~34px. Using h-8 on the td actually works great and makes them nearly perfectly square!
# Let's just swap `style="aspect-ratio: 1/1;"` for `h-8` to be highly compatible.

content = content.replace("<td class=\"p-0 border border-[#e2e8f0] relative group text-center align-middle\" style=\"aspect-ratio: 1/1;\">",
                          "<td class=\"p-0 border border-[#e2e8f0] h-[34px] relative group text-center align-middle\">")
content = content.replace("<td class=\"border border-[#e2e8f0] bg-[#f1f5f9]\" style=\"aspect-ratio: 1/1;\"></td>",
                          "<td class=\"border border-[#e2e8f0] bg-[#f1f5f9] h-[34px]\"></td>")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated to explicit height.")
