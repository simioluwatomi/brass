<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    /**
     * {@inheritdoc}
     */
    protected $appends = ['full_name'];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * A user has many accounts.
     */
    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Account::class, 'user_id');
    }
}
