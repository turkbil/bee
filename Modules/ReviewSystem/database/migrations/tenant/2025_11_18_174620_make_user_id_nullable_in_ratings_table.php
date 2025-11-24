<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foreign key var mı kontrol et
        $foreignKeys = \DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'ratings'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND COLUMN_NAME = 'user_id'
        ");

        if (count($foreignKeys) > 0) {
            Schema::table('ratings', function (Blueprint $table) use ($foreignKeys) {
                // Foreign key kaldır (gerçek adını kullan)
                $table->dropForeign($foreignKeys[0]->CONSTRAINT_NAME);
            });
        }

        Schema::table('ratings', function (Blueprint $table) {
            // user_id nullable yap
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // Unique constraint var mı kontrol et
        $uniqueKeys = \DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'ratings'
            AND CONSTRAINT_NAME = 'ratings_unique_user_item'
        ");

        if (count($uniqueKeys) > 0) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropUnique('ratings_unique_user_item');
            });
        }

        // Unique constraint yeniden oluştur (nullable user_id için çalışır)
        Schema::table('ratings', function (Blueprint $table) {
            $table->unique(['user_id', 'ratable_id', 'ratable_type'], 'ratings_unique_user_item');
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Unique constraint kaldır
            $table->dropUnique('ratings_unique_user_item');

            // user_id NOT NULL yap
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // Foreign key geri ekle
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // Unique constraint geri ekle
        Schema::table('ratings', function (Blueprint $table) {
            $table->unique(['user_id', 'ratable_id', 'ratable_type'], 'ratings_unique_user_item');
        });
    }
};
