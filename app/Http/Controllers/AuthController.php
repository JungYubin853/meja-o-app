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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your account registration is currently pending approval by an administrator.',
                ])->onlyInput('email');
            }
            if ($user->status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your account registration has been rejected. Please contact an administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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
            'password' => 'required|string|min:6|confirmed',
            'outlet_id'=> 'required|exists:outlets,id',
        ]);

        $dummyStaff = new User(['role' => 'staff']);
        $defaultPermissions = $dummyStaff->getDefaultPermissions();

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'outlet_id'=> $validated['outlet_id'],
            'role'     => 'staff', // Fixed as staff on registration
            'status'   => 'pending', // Requires approval
            'is_self_registered' => true,
            'permissions' => $defaultPermissions,
        ]);

        return redirect()->route('login')->with('status', 'Registration successful! Your account is pending approval by an administrator.');
    }

    public function showProfile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && !$user->hasPermission('nav_profile')) {
            abort(403);
        }
        $outlets = \App\Models\Outlet::all();
        return view('profile', compact('user', 'outlets'));
    }

    public function showAccountManagement()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasPermission('nav_account_mgmt')) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
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
        
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allUsers = $query->get();
        
        $outlets = \App\Models\Outlet::all();

        return view('account-management', compact('user', 'allUsers', 'outlets'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'outlet_id'=> 'nullable|exists:outlets,id',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
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
            'role' => $validated['role'],
            'outlet_id' => $validated['role'] === 'super_admin' ? null : $validated['outlet_id'],
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
        if (!$currentUser->hasPermission('nav_approval')) {
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
        if (!$currentUser->hasPermission('nav_approval')) {
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