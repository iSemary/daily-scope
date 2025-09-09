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
            $table->dropColumn([
                'provider_id',
                'category_id',
                'language_id',
                'country_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->integer('provider_id')->after('is_head');
            $table->integer('category_id')->after('provider_id');
            $table->integer('language_id')->after('category_id');
            $table->integer('country_id')->after('language_id');
        });
    }
};
