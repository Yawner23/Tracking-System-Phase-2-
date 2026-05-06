<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnWaybill extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'waybill_id',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function originalWaybill()
    {
        return $this->belongsTo(Waybill::class, 'waybill_id');
    }

    public function backjobWaybills()
    {
        return $this->hasMany(BackjobWaybill::class);
    }
}