file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Fix Right Column padding (space-y-6 -> flex flex-col gap-6)
old_right = """                <template x-if="user || bulkMode">
                    <div class="space-y-6">"""
new_right = """                <template x-if="user || bulkMode">
                    <div class="flex flex-col gap-6">"""
content = content.replace(old_right, new_right)

# 2. Compact Bulk Overwrite Container
old_bulk_overwrite = """                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold text-slate-800">Bulk Overwrite Users</h3>
                        </div>
                    </div>
                    <div class="p-4 sm:p-5 space-y-4">
                        <button type="button" @click="initBulk('staff', false)"
                            class="w-full text-left p-3.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'staff' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">All Staff</div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Overwrite existing staff</div>
                            </div>
                            <svg class="w-5 h-5 transition" :class="bulkMode === 'staff' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button type="button" @click="initBulk('admin', false)"
                            class="w-full text-left p-3.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'admin' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">All Admins</div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Overwrite existing admins</div>
                            </div>
                            <svg class="w-5 h-5 transition" :class="bulkMode === 'admin' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>"""

new_bulk_overwrite = """                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-800">Bulk Overwrite Users</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" @click="initBulk('staff', false)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'staff' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">All Staff</div>
                                <div class="text-[10px] text-slate-500 font-semibold">Overwrite existing staff</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'staff' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button type="button" @click="initBulk('admin', false)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'admin' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">All Admins</div>
                                <div class="text-[10px] text-slate-500 font-semibold">Overwrite existing admins</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'admin' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>"""
content = content.replace(old_bulk_overwrite, new_bulk_overwrite)

# 3. Compact Base Default Templates Container
old_base_default = """                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold text-slate-800">Base Default Templates</h3>
                        </div>
                    </div>
                    <div class="p-4 sm:p-5 space-y-4">
                        <button type="button" @click="initBulk('staff', true)"
                            class="w-full text-left p-3.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'staff_default' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">Staff Defaults</div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Set baseline for newly created staff</div>
                            </div>
                            <svg class="w-5 h-5 transition" :class="bulkMode === 'staff_default' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button type="button" @click="initBulk('admin', true)"
                            class="w-full text-left p-3.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'admin_default' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">Admin Defaults</div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Set baseline for newly created admins</div>
                            </div>
                            <svg class="w-5 h-5 transition" :class="bulkMode === 'admin_default' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>"""

new_base_default = """                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-800">Base Default Templates</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" @click="initBulk('staff', true)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'staff_default' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">Staff Defaults</div>
                                <div class="text-[10px] text-slate-500 font-semibold">Set baseline for newly created staff</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'staff_default' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button type="button" @click="initBulk('admin', true)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'admin_default' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">Admin Defaults</div>
                                <div class="text-[10px] text-slate-500 font-semibold">Set baseline for newly created admins</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'admin_default' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>"""

content = content.replace(old_base_default, new_base_default)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Applied UI fixes to make left containers compact and remove top margin gap.")
