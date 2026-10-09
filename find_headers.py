file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "Daily Visitor Report" in line or "Monthly Visitor Report" in line or "Yearly Visitor Report" in line or "Waitlist Customer Records" in line or "Analyze customer" in line:
        print(f"Line {i}: {line.strip()}")
