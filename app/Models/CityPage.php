<?php

namespace App\Models;

use App\Support\AssetUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class CityPage extends Model
{
    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $table = 'citypage';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'published',
        'featuredImage',
        'parentId',
        'metaTitle',
        'metaDescription',
        'createdAt',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime',
        ];
    }

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();
        $attributes['featuredImage'] = AssetUrl::relative($this->getRawOriginal('featuredImage'));

        return $attributes;
    }

    protected function featuredImage(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => AssetUrl::relative($value),
            set: fn (?string $value) => AssetUrl::relative($value),
        );
    }

    public function getContentAttribute(?string $value): string
    {
        return AssetUrl::html($value);
    }

    public function setContentAttribute(?string $value): void
    {
        $this->attributes['content'] = AssetUrl::html($value);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parentId');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parentId');
    }
}
