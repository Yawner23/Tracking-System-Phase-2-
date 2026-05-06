<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnedBackjobWaybill extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'backjob_waybill_id',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function backjobWaybill()
    {
        return $this->belongsTo(BackjobWaybill::class);
    }
}