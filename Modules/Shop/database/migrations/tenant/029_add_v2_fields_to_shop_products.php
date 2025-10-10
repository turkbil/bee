<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shop System V2 - Yeni Alanlar Ekleme (TENANT)
 *
 * Eklenen alanlar:
 * - primary_specs (4 vitrin kartı)
 * - use_cases (6+ kullanım alanı)
 * - competitive_advantages (5+ rekabet avantajı)
 * - target_industries (20+ hedef sektör)
 * - faq_data (10+ soru-cevap)
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('shop_products', function (Blueprint $table) {
            // Alanın zaten var olup olmadığını kontrol et
            if (!Schema::hasColumn('shop_products', 'primary_specs')) {
                $table->json('primary_specs')->nullable()->after('highlighted_features')
                    ->comment('4 vitrin kartı (kategori bazlı): [{"label":"Denge Tekeri","value":"Yok"}, ...]');
            }

            if (!Schema::hasColumn('shop_products', 'use_cases')) {
                $table->json('use_cases')->nullable()->after('primary_specs')
                    ->comment('Kullanım alanları (6+ senaryo): {"tr":["Senaryo 1","..."],"en":["..."],"vs.":"..."}');
            }

            if (!Schema::hasColumn('shop_products', 'competitive_advantages')) {
                $table->json('competitive_advantages')->nullable()->after('use_cases')
                    ->comment('Rekabet avantajları (5+ madde): {"tr":["Avantaj 1","..."],"en":["..."],"vs.":"..."}');
            }

            if (!Schema::hasColumn('shop_products', 'target_industries')) {
                $table->json('target_industries')->nullable()->after('competitive_advantages')
                    ->comment('Hedef sektörler (20+ sektör): {"tr":["Sektör 1","..."],"en":["..."],"vs.":"..."}');
            }

            if (!Schema::hasColumn('shop_products', 'faq_data')) {
                $table->json('faq_data')->nullable()->after('target_industries')
                    ->comment('Sık sorulan sorular (10+ soru-cevap): [{"question":{"tr":"..."},"answer":{"tr":"..."},"sort_order":1}, ...]');
            }
        });
    }

    public function down()
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $columns = ['primary_specs', 'use_cases', 'competitive_advantages', 'target_industries', 'faq_data'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('shop_products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
