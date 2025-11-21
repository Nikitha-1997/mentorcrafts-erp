<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CustomerProfileStatus extends Model
{
    use HasFactory;
     protected $fillable = [
        'customer_id',
        'service_name',
        'section_name',
        'subpoint_name',
        'planning',
        'documentation',
        'implementation',
        'training',
        'file_path',
        'remarks',
    ];
     public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
