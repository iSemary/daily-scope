<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->change();
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->unsignedBigInteger('language_id')->nullable()->change();
        });

        if (!$this->foreignKeyExists('articles', 'articles_source_id_foreign')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('articles', 'articles_author_id_foreign')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('sources', 'sources_provider_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('sources', 'sources_category_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('sources', 'sources_country_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('sources', 'sources_language_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->foreign('language_id')->references('id')->on('languages')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('authors', 'authors_source_id_foreign')) {
            Schema::table('authors', function (Blueprint $table) {
                $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('categories', 'categories_parent_id_foreign')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('users', 'users_country_id_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('user_views', 'user_views_user_id_foreign')) {
            Schema::table('user_views', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('user_interests', 'user_interests_user_id_foreign')) {
            Schema::table('user_interests', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints first (only if they exist)
        if ($this->foreignKeyExists('articles', 'articles_source_id_foreign')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropForeign(['source_id']);
            });
        }

        if ($this->foreignKeyExists('articles', 'articles_author_id_foreign')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropForeign(['author_id']);
            });
        }

        if ($this->foreignKeyExists('sources', 'sources_provider_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->dropForeign(['provider_id']);
            });
        }

        if ($this->foreignKeyExists('sources', 'sources_category_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
            });
        }

        if ($this->foreignKeyExists('sources', 'sources_country_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->dropForeign(['country_id']);
            });
        }

        if ($this->foreignKeyExists('sources', 'sources_language_id_foreign')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->dropForeign(['language_id']);
            });
        }

        if ($this->foreignKeyExists('authors', 'authors_source_id_foreign')) {
            Schema::table('authors', function (Blueprint $table) {
                $table->dropForeign(['source_id']);
            });
        }

        if ($this->foreignKeyExists('categories', 'categories_parent_id_foreign')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
            });
        }

        if ($this->foreignKeyExists('users', 'users_country_id_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['country_id']);
            });
        }

        if ($this->foreignKeyExists('user_views', 'user_views_user_id_foreign')) {
            Schema::table('user_views', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        if ($this->foreignKeyExists('user_interests', 'user_interests_user_id_foreign')) {
            Schema::table('user_interests', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }
    }

    /**
     * Check if a foreign key constraint exists
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite
            $constraints = DB::select("
                SELECT name 
                FROM sqlite_master 
                WHERE type = 'table' 
                AND name = ? 
                AND sql LIKE ?
            ", [$table, "%{$constraintName}%"]);

            return count($constraints) > 0;
        } else {
            // MySQL
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND CONSTRAINT_NAME = ?
            ", [$table, $constraintName]);

            return count($constraints) > 0;
        }
    }
};
