<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillStatusHistory extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'user_id',
        'status',
        'remarks',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}