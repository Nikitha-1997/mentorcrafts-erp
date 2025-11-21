<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','department_id','position_id','employee_id',
        'photo','kyc_document','salary','next_increment_date','joining_date','relieving_date'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function department() {
        return $this->belongsTo(Department::class);
    }
    public function position() {
        return $this->belongsTo(Position::class);
    }
}
