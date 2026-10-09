with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

import re
replacement = """
            handleGridDrop(e) {
                let rect = e.currentTarget.getBoundingClientRect();
                let cellWidthScreen = 40 * this.zoom;
                
                // Calculate raw drop column and row
                let dropCol = Math.floor((e.clientX - rect.left) / cellWidthScreen) + 1;
                let dropRow = Math.floor((e.clientY - rect.top) / cellWidthScreen) + 1;

                if (this.editMode === 'section') {
                    let secIdx = e.dataTransfer.getData('sectionIdx');
                    let tplIdx = e.dataTransfer.getData('templateIdx');
                    let w = parseInt(e.dataTransfer.getData('w') || 1);
                    let h = parseInt(e.dataTransfer.getData('h') || 1);
                    let ox = parseInt(e.dataTransfer.getData('ox') || 0);
                    let oy = parseInt(e.dataTransfer.getData('oy') || 0);
                    
                    let targetCol = dropCol - ox;
                    let targetRow = dropRow - oy;
                    
                    if (secIdx !== '') {
                        if (!this.isValidSectionPlacement(parseInt(secIdx), targetCol, targetRow, w, h)) return;
                        this.moveSection(parseInt(secIdx), targetCol, targetRow);
                    } else if (tplIdx !== '') {
                        if (!this.isValidSectionPlacement(-1, targetCol, targetRow, w, h)) return;
                        this.placeSectionFromTemplate(parseInt(tplIdx), targetCol, targetRow);
                    }
                } else {
                    let tableId = e.dataTransfer.getData('text/plain');
                    if (!tableId) return;
                    let w = parseInt(e.dataTransfer.getData('w') || 1);
                    let h = parseInt(e.dataTransfer.getData('h') || 1);
                    let ox = parseInt(e.dataTransfer.getData('ox') || 0);
                    let oy = parseInt(e.dataTransfer.getData('oy') || 0);
                    
                    let targetCol = dropCol - ox;
                    let targetRow = dropRow - oy;
                    
                    if (!this.isValidTablePlacement(parseInt(tableId), targetCol, targetRow, w, h)) {
                        // User requested to prevent dropping if out of bounds or collides
                        // Visual feedback (optional) but for now just return
                        return;
                    }
                    
                    let bm = JSON.parse(localStorage.getItem('meja_bookmarked_tables') || '[]');
                    let isClone = bm.includes(parseInt(tableId));
                    let endpoint = isClone ? ('/tables/' + tableId + '/clone') : ('/tables/' + tableId + '/coordinates');
                    fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ grid_x: targetCol, grid_y: targetRow })
                    }).then(res => { if(res.ok) window.location.reload(); });
                }
            },
            isValidSectionPlacement"""

content = content.replace("isValidSectionPlacement", replacement)

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Done inserting handleGridDrop")
