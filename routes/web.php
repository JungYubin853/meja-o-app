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
    Route::post('/tables/{id}/coordinates', [TableController::class, 'updateTableCoordinates']); // Added missing coordinate update route
    Route::post('/tables/capacities', [TableController::class, 'updateCapacities']);
    Route::post('/tables/generate', [TableController::class, 'generateTables']);
    Route::post('/tables/custom', [TableController::class, 'storeCustomTable']);
    Route::post('/grid/size', [TableController::class, 'updateGridSize']);
    Route::post('/settings/grid-size', [TableController::class, 'updateGridSize']);
    Route::delete('/tables/{id}', [TableController::class, 'destroy']);
    Route::post('/tables/{id}/clone', [TableController::class, 'cloneTable']);

    // Waitlist Routes (Separated view and actions)
    Route::get('/waitlist', [TableController::class, 'showWaitlist']);
    Route::post('/waitlist', [TableController::class, 'storeWaitlist']);
    Route::post('/waitlist/{id}/seat', [TableController::class, 'seatWaitlistCustomer']);
    Route::post('/waitlist/{id}/cancel', [TableController::class, 'cancelWaitlist']);

    // Tutorial
    Route::get('/tutorial', function () {
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
        $allKeys = array_keys($user->getDefaultPermissions());
        $perms = [];
        foreach ($allKeys as $k) {
            $perms[$k] = $user->hasPermission($k); // resolve via hasPermission
        }
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'outlet' => $user->outlet->name ?? 'ALL OUTLETS',
            'permissions' => $user->permissions ?? [] // raw saved permissions
        ]);
    });

    Route::post('/api/permissions/update/{id}', function (Illuminate\Http\Request $request, $id) {
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
    });
});
