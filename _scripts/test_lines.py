with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()

start_idx = -1
end_idx = -1

for i, line in enumerate(lines):
    if "<!-- CUSTOMER HABITS VIEW -->" in line:
        start_idx = i
    if "<!-- WAITLIST VIEW -->" in line:
        end_idx = i

print(f"Start: {start_idx}, End: {end_idx}")
for i in range(end_idx - 5, end_idx):
    print(lines[i].rstrip())
