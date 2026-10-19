<x-app-layout title="Meja-O | Account Approval">
    <main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-5 min-h-screen">
        
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold shadow-soft-xs flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
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

        <!-- Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-soft-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ __('Account Approval') }}</h1>
                    @if ($pendingCount > 0)
                        <span class="px-2.5 py-0.5 text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 rounded-full animate-pulse">
                            {{ $pendingCount }} {{ __('Pending') }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    @if ($user->isSuperAdmin())
                        {{ __('Review and manage self-registered user accounts requesting access across all outlets.') }}
                    @else
                        {{ __('Review and manage self-registered user accounts requesting access for') }} <span class="font-bold text-slate-700">{{ $user->outlet->name ?? 'your outlet' }}</span>.
                    @endif
                </p>
            </div>

            <!-- Status Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200/80 self-start md:self-auto overflow-x-auto">
                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ ($status === 'pending' || !$status) ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>{{ __('Pending') }}</span>
                    @if ($pendingCount > 0)
                        <span class="px-1.5 py-0.2 text-[10px] bg-amber-500 text-white rounded-full font-black">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'approved' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    {{ __('Approved') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'rejected' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    {{ __('Rejected') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'all' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    {{ __('All') }}
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs">
            <form method="GET" action="/approval" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
                <input type="hidden" name="status" value="{{ $status }}">

                <div class="flex-1 w-full flex flex-col sm:flex-row gap-2.5">
                    <!-- Search input -->
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search name or email...') }}"
                            class="w-full text-xs pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900">
                    </div>

                    <!-- Registration Source filter -->
                    <div class="w-full sm:w-44">
                        <select name="source" onchange="this.form.submit()"
                            class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900">
                            <option value="">{{ __('All Sources') }}</option>
                            <option value="self" {{ request('source') === 'self' ? 'selected' : '' }}>{{ __('Self-Registered') }}</option>
                            <option value="admin" {{ request('source') === 'admin' ? 'selected' : '' }}>{{ __('Admin-Created') }}</option>
                        </select>
                    </div>

                    <!-- Outlet dropdown (Super Admin only) -->
                    @if ($user->isSuperAdmin())
                        <div class="w-full sm:w-64">
                            <select name="outlet_id" onchange="this.form.submit()"
                                class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                <option value="">{{ __('All Outlets') }}</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit"
                        class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-soft-xs active:scale-98 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>{{ __('Search') }}</span>
                    </button>

                    @if (request()->hasAny(['search', 'outlet_id', 'source']) || ($status && $status !== 'pending'))
                        <a href="/approval" class="w-full sm:w-auto text-xs font-bold text-slate-500 hover:text-slate-800 px-3 py-2 rounded-xl hover:bg-slate-100 transition text-center">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Approvals List Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden" x-data="{
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
            <!-- Table Header Controls Bar (Universal Top Bar) -->
            <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <h3 class="font-bold text-slate-800 text-sm">{{ __('Applicant Accounts') }}</h3>
                    <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200 tabular-nums">
                        {{ $users->total() }} {{ __('Records') }}
                    </span>
                </div>

                <div class="flex items-center gap-3 self-end sm:self-auto flex-wrap">
                    <!-- Dropdown: Rows per page (10, 25, 50, 100) -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ __('Rows per page:') }}</span>
                        <select onchange="window.location.href = this.value"
                            class="h-8 text-xs font-bold px-2 py-0 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                            @foreach ([10, 25, 50, 100] as $opt)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $opt, 'page' => 1]) }}" {{ $perPage == $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Top Numbered Pagination -->
                    <x-numbered-pagination :paginator="$users" />
                </div>
            </div>

            @if ($users->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-14 h-14 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto border border-slate-200/80">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">
                        @if ($status === 'pending' || !$status)
                            {{ __('No pending account registrations') }}
                        @else
                            {{ __('No accounts found') }}
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        @if ($status === 'pending' || !$status)
                            {{ __('All self-registered user requests have been processed. New registrations waiting for review will appear here.') }}
                        @else
                            {{ __('Try clearing search filters or checking a different status tab.') }}
                        @endif
                    </p>
                </div>
            @else
                <div class="overflow-x-auto touch-scroll">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Applicant / User') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Outlet') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Role') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Source') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Submitted') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Status') }}</th>
                                <th class="py-2.5 px-3 sm:px-4 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($users as $applicant)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-2.5 px-3 sm:px-4">
                                        <div class="font-bold text-slate-800 text-xs">{{ $applicant->name }}</div>
                                        <div class="text-[11px] font-medium text-slate-500">{{ $applicant->email }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4 font-bold text-slate-700">
                                        {{ $applicant->outlet->name ?? 'ALL OUTLETS' }}
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $applicant->role === 'admin' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ $applicant->role }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4 font-semibold text-slate-600">
                                        {{ $applicant->is_self_registered ? __('Self-Registered') : __('Admin-Created') }}
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4 text-slate-500 font-medium text-[11px]">
                                        {{ $applicant->created_at ? $applicant->created_at->diffForHumans() : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4">
                                        @if ($applicant->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                {{ __('Pending') }}
                                            </span>
                                        @elseif ($applicant->status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ __('Approved') }}
                                            </span>
                                        @elseif ($applicant->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ __('Rejected') }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-medium">{{ $applicant->status }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 sm:px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if (!$applicant->isSuperAdmin())
                                                @if ($applicant->status === 'pending')
                                                    <!-- Approve Button -->
                                                    <button type="button"
                                                        @click="openConfirm('approve', '/approval/{{ $applicant->id }}/approve', '{{ addslashes($applicant->name) }}')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span>{{ __('Approve') }}</span>
                                                    </button>

                                                    <!-- Reject Button -->
                                                    <button type="button"
                                                        @click="openConfirm('reject', '/approval/{{ $applicant->id }}/reject', '{{ addslashes($applicant->name) }}')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-bold text-xs transition active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        <span>{{ __('Reject') }}</span>
                                                    </button>
                                                @elseif ($applicant->status === 'rejected')
                                                    <!-- Re-Approve Button -->
                                                    <button type="button"
                                                        @click="openConfirm('approve', '/approval/{{ $applicant->id }}/approve', '{{ addslashes($applicant->name) }}')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-200 font-bold text-xs transition active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span>{{ __('Approve') }}</span>
                                                    </button>
                                                @elseif ($applicant->status === 'approved')
                                                    <!-- Revoke / Reject Button -->
                                                    <button type="button"
                                                        @click="openConfirm('reject', '/approval/{{ $applicant->id }}/reject', '{{ addslashes($applicant->name) }}')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 text-[11px] font-bold transition">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                        <span>{{ __('Revoke') }}</span>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer Bar (Universal Bottom Bar) -->
                <div class="p-3 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                    <span>
                        {{ __('Showing') }} <strong class="text-slate-800">{{ $users->firstItem() ?? 0 }}</strong>
                        {{ __('to') }} <strong class="text-slate-800">{{ $users->lastItem() ?? 0 }}</strong>
                        {{ __('of') }} <strong class="text-slate-800">{{ $users->total() }}</strong> {{ __('records') }}
                    </span>
                    <x-numbered-pagination :paginator="$users" />
                </div>
            @endif

            <!-- Confirmation Modal -->
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

    </main>
</x-app-layout>
