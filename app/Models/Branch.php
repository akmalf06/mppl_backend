<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address'
    ];

    /**
     * Get all of the employee for the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employee(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }

    /**
     * Get all of the spends for the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spends(): HasMany
    {
        return $this->hasMany(Spend::class, 'branch_id', 'id');
    }

    /**
     * Get all of the incomes for the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'branch_id', 'id');
    }

    /**
     * Get all of the stocks for the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'branch_id', 'id');
    }
}
