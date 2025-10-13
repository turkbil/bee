<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tenant'ların kendi özel JSON kategorilerini oluşturabilmesi için
     * custom_json_fields kolonu ekler.
     *
     * Örnek yapı:
     * {
     *   "Teknik Çizimler": [
     *     {"name": "Montaj Çizimi", "url": "..."}
     *   ],
     *   "Video Linkleri": [
     *     {"title": "Kullanım Videosu", "url": "https://youtube.com/..."}
     *   ],
     *   "Ekstra Bilgiler": {
     *     "Üretim Yeri": "Türkiye",
     *     "Garanti Süresi": "24 ay"
     *   }
     * }
     */
    public function up(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->json('custom_json_fields')->nullable()
                ->after('tags')
                ->comment('Tenant tarafından tanımlanan özel JSON kategorileri - Dinamik yapı');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn('custom_json_fields');
        });
    }
};
