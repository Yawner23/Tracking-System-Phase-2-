<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillPhoto extends Model
{
    protected $fillable = [
        'waybill_record_id',
        'type',
        'file_path',
        'uploaded_by',
    ];

    public function record()
    {
        return $this->belongsTo(WaybillRecord::class, 'waybill_record_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}