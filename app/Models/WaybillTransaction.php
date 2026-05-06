<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillTransaction extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'amount',
        'adjustment',
        'refund_amount',
        'payment_status',
        'created_by',
        'remarks',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(WaybillPayment::class);
    }
}