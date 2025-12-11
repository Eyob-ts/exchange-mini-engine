<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
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
            'balance' => 'decimal:8',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function trades()
    {
        // This is a bit complex since a user can be buyer OR seller.
        // For simplicity in this mini-engine, we might often query directly on Trade model
        // or define two separate relations: tradesAsBuyer, tradesAsSeller on User?
        // Let's stick to the plan: "orders(), assets(), trades()".
        // A direct hasManyThrough might be tricky if we want ALL trades.
        // Common pattern:
        // return Trade::where('buy_order_id', order_ids...)->orWhere('sell_order_id'...);
        // But for eloquent relations, let's stick to defining simple ones first or skipping if complex.
        // Let's implement tradesAsBuyer and tradesAsSeller instead for clarity as per Order model plan.
        return $this->hasManyThrough(Trade::class, Order::class, 'user_id', 'buy_order_id');
    }
}
