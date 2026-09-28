with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will inject the calendar button after the tutorial link.
tutorial_pattern = r'''(<!-- Tutorial Link -->\s*<a href="/tutorial".*?</svg>\s*</div>\s*<span>Tutorial & Guidelines</span>\s*</a>)'''

# For the desktop sidebar (around line 180)
# and for the mobile drawer (around line 270)
calendar_link = """
                            <!-- Calendar Link -->
                            <a href="/calendar"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('calendar*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('calendar*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span>Calendar</span>
                            </a>"""

content = re.sub(tutorial_pattern, r'\1' + calendar_link, content, flags=re.DOTALL)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
