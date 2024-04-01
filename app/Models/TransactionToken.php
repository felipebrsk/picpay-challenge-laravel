<?php

namespace App\Models;

use Illuminate\Support\{Str, Carbon};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static creating(\Closure $param)
 */
class TransactionToken extends Model
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
        'token',
        'from_id',
        'expires_at',
    ];

    /**
     * Fill the token on transaction token creation.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->token = Str::random(64);
            $model->expires_at = Carbon::now()->addMinutes(30);
        });
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
