with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

replacement = """<!-- Mini Calendar Selector -->
                        <div class="bg-white rounded-2xl shadow-soft-sm border border-slate-200/80 p-5 flex flex-col justify-center items-center">
                            <h3 class="text-sm font-bold text-slate-800 mb-3 w-full text-left">Filters</h3>
                            <form method="GET" action="/database" class="w-full flex flex-col gap-3">
                                <input type="hidden" name="view" value="habits">
                                @if (auth()->check() && auth()->user()->isSuperAdmin())
                                    <select name="outlet_id" onchange="this.form.submit()" class="w-full text-sm font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-900 p-2.5 cursor-pointer hover:bg-slate-100 transition">
                                        @foreach ($outlets ?? [] as $outlet)
                                            <option value="{{ $outlet->id }}" {{ ($selectedOutletId ?? '') == $outlet->id ? 'selected' : '' }}>
                                                {{ $outlet->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif(isset($selectedOutletId))
                                    <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}">
                                @endif
                                <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="w-full text-sm font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-900 p-2.5 cursor-pointer hover:bg-slate-100 transition">
                            </form>
                        </div>"""

import re
# We'll use regex to replace the old block
pattern = r"<!-- Mini Calendar Selector -->[\s\S]*?</form>\s*</div>"
content = re.sub(pattern, replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Replaced Mini Calendar Selector with Filters")
