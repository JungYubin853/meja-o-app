<x-app-layout title="Meja-O | Role & Permission">
    <main class="w-full max-w-[84rem] mx-auto px-4 sm:px-6 py-4 sm:py-6 space-y-6 min-h-[calc(100vh-4rem)]" x-data="permissionManager()">

        <div class="flex flex-col lg:flex-row gap-6 items-start w-full">
            
            <!-- LEFT COLUMN: Bulk Template Management & Search -->
            <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                
                <!-- Search User -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-3">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Search User') }}</label>
                    <form @submit.prevent="searchUser()" class="flex gap-2">
                        <input type="text" x-model="searchQuery" required placeholder="{{ __('Search name or email...') }}"
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

                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Bulk Overwrite Users') }}</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" @click="initBulk('staff', false)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'staff' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ __('All Staff') }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold">{{ __('Overwrite existing staff') }}</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'staff' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button type="button" @click="initBulk('admin', false)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'admin' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ __('All Admins') }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold">{{ __('Overwrite existing admins') }}</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'admin' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-800">{{ __('Base Default Templates') }}</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" @click="initBulk('staff', true)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'staff_default' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ __('Staff Defaults') }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold">{{ __('Set baseline for newly created staff') }}</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'staff_default' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button type="button" @click="initBulk('admin', true)"
                            class="w-full text-left p-2.5 rounded-xl border-2 transition group flex items-center justify-between"
                            :class="bulkMode === 'admin_default' ? 'border-slate-900 bg-slate-50' : 'border-slate-100 hover:border-slate-300'">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ __('Admin Defaults') }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold">{{ __('Set baseline for newly created admins') }}</div>
                            </div>
                            <svg class="w-4 h-4 transition" :class="bulkMode === 'admin_default' ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-900'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
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
                        <p class="text-xs text-slate-500 max-w-xs mx-auto" x-text="error ? error : 'Choose a bulk template on the left, or select a specific user from {{ __('Account Management') }} to edit their granular access permissions.'"></p>
                    </div>
                </template>

                <template x-if="user || bulkMode">
                    <div class="flex flex-col gap-6">
                        
                        <!-- EDIT SCREEN -->
                        <div x-show="user || bulkMode" class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                                    <span x-text="user ? '{{ __('Access Control Settings') }}' : (bulkMode.includes('default') ? '{{ __('Base Default Template: ') }}' : '{{ __('Bulk Overwrite Users: ') }}') + (bulkMode.includes('staff') ? 'Staff' : 'Admins')"></span>
                                    <template x-if="user && isUserDivergent">
                                        <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-200">Customized (!)</span>
                                    </template>
                                </h2>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5" x-text="user ? (user.name + ' (' + user.email + ')') : (bulkMode.includes('default') ? '{{ __('Changes define baseline defaults for newly created accounts.') }}' : '{{ __('Changes will overwrite permissions for all existing accounts in one go without altering the Base Default Template.') }}')"></p>
                            </div>
                            <button type="button" @click="user ? savePermissions() : saveBulkPermissions()" :disabled="saving"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs shrink-0 cursor-pointer">
                                <span x-show="!saving" x-text="user ? '{{ __('Save Permissions') }}' : (bulkMode.includes('default') ? '{{ __('Save Base Default Template') }}' : '{{ __('Apply Bulk Overwrite') }}')"></span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>
                        
                        <!-- Success Banner -->
                        <template x-if="successMsg">
                            <div class="p-3 mx-5 mt-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span x-text="successMsg"></span>
                            </div>
                        </template>

                        <!-- Informational Banner for Base Default Template -->
                        <template x-if="bulkMode && bulkMode.includes('default')">
                            <div class="p-3.5 mx-5 mt-5 bg-sky-50 border border-sky-200 text-sky-800 rounded-xl text-xs font-medium flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-bold text-sky-900">Baseline Template for Newly Created <span x-text="bulkMode.includes('staff') ? 'Staff' : 'Admin'"></span> Accounts</p>
                                    <p class="text-[11px] text-sky-700 mt-0.5">Whenever an account is created in Account Management, it automatically follows this Base Default Template. Existing accounts will remain unaffected.</p>
                                </div>
                            </div>
                        </template>

                        <!-- Informational Banner for Bulk Overwrite Users (Shows Divergent Accounts with !) -->
                        <template x-if="bulkMode && !bulkMode.includes('default')">
                            <div class="mx-5 mt-5">
                                <template x-if="divergentUsers.length > 0">
                                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-full bg-amber-200 text-amber-900 font-black text-xs flex items-center justify-center shrink-0">!</span>
                                                <h4 class="text-xs font-bold text-amber-900">
                                                    <span x-text="divergentUsers.length"></span> <span x-text="bulkMode === 'staff' ? 'Staff' : 'Admin'"></span> account(s) do not follow the Base Default Template
                                                </h4>
                                            </div>
                                            <span class="text-[10px] font-bold text-amber-800 bg-amber-200/60 px-2 py-0.5 rounded-full">Different Permissions</span>
                                        </div>
                                        <p class="text-[11px] text-amber-700 font-medium">
                                            The accounts below currently have permissions that differ from the Base Default Template. Checkboxes marked with <strong class="text-amber-800">(!)</strong> differ from the Base Default Template:
                                        </p>
                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                            <template x-for="du in divergentUsers" :key="du.id">
                                                <button type="button" @click="searchUser(du.email)"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white hover:bg-amber-100 border border-amber-200 rounded-lg text-[11px] font-bold text-amber-900 transition shadow-soft-2xs cursor-pointer"
                                                    title="Click to view/edit this specific user">
                                                    <span class="text-amber-600 font-black text-xs">(!)</span>
                                                    <span x-text="du.name"></span>
                                                    <span class="text-[10px] text-amber-600 font-normal" x-text="'(' + du.email + ')'"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="divergentUsers.length === 0">
                                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs font-semibold text-emerald-800">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>All existing <span x-text="bulkMode === 'staff' ? 'Staff' : 'Admin'"></span> accounts currently match the Base Default Template.</span>
                                        </div>
                                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-200">In Sync</span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Informational Banner for Single User Divergence -->
                        <template x-if="user && isUserDivergent">
                            <div class="p-3 mx-5 mt-5 bg-amber-50 border border-amber-200 rounded-xl text-xs font-medium text-amber-800 flex items-center gap-2">
                                <span class="text-amber-600 font-black text-sm">(!)</span>
                                <span>This user has customized permissions that differ from the <strong x-text="user.role === 'staff' ? 'Staff' : 'Admin'"></strong> Base Default Template (highlighted with <strong class="text-amber-600">(!)</strong> below).</span>
                            </div>
                        </template>

                        <div class="p-5 sm:p-7 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <!-- Dashboard -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="text-sm font-bold text-slate-800">Dashboard<span x-cloak x-show="differsFromDefault('nav_dashboard')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from default template">(!)</span></span>
                                    <input type="checkbox" x-model="permissions.nav_dashboard" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_dashboard" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <!-- 1. View Only -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('View Only') }}<span x-cloak x-show="differsFromDefault('dash_view_mode')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_view_mode" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 2. Table Edit -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Table Edit') }}<span x-cloak x-show="differsFromDefault('dash_table_edit')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_table_edit" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 3. Section Edit -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Section Edit') }}<span x-cloak x-show="differsFromDefault('dash_section_edit')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_section_edit" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 4. Create New Table Container -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Create New Table Container') }}<span x-cloak x-show="differsFromDefault('dash_create_table')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_create_table" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 5. Create New Section Container -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Create New Section Container') }}<span x-cloak x-show="differsFromDefault('dash_create_section')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_create_section" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 6. Canvas & Floor Settings -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Canvas & Floor Settings') }}<span x-cloak x-show="differsFromDefault('dash_canvas_settings')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_canvas_settings" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 7. Floor Dropdown -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Floor Dropdown') }}<span x-cloak x-show="differsFromDefault('dash_floor_dropdown')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_floor_dropdown" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 8. 2D Floor Canvas -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">2D Floor Canvas<span x-cloak x-show="differsFromDefault('dash_floor_canvas')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_floor_canvas" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 9. Guest Seating -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Guest Seating') }}<span x-cloak x-show="differsFromDefault('dash_guest_seating')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_guest_seating" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 10. Waitlist -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Waitlist') }}<span x-cloak x-show="differsFromDefault('dash_waitlist')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>

                                    <!-- 11. Edit Table Specs -->
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Edit Table Specs') }}<span x-cloak x-show="differsFromDefault('dash_edit_table')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.dash_edit_table" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- {{ __('Waitlist') }} -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">{{ __('Waitlist') }}<span x-cloak x-show="differsFromDefault('nav_waitlist')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_waitlist" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- {{ __('Reports & Database') }} -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="text-sm font-bold text-slate-800">{{ __('Reports & Database') }}<span x-cloak x-show="differsFromDefault('nav_reports')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                    <input type="checkbox" x-model="permissions.nav_reports" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_reports" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Overall') }}<span x-cloak x-show="differsFromDefault('rep_overall')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_overall" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Daily Report') }}<span x-cloak x-show="differsFromDefault('rep_daily')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_daily" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Weekly Report') }}<span x-cloak x-show="differsFromDefault('rep_weekly')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_weekly" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Monthly Report') }}<span x-cloak x-show="differsFromDefault('rep_monthly')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_monthly" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Yearly Report') }}<span x-cloak x-show="differsFromDefault('rep_yearly')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_yearly" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Waitlist') }} Report<span x-cloak x-show="differsFromDefault('rep_waitlist')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Customer Habits') }}<span x-cloak x-show="differsFromDefault('rep_habits')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.rep_habits" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- {{ __('Tutorial & Guidelines') }} -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">{{ __('Tutorial & Guidelines') }}<span x-cloak x-show="differsFromDefault('nav_tutorial')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_tutorial" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- {{ __('Calendar') }} -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">{{ __('Calendar') }}<span x-cloak x-show="differsFromDefault('nav_calendar')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_calendar" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- {{ __('Profile') }} -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="text-sm font-bold text-slate-800">{{ __('Profile') }}<span x-cloak x-show="differsFromDefault('nav_profile')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                    <input type="checkbox" x-model="permissions.nav_profile" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_profile" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer col-span-1 sm:col-span-2">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Forgot Password Recovery Requests') }}<span x-cloak x-show="differsFromDefault('prof_password_requests')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.prof_password_requests" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- {{ __('Account Management') }} -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="text-sm font-bold text-slate-800">{{ __('Account Management') }}<span x-cloak x-show="differsFromDefault('nav_account_mgmt')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                    <input type="checkbox" x-model="permissions.nav_account_mgmt" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_account_mgmt" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('Create New Account') }}<span x-cloak x-show="differsFromDefault('acc_create_account')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.acc_create_account" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">{{ __('User List') }}<span x-cloak x-show="differsFromDefault('acc_user_list')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                        <input type="checkbox" x-model="permissions.acc_user_list" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- {{ __('Role & User Permission') }} -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">{{ __('Role & User Permission') }}<span x-cloak x-show="differsFromDefault('nav_role_permission')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_role_permission" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- {{ __('Approval') }} -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">{{ __('Approval') }}<span x-cloak x-show="differsFromDefault('nav_approval')" class="text-amber-500 font-black ml-1.5 text-[11px]" title="Differs from Base Default Template">(!)</span></span>
                                <input type="checkbox" x-model="permissions.nav_approval" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                        </div>
                    </div>
                    </div>
                </template>
            </div>
        </div>
    </main>

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
                divergentUsers: [],
                searchQuery: '',

                differsFromDefault(key) {
                    if (this.bulkMode && this.bulkMode.includes('default')) {
                        return false;
                    }
                    return Boolean(this.permissions[key]) !== Boolean(this.defaultPerms[key]);
                },

                get isUserDivergent() {
                    if (!this.user || !this.defaultPerms) return false;
                    return Object.keys(this.defaultPerms).some(key => Boolean(this.permissions[key]) !== Boolean(this.defaultPerms[key]));
                },

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
                        
                        this.defaultPerms = {};
                        for (const [k, v] of Object.entries(data.default_permissions || {})) {
                            this.defaultPerms[k] = Boolean(v);
                        }

                        this.permissions = {};
                        for (const [k, v] of Object.entries(data.permissions || {})) {
                            this.permissions[k] = Boolean(v);
                        }

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
                    this.divergentUsers = [];
                    
                    try {
                        const response = await fetch(`/api/permissions/bulk-check?role=${role}`);
                        if (!response.ok) throw new Error('Failed to fetch template data.');
                        const data = await response.json();
                        
                        this.permissions = {};
                        this.defaultPerms = {};
                        for (const [k, v] of Object.entries(data.default_permissions || {})) {
                            this.defaultPerms[k] = Boolean(v);
                        }
                        
                        if (isBaseDefault) {
                            // Base Default Template: display and edit the baseline template
                            for (const [k, v] of Object.entries(data.default_permissions || {})) {
                                this.permissions[k] = Boolean(v);
                            }
                        } else {
                            // Bulk Overwrite Users: display the actual permissions of existing accounts
                            const source = data.current_permissions || data.default_permissions || {};
                            for (const [k, v] of Object.entries(source)) {
                                this.permissions[k] = Boolean(v);
                            }
                            this.divergentUsers = data.divergent_users || [];
                        }
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async savePermissions() {
                    this.saving = true;
                    this.successMsg = '';
                    this.error = '';

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const response = await fetch('/api/permissions/update/' + this.user.id, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ permissions: this.permissions })
                        });
                        
                        if (!response.ok) throw new Error('Failed to save permissions.');
                        
                        this.successMsg = '{{ __('Permissions updated successfully!') }}';
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
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ 
                                role: role,
                                permissions: this.permissions
                            })
                        });
                        
                        if (!response.ok) throw new Error('Failed to bulk save permissions.');
                        const data = await response.json();
                        
                        if (isBaseDefault) {
                            this.defaultPerms = { ...this.permissions };
                            this.successMsg = `Base default template for ${role}s successfully updated! Newly created accounts will inherit this template.`;
                        } else {
                            // Re-fetch bulk check to refresh divergent users list against Base Default Template
                            const checkRes = await fetch(`/api/permissions/bulk-check?role=${role}`);
                            if (checkRes.ok) {
                                const checkData = await checkRes.json();
                                this.divergentUsers = checkData.divergent_users || [];
                            }
                            this.successMsg = `Permissions successfully applied to all ${data.updated_count} ${role}(s)! Base Default Template was not altered.`;
                        }
                        
                        setTimeout(() => this.successMsg = '', 5000);
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

