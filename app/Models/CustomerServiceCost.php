<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerServiceCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
         'name',              // ✅ instead of cost_name
        'quoted_amount',
        'approved_amount',
        'billing_type',
    ];
     public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
public function service()
{
    return $this->belongsTo(Service::class, 'service_id');
}
}
