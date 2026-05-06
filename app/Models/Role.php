<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'role_page_permission')
            ->withPivot('permission_id');
    }

    public function pagePermissions()
    {
        return $this->belongsToMany(Permission::class, 'role_page_permission')
            ->withPivot('page_id');
    }
}