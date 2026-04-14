<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'is_admin',
        'device_token',
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
        ];
    }

    public function addresses()
{
    return $this->hasMany(UserAddress::class);
}

public function defaultAddress()
{
    return $this->hasOne(UserAddress::class)->where('is_default', true);
}

public function orders()
{
    return $this->hasMany(Order::class);
}

public function isAdmin()
{
    return $this->role === 'admin';
}

protected static function boot(): void
{
    parent::boot();
    static::creating(fn($m) => $m->uuid ??= Str::uuid());
}

public function getRouteKeyName(): string
{
    return 'uuid';
}
}
