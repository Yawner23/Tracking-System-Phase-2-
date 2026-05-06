<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillRecord extends Model
{
    protected $fillable = [
        'branch_id',
        'created_by',
        'waybill_number',
        'reference_number',
        'client_name',
        'client_contact_number',
        'pos_receipt_number',
        'pos_tracking_number',
        'additional_information',
        'current_status',
        'mode_of_payment',
        'payment_status',
        'total_amount',
        'total_amount_paid',
        'balance',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function waybill()
    {
        return $this->hasOne(Waybill::class);
    }

    public function returnWaybill()
    {
        return $this->hasOne(ReturnWaybill::class);
    }

    public function backjobWaybill()
    {
        return $this->hasOne(BackjobWaybill::class);
    }

    public function returnedBackjobWaybill()
    {
        return $this->hasOne(ReturnedBackjobWaybill::class);
    }

    public function items()
    {
        return $this->hasMany(WaybillItem::class);
    }

    public function photos()
    {
        return $this->hasMany(WaybillPhoto::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(WaybillStatusHistory::class);
    }

    public function logistics()
    {
        return $this->hasOne(WaybillLogistic::class);
    }

    public function transactions()
    {
        return $this->hasMany(WaybillTransaction::class);
    }
}