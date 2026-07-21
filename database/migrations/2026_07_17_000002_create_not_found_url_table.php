<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('not_found_url', function (Blueprint $table) {
            $table->id();
            $table->string('path', 512);
            $table->date('reportDate');
            $table->unsignedInteger('occurrences')->default(1);
            $table->timestamp('firstSeenAt')->useCurrent();
            $table->timestamp('lastSeenAt')->useCurrent();
            $table->unique(['path', 'reportDate']);
            $table->index(['reportDate', 'occurrences']);
            $table->index('lastSeenAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('not_found_url');
    }
};
