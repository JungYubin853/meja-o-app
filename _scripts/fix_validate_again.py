with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re

# We will extract everything from validateGridSize(e) to the next function `isResizingSection: false,`
pattern = r"validateGridSize\(e\) \{[\s\S]*?isResizingSection: false,"
replacement = r"""validateGridSize(e) {
                let maxW = 0;
                let maxH = 0;
                
                let placedTables = {!! json_encode(
                    $tables->where('grid_x', '>', 0)->map(fn($t) => [
                        'x' => (int) $t->grid_x,
                        'y' => (int) $t->grid_y,
                        'w' => (int) $t->width,
                        'h' => (int) $t->height
                    ])->values()
                ) !!};
                
                placedTables.forEach(t => {
                    if (t.x + t.w - 1 > maxW) maxW = t.x + t.w - 1;
                    if (t.y + t.h - 1 > maxH) maxH = t.y + t.h - 1;
                });
                
                this.gridSections.forEach(s => {
                    if (s.x + s.w - 1 > maxW) maxW = s.x + s.w - 1;
                    if (s.y + s.h - 1 > maxH) maxH = s.y + s.h - 1;
                });
                
                let requestedW = parseInt(this.newGridWidth);
                let requestedH = parseInt(this.newGridHeight);
                
                if (requestedW === 0 || requestedH === 0) {
                    if (placedTables.length > 0 || this.gridSections.length > 0) {
                        e.preventDefault();
                        this.gridSizeError = 'It is not possible to empty the 2D Floor Canvas. You need to remove all tables and sections in this outlet that have been placed. Only then you can set it to 0.';
                        return;
                    }
                } else if (requestedW < maxW || requestedH < maxH) {
                    e.preventDefault();
                    this.gridSizeError = 'Cannot reduce size. A table or section is occupying up to column ' + maxW + ' and row ' + maxH + '. Please move them first.';
                    return;
                }
                
                this.gridSizeError = '';
            },
            isResizingSection: false,"""
            
content = re.sub(pattern, replacement, content)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done fixing validateGridSize regex")
