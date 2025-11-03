<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
   protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
    ];
     public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function costs()
    {
        return $this->hasMany(ServiceCost::class);
    }
    public function leadServiceCosts()
    {
        return $this->hasMany(LeadServiceCost::class, 'service_id');
    }
    
}
