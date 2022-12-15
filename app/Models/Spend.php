<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spend extends Model
{
    use HasFactory;

    const TYPE_TRANSACTION = 'TRANSACTION';
    const TYPE_OTHER = 'OTHER';

    const TYPES_SPEND = [
        self::TYPE_TRANSACTION,
        self::TYPE_OTHER
    ];

    protected $fillable = [
        'type',
        'description',
        'amount',
        'branch_id'
    ];

    protected $casts = [
        'amount' => 'integer',
        'created_at' => "datetime:Y-m-d"
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
