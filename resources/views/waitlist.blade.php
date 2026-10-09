<x-app-layout title="Meja-O | Waitlist Management">

    <main class="max-w-4xl mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-5">
        
        <!-- Queue Header Banner -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-xl font-bold text-slate-900 tracking-tight">Active {{ __('Waiting') }} Queue</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Manage customer entries and assign to available dining tables.</p>
            </div>
            
            <div class="flex items-center gap-2 sm:gap-3">
                @if (auth()->check() && auth()->user()->isSuperAdmin())
                    <form method="GET" action="/waitlist" class="m-0">
                        <select name="outlet_id" onchange="this.form.submit()" class="text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-soft-xs h-9 transition cursor-pointer">
                            @foreach ($outlets ?? [] as $outlet)
                                <option value="{{ $outlet->id }}" {{ ($selectedOutletId ?? '') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
                <span class="bg-slate-900 text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-soft-xs shrink-0 tabular-nums flex items-center h-9">
                    {{ count($waitlist) }} {{ __('Waiting') }}
                </span>
            </div>
        </div>

        <!-- Add Waitlist Form -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-soft-xs space-y-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('Add New Party to Queue') }}</h3>
            
            <form action="/waitlist" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
                @csrf
                @if (isset($selectedOutletId))
                    <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}">
                @endif
                <div>
                    <input type="text" name="customer_name" placeholder="Guest / Party Name" required
                        class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition">
                </div>
                <div>
                    <input type="tel" name="phone" placeholder="Phone Number (Optional)"
                        class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition">
                </div>
                <div>
                    <input type="number" name="pax" placeholder="{{ __('Guests ({{ __('Pax') }})') }}" min="1" required
                        class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition">
                </div>

                <button type="submit"
                    class="sm:col-span-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3.5 rounded-xl transition shadow-sm active:scale-98 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Enqueue Party</span>
                </button>
            </form>
        </div>

        <!-- Queue Cards Listing (Mobile-First Layout) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between font-bold text-xs sm:text-sm text-slate-900 uppercase tracking-wider">
                <span>Current Queue</span>
                <span class="text-slate-400 font-semibold text-xs lowercase">in order of arrival</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($waitlist as $index => $item)
                    <div class="p-4 sm:p-5 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition">
                        
                        <!-- Queue Rank & Info -->
                        <div class="flex items-center gap-3 sm:gap-4">
                            <span class="w-8 h-8 sm:w-9 sm:h-9 bg-slate-900 text-white rounded-xl flex items-center justify-center text-xs font-bold shadow-soft-xs shrink-0 tabular-nums">
                                #{{ $index + 1 }}
                            </span>
                            
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ $item->customer_name }}</h4>
                                    <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded-md border border-slate-200 tabular-nums">
                                        {{ $item->pax }} {{ __('Pax') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 font-medium">
                                    @if ($item->phone)
                                        <a href="tel:{{ $item->phone }}" class="text-slate-600 hover:text-slate-900 font-semibold flex items-center gap-1">
                                            <span>📞</span> {{ $item->phone }}
                                        </a>
                                        <span class="text-slate-300">·</span>
                                    @endif
                                    <span>{{ $item->created_at ? $item->created_at->diffForHumans() : 'Just now' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cancel / Remove Trigger -->
                        <form action="/waitlist/{{ $item->id }}/cancel" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('{{ __('Cancel this party from the waiting queue?') }}');"
                                class="text-xs text-rose-600 hover:text-rose-700 font-bold px-3 py-2 bg-rose-50 hover:bg-rose-100 rounded-xl transition border border-rose-200/60 active:scale-95">
                                {{ __('Cancel') }}
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="p-10 text-center space-y-2">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-sm font-bold">✓</div>
                        <p class="text-xs text-slate-500 font-medium">{{ __('No parties currently in the waitlist.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</x-app-layout>