<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillLogistic extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'logistics_type',
        'third_party_provider',
        'third_party_waybill_number',
        'logistics_accepted_by',
        'logistics_accepted_at',
        'main_hub_accepted_by',
        'main_hub_accepted_at',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function logisticsAcceptedBy()
    {
        return $this->belongsTo(User::class, 'logistics_accepted_by');
    }

    public function mainHubAcceptedBy()
    {
        return $this->belongsTo(User::class, 'main_hub_accepted_by');
    }
}