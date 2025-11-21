<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Followup extends Model
{
    protected $casts = [
    'next_followup_date' => 'datetime',
];
    use HasFactory;
     protected $fillable = [
        'lead_id',
        'user_id',
        'notes',
        'next_followup_date',
    ];
     public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
     public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
