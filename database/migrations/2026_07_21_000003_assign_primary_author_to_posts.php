<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $authorId = DB::table('user')
            ->where('role', 'ADMIN')
            ->orderBy('id')
            ->value('id');

        if ($authorId) {
            DB::table('post')->whereNull('authorId')->update(['authorId' => $authorId]);
        }
    }

    public function down(): void
    {
        // Content ownership must not be removed during a rollback.
    }
};
