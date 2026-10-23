<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // remember=true keeps users logged in persistently (lifetime extended to 1 year)
        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Self-registered users awaiting approval are led directly to the Waiting Room
            if ($user->status === 'pending' || $user->status === 'rejected') {
                return redirect()->route('waiting-room');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showWaitingRoom()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // If the account has already been approved, take them immediately to the dashboard
        if ($user->status !== 'pending' && $user->status !== 'rejected') {
            return redirect()->intended('/')->with('status', 'Welcome back! Your account has been approved.');
        }

        $outlet = $user->outlet;

        return view('auth.waiting-room', compact('user', 'outlet'));
    }

    public function checkWaitingRoomStatus()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['authenticated' => false]);
        }

        return response()->json([
            'authenticated' => true,
            'status'        => $user->status,
            'is_approved'   => $user->status === 'approved' || $user->isSuperAdmin(),
            'is_rejected'   => $user->status === 'rejected',
        ]);
    }

    public function showRegisterForm()
    {
        $outlets = \App\Models\Outlet::all();
        return view('auth.register', compact('outlets'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'outlet_id'=> 'required|exists:outlets,id',
        ]);

        $dummyStaff = new User(['role' => 'staff']);
        $defaultPermissions = $dummyStaff->getDefaultPermissions();

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'viewable_password' => \Illuminate\Support\Facades\Crypt::encryptString($validated['password']),
            'outlet_id'=> $validated['outlet_id'],
            'role'     => 'staff', // Fixed as staff on registration
            'status'   => 'pending', // Requires approval
            'is_self_registered' => true,
            'permissions' => $defaultPermissions,
        ]);

        return redirect()->route('login')->with('status', 'Registration successful! Please sign in with your credentials to access your account waiting room.');
    }

    public function requestForgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'master_recovery_key' => 'nullable|string',
            'new_password' => 'nullable|string|min:6',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'forgot_error' => 'No registered account found matching that email address.'
            ])->withInput();
        }

        // Super Admin instant recovery via Master Security Key
        if ($user->isSuperAdmin()) {
            $masterKey = env('SUPERADMIN_MASTER_KEY', 'MEJAO2026!');
            if (!empty($validated['master_recovery_key'])) {
                if ($validated['master_recovery_key'] === $masterKey) {
                    if (!empty($validated['new_password'])) {
                        $user->password = Hash::make($validated['new_password']);
                        $user->viewable_password = \Illuminate\Support\Facades\Crypt::encryptString($validated['new_password']);
                        $user->save();
                        return back()->with('status', 'Super Admin password successfully reset! Please sign in with your new password.');
                    }
                    return back()->withErrors(['forgot_error' => 'Please provide a new password of at least 6 characters.']);
                } else {
                    return back()->withErrors(['forgot_error' => 'Invalid Master Security Key.']);
                }
            }

            // Create / update request for super admin
            \App\Models\PasswordResetRequest::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'outlet_id' => null,
                'role' => 'super_admin',
                'status' => 'pending',
                'admin_notes' => 'Super Admin forgot password inquiry. You can also reset instantly using Master Security Key (MEJAO2026!).',
            ]);

            return back()->with('status', 'Super Admin recovery request registered. Alternatively, provide the Master Security Key (MEJAO2026!) in the recovery modal to reset immediately.');
        }

        // Create / update request for staff or admin
        $existing = \App\Models\PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            $existing->touch();
        } else {
            \App\Models\PasswordResetRequest::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'outlet_id' => $user->outlet_id,
                'role' => $user->role,
                'status' => 'pending',
            ]);
        }

        if ($user->role === 'admin') {
            $msg = 'Password reset request submitted! The Super Admin has been notified.';
        } else {
            $outletName = $user->outlet ? $user->outlet->name : 'your store';
            $msg = "Password reset request submitted! Your outlet Admin ({$outletName}) and Super Admin have been notified.";
        }

        return back()->with('status', $msg);
    }

    public function showProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && !$user->hasPermission('nav_profile')) {
            abort(403);
        }
        $outlets = \App\Models\Outlet::all();

        $resetRequests = collect();
        $pendingCount = 0;

        if ($user->hasPermission('prof_password_requests')) {
            $filterStatus = $request->query('req_status', 'all');
            $direction = $request->query('direction', 'desc') === 'asc' ? 'asc' : 'desc';

            if ($user->isSuperAdmin()) {
                $baseQuery = \App\Models\PasswordResetRequest::with(['user', 'outlet']);
            } else {
                $baseQuery = \App\Models\PasswordResetRequest::with(['user', 'outlet'])
                    ->where('outlet_id', $user->outlet_id)
                    ->where('role', 'staff');
            }

            if ($user->isSuperAdmin() && $request->filled('outlet_id')) {
                $baseQuery->where('outlet_id', $request->query('outlet_id'));
            }

            if ($request->filled('role')) {
                $baseQuery->where('role', $request->query('role'));
            }

            $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();

            if ($request->filled('search')) {
                $searchTerm = trim($request->query('search'));
                $baseQuery->where(function ($q) use ($searchTerm) {
                    $q->where('email', 'like', "%{$searchTerm}%")
                      ->orWhereHas('user', function ($uq) use ($searchTerm) {
                          $uq->where('name', 'like', "%{$searchTerm}%")
                             ->orWhere('email', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('outlet', function ($oq) use ($searchTerm) {
                          $oq->where('name', 'like', "%{$searchTerm}%");
                      });
                });
            }

            if ($filterStatus === 'pending') {
                $baseQuery->where('status', 'pending');
            } elseif ($filterStatus === 'resolved') {
                $baseQuery->where('status', 'resolved');
            }
            // If 'all', no status filter is applied: all requests (pending, resolved) are visible!

            $resetRequests = $baseQuery->orderBy('created_at', $direction)->get();
        }

        return view('profile', compact('user', 'outlets', 'resetRequests', 'pendingCount', 'direction'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success', 'Profile details updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match our records.'])->withInput();
        }

        $user->password = Hash::make($validated['new_password']);
        $user->viewable_password = \Illuminate\Support\Facades\Crypt::encryptString($validated['new_password']);
        $user->save();

        return back()->with('success_password', 'Your password has been changed successfully.');
    }

    public function adminChangeUserPassword(Request $request, $id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasPermission('prof_password_requests')) {
            abort(403, 'Unauthorized');
        }

        $resetReq = \App\Models\PasswordResetRequest::findOrFail($id);
        $targetUser = $resetReq->user;

        // Admin/Staff can only change password for staff in their own outlet
        if (!$currentUser->isSuperAdmin()) {
            if ($targetUser->role !== 'staff' || $targetUser->outlet_id !== $currentUser->outlet_id) {
                abort(403, 'You can only manage passwords for staff within your assigned outlet.');
            }
        }

        $validated = $request->validate([
            'new_password' => 'required|string|min:6',
            'admin_notes'  => 'nullable|string|max:500',
        ]);

        $newPass = $validated['new_password'];
        $targetUser->password = Hash::make($newPass);
        $targetUser->viewable_password = \Illuminate\Support\Facades\Crypt::encryptString($newPass);
        $targetUser->save();

        $resetReq->status = 'resolved';
        $resetReq->resolved_by = $currentUser->name;
        $resetReq->resolved_at = now();
        $resetReq->admin_notes = $validated['admin_notes'] ?? ('Password changed by ' . $currentUser->name);
        $resetReq->save();

        return back()->with('success_req', "Password for {$targetUser->name} ({$targetUser->email}) was successfully changed to: '{$newPass}'. Please inform the user.");
    }

    public function resolveResetRequest(Request $request, $id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasPermission('prof_password_requests')) {
            abort(403, 'Unauthorized');
        }

        $resetReq = \App\Models\PasswordResetRequest::findOrFail($id);

        if (!$currentUser->isSuperAdmin()) {
            if ($resetReq->role !== 'staff' || $resetReq->outlet_id !== $currentUser->outlet_id) {
                abort(403, 'Unauthorized');
            }
        }

        $resetReq->status = 'resolved';
        $resetReq->resolved_by = $currentUser->name;
        $resetReq->resolved_at = now();
        if ($request->filled('admin_notes')) {
            $resetReq->admin_notes = $request->admin_notes;
        }
        $resetReq->save();

        return back()->with('success_req', 'Request marked as resolved.');
    }

    public function deleteResetRequest(Request $request, $id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasPermission('prof_password_requests')) {
            abort(403, 'Unauthorized');
        }

        $resetReq = \App\Models\PasswordResetRequest::findOrFail($id);

        if (!$currentUser->isSuperAdmin()) {
            if ($resetReq->role !== 'staff' || $resetReq->outlet_id !== $currentUser->outlet_id) {
                abort(403, 'Unauthorized');
            }
        }

        $resetReq->delete();

        return back()->with('success_req', 'Recovery request deleted successfully.');
    }

    public function showAccountManagement(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasPermission('nav_account_mgmt') && !$user->hasPermission('acc_user_list') && !$user->hasPermission('nav_approval')) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            $query = User::with('outlet');
        } else {
            $query = User::where('outlet_id', $user->outlet_id)->with('outlet');
        }

        // Status filter: all, approved, rejected, pending
        $status = $request->get('status', 'all');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Source filter: 'self' or 'admin'
        if ($request->filled('source')) {
            if ($request->source === 'self') {
                $query->where('is_self_registered', true);
            } elseif ($request->source === 'admin') {
                $query->where('is_self_registered', false);
            }
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($user->isSuperAdmin() && $request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Pending count for the badge/tabs
        $pendingCountQuery = User::where('role', '!=', 'super_admin')->where('status', 'pending');
        if (!$user->isSuperAdmin()) {
            $pendingCountQuery->where('outlet_id', $user->outlet_id);
        }
        $pendingCount = $pendingCountQuery->count();

        $direction = $request->query('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $allUsers = $query->orderBy('created_at', $direction)->get();
        
        $outlets = \App\Models\Outlet::all();

        return view('account-management', compact('user', 'allUsers', 'outlets', 'status', 'pendingCount', 'direction'));
    }

    public function createUser(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasPermission('acc_create_account')) {
            abort(403);
        }

        $allowedRoles = $user->isSuperAdmin() ? 'admin,staff,super_admin' : 'admin,staff';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:' . $allowedRoles,
            'outlet_id' => $user->isSuperAdmin() ? 'nullable|exists:outlets,id' : 'required|exists:outlets,id',
        ]);

        $dummyUser = new User(['role' => $validated['role']]);
        $defaultPermissions = $dummyUser->getDefaultPermissions();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'viewable_password' => \Illuminate\Support\Facades\Crypt::encryptString($validated['password']),
            'role' => $validated['role'],
            'outlet_id' => $user->isSuperAdmin() ? ($validated['role'] === 'super_admin' ? null : ($validated['outlet_id'] ?? null)) : $user->outlet_id,
            'permissions' => $validated['role'] === 'super_admin' ? null : $defaultPermissions,
            'status' => 'approved',
            'is_self_registered' => false,
        ]);

        return back()->with('success', 'User account created successfully.');
    }

    public function deleteUser($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasPermission('acc_user_list')) {
            abort(403, 'Unauthorized');
        }

        if (Auth::id() == $id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $targetUser = User::findOrFail($id);
        if (!$user->isSuperAdmin() && $targetUser->isSuperAdmin()) {
            abort(403, 'You are not allowed to delete a Super Admin account.');
        }

        $targetUser->delete();

        return back()->with('success', 'User account deleted successfully.');
    }

    public function showApproval(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasPermission('nav_approval')) {
            abort(403, 'Unauthorized.');
        }

        // Never show Super Admin in approval!
        $query = User::where('role', '!=', 'super_admin');

        if (!$user->isSuperAdmin()) {
            $query->where('outlet_id', $user->outlet_id);
        }

        $query->with('outlet');

        // Status filter (default to 'pending')
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Source filter: 'self' (Self-Registered), 'admin' (Admin-Created), or 'all'
        if ($request->filled('source')) {
            if ($request->source === 'self') {
                $query->where('is_self_registered', true);
            } elseif ($request->source === 'admin') {
                $query->where('is_self_registered', false);
            }
        }

        // Outlet filter for super admin
        if ($user->isSuperAdmin() && $request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();

        // Pending count for badge/tabs
        $pendingCountQuery = User::where('role', '!=', 'super_admin')
            ->where('status', 'pending');
        if (!$user->isSuperAdmin()) {
            $pendingCountQuery->where('outlet_id', $user->outlet_id);
        }
        $pendingCount = $pendingCountQuery->count();

        $outlets = \App\Models\Outlet::all();

        return view('approval', compact('user', 'users', 'outlets', 'status', 'pendingCount', 'perPage'));
    }

    public function approveUser($id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->hasPermission('nav_account_mgmt') && !$currentUser->hasPermission('acc_user_list') && !$currentUser->hasPermission('nav_approval')) {
            abort(403, 'Unauthorized.');
        }

        $targetUser = User::findOrFail($id);

        if ($targetUser->isSuperAdmin()) {
            abort(403, 'Super Admin accounts cannot be modified.');
        }

        if (!$currentUser->isSuperAdmin() && $targetUser->outlet_id !== $currentUser->outlet_id) {
            abort(403, 'You can only approve users from your assigned outlet.');
        }

        $targetUser->status = 'approved';
        $targetUser->save();

        return back()->with('success', "Account for '{$targetUser->name}' has been approved successfully.");
    }

    public function rejectUser($id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->hasPermission('nav_account_mgmt') && !$currentUser->hasPermission('acc_user_list') && !$currentUser->hasPermission('nav_approval')) {
            abort(403, 'Unauthorized.');
        }

        $targetUser = User::findOrFail($id);

        if ($targetUser->isSuperAdmin()) {
            abort(403, 'Super Admin accounts cannot be modified.');
        }

        if (!$currentUser->isSuperAdmin() && $targetUser->outlet_id !== $currentUser->outlet_id) {
            abort(403, 'You can only reject users from your assigned outlet.');
        }

        $targetUser->status = 'rejected';
        $targetUser->save();

        return back()->with('success', "Account for '{$targetUser->name}' has been rejected.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}