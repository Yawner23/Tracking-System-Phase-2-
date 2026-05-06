<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    protected $fillable = [
        'waybill_record_id',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function returnWaybills()
    {
        return $this->hasMany(ReturnWaybill::class);
    }
}