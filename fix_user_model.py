file_path = r"C:\Users\LEGION\Herd\meja-o-app\app\Models\User.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_func = """    public function getDefaultPermissions()
    {
        if ($this->isSuperAdmin()) {
            return []; // Super Admin has access to everything
        }

        return [
            'nav_dashboard' => true,
            'dash_create_table' => $this->isAdmin(),
            'dash_floor_canvas' => true,
            'dash_guest_seating' => true,
            'dash_waitlist' => true,
            'dash_edit_table' => $this->isAdmin(),
            'nav_waitlist' => true,
            'nav_reports' => true,
            'rep_overall' => $this->isAdmin(),
            'rep_daily' => true,
            'rep_monthly' => $this->isAdmin(),
            'rep_yearly' => $this->isAdmin(),
            'rep_waitlist' => true,
            'rep_habits' => $this->isAdmin(),
            'nav_tutorial' => true,
            'nav_calendar' => true,
            'nav_profile' => true,
            'nav_account_mgmt' => $this->isAdmin(),
            'acc_create_account' => false,
            'acc_user_list' => $this->isAdmin(),
            'nav_role_permission' => false,
        ];
    }"""

new_func = """    public function getHardcodedDefaults()
    {
        if ($this->isSuperAdmin()) {
            return [];
        }

        return [
            'nav_dashboard' => true,
            'dash_create_table' => $this->isAdmin(),
            'dash_floor_canvas' => true,
            'dash_guest_seating' => true,
            'dash_waitlist' => true,
            'dash_edit_table' => $this->isAdmin(),
            'nav_waitlist' => true,
            'nav_reports' => true,
            'rep_overall' => $this->isAdmin(),
            'rep_daily' => true,
            'rep_monthly' => $this->isAdmin(),
            'rep_yearly' => $this->isAdmin(),
            'rep_waitlist' => true,
            'rep_habits' => $this->isAdmin(),
            'nav_tutorial' => true,
            'nav_calendar' => true,
            'nav_profile' => true,
            'nav_account_mgmt' => $this->isAdmin(),
            'acc_create_account' => false,
            'acc_user_list' => $this->isAdmin(),
            'nav_role_permission' => false,
        ];
    }

    public function getDefaultPermissions()
    {
        if ($this->isSuperAdmin()) {
            return []; // Super Admin has access to everything
        }

        try {
            $superAdmin = static::where('role', 'super_admin')->first();
            if ($superAdmin && !empty($superAdmin->permissions)) {
                $saved = is_string($superAdmin->permissions) ? json_decode($superAdmin->permissions, true) : (array)$superAdmin->permissions;
                
                if ($this->role === 'staff' && isset($saved['_staff_defaults'])) {
                    return $saved['_staff_defaults'];
                }
                if ($this->role === 'admin' && isset($saved['_admin_defaults'])) {
                    return $saved['_admin_defaults'];
                }
            }
        } catch (\Exception $e) {
            // Fallback in case of DB issues during migration/setup
        }

        return $this->getHardcodedDefaults();
    }"""

content = content.replace(old_func, new_func)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated User.php to support dynamic defaults.")
