file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "<!-- OVERALL VIEW -->" in line or "HOURLY (DAILY) REPORT VIEW" in line or "habits" in line:
        print(f"Line {i}: {line.strip()}")
