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
        password: '',
        showPassword: false,
        get isPasswordValid() { return this.password.length >= 6; }
    }">

    <!-- Ambient Subtle Glows -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-200/25 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-slate-200/40 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-grid-pattern opacity-50"></div>
    </div>

    <!-- Main Container Card -->
    <div class="relative z-10 bg-white/95 backdrop-blur-md p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-lg shadow-slate-200/40 max-w-xs sm:max-w-sm w-full space-y-3.5 transition-all duration-300">
        
        <!-- Branding & Header -->
        <div class="text-center space-y-0.5">
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Create Staff Account</h1>
            <p class="text-[11px] text-slate-500 font-medium">Store outlet table management portal</p>
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
        @if ($errors->any())
            <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-[11px] font-semibold shadow-2xs flex items-start gap-2">
                <svg class="w-3.5 h-3.5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="leading-relaxed">{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Registration Form -->
        <form action="/register" method="POST" class="space-y-3">
            @csrf

            <!-- Full Name Input -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Full Name
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                    class="w-full text-xs px-3 py-2 bg-slate-50/70 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all placeholder:text-slate-400 font-medium">
            </div>

            <!-- Email Address Input -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="staff@kopiogroup.com"
                    class="w-full text-xs px-3 py-2 bg-slate-50/70 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all placeholder:text-slate-400 font-medium">
            </div>

            <!-- Outlet Selection Input -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Assigned Store Outlet
                </label>
                <div class="relative">
                    <select name="outlet_id" required
                        class="w-full text-xs pl-3 pr-7 py-2 bg-slate-50/70 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all font-semibold text-slate-800 cursor-pointer appearance-none">
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}" {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Role Indicator (Locked to Staff) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Portal Role
                    </label>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                        Default
                    </span>
                </div>
                <div class="px-3 py-1.5 bg-slate-100/80 border border-slate-200 rounded-lg flex items-center gap-1.5 text-xs font-bold text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Store Staff Member</span>
                </div>
            </div>

            <!-- Single Password Input with Show/Hide Toggle & Live Character Check -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Create Password
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                        name="password" 
                        x-model="password"
                        required 
                        placeholder="At least 6 characters"
                        class="w-full text-xs pl-3 pr-9 py-2 bg-slate-50/70 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all placeholder:text-slate-400 font-medium font-mono">

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

                <!-- Interactive Password Requirement Indicator -->
                <div class="mt-1 flex items-center gap-1 text-[10px] transition-colors"
                     :class="password.length === 0 ? 'text-slate-400' : (isPasswordValid ? 'text-emerald-600 font-semibold' : 'text-amber-600 font-medium')">
                    <svg class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!isPasswordValid" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path x-show="isPasswordValid" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="password.length === 0 ? 'Minimum 6 characters' : (isPasswordValid ? 'Valid length (' + password.length + ' chars)' : (6 - password.length) + ' more character(s) required')"></span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full group bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-xs font-bold py-2.5 px-3 rounded-lg transition-all shadow-sm flex items-center justify-center gap-1.5 mt-1">
                <span>Register Staff Account</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>

        <!-- Footer Help -->
        <div class="text-center pt-2 border-t border-slate-100 flex items-center justify-center gap-1.5 text-[11px] text-slate-500 font-medium">
            <span>Already registered?</span>
            <a href="/login" class="text-slate-900 font-bold hover:underline">Sign In</a>
        </div>

    </div>
</body>
</html>
