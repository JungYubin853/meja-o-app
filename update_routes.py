file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

new_routes = """    Route::post('/api/permissions/update/{id}', function (Illuminate\Http\Request $request, $id) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $user = App\Models\User::findOrFail($id);
        $user->permissions = $request->permissions;
        $user->save();
        return response()->json(['success' => true]);
    });

    Route::get('/api/permissions/bulk-check', function (Illuminate\Http\Request $request) {
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
    });

    Route::post('/api/permissions/bulk-update', function (Illuminate\Http\Request $request) {
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

old_route = """    Route::post('/api/permissions/update/{id}', function (Illuminate\Http\Request $request, $id) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $user = App\Models\User::findOrFail($id);
        $user->permissions = $request->permissions;
        $user->save();
        return response()->json(['success' => true]);
    });"""

content = content.replace(old_route, new_routes)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated routes/web.php")
