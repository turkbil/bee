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
            // Anasayfa sıralama kolonu
            $table->integer('homepage_sort_order')
                ->nullable()
                ->index()
                ->after('show_on_homepage')
                ->comment('Anasayfada gösterim sırası (null = sıralanmamış)');
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
            $table->dropColumn('homepage_sort_order');
        });
    }
};
