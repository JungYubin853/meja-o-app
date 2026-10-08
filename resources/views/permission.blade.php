<x-app-layout title="Meja-O | Role & Permission">
    <main class="w-full max-w-[84rem] mx-auto px-4 sm:px-6 py-4 sm:py-6 space-y-6 min-h-[calc(100vh-4rem)]" x-data="permissionManager()">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: Search & User List -->
            <div class="lg:col-span-4 xl:col-span-4 space-y-6">
                
                <!-- Search Bar -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-soft-xs space-y-3">
                    <label class="block text-sm font-bold text-slate-900">Search User by Email</label>
                    <form @submit.prevent="searchUser" class="flex gap-2">
                        <input type="email" x-model="searchEmail" required placeholder="Enter user's email address..."
                            class="flex-1 text-xs p-3 bg-slate-50 border border-slate-300 rounded-xl font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                        <button type="submit" :disabled="loading"
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs">
                            <span x-show="!loading">Search</span>
                            <span x-show="loading">...</span>
                        </button>
                    </form>
                    
                    <template x-if="error">
                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold mt-2" x-text="error"></div>
                    </template>
                </div>

                <!-- User List -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">User List</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Click a user to configure.</p>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-1 rounded-full border border-slate-200" x-text="allUsersList.length + ' Users'"></span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[9px] font-bold text-slate-400 tracking-wider">SHOW</span>
                                <select x-model.number="userLimit" class="text-[10px] font-bold text-slate-700 bg-white border border-slate-200 rounded px-1.5 py-0.5 focus:outline-none cursor-pointer">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto touch-scroll">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                            <thead class="sticky top-0 bg-slate-50 z-10 shadow-sm border-b border-slate-200">
                                <tr class="text-slate-700 font-semibold">
                                    <th class="p-3 sm:p-4">Name / Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="u in displayedUsers" :key="u.id">
                                    <tr class="hover:bg-slate-50 transition cursor-pointer" 
                                        @click="searchEmail = u.email; searchUser(); window.scrollTo({top: 0, behavior: 'smooth'});">
                                        <td class="p-3 sm:p-4">
                                            <div class="font-bold text-slate-900 text-sm" x-text="u.name"></div>
                                            <div class="text-slate-500 text-[11px] mt-0.5" x-text="u.email"></div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Permissions Editor -->
            <div class="lg:col-span-8 xl:col-span-8">
                
                <template x-if="!user && !loading">
                    <div class="bg-slate-50 border border-slate-200/80 border-dashed rounded-2xl p-10 flex flex-col items-center justify-center text-center h-full min-h-[300px] shadow-soft-xs">
                        <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                        <h3 class="text-sm font-bold text-slate-700 mb-1" x-text="error ? 'No User Found!' : 'No User Selected'"></h3>
                        <p class="text-xs text-slate-500 max-w-xs mx-auto" x-text="error ? 'We couldn\'t find an account matching that email address.' : 'Select a user from the list on the left or search by email to edit their granular access permissions.'"></p>
                    </div>
                </template>

                <template x-if="user">
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Access Control Settings</h2>
                                <p class="text-sm font-semibold text-slate-500 mt-0.5" x-text="user.email"></p>
                            </div>
                            <button @click="savePermissions" :disabled="saving"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-6 py-2.5 rounded-xl transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-sm shrink-0">
                                <span x-show="!saving">Save Permissions</span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>
                        
                        <template x-if="successMsg">
                            <div class="p-3 mx-5 mt-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold flex items-center gap-2">
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
                                    <span class="font-bold text-slate-800">Dashboard</span>
                                    <input type="checkbox" x-model="permissions.nav_dashboard" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_dashboard" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Create New Table Container</span>
                                        <input type="checkbox" x-model="permissions.dash_create_table" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">2D Floor Canvas</span>
                                        <input type="checkbox" x-model="permissions.dash_floor_canvas" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Guest Seating</span>
                                        <input type="checkbox" x-model="permissions.dash_guest_seating" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Waitlist</span>
                                        <input type="checkbox" x-model="permissions.dash_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Edit Table Specs</span>
                                        <input type="checkbox" x-model="permissions.dash_edit_table" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- Waitlist -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="font-bold text-slate-800">Waitlist</span>
                                <input type="checkbox" x-model="permissions.nav_waitlist" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Reports & Database -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="font-bold text-slate-800">Reports & Database</span>
                                    <input type="checkbox" x-model="permissions.nav_reports" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_reports" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Overall</span>
                                        <input type="checkbox" x-model="permissions.rep_overall" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Daily Report</span>
                                        <input type="checkbox" x-model="permissions.rep_daily" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Monthly Report</span>
                                        <input type="checkbox" x-model="permissions.rep_monthly" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Yearly Report</span>
                                        <input type="checkbox" x-model="permissions.rep_yearly" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Waitlist Report</span>
                                        <input type="checkbox" x-model="permissions.rep_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- Profile -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="font-bold text-slate-800">Profile</span>
                                <input type="checkbox" x-model="permissions.nav_profile" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Account Management -->
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                    <span class="font-bold text-slate-800">Account Management</span>
                                    <input type="checkbox" x-model="permissions.nav_account_mgmt" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                </label>
                                
                                <div x-show="permissions.nav_account_mgmt" x-transition class="pl-6 sm:pl-10 grid grid-cols-1 sm:grid-cols-2 gap-2 border-l-2 border-slate-100 ml-4">
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">Create New Account</span>
                                        <input type="checkbox" x-model="permissions.acc_create_account" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-sm font-semibold text-slate-700">User List</span>
                                        <input type="checkbox" x-model="permissions.acc_user_list" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                </div>
                            </div>

                            <!-- Role & User Permission -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="font-bold text-slate-800">Role & User Permission</span>
                                <input type="checkbox" x-model="permissions.nav_role_permission" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
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
                allUsersList: @json($allUsers),
                userLimit: 5,
                searchEmail: '',
                loading: false,
                saving: false,
                error: '',
                successMsg: '',
                user: null,
                permissions: {},

                init() {
                    const params = new URLSearchParams(window.location.search);
                    const email = params.get('email');
                    if (email) {
                        this.searchEmail = email;
                        this.searchUser();
                    }
                },

                get displayedUsers() {
                    return this.allUsersList.slice(0, parseInt(this.userLimit));
                },

                async searchUser() {
                    this.error = '';
                    this.successMsg = '';
                    this.loading = true;
                    this.user = null;
                    
                    try {
                        const response = await fetch('/api/permissions/search?email=' + encodeURIComponent(this.searchEmail));
                        if (!response.ok) {
                            throw new Error('User not found.');
                        }
                        const data = await response.json();
                        if (data.role === 'super_admin') {
                            throw new Error('Cannot edit Super Admin permissions.');
                        }
                        
                        this.user = data;
                        
                        // Default structure if user has no specific permissions saved yet
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
                            nav_profile: data.role === 'admin' || data.role === 'staff',
                            nav_account_mgmt: data.role === 'admin',
                            acc_create_account: false,
                            acc_user_list: data.role === 'admin',
                            nav_role_permission: false
                        };
                        
                        // Merge saved permissions with defaults
                        this.permissions = { ...defaultPerms, ...data.permissions };

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
                        const response = await fetch('/api/permissions/update/' + this.user.id, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ permissions: this.permissions })
                        });
                        
                        if (!response.ok) throw new Error('Failed to save permissions.');
                        
                        this.successMsg = 'Permissions updated successfully!';
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

