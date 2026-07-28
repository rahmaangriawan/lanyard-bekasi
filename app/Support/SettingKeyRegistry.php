<?php

namespace App\Support;

final class SettingKeyRegistry
{
    public const API_WRITABLE = [
        'site_title',
        'site_description',
        'footer_text',
        'site_logo',
        'site_logo_header',
        'site_logo_mobile',
        'site_favicon',
        'og_image',
        'contact_phone',
        'contact_whatsapp',
        'contact_email',
        'contact_address',
        'seo_meta_title',
        'seo_meta_description',
        'google_site_verification',
        'bing_site_verification',
        'seo_auto_links',
        'seo_auto_links_limit',
        'social_instagram',
        'social_facebook',
        'social_tiktok',
        'turnstile_homepage_enabled',
        'turnstile_login_enabled',
        'turnstile_site_key',
        'turnstile_secret_key',
        'otp_verify_url',
        'otp_api_key',
        'otp_target',
        'google_service_account_json',
        'google_analytics_measurement_id',
        'google_analytics_property_id',
        'google_search_console_site_url',
        'google_spreadsheet_id',
        'google_spreadsheet_range',
    ];
}
