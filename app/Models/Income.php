<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    const TYPE_TRANSACTION = 'TRANSACTION';
    const TYPE_OTHER = 'OTHER';

    const TYPES_INCOME = [
        self::TYPE_TRANSACTION,
        self::TYPE_OTHER
    ];

    protected $fillable = [
        'type',
        'description',
        'amount',
        'branch_id'
    ];

    /**
     * Get the branch that owns the Income
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
