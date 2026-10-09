file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

start_idx = -1
end_idx = -1
for i, line in enumerate(lines):
    if "Top Bar (One long container)" in line:
        start_idx = i
    if "2. Main Content Box (Tabbed)" in line:
        end_idx = i
        break

if start_idx != -1 and end_idx != -1:
    print("".join(lines[start_idx:end_idx]))
