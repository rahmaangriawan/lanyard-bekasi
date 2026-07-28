<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            if (! Schema::hasColumn('user', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('password');
            }

            if (! Schema::hasColumn('user', 'remember_token')) {
                $table->rememberToken()->after('role');
            }
        });

        DB::table('user')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'remember_token')) {
                $table->dropColumn('remember_token');
            }

            if (Schema::hasColumn('user', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }
};
