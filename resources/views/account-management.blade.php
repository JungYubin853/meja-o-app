<x-app-layout title="Meja-O | Account Management">
    <main class="w-full max-w-6xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 pt-20 sm:pt-24 min-h-screen">
        
        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Account Management</h1>
            <p class="text-sm text-slate-500 font-medium">Create and oversee system accounts.</p>
        </div>

        @if (session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold shadow-soft-xs flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-semibold shadow-soft-xs">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (Auth::user()->hasPermission('acc_create_account') || Auth::user()->hasPermission('acc_user_list'))

            @if (Auth::user()->hasPermission('acc_create_account'))
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-soft-xs space-y-5">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">Create New Account (Super Admin)</h2>
                        <p class="text-xs text-slate-500 font-medium">Provision new staff, admin, or super admin accounts across all outlets.</p>
                    </div>

                    <form action="/users" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Full Name</label>
                                <input type="text" name="name" required
                                    class="w-full text-xs sm:text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Email Address</label>
                                <input type="email" name="email" required
                                    class="w-full text-xs sm:text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Role</label>
                                <select name="role" required
                                    class="w-full text-xs sm:text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Assign Outlet</label>
                                <select name="outlet_id"
                                    class="w-full text-xs sm:text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                    <option value="">None (For Super Admin)</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Temporary Password</label>
                                <input type="password" name="password" required
                                    class="w-full text-xs sm:text-sm p-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-6 py-3 rounded-xl transition shadow-soft-xs active:scale-98">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if (Auth::user()->hasPermission('acc_user_list'))
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">User List</h3>
                        <p class="text-xs text-slate-500 font-medium">Read, oversee, and delete staff accounts.</p>
                    </div>
                    <span class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full border border-slate-200">
                        {{ count($allUsers) }} Users
                    </span>
                </div>

                <div class="overflow-x-auto touch-scroll">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                <th class="p-3.5 sm:p-4">Name</th>
                                <th class="p-3.5 sm:p-4">Email</th>
                                <th class="p-3.5 sm:p-4">Outlet / Brand</th>
                                <th class="p-3.5 sm:p-4">Role</th>
                                <th class="p-3.5 sm:p-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($allUsers as $u)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="p-3.5 sm:p-4 font-bold text-slate-900">{{ $u->name }}</td>
                                    <td class="p-3.5 sm:p-4 text-slate-600">{{ $u->email }}</td>
                                    <td class="p-3.5 sm:p-4 font-medium text-slate-800">{{ $u->outlet->name ?? 'ALL OUTLETS' }}</td>
                                    <td class="p-3.5 sm:p-4">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $u->role === 'admin' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ $u->role }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 sm:p-4 text-right">
                                        @if ($u->id !== $user->id)
                                            <form action="/users/{{ $u->id }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete user {{ $u->name }}?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Current User</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-soft-xs space-y-5 text-center">
                <p class="text-sm text-slate-500 font-medium">You do not have permission to view this page.</p>
            </div>
        @endif

    </main>
</x-app-layout>
