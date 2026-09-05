<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja-O | Restaurant Table Management</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen font-sans antialiased" x-data="{ modalOpen: false, waitlistModalOpen: false, settingsModalOpen: false, selectedTableId: null, tableNumber: '', selectedTableCapacity: 0 }">

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
                @if (auth()->check() && auth()->user()->role === 'admin')
                    <!-- Settings Button (Hidden for Staff) -->
                    <button @click="settingsModalOpen = true" title="Restaurant Layout Settings"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 p-2 rounded-xl transition flex items-center justify-center border border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                    </button>
                @endif

                <!-- Database / Reports Button -->
                <a href="/database" title="Visitor Analytics & Database"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 p-2 rounded-xl transition flex items-center justify-center border border-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                        </path>
                    </svg>
                </a>

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
                            <div
                                class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl font-bold">
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

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6" x-data="{ search: '', activeTab: 'all' }">

        <!-- Success Modal Popup -->
        @if (session('success_waitlist'))
            <div x-data="{ open: true }" x-show="open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4"
                x-cloak>
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center space-y-4 shadow-xl border border-slate-100"
                    @click.away="open = false">
                    <div
                        class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-xl font-bold">
                        ✓</div>
                    <h3 class="text-base font-bold text-slate-900">Successfully Added to Waitlist</h3>
                    <p class="text-xs text-slate-500">The customer has been successfully queued into the waiting list.
                    </p>
                    <button @click="open = false"
                        class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold py-2.5 rounded-xl transition">
                        Close
                    </button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold shadow-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Waiting Queue Container (Left on Desktop, Top on Mobile) -->
            <div class="order-1 lg:order-1 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-800 text-lg">Waiting Queue</h3>
                        <span
                            class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ count($waitlist) }}</span>
                    </div>

                    <!-- Add to Waitlist Form -->
                    <form action="/waitlist" method="POST" class="space-y-3 mb-6">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Customer Name
                                (Optional)</label>
                            <input type="text" name="customer_name" placeholder="e.g., John"
                                class="w-full text-sm p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number
                                (Optional)</label>
                            <input type="text" name="phone" placeholder="e.g., 0123-4567-89xx"
                                class="w-full text-sm p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Number of Pax</label>
                            <input type="number" name="pax" placeholder="e.g., 4" min="1" required
                                class="w-full text-sm p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <button type="submit"
                            class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold py-2.5 rounded-lg transition shadow-xs">
                            Add to Waiting List
                        </button>
                    </form>

                    <!-- Queue List -->
                    <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                        @forelse($waitlist as $item)
                            <div
                                class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-2">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $item->customer_name }}</h4>
                                    <p class="text-xs text-slate-600 font-medium">Pax: {{ $item->pax }}</p>
                                    <span class="block text-xs text-slate-600 font-medium mt-0.5">
                                        {{ $item->phone }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 bg-amber-100 text-amber-800 rounded-md">Waiting</span>
                                    <form action="/waitlist/{{ $item->id }}/cancel" method="POST">
                                        @csrf
                                        <button type="submit" title="Cancel Queue"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 p-1.5 rounded-lg transition flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 border-2 border-dashed border-slate-100 rounded-lg">
                                <p class="text-xs text-slate-400">No active queue entries.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

            <!-- Table Grid & Section Cards Container (Right on Desktop, Bottom on Mobile) -->
            <div class="order-2 lg:order-2 lg:col-span-3 space-y-6">

                <!-- Controls Header (Search Bar & Floor Map Title) -->
                <div
                    class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-slate-900">Restaurant Floor Map</h2>
                        <p class="text-xs text-slate-500">Real-time tracking of table statuses by category sections.
                        </p>
                    </div>
                    <!-- Search Bar -->
                    <div class="w-full sm:w-64">
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" x-model="search" placeholder="Search table ID..."
                                class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                        </div>
                    </div>
                </div>

                @php
                    $groupedTables = $tables->groupBy('capacity');
                @endphp

                @if ($groupedTables->isNotEmpty())
                    <!-- Tabbed Category Navigation Bar -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                        <button @click="activeTab = 'all'"
                            :class="activeTab === 'all' ? 'bg-slate-900 text-white shadow-sm' :
                                'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0">
                            All Sections
                        </button>
                        @foreach ($groupedTables as $capacity => $secTables)
                            <button @click="activeTab = '{{ $capacity }}'"
                                :class="activeTab === '{{ $capacity }}' ? 'bg-amber-600 text-white shadow-sm' :
                                    'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 flex items-center gap-1.5">
                                <span>{{ $capacity }}-Pax Tables</span>
                                <span
                                    class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]">{{ count($secTables) }}</span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Sections Wrapper -->
                    <div class="space-y-6">
                        @foreach ($groupedTables as $capacity => $sectionTables)
                            <div class="space-y-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs"
                                x-show="activeTab === 'all' || activeTab === '{{ $capacity }}'">
                                <!-- Section Header -->
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                                        <span class="h-3 w-3 bg-amber-600 rounded-md"></span>
                                        {{ $capacity }}-Pax Tables
                                    </h3>
                                    <span
                                        class="text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full">{{ count($sectionTables) }}
                                        Units</span>
                                </div>

                                <!-- Structured Grid Layout per Section -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach ($sectionTables as $table)
                                        <div class="bg-slate-50/60 rounded-xl border border-slate-200 p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between"
                                            x-show="search === '' || '{{ strtolower($table->table_number) }}'.includes(search.toLowerCase())">

                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Table
                                                        ID</span>
                                                    <h4 class="text-lg font-extrabold text-slate-800">
                                                        {{ $table->table_number }}</h4>
                                                </div>
                                                <div
                                                    class="flex items-center space-x-1.5 bg-white px-2.5 py-1 rounded-full border border-slate-200">
                                                    <span
                                                        class="h-2.5 w-2.5 rounded-full 
                                                        {{ $table->status == 'available' ? 'bg-emerald-500' : ($table->status == 'occupied' ? 'bg-rose-500' : 'bg-slate-400') }}"></span>
                                                    <span
                                                        class="text-xs font-medium capitalize text-slate-600">{{ $table->status }}</span>
                                                </div>
                                            </div>

                                            <div class="my-3 py-2 border-t border-b border-slate-200/60 space-y-1">
                                                <div class="flex justify-between text-xs text-slate-500">
                                                    <span>Capacity:</span>
                                                    <span class="font-bold text-slate-700">{{ $table->capacity }}
                                                        Pax</span>
                                                </div>
                                                @if ($table->status == 'occupied')
                                                    <div class="flex justify-between text-xs text-slate-500">
                                                        <span>Current Guests:</span>
                                                        <span class="font-bold text-slate-700">{{ $table->pax }}
                                                            Persons</span>
                                                    </div>
                                                    <div class="flex justify-between text-xs text-slate-500 mt-0.5">
                                                        <span>Seated At:</span>
                                                        <span
                                                            class="font-medium text-slate-700">{{ $table->seated_time ? \Carbon\Carbon::parse($table->seated_time)->format('H:i') : '-' }}</span>
                                                    </div>
                                                @elseif($table->status == 'dirty')
                                                    <p class="text-xs text-slate-400 italic">Awaiting cleaning
                                                        collection...</p>
                                                @else
                                                    <p class="text-xs text-emerald-600 font-medium">Ready for customer
                                                    </p>
                                                @endif
                                            </div>

                                            <div>
                                                @if ($table->status == 'available')
                                                    <div class="flex gap-2">
                                                        <button
                                                            @click="modalOpen = true; selectedTableId = {{ $table->id }}; tableNumber = '{{ $table->table_number }}'"
                                                            class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2 px-3 rounded-lg transition shadow-xs">
                                                            Seat
                                                        </button>
                                                        <button
                                                            @click="waitlistModalOpen = true; selectedTableId = {{ $table->id }}; tableNumber = '{{ $table->table_number }}'; selectedTableCapacity = {{ $table->capacity }}"
                                                            title="Seat from Waiting List"
                                                            class="bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-lg transition shadow-xs flex items-center justify-center shrink-0">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @elseif($table->status == 'occupied')
                                                    <form action="/tables/{{ $table->id }}/finish" method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="w-full bg-slate-600 hover:bg-slate-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition shadow-xs">
                                                            Finished (Mark Dirty)
                                                        </button>
                                                    </form>
                                                @elseif($table->status == 'dirty')
                                                    <span
                                                        class="block text-center text-xs text-slate-400 font-medium py-1.5">
                                                        Pending Cleanup
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white p-12 text-center rounded-2xl border border-slate-200 shadow-xs">
                        <div
                            class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 font-bold text-lg">
                            !</div>
                        <h3 class="text-sm font-bold text-slate-800 mb-1">No Floor Map Configured</h3>
                        <p class="text-xs text-slate-500 mb-4">Click the settings icon in the top right corner to
                            generate your custom restaurant table layout.</p>
                        @if (auth()->check() && auth()->user()->role === 'admin')
                            <button @click="settingsModalOpen = true"
                                class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-xs">
                                Open Layout Generator
                            </button>
                        @endif
                    </div>
                @endif

            </div>

        </div>

    </main>

    <!-- Pop-up Modal for Standard Seating -->
    <div x-show="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-100 transform transition-all"
            @click.away="modalOpen = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-900">Seat Customer</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p class="text-xs text-slate-500 mb-4">Assigning guests to table <strong class="text-slate-800"
                    x-text="tableNumber"></strong>. Please input total number of customers.</p>

            <form :action="'/tables/' + selectedTableId + '/seat'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Number of Guests (Pax)</label>
                    <input type="number" name="pax" min="1" placeholder="Enter total pax" required
                        class="w-full text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex space-x-3 pt-2">
                    <button type="button" @click="modalOpen = false"
                        class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="w-1/2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold py-2.5 rounded-xl transition shadow-xs">
                        Confirm Seating
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pop-up Modal for Seating Waitlist Customer -->
    <div x-show="waitlistModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-100 transform transition-all"
            @click.away="waitlistModalOpen = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-900">Seat from Waitlist</h3>
                <button @click="waitlistModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p class="text-xs text-slate-500 mb-4">Select a waiting party matching table <strong
                    class="text-slate-800" x-text="tableNumber"></strong> capacity.</p>

            <div class="space-y-3 max-h-60 overflow-y-auto">
                @forelse($waitlist as $wItem)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-2"
                        x-show="{{ $wItem->pax }} <= selectedTableCapacity">
                        <div>
                            <h4 class="text-xs font-bold text-slate-800">{{ $wItem->customer_name }}</h4>
                            <span class="text-[10px] text-slate-500 font-medium">Party of {{ $wItem->pax }}</span>
                        </div>
                        <form :action="'/waitlist/' + {{ $wItem->id }} + '/seat'" method="POST">
                            @csrf
                            <input type="hidden" name="table_id" :value="selectedTableId">
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition shadow-xs">
                                Seat Here
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-6 border-2 border-dashed border-slate-100 rounded-xl">
                        <p class="text-xs text-slate-400">No compatible customers in the waiting list.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                <button type="button" @click="waitlistModalOpen = false"
                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 rounded-xl transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    @if (auth()->check() && auth()->user()->role === 'admin')
        <!-- Settings Modal (Admin Only) -->
        <div x-show="settingsModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 transform transition-all"
                @click.away="settingsModalOpen = false" x-data="{ rows: [{ capacity: '', quantity: '' }] }">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Restaurant Layout Generator</h3>
                    <button @click="settingsModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <p class="text-xs text-slate-500 mb-4">Configure your restaurant floor map sections dynamically using
                    the
                    plus button below.</p>

                <form action="/tables/generate" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                        <template x-for="(row, index) in rows" :key="index">
                            <div
                                class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200 gap-3">
                                <div class="flex items-center gap-2 flex-1">
                                    <span class="text-xs font-bold text-slate-500">Pax Size:</span>
                                    <input type="number" :name="'capacities[' + index + ']'"
                                        x-model.number="row.capacity" min="1" required
                                        class="w-16 text-xs p-2 bg-white border border-slate-300 rounded-lg text-center font-bold focus:outline-none focus:ring-2 focus:ring-amber-500">
                                </div>
                                <div class="flex items-center gap-2 flex-1">
                                    <span class="text-xs font-bold text-slate-500">Quantity:</span>
                                    <input type="number" :name="'quantities[' + index + ']'"
                                        x-model.number="row.quantity" min="1" required
                                        class="w-16 text-xs p-2 bg-white border border-slate-300 rounded-lg text-center font-bold focus:outline-none focus:ring-2 focus:ring-amber-500">
                                </div>
                                <!-- Delete Row Button -->
                                <button type="button" @click="rows.splice(index, 1)"
                                    class="text-rose-500 hover:text-rose-700 p-1.5 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Plus (+) Button to Add Custom Table Group -->
                    <button type="button" @click="rows.push({ capacity: '', quantity: '' })"
                        class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 border border-dashed border-slate-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Add Custom Table Group (+)
                    </button>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" @click="settingsModalOpen = false"
                            class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="w-1/2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold py-2.5 rounded-xl transition shadow-xs">
                            Generate Layout
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</body>

</html>
