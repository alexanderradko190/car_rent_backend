<?php

namespace App\Models\User;

use App\Enums\User\UserRole;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'role' => UserRole::class,
        'email_verified_at' => 'datetime'
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
