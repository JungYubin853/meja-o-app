<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Staff Registration</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center font-sans antialiased px-4 py-8">
    <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm max-w-sm w-full space-y-5">
        
        <div class="text-center space-y-1">
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Staff Registration</h1>
            <p class="text-xs text-slate-500 font-medium">Create your store portal account</p>
        </div>

        @if ($errors->any())
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

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-500 font-medium">
                Already registered? 
                <a href="/login" class="text-slate-900 font-bold hover:underline">Sign In</a>
            </p>
        </div>

    </div>
</body>
</html>