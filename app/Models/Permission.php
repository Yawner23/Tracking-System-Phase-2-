<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'group',
    ];

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'role_page_permission')
            ->withPivot('role_id');
    }
}