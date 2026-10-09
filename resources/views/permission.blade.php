<x-app-layout title="Meja-O | Role & Permission">
    <main class="w-full max-w-[84rem] mx-auto px-4 sm:px-6 py-4 sm:py-6 space-y-6 min-h-[calc(100vh-4rem)]" x-data="permissionManager()">

        <div class="flex flex-col lg:flex-row gap-6 items-start w-full">
            
            <!-- LEFT COLUMN: Bulk Template Management & Search -->
            <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                
                <!-- Search User -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-3">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Search User</label>
                    <form @submit.prevent="searchUser()" class="flex gap-2">
                        <input type="text" x-model="searchQuery" required placeholder="Search name or email..."
                            class="flex-1 w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                        <button type="submit" :disabled="loading"
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold w-10 h-[38px] rounded-xl flex items-center justify-center transition shadow-soft-xs active:scale-98 disabled:opacity-50 shrink-0">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </form>
                    <template x-if="error">
                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold mt-2" x-text="error"></div>
                    </template>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">
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
            </div>
            
            <!-- RIGHT COLUMN: Permissions Editor -->
            <div class="flex-1 min-w-0">
                
                <template x-if="!user && !bulkMode && !loading">
                    <div class="bg-slate-50 border border-slate-200/80 border-dashed rounded-2xl p-10 flex flex-col items-center justify-center text-center h-full min-h-[300px] shadow-soft-xs">
                        <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                        <h3 class="text-sm font-bold text-slate-700 mb-1" x-text="error ? 'Error Encountered!' : 'No Target Selected'"></h3>
                        <p class="text-xs text-slate-500 max-w-xs mx-auto" x-text="error ? error : 'Choose a bulk template on the left, or select a specific user from Account Management to edit their granular access permissions.'"></p>
                    </div>
                </template>

                <template x-if="user || bulkMode">
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
                                                <li class="text-[11px] font-bold text-amber-900 flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                        <span x-text="du.name + ' (' + du.email + ')'"></span>
                                                    </div>
                                                    <button type="button" @click="searchUser(du.email)" class="w-7 h-7 bg-amber-200 hover:bg-amber-300 rounded-lg flex items-center justify-center transition shrink-0" title="Manage this specific user">
                                                        <svg class="w-3.5 h-3.5 text-amber-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    </button>
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
                        <div x-show="user || (bulkMode && bulkStep === 'edit')" class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                                    <span x-text="user ? 'Access Control Settings' : (bulkMode.includes('default') ? 'Edit Base Default: ' : 'Bulk Overwrite: ') + (bulkMode.includes('staff') ? 'Staff' : 'Admins')"></span>
                                </h2>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5" x-text="user ? user.email : (bulkMode.includes('default') ? 'Changes will apply to newly created accounts.' : 'Changes will overwrite all existing accounts.')"></p>
                            </div>
                            <button type="button" @click="user ? savePermissions() : saveBulkPermissions()" :disabled="saving"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs shrink-0">
                                <span x-show="!saving">Save Permissions</span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>
                        
                        <template x-if="successMsg">
                            <div class="p-3 mx-5 mt-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span x-text="successMsg"></span>
                            </div>
                        </template>

                        <div class="p-5 sm:p-7 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <!-- Dashboard -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="text-sm font-bold text-slate-800">Dashboard<span x-cloak x-show="user && permissions.nav_dashboard !== defaultPerms.nav_dashboard" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                    <input type="checkbox" x-model="permissions.nav_dashboard" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_dashboard" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Create New Table Container<span x-cloak x-show="user && permissions.dash_create_table !== defaultPerms.dash_create_table" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_create_table" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">2D Floor Canvas<span x-cloak x-show="user && permissions.dash_floor_canvas !== defaultPerms.dash_floor_canvas" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_floor_canvas" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Guest Seating<span x-cloak x-show="user && permissions.dash_guest_seating !== defaultPerms.dash_guest_seating" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_guest_seating" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Waitlist<span x-cloak x-show="user && permissions.dash_waitlist !== defaultPerms.dash_waitlist" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Edit Table Specs<span x-cloak x-show="user && permissions.dash_edit_table !== defaultPerms.dash_edit_table" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_edit_table" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- Waitlist -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Waitlist<span x-cloak x-show="user && permissions.nav_waitlist !== defaultPerms.nav_waitlist" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_waitlist" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Reports & Database -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="text-sm font-bold text-slate-800">Reports & Database<span x-cloak x-show="user && permissions.nav_reports !== defaultPerms.nav_reports" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                    <input type="checkbox" x-model="permissions.nav_reports" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_reports" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Overall<span x-cloak x-show="user && permissions.rep_overall !== defaultPerms.rep_overall" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_overall" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Daily Report<span x-cloak x-show="user && permissions.rep_daily !== defaultPerms.rep_daily" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_daily" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Monthly Report<span x-cloak x-show="user && permissions.rep_monthly !== defaultPerms.rep_monthly" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_monthly" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Yearly Report<span x-cloak x-show="user && permissions.rep_yearly !== defaultPerms.rep_yearly" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_yearly" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Waitlist Report<span x-cloak x-show="user && permissions.rep_waitlist !== defaultPerms.rep_waitlist" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Customer Habits<span x-cloak x-show="user && permissions.rep_habits !== defaultPerms.rep_habits" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_habits" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- Tutorial & Guidelines -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Tutorial & Guidelines<span x-cloak x-show="user && permissions.nav_tutorial !== defaultPerms.nav_tutorial" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_tutorial" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Calendar -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Calendar<span x-cloak x-show="user && permissions.nav_calendar !== defaultPerms.nav_calendar" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_calendar" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Profile -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Profile<span x-cloak x-show="user && permissions.nav_profile !== defaultPerms.nav_profile" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_profile" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Account Management -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="text-sm font-bold text-slate-800">Account Management<span x-cloak x-show="user && permissions.nav_account_mgmt !== defaultPerms.nav_account_mgmt" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                    <input type="checkbox" x-model="permissions.nav_account_mgmt" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_account_mgmt" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Create New Account<span x-cloak x-show="user && permissions.acc_create_account !== defaultPerms.acc_create_account" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.acc_create_account" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">User List<span x-cloak x-show="user && permissions.acc_user_list !== defaultPerms.acc_user_list" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.acc_user_list" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- Role & User Permission -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Role & User Permission<span x-cloak x-show="user && permissions.nav_role_permission !== defaultPerms.nav_role_permission" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_role_permission" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                        </div>
                    </div>
                    </div>
                </template>
            </div>
        </div>
    </main>
'
$script = @'
        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('permissionManager', () => ({
                loading: false,
                saving: false,
                error: '',
                successMsg: '',
                user: null,
                permissions: {},
                defaultPerms: {},

                bulkMode: null,
                bulkStep: 'select',
                divergentUsers: [],
                bulkOverwrite: false,
                searchQuery: '',

                init() {
                    const params = new URLSearchParams(window.location.search);
                    const email = params.get('email');
                    if (email) {
                        this.searchUser(email);
                    }
                },

                async searchUser(query = null) {
                    const term = query || this.searchQuery;
                    if (!term) return;
                    
                    this.error = '';
                    this.successMsg = '';
                    this.loading = true;
                    this.user = null;
                    this.bulkMode = null;
                    
                    try {
                        const response = await fetch('/api/permissions/search?email=' + encodeURIComponent(term));
                        if (!response.ok) {
                            throw new Error('User not found.');
                        }
                        const data = await response.json();
                        if (data.role === 'super_admin') {
                            throw new Error('Cannot edit Super Admin permissions.');
                        }
                        
                        this.user = data;
                        
                        const defaultPerms = {
                            nav_dashboard: data.role === 'admin' || data.role === 'staff',
                            dash_create_table: data.role === 'admin',
                            dash_floor_canvas: data.role === 'admin' || data.role === 'staff',
                            dash_guest_seating: data.role === 'admin' || data.role === 'staff',
                            dash_waitlist: data.role === 'admin' || data.role === 'staff',
                            dash_edit_table: data.role === 'admin',
                            nav_waitlist: data.role === 'admin' || data.role === 'staff',
                            nav_reports: data.role === 'admin' || data.role === 'staff',
                            rep_overall: data.role === 'admin',
                            rep_daily: data.role === 'admin' || data.role === 'staff',
                            rep_monthly: data.role === 'admin',
                            rep_yearly: data.role === 'admin',
                            rep_waitlist: data.role === 'admin' || data.role === 'staff',
                            rep_habits: data.role === 'admin',
                            nav_tutorial: data.role === 'admin' || data.role === 'staff',
                            nav_calendar: data.role === 'admin' || data.role === 'staff',
                            nav_profile: data.role === 'admin' || data.role === 'staff',
                            nav_account_mgmt: data.role === 'admin',
                            acc_create_account: false,
                            acc_user_list: data.role === 'admin',
                            nav_role_permission: false
                        };
                        
                        this.defaultPerms = data.default_permissions || defaultPerms;
                        this.permissions = { ...this.defaultPerms, ...data.permissions };

                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async initBulk(role, isBaseDefault = false) {
                    this.loading = true;
                    this.error = '';
                    this.successMsg = '';
                    this.user = null;
                    this.bulkMode = isBaseDefault ? role + '_default' : role;
                    
                    try {
                        const response = await fetch(`/api/permissions/bulk-check?role=${role}`);
                        if (!response.ok) throw new Error('Failed to fetch bulk data.');
                        const data = await response.json();
                        
                        this.permissions = data.default_permissions;
                        this.defaultPerms = data.default_permissions;
                        
                        if (isBaseDefault) {
                            this.bulkStep = 'edit';
                            this.bulkOverwrite = false;
                        } else {
                            this.divergentUsers = data.divergent_users;
                            if (this.divergentUsers.length > 0) {
                                this.bulkStep = 'warn';
                            } else {
                                this.bulkStep = 'edit';
                                this.bulkOverwrite = true;
                            }
                        }
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                saveBulk(overwrite) {
                    this.bulkOverwrite = overwrite;
                    this.bulkStep = 'edit';
                },

                async savePermissions() {
                    this.saving = true;
                    this.successMsg = '';
                    this.error = '';

                    try {
                        const response = await fetch('/api/permissions/update/' + this.user.id, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ permissions: this.permissions })
                        });
                        
                        if (!response.ok) throw new Error('Failed to save permissions.');
                        
                        this.successMsg = 'Permissions updated successfully!';
                        setTimeout(() => this.successMsg = '', 3000);
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.saving = false;
                    }
                },

                async saveBulkPermissions() {
                    this.saving = true;
                    this.successMsg = '';
                    this.error = '';

                    try {
                        const isBaseDefault = this.bulkMode.endsWith('_default');
                        const role = isBaseDefault ? this.bulkMode.replace('_default', '') : this.bulkMode;
                        const endpoint = isBaseDefault ? '/api/permissions/save-default-template' : '/api/permissions/bulk-update';

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ 
                                role: role,
                                permissions: this.permissions,
                                overwrite_custom: this.bulkOverwrite
                            })
                        });
                        
                        if (!response.ok) throw new Error('Failed to bulk save permissions.');
                        const data = await response.json();
                        
                        if (isBaseDefault) {
                            this.successMsg = `Global default template for ${role}s successfully updated!`;
                        } else {
                            this.successMsg = `Permissions successfully applied to ${data.updated_count} ${role}s!`;
                        }
                        
                        setTimeout(() => this.successMsg = '', 4000);
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.saving = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>

