<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCost extends Model
{
     protected $fillable = [
        'service_id',
        'name',
        'amount',
        'billing_type',
    ];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
