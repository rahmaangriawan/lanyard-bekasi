<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsProductSync;
use Illuminate\Console\Command;

class SyncGoogleProducts extends Command
{
    protected $signature = 'products:sync-google';

    protected $description = 'Sinkronisasi produk dari Google Sheets';

    public function handle(GoogleSheetsProductSync $sync): int
    {
        $this->info('Memulai sinkronisasi produk dari Google Sheets...');

        try {
            $result = $sync->sync();
        } catch (\Throwable $exception) {
            $this->error('Sinkronisasi gagal: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Sinkronisasi selesai. {$result['created']} produk baru, {$result['updated']} diperbarui, {$result['skipped']} dilewati.");

        return self::SUCCESS;
    }
}
