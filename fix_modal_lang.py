file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

pattern = r"    <!-- ========================================================================= -->\s*<!-- LOGOUT CONFIRMATION MODAL \(Also teleported to body\) -->"

new_modal = """    <!-- ========================================================================= -->
    <!-- LANGUAGE SWITCHER MODAL -->
    <!-- ========================================================================= -->
    <template x-teleport="body">
        <div x-show="langModalOpen" class="relative z-50" x-cloak>
            <div x-show="langModalOpen" @click="langModalOpen = false"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
                
                <div class="bg-white rounded-2xl max-w-sm w-full p-5 space-y-4 shadow-soft-xl border border-slate-100 text-center"
                    @click.stop>
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Choose Language</h3>
                        <p class="text-xs text-slate-500 mt-1">Select your preferred language for the interface.</p>
                    </div>
                    <div class="flex flex-col gap-2 mt-4">
                        <a href="/lang/en"
                            class="w-full py-3 text-sm font-bold rounded-xl transition flex items-center justify-between px-4 border-2 {{ app()->getLocale() === 'en' ? 'border-slate-900 text-slate-900 bg-slate-50' : 'border-slate-100 text-slate-600 hover:border-slate-300' }}">
                            <span>English</span>
                            @if(app()->getLocale() === 'en')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @endif
                        </a>
                        <a href="/lang/id"
                            class="w-full py-3 text-sm font-bold rounded-xl transition flex items-center justify-between px-4 border-2 {{ app()->getLocale() === 'id' ? 'border-slate-900 text-slate-900 bg-slate-50' : 'border-slate-100 text-slate-600 hover:border-slate-300' }}">
                            <span>Indonesian (Bahasa Indonesia)</span>
                            @if(app()->getLocale() === 'id')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @endif
                        </a>
                    </div>
                    <button type="button" @click="langModalOpen = false" class="w-full py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition mt-2">Cancel</button>
                </div>
            </div>
        </div>
    </template>

    <!-- ========================================================================= -->
    <!-- LOGOUT CONFIRMATION MODAL (Also teleported to body) -->"""

content = re.sub(pattern, new_modal, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Injected Language Modal successfully.")
