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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'full_name');
            $table->bigInteger('phone')->unique()->nullable()->after('email');
            $table->string('username', 64)->nullable()->after('phone');
            $table->integer('country_id')->after('username');
            $table->timestamp('last_password_at')->nullable()->after('password');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['last_password_at', 'country_id', 'username', 'phone']);
            $table->renameColumn('full_name', 'name');
        });
    }
};