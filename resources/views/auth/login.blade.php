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

        <form action="/login" method="POST" class="space-y-4" id="loginForm">
            @csrf
            
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Email Address</label>
                <!-- Fake input to evade browser detection -->
                <input type="text" id="display_email" value="{{ old('email') }}" required placeholder="e.g. staff@kopiogroup.com" autocomplete="none" spellcheck="false" data-form-type="other"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                <input type="hidden" name="email" id="real_email">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Password</label>
                <!-- type="text" completely bypasses all password managers. The CSS style hides the characters. -->
                <input type="text" id="display_password" required placeholder="••••••••" autocomplete="none" spellcheck="false" data-form-type="other"
                    style="-webkit-text-security: disc; text-security: disc;"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                <input type="hidden" name="password" id="real_password">
            </div>
            
            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2.5 rounded-xl transition shadow-sm">
                Sign In to Portal
            </button>
        </form>

        <script>
            document.getElementById('loginForm').addEventListener('submit', function() {
                document.getElementById('real_email').value = document.getElementById('display_email').value;
                document.getElementById('real_password').value = document.getElementById('display_password').value;
            });
        </script>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-500 font-medium">
                Don't have an account? 
                <a href="/register" class="text-slate-900 font-bold hover:underline">Register Staff</a>
            </p>
        </div>

    </div>
</body>
</html>