<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Throwable;

final class SecureSettings
{
    public const SECRET_KEYS = [
        'google_service_account_json',
        'turnstile_secret_key',
        'otp_api_key',
    ];

    private const PREFIX = 'enc:v1:';

    public static function isSecret(string $key): bool
    {
        return in_array($key, self::SECRET_KEYS, true);
    }

    public static function isEncryptedPayload(?string $value): bool
    {
        return is_string($value) && str_starts_with($value, self::PREFIX);
    }

    /**
     * Encrypt a secret setting value for at-rest storage.
     * Non-secret keys and empty values are returned unchanged.
     * Already-encrypted payloads are left as-is (idempotent).
     */
    public static function encrypt(string $key, ?string $value): string
    {
        $value = (string) $value;

        if ($value === '' || ! self::isSecret($key)) {
            return $value;
        }

        if (self::isEncryptedPayload($value)) {
            return $value;
        }

        return self::PREFIX.Crypt::encryptString($value);
    }

    /**
     * Decrypt a setting value. Plain-text legacy values pass through
     * so existing databases keep working until they are re-saved.
     */
    public static function decrypt(string $key, ?string $value): string
    {
        $value = (string) $value;

        if ($value === '' || ! self::isSecret($key)) {
            return $value;
        }

        if (! self::isEncryptedPayload($value)) {
            // Transparent upgrade path: encrypt legacy plaintext when first read.
            try {
                Setting::query()->where('key', $key)->where('value', $value)->update([
                    'value' => self::encrypt($key, $value),
                ]);
            } catch (Throwable) {
                // Best-effort; still return usable plaintext to callers.
            }

            return $value;
        }

        try {
            return Crypt::decryptString(substr($value, strlen(self::PREFIX)));
        } catch (DecryptException|Throwable) {
            // Corrupted or foreign-key payload — return empty rather than crash readers.
            return '';
        }
    }

    /**
     * @param  array<string, string|null>  $settings
     * @return array<string, string>
     */
    public static function decryptMany(array $settings): array
    {
        $decoded = [];

        foreach ($settings as $key => $value) {
            $decoded[(string) $key] = self::decrypt((string) $key, $value);
        }

        return $decoded;
    }

    /**
     * Public / API-safe view of settings: secrets are never returned raw.
     *
     * @param  array<string, string|null>  $settings
     * @return array<string, string>
     */
    public static function forPublic(array $settings): array
    {
        $safe = [];

        foreach ($settings as $key => $value) {
            $key = (string) $key;

            if (self::isSecret($key)) {
                $plain = self::decrypt($key, $value);
                $safe[$key] = $plain !== '' ? '[configured]' : '';
                continue;
            }

            $safe[$key] = (string) ($value ?? '');
        }

        return $safe;
    }

    public static function put(string $key, ?string $value): Setting
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => self::encrypt($key, $value)],
        );
    }

    /**
     * Read one decrypted setting value.
     */
    public static function get(string $key, string $default = ''): string
    {
        $raw = Setting::query()->where('key', $key)->value('value');

        if ($raw === null) {
            return $default;
        }

        return self::decrypt($key, (string) $raw);
    }

    /**
     * @param  list<string>|null  $keys
     * @return array<string, string>
     */
    public static function getMany(?array $keys = null): array
    {
        $rows = Setting::query()
            ->when($keys, fn ($query) => $query->whereIn('key', $keys))
            ->pluck('value', 'key')
            ->all();

        return self::decryptMany($rows);
    }

    /**
     * Re-encrypt any plain-text secrets already stored in the database.
     * Safe to call repeatedly (idempotent).
     */
    public static function migratePlaintextSecrets(): int
    {
        $count = 0;

        $rows = Setting::query()
            ->whereIn('key', self::SECRET_KEYS)
            ->get(['id', 'key', 'value']);

        foreach ($rows as $row) {
            $raw = (string) $row->value;

            if ($raw === '' || self::isEncryptedPayload($raw)) {
                continue;
            }

            $row->value = self::encrypt((string) $row->key, $raw);
            $row->save();
            $count++;
        }

        return $count;
    }
}
