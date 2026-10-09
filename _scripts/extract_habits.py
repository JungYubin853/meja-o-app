file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

start_idx = -1
end_idx = -1
for i, line in enumerate(lines):
    if "@if ($viewMode === 'habits')" in line:
        start_idx = i
    if "<!-- OVERALL VIEW -->" in line:
        end_idx = i
        break

if start_idx != -1 and end_idx != -1:
    print("".join(lines[start_idx:end_idx]))
else:
    print("Not found")
