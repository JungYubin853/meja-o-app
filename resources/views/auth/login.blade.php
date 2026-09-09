<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center font-sans antialiased px-4">
    <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm max-w-sm w-full space-y-5">
        
        <div class="text-center space-y-1">
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">My Kopi-O Group</h1>
            <p class="text-xs text-slate-500 font-medium">Table Management System</p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold shadow-2xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. staff@kopiogroup.com"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
            </div>
            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold py-2.5 rounded-xl transition shadow-sm">
                Sign In to Portal
            </button>
        </form>

    </div>
</body>
</html>