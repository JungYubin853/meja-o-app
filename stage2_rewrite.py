file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

new_script = """    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('permissionManager', () => ({
                loading: false,
                saving: false,
                error: '',
                successMsg: '',
                user: null,
                permissions: {},

                bulkMode: null,
                bulkStep: 'select',
                divergentUsers: [],
                bulkOverwrite: false,

                init() {
                    const params = new URLSearchParams(window.location.search);
                    const email = params.get('email');
                    if (email) {
                        this.searchUser(email);
                    }
                },

                async searchUser(email) {
                    this.error = '';
                    this.successMsg = '';
                    this.loading = true;
                    this.user = null;
                    this.bulkMode = null;
                    
                    try {
                        const response = await fetch('/api/permissions/search?email=' + encodeURIComponent(email));
                        if (!response.ok) {
                            throw new Error('User not found.');
                        }
                        const data = await response.json();
                        if (data.role === 'super_admin') {
                            throw new Error('Cannot edit Super Admin permissions.');
                        }
                        
                        this.user = data;
                        
                        const defaultPerms = {
                            nav_dashboard: data.role === 'admin' || data.role === 'staff',
                            dash_create_table: data.role === 'admin',
                            dash_floor_canvas: data.role === 'admin' || data.role === 'staff',
                            dash_guest_seating: data.role === 'admin' || data.role === 'staff',
                            dash_waitlist: data.role === 'admin' || data.role === 'staff',
                            dash_edit_table: data.role === 'admin',
                            nav_waitlist: data.role === 'admin' || data.role === 'staff',
                            nav_reports: data.role === 'admin' || data.role === 'staff',
                            rep_overall: data.role === 'admin',
                            rep_daily: data.role === 'admin' || data.role === 'staff',
                            rep_monthly: data.role === 'admin',
                            rep_yearly: data.role === 'admin',
                            rep_waitlist: data.role === 'admin' || data.role === 'staff',
                            rep_habits: data.role === 'admin',
                            nav_tutorial: data.role === 'admin' || data.role === 'staff',
                            nav_calendar: data.role === 'admin' || data.role === 'staff',
                            nav_profile: data.role === 'admin' || data.role === 'staff',
                            nav_account_mgmt: data.role === 'admin',
                            acc_create_account: false,
                            acc_user_list: data.role === 'admin',
                            nav_role_permission: false
                        };
                        
                        this.permissions = { ...defaultPerms, ...data.permissions };

                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async initBulk(role) {
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
                },

                saveBulk(overwrite) {
                    this.bulkOverwrite = overwrite;
                    this.bulkStep = 'edit';
                },

                async savePermissions() {
                    this.saving = true;
                    this.successMsg = '';
                    this.error = '';

                    try {
                        const response = await fetch('/api/permissions/update/' + this.user.id, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ permissions: this.permissions })
                        });
                        
                        if (!response.ok) throw new Error('Failed to save permissions.');
                        
                        this.successMsg = 'Permissions updated successfully!';
                        setTimeout(() => this.successMsg = '', 3000);
                    } catch (err) {
                        this.error = err.message;
                    } finally {
                        this.saving = false;
                    }
                },

                async saveBulkPermissions() {
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
                }
            }));
        });
    </script>"""

pattern_script = r"<script>\s*document\.addEventListener\('alpine:init'.*?</script>"
content = re.sub(pattern_script, new_script, content, flags=re.DOTALL)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Alpine.js script.")
