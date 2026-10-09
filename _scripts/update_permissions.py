with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Models\User.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("'rep_waitlist' => true,", "'rep_waitlist' => true,\n            'rep_habits' => $this->isAdmin(),")

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Models\User.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done")
