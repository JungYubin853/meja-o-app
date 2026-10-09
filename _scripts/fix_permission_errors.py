file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Insert the Warning Screen and wrapping div
old_right_col_start = """                <template x-if="user || bulkMode">
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">"""

new_right_col_start = """                <template x-if="user || bulkMode">
                    <div class="space-y-6">
                        
                        <!-- WARNING SCREEN -->
                        <template x-if="bulkMode && bulkStep === 'warn'">
                            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 sm:p-6 space-y-4 shadow-soft-xs">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0 border border-amber-200/50">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-amber-900">Custom Permissions Detected</h3>
                                        <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                                            The following <span class="font-bold uppercase tracking-wider" x-text="bulkMode"></span>s have custom access settings that differ from the default template:
                                        </p>
                                        <ul class="mt-3 space-y-1.5 bg-amber-100/50 p-3 rounded-xl border border-amber-200/50">
                                            <template x-for="du in divergentUsers" :key="du.id">
                                                <li class="text-[11px] font-bold text-amber-900 flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                    <span x-text="du.name + ' (' + du.email + ')'"></span>
                                                </li>
                                            </template>
                                        </ul>
                                        <p class="text-xs text-amber-800 mt-4 font-semibold leading-relaxed">
                                            Do you want to overwrite everyone to make them exactly the same, or keep these custom users as they are?
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row items-center gap-3 pt-3 border-t border-amber-200/50">
                                    <button type="button" @click="saveBulk(true)" class="w-full sm:flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 px-4 rounded-xl transition text-xs shadow-soft-xs text-center">
                                        Overwrite Everyone
                                    </button>
                                    <button type="button" @click="saveBulk(false)" class="w-full sm:flex-1 bg-white hover:bg-amber-50 text-amber-900 border border-amber-200 font-bold py-2.5 px-4 rounded-xl transition text-xs shadow-soft-xs text-center">
                                        Keep Custom Users
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- EDIT SCREEN -->
                        <div x-show="user || (bulkMode && bulkStep === 'edit')" class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">"""

content = content.replace(old_right_col_start, new_right_col_start)

# 2. Fix the Save button
old_button = """                            <button type="submit" :disabled="saving"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs shrink-0">"""
new_button = """                            <button type="button" @click="user ? savePermissions() : saveBulkPermissions()" :disabled="saving"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs shrink-0">"""

content = content.replace(old_button, new_button)

# 3. Close the new <div class="space-y-6"> before the </template>
old_end = """                            <!-- Role & User Permission -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Role & User Permission</span>
                                <input type="checkbox" x-model="permissions.nav_role_permission" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                        </div>
                    </div>
                </template>"""

new_end = """                            <!-- Role & User Permission -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Role & User Permission</span>
                                <input type="checkbox" x-model="permissions.nav_role_permission" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                        </div>
                    </div>
                    </div>
                </template>"""
content = content.replace(old_end, new_end)


with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed missing warning screen and broken submit button.")
