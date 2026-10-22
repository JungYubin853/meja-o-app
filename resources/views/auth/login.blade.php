<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Sign In</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .bg-grid-pattern {
            background-size: 20px 20px;
            background-image: radial-gradient(circle, rgba(148, 163, 184, 0.16) 1px, transparent 1px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center font-sans antialiased px-4 py-6 relative overflow-x-hidden selection:bg-slate-900 selection:text-white"
    x-data="{ 
        showForgotModal: {{ $errors->has('forgot_error') ? 'true' : 'false' }}, 
        isSuperAdminRecovery: false,
        showPassword: false 
    }">

    <!-- Ambient Subtle Glows -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-amber-200/25 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-slate-200/40 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-grid-pattern opacity-50"></div>
    </div>

    <!-- Main Container Card -->
    <div class="relative z-10 bg-white/95 backdrop-blur-md p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-lg shadow-slate-200/40 max-w-xs sm:max-w-sm w-full space-y-4 transition-all duration-300">
        
        <!-- Branding & Header -->
        <div class="text-center space-y-0.5">
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">My Kopi-O Group</h1>
            <p class="text-[11px] text-slate-500 font-medium">Table Management System</p>
        </div>

        <!-- Flash Status Notification -->
        @if (session('status'))
            <div class="p-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-[11px] font-semibold shadow-2xs flex items-start gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="leading-relaxed">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Form Error Notification -->
        @if ($errors->any() && !$errors->has('forgot_error'))
            <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-[11px] font-semibold shadow-2xs flex items-start gap-2">
                <svg class="w-3.5 h-3.5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="leading-relaxed">{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form action="/login" method="POST" class="space-y-3.5" id="loginForm">
            @csrf
            
            <!-- Email Input -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Email Address
                </label>
                <input type="text" id="display_email" value="{{ old('email') }}" required placeholder="staff@kopiogroup.com" autocomplete="none" spellcheck="false" data-form-type="other"
                    class="w-full text-xs px-3 py-2 bg-slate-50/70 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all placeholder:text-slate-400 font-medium">
                <input type="hidden" name="email" id="real_email">
            </div>

            <!-- Password Input with Show/Hide Toggle -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Password
                    </label>
                    <button type="button" @click="showForgotModal = true" class="text-[10px] font-semibold text-slate-500 hover:text-slate-900 hover:underline transition">
                        Forgot Password?
                    </button>
                </div>
                <div class="relative">
                    <input type="text" id="display_password" required placeholder="••••••••" autocomplete="none" spellcheck="false" data-form-type="other"
                        :style="showPassword ? '-webkit-text-security: none; text-security: none;' : '-webkit-text-security: disc; text-security: disc;'"
                        class="w-full text-xs pl-3 pr-9 py-2 bg-slate-50/70 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all placeholder:text-slate-400 font-medium font-mono">
                    
                    <input type="hidden" name="password" id="real_password">

                    <!-- Interactive Show / Hide Password Toggle -->
                    <button type="button" @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-800 transition"
                        :title="showPassword ? 'Hide password' : 'Show password'">
                        <svg x-show="!showPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-3.5 h-3.5 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" 
                class="w-full group bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-xs font-bold py-2.5 px-3 rounded-lg transition-all shadow-sm flex items-center justify-center gap-1.5 mt-1">
                <span>Sign In to Portal</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>

        <script>
            document.getElementById('loginForm').addEventListener('submit', function() {
                document.getElementById('real_email').value = document.getElementById('display_email').value;
                document.getElementById('real_password').value = document.getElementById('display_password').value;
            });
        </script>

        <!-- Footer Help -->
        <div class="text-center pt-2.5 border-t border-slate-100 flex items-center justify-center gap-1.5 text-[11px] text-slate-500 font-medium">
            <span>New staff member?</span>
            <a href="/register" class="text-slate-900 font-bold hover:underline">Create account</a>
        </div>

    </div>

    <!-- Forgot Password Modal -->
    <div x-show="showForgotModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-opacity"
         @keydown.escape.window="showForgotModal = false">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xl max-w-xs sm:max-w-sm w-full p-5 space-y-3 text-left transition-all"
             @click.outside="showForgotModal = false">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Forgot Password Recovery</h3>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                        Submit your email to request recovery from your store Admin or Super Admin.
                    </p>
                </div>
                <button type="button" @click="showForgotModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->has('forgot_error'))
                <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-[11px] font-semibold">
                    {{ $errors->first('forgot_error') }}
                </div>
            @endif

            <form action="/forgot-password" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Your Registered Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="staff@kopiogroup.com"
                        class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                </div>

                <!-- Super Admin Instant Recovery Toggle -->
                <div class="pt-0.5">
                    <button type="button" @click="isSuperAdminRecovery = !isSuperAdminRecovery" 
                        class="text-[10px] font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1 transition">
                        <svg class="w-3 h-3 transition-transform" :class="isSuperAdminRecovery ? 'rotate-90 text-slate-900' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span>Super Admin Instant Recovery</span>
                    </button>
                </div>

                <div x-show="isSuperAdminRecovery" x-cloak class="space-y-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-0.5 uppercase tracking-wider">Master Security Key</label>
                        <input type="password" name="master_recovery_key" placeholder="Enter master key"
                            class="w-full text-xs px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-900 font-mono">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-0.5 uppercase tracking-wider">New Password</label>
                        <input type="password" name="new_password" placeholder="Min 6 characters"
                            class="w-full text-xs px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-900">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showForgotModal = false" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-3.5 py-1.5 rounded-lg transition shadow-xs">
                        Send Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
