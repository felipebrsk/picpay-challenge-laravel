<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\{Collection, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{HasMany, HasOne, MorphTo};

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'userable_id',
        'userable_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Eager load default relations.
     *
     * @var array<int, string>
     */
    protected $with = [
        'userable',
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

    /**
     * Boot creating wallet.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function (self $user) {
            $user->wallet()->create();
        });
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The customer type const.
     *
     * @var string
     */
    public const CUSTOMER_TYPE = 'customer';

    /**
     * The shopkeeper type const.
     *
     * @var string
     */
    public const SHOPKEEEPER_TYPE = 'shopkeeper';

    /**
     * Verify if the user has the given userable type.
     *
     * @param string $class
     * @return bool
     */
    public function isType(string $class): bool
    {
        return $this->userable instanceof $class;
    }

    /**
     * Get the userable that belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the wallet associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get all of the transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function transactions(): Collection
    {
        return $this->sentTransactions->merge($this->receivedTransactions);
    }

    /**
     * Get all of the sentTransactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_id');
    }

    /**
     * Get all of the receivedTransactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_id');
    }

    /**
     * Update the user balance.
     *
     * @param int $type
     * @param int $amount
     * @return self
     */
    public function updateBalance(int $type, int $amount): void
    {
        $this->load('wallet');

        if ($type === Transaction::ADDITION_CONST_ID) {
            $this->wallet->update([
                'balance' => $this->wallet->balance + $amount,
            ]);

            return;
        }

        $this->wallet->update([
            'balance' => $this->wallet->balance - $amount,
        ]);
    }
}
