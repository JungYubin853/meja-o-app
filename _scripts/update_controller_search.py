import re
file_path = r"C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\AuthController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_logic = """        if (request()->filled('outlet_id')) {
            $query->where('outlet_id', request('outlet_id'));
        }

        $allUsers = $query->get();"""
new_logic = """        if (request()->filled('outlet_id')) {
            $query->where('outlet_id', request('outlet_id'));
        }
        
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allUsers = $query->get();"""

if old_logic in content:
    content = content.replace(old_logic, new_logic)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Added search filter to AuthController.")
else:
    print("Could not find AuthController old logic.")
