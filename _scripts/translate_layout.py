file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace NAVIGATION MENU
content = content.replace("uppercase\">NAVIGATION MENU</h3>", "uppercase\">{{ __('NAVIGATION MENU') }}</h3>")

# Replace Sidebar labels
sidebar_labels = [
    "Dashboard",
    "Waitlist",
    "Reports & Database",
    "Tutorial & Guidelines",
    "Calendar",
    "Profile",
    "Account Management",
    "Role & User Permission",
    "Logout"
]

for label in sidebar_labels:
    # Look for exact text in a span or text node. Usually it's in a span or directly inside anchor
    # E.g. <span class="font-bold">Dashboard</span> or >Dashboard</span>
    content = content.replace(f">{label}<", f">{{ __('{label}') }}<")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated app-layout.blade.php translations.")
