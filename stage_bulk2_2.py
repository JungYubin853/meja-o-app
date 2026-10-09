file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_initBulk = """                async initBulk(role) {
                    this.loading = true;
                    this.error = '';
                    this.successMsg = '';
                    this.user = null;
                    this.bulkMode = role;
                    
                    try {
                        const response = await fetch(`/api/permissions/bulk-check?role=${role}`);
                        if (!response.ok) throw new Error('Failed to fetch bulk data.');
                        const data = await response.json();
                        
                        this.divergentUsers = data.divergent_users;
                        this.permissions = data.default_permissions;
                        
                        if (this.divergentUsers.length > 0) {
                            this.bulkStep = 'warn';
                        } else {
                            this.bulkStep = 'edit';
                            this.bulkOverwrite = true;
                        }
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },"""

new_initBulk = """                async initBulk(role, isBaseDefault = false) {
                    this.loading = true;
                    this.error = '';
                    this.successMsg = '';
                    this.user = null;
                    this.bulkMode = isBaseDefault ? role + '_default' : role;
                    
                    try {
                        const response = await fetch(`/api/permissions/bulk-check?role=${role}`);
                        if (!response.ok) throw new Error('Failed to fetch bulk data.');
                        const data = await response.json();
                        
                        this.permissions = data.default_permissions;
                        this.defaultPerms = data.default_permissions;
                        
                        if (isBaseDefault) {
                            this.bulkStep = 'edit';
                            this.bulkOverwrite = false;
                        } else {
                            this.divergentUsers = data.divergent_users;
                            if (this.divergentUsers.length > 0) {
                                this.bulkStep = 'warn';
                            } else {
                                this.bulkStep = 'edit';
                                this.bulkOverwrite = true;
                            }
                        }
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },"""

old_saveBulk = """                async saveBulkPermissions() {
                    this.saving = true;
                    this.successMsg = '';
                    this.error = '';

                    try {
                        const response = await fetch('/api/permissions/bulk-update', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ 
                                role: this.bulkMode,
                                permissions: this.permissions,
                                overwrite_custom: this.bulkOverwrite
                            })
                        });
                        
                        if (!response.ok) throw new Error('Failed to bulk save permissions.');
                        const data = await response.json();
                        
                        this.successMsg = `Permissions successfully applied to ${data.updated_count} ${this.bulkMode}s!`;
                        setTimeout(() => this.successMsg = '', 4000);
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.saving = false;
                    }
                }"""

new_saveBulk = """                async saveBulkPermissions() {
                    this.saving = true;
                    this.successMsg = '';
                    this.error = '';

                    try {
                        const isBaseDefault = this.bulkMode.endsWith('_default');
                        const role = isBaseDefault ? this.bulkMode.replace('_default', '') : this.bulkMode;
                        const endpoint = isBaseDefault ? '/api/permissions/save-default-template' : '/api/permissions/bulk-update';

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ 
                                role: role,
                                permissions: this.permissions,
                                overwrite_custom: this.bulkOverwrite
                            })
                        });
                        
                        if (!response.ok) throw new Error('Failed to bulk save permissions.');
                        const data = await response.json();
                        
                        if (isBaseDefault) {
                            this.successMsg = `Global default template for ${role}s successfully updated!`;
                        } else {
                            this.successMsg = `Permissions successfully applied to ${data.updated_count} ${role}s!`;
                        }
                        
                        setTimeout(() => this.successMsg = '', 4000);
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.saving = false;
                    }
                }"""

content = content.replace(old_initBulk, new_initBulk)
content = content.replace(old_saveBulk, new_saveBulk)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Alpine bulk methods.")
