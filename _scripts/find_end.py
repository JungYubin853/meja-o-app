with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    lines = f.readlines()

start_idx = 196
depth = 1
end_idx = -1
for i in range(197, len(lines)):
    line = lines[i]
    if "@if" in line and not "@endif" in line:
        depth += 1
    elif "@endif" in line and not "@if" in line:
        depth -= 1
        if depth == 0:
            end_idx = i
            break

print(f"End idx: {end_idx}")
if end_idx != -1:
    print("Content around end_idx:")
    for i in range(end_idx - 5, end_idx + 5):
        print(f"{i}: {lines[i].rstrip()}")
