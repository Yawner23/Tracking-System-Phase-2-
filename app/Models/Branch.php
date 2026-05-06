<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'address',
        'contact_number',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function waybillRecords()
    {
        return $this->hasMany(WaybillRecord::class);
    }
}