<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'remind_at',
        'type',
        'related_id',
    ];
 // If you want Eloquent to treat 'remind_at' as a Carbon date automatically
    protected $dates = [
        'remind_at',
    ];
 public function lead()
    {
        return $this->belongsTo(Lead::class, 'related_id');
    }
     /**
     * Scope to get upcoming reminders
     */
    public function scopeUpcoming($query)
    {
        return $query->where('remind_at', '>=', now())->orderBy('remind_at');
    }
    public function projectDetail()
    {
        return $this->belongsTo(CustomerProjectDetail::class, 'related_id');
    }

}

