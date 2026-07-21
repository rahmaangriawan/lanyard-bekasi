<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah B-Tree index (standar) di tabel 'product'
        Schema::table('product', function (Blueprint $table) {
            $table->index('slug');
            $table->index('sku');
            $table->index('published');
            $table->index('categoryId');
        });

        // 2. Tambah B-Tree index di tabel 'post'
        Schema::table('post', function (Blueprint $table) {
            $table->index('slug');
            $table->index('published');
            $table->index('categoryId');
        });

        // 3. Tambah B-Tree index di tabel 'category'
        Schema::table('category', function (Blueprint $table) {
            $table->index('slug');
            $table->index('type');
        });

        // SQLite test databases do not support MySQL FULLTEXT syntax.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE product ADD FULLTEXT fulltext_product_description (description)');
            DB::statement('ALTER TABLE post ADD FULLTEXT fulltext_post_title_content (title, content)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['sku']);
            $table->dropIndex(['published']);
            $table->dropIndex(['categoryId']);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE product DROP INDEX fulltext_product_description');
        }

        Schema::table('post', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['published']);
            $table->dropIndex(['categoryId']);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE post DROP INDEX fulltext_post_title_content');
        }

        Schema::table('category', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['type']);
        });
    }
};
