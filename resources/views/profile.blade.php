<x-app-layout title="Meja-O | User Profile">

    <main class="max-w-5xl mx-auto px-3 sm:px-6 py-6 sm:py-8 space-y-6"
        x-data="{ 
            showChangePasswordModal: {{ $errors->has('current_password') || $errors->has('new_password') ? 'true' : 'false' }},
            showMyPassword: false,
            adminModalReqId: null,
            adminModalUserName: '',
            adminModalUserEmail: ''
        }">

        <!-- Top Header & Breadcrumb -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md border {{ $user->isSuperAdmin() ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : ($user->isAdmin() ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-100 text-slate-700 border-slate-200') }}">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">
                        {{ $user->email }} &bull; {{ $user->isSuperAdmin() ? __('All Outlets Access') : ($user->outlet->name ?? __('No Outlet Assigned')) }}
                    </p>
                </div>
            </div>

            @if (($user->isAdmin() || $user->isSuperAdmin()) && isset($pendingCount) && $pendingCount > 0)
                <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-3 py-1.5 rounded-xl text-xs font-bold animate-pulse">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $pendingCount }} {{ __('Password Reset Request(s) Pending') }}</span>
                </div>
            @endif
        </div>

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

        <!-- 1. ACCOUNT INFORMATION CARD -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Account Information') }}</h2>
                    <p class="text-xs text-slate-500 font-medium">{{ __('Manage personal account details and credentials.') }}</p>
                </div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    ID #{{ $user->id }}
                </span>
            </div>

            <form action="/profile" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">{{ __('Full Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                    </div>

                    <!-- Email Address (Locked) -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">{{ __('Email Address') }}</label>
                        <div class="relative">
                            <input type="email" value="{{ $user->email }}" disabled
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-semibold text-slate-500 cursor-not-allowed">
                            <svg class="w-4 h-4 text-slate-400 absolute right-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>

                    <!-- Assigned Brand / Outlet -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">{{ __('Assigned Brand / Outlet') }}</label>
                        <input type="text" value="{{ $user->isSuperAdmin() ? 'ALL OUTLETS' : ($user->outlet->name ?? 'None') }}" disabled
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-600 cursor-not-allowed">
                    </div>

                    <!-- Current Password Display with "See Current Password" toggle -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">{{ __('Current Password') }}</label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input :type="showMyPassword ? 'text' : 'password'" 
                                       value="{{ $user->getViewablePassword() ?? '********' }}" 
                                       readonly
                                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-mono font-bold text-slate-700 cursor-default select-all pr-10">
                                
                                <button type="button" @click="showMyPassword = !showMyPassword"
                                    class="absolute right-2.5 top-2.5 p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-200/60 transition"
                                    :title="showMyPassword ? 'Hide password' : 'See current password'">
                                    <svg x-show="!showMyPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showMyPassword" x-cloak class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            
                            <button type="button" @click="showMyPassword = !showMyPassword" 
                                class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 transition whitespace-nowrap shrink-0">
                                <span x-text="showMyPassword ? 'Hide' : 'See Password'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons: Change Password (on left) and Save Profile Changes (on right) -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <!-- Change Password Button (on the left side of Save Profile Changes) -->
                    <button type="button" @click="showChangePasswordModal = true"
                        class="bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-2xs active:scale-98 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        <span>Change Password</span>
                    </button>

                    <!-- Save Profile Changes Button -->
                    <button type="submit"
                        class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-soft-xs active:scale-98 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Profile Changes</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. FORGOT PASSWORD REQUESTS (ADMIN & SUPER ADMIN ONLY) -->
        @if ($user->isAdmin() || $user->isSuperAdmin())
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Forgot Password Recovery Requests') }}</h2>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border {{ $pendingCount > 0 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200' }} tabular-nums">
                                {{ $pendingCount }} {{ __('Pending') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            @if ($user->isSuperAdmin())
                                {{ __('Review and manage password recovery requests submitted across all store outlets.') }}
                            @else
                                {{ __('Review and manage password recovery requests submitted by staff in your assigned outlet.') }}
                            @endif
                        </p>
                    </div>

                    <!-- Status Filter Tabs -->
                    @php $reqStatus = request('req_status', 'all'); @endphp
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-end sm:self-auto text-xs font-semibold">
                        <a href="/profile?req_status=all" 
                           class="px-3 py-1 rounded-lg transition {{ $reqStatus === 'all' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            {{ __('All') }}
                        </a>
                        <a href="/profile?req_status=pending" 
                           class="px-3 py-1 rounded-lg transition {{ $reqStatus === 'pending' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            {{ __('Pending') }}
                        </a>
                        <a href="/profile?req_status=resolved" 
                           class="px-3 py-1 rounded-lg transition {{ $reqStatus === 'resolved' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            {{ __('Resolved') }}
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto touch-scroll">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Applicant / User') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Outlet') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Role') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Current Password') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Requested') }}</th>
                                <th class="py-2.5 px-3 sm:px-4">{{ __('Status') }}</th>
                                <th class="py-2.5 px-3 sm:px-4 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($resetRequests as $req)
                                @php $reqUser = $req->user; @endphp
                                <tr class="hover:bg-slate-50/60 transition" x-data="{ showReqPass: false }">
                                    <!-- User Info -->
                                    <td class="py-3 px-3 sm:px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($reqUser->name ?? $req->email, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $reqUser->name ?? 'User' }}</div>
                                                <div class="text-[11px] text-slate-400 font-mono">{{ $req->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Outlet -->
                                    <td class="py-3 px-3 sm:px-4 whitespace-nowrap">
                                        <span class="font-semibold text-slate-700">
                                            {{ $req->outlet ? $req->outlet->name : ($req->role === 'super_admin' ? 'ALL OUTLETS' : '-') }}
                                        </span>
                                    </td>

                                    <!-- Role -->
                                    <td class="py-3 px-3 sm:px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $req->role === 'super_admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : ($req->role === 'admin' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-100 text-slate-700 border-slate-200') }}">
                                            {{ str_replace('_', ' ', $req->role) }}
                                        </span>
                                    </td>

                                    <!-- See Current Password -->
                                    <td class="py-3 px-3 sm:px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 font-mono text-xs">
                                            <span x-text="showReqPass ? '{{ $reqUser ? $reqUser->getViewablePassword() : 'Unavailable' }}' : '••••••••'"
                                                  class="px-2 py-1 bg-slate-100 border border-slate-200/80 rounded-lg text-slate-800 font-bold select-all tracking-wider"></span>
                                            <button type="button" @click="showReqPass = !showReqPass"
                                                class="p-1 text-slate-400 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                                                :title="showReqPass ? 'Hide password' : 'See current password'">
                                                <svg x-show="!showReqPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg x-show="showReqPass" x-cloak class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                            </button>
                                        </div>
                                    </td>

                                    <!-- Requested Time -->
                                    <td class="py-3 px-3 sm:px-4 whitespace-nowrap text-slate-500 text-[11px]">
                                        <div class="font-medium text-slate-700">{{ $req->created_at->diffForHumans() }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $req->created_at->format('d M Y, H:i') }}</div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3 px-3 sm:px-4 whitespace-nowrap">
                                        @if ($req->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                                                  title="Resolved by {{ $req->resolved_by }} at {{ $req->resolved_at ? $req->resolved_at->format('d M, H:i') : '' }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Resolved
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3 px-3 sm:px-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Change Password for user button -->
                                            <button type="button"
                                                @click="adminModalReqId = {{ $req->id }}; adminModalUserName = '{{ addslashes($reqUser->name ?? $req->email) }}'; adminModalUserEmail = '{{ addslashes($req->email) }}';"
                                                class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] rounded-lg transition shadow-2xs flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                                <span>{{ __('Change Password') }}</span>
                                            </button>

                                            <!-- Mark Resolved if already resolved verbally -->
                                            @if ($req->status === 'pending')
                                                <form action="/profile/reset-requests/{{ $req->id }}/resolve" method="POST" class="inline m-0 p-0">
                                                    @csrf
                                                    <button type="submit" 
                                                        class="px-2 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-[11px] rounded-lg transition shadow-2xs"
                                                        title="Mark request as resolved without changing password">
                                                        {{ __('Done') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-400 font-medium">
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
            </div>
        @endif

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