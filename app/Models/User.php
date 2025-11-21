<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\EmployeeDetail;


/**
 * @method bool hasRole(string|array|\Spatie\Permission\Models\Role|\Illuminate\Support\Collection $roles, string|null $guard = null)
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Models\Role|\Illuminate\Support\Collection $roles, string|null $guard = null)
 * @method bool hasPermissionTo(string|\Spatie\Permission\Models\Permission $permission, string|null $guardName = null)
 */

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // ✅ This is the key: define the relationship
    public function employeeDetail()
    {
        return $this->hasOne(EmployeeDetail::class);
    }
}
