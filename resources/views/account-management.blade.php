<x-app-layout title="Meja-O | Account Management">
    <main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 min-h-screen w-full overflow-x-hidden">
        
        @if (session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold shadow-soft-xs flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-semibold shadow-soft-xs">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (Auth::user()->hasPermission('acc_create_account') || Auth::user()->hasPermission('acc_search_bar') || Auth::user()->hasPermission('acc_user_list') || Auth::user()->hasPermission('nav_account_mgmt') || Auth::user()->hasPermission('nav_approval'))
            <div class="flex flex-col lg:flex-row gap-6 items-start w-full min-w-0">

            @if (Auth::user()->hasPermission('acc_create_account') || Auth::user()->hasPermission('acc_search_bar'))
                <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-4">
                    @if (Auth::user()->hasPermission('acc_search_bar'))
                    <!-- Search Bar Container (Height & styling aligned with User List header) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs flex items-center min-h-[64px] sm:h-[64px]">
                        <form method="GET" action="/account-management" class="flex items-center gap-2 w-full m-0">
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if(request('direction'))
                                <input type="hidden" name="direction" value="{{ request('direction') }}">
                            @endif
                            @if(request('source'))
                                <input type="hidden" name="source" value="{{ request('source') }}">
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

                    @if (Auth::user()->hasPermission('acc_create_account'))
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-bold text-slate-800">{{ __('Create New Account') }}</h2>
                        </div>

                        <form action="/users" method="POST" class="space-y-4">
                            @csrf
                            <div class="flex flex-col gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Full Name') }}</label>
                                    <input type="text" name="name" required
                                        class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Email Address') }}</label>
                                    <input type="email" name="email" required
                                        class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                </div>
                            </div>

                            <div class="flex flex-col gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Role') }}</label>
                                    <select name="role" required
                                        class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                        <option value="staff">{{ __('Staff') }}</option>
                                        <option value="admin">{{ __('Admin') }}</option>
                                        @if (Auth::user()->isSuperAdmin())
                                            <option value="super_admin">{{ __('Super Admin') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Assign Outlet') }}</label>
                                    @if (Auth::user()->isSuperAdmin())
                                        <select name="outlet_id"
                                            class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                            <option value="">{{ __('None (For Super Admin)') }}</option>
                                            @foreach($outlets as $outlet)
                                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="outlet_id" value="{{ Auth::user()->outlet_id }}">
                                        <select disabled
                                            class="w-full text-xs p-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-600 cursor-not-allowed"
                                            title="{{ __('Assigned Outlet (Read-only)') }}">
                                            <option selected>{{ Auth::user()->outlet->name ?? __('No Outlet Assigned') }}</option>
                                        </select>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Temporary Password') }}</label>
                                    <input type="password" name="password" required
                                        class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit"
                                    class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98">
                                    {{ __('Create Account') }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            @endif

            @if (Auth::user()->hasPermission('acc_user_list') || Auth::user()->hasPermission('nav_account_mgmt') || Auth::user()->hasPermission('nav_approval'))
                <div class="flex-1 min-w-0 w-full max-w-full flex flex-col gap-4" x-data="{
                    currentPage: 1,
                    perPage: 5,
                    compactView: true,
                    compactOutlet: true,
                    showColumnModal: false,
                    cols: {
                        user: {{ Auth::user()->hasPermission('acc_col_user') ? 'true' : 'false' }},
                        outlet: {{ Auth::user()->hasPermission('acc_col_outlet') ? 'true' : 'false' }},
                        role: {{ Auth::user()->hasPermission('acc_col_role') ? 'true' : 'false' }},
                        source: {{ Auth::user()->hasPermission('acc_col_source') ? 'true' : 'false' }},
                        status: {{ Auth::user()->hasPermission('acc_col_status') ? 'true' : 'false' }},
                        submitted: {{ Auth::user()->hasPermission('acc_col_submitted') ? 'true' : 'false' }},
                        actions: {{ Auth::user()->hasPermission('acc_col_actions') ? 'true' : 'false' }},
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
                                        acc_col_user: this.cols.user,
                                        acc_col_outlet: this.cols.outlet,
                                        acc_col_role: this.cols.role,
                                        acc_col_source: this.cols.source,
                                        acc_col_status: this.cols.status,
                                        acc_col_submitted: this.cols.submitted,
                                        acc_col_actions: this.cols.actions,
                                    }
                                })
                            });
                        } catch (e) {
                            console.error('Failed to save columns', e);
                        }
                    },
                    totalItems: {{ count($allUsers) }},
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
                    },
                    confirmModalOpen: false,
                    confirmAction: '',
                    confirmUrl: '',
                    confirmUserName: '',
                    openConfirm(action, url, name) {
                        this.confirmAction = action;
                        this.confirmUrl = url;
                        this.confirmUserName = name;
                        this.confirmModalOpen = true;
                    }
                }">
                    <!-- Container 1: User List Title, Records Badge, All Outlets, All Roles -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-soft-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 min-h-[64px] sm:h-[64px] w-full max-w-full">
                        <div class="flex items-center gap-2.5">
                            <h3 class="text-sm font-bold text-slate-800">{{ __('User List') }}</h3>
                            <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200 tabular-nums">
                                {{ count($allUsers) }} {{ __('Records') }}
                            </span>
                        </div>
                        
                        <!-- Filters -->
                        <form method="GET" action="/account-management" class="flex items-center gap-2 w-full sm:w-auto flex-wrap sm:flex-nowrap m-0">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if(request('direction'))
                                <input type="hidden" name="direction" value="{{ request('direction') }}">
                            @endif
                            @if(request('source'))
                                <input type="hidden" name="source" value="{{ request('source') }}">
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
                                @endif
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                            </select>
                        </form>
                    </div>

                    <!-- Container 2: Approval Status Tabs & Registration Source Filter & Column Settings -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-soft-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 min-h-[64px] sm:h-[64px] w-full max-w-full">
                        <!-- Status Tabs: All, Approved, Rejected, Pending -->
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-semibold overflow-x-auto max-w-full shrink-0">
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}"
                                class="px-3 py-1.5 rounded-lg transition {{ ($status === 'all' || !$status) ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                                {{ __('All') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}"
                                class="px-3 py-1.5 rounded-lg transition {{ $status === 'approved' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                                {{ __('Approved') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
                                class="px-3 py-1.5 rounded-lg transition {{ $status === 'rejected' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                                {{ __('Rejected') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition {{ $status === 'pending' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                                <span>{{ __('Pending') }}</span>
                                @if ($pendingCount > 0)
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-extrabold bg-amber-500 text-white shadow-2xs aspect-square shrink-0">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        <!-- Right Controls: Source Filter & Column Settings Gear Button -->
                        <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                            <form method="GET" action="/account-management" class="flex items-center gap-2 m-0 flex-1 sm:flex-initial">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
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
                                <div class="flex items-center gap-1.5 w-full">
                                    <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Source:') }}</span>
                                    <select name="source" onchange="this.form.submit()"
                                        class="h-8 text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-0 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer w-full sm:w-auto">
                                        <option value="">{{ __('All Sources') }}</option>
                                        <option value="self" {{ request('source') === 'self' ? 'selected' : '' }}>{{ __('Self-Registered') }}</option>
                                        <option value="admin" {{ request('source') === 'admin' ? 'selected' : '' }}>{{ __('Admin-Created') }}</option>
                                    </select>
                                </div>
                            </form>

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
                    </div>

                    <!-- Container 3: Table & Pagination Controls -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col w-full max-w-full min-w-0">
                        <!-- Top Controls Bar -->
                        <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 w-full">
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

                        @if ($allUsers->isEmpty())
                            <div class="p-12 text-center space-y-3">
                                <div class="w-14 h-14 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto border border-slate-200/80">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">
                                    @if ($status === 'pending')
                                        {{ __('No pending account registrations') }}
                                    @else
                                        {{ __('No accounts found') }}
                                    @endif
                                </h3>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                    @if ($status === 'pending')
                                        {{ __('All self-registered user requests have been processed. New registrations waiting for review will appear here.') }}
                                    @else
                                        {{ __('Try clearing search filters or checking a different status tab.') }}
                                    @endif
                                </p>
                            </div>
                        @else
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
                                            <th x-show="cols.source" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">{{ __('Source') }}</th>
                                            <th x-show="cols.status" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">{{ __('Status') }}</th>
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
                                            <th x-show="cols.actions" class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap min-w-[130px] w-36">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($allUsers as $index => $u)
                                            <tr class="hover:bg-slate-50/60 transition" x-show="currentPage === Math.ceil(({{ $index }} + 1) / perPage)" x-cloak>
                                                <!-- Applicant / User (Name and Email in one) -->
                                                <td x-show="cols.user" class="py-2.5 px-3 sm:px-4">
                                                    <div class="font-bold text-slate-800 text-xs transition-all leading-snug"
                                                         :class="compactView ? 'max-w-[110px] sm:max-w-[130px] truncate' : ''"
                                                         title="{{ $u->name }} ({{ $u->email }})">
                                                        {{ $u->name }}
                                                    </div>
                                                    <div class="text-[11px] font-medium text-slate-500 transition-all leading-snug"
                                                         :class="compactView ? 'max-w-[110px] sm:max-w-[130px] truncate' : ''"
                                                         title="{{ $u->email }}">
                                                        {{ $u->email }}
                                                    </div>
                                                </td>

                                                <!-- Outlet -->
                                                <td x-show="cols.outlet" class="py-2.5 px-3 sm:px-4 font-bold text-slate-700 text-xs whitespace-nowrap">
                                                    <div :class="compactOutlet ? 'max-w-[120px] sm:max-w-[140px] truncate' : ''" title="{{ $u->outlet->name ?? 'ALL OUTLETS' }}">
                                                        {{ $u->outlet->name ?? 'ALL OUTLETS' }}
                                                    </div>
                                                </td>

                                                <!-- Role -->
                                                <td x-show="cols.role" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $u->role === 'admin' ? 'bg-slate-900 text-white' : ($u->role === 'super_admin' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                                        {{ $u->role }}
                                                    </span>
                                                </td>

                                                <!-- Source -->
                                                <td x-show="cols.source" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $u->is_self_registered ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-slate-900 text-white' }}">
                                                        {{ $u->is_self_registered ? __('Self-Registered') : __('Admin-Created') }}
                                                    </span>
                                                </td>

                                                <!-- Status -->
                                                <td x-show="cols.status" class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                                                    @if ($u->status === 'pending')
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                                            {{ __('Pending') }}
                                                        </span>
                                                    @elseif ($u->status === 'approved')
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            {{ __('Approved') }}
                                                        </span>
                                                    @elseif ($u->status === 'rejected')
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                                            {{ __('Rejected') }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 font-medium text-[11px]">{{ $u->status }}</span>
                                                    @endif
                                                </td>

                                                <!-- Submitted -->
                                                <td x-show="cols.submitted" class="py-2.5 px-3 sm:px-4 text-slate-500 font-medium text-[11px] whitespace-nowrap">
                                                    @if ($u->created_at)
                                                        <div class="font-semibold text-slate-700 leading-tight">{{ $u->created_at->diffForHumans() }}</div>
                                                        <div class="text-[10px] text-slate-400 leading-tight">{{ $u->created_at->format('d M Y, H:i') }}</div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <!-- Actions -->
                                                <td x-show="cols.actions" class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap min-w-[130px] w-36">
                                                    @if ($u->id === $user->id)
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Current User') }}</span>
                                                    @elseif ($u->isSuperAdmin() && !$user->isSuperAdmin())
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Protected') }}</span>
                                                    @else
                                                        <div class="inline-flex items-center justify-end gap-1.5 flex-nowrap">
                                                            @if ($u->status === 'pending')
                                                                <!-- Approve Button -->
                                                                <button type="button"
                                                                    @click="openConfirm('approve', '/approval/{{ $u->id }}/approve', '{{ addslashes($u->name) }}')"
                                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs active:scale-95 shrink-0"
                                                                    title="{{ __('Approve User') }}">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    <span>{{ __('Approve') }}</span>
                                                                </button>

                                                                <!-- Reject Button -->
                                                                <button type="button"
                                                                    @click="openConfirm('reject', '/approval/{{ $u->id }}/reject', '{{ addslashes($u->name) }}')"
                                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-bold text-xs transition active:scale-95 shrink-0"
                                                                    title="{{ __('Reject User') }}">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                    <span>{{ __('Reject') }}</span>
                                                                </button>
                                                            @elseif ($u->status === 'rejected')
                                                                <!-- Re-Approve Button -->
                                                                <button type="button"
                                                                    @click="openConfirm('approve', '/approval/{{ $u->id }}/approve', '{{ addslashes($u->name) }}')"
                                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-200 font-bold text-xs transition active:scale-95 shrink-0"
                                                                    title="{{ __('Re-Approve User') }}">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    <span>{{ __('Approve') }}</span>
                                                                </button>

                                                                <!-- Delete Button -->
                                                                <form action="/users/{{ $u->id }}" method="POST"
                                                                    onsubmit="return confirm('Are you sure you want to delete user {{ addslashes($u->name) }}?');" class="inline-block m-0 shrink-0">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" title="{{ __('Delete User') }}"
                                                                        class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-500 hover:text-rose-700 transition shrink-0">
                                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <!-- Approved Actions: Manage Permissions, Revoke, Delete -->
                                                                <a href="/role-permission?email={{ urlencode($u->email) }}" title="{{ __('Manage Permissions') }}"
                                                                   class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-500 hover:text-slate-800 transition shrink-0">
                                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                                </a>

                                                                <!-- Revoke Button -->
                                                                <button type="button"
                                                                    @click="openConfirm('reject', '/approval/{{ $u->id }}/reject', '{{ addslashes($u->name) }}')"
                                                                    class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-slate-50 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 text-slate-400 hover:text-rose-600 transition shrink-0"
                                                                    title="{{ __('Revoke Access') }}">
                                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                    </svg>
                                                                </button>

                                                                <!-- Delete Button -->
                                                                <form action="/users/{{ $u->id }}" method="POST"
                                                                    onsubmit="return confirm('Are you sure you want to delete user {{ addslashes($u->name) }}?');" class="inline-block m-0 shrink-0">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" title="{{ __('Delete User') }}"
                                                                        class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-500 hover:text-rose-700 transition shrink-0">
                                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
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
                        @endif
                    </div>

                    <!-- Column Settings Modal -->
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
                                        <span class="text-xs font-bold text-slate-800">{{ __('SOURCE') }}</span>
                                        <input type="checkbox" x-model="tempCols.source" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                        <span class="text-xs font-bold text-slate-800">{{ __('STATUS') }}</span>
                                        <input type="checkbox" x-model="tempCols.status" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer select-none">
                                        <span class="text-xs font-bold text-slate-800">{{ __('SUBMITTED') }}</span>
                                        <input type="checkbox" x-model="tempCols.submitted" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
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

                    <!-- Confirmation Modal for Approve / Reject -->
                    <template x-teleport="body">
                        <div x-show="confirmModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" x-cloak>
                            <div @click.away="confirmModalOpen = false"
                                x-show="confirmModalOpen"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="bg-white rounded-2xl max-w-sm w-full p-5 space-y-4 shadow-xl border border-slate-100">
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                        :class="confirmAction === 'approve' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'">
                                        <template x-if="confirmAction === 'approve'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </template>
                                        <template x-if="confirmAction !== 'approve'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </template>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900" x-text="confirmAction === 'approve' ? '{{ __('Approve Account') }}' : '{{ __('Reject Account') }}'"></h3>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="confirmAction === 'approve' ? '{{ __('Grant portal access to this user?') }}' : '{{ __('Deny portal access for this user?') }}'"></p>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    {{ __('User:') }} <span class="font-bold text-slate-900" x-text="confirmUserName"></span>
                                </p>

                                <form :action="confirmUrl" method="POST" class="flex gap-2 justify-end pt-2">
                                    @csrf
                                    <button type="button" @click="confirmModalOpen = false"
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-white transition shadow-soft-xs active:scale-95"
                                        :class="confirmAction === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                                        x-text="confirmAction === 'approve' ? '{{ __('Confirm Approval') }}' : '{{ __('Confirm Rejection') }}'">
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-soft-xs space-y-5 text-center">
                <p class="text-sm text-slate-500 font-medium">{{ __('You do not have permission to view this page.') }}</p>
            </div>
        @endif

    </main>
</x-app-layout>