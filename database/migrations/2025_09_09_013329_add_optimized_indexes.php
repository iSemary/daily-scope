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
        // Add only the indexes that don't already exist from previous migrations
        
        // Sources table - add composite index for common filtering patterns
        Schema::table('sources', function (Blueprint $table) {
            // Individual indexes already exist from 2025_09_09_013328_add_foreign_key_indexes.php
            // Only add composite index for common filtering patterns
            $table->index(['category_id', 'country_id', 'language_id'], 'sources_category_country_language_index');
        });

        // Categories table - add status index (parent_id already exists from previous migration)
        Schema::table('categories', function (Blueprint $table) {
            $table->index('status');
        });

        // User interests and views - add composite indexes for common queries
        // Individual indexes already exist from 2025_09_09_013328_add_foreign_key_indexes.php
        Schema::table('user_interests', function (Blueprint $table) {
            $table->index(['user_id', 'item_type_id'], 'user_interests_user_type_index');
        });

        Schema::table('user_views', function (Blueprint $table) {
            $table->index(['user_id', 'item_type_id'], 'user_views_user_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop only the indexes that this migration created
        
        // Drop sources composite index
        Schema::table('sources', function (Blueprint $table) {
            $table->dropIndex('sources_category_country_language_index');
        });

        // Drop categories status index
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        // Drop user interests composite index
        Schema::table('user_interests', function (Blueprint $table) {
            $table->dropIndex('user_interests_user_type_index');
        });

        // Drop user views composite index
        Schema::table('user_views', function (Blueprint $table) {
            $table->dropIndex('user_views_user_type_index');
        });
    }
};
