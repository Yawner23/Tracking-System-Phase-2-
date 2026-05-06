<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_page_permission')
            ->withPivot('permission_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_page_permission')
            ->withPivot('role_id');
    }
}