<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * F4 - 2 Ton 48V Li-Ion Transpalet (Genel Kullanım)
 *
 * PDF Kaynağı: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4/F4-EN-Brochure-4.pdf
 * Marka: İXTİF (brand_id = 1)
 * Kategori: TRANSPALETLER (category_id = 165)
 * NOT: F4 201 variant'ı ayrı seeder'da (F4_201_Transpalet_Seeder.php)
 */
class F4_Transpalet_Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 F4 Transpalet (Genel Versiyon) ekleniyor...');

        // Dinamik olarak Transpalet kategorisi ID'sini bul
        $categoryId = DB::table('shop_categories')->where('title->tr', 'Transpalet')->value('category_id');
        if (!$categoryId) {
            $this->command->error('❌ Transpalet kategorisi bulunamadı!');
            return;
        }

        // Brand ID'sini bul veya oluştur
        $brandId = DB::table('shop_brands')->where('title->tr', 'İXTİF')->value('brand_id');
        if (!$brandId) {
            $brandId = DB::table('shop_brands')->insertGetId([
                'title' => json_encode(['tr' => 'İXTİF', 'en' => 'İXTİF']),
                'slug' => json_encode(['tr' => 'ixtif', 'en' => 'ixtif']),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Mevcut kayıtları temizle
        $existingProducts = DB::table('shop_products')
            ->where('sku', 'F4-GENERAL')
            ->pluck('product_id');

        if ($existingProducts->isNotEmpty()) {
            DB::table('shop_products')->whereIn('product_id', $existingProducts)->delete();
            $this->command->info('🧹 Eski F4 kayıtları temizlendi');
        }

        // Ürün ekle
        $productId = DB::table('shop_products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'sku' => 'F4-GENERAL',
            'model_number' => 'F4',
            'parent_product_id' => null,
            'is_master_product' => false,
            'title' => json_encode(['tr' => 'F4 Elektrikli Transpalet 2T - Genel Amaçlı Kullanım'], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode(['tr' => 'f4-elektrikli-transpalet-2t'], JSON_UNESCAPED_UNICODE),
            'short_description' => json_encode(['tr' => 'F4 genel amaçlı 2 ton transpalet. Platform tasarımı ile ekonomik çözüm. F4 201 kompakt versiyonunun kardeş modeli.'], JSON_UNESCAPED_UNICODE),
            'long_description' => json_encode(['tr' => <<<HTML
<section class="marketing-intro">
    <h2>F Serisi Platform Avantajı</h2>
    <p>F4, F Serisi platformunun 2 ton genel kullanım versiyonu. Platform tasarımı ile esnek konfigürasyon ve maliyet avantajı sağlar.</p>
    <p>4 farklı şasi seçeneğiyle pazar ihtiyaçlarına uygun çözümler sunar.</p>
</section>
<section class="marketing-body">
    <h3>Platform Avantajları</h3>
    <ul>
        <li>Basitleştirilmiş konfigürasyon</li>
        <li>Esnek ürün stratejisi</li>
        <li>Maliyet optimizasyonu</li>
        <li>Hızlı tedarik</li>
    </ul>
    <h3>İXTİF Desteği</h3>
    <p>F4 için yeni satış, ikinci el, kiralama ve teknik servis: 0216 755 3 555 | info@ixtif.com</p>
</section>
HTML], JSON_UNESCAPED_UNICODE),
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => 1,
            'currency' => 'TRY',
            'stock_tracking' => 1,
            'current_stock' => 0,
            'lead_time_days' => 30,
            'weight' => 140,
            'dimensions' => json_encode(['length' => 1600, 'width' => 700, 'height' => 110, 'unit' => 'mm'], JSON_UNESCAPED_UNICODE),
            'technical_specs' => json_encode([
                'capacity' => ['load_capacity' => ['value' => 2000, 'unit' => 'kg']],
                'electrical' => [
                    'battery' => ['type' => 'Li-Ion', 'voltage' => 48, 'note' => 'Konfigürasyona göre değişir']
                ]
            ], JSON_UNESCAPED_UNICODE),
            'features' => json_encode(['tr' => ['list' => [
                'F Serisi platform tasarımı',
                '2 ton yük kapasitesi',
                'Esnek konfigürasyon seçenekleri',
                '4 farklı şasi alternatifi',
                'Maliyet optimizasyonu'
            ]]], JSON_UNESCAPED_UNICODE),
            'primary_specs' => json_encode([
                ['label' => 'Yük Kapasitesi', 'value' => '2 Ton'],
                ['label' => 'Platform', 'value' => 'F Series'],
                ['label' => 'Konfigürasyon', 'value' => 'Esnek'],
                ['label' => 'Şasi Seçenekleri', 'value' => '4 Farklı']
            ], JSON_UNESCAPED_UNICODE),
            'is_active' => 1,
            'is_featured' => 0,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ F4 Transpalet eklendi (ID: {$productId})");
        $this->command->info("ℹ️  NOT: F4 201 kompakt versiyon için F4_201_Transpalet_Seeder kullanın");
    }
}
