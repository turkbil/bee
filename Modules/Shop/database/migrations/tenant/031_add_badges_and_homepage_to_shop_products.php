<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_products', function (Blueprint $table) {
            // Anasayfa gösterim kontrolü
            $table->boolean('show_on_homepage')
                ->default(false)
                ->index()
                ->after('is_bestseller')
                ->comment('Anasayfada gösterilsin mi?');

            // Badge sistemi - Esnek JSON yapısı
            $table->json('badges')
                ->nullable()
                ->after('show_on_homepage')
                ->comment('Ürün etiketleri/badge\'leri - JSON array: [{"type":"new_arrival","label":{"tr":"Yeni"},"color":"green","icon":"sparkles","priority":1,"is_active":true,"value":null}]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn(['show_on_homepage', 'badges']);
        });
    }
};
