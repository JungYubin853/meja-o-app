import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

pattern = r"<div class=\"flex justify-between items-center py-1 px-1 mb-2\">\s*<a href=\"\?view=habits&date=\{\{ \$dateFilter \}\}&calendar_month=\{\{ \$cDate->copy\(\)->subMonth\(\)->format\('Y-m'\) \}\}\" class=\"text-\[\#475569\] hover:bg-slate-100 p-1\.5 rounded-full transition\">\s*<svg.*?</svg>\s*</a>\s*<div class=\"text-\[1rem\] font-\[800\] text-\[\#334155\] tracking-wide\">\{\{ \$cDate->format\('F'\) \}\}</div>\s*<a href=\"\?view=habits&date=\{\{ \$dateFilter \}\}&calendar_month=\{\{ \$cDate->copy\(\)->addMonth\(\)->format\('Y-m'\) \}\}\" class=\"text-\[\#475569\] hover:bg-slate-100 p-1\.5 rounded-full transition\">\s*<svg.*?</svg>\s*</a>\s*</div>"

new_block = """<div class="flex justify-between items-center py-1 mb-2">
                                    <div class="flex items-center">
                                        <select onchange="window.location='?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->format('Y') }}-' + this.value" class="text-[0.95rem] font-[800] text-[#334155] tracking-wide bg-transparent border-none px-1 py-1 focus:ring-0 cursor-pointer hover:bg-slate-100 rounded-md transition-colors appearance-none" style="background-image: none;">
                                            @foreach(['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'] as $num => $name)
                                                <option value="{{ $num }}" {{ $cDate->format('m') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <select onchange="window.location='?view=habits&date={{ $dateFilter }}&calendar_month=' + this.value + '-{{ $cDate->format('m') }}'" class="text-[0.95rem] font-[800] text-[#334155] tracking-wide bg-transparent border-none px-1 py-1 focus:ring-0 cursor-pointer hover:bg-slate-100 rounded-md transition-colors appearance-none" style="background-image: none;">
                                            @for($y = 2023; $y <= 2030; $y++)
                                                <option value="{{ $y }}" {{ $cDate->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="flex items-center shrink-0">
                                        <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->subMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                        </a>
                                        <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->addMonth()->format('Y-m') }}" class="text-[#475569] hover:bg-slate-100 p-1.5 rounded-full transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </div>
                                </div>"""

if re.search(pattern, content, re.DOTALL):
    content = re.sub(pattern, new_block, content, flags=re.DOTALL)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Regex replacement success.")
else:
    print("Could not find pattern.")
