file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_search = r"""    Route::get('/api/permissions/search', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $user = App\Models\User::where('email', $request->email)->with('outlet')->first();"""

new_search = r"""    Route::get('/api/permissions/search', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        
        $term = $request->email; // Term passed from frontend
        $user = App\Models\User::where('email', $term)
            ->orWhere('name', 'like', "%{$term}%")
            ->with('outlet')
            ->first();"""

content = content.replace(old_search, new_search)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated routes/web.php with flexible search logic.")
