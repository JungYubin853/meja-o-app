file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_route = """    // Super Admin / Role Permissions Routes
    Route::get('/role-permission', function () {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasPermission('nav_role_permission')) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch all users for the list view
        $allUsers = App\Models\User::select('id', 'name', 'email', 'role', 'outlet_id')
            ->orderBy('name')
            ->get();

        return view('permission', compact('allUsers'));
    });"""

new_route = """    // Super Admin / Role Permissions Routes
    Route::get('/role-permission', function () {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasPermission('nav_role_permission')) {
            abort(403, 'Unauthorized action.');
        }

        return view('permission');
    });"""

content = content.replace(old_route, new_route)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Cleaned up web.php")
