<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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

    public function redirectByRole()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return redirect()->route('dashboard', [
            'account' => $this->accountPrefix($user),
        ]);
    }

    public function accountDashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $roleNames = $user->roles
            ->pluck('name')
            ->map(fn ($role) => strtolower(str_replace(' ', '_', $role)))
            ->toArray();

        if (in_array('super_admin', $roleNames)) {
            return $this->superAdminDashboard();
        }

        if (in_array('admin', $roleNames)) {
            return $this->adminDashboard();
        }

        if (in_array('logistics', $roleNames)) {
            return $this->logisticsDashboard();
        }

        if (in_array('branch', $roleNames)) {
            return $this->branchDashboard();
        }

        if (in_array('operator', $roleNames)) {
            return $this->operatorDashboard();
        }

        return $this->userDashboard();
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