<x-app-layout title="Meja-O | Account Management">
    <main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 min-h-screen">
        


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

                @if (Auth::user()->hasPermission('acc_create_account') || Auth::user()->hasPermission('acc_user_list'))
            <div class="flex flex-col lg:flex-row gap-6 items-start w-full">

            @if (Auth::user()->hasPermission('acc_create_account'))
                <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                    <!-- Search Bar -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-3">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Search User') }}</label>
                        <form method="GET" action="/account-management" class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search name or email...') }}"
                                class="flex-1 w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                            <button type="submit"
                                class="bg-slate-900 hover:bg-slate-800 text-white font-bold w-10 h-[38px] rounded-xl flex items-center justify-center transition shadow-soft-xs active:scale-98 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
                    </div>

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
                                <select name="outlet_id" {{ !Auth::user()->isSuperAdmin() ? 'required' : '' }}
                                    class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                    @if (Auth::user()->isSuperAdmin())
                                        <option value="">{{ __('None (For Super Admin)') }}</option>
                                    @else
                                        <option value="" disabled {{ !Auth::user()->outlet_id ? 'selected' : '' }}>{{ __('Select Outlet') }}</option>
                                    @endif
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}" {{ (!Auth::user()->isSuperAdmin() && Auth::user()->outlet_id == $outlet->id) ? 'selected' : '' }}>
                                            {{ $outlet->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
                </div>
            @endif

            @if (Auth::user()->hasPermission('acc_user_list'))
                <div class="flex-1 min-w-0 flex flex-col gap-4" x-data="{
                    currentPage: 1,
                    perPage: 10,
                    totalItems: {{ count($allUsers) }},
                    get totalPages() { return Math.ceil(this.totalItems / this.perPage) || 1; },
                    nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                    prevPage() { if (this.currentPage > 1) this.currentPage--; },
                    goToPage(p) { if (p >= 1 && p <= this.totalPages) this.currentPage = p; },
                    get pageRange() {
                        const total = this.totalPages;
                        const current = this.currentPage;
                        const delta = 3;
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
                    <!-- Above Container: User List Title, Records Badge, All Outlets, All Roles -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <h3 class="text-sm font-bold text-slate-800">{{ __('User List') }}</h3>
                            <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200 tabular-nums">
                                {{ count($allUsers) }} {{ __('Records') }}
                            </span>
                        </div>
                        
                        <!-- Filters -->
                        <form method="GET" action="/account-management" class="flex items-center gap-2.5 w-full sm:w-auto flex-wrap">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <select name="outlet_id" onchange="this.form.submit()" class="h-9 text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 focus:outline-none focus:ring-2 focus:ring-slate-900 cursor-pointer w-full sm:w-auto">
                                <option value="">{{ __('All Outlets') }}</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                            <select name="role" onchange="this.form.submit()" class="h-9 text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 focus:outline-none focus:ring-2 focus:ring-slate-900 cursor-pointer w-full sm:w-auto">
                                <option value="">{{ __('All Roles') }}</option>
                                @if (Auth::user()->isSuperAdmin())
                                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
                                @endif
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                            </select>
                            @if(request()->filled('role') || request()->filled('outlet_id') || request()->filled('search'))
                                <a href="/account-management" class="text-xs font-bold text-slate-400 hover:text-slate-800 px-2 py-1">Clear</a>
                            @endif
                        </form>
                    </div>

                    <!-- Below Container: Table & Pagination Controls -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">
                        <!-- Top Controls Bar -->
                        <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Rows per page:') }}</span>
                                <select x-model.number="perPage" @change="currentPage = 1" class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
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

                    <div class="overflow-x-auto touch-scroll">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                    <th class="py-2.5 px-3 sm:px-4">{{ __('Name') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Email') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Outlet / Brand') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Role') }}</th>
                                <th class="py-2.5 px-3 sm:px-4 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($allUsers as $index => $u)
                                <tr class="hover:bg-slate-50/60 transition" x-show="currentPage === Math.ceil(({{ $index }} + 1) / perPage)" x-cloak>
                                    <td class="py-2.5 px-3 sm:px-4 text-xs font-bold text-slate-800">{{ $u->name }}</td>
                                    <td class="py-2.5 px-3 sm:px-4 text-xs font-semibold text-slate-500">{{ $u->email }}</td>
                                    <td class="py-2.5 px-3 sm:px-4 text-xs font-bold text-slate-700">{{ $u->outlet->name ?? 'ALL OUTLETS' }}</td>
                                    <td class="py-2.5 px-3 sm:px-4">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $u->role === 'admin' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                                {{ $u->role }}
                                            </span>
                                            @if($u->is_self_registered)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                                    {{ __('Self-Registered') }}
                                                </span>
                                            @endif
                                            @if($u->status === 'pending')
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                                    {{ __('Pending') }}
                                                </span>
                                            @elseif($u->status === 'rejected')
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                                    {{ __('Rejected') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4 text-right">
                                        @if ($u->id !== $user->id && (!$u->isSuperAdmin() || $user->isSuperAdmin()))
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="/role-permission?email={{ urlencode($u->email) }}" title="Manage Permissions"
                                                   class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-500 hover:text-slate-800 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                </a>
                                                
                                                <form action="/users/{{ $u->id }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete user {{ $u->name }}?');" class="inline-block m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="{{ __('Delete User') }}"
                                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-500 hover:text-rose-700 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Current User</span>
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
                </div>
            @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-soft-xs space-y-5 text-center">
                <p class="text-sm text-slate-500 font-medium">You do not have permission to view this page.</p>
            </div>
        @endif

    </main>
</x-app-layout>
