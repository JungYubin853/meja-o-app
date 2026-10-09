file_path = r"C:\Users\LEGION\Herd\meja-o-app\app\Models\User.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_perms = """            'rep_waitlist' => true,
            'rep_habits' => $this->isAdmin(),
            'nav_profile' => true,"""

new_perms = """            'rep_waitlist' => true,
            'rep_habits' => $this->isAdmin(),
            'nav_tutorial' => true,
            'nav_calendar' => true,
            'nav_profile' => true,"""

content = content.replace(old_perms, new_perms)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated User.php")
