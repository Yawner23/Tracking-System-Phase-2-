<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillItem extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'service_id',
        'shoe_brand',
        'colorway',
        'item_status',
        'price',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}