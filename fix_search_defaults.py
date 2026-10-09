file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

import re

old_return = """        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'outlet' => $user->outlet->name ?? 'ALL OUTLETS',
            'permissions' => $user->permissions ?? [] // raw saved permissions
        ]);"""

new_return = """        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'outlet' => $user->outlet->name ?? 'ALL OUTLETS',
            'permissions' => $user->permissions ?? [], // raw saved permissions
            'default_permissions' => $user->getDefaultPermissions() // The dynamic base template defaults
        ]);"""

content = content.replace(old_return, new_return)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated /api/permissions/search to return dynamic default_permissions.")
