<x-app-layout title="Meja-O | User Profile">

    <main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 min-h-screen"
        x-data="{ 
            showChangePasswordModal: {{ $errors->has('current_password') || $errors->has('new_password') ? 'true' : 'false' }},
            showMyPassword: false,
            adminModalReqId: null,
            adminModalUserName: '',
            adminModalUserEmail: ''
        }">

        <!-- Feedback Alerts -->
        @if (session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('success_password'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>{{ session('success_password') }}</span>
            </div>
        @endif

        @if (session('success_req'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success_req') }}</span>
            </div>
        @endif

        @if ($errors->any() && !$errors->has('current_password') && !$errors->has('new_password'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-bold shadow-2xs">
                {{ $errors->first() }}
            </div>
        @endif

        @php
            $hasSearch = Auth::user()->hasPermission('prof_search_bar') && Auth::user()->hasPermission('prof_password_requests');
            $hasAccountInfo = Auth::user()->hasPermission('prof_account_info');
            $hasLeft = $hasSearch || $hasAccountInfo;
        @endphp

        <!-- Two Column Layout: Account Information on Left, Password Request on Right -->
        <div class="flex flex-col lg:flex-row gap-6 items-start w-full">

            @if ($hasLeft)
            <!-- 1. ACCOUNT INFORMATION / SEARCH CARD (Left Sidebar) -->
            <div class="w-full {{ Auth::user()->hasPermission('prof_password_requests') ? 'lg:w-[320px]' : 'lg:max-w-xl mx-auto' }} shrink-0 flex flex-col gap-4">

                @if ($hasSearch)
                    <!-- Search User Container (Height & styling identical to account-management) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs flex items-center min-h-[64px] sm:h-[64px]">
                        <form method="GET" action="/profile" class="flex items-center gap-2 w-full m-0">
                            @if(request('req_status'))
                                <input type="hidden" name="req_status" value="{{ request('req_status') }}">
                            @endif
                            @if(request('direction'))
                                <input type="hidden" name="direction" value="{{ request('direction') }}">
                            @endif
                            @if(request('outlet_id'))
                                <input type="hidden" name="outlet_id" value="{{ request('outlet_id') }}">
                            @endif
                            @if(request('role'))
                                <input type="hidden" name="role" value="{{ request('role') }}">
                            @endif
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search name or email...') }}"
                                class="flex-1 w-full text-xs h-8 px-2.5 bg-slate-50 border border-slate-300 rounded-lg font-medium text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900">
                            <button type="submit"
                                class="bg-slate-900 hover:bg-slate-800 text-white font-bold w-8 h-8 rounded-lg flex items-center justify-center transition shadow-2xs active:scale-95 shrink-0"
                                title="{{ __('Search') }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
                    </div>
                @endif

                @if ($hasAccountInfo)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-800">{{ __('Account Information') }}</h2>
                    </div>

                    <form action="/profile" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex flex-col gap-4">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Full Name') }}</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                            </div>

                            <!-- Email Address (Locked) -->
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Email Address') }}</label>
                                <div class="relative">
                                    <input type="email" value="{{ $user->email }}" disabled
                                        class="w-full text-xs p-2.5 bg-slate-100 border border-slate-200 rounded-xl font-semibold text-slate-500 cursor-not-allowed">
                                    <svg class="w-4 h-4 text-slate-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                            </div>

                            <!-- Assigned Brand / Outlet -->
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Assigned Brand / Outlet') }}</label>
                                <input type="text" value="{{ $user->isSuperAdmin() ? 'ALL OUTLETS' : ($user->outlet->name ?? 'None') }}" disabled
                                    class="w-full text-xs p-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-600 cursor-not-allowed">
                            </div>

                            <!-- Current Password Display with "See Password" toggle -->
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Current Password') }}</label>
                                <div class="relative">
                                    <input :type="showMyPassword ? 'text' : 'password'" 
                                           value="{{ $user->getViewablePassword() ?? '********' }}" 
                                           readonly
                                           class="w-full text-xs p-2.5 bg-slate-100 border border-slate-200 rounded-xl font-mono font-bold text-slate-700 cursor-default select-all pr-9">
                                    
                                    <button type="button" @click="showMyPassword = !showMyPassword"
                                        class="absolute right-2.5 top-2.5 p-0.5 text-slate-400 hover:text-slate-900 transition"
                                        :title="showMyPassword ? 'Hide password' : 'See current password'">
                                        <svg x-show="!showMyPassword" class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showMyPassword" x-cloak class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Action Buttons: Change Password & Save (Same size & neat) -->
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="showChangePasswordModal = true"
                                class="w-full bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 text-xs font-bold py-2.5 px-2 rounded-xl transition shadow-2xs active:scale-98 flex items-center justify-center gap-1.5 text-center">
                                <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                <span>{{ __('Change Password') }}</span>
                            </button>

                            <button type="submit"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-2.5 px-2 rounded-xl transition shadow-soft-xs active:scale-98 flex items-center justify-center gap-1.5 text-center">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ __('Save') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            @endif

            <!-- 2. FORGOT PASSWORD REQUESTS (Right Column - Admin & Super Admin) -->
            @if (Auth::user()->hasPermission('prof_password_requests'))
                <div class="flex-1 min-w-0 w-full max-w-full flex flex-col gap-4" x-data="{
                    currentPage: 1,
                    perPage: 5,
                    compactView: true,
                    compactOutlet: true,
                    showColumnModal: false,
                    cols: {
                        user: {{ Auth::user()->hasPermission('prof_col_user') ? 'true' : 'false' }},
                        outlet: {{ Auth::user()->hasPermission('prof_col_outlet') ? 'true' : 'false' }},
                        role: {{ Auth::user()->hasPermission('prof_col_role') ? 'true' : 'false' }},
                        submitted: {{ Auth::user()->hasPermission('prof_col_submitted') ? 'true' : 'false' }},
                        password: {{ Auth::user()->hasPermission('prof_col_password') ? 'true' : 'false' }},
                        actions: {{ Auth::user()->hasPermission('prof_col_actions') ? 'true' : 'false' }},
                    },
                    tempCols: {},
                    openColumnModal() {
                        this.tempCols = { ...this.cols };
                        this.showColumnModal = true;
                    },
                    async saveColumns() {
                        this.cols = { ...this.tempCols };
                        this.showColumnModal = false;
                        try {
                            await fetch('/api/user/columns', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    columns: {
                                        prof_col_user: this.cols.user,
                                        prof_col_outlet: this.cols.outlet,
                                        prof_col_role: this.cols.role,
                                        prof_col_submitted: this.cols.submitted,
                                        prof_col_password: this.cols.password,
                                        prof_col_actions: this.cols.actions,
                                    }
                                })
                            });
                        } catch (e) {
                            console.error('Failed to save columns', e);
                        }
                    },
                    totalItems: {{ count($resetRequests) }},
                    get totalPages() { return Math.ceil(this.totalItems / this.perPage) || 1; },
                    nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                    prevPage() { if (this.currentPage > 1) this.currentPage--; },
                    goToPage(p) { if (p >= 1 && p <= this.totalPages) this.currentPage = p; },
                    get pageRange() {
                        const total = this.totalPages;
                        const current = this.currentPage;
                        const delta = 2;
                        if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
                        const range = [];
                        const left = Math.max(2, current - delta);
                        const right = Math.min(total - 1, current + delta);
                        range.push(1);
                        if (left > 2) range.push('...');
                        for (let i = left; i <= right; i++) range.push(i);
                        if (right < total - 1) range.push('...');
                        range.push(total);
                        return range;
                    }
                }">
                    <!-- Container 1: Title, Records Badge, All Outlets, All Roles -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-soft-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 min-h-[64px] sm:h-[64px] w-full max-w-full">
                        <div class="flex items-center gap-2.5">
                            <h3 class="text-sm font-bold text-slate-800">{{ __('Password Request') }}</h3>
                            <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200 tabular-nums">
                                {{ count($resetRequests) }} {{ __('Records') }}
                            </span>
                        </div>

                        <!-- Filters: Outlets and Roles (Identical to account-management) -->
                        <form method="GET" action="/profile" class="flex items-center gap-2 w-full sm:w-auto flex-wrap sm:flex-nowrap m-0">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('req_status'))
                                <input type="hidden" name="req_status" value="{{ request('req_status') }}">
                            @endif
                            @if(request('direction'))
                                <input type="hidden" name="direction" value="{{ request('direction') }}">
                            @endif
                            @if(Auth::user()->isSuperAdmin())
                                <select name="outlet_id" onchange="this.form.submit()" class="h-8 text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-0 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer flex-1 sm:flex-initial min-w-0">
                                    <option value="">{{ __('All Outlets') }}</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select disabled class="h-8 text-xs font-semibold text-slate-600 bg-slate-100 border border-slate-200 rounded-lg px-2.5 py-0 cursor-not-allowed flex-1 sm:flex-initial min-w-0 opacity-90"
                                    title="{{ __('Assigned Outlet (Read-only)') }}">
                                    <option selected>{{ Auth::user()->outlet->name ?? __('No Outlet Assigned') }}</option>
                                </select>
                            @endif
                            <select name="role" onchange="this.form.submit()" class="h-8 text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-0 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer flex-1 sm:flex-initial min-w-0">
                                <option value="">{{ __('All Roles') }}</option>
                                @if (Auth::user()->isSuperAdmin())
                                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                @endif
                                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                            </select>
                        </form>
                    </div>

                    <!-- Container 2: Request Status Tabs (All, Resolved, Pending) & Settings Gear -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-soft-xs flex flex-row items-center justify-between gap-2 min-h-[56px] sm:h-[64px] w-full max-w-full">
                        @php $reqStatus = request('req_status', 'all'); @endphp
                        <!-- Status Tabs: All, Resolved, Pending -->
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-semibold overflow-x-auto shrink-0 max-w-[calc(100%-44px)] sm:max-w-none">
                            <a href="{{ request()->fullUrlWithQuery(['req_status' => 'all']) }}" 
                               class="px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ ($reqStatus === 'all' || !$reqStatus) ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                                {{ __('All') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['req_status' => 'resolved']) }}" 
                               class="px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ $reqStatus === 'resolved' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                                {{ __('Resolved') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['req_status' => 'pending']) }}" 
                               class="flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ $reqStatus === 'pending' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                                <span>{{ __('Pending') }}</span>
                                @if ($pendingCount > 0)
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-extrabold bg-amber-500 text-white shadow-2xs aspect-square shrink-0">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        <!-- Column Settings Button (Gear Icon) -->
                        <button type="button" @click="openColumnModal()"
                            class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 hover:text-slate-900 flex items-center justify-center transition shadow-2xs shrink-0 cursor-pointer text-xs font-semibold"
                            title="{{ __('Customize Columns') }}">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Below Container: Table & Pagination Controls -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col w-full max-w-full min-w-0">
                        <!-- Top Controls Bar -->
                        <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Rows per page:') }}</span>
                                <select x-model.number="perPage" @change="currentPage = 1" class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            <template x-if="totalPages > 1">
                                <div class="flex items-center gap-1 overflow-x-auto py-1">
                                    <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                        :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>

                                    <template x-for="(page, idx) in pageRange" :key="idx">
                                        <span>
                                            <template x-if="page === '...'">
                                                <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                            </template>
                                            <template x-if="page !== '...'">
                                                <button type="button" @click="goToPage(page)"
                                                    :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                                    x-text="page">
                                                </button>
                                            </template>
                                        </span>
                                    </template>

                                    <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                        :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="overflow-x-auto touch-scroll w-full max-w-full">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                        <th x-show="cols.user" class="py-2.5 px-3 sm:px-4 whitespace-nowrap min-w-[120px]">
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ __('User') }}</span>
                                                <button type="button" @click="compactView = !compactView"
                                                    class="inline-flex items-center gap-0.5 text-[8px] font-bold px-1 py-0.5 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-800 transition cursor-pointer select-none leading-none shadow-2xs"
                                                    :title="compactView ? '{{ __('Show full details') }}' : '{{ __('Minimize details') }}'">
                                                    <span x-text="compactView ? '{{ __('Show Full') }}' : '{{ __('Minimize') }}'"></span>
                                                    <svg class="w-2 h-2 transition-transform" :class="compactView ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </th>
                                        <th x-show="cols.outlet" class="py-2.5 px-3 sm:px-4 whitespace-nowrap min-w-[120px]">
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ __('Outlet') }}</span>
                                                <button type="button" @click="compactOutlet = !compactOutlet"
                                                    class="inline-flex items-center gap-0.5 text-[8px] font-bold px-1 py-0.5 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-800 transition cursor-pointer select-none leading-none shadow-2xs"
                                                    :title="compactOutlet ? '{{ __('Show full outlet details') }}' : '{{ __('Minimize outlet details') }}'">
                                                    <span x-text="compactOutlet ? '{{ __('Show Full') }}' : '{{ __('Minimize') }}'"></span>
                                                    <svg class="w-2 h-2 transition-transform" :class="compactOutlet ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </th>
                                        <th x-show="cols.role" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">{{ __('Role') }}</th>
                                        <th x-show="cols.submitted" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                                            <a href="{{ request()->fullUrlWithQuery(['direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                               class="inline-flex items-center gap-1.5 text-slate-500 hover:text-slate-900 group cursor-pointer transition select-none"
                                               title="{{ request('direction') === 'asc' ? __('Sorted Oldest First. Click for Newest First.') : __('Sorted Newest First. Click for Oldest First.') }}">
                                                <span>{{ __('Submitted') }}</span>
                                                <span class="inline-flex flex-col -space-y-1">
                                                    <svg class="w-2.5 h-2.5 {{ request('direction') === 'asc' ? 'text-slate-900 font-extrabold' : 'text-slate-300 group-hover:text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <svg class="w-2.5 h-2.5 {{ request('direction') !== 'asc' ? 'text-slate-900 font-extrabold' : 'text-slate-300 group-hover:text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </a>
                                        </th>
                                        <th x-show="cols.password" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">{{ __('Current Password') }}</th>
                                        <th x-show="cols.actions" class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($resetRequests as $index => $req)
                                        @php $reqUser = $req->user; @endphp
                                        <tr class="hover:bg-slate-50/60 transition" 
                                            x-show="currentPage === Math.ceil(({{ $index }} + 1) / perPage)" 
                                            x-cloak 
                                            x-data="{ showReqPass: false }">
                                    <!-- User Info -->
                                    <td x-show="cols.user" class="py-3 px-3 sm:px-4">
                                        <div>
                                            <div class="font-bold text-slate-900 transition-all leading-snug"
                                                 :class="compactView ? 'max-w-[110px] sm:max-w-[130px] truncate' : ''"
                                                 title="{{ $reqUser->name ?? 'User' }} ({{ $req->email }})">
                                                {{ $reqUser->name ?? 'User' }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono transition-all leading-snug"
                                                 :class="compactView ? 'max-w-[110px] sm:max-w-[130px] truncate' : ''"
                                                 title="{{ $req->email }}">
                                                {{ $req->email }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Outlet -->
                                    <td x-show="cols.outlet" class="py-3 px-3 sm:px-4 whitespace-nowrap">
                                        <div :class="compactOutlet ? 'max-w-[120px] sm:max-w-[140px] truncate' : ''" title="{{ $req->outlet ? $req->outlet->name : ($req->role === 'super_admin' ? 'ALL OUTLETS' : '-') }}">
                                            <span class="font-semibold text-slate-700">
                                                {{ $req->outlet ? $req->outlet->name : ($req->role === 'super_admin' ? 'ALL OUTLETS' : '-') }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Role -->
                                    <td x-show="cols.role" class="py-3 px-3 sm:px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $req->role === 'super_admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : ($req->role === 'admin' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-100 text-slate-700 border-slate-200') }}">
                                            {{ str_replace('_', ' ', $req->role) }}
                                        </span>
                                    </td>

                                    <!-- Requested Time (Submitted) -->
                                    <td x-show="cols.submitted" class="py-3 px-3 sm:px-4 whitespace-nowrap text-slate-500 text-[11px]">
                                        <div class="font-medium text-slate-700">{{ $req->created_at->diffForHumans() }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $req->created_at->format('d M Y, H:i') }}</div>
                                    </td>

                                    <!-- See Current Password -->
                                    <td x-show="cols.password" class="py-3 px-3 sm:px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 font-mono text-xs">
                                            <span x-text="showReqPass ? '{{ $reqUser ? $reqUser->getViewablePassword() : 'Unavailable' }}' : '••••••••'"
                                                  class="px-2 py-1 bg-slate-100 border border-slate-200/80 rounded-lg text-slate-800 font-bold select-all tracking-wider"></span>
                                            <button type="button" @click="showReqPass = !showReqPass"
                                                class="p-1 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                                                :title="showReqPass ? 'Hide password' : 'See current password'">
                                                <svg x-show="!showReqPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg x-show="showReqPass" x-cloak class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                            </button>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td x-show="cols.actions" class="py-3 px-3 sm:px-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Change Password for user button -->
                                            <button type="button"
                                                @click="adminModalReqId = {{ $req->id }}; adminModalUserName = '{{ addslashes($reqUser->name ?? $req->email) }}'; adminModalUserEmail = '{{ addslashes($req->email) }}';"
                                                class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] rounded-lg transition shadow-2xs flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                                <span>{{ __('Change Password') }}</span>
                                            </button>

                                            <!-- Mark Resolved if already resolved verbally (Green Check Icon) -->
                                            @if ($req->status === 'pending')
                                                <form action="/profile/reset-requests/{{ $req->id }}/resolve" method="POST" class="inline m-0 p-0">
                                                    @csrf
                                                    <button type="submit" 
                                                        class="w-7 h-7 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 hover:text-emerald-700 border border-emerald-200 rounded-lg transition shadow-2xs"
                                                        title="{{ __('Mark request as resolved') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Delete Resolved Request (Trash Can Icon) so it doesn't pile up in Resolved or All -->
                                                <form action="/profile/reset-requests/{{ $req->id }}" method="POST"
                                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this recovery request?') }}');" class="inline m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                        class="w-7 h-7 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-500 hover:text-rose-700 border border-rose-200 rounded-lg transition shadow-2xs"
                                                        title="{{ __('Delete resolved request') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td :colspan="Object.values(cols).filter(Boolean).length || 1" class="py-10 text-center text-slate-400 font-medium">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ __('No forgot password requests found.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer Bar (Universal Bottom Bar) -->
                <div class="p-3 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                    <span>
                        {{ __('Showing') }} <strong class="text-slate-800" x-text="totalItems > 0 ? ((currentPage - 1) * perPage) + 1 : 0"></strong>
                        {{ __('to') }} <strong class="text-slate-800" x-text="Math.min(currentPage * perPage, totalItems)"></strong>
                        {{ __('of') }} <strong class="text-slate-800" x-text="totalItems"></strong> {{ __('records') }}
                    </span>

                    <template x-if="totalPages > 1">
                        <div class="flex items-center gap-1 overflow-x-auto py-1">
                            <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>

                            <template x-for="(page, idx) in pageRange" :key="idx">
                                <span>
                                    <template x-if="page === '...'">
                                        <span class="w-6 h-7 sm:h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none shrink-0">...</span>
                                    </template>
                                    <template x-if="page !== '...'">
                                        <button type="button" @click="goToPage(page)"
                                            :class="currentPage === page ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-xs font-bold transition shrink-0 tabular-nums"
                                            x-text="page">
                                        </button>
                                    </template>
                                </span>
                            </template>

                            <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                                :class="currentPage >= totalPages ? 'border-slate-200 bg-white text-slate-300 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg border transition text-xs font-bold shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Column Settings Modal for Profile -->
            <template x-teleport="body">
                <div x-show="showColumnModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
                     @keydown.escape.window="showColumnModal = false" x-cloak>
                    <div @click.away="showColumnModal = false"
                         x-show="showColumnModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xl max-w-sm w-full p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">{{ __('Table Column Settings') }}</h3>
                                    <p class="text-[11px] text-slate-500 font-medium">{{ __('Choose which columns to show or hide') }}</p>
                                </div>
                            </div>
                            <button type="button" @click="showColumnModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-1.5 max-h-[60vh] overflow-y-auto pr-1">
                            <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                <span class="text-xs font-bold text-slate-800">{{ __('USER') }}</span>
                                <input type="checkbox" x-model="tempCols.user" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                            <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                <span class="text-xs font-bold text-slate-800">{{ __('OUTLET') }}</span>
                                <input type="checkbox" x-model="tempCols.outlet" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                            <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                <span class="text-xs font-bold text-slate-800">{{ __('ROLE') }}</span>
                                <input type="checkbox" x-model="tempCols.role" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                            <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                <span class="text-xs font-bold text-slate-800">{{ __('SUBMITTED') }}</span>
                                <input type="checkbox" x-model="tempCols.submitted" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                            <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                <span class="text-xs font-bold text-slate-800">{{ __('CURRENT PASSWORD') }}</span>
                                <input type="checkbox" x-model="tempCols.password" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                            <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                <span class="text-xs font-bold text-slate-800">{{ __('ACTIONS') }}</span>
                                <input type="checkbox" x-model="tempCols.actions" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="showColumnModal = false"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                {{ __('Cancel') }}
                            </button>
                            <button type="button" @click="saveColumns()"
                                class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition shadow-soft-xs active:scale-95">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        @endif
    </div>
</div>

        <!-- MODAL: USER CHANGE OWN PASSWORD (REQUIRES ORIGINAL PASSWORD VERIFICATION) -->
        <div x-show="showChangePasswordModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition-opacity"
             @keydown.escape.window="showChangePasswordModal = false">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xl max-w-sm w-full p-5 sm:p-6 space-y-4 text-left"
                 @click.outside="showChangePasswordModal = false">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ __('Change Your Password') }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            {{ __('Please input your original/current password first for verification.') }}
                        </p>
                    </div>
                    <button type="button" @click="showChangePasswordModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                @if ($errors->has('current_password'))
                    <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                        {{ $errors->first('current_password') }}
                    </div>
                @endif

                @if ($errors->has('new_password'))
                    <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                        {{ $errors->first('new_password') }}
                    </div>
                @endif

                <form action="/profile/change-password" method="POST" class="space-y-3.5">
                    @csrf
                    
                    <!-- Step 1: Input Original Password -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                            {{ __('Original / Current Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="current_password" required placeholder="Enter current password"
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                    </div>

                    <!-- Step 2: New Password -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                            {{ __('New Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="new_password" required placeholder="Minimum 6 characters"
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                    </div>

                    <!-- Step 3: Confirm New Password -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                            {{ __('Confirm New Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="new_password_confirmation" required placeholder="Repeat new password"
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showChangePasswordModal = false" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-xs">
                            Verify & Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: ADMIN CHANGE PASSWORD FOR STAFF MEMBER -->
        <div x-show="adminModalReqId !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition-opacity"
             @keydown.escape.window="adminModalReqId = null">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xl max-w-sm w-full p-5 sm:p-6 space-y-4 text-left"
                 @click.outside="adminModalReqId = null">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ __('Set New Password for User') }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Target: <strong class="text-slate-800" x-text="adminModalUserName"></strong> (<span class="font-mono text-[11px]" x-text="adminModalUserEmail"></span>)
                        </p>
                    </div>
                    <button type="button" @click="adminModalReqId = null" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'/profile/reset-requests/' + adminModalReqId + '/change-password'" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                            {{ __('Assign New Password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="new_password" required placeholder="e.g. staff2026!"
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold font-mono focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                        <p class="text-[10px] text-slate-400 mt-1">This will be updated instantly and shown so you can inform the staff.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                            {{ __('Admin Resolution Note (Optional)') }}
                        </label>
                        <input type="text" name="admin_notes" placeholder="e.g. Password informed via WhatsApp"
                            class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-slate-900 transition">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="adminModalReqId = null" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-xs">
                            Update & Mark Resolved
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</x-app-layout>