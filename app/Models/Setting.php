<?php

namespace App\Models;

use App\Support\AssetUrl;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    private const ASSET_FALLBACKS = [
        'site_logo' => '/uploads/pusat-tali-lanyard-logo.webp',
        'logo_url' => '/uploads/pusat-tali-lanyard-logo.webp',
        'site_logo_header' => '/uploads/pusat-tali-lanyard-logo.webp',
        'site_logo_mobile' => '/uploads/pusat-tali-lanyard-logo.webp',
        'site_favicon' => '/uploads/pusat-tali-lanyard-favicon.webp',
        'favicon_url' => '/uploads/pusat-tali-lanyard-favicon.webp',
        'og_image' => '/uploads/pusat-tali-lanyard-logo.webp',
    ];

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $table = 'setting';

    protected $fillable = ['key', 'value'];

    public function getValueAttribute(?string $value): string
    {
        $key = (string) ($this->attributes['key'] ?? '');

        if (array_key_exists($key, self::ASSET_FALLBACKS)) {
            return AssetUrl::relative($value, self::ASSET_FALLBACKS[$key]) ?? self::ASSET_FALLBACKS[$key];
        }

        return (string) $value;
    }

    public function setValueAttribute(?string $value): void
    {
        $key = (string) ($this->attributes['key'] ?? '');
        $this->attributes['value'] = array_key_exists($key, self::ASSET_FALLBACKS)
            ? AssetUrl::relative($value, self::ASSET_FALLBACKS[$key])
            : (string) $value;
    }
}
