<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'contact_number',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function createdWaybillRecords()
    {
        return $this->hasMany(WaybillRecord::class, 'created_by');
    }

    public function uploadedPhotos()
    {
        return $this->hasMany(WaybillPhoto::class, 'uploaded_by');
    }

    public function statusHistories()
    {
        return $this->hasMany(WaybillStatusHistory::class);
    }

    public function hasRole(string $roleName): bool
{
    return $this->roles()
        ->where('name', $roleName)
        ->exists();
}

    public function hasPagePermission(string $pageDescription, string $permissionName = 'can_view'): bool
    {

        $roleIds = $this->roles()->pluck('roles.id');

        return DB::table('role_page_permission')
            ->join('pages', 'pages.id', '=', 'role_page_permission.page_id')
            ->join('permissions', 'permissions.id', '=', 'role_page_permission.permission_id')
            ->whereIn('role_page_permission.role_id', $roleIds)
            ->where('pages.description', $pageDescription)
            ->where('permissions.name', $permissionName)
            ->exists();
    }

    public function routePrefix(): string
    {
        if ($this->hasRole('Super Admin')) {
            return 'super-admin';
        }

        if ($this->hasRole('Admin')) {
            return 'admin';
        }

        if ($this->hasRole('Logistics')) {
            return 'logistics';
        }

        if ($this->hasRole('Branch')) {
            return 'branch';
        }

        if ($this->hasRole('Operator')) {
            return 'operator';
        }

        return 'user';
    }
}