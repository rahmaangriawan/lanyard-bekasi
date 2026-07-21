<?php

namespace App\Models;

use App\Support\AssetUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $table = 'portfolio';

    protected $fillable = ['title', 'description', 'imageUrl', 'logoUrl', 'logoText', 'link'];

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();
        $attributes['imageUrl'] = AssetUrl::relative($this->getRawOriginal('imageUrl'));
        $attributes['logoUrl'] = AssetUrl::relative($this->getRawOriginal('logoUrl'));

        return $attributes;
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => AssetUrl::relative($value),
            set: fn (?string $value) => AssetUrl::relative($value),
        );
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => AssetUrl::relative($value),
            set: fn (?string $value) => AssetUrl::relative($value),
        );
    }
}
