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
        // Media tablosuna PRIMARY KEY ve AUTO_INCREMENT ekliyoruz
        // Önce PRIMARY KEY var mı kontrol et
        $hasPrimaryKey = DB::select("SHOW KEYS FROM media WHERE Key_name = 'PRIMARY'");

        if (empty($hasPrimaryKey)) {
            // PRIMARY KEY yoksa ekle
            DB::statement('ALTER TABLE media ADD PRIMARY KEY (id)');
        }

        // AUTO_INCREMENT ekle
        DB::statement('ALTER TABLE media MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi - AUTO_INCREMENT kaldırma
        DB::statement('ALTER TABLE media MODIFY COLUMN id bigint(20) UNSIGNED NOT NULL');
    }
};
