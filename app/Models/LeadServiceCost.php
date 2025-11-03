<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadServiceCost extends Model
{
     use HasFactory;

    protected $fillable = [
        'lead_id',
        'service_id',
        'name',
        'amount',
        'billing_type',
    ];
     public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
