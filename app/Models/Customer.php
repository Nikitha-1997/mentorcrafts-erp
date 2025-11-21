<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_code',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'lead_id',
        'address_line1',
        'address_line2',
        'country',
        'state',
        'district',
        'city',
        'pincode',
    ];
    /*public function services()
    {
          return $this->belongsToMany(Service::class, 'customer_services')
                    ->withPivot('service_code');
    }*/
public function services()
{
    return $this->belongsToMany(Service::class, 'customer_services')
                ->withPivot('service_code') // add more pivot fields if you have them
                ->withTimestamps();
}

   
public function lead()
{
    return $this->belongsTo(Lead::class);
}
public function serviceCosts()
{
    return $this->hasMany(CustomerServiceCost::class);
}

public static function generateCustomerCode()
{
    // Fetch the last customer
    $lastCustomer = self::orderBy('id', 'desc')->first();

    if ($lastCustomer && is_numeric($lastCustomer->customer_code)) {
        $nextCode = intval($lastCustomer->customer_code) + 1;
    } else {
        $nextCode = 100001; // starting point
    }

    // Ensure 6-digit format
    return str_pad($nextCode, 6, '0', STR_PAD_LEFT);
}
public function leads()
{
    return $this->hasMany(Lead::class, 'customer_id');
}
public function recentLeads()
{
    return $this->leads()->latest()->take(5); // last 5 leads
}
public function projectDetails()
{
    return $this->hasMany(CustomerProjectDetail::class);
}

public function projects()
{
    return $this->hasMany(Project::class);
}


}
