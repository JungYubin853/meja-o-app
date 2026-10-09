file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_check = r"""    Route::get('/api/permissions/bulk-check', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role; // 'admin' or 'staff'
        $users = App\Models\User::where('role', $role)->get();
        
        $divergentUsers = [];
        foreach($users as $u) {
            if (!empty($u->permissions)) {
                $divergentUsers[] = ['id' => $u->id, 'name' => $u->name, 'email' => $u->email];
            }
        }
        
        $dummyUser = new App\Models\User(['role' => $role]);
        $defaultPerms = $dummyUser->getDefaultPermissions();
        
        return response()->json([
            'divergent_users' => $divergentUsers,
            'default_permissions' => $defaultPerms,
        ]);
    });"""

new_check = r"""    Route::get('/api/permissions/bulk-check', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role; // 'admin' or 'staff'
        $users = App\Models\User::where('role', $role)->get();
        
        $dummyUser = new App\Models\User(['role' => $role]);
        $defaultPerms = $dummyUser->getDefaultPermissions();
        
        $divergentUsers = [];
        foreach($users as $u) {
            if (!empty($u->permissions)) {
                $savedPerms = $u->permissions;
                $differs = false;
                foreach ($defaultPerms as $key => $defaultVal) {
                    $savedVal = array_key_exists($key, $savedPerms) ? $savedPerms[$key] : $defaultVal;
                    if ((bool)$savedVal !== (bool)$defaultVal) {
                        $differs = true;
                        break;
                    }
                }
                if ($differs) {
                    $divergentUsers[] = ['id' => $u->id, 'name' => $u->name, 'email' => $u->email];
                }
            }
        }
        
        return response()->json([
            'divergent_users' => $divergentUsers,
            'default_permissions' => $defaultPerms,
        ]);
    });"""

old_update = r"""    Route::post('/api/permissions/bulk-update', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role;
        $permissions = $request->permissions;
        $overwriteCustom = filter_var($request->overwrite_custom, FILTER_VALIDATE_BOOLEAN);

        $users = App\Models\User::where('role', $role)->get();
        $updatedCount = 0;
        foreach($users as $u) {
            $isCustom = !empty($u->permissions);
            if ($isCustom && !$overwriteCustom) {
                continue; // Skip if they have custom permissions and we are keeping them
            }
            $u->permissions = $permissions;
            $u->save();
            $updatedCount++;
        }
        
        return response()->json(['success' => true, 'updated_count' => $updatedCount]);
    });"""

new_update = r"""    Route::post('/api/permissions/bulk-update', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role;
        $permissions = $request->permissions;
        $overwriteCustom = filter_var($request->overwrite_custom, FILTER_VALIDATE_BOOLEAN);

        $users = App\Models\User::where('role', $role)->get();
        $updatedCount = 0;
        
        $dummyUser = new App\Models\User(['role' => $role]);
        $defaultPerms = $dummyUser->getDefaultPermissions();
        
        foreach($users as $u) {
            $isCustom = false;
            if (!empty($u->permissions)) {
                $savedPerms = $u->permissions;
                foreach ($defaultPerms as $key => $defaultVal) {
                    $savedVal = array_key_exists($key, $savedPerms) ? $savedPerms[$key] : $defaultVal;
                    if ((bool)$savedVal !== (bool)$defaultVal) {
                        $isCustom = true;
                        break;
                    }
                }
            }
            
            if ($isCustom && !$overwriteCustom) {
                continue; // Skip if they have custom permissions and we are keeping them
            }
            $u->permissions = $permissions;
            $u->save();
            $updatedCount++;
        }
        
        return response()->json(['success' => true, 'updated_count' => $updatedCount]);
    });"""

content = content.replace(old_check, new_check)
content = content.replace(old_update, new_update)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated routes/web.php with strict divergence logic.")
