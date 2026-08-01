<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $now = now();
        $defaults = [
            'site_title' => [
                'value' => 'Lanyard Bekasi',
                'legacy' => ['Pusat Tali Lanyard', ''],
            ],
            'site_url' => [
                'value' => 'https://lanyardbekasi.com',
                'legacy' => ['https://pusattalilanyard.com', 'https://pusattalilanyard.com/', ''],
            ],
            'logo_url' => [
                'value' => '/uploads/lanyard-bekasi-logo.webp',
                'legacy' => ['/uploads/pusat-tali-lanyard-logo.webp', ''],
            ],
            'site_logo' => [
                'value' => '/uploads/lanyard-bekasi-logo.webp',
                'legacy' => ['/uploads/pusat-tali-lanyard-logo.webp', ''],
            ],
            'site_logo_header' => [
                'value' => '/uploads/lanyard-bekasi-logo.webp',
                'legacy' => ['/uploads/pusat-tali-lanyard-logo.webp', ''],
            ],
            'site_logo_mobile' => [
                'value' => '/uploads/lanyard-bekasi-logo.webp',
                'legacy' => ['/uploads/pusat-tali-lanyard-logo.webp', ''],
            ],
            'favicon_url' => [
                'value' => '/uploads/lanyard-bekasi-favicon.webp',
                'legacy' => ['/uploads/pusat-tali-lanyard-favicon.webp', ''],
            ],
            'site_favicon' => [
                'value' => '/uploads/lanyard-bekasi-favicon.webp',
                'legacy' => ['/uploads/pusat-tali-lanyard-favicon.webp', ''],
            ],
            'og_image' => [
                'value' => '/uploads/lanyard-bekasi-logo.webp',
                'legacy' => ['/uploads/pusat-tali-lanyard-logo.webp', ''],
            ],
            'contact_email' => [
                'value' => 'info@lanyardbekasi.com',
                'legacy' => [
                    'info@pusattalilanyard.com',
                    'hello@pusattalilanyard.com',
                    'hello@pusattalilanyard.id',
                    '',
                ],
            ],
        ];

        foreach ($defaults as $key => $definition) {
            $current = DB::table('setting')->where('key', $key)->value('value');

            if ($current === null) {
                DB::table('setting')->insert([
                    'key' => $key,
                    'value' => $definition['value'],
                    'createdAt' => $now,
                    'updatedAt' => $now,
                ]);

                continue;
            }

            if (in_array(trim((string) $current), $definition['legacy'], true)) {
                DB::table('setting')->where('key', $key)->update([
                    'value' => $definition['value'],
                    'updatedAt' => $now,
                ]);
            }
        }

        foreach ([
            'site_description',
            'seo_meta_title',
            'seo_meta_description',
            'footer_text',
            'google_search_console_site_url',
        ] as $key) {
            $current = DB::table('setting')->where('key', $key)->value('value');
            if (! is_string($current) || $current === '') {
                continue;
            }

            $updated = str_replace(
                [
                    'https://pusattalilanyard.com',
                    'pusattalilanyard.com',
                    'PusatTaliLanyard',
                    'Pusat Tali Lanyard',
                    'pusattalilanyard',
                ],
                [
                    'https://lanyardbekasi.com',
                    'lanyardbekasi.com',
                    'LanyardBekasi',
                    'Lanyard Bekasi',
                    'lanyardbekasi',
                ],
                $current,
            );

            if ($updated !== $current) {
                DB::table('setting')->where('key', $key)->update([
                    'value' => $updated,
                    'updatedAt' => $now,
                ]);
            }
        }

        foreach (['social_instagram', 'social_facebook', 'social_tiktok'] as $key) {
            $current = DB::table('setting')->where('key', $key)->value('value');
            if (is_string($current) && str_contains(strtolower($current), 'pusattalilanyard')) {
                DB::table('setting')->where('key', $key)->update([
                    'value' => '',
                    'updatedAt' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Brand migrations intentionally do not restore stale public identity data.
    }
};
