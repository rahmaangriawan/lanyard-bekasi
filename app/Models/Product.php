<?php

namespace App\Models;

use App\Support\AssetUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $table = 'product';

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'specs',
        'accessories',
        'basePrice',
        'minOrder',
        'shortDescription',
        'sheetStatus',
        'sites',
        'sortOrder',
        'featuredImage',
        'categoryId',
        'description',
        'published',
        'metaTitle',
        'metaDescription',
        'createdAt',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'sortOrder' => 'integer',
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

    public function getDescriptionAttribute(?string $value): string
    {
        return AssetUrl::html($value);
    }

    public function setDescriptionAttribute(?string $value): void
    {
        $this->attributes['description'] = AssetUrl::html($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }
}
