file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace(
    'class="text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition">',
    'class="text-[10px] font-bold uppercase tracking-wider text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition">'
)

content = content.replace(
    '<span class="text-xs text-slate-400 italic">Current User</span>',
    '<span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Current User</span>'
)

# And make the role badge a tiny bit tighter
content = content.replace('px-2 py-0.5 rounded-md text-[10px] font-bold uppercase', 'px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider')

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated buttons and badges")
