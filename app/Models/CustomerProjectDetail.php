<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerProjectDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'project_name',
        'project_id',
        'domain_name',
        'domain_provider',
        'hosting_provider',
        'ssl_provider',
        'purchase_date',
        'expiry_date',
        'cms_or_technology',
        'server_details',
        'credentials',
        'notes',

        // 🟦 Domain details
        'domain_type',
        'domain_service_provider',
        'domain_purchase_date',
        'domain_expiry_date',
        'domain_subscription_duration',
        'domain_renewal_month',
        'domain_url',
        'domain_username',
        'domain_password',
        'domain_not_included_in_amc',

        // 🟩 Hosting details
        'hosting_type',
        'hosting_service_provider',
        'hosting_purchase_date',
         'hosting_expiry_date',
        'hosting_subscription_duration',
        'hosting_renewal_month',
        'hosting_url',
        'hosting_username',
        'hosting_password',
        //AMC details

        'amc_description',
        'amc_month',
        'amc_amount',
        'amc_remarks',

    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    /*public function detail()
{
    return $this->hasOne(CustomerProjectDetail::class, 'project_id');
}*/
public function project()
{
    return $this->belongsTo(Project::class);
}

}
