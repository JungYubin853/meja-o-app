import os
import re

views_dir = r"C:\Users\LEGION\Herd\meja-o-app\resources\views"
for root, _, files in os.walk(views_dir):
    for file in files:
        if file.endswith(".blade.php"):
            path = os.path.join(root, file)
            with open(path, "r", encoding="utf-8") as f:
                content = f.read()
            
            # Find instances where {{ __('Text') }} is inside {{ }}
            # Example: {{ $val . ' {{ __('Text') }}' }}
            # We can just look for ' {{ __' or " {{ __" or {{ ... {{ 
            lines = content.split('\n')
            for i, line in enumerate(lines):
                if line.count('{{') > 1 and "__(" in line:
                    # check if they are nested
                    # a simple check is if there is no closing }} before the second {{
                    idx1 = line.find('{{')
                    idx2 = line.find('{{', idx1 + 2)
                    idx_close = line.find('}}', idx1 + 2)
                    if idx2 != -1 and (idx_close == -1 or idx2 < idx_close):
                        print(f"Potential nested error in {file} line {i+1}: {line.strip()}")
