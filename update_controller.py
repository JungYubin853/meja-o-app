import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\AuthController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_logic = """        if ($user->isSuperAdmin()) {
            $allUsers = User::with('outlet')->get();
        } else {
            $allUsers = User::where('outlet_id', $user->outlet_id)->with('outlet')->get();
        }"""

new_logic = """        if ($user->isSuperAdmin()) {
            $query = User::with('outlet');
        } else {
            $query = User::where('outlet_id', $user->outlet_id)->with('outlet');
        }

        if (request()->filled('role')) {
            $query->where('role', request('role'));
        }
        
        if (request()->filled('outlet_id')) {
            $query->where('outlet_id', request('outlet_id'));
        }

        $allUsers = $query->get();"""

if old_logic in content:
    content = content.replace(old_logic, new_logic)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("AuthController updated with filtering logic.")
else:
    print("Could not find the target code in AuthController.")
