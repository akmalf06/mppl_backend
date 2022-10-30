<?php

namespace App\Models;

use App\Models\Traits\DateSerializer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;
    use DateSerializer;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "route",
    ];

    /**
     * The attributes that should be hidden.
     *
     * @var array
     */
    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    /**
     * The permissions that belong to the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'role_id', 'permission_id');
    }

    /**
     * Get all of the permissionRole for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissionRole(): HasMany
    {
        return $this->hasMany(PermissionRole::class, 'role');
    }
}
