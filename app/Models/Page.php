<?php

namespace App\Models;

use App\Support\AssetUrl;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $table = 'page';

    protected $fillable = ['title', 'slug', 'content', 'published', 'metaTitle', 'metaDescription', 'createdAt'];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime',
        ];
    }

    public function getContentAttribute(?string $value): string
    {
        return AssetUrl::html($value);
    }

    public function setContentAttribute(?string $value): void
    {
        $this->attributes['content'] = AssetUrl::html($value);
    }
}
