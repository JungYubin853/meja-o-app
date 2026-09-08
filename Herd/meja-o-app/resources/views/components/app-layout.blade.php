@props(['title' => 'Meja-O | Restaurant Table Management'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen font-sans antialiased" {{ $attributes }}>

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">My Kopi-O Group</h1>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium">Table Management System</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                {{ $headerActions ?? '' }}

                <!-- Logout Button with Confirmation Modal Trigger -->
                <div x-data="{ logoutModalOpen: false }" class="inline">
                    <button @click="logoutModalOpen = true" type="button" title="Logout"
                        class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 p-2 rounded-xl transition flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>

                    <!-- Pop-up Confirmation Modal -->
                    <div x-show="logoutModalOpen"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4"
                        x-cloak>
                        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center space-y-4 shadow-xl border border-slate-100 transform transition-all"
                            @click.away="logoutModalOpen = false">
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl font-bold">
                                !
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Confirm Logout</h3>
                            <p class="text-xs text-slate-500">Are you sure you want to log out of your session?</p>
                            <form action="/logout" method="POST" class="flex space-x-3 pt-2">
                                @csrf
                                <button type="button" @click="logoutModalOpen = false"
                                    class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 rounded-xl transition">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="w-1/2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold py-2.5 rounded-xl transition shadow-xs">
                                    Yes, Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    {{ $slot }}

</body>
</html>