<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackjobWaybill extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'return_waybill_id',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function returnWaybill()
    {
        return $this->belongsTo(ReturnWaybill::class);
    }

    public function returnedBackjobWaybills()
    {
        return $this->hasMany(ReturnedBackjobWaybill::class);
    }
}