<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Foreign key kaldır (gerçek adını kullan)
            $table->dropForeign('ratings_ibfk_1');

            // user_id nullable yap
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Unique constraint kaldır ve yeniden oluştur (nullable için)
            $table->dropUnique('ratings_unique_user_item');
        });

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
