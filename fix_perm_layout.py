file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1, 2, 3: Layout Wrappers
content = content.replace('<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">', '<div class="flex flex-col lg:flex-row gap-6 items-start w-full">')
content = content.replace('<div class="lg:col-span-4 xl:col-span-4 space-y-6">', '<div class="w-full lg:w-[320px] shrink-0 space-y-6">')
content = content.replace('<div class="lg:col-span-8 xl:col-span-8">', '<div class="flex-1 min-w-0">')

# 4: Search Button
old_search_btn = """                        <button type="submit" :disabled="loading"
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-lg transition shadow-soft-xs active:scale-98 disabled:opacity-50 text-xs">
                            <span x-show="!loading">Search</span>
                            <span x-show="loading">...</span>
                        </button>"""
new_search_btn = """                        <button type="submit" :disabled="loading"
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold w-10 h-[38px] rounded-xl flex items-center justify-center transition shadow-soft-xs active:scale-98 disabled:opacity-50 shrink-0">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>"""
content = content.replace(old_search_btn, new_search_btn)

# Remove shadow-soft-xs space-y-3 padding adjustments to match AM exactly
content = content.replace('p-4 sm:p-5 shadow-soft-xs space-y-3', 'p-4 shadow-soft-xs space-y-3')
content = content.replace('w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900', 'flex-1 w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900')


# 5: Remake User List UI
old_user_list_table = """                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto touch-scroll">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                            <thead class="sticky top-0 bg-slate-50/80 z-10 shadow-sm border-b border-slate-200">
                                <tr class="text-slate-500 text-[10px] uppercase tracking-wider font-bold">
                                    <th class="p-3 sm:p-4">Name / Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="u in displayedUsers" :key="u.id">
                                    <tr class="hover:bg-slate-50 transition cursor-pointer" 
                                        @click="searchEmail = u.email; searchUser(); window.scrollTo({top: 0, behavior: 'smooth'});">
                                        <td class="p-3 sm:p-4">
                                            <div class="font-bold text-slate-800 text-xs" x-text="u.name"></div>
                                            <div class="text-slate-500 text-[11px] font-semibold mt-0.5" x-text="u.email"></div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>"""
new_user_list_table = """                    <div class="flex flex-col divide-y divide-slate-100 max-h-[500px] overflow-y-auto touch-scroll">
                        <template x-for="u in displayedUsers" :key="u.id">
                            <div class="p-3.5 hover:bg-slate-50 transition cursor-pointer flex flex-col gap-0.5 group"
                                :class="user && user.email === u.email ? 'bg-slate-50 border-l-2 border-slate-900' : 'border-l-2 border-transparent'"
                                @click="searchEmail = u.email; searchUser(); window.scrollTo({top: 0, behavior: 'smooth'});">
                                <div class="font-bold text-slate-800 text-xs truncate group-hover:text-slate-900" x-text="u.name"></div>
                                <div class="text-slate-500 text-[10px] font-semibold truncate group-hover:text-slate-700" x-text="u.email"></div>
                            </div>
                        </template>
                        <template x-if="displayedUsers.length === 0">
                            <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                                No users found.
                            </div>
                        </template>
                    </div>"""
content = content.replace(old_user_list_table, new_user_list_table)


with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated permission.blade.php layout and UI.")
