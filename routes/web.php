<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DatabaseController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// Protected Routes

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
    }
    return back();
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/account-management', [AuthController::class, 'showAccountManagement'])->name('account-management');
    Route::post('/users', [AuthController::class, 'createUser']);
    Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
    Route::get('/', [TableController::class, 'index']);

    // Table Management & Custom 2D Grid Customization
    Route::get('/api/tables/status', [TableController::class, 'getLiveStatus']);
    Route::post('/tables/{id}/seat', [TableController::class, 'seatCustomer']);
    Route::post('/tables/{id}/finish', [TableController::class, 'finishMeal']);
    Route::post('/tables/{id}/cancel', [TableController::class, 'cancelSession']);
    Route::post('/tables/{id}/update-pax', [TableController::class, 'updatePax']);
    Route::post('/tables/{id}/coordinates', [TableController::class, 'updateTableCoordinates']); // Added missing coordinate update route
    Route::post('/tables/capacities', [TableController::class, 'updateCapacities']);
    Route::post('/tables/generate', [TableController::class, 'generateTables']);
    Route::post('/tables/custom', [TableController::class, 'storeCustomTable']);
    Route::post('/grid/size', [TableController::class, 'updateGridSize']);
    Route::post('/settings/grid-size', [TableController::class, 'updateGridSize']);
    Route::delete('/tables/{id}', [TableController::class, 'destroy']);
    Route::post('/tables/{id}/clone', [TableController::class, 'cloneTable']);

    // Multi-Floor Management Routes
    Route::post('/floors', [TableController::class, 'storeFloor']);
    Route::post('/floors/{id}', [TableController::class, 'updateFloor']);
    Route::delete('/floors/{id}', [TableController::class, 'destroyFloor']);

    // Waitlist Routes (Separated view and actions)
    Route::get('/waitlist', [TableController::class, 'showWaitlist']);
    Route::post('/waitlist', [TableController::class, 'storeWaitlist']);
    Route::post('/waitlist/{id}/seat', [TableController::class, 'seatWaitlistCustomer']);
    Route::post('/waitlist/{id}/cancel', [TableController::class, 'cancelWaitlist']);

    // Tutorial
    Route::get('/tutorial', function () {
        if (Auth::check() && !Auth::user()->hasPermission('nav_tutorial')) abort(403);
        return view('tutorial');
    });

    // Calendar
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index']);
    Route::get('/api/calendar/holidays', [\App\Http\Controllers\CalendarController::class, 'getHolidays']);

    // Reports & Analytics
    Route::get('/database', [ReportController::class, 'index']);
    Route::get('/database/export', [ReportController::class, 'export'])->middleware('auth');

    // Super Admin / Role Permissions Routes
    Route::get('/role-permission', function () {
        $currentUser = Auth::user();
        if (!$currentUser->hasPermission('nav_role_permission')) abort(403);
        
        if ($currentUser->isSuperAdmin()) {
            $allUsers = App\Models\User::with('outlet')->get();
        } else {
            $allUsers = App\Models\User::where('outlet_id', $currentUser->outlet_id)->with('outlet')->get();
        }
        
        return view('permission', compact('allUsers'));
    });

    Route::get('/api/permissions/search', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        
        $term = $request->email; // Term passed from frontend
        $user = App\Models\User::where('email', $term)
            ->orWhere('name', 'like', "%{$term}%")
            ->with('outlet')
            ->first();
        if (!$user) return response()->json(['error' => 'User not found'], 404);
        
        // Return user with their current actual permissions resolved with defaults
        $defaultPerms = $user->getDefaultPermissions();
        $savedPerms = is_string($user->permissions) ? json_decode($user->permissions, true) : ((array)$user->permissions ?: []);
        
        $resolved = [];
        foreach ($defaultPerms as $k => $defVal) {
            $resolved[$k] = array_key_exists($k, $savedPerms) ? (bool)$savedPerms[$k] : (bool)$defVal;
        }
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'outlet' => $user->outlet->name ?? 'ALL OUTLETS',
            'permissions' => $resolved,
            'default_permissions' => $defaultPerms
        ]);
    });

    Route::post('/api/permissions/update/{id}', function (Illuminate\Http\Request $request, $id) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $user = App\Models\User::findOrFail($id);
        
        $perms = $request->permissions;
        if (is_array($perms)) {
            foreach ($perms as $k => $v) {
                $perms[$k] = filter_var($v, FILTER_VALIDATE_BOOLEAN);
            }
        }
        $user->permissions = $perms;
        $user->save();
        return response()->json(['success' => true]);
    });

    Route::get('/api/permissions/bulk-check', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role; // 'admin' or 'staff'
        $users = App\Models\User::where('role', $role)->get();
        
        $dummyUser = new App\Models\User(['role' => $role]);
        $defaultPerms = $dummyUser->getDefaultPermissions();
        
        $divergentUsers = [];
        foreach($users as $u) {
            $savedPerms = is_string($u->permissions) ? json_decode($u->permissions, true) : (array)$u->permissions;
            $differs = false;
            foreach ($defaultPerms as $key => $defaultVal) {
                $savedVal = array_key_exists($key, (array)$savedPerms) ? $savedPerms[$key] : false;
                if ((bool)$savedVal !== (bool)$defaultVal) {
                    $differs = true;
                    break;
                }
            }
            if ($differs) {
                $divergentUsers[] = ['id' => $u->id, 'name' => $u->name, 'email' => $u->email];
            }
        }
        
        return response()->json([
            'divergent_users' => $divergentUsers,
            'default_permissions' => $defaultPerms,
            'total_users' => count($users),
        ]);
    });

    Route::post('/api/permissions/bulk-update', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role;
        $permissions = $request->permissions;
        if (is_array($permissions)) {
            foreach ($permissions as $k => $v) {
                $permissions[$k] = filter_var($v, FILTER_VALIDATE_BOOLEAN);
            }
        }

        $users = App\Models\User::where('role', $role)->get();
        $updatedCount = 0;
        
        foreach($users as $u) {
            $u->permissions = $permissions;
            $u->save();
            $updatedCount++;
        }
        
        // Bulk Overwrite NEVER touches Base Default Templates!
        return response()->json(['success' => true, 'updated_count' => $updatedCount]);
    });

    Route::post('/api/permissions/save-default-template', function (Illuminate\Http\Request $request) {
        if (!Auth::user()->hasPermission('nav_role_permission')) abort(403);
        $role = $request->role;
        $permissions = $request->permissions;
        if (is_array($permissions)) {
            foreach ($permissions as $k => $v) {
                $permissions[$k] = filter_var($v, FILTER_VALIDATE_BOOLEAN);
            }
        }
        
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

        // Base Default Template updates NEVER touch existing accounts!
        // They only set the baseline for newly created accounts.
        return response()->json(['success' => true]);
    });
});
