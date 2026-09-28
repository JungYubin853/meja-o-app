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

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'outlet_id'=> $validated['outlet_id'],
            'role'     => 'staff', // Fixed as staff on registration
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/');
    }

    public function showProfile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
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
            $allUsers = User::with('outlet')->get();
        } else {
            $allUsers = User::where('outlet_id', $user->outlet_id)->with('outlet')->get();
        }
        
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff,super_admin',
            'outlet_id' => 'nullable|exists:outlets,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'outlet_id' => $validated['role'] === 'super_admin' ? null : $validated['outlet_id'],
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

        User::findOrFail($id)->delete();

        return back()->with('success', 'User account deleted successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}