<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shop System V2 - Kategori Spec Template Ekleme (TENANT)
 *
 * Her kategori için sabit 4 kart yapısını tanımlar.
 * Örnek: Transpalet → Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('shop_categories', function (Blueprint $table) {
            // Alanın zaten var olup olmadığını kontrol et
            if (!Schema::hasColumn('shop_categories', 'primary_specs_template')) {
                $table->json('primary_specs_template')->nullable()->after('description')
                    ->comment('Kategori bazlı sabit 4 kart yapısı: {"card_1":{"label":"...","field_path":"...","icon":"...","format":"..."}, ...}');
            }
        });
    }

    public function down()
    {
        Schema::table('shop_categories', function (Blueprint $table) {
            if (Schema::hasColumn('shop_categories', 'primary_specs_template')) {
                $table->dropColumn('primary_specs_template');
            }
        });
    }
};
