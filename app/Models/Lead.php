<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
     use HasFactory;
     protected $fillable = [
         'lead_code',
        'company_name',
        'address',
        'contact_person',
        'designation',
        'phone',
        'email',
        'status',
        'source',
        'source_sub',
        'created_by',
         'address_line1',
	'address_line2', 
	'country', 
	'state', 
	'district', 
	'city',
	'pincode',
    'customer_id',
    'status',
    'requested_via_customer', // ✅ must be included
    'created_by',
    ];
    public function services()
    {
          return $this->belongsToMany(Service::class, 'lead_services')->with('costs');

    }
    public function followups()
    {
        return $this->hasMany(Followup::class);
        
    }
public function serviceCosts()
{
    return $this->hasMany(LeadServiceCost::class);
    
}
public function leadServiceCosts()
{
    return $this->hasMany(\App\Models\LeadServiceCost::class, 'lead_id');
}


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
public static function generateLeadCode()
{
    $prefix = 'LD';
    $monthYear = now()->format('Ym'); // e.g., 202510

    // Get the last lead code
    $lastLead = self::latest('id')->first();
    
    if ($lastLead && preg_match('/-(\d+)$/', $lastLead->lead_code, $matches)) {
        $count = (int)$matches[1] + 1;
    } else {
        $count = 1;
    }

    // Build code like LD-202510-001, LD-202510-002, etc.
    return sprintf('%s-%s-%03d', $prefix, $monthYear, $count);
}
public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id');
}


}
