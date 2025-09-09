<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->index('slug', 'categories_slug_index');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->index('slug', 'sources_slug_index');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->index('slug', 'articles_slug_index');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_slug_index');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->dropIndex('sources_slug_index');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_slug_index');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex(['slug']);
        });
    }

};
