file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()
for i, line in enumerate(lines):
    if line.count('{{') > 1 and "__(" in line:
        idx1 = line.find('{{')
        idx2 = line.find('{{', idx1 + 2)
        idx_close = line.find('}}', idx1 + 2)
        if idx2 != -1 and (idx_close == -1 or idx2 < idx_close):
            print(f"Line {i+1}: {line.strip()}")
