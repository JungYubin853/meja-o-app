with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

# Let's add some subtle gradient and borders to the holiday banner
content = content.replace(
    'class="mb-4 bg-{{ $holidayType == \'Collective Leave\' ? \'amber\' : \'rose\' }}-50 border border-{{ $holidayType == \'Collective Leave\' ? \'amber\' : \'rose\' }}-200 rounded-xl p-4 flex items-center gap-3"',
    'class="mb-6 bg-gradient-to-r from-{{ $holidayType == \'Collective Leave\' ? \'amber\' : \'rose\' }}-50 to-white border border-{{ $holidayType == \'Collective Leave\' ? \'amber\' : \'rose\' }}-100 rounded-2xl p-5 flex items-center gap-4 shadow-soft-xs"'
)

# Enhance the Daily Log Timeline empty state
content = content.replace(
    '<p class="text-xs text-slate-400 font-semibold text-center py-6">No completed visits on this date.</p>',
    '<div class="flex flex-col items-center justify-center py-10"><svg class="w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8l-8 8 8 8"></path></svg><p class="text-sm text-slate-400 font-semibold">No completed visits recorded on this date.</p></div>'
)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done polishing")
