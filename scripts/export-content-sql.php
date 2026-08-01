<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require dirname(__DIR__).'/vendor/autoload.php';

$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$tables = ['category', 'post', 'page', 'media', 'setting', 'product', 'portfolio', 'citypage'];
$pdo = DB::connection()->getPdo();
$statements = [
    '-- Public CMS content generated from the local Lanyard Bekasi database.',
    'SET NAMES utf8mb4;',
    'SET FOREIGN_KEY_CHECKS = 0;',
    '-- The bundled blog is a complete snapshot, not an additive seed.',
    'DELETE FROM `comment`;',
    'DELETE FROM `post`;',
    "DELETE FROM `media` WHERE `filepath` LIKE 'storage/uploads/blog-import/%' OR `url` LIKE '/storage/uploads/blog-import/%';",
];

foreach ($tables as $table) {
    if (! Schema::hasTable($table)) {
        continue;
    }

    foreach (DB::table($table)->orderBy('id')->get() as $row) {
        $values = get_object_vars($row);
        if ($table === 'setting' && in_array($values['key'] ?? null, [
            'google_service_account_json',
            'otp_api_key',
            'turnstile_secret_key',
        ], true)) {
            continue;
        }
        if ($table === 'post' && array_key_exists('authorId', $values)) {
            // User accounts are environment-specific; ImportInitialContent remaps this to the local primary admin.
            $values['authorId'] = null;
        }
        $columns = array_keys($values);
        $quotedColumns = implode(', ', array_map(fn (string $column) => '`'.$column.'`', $columns));
        $quotedValues = implode(', ', array_map(
            fn (mixed $value) => $value === null ? 'NULL' : $pdo->quote((string) $value),
            $values,
        ));
        $updates = implode(', ', array_map(
            fn (string $column) => '`'.$column.'` = VALUES(`'.$column.'`)',
            array_values(array_filter($columns, fn (string $column) => $column !== 'id')),
        ));

        $statements[] = "INSERT INTO `{$table}` ({$quotedColumns}) VALUES ({$quotedValues}) ON DUPLICATE KEY UPDATE {$updates};";
    }
}

$statements[] = 'SET FOREIGN_KEY_CHECKS = 1;';

$path = dirname(__DIR__).'/database/mysql-content.sql';
file_put_contents($path, implode(PHP_EOL, $statements).PHP_EOL);

echo "Wrote {$path}".PHP_EOL;

$schemaPath = dirname(__DIR__).'/database/mysql-schema.sql';
$fullPath = dirname(__DIR__).'/database/pusat-tali-lanyard-full.sql';
if (! is_file($schemaPath)) {
    throw new RuntimeException("Schema bundle tidak ditemukan: {$schemaPath}");
}

$schema = rtrim((string) file_get_contents($schemaPath));
$content = rtrim((string) file_get_contents($path));
file_put_contents(
    $fullPath,
    $schema.PHP_EOL.PHP_EOL
    .'-- Public Lanyard Bekasi content snapshot; credentials and environment-specific users are excluded.'.PHP_EOL
    .$content.PHP_EOL,
);

echo "Wrote {$fullPath}".PHP_EOL;
