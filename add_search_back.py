file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# 1. Update Alpine JS
old_script_start = """                bulkOverwrite: false,

                init() {"""
new_script_start = """                bulkOverwrite: false,
                searchQuery: '',

                init() {"""
content = content.replace(old_script_start, new_script_start)

old_search_user = """                async searchUser(email) {
                    this.error = '';
                    this.successMsg = '';
                    this.loading = true;
                    this.user = null;
                    this.bulkMode = null;
                    
                    try {
                        const response = await fetch('/api/permissions/search?email=' + encodeURIComponent(email));"""
new_search_user = """                async searchUser(query = null) {
                    const term = query || this.searchQuery;
                    if (!term) return;
                    
                    this.error = '';
                    this.successMsg = '';
                    this.loading = true;
                    this.user = null;
                    this.bulkMode = null;
                    
                    try {
                        const response = await fetch('/api/permissions/search?email=' + encodeURIComponent(term));"""
content = content.replace(old_search_user, new_search_user)


# 2. Update HTML (Add search bar before Bulk Templates)
old_left_html = """            <!-- LEFT COLUMN: Bulk Template Management -->
            <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">"""

new_left_html = """            <!-- LEFT COLUMN: Bulk Template Management & Search -->
            <div class="w-full lg:w-[320px] shrink-0 space-y-6">
                
                <!-- Search User -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-3">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Search User</label>
                    <form @submit.prevent="searchUser()" class="flex gap-2">
                        <input type="text" x-model="searchQuery" required placeholder="Search name or email..."
                            class="flex-1 w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                        <button type="submit" :disabled="loading"
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold w-10 h-[38px] rounded-xl flex items-center justify-center transition shadow-soft-xs active:scale-98 disabled:opacity-50 shrink-0">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </form>
                    <template x-if="error">
                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold mt-2" x-text="error"></div>
                    </template>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col overflow-hidden">"""

content = content.replace(old_left_html, new_left_html)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated permission.blade.php with Search block.")
