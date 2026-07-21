<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirect', function (Blueprint $table) {
            $table->id();
            $table->string('sourcePath', 512)->unique();
            $table->string('targetUrl', 2048);
            $table->boolean('active')->default(true)->index();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirect');
    }
};
