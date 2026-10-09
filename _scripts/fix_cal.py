import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update the initialization of $cDate
old_init = """                            @php
                                $cDate = \\Carbon\\Carbon::parse($dateFilter);"""
new_init = """                            @php
                                $calReq = request('calendar_month');
                                $cDate = $calReq ? \\Carbon\\Carbon::parse($calReq . '-01') : \\Carbon\\Carbon::parse($dateFilter)->startOfMonth();"""
content = content.replace(old_init, new_init)

# 2. Update the Calendar Header
old_cal_header_pattern = r'<div class="flex justify-between items-center bg-white px-2 py-3">.*?</div>\s*<table'
# Let's extract the exact string to avoid regex escape issues
match = re.search(old_cal_header_pattern, content, re.DOTALL)
if match:
    old_cal_header = match.group(0)
    
    new_cal_header = """<div class="flex justify-between items-center bg-white px-1 py-3">
                                    <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->subMonth()->format('Y-m') }}" class="text-[#475569] hover:text-[#0f172a] transition px-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </a>
                                    <div class="flex items-center gap-1">
                                        <select onchange="window.location='?view=habits&date={{ $dateFilter }}&calendar_month=' + this.value + '-{{ $cDate->format('m') }}'" class="text-[0.95rem] font-[800] text-[#334155] border-none bg-transparent p-0 pr-4 focus:ring-0 cursor-pointer appearance-none text-right">
                                            @for($y = 2020; $y <= 2035; $y++)
                                                <option value="{{ $y }}" {{ $cDate->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                        <select onchange="window.location='?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->format('Y') }}-' + this.value" class="text-[0.95rem] font-[800] text-[#334155] border-none bg-transparent p-0 pr-4 focus:ring-0 cursor-pointer appearance-none">
                                            @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'] as $num => $name)
                                                <option value="{{ $num }}" {{ $cDate->format('m') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <a href="?view=habits&date={{ $dateFilter }}&calendar_month={{ $cDate->copy()->addMonth()->format('Y-m') }}" class="text-[#475569] hover:text-[#0f172a] transition px-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                                <table"""
    content = content.replace(old_cal_header, new_cal_header)
else:
    print("Could not find calendar header.")

# 3. Update the calendar day links so they DON'T pass calendar_month (resets on click)
# and they still preserve other query parameters naturally. Actually, they are currently:
# <a href="?view=habits&date={{ $tDate }}{{ isset($selectedOutletId) ? '&outlet_id='.$selectedOutletId : '' }}"
# Which is fine.

# 4. Update the "Selected Date" display to be an editable input
old_selected_date_str = """                                        <div class="text-lg font-extrabold text-slate-800">{{ $formattedDate }}</div>"""
new_selected_date_str = """                                        <form action="/database" method="GET" class="m-0 p-0">
                                            <input type="hidden" name="view" value="habits">
                                            <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" 
                                                   class="text-lg font-extrabold text-slate-800 border-none bg-transparent p-0 focus:ring-0 cursor-pointer w-[145px] -ml-1">
                                        </form>"""
if old_selected_date_str in content:
    content = content.replace(old_selected_date_str, new_selected_date_str)
else:
    print("Could not find selected date string.")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Blade for Task 2!")
