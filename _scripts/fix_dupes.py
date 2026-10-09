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

main_idx = -1
if second_overall_idx != -1:
    # find the next </main> after the second_overall_idx
    for i in range(len(lines)-1, -1, -1):
        if "</main>" in lines[i]:
            main_idx = i
            break

if second_overall_idx != -1 and main_idx != -1 and second_overall_idx < main_idx:
    del lines[second_overall_idx:main_idx]
    
with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
    f.writelines(lines)
print("Done removing duplicates")
