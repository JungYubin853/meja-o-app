file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will use Regex to find the hamburger block and replace it.
pattern_hamburger = r"<!-- Right: Hamburger Menu Button -->\s*@if \(Auth::check\(\)\)\s*<button type=\"button\" @click=\"sideMenuOpen = true\" title=\"Open Navigation Menu\"[\s\S]*?</button>\s*@endif"

new_hamburger = """<!-- Right: Actions -->
                <div class="flex items-center gap-2">
                    <!-- Language Switcher Button -->
                    <button type="button" @click="langModalOpen = true" title="Switch Language"
                        class="bg-white hover:bg-slate-50 text-slate-700 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl transition flex items-center justify-center border border-slate-200 shadow-soft-xs hover:border-slate-300 active:scale-95 gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">{{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}</span>
                    </button>

                    <!-- Hamburger Menu Button (Mobile) -->
                    @if (Auth::check())
                        <button type="button" @click="sideMenuOpen = true" title="Open Navigation Menu"
                            class="bg-white hover:bg-slate-50 text-slate-700 p-2.5 rounded-xl transition flex lg:hidden items-center justify-center border border-slate-200 shadow-soft-xs hover:border-slate-300 active:scale-95">
                            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    @endif
                </div>"""

content = re.sub(pattern_hamburger, new_hamburger, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated hamburger to include language button.")
