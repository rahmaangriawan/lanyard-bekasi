<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

final class PublicApi
{
    /**
     * @param list<string>|null $keys
     * @return array<string, string>
     */
    public static function settings(?array $keys = null): array
    {
        $settings = Setting::query()
            ->when($keys, fn ($query) => $query->whereIn('key', $keys))
            ->pluck('value', 'key')
            ->all();

        foreach (['site_logo', 'site_favicon', 'og_image', 'logo_url', 'favicon_url', 'site_logo_header', 'site_logo_mobile'] as $key) {
            if (array_key_exists($key, $settings)) {
                $fallback = str_contains($key, 'favicon')
                    ? '/uploads/pusat-tali-lanyard-favicon.webp'
                    : (str_contains($key, 'header')
                        ? '/uploads/pusat-tali-lanyard-logo.webp'
                        : (str_contains($key, 'mobile') ? '/uploads/pusat-tali-lanyard-logo.webp' : '/uploads/pusat-tali-lanyard-logo.webp'));
                $settings[$key] = AssetUrl::relative($settings[$key], $fallback) ?? $fallback;
            }
        }

        return $settings;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function json(array $payload, int $seconds = 600): JsonResponse
    {
        return response()->json($payload)
            ->header('Cache-Control', "public, s-maxage={$seconds}, stale-while-revalidate=300");
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function noStoreJson(array $payload): JsonResponse
    {
        return response()->json($payload)
            ->header('Cache-Control', 'no-store, private');
    }

    public static function sanitizeContent(mixed $model): mixed
    {
        if ($model && isset($model->content)) {
            $model->content = HtmlSanitizer::clean($model->content);
        }

        return $model;
    }

    public static function sanitizeProduct(mixed $model): mixed
    {
        if ($model && isset($model->description)) {
            $model->description = HtmlSanitizer::clean($model->description);
        }

        return $model;
    }

    public static function normalizeText(?string $value): ?string
    {
        return is_string($value)
            ? str_replace(['pusattalilanyard', 'Pusat Tali Lanyard', 'Bogor'], ['pusattalilanyard', 'Pusat Tali Lanyard', 'Depok'], $value)
            : $value;
    }
}
