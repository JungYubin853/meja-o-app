import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

pattern = r"(<form method=\"GET\" action=\"/database\" class=\"m-0 p-0 w-full sm:w-auto\">\s*<input type=\"hidden\" name=\"view\" value=\"habits\">\s*<input type=\"hidden\" name=\"date\" value=\"\{\{ \$dateFilter \}\}\">\s*<select name=\"outlet_id\".*?</select>\s*</form>\s*@endif)"

new_date_picker = """
                        <form method="GET" action="/database" class="flex items-center gap-2 m-0 p-0 w-full sm:w-auto">
                            <input type="hidden" name="view" value="habits">
                            @if (isset($selectedOutletId)) <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}"> @endif
                            <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="h-10 text-xs px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 box-border m-0 flex-1 sm:w-auto cursor-pointer shadow-soft-xs">
                        </form>
"""

content = re.sub(pattern, r"\1" + new_date_picker, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Added date picker to top container.")
