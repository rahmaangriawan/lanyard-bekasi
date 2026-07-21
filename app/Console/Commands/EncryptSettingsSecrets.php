<?php

namespace App\Console\Commands;

use App\Support\SecureSettings;
use Illuminate\Console\Command;

class EncryptSettingsSecrets extends Command
{
    protected $signature = 'settings:encrypt-secrets';

    protected $description = 'Encrypt plain-text secret settings (Google credentials, Turnstile secret, OTP API key) at rest.';

    public function handle(): int
    {
        $count = SecureSettings::migratePlaintextSecrets();

        if ($count === 0) {
            $this->info('Semua secret settings sudah terenkripsi atau kosong.');
        } else {
            $this->info("Berhasil mengenkripsi {$count} secret setting(s).");
        }

        return self::SUCCESS;
    }
}
