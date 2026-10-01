with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We need to inject the collision logic functions inside the Alpine x-data object
# Let's find "moveSection(idx, col, row) {"
pattern_move = r"moveSection\(idx, col, row\) \{"
replacement_move = """isValidSectionPlacement(ignoreIdx, x, y, w, h) {
                let gw = {{ $width }};
                let gh = {{ $height }};
                if (x < 1 || y < 1 || x + w - 1 > gw || y + h - 1 > gh) return false;
                for (let i=0; i<this.gridSections.length; i++) {
                    if (i !== ignoreIdx) {
                        let s = this.gridSections[i];
                        if (!(x + w - 1 < s.x || x > s.x + s.w - 1 || y + h - 1 < s.y || y > s.y + s.h - 1)) return false;
                    }
                }
                return true;
            },
            isValidTablePlacement(id, x, y, w, h) {
                let gw = {{ $width }};
                let gh = {{ $height }};
                if (x < 1 || y < 1 || x + w - 1 > gw || y + h - 1 > gh) return false;
                for (let t of this.placedTablesBounds) {
                    if (t.id != id) {
                        if (!(x + w - 1 < t.x || x > t.x + t.w - 1 || y + h - 1 < t.y || y > t.y + t.h - 1)) return false;
                    }
                }
                return true;
            },
            moveSection(idx, col, row) {"""
            
content = content.replace("moveSection(idx, col, row) {", replacement_move)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done inserting helper functions")
