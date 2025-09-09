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
            $table->unsignedBigInteger('source_id')->change();
            $table->unsignedBigInteger('author_id')->change();
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->change();
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->unsignedBigInteger('language_id')->nullable()->change();
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->change();
        });

        Schema::table('user_views', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('item_id')->change();
        });

        Schema::table('user_interests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('item_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->integer('source_id')->change();
            $table->integer('author_id')->change();
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->tinyInteger('provider_id')->change();
            $table->integer('category_id')->nullable()->change();
            $table->integer('country_id')->nullable()->change();
            $table->integer('language_id')->nullable()->change();
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->integer('source_id')->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('country_id')->change();
        });

        Schema::table('user_views', function (Blueprint $table) {
            $table->integer('user_id')->change();
            $table->integer('item_id')->change();
        });

        Schema::table('user_interests', function (Blueprint $table) {
            $table->integer('user_id')->change();
            $table->integer('item_id')->change();
        });
    }
};
