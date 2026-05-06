<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'price',
        'gross_sales',
        'net_sales'
    ];

    public function waybillItems()
    {
        return $this->hasMany(WaybillItem::class);
    }
}