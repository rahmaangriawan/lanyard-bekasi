<?php

namespace App\Models;

use App\Support\PublicRedirectRegistry;
use Illuminate\Database\Eloquent\Model;

class PublicRedirect extends Model
{
    public const CREATED_AT = 'createdAt';

    public const UPDATED_AT = 'updatedAt';

    protected $table = 'redirect';

    protected $fillable = ['sourcePath', 'targetUrl', 'active'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => PublicRedirectRegistry::forget());
        static::deleted(fn () => PublicRedirectRegistry::forget());
    }
}
