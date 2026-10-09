file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Add a wrapper for the left column and insert the search bar
old_left_col = """            @if (Auth::user()->hasPermission('acc_create_account'))
                <div class="w-full lg:w-[320px] shrink-0 bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">"""

new_left_col = """            @if (Auth::user()->hasPermission('acc_create_account'))
                <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                    <!-- Search Bar -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-3">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Search User</label>
                        <form method="GET" action="/account-management" class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
                                class="flex-1 w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                            <button type="submit"
                                class="bg-slate-900 hover:bg-slate-800 text-white font-bold w-10 h-[38px] rounded-xl flex items-center justify-center transition shadow-soft-xs active:scale-98 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-4">"""

content = content.replace(old_left_col, new_left_col)

# Close the new wrapper div at the end of the create account card
old_create_end = """                        </div>
                    </form>
                </div>
            @endif"""

new_create_end = """                        </div>
                    </form>
                </div>
                </div>
            @endif"""

content = content.replace(old_create_end, new_create_end)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated account-management.blade.php layout & search.")
