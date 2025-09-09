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
        // Add indexes on slug columns for better lookup performance
        // Note: slug columns already have unique constraints, but we're adding regular indexes
        // for better performance on WHERE clauses that don't use the unique constraint
        
        Schema::table('categories', function (Blueprint $table) {
            // Slug already has unique index, but adding regular index for better performance
            // when used in JOINs and WHERE clauses
            $table->index('slug', 'categories_slug_index');
        });

        Schema::table('sources', function (Blueprint $table) {
            // Slug already has unique index, but adding regular index for better performance
            $table->index('slug', 'sources_slug_index');
        });

        Schema::table('articles', function (Blueprint $table) {
            // Slug already has unique index, but adding regular index for better performance
            $table->index('slug', 'articles_slug_index');
        });

        Schema::table('authors', function (Blueprint $table) {
            // Add index on slug column for lookup performance
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
