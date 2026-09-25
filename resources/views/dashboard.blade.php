@if (auth()->check() && auth()->user()->isSuperAdmin())
    <script>
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('outlet_id')) {
                const savedUrl = localStorage.getItem('meja_last_dashboard_url');
                if (savedUrl && savedUrl !== window.location.href) {
                    window.location.replace(savedUrl);
                    return;
                }
            }
            localStorage.setItem('meja_last_dashboard_url', window.location.href);
        })();
    </script>
@endif

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('floorCanvas', () => ({
            zoom: parseFloat(localStorage.getItem('meja_floor_zoom')) || 1,
            editMode: 'table',
            gridSections: [],
            isResizingSection: false,
            justResizedSection: false,
            resizeIdx: -1,
            startX: 0,
            startY: 0,
            startW: 0,
            startH: 0,
            newGridWidth: {{ $gridWidth > 0 ? $gridWidth : 15 }},
            newGridHeight: {{ $gridHeight > 0 ? $gridHeight : 10 }},
            gridSizeError: '',
            
            getMinGridWidth() {
                let maxW = 5;
                let placedTables = {!! json_encode($tables->where('grid_x', '>', 0)->map(fn($t) => ['x' => (int)$t->grid_x, 'w' => (int)$t->width])->values()) !!};
                placedTables.forEach(t => { if (t.x + t.w - 1 > maxW) maxW = t.x + t.w - 1; });
                this.gridSections.forEach(s => { if (s.x + s.w - 1 > maxW) maxW = s.x + s.w - 1; });
                return maxW;
            },
            getMinGridHeight() {
                let maxH = 5;
                let placedTables = {!! json_encode($tables->where('grid_x', '>', 0)->map(fn($t) => ['y' => (int)$t->grid_y, 'h' => (int)$t->height])->values()) !!};
                placedTables.forEach(t => { if (t.y + t.h - 1 > maxH) maxH = t.y + t.h - 1; });
                this.gridSections.forEach(s => { if (s.y + s.h - 1 > maxH) maxH = s.y + s.h - 1; });
                return maxH;
            },
            validateGridSize(e) {
                let maxW = 5;
                let maxH = 5;
                
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
                
                if (requestedW < maxW || requestedH < maxH) {
                    e.preventDefault();
                    this.gridSizeError = 'Cannot reduce size. A table or section is occupying up to column ' + maxW + ' and row ' + maxH + '. Please move them first.';
                } else {
                    this.gridSizeError = '';
                }
            },
            isResizingSection: false,
            justResizedSection: false,
            resizeIdx: -1,
            startX: 0,
            startY: 0,
            startW: 0,
            startH: 0,
            settingsModalOpen: {{ $errors->has('table_number') ? 'true' : 'false' }},
            gridSizeModalOpen: false,
            modalOpen: false,
            inventoryModalOpen: false,
            activeModalTab: 'seating',
            selectedTableId: null,
            selectedInventoryTable: null,
            tableNumber: '',
            tableCapacity: 4,
            tableWidth: 2,
            tableHeight: 2,
            tableGridX: null,
            tableGridY: null,
            tableStatus: 'available',
            paxCount: '',
            paxError: '',
            bookmarkedTables: [],
            liveTables: {!! json_encode(
                $tables->keyBy('id')->map(
                    fn($t) => [
                        'status' => $t->status,
                        'pax' => $t->pax,
                        'table_number' => $t->table_number ?? '',
                        'capacity' => $t->capacity ?? 0,
                    ],
                ),
            ) !!},

            sectionTemplates: {!! $sectionTemplatesStr ?? '[]' !!},
            sectionModalOpen: false,
            newSecName: '', newSecColor: 'blue', newSecW: 5, newSecH: 5,
            editSecTemplateIdx: -1,
            editPlacedSecIdx: -1,
            
            
            getMaxTableWidth() {
                if (!this.tableGridX || !this.tableGridY) return 10;
                let maxAllowedW = Math.min(10, this.newGridWidth - this.tableGridX + 1);
                this.placedTablesBounds.forEach(t => {
                    if (t.id === this.selectedTableId) return;
                    if (!(this.tableGridY > t.y + t.h - 1 || this.tableGridY + this.tableHeight - 1 < t.y)) {
                        if (t.x > this.tableGridX) {
                            maxAllowedW = Math.min(maxAllowedW, t.x - this.tableGridX);
                        }
                    }
                });
                return maxAllowedW;
            },
            getMaxTableHeight() {
                if (!this.tableGridX || !this.tableGridY) return 10;
                let maxAllowedH = Math.min(10, this.newGridHeight - this.tableGridY + 1);
                this.placedTablesBounds.forEach(t => {
                    if (t.id === this.selectedTableId) return;
                    if (!(this.tableGridX > t.x + t.w - 1 || this.tableGridX + this.tableWidth - 1 < t.x)) {
                        if (t.y > this.tableGridY) {
                            maxAllowedH = Math.min(maxAllowedH, t.y - this.tableGridY);
                        }
                    }
                });
                return maxAllowedH;
            },
            getMaxSectionWidth() {
                if (this.editPlacedSecIdx < 0) return this.newGridWidth;
                let sec = this.gridSections[this.editPlacedSecIdx];
                let maxAllowedW = this.newGridWidth - sec.x + 1;
                this.gridSections.forEach((s, i) => {
                    if (i === this.editPlacedSecIdx) return;
                    if (!(sec.y > s.y + s.h - 1 || sec.y + this.newSecH - 1 < s.y)) {
                        if (s.x > sec.x) {
                            maxAllowedW = Math.min(maxAllowedW, s.x - sec.x);
                        }
                    }
                });
                return maxAllowedW;
            },
            getMaxSectionHeight() {
                if (this.editPlacedSecIdx < 0) return this.newGridHeight;
                let sec = this.gridSections[this.editPlacedSecIdx];
                let maxAllowedH = this.newGridHeight - sec.y + 1;
                this.gridSections.forEach((s, i) => {
                    if (i === this.editPlacedSecIdx) return;
                    if (!(sec.x > s.x + s.w - 1 || sec.x + this.newSecW - 1 < s.x)) {
                        if (s.y > sec.y) {
                            maxAllowedH = Math.min(maxAllowedH, s.y - sec.y);
                        }
                    }
                });
                return maxAllowedH;
            },
            init() {
                // Load grid sections from blade
                this.gridSections = {!! $gridSectionsStr ?? '[]' !!};

                // Restore zoom
                let savedZoom = localStorage.getItem('meja_floor_zoom');
                if (savedZoom !== null && !isNaN(parseFloat(savedZoom))) {
                    this.zoom = parseFloat(savedZoom);
                }
                this.$watch('zoom', (val) => {
                    localStorage.setItem('meja_floor_zoom', val);
                });

                // Bookmarks cleanup
                let stored = JSON.parse(localStorage.getItem('meja_bookmarked_tables') || '[]').map(
                    Number);
                let validIds = @json($tables->pluck('id'));
                this.bookmarkedTables = stored.filter(id => validIds.includes(id));
                localStorage.setItem('meja_bookmarked_tables', JSON.stringify(this
                    .bookmarkedTables));

                // Live background poll every 3 seconds
                setInterval(() => {
                    this.fetchLiveStatus();
                }, 3000);
            },

            fetchLiveStatus() {
                let outletParam = new URLSearchParams(window.location.search).get('outlet_id') ||
                '';
                let url = '/api/tables/status' + (outletParam ? '?outlet_id=' + outletParam : '');

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(item => {
                            if (this.liveTables[item.id]) {
                                this.liveTables[item.id].status = item.status;
                                this.liveTables[item.id].pax = item.pax;
                                if (item.table_number !== undefined) {
                                    this.liveTables[item.id].table_number = item
                                        .table_number;
                                }
                            } else {
                                this.liveTables[item.id] = {
                                    status: item.status,
                                    pax: item.pax,
                                    table_number: item.table_number || '',
                                    capacity: item.capacity || 0
                                };
                            }

                            if (this.selectedTableId === item.id) {
                                this.tableStatus = item.status;
                            }
                        });
                    })
                    .catch(err => console.debug('Live poll skipped:', err));
            },

            setZoom(val) {
                this.zoom = parseFloat(val.toFixed(2));
            },

            getSectionStyle(sec) {
                let left = (sec.x - 1) * 40;
                let top = (sec.y - 1) * 40;
                let width = sec.w * 40 - 2;
                let height = sec.h * 40 - 2;
                return `left: ${left}px; top: ${top}px; width: ${width}px; height: ${height}px;`;
            },
            getSectionColorClass(color) {
                if (color === 'blue') return 'bg-blue-900/40 border-blue-800/80';
                if (color === 'emerald') return 'bg-emerald-900/40 border-emerald-800/80';
                if (color === 'rose') return 'bg-rose-900/40 border-rose-800/80';
                if (color === 'amber') return 'bg-amber-900/40 border-amber-800/80';
                if (color === 'purple') return 'bg-purple-900/40 border-purple-800/80';
                return 'bg-slate-900/40 border-slate-800/80';
            },
            
            moveSection(idx, col, row) {
                this.gridSections[idx].x = col;
                this.gridSections[idx].y = row;
                this.saveGridSections();
            },
            
            placedTablesBounds: {!! json_encode(
                $tables->where('grid_x', '>', 0)->map(fn($t) => [
                    'id' => $t->id,
                    'x' => (int) $t->grid_x,
                    'y' => (int) $t->grid_y,
                    'w' => (int) $t->width,
                    'h' => (int) $t->height
                ])->values()
            ) !!},
            isResizingTable: false,
            justResizedTable: false,
            resizingTableId: null,
            startTableResize(e, id, gridX, gridY, startW, startH) {
                this.gridX = gridX;
                this.gridY = gridY;
                e.preventDefault();
                this.isResizingTable = true;
                this.resizingTableId = id;
                this.startX = e.clientX;
                this.startY = e.clientY;
                this.startW = startW;
                this.startH = startH;
                
                this.resizeMoveHandler = this.onTableResizeMove.bind(this);
                this.resizeUpHandler = this.onTableResizeUp.bind(this);
                document.addEventListener('mousemove', this.resizeMoveHandler);
                document.addEventListener('mouseup', this.resizeUpHandler);
            },
            onTableResizeMove(e) {
                if(!this.isResizingTable) return;
                let dx = (e.clientX - this.startX) / this.zoom;
                let dy = (e.clientY - this.startY) / this.zoom;
                
                let proposedW = Math.max(1, Math.min(10, this.startW + Math.round(dx / 40)));
                let proposedH = Math.max(1, Math.min(10, this.startH + Math.round(dy / 40)));
                
                let maxAllowedW = Math.min(10, this.newGridWidth - this.gridX + 1);
                let maxAllowedH = Math.min(10, this.newGridHeight - this.gridY + 1);
                
                this.placedTablesBounds.forEach(t => {
                    if (t.id === this.resizingTableId) return;
                    
                    // Check if it's blocking horizontal expansion
                    if (!(this.gridY > t.y + t.h - 1 || this.gridY + proposedH - 1 < t.y)) {
                        if (t.x > this.gridX) {
                            maxAllowedW = Math.min(maxAllowedW, t.x - this.gridX);
                        }
                    }
                    
                    // Check if it's blocking vertical expansion
                    if (!(this.gridX > t.x + t.w - 1 || this.gridX + proposedW - 1 < t.x)) {
                        if (t.y > this.gridY) {
                            maxAllowedH = Math.min(maxAllowedH, t.y - this.gridY);
                        }
                    }
                });
                
                let newW = Math.min(proposedW, maxAllowedW);
                let newH = Math.min(proposedH, maxAllowedH);
                
                let el = document.getElementById('table-' + this.resizingTableId);
                if(el) {
                    el.style.width = `calc((${newW} * 38px) + ((${newW} - 1) * 2px))`;
                    el.style.height = `calc((${newH} * 38px) + ((${newH} - 1) * 2px))`;
                    el.dataset.newW = newW;
                    el.dataset.newH = newH;
                }
            },
            onTableResizeUp(e) {
                if(!this.isResizingTable) return;
                this.isResizingTable = false;
                this.justResizedTable = true;
                setTimeout(() => { this.justResizedTable = false; }, 100);
                document.removeEventListener('mousemove', this.resizeMoveHandler);
                document.removeEventListener('mouseup', this.resizeUpHandler);
                
                let el = document.getElementById('table-' + this.resizingTableId);
                if(el && el.dataset.newW && el.dataset.newH) {
                    fetch('/tables/' + this.resizingTableId + '/coordinates', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ width: parseInt(el.dataset.newW), height: parseInt(el.dataset.newH) })
                    }).then(res => { if(res.ok) window.location.reload(); });
                }
            },
            startSectionResize(e, idx) {
                e.preventDefault();
                this.isResizingSection = true;
                this.resizeIdx = idx;
                this.startX = e.clientX;
                this.startY = e.clientY;
                this.startW = this.gridSections[idx].w;
                this.startH = this.gridSections[idx].h;
                
                this.resizeMoveHandler = this.onSectionResizeMove.bind(this);
                this.resizeUpHandler = this.onSectionResizeUp.bind(this);
                document.addEventListener('mousemove', this.resizeMoveHandler);
                document.addEventListener('mouseup', this.resizeUpHandler);
            },
            onSectionResizeMove(e) {
                if(!this.isResizingSection) return;
                let dx = (e.clientX - this.startX) / this.zoom;
                let dy = (e.clientY - this.startY) / this.zoom;
                
                let newW = Math.max(1, this.startW + Math.round(dx / 40));
                let newH = Math.max(1, this.startH + Math.round(dy / 40));
                
                let section = this.gridSections[this.resizeIdx];
                let maxAllowedW = this.newGridWidth - section.x + 1;
                let maxAllowedH = this.newGridHeight - section.y + 1;
                
                this.gridSections.forEach((s, i) => {
                    if (i === this.resizeIdx) return;
                    
                    // Check if it's blocking horizontal expansion
                    if (!(section.y > s.y + s.h - 1 || section.y + newH - 1 < s.y)) {
                        if (s.x > section.x) {
                            maxAllowedW = Math.min(maxAllowedW, s.x - section.x);
                        }
                    }
                    
                    // Check if it's blocking vertical expansion
                    if (!(section.x > s.x + s.w - 1 || section.x + newW - 1 < s.x)) {
                        if (s.y > section.y) {
                            maxAllowedH = Math.min(maxAllowedH, s.y - section.y);
                        }
                    }
                });
                
                this.gridSections[this.resizeIdx].w = Math.min(newW, maxAllowedW);
                this.gridSections[this.resizeIdx].h = Math.min(newH, maxAllowedH);
            },
            onSectionResizeUp(e) {
                document.removeEventListener('mousemove', this.resizeMoveHandler);
                document.removeEventListener('mouseup', this.resizeUpHandler);
                this.isResizingSection = false;
                this.justResizedSection = true;
                setTimeout(() => { this.justResizedSection = false; }, 100);
                this.saveGridSections();
            },
            
            saveGridSections() {
                let payload = {
                    grid_width: {{ $gridWidth > 0 ? $gridWidth : 15 }},
                    grid_height: {{ $gridHeight > 0 ? $gridHeight : 10 }},
                    grid_sections: JSON.stringify(this.gridSections)
                };
                @if(isset($selectedOutletId)) payload.outlet_id = '{{ $selectedOutletId }}'; @endif
                
                fetch('/settings/grid-size', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                }); // Fire and forget, UI is already updated optimistically
            },
            
            saveSectionTemplates() {
                let payload = {
                    grid_width: {{ $gridWidth > 0 ? $gridWidth : 15 }},
                    grid_height: {{ $gridHeight > 0 ? $gridHeight : 10 }},
                    section_templates: JSON.stringify(this.sectionTemplates)
                };
                @if(isset($selectedOutletId)) payload.outlet_id = '{{ $selectedOutletId }}'; @endif
                
                fetch('/settings/grid-size', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
            },
            
            openSectionTemplateModal(idx) {
                this.editSecTemplateIdx = idx;
                this.editPlacedSecIdx = -1;
                let sec = this.sectionTemplates[idx];
                this.newSecName = sec.name;
                this.newSecColor = sec.color;
                this.newSecW = sec.w;
                this.newSecH = sec.h;
                this.sectionModalOpen = true;
            },
            
            openPlacedSectionModal(idx) {
                this.editPlacedSecIdx = idx;
                this.editSecTemplateIdx = -1;
                let sec = this.gridSections[idx];
                this.newSecName = sec.name;
                this.newSecColor = sec.color;
                this.newSecW = sec.w;
                this.newSecH = sec.h;
                this.sectionModalOpen = true;
            },
            
            saveSection() {
                if(!this.newSecName) {
                    alert('Please enter a section name.');
                    return;
                }
                let sec = {
                    name: this.newSecName, color: this.newSecColor, 
                    w: parseInt(this.newSecW), h: parseInt(this.newSecH),
                    bookmarked: false
                };
                
                if (this.editSecTemplateIdx >= 0) {
                    sec.bookmarked = this.sectionTemplates[this.editSecTemplateIdx].bookmarked;
                    this.sectionTemplates[this.editSecTemplateIdx] = sec;
                    this.saveSectionTemplates();
                } else if (this.editPlacedSecIdx >= 0) {
                    sec.x = this.gridSections[this.editPlacedSecIdx].x;
                    sec.y = this.gridSections[this.editPlacedSecIdx].y;
                    this.gridSections[this.editPlacedSecIdx] = sec;
                    this.saveGridSections();
                } else {
                    this.sectionTemplates = [...this.sectionTemplates, sec];
                    this.saveSectionTemplates();
                }
                this.sectionModalOpen = false;
            },
            
            deleteSection() {
                if (this.editSecTemplateIdx >= 0) {
                    this.sectionTemplates.splice(this.editSecTemplateIdx, 1);
                    this.saveSectionTemplates();
                } else if (this.editPlacedSecIdx >= 0) {
                    this.gridSections.splice(this.editPlacedSecIdx, 1);
                    this.saveGridSections();
                }
                this.sectionModalOpen = false;
            },
            
            placeSectionFromTemplate(tplIdx, col, row) {
                let tpl = this.sectionTemplates[tplIdx];
                let newSec = {
                    name: tpl.name, color: tpl.color,
                    w: tpl.w, h: tpl.h,
                    x: col, y: row
                };
                this.gridSections = [...this.gridSections, newSec];
                if (!tpl.bookmarked) {
                    this.sectionTemplates.splice(tplIdx, 1);
                    this.saveSectionTemplates();
                }
                this.saveGridSections();
            },
            
            unplaceSection(secIdx) {
                let sec = this.gridSections[secIdx];
                this.sectionTemplates = [...this.sectionTemplates, {
                    name: sec.name, color: sec.color, w: sec.w, h: sec.h, bookmarked: false
                }];
                this.gridSections.splice(secIdx, 1);
                this.saveSectionTemplates();
                this.saveGridSections();
            },
            
            isBookmarked(id) {
                return this.bookmarkedTables.includes(Number(id));
            },

            toggleBookmark(id) {
                let numId = Number(id);
                if (this.isBookmarked(numId)) {
                    this.bookmarkedTables = this.bookmarkedTables.filter(x => x !== numId);
                } else {
                    this.bookmarkedTables.push(numId);
                }
                localStorage.setItem('meja_bookmarked_tables', JSON.stringify(this
                    .bookmarkedTables));
            },

            openInventoryModal(table) {
                this.selectedInventoryTable = table;
                this.inventoryModalOpen = true;
            },

            appendPax(num) {
                let val = (this.paxCount || '').toString() + num;
                let parsed = parseInt(val);
                if (!isNaN(parsed)) {
                    this.paxCount = parsed;
                    this.validatePax();
                }
            },
            clearPax() {
                this.paxCount = '';
                this.paxError = '';
            },
            decrementPax() {
                let current = parseInt(this.paxCount) || 0;
                this.paxCount = Math.max(1, current - 1);
                this.validatePax();
            },
            incrementPax() {
                let current = parseInt(this.paxCount) || 0;
                this.paxCount = current + 1;
                this.validatePax();
            },
            validatePax() {
                if (this.paxCount && parseInt(this.paxCount) > parseInt(this.tableCapacity)) {
                    this.paxError = `Exceeds max capacity (${this.tableCapacity} Pax)`;
                } else {
                    this.paxError = '';
                }
            }
        }));
    });
</script>

<x-app-layout title="Meja-O | Interactive Floor Plan">
    <div x-data="floorCanvas">

        
<main class="max-w-[84rem] mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4">

            <div class="grid grid-cols-1 lg:grid-cols-7 gap-4 sm:gap-5 items-start">

                <!-- ADMIN INVENTORY: Order-2 on Mobile, 2 Columns on Desktop -->
                @if (Auth::user()->hasPermission('dash_create_table'))
                    <div class="order-2 lg:order-1 lg:col-span-2 space-y-4">

                        <!-- EDIT MODE TRAY -->
                        @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                        <div class="bg-white p-1 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col">
                            <div class="flex bg-slate-100 p-1 rounded-xl">
                                <button type="button" @click="editMode = 'table'" :class="editMode === 'table' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[11px] font-bold rounded-lg transition">Table Edit</button>
                                <button type="button" @click="editMode = 'section'" :class="editMode === 'section' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-1.5 text-[11px] font-bold rounded-lg transition">Section Edit</button>
                            </div>
                        </div>
                        @endif

                        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-3.5">

                            <button @click="settingsModalOpen = true"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-3 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2 active:scale-98">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Create New Table</span>
                            </button>

                            <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-xs tracking-wide uppercase">Unplaced
                                        Inventory</h3>
                                    <p class="text-[11px] text-slate-500">Drag to floor or click card to edit/delete</p>
                                </div>
                                <span
                                    class="bg-slate-100 text-slate-700 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-slate-200">
                                    {{ $tables->where('grid_x', 0)->count() }}
                                </span>
                            </div>

                            <!-- Unplaced Inventory Tray -->
                            <div class="space-y-2 max-h-[260px] sm:max-h-[420px] overflow-y-auto pr-1 border border-dashed border-slate-200 p-2.5 rounded-xl bg-slate-50/70"
                                @dragover.prevent
                                @drop.prevent="
                                let tableId = $event.dataTransfer.getData('text/plain');
                                fetch('/tables/' + tableId + '/coordinates', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ grid_x: 0, grid_y: 0 })
                                }).then(res => { if(res.ok) window.location.reload(); });
                             ">
                                @forelse($tables->where('grid_x', 0) as $table)
                                    <div
                                        class="bg-white border border-slate-200 shadow-soft-xs hover:border-slate-400 hover:shadow-md rounded-xl p-3 flex items-center justify-between transition group select-none">

                                        <!-- Left Section: Clickable to Edit specs -->
                                        <div class="flex items-center gap-3 cursor-pointer flex-1 mr-2"
                                            @click="openInventoryModal({
                                            id: {{ $table->id }},
                                            table_number: '{{ $table->table_number ?? '' }}',
                                            capacity: {{ $table->capacity ?? 0 }},
                                            width: {{ $table->width }},
                                            height: {{ $table->height }},
                                            shape: '{{ $table->shape }}'
                                        })">
                                            <div
                                                class="w-4 h-4 {{ $table->shape === 'circle' ? 'rounded-full' : ($table->shape === 'triangle' ? 'clip-triangle' : 'rounded-sm') }} bg-slate-800 flex-shrink-0">
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 group-hover:text-slate-800">
                                                    {{ $table->table_number ?: '—' }}
                                                </p>
                                                <p class="text-[10px] text-slate-500 font-medium">
                                                    {{ $table->capacity ? $table->capacity . ' Pax' : '0 Pax' }}
                                                    ({{ $table->width }}×{{ $table->height }})
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Right Controls: Bookmark Template Button & Draggable Handle -->
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <button type="button" @click.stop="toggleBookmark({{ $table->id }})"
                                                :title="isBookmarked({{ $table->id }}) ?
                                                    'Template is active (drag will create copies)' :
                                                    'Lock as template to duplicate'"
                                                :class="isBookmarked({{ $table->id }}) ?
                                                    'text-amber-500 bg-amber-50 border-amber-300 ring-2 ring-amber-100' :
                                                    'text-slate-400 bg-slate-50 border-slate-200 hover:text-amber-500 hover:bg-amber-50'"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg border transition">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17 3H7a2 2 0 00-2 2v16l7-3 7 3V5a2 2 0 00-2-2z" />
                                                </svg>
                                            </button>

                                            <div draggable="true"
                                                @dragstart="$event.dataTransfer.setData('text/plain', '{{ $table->id }}')"
                                                class="text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-2.5 py-1 rounded-lg cursor-grab active:cursor-grabbing transition flex items-center gap-1">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M4 8h16M4 16h16" />
                                                </svg>
                                                <span>Drag Me</span>
                                            </div>
                                        </div>

                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                            ✓</div>
                                        <p class="text-xs text-slate-500 font-medium">No Available Tables</p>
                                    </div>
                                @endforelse
                            </div>

                        </div>

                        <!-- SECTION INVENTORY TRAY -->
                        @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                        
                        <!-- SECTION INVENTORY TRAY CONTAINER -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-soft-xs space-y-3.5 mb-4">
                            <!-- + Create New Section Button -->
                            <button type="button" @click="sectionModalOpen = true; editSecTemplateIdx = -1; editPlacedSecIdx = -1; newSecName = ''; newSecColor = 'blue'; newSecW = 5; newSecH = 5;"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-3 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2 active:scale-98">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Create New Section</span>
                            </button>

                            <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-xs tracking-wide uppercase">Unplaced Sections</h3>
                                    <p class="text-[11px] text-slate-500">Drag to floor or click card to edit/delete</p>
                                </div>
                                <span class="bg-slate-100 text-slate-700 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-slate-200" x-text="sectionTemplates.length">
                                </span>
                            </div>

                            <div class="space-y-2 max-h-[260px] sm:max-h-[420px] overflow-y-auto pr-1 border border-dashed border-slate-200 p-2.5 rounded-xl bg-slate-50/70"
                                @dragover.prevent
                                @drop.prevent="
                                if (editMode === 'section') {
                                    let secIdx = $event.dataTransfer.getData('sectionIdx');
                                    if (secIdx !== '') unplaceSection(parseInt(secIdx));
                                }
                             ">
                                <template x-for="(sec, idx) in sectionTemplates" :key="idx">
                                    <div class="bg-white border border-slate-200 shadow-soft-xs hover:border-slate-400 hover:shadow-md rounded-xl p-3 flex items-center justify-between transition group select-none">
                                        
                                        <!-- Left Side: Color, Name, Size -->
                                        <div class="flex items-center gap-3 cursor-pointer flex-1 mr-2"
                                            @click="openSectionTemplateModal(idx)">
                                            <div class="w-4 h-4 rounded-sm flex-shrink-0" :class="'bg-' + sec.color + '-500'"></div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 group-hover:text-slate-800" x-text="sec.name"></p>
                                                <p class="text-[10px] text-slate-500 font-medium" x-text="sec.w + 'x' + sec.h"></p>
                                            </div>
                                        </div>

                                        <!-- Right Controls: Bookmark Template Button & Draggable Handle -->
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <button type="button" @click.stop="sec.bookmarked = !sec.bookmarked; saveSectionTemplates();"
                                                :title="sec.bookmarked ? 'Template is active (drag will create copies)' : 'Lock as template to duplicate'"
                                                :class="sec.bookmarked ? 'text-amber-500 bg-amber-50 border-amber-300 ring-2 ring-amber-100' : 'text-slate-400 bg-slate-50 border-slate-200 hover:text-amber-500 hover:bg-amber-50'"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg border transition">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17 3H7a2 2 0 00-2 2v16l7-3 7 3V5a2 2 0 00-2-2z" />
                                                </svg>
                                            </button>

                                            <div draggable="true"
                                                @dragstart="if(editMode === 'section') { $event.dataTransfer.setData('templateIdx', idx); }"
                                                class="text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-2.5 py-1 rounded-lg cursor-grab active:cursor-grabbing transition flex items-center gap-1"
                                                :class="editMode === 'section' ? '' : 'opacity-50 cursor-not-allowed pointer-events-none'">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                                </svg>
                                                <span>Drag Me</span>
                                            </div>
                                        </div>

                                    </div>
                                </template>
                                
                                <div x-show="sectionTemplates.length === 0" class="text-center py-8">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">✓</div>
                                    <p class="text-xs text-slate-500 font-medium">No Available Sections</p>
                                </div>
                            </div>

                        </div>
                        @endif

                    </div>
                @endif

                <!-- 2D FLOOR PLAN CANVAS -->
                @if (Auth::user()->hasPermission('dash_floor_canvas'))
                    <div
                        class="order-1 lg:order-2 {{ Auth::user()->hasPermission('dash_create_table') ? 'lg:col-span-5' : 'lg:col-span-7' }} space-y-3">

                    <!-- Map Toolbar Bar -->
                    <div
                        class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200/80 shadow-soft-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">

                        <div class="flex items-center justify-between sm:justify-start gap-3">
                            <div>
                                <h2
                                    class="text-sm sm:text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                                    <span>2D Floor Canvas</span>
                                </h2>
                                <p class="text-[11px] text-slate-500 font-medium flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Available
                                    </span>
                                    <span class="text-slate-300">·</span>
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Occupied
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Persistent Zoom Controls & Action Buttons -->
                        <div
                            class="flex flex-wrap items-center justify-start sm:justify-end gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">

                            <div class="flex items-center gap-2">
                                @if (auth()->check() && auth()->user()->isSuperAdmin())
                                    <form method="GET" action="/" class="inline-flex m-0">
                                        <select name="outlet_id" onchange="this.form.submit()"
                                            class="text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 shadow-soft-xs h-9 transition cursor-pointer">
                                            @foreach ($outlets ?? [] as $outlet)
                                                <option value="{{ $outlet->id }}"
                                                    {{ ($selectedOutletId ?? '') == $outlet->id ? 'selected' : '' }}>
                                                    {{ $outlet->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif

                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <button @click="gridSizeModalOpen = true"
                                        class="inline-flex items-center justify-center bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-3 py-1.5 h-9 rounded-xl transition border border-slate-200 shadow-soft-xs">
                                        Canvas Size
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/80">
                                <button @click="setZoom(Math.max(0.5, zoom - 0.1))"
                                    class="w-7 h-7 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-lg text-sm flex items-center justify-center border border-slate-200 shadow-soft-xs active:scale-90 transition">-</button>
                                <span class="text-xs font-semibold text-slate-800 w-12 text-center tabular-nums"
                                    x-text="Math.round(zoom * 100) + '%'"></span>
                                <button @click="setZoom(Math.min(1.8, zoom + 0.1))"
                                    class="w-7 h-7 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-lg text-sm flex items-center justify-center border border-slate-200 shadow-soft-xs active:scale-90 transition">+</button>
                            </div>

                        </div>
                    </div>

                    <!-- 2D Canvas Workspace -->
                    <div
                        class="bg-slate-950 -mx-3 sm:mx-0 rounded-none sm:rounded-2xl border-t border-b-0 border-x-0 sm:border border-slate-800 overflow-auto p-2 sm:p-4 relative max-h-[70vh] sm:max-h-[600px] h-auto shadow-soft-xl touch-scroll">

                        @php
                            $width = (int) ($gridWidth ?? 0);
                            $height = (int) ($gridHeight ?? 0);

                            $placedTables = $tables
                                ->where('grid_x', '>', 0)
                                ->filter(function ($t) use ($width, $height) {
                                    return $t->grid_x <= $width && $t->grid_y <= $height;
                                });
                        @endphp

                        @if ($width == 0 || $height == 0)
                            <div
                                class="w-full py-16 flex flex-col items-center justify-center text-center p-6 space-y-3">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-200">Canvas dimensions uninitialized</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Set grid dimensions to begin
                                        mapping tables.</p>
                                </div>
                                @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <button @click="gridSizeModalOpen = true"
                                        class="bg-white hover:bg-slate-100 text-slate-900 text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition">
                                        Set Dimensions (X × Y)
                                    </button>
                                @endif
                            </div>
                        @else
                            <!-- Tightly bounded grid board with persistent CSS zoom -->
                            <div class="inline-grid border border-slate-800 relative select-none"
                                :style="'zoom: ' + zoom +
                                    '; grid-template-columns: repeat({{ $width }}, 38px); grid-template-rows: repeat({{ $height }}, 38px); gap: 2px; background-color: #0f172a; width: max-content; height: max-content; min-width: max-content; min-height: max-content; grid-auto-columns: 0px; grid-auto-rows: 0px;'">

                                @for ($row = 1; $row <= $height; $row++)
                                    @for ($col = 1; $col <= $width; $col++)
                                        <div @dragover.prevent
                                            @drop.prevent="
                                            if (editMode === 'section') {
                                                let secIdx = $event.dataTransfer.getData('sectionIdx');
                                                let tplIdx = $event.dataTransfer.getData('templateIdx');
                                                
                                                if (secIdx !== '') {
                                                    moveSection(parseInt(secIdx), {{ $col }}, {{ $row }});
                                                } else if (tplIdx !== '') {
                                                    placeSectionFromTemplate(parseInt(tplIdx), {{ $col }}, {{ $row }});
                                                }
                                            } else {
                                                let tableId = $event.dataTransfer.getData('text/plain');
                                                if (!tableId) return;
                                                let bm = JSON.parse(localStorage.getItem('meja_bookmarked_tables') || '[]');
                                                let isClone = bm.includes(parseInt(tableId));
                                                let endpoint = isClone ? ('/tables/' + tableId + '/clone') : ('/tables/' + tableId + '/coordinates');
                                                fetch(endpoint, {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                    body: JSON.stringify({ grid_x: {{ $col }}, grid_y: {{ $row }} })
                                                }).then(res => { if(res.ok) window.location.reload(); });
                                            }
                                         "
                                            class="border border-slate-800/80 bg-slate-900 w-[38px] h-[38px]"
                                            style="grid-column: {{ $col }}; grid-row: {{ $row }};">
                                        </div>
                                    @endfor
                                @endfor
                                
                                <!-- Render interactive sections overlay -->
                                <template x-for="(sec, idx) in gridSections" :key="idx">
                                    <div class="absolute border-2 rounded-xl transition-all flex flex-col justify-center items-center"
                                         :style="getSectionStyle(sec)"
                                         :class="[
                                            getSectionColorClass(sec.color),
                                            editMode === 'section' ? 'pointer-events-auto cursor-grab active:cursor-grabbing hover:ring-2 ring-white/50 shadow-lg z-10' : 'pointer-events-none z-0'
                                         ]"
                                         :draggable="editMode === 'section'"
                                         @dragstart="if(editMode === 'section') { $event.dataTransfer.setData('sectionIdx', idx); }"
                                         >
                                         <div x-show="editMode === 'section'" @click.stop="if(justResizedSection) return; openPlacedSectionModal(idx)" class="w-full h-full relative group cursor-pointer">
                                             <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200 bg-black/10 rounded-xl">
                                                 <span class="bg-white/90 text-slate-900 p-2 rounded-lg shadow-sm font-bold flex items-center justify-center">
                                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                 </span>
                                             </div>
                                             
                                             <!-- Resize Handle (bottom-right) -->
                                             <div class="absolute -bottom-[2px] -right-[2px] w-[14px] h-[14px] bg-white border-2 border-slate-900 rounded-full cursor-se-resize shadow pointer-events-auto z-20"
                                                  @mousedown.stop.prevent="startSectionResize($event, idx)" @click.stop=""></div>
                                         </div>
                                    </div>
                                </template>

                                @foreach ($placedTables as $table)
                                    @php
                                        $tableSpanW = min(
                                            (int) $table->width,
                                            max(1, $width - (int) $table->grid_x + 1),
                                        );
                                        $tableSpanH = min(
                                            (int) $table->height,
                                            max(1, $height - (int) $table->grid_y + 1),
                                        );
                                    @endphp
                                    <div id="table-{{ $table->id }}" draggable="{{ Auth::user()->hasPermission('dash_create_table') ? 'true' : 'false' }}"
                                        @if (Auth::user()->hasPermission('dash_create_table')) @dragstart="$event.dataTransfer.setData('text/plain', '{{ $table->id }}')" @endif
                                        @click="
                                        if (justResizedTable) return;
                                        selectedTableId = {{ $table->id }};
                                        tableNumber = liveTables[{{ $table->id }}]?.table_number ?? '{{ $table->table_number ?? '' }}';
                                        tableCapacity = liveTables[{{ $table->id }}]?.capacity ?? {{ $table->capacity ?? 0 }};
                                        tableWidth = {{ $table->width }};
                                        tableHeight = {{ $table->height }};
                                        tableGridX = {{ $table->grid_x }};
                                        tableGridY = {{ $table->grid_y }};
                                        tableStatus = liveTables[{{ $table->id }}]?.status ?? '{{ $table->status }}';
                                        paxCount = '';
                                        paxError = '';
                                        activeModalTab = '{{ Auth::user()->hasPermission('dash_guest_seating') ? 'seating' : (Auth::user()->hasPermission('dash_waitlist') ? 'waitlist' : 'settings') }}';
                                        modalOpen = true;
                                     "
                                        :class="[
                                            (liveTables[{{ $table->id }}]?.status || '{{ $table->status }}') === 'available'
                                                ? 'bg-emerald-950/85 border-emerald-500 text-emerald-300 ' + (editMode === 'table' ? 'hover:bg-emerald-900' : '')
                                                : 'bg-rose-950/85 border-rose-500 text-rose-200 ' + (editMode === 'table' ? 'hover:bg-rose-900' : ''),
                                            editMode === 'section' ? 'pointer-events-none opacity-30 grayscale blur-[0.5px] z-0' : 'cursor-pointer hover:z-30 hover:shadow-lg z-20 active:scale-95'
                                        ]"
                                        class="absolute border select-none transition-all duration-300 flex flex-col items-center justify-center {{ $table->shape === 'circle' ? 'rounded-full' : ($table->shape === 'triangle' ? '' : 'rounded-xl') }}"
                                        style="
                                        grid-column: {{ $table->grid_x }} / span {{ $tableSpanW }}; 
                                        grid-row: {{ $table->grid_y }} / span {{ $tableSpanH }};
                                        width: calc(({{ $tableSpanW }} * 38px) + (({{ $tableSpanW }} - 1) * 2px));
                                        height: calc(({{ $tableSpanH }} * 38px) + (({{ $tableSpanH }} - 1) * 2px));
                                        @if ($table->shape === 'triangle') clip-path: polygon(50% 0%, 0% 100%, 100% 100%); padding-top: 12px; @endif
                                     ">

                                        <div class="text-center pointer-events-none space-y-0 px-1">
                                            <div class="flex items-center justify-center gap-1">
                                                <p class="text-[10px] font-extrabold tracking-tight leading-none text-white drop-shadow"
                                                    x-text="liveTables[{{ $table->id }}]?.table_number || '{{ $table->table_number ?: '—' }}'">
                                                </p>
                                            </div>
                                            <p class="text-[10px] font-semibold opacity-85 leading-none mt-1"
                                                x-text="(liveTables[{{ $table->id }}]?.status || '{{ $table->status }}') === 'occupied' 
                                                ? ((liveTables[{{ $table->id }}]?.pax || '{{ $table->pax ?? $table->capacity }}') + 'p')
                                                : ((liveTables[{{ $table->id }}]?.capacity || {{ $table->capacity ?? 0 }}) + 'p')">
                                            </p>
                                        </div>
                                        <template x-if="editMode === 'table'">
                                            <div class="absolute -bottom-[2px] -right-[2px] w-[14px] h-[14px] bg-white border-2 border-slate-900 rounded-full cursor-se-resize shadow pointer-events-auto z-30"
                                                 @mousedown.stop.prevent="startTableResize($event, {{ $table->id }}, {{ $table->grid_x }}, {{ $table->grid_y }}, {{ $tableSpanW }}, {{ $tableSpanH }})" @click.stop=""></div>
                                        </template>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </main>

        <!-- TABLE MANAGE POP-UP (Canvas Placed Table) -->
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/40 sm:px-4" x-cloak>

            <div class="bg-white rounded-t-3xl sm:rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto p-5 sm:p-6 shadow-soft-xl border border-slate-100 space-y-4 transition-all safe-pb"
                @click.away="modalOpen = false">

                <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto sm:hidden mb-1"></div>

                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-slate-900">
                            Table <span x-text="tableNumber || '—'"></span>
                        </h3>
                        <span class="text-slate-300">·</span>
                        <span class="text-xs font-semibold text-slate-500">
                            Max Capacity: <strong class="text-slate-800" x-text="tableCapacity"></strong> Pax
                        </span>
                    </div>
                    <button @click="modalOpen = false"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-xs transition">
                        ✕
                    </button>
                </div>

                <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
                    @if (Auth::user()->hasPermission('dash_guest_seating'))
                        <button type="button" @click="activeModalTab = 'seating'"
                            :class="activeModalTab === 'seating' ? 'bg-white text-slate-900 shadow-soft-xs font-bold' :
                                'text-slate-600 font-medium hover:text-slate-900'"
                            class="flex-1 text-[11px] font-bold py-2 rounded-lg transition text-center truncate px-1">
                            Guest Seating
                        </button>
                    @endif
                    @if (Auth::user()->hasPermission('dash_waitlist'))
                        <button type="button" @click="activeModalTab = 'waitlist'"
                            :class="activeModalTab === 'waitlist' ? 'bg-white text-slate-900 shadow-soft-xs font-bold' :
                                'text-slate-600 font-medium hover:text-slate-900'"
                            class="flex-1 text-[11px] font-bold py-2 rounded-lg transition text-center truncate px-1">
                            Waitlist
                        </button>
                    @endif
                    @if (Auth::user()->hasPermission('dash_edit_table'))
                        <button type="button" @click="activeModalTab = 'settings'"
                            :class="activeModalTab === 'settings' ? 'bg-white text-slate-900 shadow-soft-xs font-bold' :
                                'text-slate-600 font-medium hover:text-slate-900'"
                            class="flex-1 text-[11px] font-bold py-2 rounded-lg transition text-center truncate px-1">
                            Edit Specs
                        </button>
                    @endif
                </div>

                @if (Auth::user()->hasPermission('dash_guest_seating'))
                <!-- TAB 1: GUEST SEATING -->
                <div x-show="activeModalTab === 'seating'" class="space-y-4">

                    <template x-if="tableStatus === 'available'">
                        <form :action="'/tables/' + selectedTableId + '/seat'" method="POST"
                            @submit.prevent="
                            if (parseInt(paxCount) > parseInt(tableCapacity)) {
                                paxError = 'Warning: ' + paxCount + ' Pax exceeds capacity (' + tableCapacity + ' Pax)';
                                return;
                            }
                            // Instantly update local state so there is zero lag
                            liveTables[selectedTableId].status = 'occupied';
                            liveTables[selectedTableId].pax = paxCount;
                            tableStatus = 'occupied';
                            modalOpen = false;

                            // Submit in background
                            fetch('/tables/' + selectedTableId + '/seat', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: 'pax=' + encodeURIComponent(paxCount)
                            }).then(() => fetchLiveStatus());
                        ">
                            @csrf

                            <div class="w-full max-w-[270px] mx-auto space-y-3 pt-1">

                                <template x-if="paxError">
                                    <div
                                        class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-xl text-xs font-semibold text-center">
                                        <span x-text="paxError"></span>
                                    </div>
                                </template>

                                <div
                                    class="bg-slate-50 border-2 border-slate-200 focus-within:border-slate-800 focus-within:bg-white rounded-2xl p-2 px-3 flex items-center justify-between transition">
                                    <button type="button" @click="decrementPax()"
                                        class="w-10 h-10 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-lg flex items-center justify-center border border-slate-200 shadow-soft-xs active:scale-90 transition shrink-0">-</button>

                                    <div class="text-center flex-1 px-2">
                                        <span
                                            class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-tight">Total
                                            Guests</span>
                                        <input type="number" name="pax" min="1"
                                            x-model.number="paxCount" placeholder="0" required
                                            class="w-full text-2xl font-black border-none focus:ring-0 p-0 text-slate-900 bg-transparent text-center placeholder-slate-300 tabular-nums leading-tight">
                                    </div>

                                    <button type="button" @click="incrementPax()"
                                        class="w-10 h-10 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-lg flex items-center justify-center border border-slate-200 shadow-soft-xs active:scale-90 transition shrink-0">+</button>
                                </div>

                                <div class="grid grid-cols-3 gap-2 w-full">
                                    <button type="button" @click="appendPax(1)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">1</button>
                                    <button type="button" @click="appendPax(2)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">2</button>
                                    <button type="button" @click="appendPax(3)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">3</button>

                                    <button type="button" @click="appendPax(4)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">4</button>
                                    <button type="button" @click="appendPax(5)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">5</button>
                                    <button type="button" @click="appendPax(6)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">6</button>

                                    <button type="button" @click="appendPax(7)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">7</button>
                                    <button type="button" @click="appendPax(8)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">8</button>
                                    <button type="button" @click="appendPax(9)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">9</button>

                                    <button type="button" @click="clearPax()"
                                        class="h-12 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs rounded-xl transition border border-rose-200/80 active:scale-95">CLEAR</button>
                                    <button type="button" @click="appendPax(0)"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-base rounded-xl transition border border-slate-200/80 active:scale-95">0</button>
                                    <button type="button"
                                        @click="paxCount = paxCount ? paxCount.toString().slice(0, -1) : ''; validatePax();"
                                        class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-base rounded-xl transition border border-slate-200/80 flex items-center justify-center active:scale-95">⌫</button>
                                </div>

                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-3.5 rounded-xl transition shadow-sm active:scale-98">
                                    Seat Guests & Start Session
                                </button>
                            </div>
                        </form>
                    </template>

                    <template x-if="tableStatus === 'occupied'">
                        <div class="w-full max-w-[270px] mx-auto space-y-4 py-3">
                            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 text-center">
                                <p class="text-xs font-bold text-rose-900">Table is Occupied</p>
                                <p class="text-[11px] text-rose-600 mt-0.5">Guests are currently seated.</p>
                            </div>
                            <form :action="'/tables/' + selectedTableId + '/finish'" method="POST"
                                @submit.prevent="
                                // Instantly switch local state
                                liveTables[selectedTableId].status = 'available';
                                liveTables[selectedTableId].pax = null;
                                tableStatus = 'available';
                                modalOpen = false;

                                // Submit in background
                                fetch('/tables/' + selectedTableId + '/finish', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                }).then(() => fetchLiveStatus());
                            ">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3.5 rounded-xl transition shadow-sm active:scale-98">
                                    Complete Session (Free Table)
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
                @endif

                @if (Auth::user()->hasPermission('dash_waitlist'))
                <!-- TAB 2: WAITLIST -->
                <div x-show="activeModalTab === 'waitlist'" class="space-y-3 pt-1">
                    <div class="text-[11px] font-semibold text-slate-500 mb-2">Waitlist Customers (Pax &le; <span x-text="tableCapacity"></span>)</div>
                    <div class="space-y-2 max-h-[350px] overflow-y-auto pr-1 touch-scroll">
                        @forelse ($waitlist as $w)
                            <div x-show="{{ $w->pax }} <= parseInt(tableCapacity || 99)" class="bg-slate-50 border border-slate-200 p-3 rounded-xl flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-slate-800 text-xs">{{ $w->customer_name ?: 'Walk-in' }}</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">{{ $w->phone ?: 'No Phone' }} &bull; {{ \Carbon\Carbon::parse($w->created_at)->diffForHumans() }}</div>
                                </div>
                                <div class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded-md font-bold text-[10px]">
                                    {{ $w->pax }} Pax
                                </div>
                            </div>
                        @empty
                            <div class="text-xs font-semibold text-slate-400 text-center py-6 border-2 border-dashed border-slate-100 rounded-xl">No waitlist customers.</div>
                        @endforelse
                    </div>
                </div>
                @endif

                <!-- TAB 3: EDIT TABLE SPECS -->
                @if (Auth::user()->hasPermission('dash_edit_table'))
                    <div x-show="activeModalTab === 'settings'" class="space-y-3.5 pt-1">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Table Number</label>
                                <input type="text" x-model="tableNumber" placeholder="e.g. T-01"
                                    class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Capacity
                                    (Pax)</label>
                                <input type="number" min="1" x-model="tableCapacity" placeholder="e.g. 4"
                                    class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                <span class="text-[11px] font-semibold text-slate-600">Width:</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="tableWidth = Math.max(1, tableWidth - 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">-</button>
                                    <span class="text-xs font-bold text-slate-900 w-4 text-center tabular-nums"
                                        x-text="tableWidth"></span>
                                    <button type="button" @click="tableWidth = Math.min(getMaxTableWidth(), tableWidth + 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">+</button>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                <span class="text-[11px] font-semibold text-slate-600">Height:</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="tableHeight = Math.max(1, tableHeight - 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">-</button>
                                    <span class="text-xs font-bold text-slate-900 w-4 text-center tabular-nums"
                                        x-text="tableHeight"></span>
                                    <button type="button" @click="tableHeight = Math.min(getMaxTableHeight(), tableHeight + 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">+</button>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="
                        fetch('/tables/' + selectedTableId + '/coordinates', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ table_number: tableNumber, capacity: tableCapacity, width: tableWidth, height: tableHeight })
                        }).then(res => { if(res.ok) window.location.reload(); });
                    "
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-xl transition shadow-sm active:scale-98">
                            Save Table Specifications
                        </button>

                        <div class="pt-2 border-t border-slate-100">
                            <form :action="'/tables/' + selectedTableId" method="POST"
                                onsubmit="return confirm('WARNING: Are you sure you want to completely delete this table from the system?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold py-2.5 rounded-xl transition border border-rose-200">
                                    Delete Table Permanently
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- INVENTORY CARD MODAL: Edit / Delete directly from Inventory Tray -->
        @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
            <div x-show="inventoryModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/40 sm:px-4"
                x-cloak>

                <div class="bg-white rounded-t-3xl sm:rounded-2xl w-full max-w-sm p-5 sm:p-6 shadow-soft-xl border border-slate-100 space-y-4 safe-pb"
                    @click.away="inventoryModalOpen = false" x-data="{ invWidth: 2, invHeight: 2, invNumber: '', invCapacity: '' }" x-init="$watch('selectedInventoryTable', t => {
                        if (t) {
                            invNumber = t.table_number || '';
                            invCapacity = t.capacity || '';
                            invWidth = t.width;
                            invHeight = t.height;
                        }
                    })">

                    <div class="w-10 h-1 bg-slate-300 rounded-full mx-auto sm:hidden mb-1"></div>

                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Edit Inventory Table</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Edit table specs or delete directly from
                                inventory.</p>
                        </div>
                        <button @click="inventoryModalOpen = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs transition">✕</button>
                    </div>

                    <template x-if="selectedInventoryTable">
                        <div class="space-y-3.5">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Table Name /
                                        No.</label>
                                    <input type="text" x-model="invNumber" placeholder="e.g. T-01"
                                        class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Capacity
                                        (Pax)</label>
                                    <input type="number" min="1" x-model="invCapacity" placeholder="e.g. 4"
                                        class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div
                                    class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                    <span class="text-[11px] font-semibold text-slate-600">Width:</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="invWidth = Math.max(1, invWidth - 1)"
                                            class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center border border-slate-200">-</button>
                                        <span class="text-xs font-bold text-slate-900 w-4 text-center tabular-nums"
                                            x-text="invWidth"></span>
                                        <button type="button" @click="invWidth = Math.min(5, invWidth + 1)"
                                            class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center border border-slate-200">+</button>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                    <span class="text-[11px] font-semibold text-slate-600">Height:</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="invHeight = Math.max(1, invHeight - 1)"
                                            class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center border border-slate-200">-</button>
                                        <span class="text-xs font-bold text-slate-900 w-4 text-center tabular-nums"
                                            x-text="invHeight"></span>
                                        <button type="button" @click="invHeight = Math.min(5, invHeight + 1)"
                                            class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center border border-slate-200">+</button>
                                    </div>
                                </div>
                            </div>

                            <button
                                @click="
                            fetch('/tables/' + selectedInventoryTable.id + '/coordinates', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({ table_number: invNumber, capacity: invCapacity, width: invWidth, height: invHeight, grid_x: 0, grid_y: 0 })
                            }).then(res => { if(res.ok) { inventoryModalOpen = false; window.location.reload(); } });
                        "
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-xl transition shadow-sm">
                                Save Changes
                            </button>

                            <div class="pt-1 border-t border-slate-100">
                                <form :action="'/tables/' + selectedInventoryTable.id" method="POST"
                                    onsubmit="return confirm('Delete this table from inventory permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold py-2.5 rounded-xl border border-rose-200 transition">
                                        Delete Table Permanently
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ADMIN POP-UP: CONFIGURE CANVAS DIMENSIONS -->
            <div x-show="gridSizeModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/40 sm:px-4"
                x-cloak>
                <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-sm w-full shadow-soft-xl border border-slate-100 safe-pb flex flex-col overflow-hidden"
                    @click.away="gridSizeModalOpen = false">
                    
                    <div class="p-5 sm:p-6 bg-white w-full">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">Canvas Dimensions</h3>
                                <p class="text-[11px] text-slate-500">Configure floor width and height grid units.</p>
                            </div>
                            <button type="button" @click="gridSizeModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form method="POST" action="/settings/grid-size" @submit="validateGridSize($event)">
                            @csrf
                            <input type="hidden" name="grid_sections" :value="JSON.stringify(gridSections)">
                            <input type="hidden" name="section_templates" :value="JSON.stringify(sectionTemplates)">
                            @if(isset($selectedOutletId)) <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}"> @endif
                            
                            <template x-if="gridSizeError">
                                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-xl text-xs font-semibold flex items-start gap-2 mb-4">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span x-text="gridSizeError"></span>
                                </div>
                            </template>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Width (Columns)</label>
                                    <input type="number" name="grid_width" :min="getMinGridWidth()" max="40" x-model="newGridWidth" required class="w-full text-sm p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-900 font-bold text-center">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Height (Rows)</label>
                                    <input type="number" name="grid_height" :min="getMinGridHeight()" max="40" x-model="newGridHeight" required class="w-full text-sm p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-900 font-bold text-center">
                                </div>
                            </div>

                            <div class="flex gap-2.5 pt-6">
                                @if ($gridWidth > 0 && $gridHeight > 0)
                                    <button type="button" @click="gridSizeModalOpen = false" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-3 rounded-xl transition">Cancel</button>
                                @endif
                                <button type="submit" class="{{ $gridWidth > 0 && $gridHeight > 0 ? 'w-1/2' : 'w-full' }} bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-3 rounded-xl shadow-md transition active:scale-98">
                                    Apply Size
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ADMIN POP-UP: CREATE NEW TABLE -->
            <div x-show="settingsModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/40 sm:px-4"
                x-cloak>
                <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-soft-xl border border-slate-100 space-y-4 safe-pb"
                    @click.away="settingsModalOpen = false">

                    <div class="w-10 h-1 bg-slate-300 rounded-full mx-auto sm:hidden mb-1"></div>

                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900">Create New Table</h3>
                            <p class="text-[11px] text-slate-500">Define table specifications and dimensions.</p>
                        </div>
                        <button @click="settingsModalOpen = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-xs transition">✕</button>
                    </div>

                    @if ($errors->has('table_number'))
                        <div
                            class="bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-xl text-xs font-semibold flex items-start gap-2">
                            <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <span>{{ $errors->first('table_number') }}</span>
                        </div>
                    @endif

                    <form action="/tables/custom" method="POST" class="space-y-3.5 pt-1" x-data="{ newWidth: 2, newHeight: 2 }">
                        @csrf
                        @if (isset($selectedOutletId))
                            <input type="hidden" name="outlet_id" value="{{ $selectedOutletId }}">
                        @endif
                        <input type="hidden" name="grid_x" value="0">
                        <input type="hidden" name="grid_y" value="0">
                        <input type="hidden" name="width" x-model="newWidth">
                        <input type="hidden" name="height" x-model="newHeight">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Table Number
                                    (Opt.)</label>
                                <input type="text" name="table_number" value="{{ old('table_number') }}"
                                    placeholder="e.g. T-01"
                                    class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Capacity
                                    (Pax)</label>
                                <input type="number" name="capacity" min="1" value="{{ old('capacity') }}"
                                    placeholder="e.g. 4"
                                    class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Geometry Shape</label>
                            <select name="shape"
                                class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-700">
                                <option value="square">Square / Rectangle</option>
                                <option value="circle">Round / Circle</option>
                                <option value="triangle">Corner / Triangle</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                <span class="text-[11px] font-semibold text-slate-600">Width:</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="newWidth = Math.max(1, newWidth - 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">-</button>
                                    <span class="text-xs font-bold text-slate-900 w-3 text-center tabular-nums"
                                        x-text="newWidth"></span>
                                    <button type="button" @click="newWidth = Math.min(4, newWidth + 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">+</button>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                <span class="text-[11px] font-semibold text-slate-600">Height:</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="newHeight = Math.max(1, newHeight - 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">-</button>
                                    <span class="text-xs font-bold text-slate-900 w-3 text-center tabular-nums"
                                        x-text="newHeight"></span>
                                    <button type="button" @click="newHeight = Math.min(4, newHeight + 1)"
                                        class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">+</button>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-xl transition shadow-sm mt-2 active:scale-98">
                            Confirm & Add to Inventory
                        </button>
                    </form>
                </div>
            </div>
        @endif
<!-- Section Template Modal -->
    <div x-show="sectionModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/40 sm:px-4" x-cloak>
            <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-md w-full shadow-soft-xl border border-slate-100 safe-pb overflow-hidden"
                @click.away="sectionModalOpen = false">
                <div class="p-5 sm:p-6 bg-white space-y-4 relative">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900" x-text="editSecTemplateIdx >= 0 ? 'Edit Section Template' : (editPlacedSecIdx >= 0 ? 'Edit Placed Section' : 'Create New Section')"></h3>
                            <p class="text-[11px] text-slate-500">Configure layout section properties.</p>
                        </div>
                        <button type="button" @click="sectionModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-xs transition">?</button>
                    </div>
                    
                    <div class="space-y-3.5 pt-1">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Section Name</label>
                                <input type="text" x-model="newSecName" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-900" placeholder="e.g. VIP">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Section Color</label>
                                <select x-model="newSecColor" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-700">
                                    <option value="blue">Blue</option>
                                    <option value="emerald">Emerald</option>
                                    <option value="amber">Yellow</option>
                                    <option value="rose">Red</option>
                                    <option value="purple">Purple</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                <span class="text-[11px] font-semibold text-slate-600">Width:</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="newSecW = Math.max(1, parseInt(newSecW) - 1)" class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">-</button>
                                    <span class="text-xs font-bold text-slate-900 w-4 text-center tabular-nums" x-text="newSecW"></span>
                                    <button type="button" @click="newSecW = Math.min(getMaxSectionWidth(), parseInt(newSecW) + 1)" class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">+</button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-300">
                                <span class="text-[11px] font-semibold text-slate-600">Height:</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="newSecH = Math.max(1, parseInt(newSecH) - 1)" class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">-</button>
                                    <span class="text-xs font-bold text-slate-900 w-4 text-center tabular-nums" x-text="newSecH"></span>
                                    <button type="button" @click="newSecH = Math.min(getMaxSectionHeight(), parseInt(newSecH) + 1)" class="w-6 h-6 bg-white hover:bg-slate-200 text-slate-800 font-bold rounded-md flex items-center justify-center transition border border-slate-200">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-100 space-y-2 mt-4">
                        <button type="button" @click="saveSection()" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-xl shadow-md transition active:scale-98">Save</button>
                        <button type="button" x-show="editSecTemplateIdx >= 0 || editPlacedSecIdx >= 0" @click="deleteSection()" class="w-full bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold py-2.5 rounded-xl transition border border-rose-200">Delete Section Permanently</button>
                        <button type="button" x-show="editSecTemplateIdx < 0 && editPlacedSecIdx < 0" @click="sectionModalOpen = false" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 rounded-xl transition border border-slate-200">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Section Modal -->
    </div>
</x-app-layout>
