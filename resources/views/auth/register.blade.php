<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Staff Registration</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center font-sans antialiased px-4 py-8"
    x-data="{ showForgotModal: {{ $errors->has('forgot_error') ? 'true' : 'false' }}, isSuperAdminRecovery: false }">
    <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm max-w-sm w-full space-y-5">
        
        <div class="text-center space-y-1">
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Staff Registration</h1>
            <p class="text-xs text-slate-500 font-medium">Create your store portal account</p>
        </div>

        @if (session('status'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold shadow-2xs">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any() && !$errors->has('forgot_error'))
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold shadow-2xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/register" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. staff@kopiogroup.com"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Assigned Outlet / Brand</label>
                <select name="outlet_id" required
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 font-semibold text-slate-800 transition">
                    @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}" {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>
                            {{ $outlet->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">System Role</label>
                <input type="text" value="Staff" disabled
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-500 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Password</label>
                <input type="password" name="password" required placeholder="Minimum 6 characters"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required placeholder="Repeat password"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-3 rounded-xl transition shadow-sm mt-2">
                Register & Enter Portal
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100 flex items-center justify-center gap-2 text-xs text-slate-500 font-medium">
            <span>Already registered? <a href="/login" class="text-slate-900 font-bold hover:underline">Sign In</a></span>
            <span class="text-slate-300">•</span>
            <button type="button" @click="showForgotModal = true" class="text-slate-500 hover:text-slate-900 font-medium">
                Forgot Password?
            </button>
        </div>

    </div>

    <!-- Forgot Password Modal -->
    <div x-show="showForgotModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition-opacity"
         @keydown.escape.window="showForgotModal = false">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xl max-w-sm w-full p-5 sm:p-6 space-y-4 text-left"
             @click.outside="showForgotModal = false">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Forgot Password?</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        Input your email. A recovery notification will be dispatched to your outlet Administrator and Super Admin.
                    </p>
                </div>
                <button type="button" @click="showForgotModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->has('forgot_error'))
                <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                    {{ $errors->first('forgot_error') }}
                </div>
            @endif

            <form action="/forgot-password" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Your Registered Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. staff@kopiogroup.com"
                        class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                </div>

                <!-- Super Admin Instant Recovery Toggle -->
                <div class="pt-1">
                    <button type="button" @click="isSuperAdminRecovery = !isSuperAdminRecovery" 
                        class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 transition">
                        <svg class="w-3.5 h-3.5 transition-transform" :class="isSuperAdminRecovery ? 'rotate-90 text-slate-900' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span>Super Admin Instant Recovery Option</span>
                    </button>
                </div>

                <div x-show="isSuperAdminRecovery" x-cloak class="space-y-2.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Master Security Key</label>
                        <input type="password" name="master_recovery_key" placeholder="Enter master key"
                            class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-900 font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wider">New Password</label>
                        <input type="password" name="new_password" placeholder="Minimum 6 characters"
                            class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-900">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showForgotModal = false" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-xs">
                        Send Recovery Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>