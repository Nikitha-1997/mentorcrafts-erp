<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
     protected $fillable = [
        'customer_id',
        'lead_id',
        'project_name',
        'service_type',
        'short_description',
        'description',
        'assigned_to',
        'start_date',
        'end_date'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function service()
    {
    return $this->belongsTo(Service::class);
    }
    public function detail()
{
    return $this->hasOne(CustomerProjectDetail::class, 'project_id');
    
}


}
