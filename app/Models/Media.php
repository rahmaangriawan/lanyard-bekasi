<?php

namespace App\Models;

use App\Support\AssetUrl;
use App\Support\MediaThumbnail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    private ?string $resolvedPreviewUrl = null;

    private ?string $resolvedPreviewSrc = null;

    private ?string $resolvedThumbnailSrc = null;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = null;

    protected $table = 'media';

    protected $fillable = ['filename', 'filepath', 'mimetype', 'size', 'url'];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'createdAt' => 'datetime',
        ];
    }

    public function getUrlAttribute(?string $value): ?string
    {
        return AssetUrl::relative($value);
    }

    public function setUrlAttribute(?string $value): void
    {
        $this->attributes['url'] = AssetUrl::relative($value);
    }

    /**
     * Resolve legacy database URLs through the current public disk. Records
     * imported from older sites often keep an obsolete absolute URL, while
     * filepath still points at the file that is present in storage.
     */
    public function getPreviewUrlAttribute(): string
    {
        if ($this->resolvedPreviewUrl !== null) {
            return $this->resolvedPreviewUrl;
        }

        $rawPath = ltrim(str_replace('\\', '/', trim((string) $this->filepath)), '/');

        if (str_starts_with($rawPath, 'public/')) {
            return $this->resolvedPreviewUrl = '/'.substr($rawPath, strlen('public/'));
        }

        if (str_starts_with($rawPath, 'media/')) {
            $legacyDirectory = trim(substr($rawPath, strlen('media/')), '/');
            $storedFiles = $legacyDirectory !== ''
                ? Storage::disk('public')->files($legacyDirectory)
                : [];

            if ($storedFiles === [] && $legacyDirectory !== '') {
                $rootStorage = dirname(base_path(), 2).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'storage';
                $legacyFiles = glob($rootStorage.DIRECTORY_SEPARATOR.$legacyDirectory.DIRECTORY_SEPARATOR.'*') ?: [];
                $storedFiles = array_map(
                    fn (string $file) => $legacyDirectory.'/'.basename($file),
                    array_values(array_filter($legacyFiles, 'is_file')),
                );
            }

            if ($storedFiles !== []) {
                return $this->resolvedPreviewUrl = '/storage/'.implode('/', array_map('rawurlencode', explode('/', $storedFiles[0])));
            }

            return $this->resolvedPreviewUrl = '/'.$rawPath;
        }

        $path = $this->disk_path;

        if ($path !== '') {
            return $this->resolvedPreviewUrl = '/storage/'.implode('/', array_map('rawurlencode', explode('/', $path)));
        }

        $url = trim((string) $this->url);

        if ($url !== '') {
            return $this->resolvedPreviewUrl = $url;
        }

        return $this->resolvedPreviewUrl = Storage::disk('public')->url((string) $this->filename);
    }

    public function getPreviewSrcAttribute(): string
    {
        if ($this->resolvedPreviewSrc !== null) {
            return $this->resolvedPreviewSrc;
        }

        $url = $this->preview_url;
        $version = implode('-', [
            (string) ($this->getKey() ?: 'new'),
            (string) ($this->size ?: 0),
        ]);

        return $this->resolvedPreviewSrc = $url.(str_contains($url, '?') ? '&' : '?').'v='.rawurlencode($version);
    }

    public function getThumbnailSrcAttribute(): string
    {
        if ($this->resolvedThumbnailSrc !== null) {
            return $this->resolvedThumbnailSrc;
        }

        return $this->resolvedThumbnailSrc = MediaThumbnail::url($this) ?: $this->preview_src;
    }

    public function getDiskPathAttribute(): string
    {
        $path = ltrim(str_replace('\\', '/', trim((string) $this->filepath)), '/');

        if (str_starts_with($path, 'public/') || str_starts_with($path, 'media/')) {
            return '';
        }

        foreach (['storage/app/public/', 'public/storage/', 'storage/', 'public/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
                break;
            }
        }

        return $path;
    }
}
