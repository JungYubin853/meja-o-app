import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update the overall layout wrap
new_layout_start = """        @if (Auth::user()->hasPermission('acc_create_account') || Auth::user()->hasPermission('acc_user_list'))
            <div class="flex flex-col lg:flex-row gap-6 items-start w-full">"""

content = content.replace("@if (Auth::user()->hasPermission('acc_create_account') || Auth::user()->hasPermission('acc_user_list'))", new_layout_start)

# 2. Add closing tag for new_layout_start before the @else
content = content.replace("        @else\n            <div class=\"bg-white rounded-2xl", "            </div>\n        @else\n            <div class=\"bg-white rounded-2xl")

# 3. Update Create New Account container width and internal grid
content = content.replace(
    "<div class=\"bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-soft-xs space-y-5\">",
    "<div class=\"w-full lg:w-[320px] shrink-0 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-soft-xs space-y-5\">"
)
content = content.replace("grid grid-cols-1 sm:grid-cols-2 gap-4", "flex flex-col gap-4")
content = content.replace("grid grid-cols-1 sm:grid-cols-3 gap-4", "flex flex-col gap-4")

# 4. Update User List container for pagination and width
old_user_list_start = """                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">User List</h3>
                        <p class="text-xs text-slate-500 font-medium">Read, oversee, and delete staff accounts.</p>
                    </div>
                    <span class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1 rounded-full border border-slate-200">
                        {{ count($allUsers) }} Users
                    </span>
                </div>"""

new_user_list_start = """                <div x-data="{
                    currentPage: 1,
                    perPage: 10,
                    totalItems: {{ count($allUsers) }},
                    get totalPages() { return Math.ceil(this.totalItems / this.perPage) || 1; },
                    nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                    prevPage() { if (this.currentPage > 1) this.currentPage--; }
                }" class="flex-1 min-w-0 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900">User List</h3>
                            <p class="text-xs text-slate-500 font-medium">Read, oversee, and delete staff accounts.</p>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">Rows per page:</span>
                                <select x-model.number="perPage" @change="currentPage = 1" class="h-8 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg px-2 py-0 focus:ring-0 focus:border-slate-300">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-0.5">
                                <button @click="prevPage()" :disabled="currentPage === 1" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-white disabled:opacity-50 disabled:hover:bg-transparent transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <span class="text-xs font-bold text-slate-700 min-w-[3rem] text-center"><span x-text="currentPage"></span> / <span x-text="totalPages"></span></span>
                                <button @click="nextPage()" :disabled="currentPage === totalPages" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-white disabled:opacity-50 disabled:hover:bg-transparent transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>"""

content = content.replace(old_user_list_start, new_user_list_start)

# 5. Apply x-show to table rows
content = content.replace("@foreach ($allUsers as $u)", "@foreach ($allUsers as $index => $u)")
content = content.replace(
    "<tr class=\"hover:bg-slate-50/60 transition\">",
    "<tr class=\"hover:bg-slate-50/60 transition\" x-show=\"currentPage === Math.ceil(({{ $index }} + 1) / perPage)\" x-cloak>"
)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Update complete")
