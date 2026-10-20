<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Account Waiting Room</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center font-sans antialiased px-4 py-8"
      x-data="waitingRoom()" x-init="initPolling()">

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-soft-xl max-w-md w-full space-y-6 text-center">

        <!-- Top Header & Branding -->
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold tracking-wider uppercase">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>My Kopi-O Group</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Account Waiting Room</h1>
            <p class="text-xs text-slate-500 font-medium">Table Management System Access Portal</p>
        </div>

        <!-- Approved Celebration Banner (shown when approved via live poll) -->
        <div x-show="isApproved" x-cloak x-transition class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold space-y-2">
            <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto shadow-sm">
                <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="text-sm font-bold text-emerald-900">Your Account is Approved!</div>
            <p class="text-emerald-700 text-[11px]">Redirecting you to the dashboard now...</p>
        </div>

        <!-- Normal State (Pending or Rejected) -->
        <div x-show="!isApproved" class="space-y-6">

            <!-- Animated Status Icon & Badge -->
            @if ($user->status === 'rejected')
                <div class="space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shadow-soft-xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Registration Declined
                        </span>
                        <h2 class="text-base font-bold text-slate-900 mt-2">Access Not Approved</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Your registration request was declined by the administrator.</p>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div class="relative w-16 h-16 mx-auto rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-soft-xs">
                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-amber-500 border-2 border-white"></span>
                        </span>
                        <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Pending Administrator Approval
                        </span>
                        <h2 class="text-base font-bold text-slate-900 mt-2">Waiting in Queue</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Your staff registration is currently waiting to be reviewed.</p>
                    </div>
                </div>
            @endif

            <!-- Account Details Card -->
            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 text-left space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Signed In Account</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600">Active Session</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-2xs">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-xs sm:text-sm text-slate-900 truncate">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono truncate">{{ $user->email }}</div>
                    </div>
                </div>

                <div class="pt-2 grid grid-cols-2 gap-2 text-xs border-t border-slate-200/60">
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400 uppercase">Assigned Outlet</div>
                        <div class="font-bold text-slate-800 text-[11px] truncate mt-0.5">
                            {{ $outlet ? $outlet->name : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400 uppercase">Account Role</div>
                        <div class="font-bold text-slate-800 text-[11px] capitalize mt-0.5">
                            {{ $user->role }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions / Report to Admin -->
            @if ($user->status === 'rejected')
                <div class="bg-rose-50/70 border border-rose-200/80 rounded-2xl p-4 text-left space-y-1.5">
                    <div class="flex items-center gap-2 text-rose-800 font-bold text-xs">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>What should you do?</span>
                    </div>
                    <p class="text-xs text-rose-700 leading-relaxed pl-6">
                        Please contact your outlet's <strong>Administrator</strong> or store management directly if you believe this was an error.
                    </p>
                </div>
            @else
                <div class="bg-amber-50/60 border border-amber-200/70 rounded-2xl p-4 text-left space-y-1.5">
                    <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>What should you do next?</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed pl-6">
                        Please notify and ask your store outlet's <strong>Administrator</strong> or the <strong>Super Admin</strong> to review and approve your account in the <strong>Approval</strong> menu. Once approved, you will immediately have access to the dashboard.
                    </p>
                </div>
            @endif

            <!-- Action Buttons: Check Status & Log Out -->
            <div class="space-y-2.5 pt-2">
                <!-- Check Approval Status Button -->
                <button type="button" @click="checkStatusManual()" :disabled="checking"
                    class="w-full bg-slate-900 hover:bg-slate-800 disabled:opacity-60 text-white text-xs font-semibold py-3 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" :class="checking ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="checking ? 'Checking Status...' : 'Check Approval Status'">Check Approval Status</span>
                </button>

                <!-- Feedback status message -->
                <div x-show="feedbackMsg" x-cloak class="text-xs font-semibold text-slate-500 py-1" x-text="feedbackMsg"></div>

                <!-- Log Out Button -->
                <form action="/logout" method="POST" class="w-full m-0 p-0">
                    @csrf
                    <button type="submit"
                        class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Log Out & Return to Sign In</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

    <script>
        function waitingRoom() {
            return {
                checking: false,
                isApproved: false,
                feedbackMsg: '',
                pollTimer: null,

                initPolling() {
                    // Check status automatically every 8 seconds
                    this.pollTimer = setInterval(() => {
                        this.pollStatus();
                    }, 8000);
                },

                async pollStatus() {
                    try {
                        const res = await fetch('/api/waiting-room/status');
                        if (!res.ok) return;
                        const data = await res.json();
                        if (data.is_approved) {
                            this.isApproved = true;
                            clearInterval(this.pollTimer);
                            setTimeout(() => {
                                window.location.href = '/';
                            }, 1500);
                        }
                    } catch (e) {
                        // Silent catch on poll
                    }
                },

                async checkStatusManual() {
                    this.checking = true;
                    this.feedbackMsg = '';
                    try {
                        const res = await fetch('/api/waiting-room/status');
                        if (!res.ok) throw new Error('Status check failed');
                        const data = await res.json();
                        if (data.is_approved) {
                            this.isApproved = true;
                            this.feedbackMsg = 'Account approved! Redirecting...';
                            setTimeout(() => {
                                window.location.href = '/';
                            }, 1000);
                        } else if (data.is_rejected) {
                            this.feedbackMsg = 'Status: Registration declined.';
                        } else {
                            this.feedbackMsg = 'Status is still pending approval. Please contact your admin.';
                            setTimeout(() => this.feedbackMsg = '', 4000);
                        }
                    } catch (err) {
                        this.feedbackMsg = 'Could not verify status. Please try again.';
                    } finally {
                        this.checking = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
