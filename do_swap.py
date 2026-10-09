import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

form_pattern = r"<!-- Native Date Picker \(For quick jumping\) -->\s*<form method=\"GET\" action=\"/database\" class=\"m-0 p-0 w-full\">\s*<input type=\"hidden\" name=\"view\" value=\"habits\">\s*@if \(isset\(\$selectedOutletId\)\) <input type=\"hidden\" name=\"outlet_id\" value=\"\{\{ \$selectedOutletId \}\}\"> @endif\s*<input type=\"date\" name=\"date\" value=\"\{\{ \$dateFilter \}\}\" onchange=\"this\.form\.submit\(\)\" class=\"h-10 w-full text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 box-border m-0 shadow-soft-xs cursor-pointer\">\s*</form>\s*<hr class=\"border-slate-100 border-t-2\">"

classification_pattern = r"@php\s*\$dateObj = \\Carbon\\Carbon::parse\(\$dateFilter\);.*?<div class=\"flex flex-col items-center justify-center p-3 bg-slate-50 rounded-xl border border-slate-200 mt-2\">\s*<div class=\"text-\[10px\] font-black text-\{\{ \$classColor \}\}-500 uppercase tracking-widest leading-none mb-1\.5 text-center\">\{\{ \$classification \}\}</div>\s*<div class=\"text-sm font-extrabold text-slate-800 text-center leading-none\">\{\{ \$displayName \}\}</div>\s*</div>"

# Extract classification block
class_match = re.search(classification_pattern, content, re.DOTALL)
if class_match:
    class_block = class_match.group(0)
    # Remove from bottom
    content = content.replace(class_block, "")
    
    # Replace top form with class block using a lambda to avoid escape sequence issues
    content = re.sub(form_pattern, lambda m: class_block + "\n\n<hr class=\"border-slate-100 border-t-2\">\n", content)

    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Replaced date picker with classification block.")
else:
    print("Could not find classification block.")

