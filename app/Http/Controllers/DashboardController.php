<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirectByRole()
    {
        $user = Auth::user();

       $roleNames = $user->roles
        ->pluck('name')
        ->map(fn ($role) => strtolower(str_replace(' ', '_', $role)))
        ->toArray();

        if (in_array('super_admin', $roleNames)) {
            return redirect()->route('super_admin.dashboard');
        }

        if (in_array('admin', $roleNames)) {
            return redirect()->route('admin.dashboard');
        }

        if (in_array('logistics', $roleNames)) {
            return redirect()->route('logistics.dashboard');
        }

        if (in_array('branch', $roleNames)) {
            return redirect()->route('branch.dashboard');
        }

        if (in_array('operator', $roleNames)) {
            return redirect()->route('operator.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    public function superAdminDashboard()
    {
        return view('backend.dashboard.index');
    }

    public function adminDashboard()
    {
        return view('backend.dashboard.index');
    }

    public function logisticsDashboard()
    {
        return view('backend.dashboard.index');
    }

    public function branchDashboard()
    {
        return view('backend.dashboard.index');
    }

    public function operatorDashboard()
    {
        return view('backend.dashboard.index');
    }

    public function userDashboard()
    {
        return view('backend.dashboard.index');
    }
}