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
        Schema::table('articles', function (Blueprint $table) {
            // Add indexes on remaining foreign key columns
            $table->index('source_id');
            $table->index('author_id');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->index('provider_id');
            $table->index('category_id');
            $table->index('country_id');
            $table->index('language_id');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->index('source_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('parent_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('country_id');
        });

        Schema::table('user_views', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('item_id');
            $table->index('item_type_id');
        });

        Schema::table('user_interests', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('item_id');
            $table->index('item_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['source_id']);
            $table->dropIndex(['author_id']);
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->dropIndex(['provider_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['country_id']);
            $table->dropIndex(['language_id']);
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropIndex(['source_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['country_id']);
        });

        Schema::table('user_views', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['item_id']);
            $table->dropIndex(['item_type_id']);
        });

        Schema::table('user_interests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['item_id']);
            $table->dropIndex(['item_type_id']);
        });
    }
};
