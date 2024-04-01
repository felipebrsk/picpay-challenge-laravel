<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'to_id',
        'amount',
        'status',
        'from_id',
    ];

    /**
     * The addition const id.
     *
     * @var int
     */
    public const ADDITION_CONST_ID = 1;

    /**
     * The subtraction const id.
     *
     * @var int
     */
    public const SUBTRACTION_CONST_ID = 2;

    /**
     * The normal transaction type const id.
     *
     * @var int
     */
    public const NORMAL_TRANSACTION_TYPE_ID = 3;

    /**
     * The chargeback transaction type const id.
     *
     * @var int
     */
    public const CHARGEBACK_TRANSACTION_TYPE_ID = 4;

    /**
     * The sent const id.
     *
     * @var int
     */
    public const SENT_CONST_ID = 98;

    /**
     * The received const id.
     *
     * @var int
     */
    public const RECEIVED_CONST_ID = 99;

    /**
     * Get the from or to transaction.
     *
     * @return int
     */
    public function getTypeAttribute(): int
    {
        $userId = Auth::id();

        return $this->to_id == $userId ? self::RECEIVED_CONST_ID : self::SENT_CONST_ID;
    }

    /**
     * Get the to that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function to(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    /**
     * Get the from that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id');
    }
}
