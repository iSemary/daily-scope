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
        // Make country_id nullable in users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->change();
        });

        // Make parent_id nullable in categories table (if not already)
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert country_id to not nullable
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable(false)->change();
        });
    }
};
