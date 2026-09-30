with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()

second_overall_idx = -1
count = 0
for i, line in enumerate(lines):
    if "<!-- OVERALL VIEW -->" in line:
        count += 1
        if count == 2:
            second_overall_idx = i
            break

print("Second overall view starts at index:", second_overall_idx)
