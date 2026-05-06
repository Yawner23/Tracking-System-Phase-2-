<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillPayment extends Model
{
    protected $fillable = [
        'waybill_transaction_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference_number',
        'payment_proof_path',
        'received_by',
        'remarks',
    ];

    public function transaction()
    {
        return $this->belongsTo(WaybillTransaction::class, 'waybill_transaction_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}