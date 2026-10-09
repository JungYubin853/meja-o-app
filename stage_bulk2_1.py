file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Update searchUser to handle defaultPerms
old_alpine_start = """                user: null,
                permissions: {},

                bulkMode: null,"""
new_alpine_start = """                user: null,
                permissions: {},
                defaultPerms: {},

                bulkMode: null,"""
content = content.replace(old_alpine_start, new_alpine_start)

# 2. Extract default_permissions when searching user
old_search_perms = """                        this.permissions = { ...defaultPerms, ...data.permissions };

                    } catch (err) {"""
new_search_perms = """                        this.defaultPerms = data.default_permissions || defaultPerms;
                        this.permissions = { ...this.defaultPerms, ...data.permissions };

                    } catch (err) {"""
content = content.replace(old_search_perms, new_search_perms)

# 3. Add Manage Button to Divergent Users List
old_li = """                                                <li class="text-[11px] font-bold text-amber-900 flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                    <span x-text="du.name + ' (' + du.email + ')'"></span>
                                                </li>"""
new_li = """                                                <li class="text-[11px] font-bold text-amber-900 flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                        <span x-text="du.name + ' (' + du.email + ')'"></span>
                                                    </div>
                                                    <button type="button" @click="searchUser(du.email)" class="w-7 h-7 bg-amber-200 hover:bg-amber-300 rounded-lg flex items-center justify-center transition shrink-0" title="Manage this specific user">
                                                        <svg class="w-3.5 h-3.5 text-amber-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    </button>
                                                </li>"""
content = content.replace(old_li, new_li)

# 4. Add "Base Default Templates" Block
old_left_end = """                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold text-slate-800">Bulk Template Management</h3>
                        </div>
                    </div>
                    <div class="p-4 sm:p-5 space-y-4">
                        <button type="button" @click="initBulk('staff')"
                            class="w-full text-left p-3.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'staff' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">All Staff</div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Manage default permissions for staff</div>
                            </div>
                            <svg class="w-5 h-5 transition" :class="bulkMode === 'staff' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button type="button" @click="initBulk('admin')"
                            class="w-full text-left p-3.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'admin' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">All Admins</div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Manage default permissions for admins</div>
                            </div>
                            <svg class="w-5 h-5 transition" :class="bulkMode === 'admin' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>"""

new_left_end = """                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">
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
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">
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
                </div>
            </div>"""
content = content.replace(old_left_end, new_left_end)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Alpine state, Warning List Button, and Base Templates Block.")
