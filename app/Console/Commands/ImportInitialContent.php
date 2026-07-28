<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AuthorProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportInitialContent extends Command
{
    protected $signature = 'content:import-initial {--force : Import even when posts already exist}';

    protected $description = 'Import the bundled public CMS content into an empty database.';

    public function handle(): int
    {
        $publishedPosts = DB::table('post')->where('published', true)->count();
        $adminMarker = '.initial-admin-seeded';
        $primaryAuthor = AuthorProfile::primaryAuthor();

        if (! $primaryAuthor) {
            if (! $this->ensureInitialAdmin()) {
                return self::FAILURE;
            }

            Storage::disk('local')->put($adminMarker, now()->toIso8601String());
            $primaryAuthor = AuthorProfile::primaryAuthor();
        } else {
            $this->line('Existing admin credentials preserved; environment bootstrap skipped.');
        }

        if (! $this->option('force') && $publishedPosts >= 6) {
            AuthorProfile::assignUnownedPosts($primaryAuthor);
            $this->line('Initial public content already exists; import skipped.');

            return self::SUCCESS;
        }

        $path = database_path('mysql-content.sql');

        if (! is_file($path)) {
            $this->warn('Bundled content dump is not available; import skipped.');

            return self::SUCCESS;
        }

        $sql = file_get_contents($path);

        if ($sql === false || trim($sql) === '') {
            $this->warn('Bundled content dump is empty; import skipped.');

            return self::SUCCESS;
        }

        DB::unprepared($sql);
        $primaryAuthor = AuthorProfile::primaryAuthor();
        if (! $primaryAuthor) {
            $this->error('Initial content tidak dapat dihubungkan karena akun ADMIN tidak ditemukan.');

            return self::FAILURE;
        }
        AuthorProfile::assignUnownedPosts($primaryAuthor);
        $this->info('Initial CMS content imported.');

        return self::SUCCESS;
    }

    private function ensureInitialAdmin(): bool
    {
        $email = trim((string) env('CMS_ADMIN_EMAIL'));
        $password = (string) env('CMS_ADMIN_PASSWORD');

        if ($email === '' || $password === '') {
            $this->error('CMS_ADMIN_EMAIL dan CMS_ADMIN_PASSWORD wajib diisi saat membuat admin awal.');

            return false;
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => trim((string) env('CMS_ADMIN_NAME')) ?: 'Administrator',
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->slug) {
            $admin->slug = AuthorProfile::nextSlug($admin, $admin->name);
            $admin->save();
        }

        $this->line("Initial admin account is ready: {$email}");

        return true;
    }
}
