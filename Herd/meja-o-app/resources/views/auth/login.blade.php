<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm max-w-md w-full space-y-6">
        <div class="text-center space-y-1">
            <h1 class="text-2xl font-bold text-slate-900">My Kopi-O Group</h1>
            <p class="text-xs text-slate-500">Table Management System Login</p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. mko_staff@gmail.com"
                    class="w-full text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                <input type="password" name="password" required placeholder="Enter password"
                    class="w-full text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold py-3 rounded-xl transition shadow-sm">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>