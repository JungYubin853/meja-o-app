<x-app-layout title="Meja-O | User Profile">

    <main class="max-w-4xl mx-auto px-3 sm:px-6 py-6 sm:py-8 space-y-6">

        @if (session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- My Profile Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-soft-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Account Information</h2>
                    <p class="text-xs text-slate-500 font-medium">Manage your personal details and assigned outlet.</p>
                </div>
                <span class="text-xs font-bold uppercase px-3 py-1 rounded-full border {{ $user->role === 'admin' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    {{ $user->role }}
                </span>
            </div>

            <form action="/profile" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full text-xs sm:text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Email Address</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full text-xs sm:text-sm p-3 bg-slate-100 border border-slate-200 rounded-xl font-semibold text-slate-500 cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if (!$user->isSuperAdmin())
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Assigned Brand / Outlet</label>
                            <input type="text" value="{{ $user->outlet->name ?? 'None' }}" disabled
                                class="w-full text-xs sm:text-sm p-3 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-600 cursor-not-allowed">
                        </div>
                    @else
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Assigned Outlet</label>
                            <input type="text" value="ALL OUTLETS" disabled
                                class="w-full text-xs sm:text-sm p-3 bg-slate-100 border border-slate-200 rounded-xl font-semibold text-slate-500 cursor-not-allowed">
                        </div>
                    @endif

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Change Password (leave empty to keep current)</label>
                        <input type="password" name="password" placeholder="New password"
                            class="w-full text-xs sm:text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-6 py-3 rounded-xl transition shadow-soft-xs active:scale-98">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- ADMIN / SUPER ADMIN: Manage User Accounts -->


    </main>
</x-app-layout>