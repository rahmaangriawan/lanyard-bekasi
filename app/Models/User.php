<?php

namespace App\Models;

use App\Support\AssetUrl;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'bio',
        'avatar',
        'email',
        'password',
        'role',
        'email_verified_at',
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
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'userId');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'authorId');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function getAvatarAttribute(?string $value): ?string
    {
        return AssetUrl::relative($value);
    }

    public function setAvatarAttribute(?string $value): void
    {
        $this->attributes['avatar'] = AssetUrl::relative($value);
    }
}
