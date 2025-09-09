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
        Schema::table('sources', function (Blueprint $table) {
            $table->index(['category_id', 'country_id', 'language_id'], 'sources_category_country_language_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('status');
        });

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
        Schema::table('sources', function (Blueprint $table) {
            $table->dropIndex('sources_category_country_language_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('user_interests', function (Blueprint $table) {
            $table->dropIndex('user_interests_user_type_index');
        });

        Schema::table('user_views', function (Blueprint $table) {
            $table->dropIndex('user_views_user_type_index');
        });
    }
};
