import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update left column: Replace Search & User List with Bulk Templates
old_left_start = '<!-- LEFT COLUMN: Search & User List -->'
old_left_end = '<!-- RIGHT COLUMN: Permissions Editor -->'
# I will use regex to extract and replace the entire left column content

pattern_left = r"(<!-- LEFT COLUMN: Search & User List -->\s*<div class=\"w-full lg:w-\[320px\] shrink-0 space-y-6\">)(.*?)(?=<!-- RIGHT COLUMN: Permissions Editor -->)"
new_left = r"""<!-- LEFT COLUMN: Bulk Template Management -->
            <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">
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
            </div>
            
            """

content = re.sub(pattern_left, new_left, content, flags=re.DOTALL)


# 2. Update the Right Column Template condition
old_right_show = '<template x-if="user">'
new_right_show = '<template x-if="user || bulkMode">'
content = content.replace(old_right_show, new_right_show)


# 3. Update the Right Column Header
old_header = """                                    <div>
                                        <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                                            <span>Access Control Settings</span>
                                        </h2>
                                        <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="user.email"></p>
                                    </div>"""
new_header = """                                    <div>
                                        <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                                            <span x-text="user ? 'Access Control Settings' : 'Bulk Editing: ' + (bulkMode === 'staff' ? 'All Staff' : 'All Admins')"></span>
                                        </h2>
                                        <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="user ? user.email : 'Applying template to all users in this role'"></p>
                                    </div>"""
content = content.replace(old_header, new_header)

# 4. Insert Warning Template & Hide Checkboxes conditionally
old_form_start = '<form @submit.prevent="savePermissions"'
new_form_start = """
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

                                <form x-show="user || (bulkMode && bulkStep === 'edit')" @submit.prevent="user ? savePermissions() : saveBulkPermissions()" """
content = content.replace(old_form_start, new_form_start)

# 5. Alpine JS additions
old_alpine = """                return {
                    searchEmail: '',
                    allUsersList: [],
                    displayedUsers: [],
                    userLimit: 5,
                    user: null,
                    error: '',
                    loading: false,
                    saving: false,
                    successMessage: '',"""

new_alpine = """                return {
                    bulkMode: null,
                    bulkStep: 'select',
                    divergentUsers: [],
                    bulkOverwrite: false,
                    user: null,
                    error: '',
                    loading: false,
                    saving: false,
                    successMessage: '',"""
# Wait, userList variables are completely removed now!
# Let's cleanly replace the entire Alpine function!

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Applied initial HTML rewrites.")
