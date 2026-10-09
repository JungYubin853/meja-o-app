file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_waitlist_rep = """                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Waitlist Report</span>
                                        <input type="checkbox" x-model="permissions.rep_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>"""
new_waitlist_rep = """                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Waitlist Report</span>
                                        <input type="checkbox" x-model="permissions.rep_waitlist" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>
                                    <label class="flex items-center justify-between p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                        <span class="text-xs font-semibold text-slate-700">Customer Habits</span>
                                        <input type="checkbox" x-model="permissions.rep_habits" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                                    </label>"""
content = content.replace(old_waitlist_rep, new_waitlist_rep)


old_profile = """                            <!-- Profile -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Profile</span>
                                <input type="checkbox" x-model="permissions.nav_profile" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>"""
new_profile = """                            <!-- Tutorial & Guidelines -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Tutorial & Guidelines</span>
                                <input type="checkbox" x-model="permissions.nav_tutorial" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Calendar -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Calendar</span>
                                <input type="checkbox" x-model="permissions.nav_calendar" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>

                            <!-- Profile -->
                            <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                <span class="text-sm font-bold text-slate-800">Profile</span>
                                <input type="checkbox" x-model="permissions.nav_profile" class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            </label>"""
content = content.replace(old_profile, new_profile)

# Also update the Alpine.js default data initialization!
old_alpine_default = """                            rep_monthly: data.role === 'admin',
                            rep_yearly: data.role === 'admin',
                            rep_waitlist: data.role === 'admin' || data.role === 'staff',
                            nav_profile: data.role === 'admin' || data.role === 'staff',"""

new_alpine_default = """                            rep_monthly: data.role === 'admin',
                            rep_yearly: data.role === 'admin',
                            rep_waitlist: data.role === 'admin' || data.role === 'staff',
                            rep_habits: data.role === 'admin',
                            nav_tutorial: data.role === 'admin' || data.role === 'staff',
                            nav_calendar: data.role === 'admin' || data.role === 'staff',
                            nav_profile: data.role === 'admin' || data.role === 'staff',"""

content = content.replace(old_alpine_default, new_alpine_default)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated permission.blade.php with new permissions")
