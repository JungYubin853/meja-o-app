import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update Create Account Header
old_header = """                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">Create New Account (Super Admin)</h2>
                        <p class="text-xs text-slate-500 font-medium">Provision new staff, admin, or super admin accounts across all outlets.</p>
                    </div>"""
new_header = """                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-800">Create New Account</h2>
                    </div>"""
content = content.replace(old_header, new_header)

# 2. Update Label and Input formatting for a compact UI
content = content.replace("block text-[11px] font-semibold text-slate-600 mb-1", "block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1")
content = content.replace("w-full text-xs sm:text-sm p-3", "w-full text-xs p-2.5")
content = content.replace("px-6 py-3 rounded-xl", "px-4 py-2 rounded-lg")
content = content.replace("p-5 shadow-soft-xs space-y-5", "p-4 shadow-soft-xs space-y-4")

# 3. Insert Filters into User List Container
# Right below <h3 class="text-sm sm:text-base font-bold text-slate-900">User List</h3>
# Or as a separate filter bar. Let's add it right after the pagination flex row.

old_user_list_header = """                        <!-- Pagination Controls -->
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">Rows per page:</span>"""

new_user_list_header = """                        <!-- Filters -->
                        <form method="GET" action="/account-management" class="flex items-center gap-2">
                            <select name="outlet_id" onchange="this.form.submit()" class="h-8 text-[11px] font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg px-2 py-0 focus:ring-0 focus:border-slate-300">
                                <option value="">All Outlets</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                            <select name="role" onchange="this.form.submit()" class="h-8 text-[11px] font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg px-2 py-0 focus:ring-0 focus:border-slate-300">
                                <option value="">All Roles</option>
                                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @if(request()->filled('role') || request()->filled('outlet_id'))
                                <a href="/account-management" class="text-[11px] font-bold text-slate-400 hover:text-slate-800 ml-1">Clear</a>
                            @endif
                        </form>

                        <!-- Pagination Controls -->
                        <div class="flex items-center gap-4 ml-auto">
                            <div class="flex items-center gap-2 hidden sm:flex">
                                <span class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">Rows per page:</span>"""

content = content.replace(old_user_list_header, new_user_list_header)

# Make table cells slightly more compact
content = content.replace("p-3.5 sm:p-4", "p-3")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated UI texts and added filters.")
