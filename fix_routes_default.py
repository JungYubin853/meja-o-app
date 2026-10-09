file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

# We insert the new route right after bulk-update
pattern = r"        return response()->json\(\['success' => true, 'updated_count' => \$updatedCount\]\);\n    }\);"

new_route = r"""        return response()->json(['success' => true, 'updated_count' => $updatedCount]);
    });

    Route::post('/api/permissions/save-default-template', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role;
        $permissions = $request->permissions;
        
        $superAdmin = App\Models\User::where('role', 'super_admin')->first();
        if ($superAdmin) {
            $perms = is_string($superAdmin->permissions) ? json_decode($superAdmin->permissions, true) : ((array)$superAdmin->permissions ?: []);
            if ($role === 'staff') {
                $perms['_staff_defaults'] = $permissions;
            } else {
                $perms['_admin_defaults'] = $permissions;
            }
            $superAdmin->permissions = $perms;
            $superAdmin->save();
        }
        
        return response()->json(['success' => true]);
    });"""

content = re.sub(pattern, new_route, content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Added save-default-template route.")
