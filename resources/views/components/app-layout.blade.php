@props(['title' => 'Meja-O'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ $title ?? 'Meja-O' }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    boxShadow: {
                        'soft-2xs': '0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                        'soft-xs': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
                        'soft-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .touch-scroll {
            -webkit-overflow-scrolling: touch;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body {{ $attributes->merge(['class' => 'bg-slate-100 text-slate-800 min-h-screen font-sans antialiased selection:bg-amber-100 selection:text-amber-900']) }}
    x-data="{
        sideMenuOpen: false,
        logoutModalOpen: false
    }">

    <div class="min-h-screen flex flex-col lg:pr-[300px]">

        <!-- Top Header Navigation -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 h-16 box-border">
            <div class="max-w-[84rem] mx-auto px-3 sm:px-6 h-full flex items-center justify-between">

                <!-- Left: Dynamic Brand / Outlet Name -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="flex flex-col justify-center h-full">
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight uppercase group-hover:text-slate-700 transition">
                            {{ Auth::check() ? (Auth::user()->isSuperAdmin() ? 'ALL OUTLETS' : (Auth::user()->outlet->name ?? 'My Kopi-O Group')) : 'My Kopi-O Group' }}
                        </h3>
                    </div>
                    @if (Auth::check())
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md border tracking-wider {{ Auth::user()->role === 'admin' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                            {{ Auth::user()->role }}
                        </span>
                    @endif
                </a>

                <!-- Right: Hamburger Menu Button -->
                @if (Auth::check())
                    <button type="button" @click="sideMenuOpen = true" title="Open Navigation Menu"
                        class="bg-white hover:bg-slate-50 text-slate-700 p-2.5 rounded-xl transition flex lg:hidden items-center justify-center border border-slate-200 shadow-soft-xs hover:border-slate-300 active:scale-95">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                @endif

            </div>
        </header>

        <!-- Main Page Content (Slot) -->
        <div class="flex-1">
            {{ $slot }}
        </div>



        <!-- Right Side: Permanent Desktop Navigation -->
        @if(Auth::check())
        <aside class="hidden lg:flex flex-col bg-white border-l border-slate-200/80 fixed right-0 top-0 h-screen overflow-hidden z-40 shadow-sm w-[300px]">
            <div class="flex flex-col h-full pb-6">
                <!-- Header -->
                <div class="h-16 box-border flex items-center justify-between border-b border-slate-200/80 shrink-0 px-5 mb-6">
                    <div class="leading-tight">
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight uppercase">NAVIGATION MENU</h3>
                    </div>
                </div>
                
                <!-- Links -->
                <div class="space-y-1.5 flex-1 overflow-y-auto px-5 pb-4">
                    <!-- 1. Dashboard Link -->
                            @if (Auth::user()->hasPermission('nav_dashboard'))
                                <a href="/"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('/') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('/') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                    </div>
                                    <span>Dashboard</span>
                                </a>
                            @endif

                            <!-- 2. Waitlist Link -->
                            <a href="/waitlist"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('waitlist*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('waitlist*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span>Waitlist</span>
                            </a>

                            <!-- 3. Reports & Database Link -->
                            @if (Auth::user()->hasPermission('nav_reports'))
                                <a href="/database"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('database*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('database*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <span>Reports & Database</span>
                                </a>
                            @endif

                            <!-- Tutorial Link -->
                            @if(Auth::user()->hasPermission('nav_tutorial'))
                            <a href="/tutorial"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('tutorial*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('tutorial*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <span>Tutorial & Guidelines</span>
                            </a>
                            @endif
                            <!-- Calendar Link -->
                            @if(Auth::user()->hasPermission('nav_calendar'))
                            <a href="/calendar"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('calendar*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('calendar*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span>Calendar</span>
                            </a>

                            <!-- 4. Profile Link -->
                            @if (Auth::user()->hasPermission('nav_profile'))
                                <a href="/profile"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('profile*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('profile*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span>Profile</span>
                                </a>
                            @endif
                            
                            @if (Auth::user()->hasPermission('nav_account_mgmt'))
                                <!-- 5. Account Management -->
                                <a href="/account-management"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('account-management*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('account-management*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <span>Account Management</span>
                                </a>
                            @endif

                            @if (Auth::user()->hasPermission('nav_role_permission'))
                                <!-- 6. Role & User Permission -->
                                <a href="/role-permission"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('role-permission*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('role-permission*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <span>Role & User Permission</span>
                                </a>
                            @endif
                        </div>

                <!-- Logout Button -->
                <div class="pt-4 shrink-0 border-t border-slate-100 mt-auto px-5">
                    <button type="button" @click="logoutModalOpen = true"
                        class="w-full bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200 hover:border-rose-200 text-xs font-bold py-2.5 rounded-xl transition shadow-soft-2xs">
                        Logout
                    </button>
                </div>
            </div>
        </aside>
        @endif

    </div>

    <!-- ========================================================================= -->
    <!-- SLIDE-OVER DRAWER MENU (Using x-teleport so it never interferes with dashboard clicks) -->
    <!-- ========================================================================= -->
    <template x-teleport="body">
        <div x-show="sideMenuOpen" class="relative z-50" x-cloak>

            <!-- Backdrop Overlay -->
            <div x-show="sideMenuOpen"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sideMenuOpen = false"
                class="fixed inset-0 bg-slate-900/50"></div>

            <!-- Drawer Container on the RIGHT covering 66% width -->
            <div class="fixed inset-y-0 right-0 flex max-w-full pointer-events-auto lg:hidden">
                <div x-show="sideMenuOpen"
                    x-transition:enter="transform transition ease-in-out duration-250"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-[300px] bg-white shadow-soft-xl flex flex-col border-l border-slate-200 h-full overflow-hidden">

                    <!-- Top Section: Header + Navigation List -->
                    <div class="flex flex-col flex-1 min-h-0">

                        <!-- Header with Title & Close Button (X) -->
                        <div class="h-16 box-border flex items-center justify-between border-b border-slate-200/80 shrink-0 px-5 mb-6">
                            <div class="leading-tight">
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight uppercase">NAVIGATION MENU</h3>
                            </div>

                            <!-- Close Button (X) -->
                            <button @click="sideMenuOpen = false" title="Close Menu"
                                class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-sm transition">
                                ✕
                            </button>
                        </div>

                        <!-- Menu Navigation Links -->
                        <div class="space-y-1.5 flex-1 overflow-y-auto px-5 pb-4">

                            <!-- 1. Dashboard Link -->
                            @if (Auth::user()->hasPermission('nav_dashboard'))
                                <a href="/"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('/') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('/') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                    </div>
                                    <span>Dashboard</span>
                                </a>
                            @endif

                            <!-- 2. Waitlist Link -->
                            <a href="/waitlist"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('waitlist*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('waitlist*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span>Waitlist</span>
                            </a>

                            <!-- 3. Reports & Database Link -->
                            @if (Auth::user()->hasPermission('nav_reports'))
                                <a href="/database"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('database*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('database*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <span>Reports & Database</span>
                                </a>
                            @endif

                            <!-- Tutorial Link -->
                            @if(Auth::user()->hasPermission('nav_tutorial'))
                            <a href="/tutorial"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('tutorial*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('tutorial*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <span>Tutorial & Guidelines</span>
                            </a>
                            @endif
                            <!-- Calendar Link -->
                            @if(Auth::user()->hasPermission('nav_calendar'))
                            <a href="/calendar"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('calendar*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('calendar*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span>Calendar</span>
                            </a>

                            <!-- 4. Profile Link -->
                            @if (Auth::user()->hasPermission('nav_profile'))
                                <a href="/profile"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('profile*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('profile*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span>Profile</span>
                                </a>
                            @endif
                            
                            @if (Auth::user()->hasPermission('nav_account_mgmt'))
                                <!-- 5. Account Management -->
                                <a href="/account-management"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('account-management*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('account-management*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <span>Account Management</span>
                                </a>
                            @endif

                            @if (Auth::user()->hasPermission('nav_role_permission'))
                                <!-- 6. Role & User Permission -->
                                <a href="/role-permission"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->is('role-permission*') ? 'bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->is('role-permission*') ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-100 text-slate-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <span>Role & User Permission</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Bottom Logout Trigger Button -->
                      <div class="pt-4 shrink-0 border-t border-slate-100 mt-auto px-5 pb-5">
                        <button type="button" @click="logoutModalOpen = true"
                            class="w-full bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200 hover:border-rose-200 text-xs font-bold py-2.5 rounded-xl transition shadow-soft-2xs">
                            Logout
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </template>

    <!-- ========================================================================= -->
    <!-- LOGOUT CONFIRMATION MODAL (Also teleported to body) -->
    <!-- ========================================================================= -->
    <template x-teleport="body">
        <div x-show="logoutModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4" x-cloak>

            <div class="bg-white rounded-2xl max-w-xs w-full p-5 space-y-4 shadow-soft-xl border border-slate-100 text-center"
                @click.away="logoutModalOpen = false">

                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Confirm Logout</h3>
                    <p class="text-xs text-slate-500 mt-1">Are you sure you want to end this session?</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="logoutModalOpen = false"
                        class="w-1/2 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <form action="/logout" method="POST" class="w-1/2 m-0">
                        @csrf
                        <button type="submit"
                            class="w-full py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition">
                            Yes, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>

</body>

</html>