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
            // Remove redundant columns that can be accessed via source relationship
            $table->dropColumn([
                'provider_id',    // Can get via source.provider_id
                'category_id',    // Can get via source.category_id
                'language_id',    // Can get via source.language_id
                'country_id',     // Can get via source.country_id
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Add back the columns if rollback is needed
            $table->integer('provider_id')->after('is_head');
            $table->integer('category_id')->after('provider_id');
            $table->integer('language_id')->after('category_id');
            $table->integer('country_id')->after('language_id');
        });
    }
};
