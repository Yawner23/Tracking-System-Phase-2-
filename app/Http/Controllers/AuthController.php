<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private function accountPrefix($user): string
    {
        $roleNames = $user->roles
            ->pluck('name')
            ->map(fn ($role) => strtolower(str_replace(' ', '_', $role)))
            ->toArray();

        if (in_array('super_admin', $roleNames)) {
            return 'super-admin';
        }

        if (in_array('admin', $roleNames)) {
            return 'admin';
        }

        if (in_array('logistics', $roleNames)) {
            return 'logistics';
        }

        if (in_array('branch', $roleNames)) {
            return 'branch';
        }

        if (in_array('operator', $roleNames)) {
            return 'operator';
        }

        return 'user';
    }

    public function showLoginForm()
    {
        return view('login.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->route('dashboard', [
            'account' => $this->accountPrefix($user),
        ]);
    }

    public function index()
    {
        return redirect()->route('dashboard', [
            'account' => $this->accountPrefix(Auth::user()),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Logged out successfully.');
    }
}