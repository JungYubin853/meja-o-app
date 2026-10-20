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

    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-soft-xl max-w-sm sm:max-w-md w-full text-center flex flex-col gap-4">

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
        <div x-show="!isApproved" class="flex flex-col gap-4">

            <!-- Status Badge -->
            <div>
                @if ($user->status === 'rejected')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Registration Declined
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Pending Administrator Approval
                    </span>
                @endif
            </div>

            <!-- Account Details Card -->
            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 text-left space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Signed In Account</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600">Active Session</span>
                </div>

                <div>
                    <div class="font-bold text-sm text-slate-900">{{ $user->name }}</div>
                    <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $user->email }}</div>
                </div>

                <div class="pt-2.5 border-t border-slate-200/60 space-y-2 text-xs">
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Assigned Outlet</div>
                        <div class="font-bold text-slate-800 text-xs sm:text-sm mt-0.5 leading-snug">
                            {{ $outlet ? $outlet->name : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Account Role</div>
                        <div class="font-bold text-slate-800 text-xs capitalize mt-0.5">
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
            <div class="space-y-2.5 pt-1">
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
