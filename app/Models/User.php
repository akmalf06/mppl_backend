<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    const USER_EMPLOYEE = 'EMPLOYEE';
    const USER_ADMIN = 'ADMIN';

    CONST USER_TYPES = [
        self::USER_EMPLOYEE,
        self::USER_ADMIN
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'profile_picture',
        'is_supervisor',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * Get the branch that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
