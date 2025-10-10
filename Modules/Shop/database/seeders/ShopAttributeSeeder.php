<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Shop Attribute Seeder
 *
 * Tüm ürün tipleri için ortak filtreleme attribute'larını oluşturur:
 * - Yük Kapasitesi, Voltaj, Batarya Tipi, Asansör Yüksekliği, vb.
 */
class ShopAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏷️  Shop Attribute Seeder başlatılıyor...');

        // Mevcut attribute'ları temizle (geliştirme için)
        DB::table('shop_product_attributes')->truncate();
        DB::table('shop_attributes')->truncate();

        // 1. YÜK KAPASİTESİ (Tüm ürün tipleri)
        DB::table('shop_attributes')->insert([
            'attribute_id' => 1,
            'title' => json_encode([
                'tr' => 'Yük Kapasitesi',
                'en' => 'Load Capacity',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode([
                'tr' => 'yuk-kapasitesi',
                'en' => 'load-capacity',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'description' => json_encode([
                'tr' => 'Maksimum yük taşıma kapasitesi',
                'en' => 'Maximum load carrying capacity',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'type' => 'range',
            'unit' => 'kg',
            'is_filterable' => 1,
            'is_searchable' => 1,
            'is_comparable' => 1,
            'is_visible' => 1,
            'sort_order' => 1,
            'validation_rules' => json_encode(['min' => 0, 'max' => 50000], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. VOLTAJ (Akülü ürünler)
        DB::table('shop_attributes')->insert([
            'attribute_id' => 2,
            'title' => json_encode([
                'tr' => 'Voltaj',
                'en' => 'Voltage',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode([
                'tr' => 'voltaj',
                'en' => 'voltage',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'description' => json_encode([
                'tr' => 'Batarya voltaj değeri',
                'en' => 'Battery voltage value',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'type' => 'select',
            'unit' => 'V',
            'options' => json_encode([
                ['value' => '24', 'label' => ['tr' => '24V', 'en' => '24V', 'vs.' => '...']],
                ['value' => '48', 'label' => ['tr' => '48V', 'en' => '48V', 'vs.' => '...']],
                ['value' => '80', 'label' => ['tr' => '80V', 'en' => '80V', 'vs.' => '...']],
            ], JSON_UNESCAPED_UNICODE),
            'is_filterable' => 1,
            'is_searchable' => 1,
            'is_comparable' => 1,
            'is_visible' => 1,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. BATARYA TİPİ
        DB::table('shop_attributes')->insert([
            'attribute_id' => 3,
            'title' => json_encode([
                'tr' => 'Batarya Tipi',
                'en' => 'Battery Type',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode([
                'tr' => 'batarya-tipi',
                'en' => 'battery-type',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'description' => json_encode([
                'tr' => 'Kullanılan batarya teknolojisi',
                'en' => 'Battery technology used',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'type' => 'select',
            'options' => json_encode([
                ['value' => 'li-ion', 'label' => ['tr' => 'Li-Ion', 'en' => 'Li-Ion', 'vs.' => '...']],
                ['value' => 'lead-acid', 'label' => ['tr' => 'Kurşun Asit', 'en' => 'Lead Acid', 'vs.' => '...']],
                ['value' => 'gel', 'label' => ['tr' => 'Gel', 'en' => 'Gel', 'vs.' => '...']],
            ], JSON_UNESCAPED_UNICODE),
            'is_filterable' => 1,
            'is_searchable' => 1,
            'is_comparable' => 1,
            'is_visible' => 1,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. ASANSÖR YÜKSEKLİĞİ (Forklift, İstif)
        DB::table('shop_attributes')->insert([
            'attribute_id' => 4,
            'title' => json_encode([
                'tr' => 'Asansör Yüksekliği',
                'en' => 'Lift Height',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode([
                'tr' => 'asansor-yuksekligi',
                'en' => 'lift-height',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'description' => json_encode([
                'tr' => 'Maksimum kaldırma yüksekliği',
                'en' => 'Maximum lifting height',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'type' => 'range',
            'unit' => 'mm',
            'is_filterable' => 1,
            'is_searchable' => 1,
            'is_comparable' => 1,
            'is_visible' => 1,
            'sort_order' => 4,
            'validation_rules' => json_encode(['min' => 0, 'max' => 15000], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. AĞIRLIK
        DB::table('shop_attributes')->insert([
            'attribute_id' => 5,
            'title' => json_encode([
                'tr' => 'Servis Ağırlığı',
                'en' => 'Service Weight',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode([
                'tr' => 'servis-agirligi',
                'en' => 'service-weight',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'description' => json_encode([
                'tr' => 'Ürünün kendi ağırlığı',
                'en' => 'Product own weight',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'type' => 'range',
            'unit' => 'kg',
            'is_filterable' => 1,
            'is_searchable' => 0,
            'is_comparable' => 1,
            'is_visible' => 1,
            'sort_order' => 5,
            'validation_rules' => json_encode(['min' => 0, 'max' => 10000], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. ÇATAL UZUNLUĞU (Transpalet, Forklift)
        DB::table('shop_attributes')->insert([
            'attribute_id' => 6,
            'title' => json_encode([
                'tr' => 'Çatal Uzunluğu',
                'en' => 'Fork Length',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode([
                'tr' => 'catal-uzunlugu',
                'en' => 'fork-length',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'description' => json_encode([
                'tr' => 'Standart çatal uzunluğu',
                'en' => 'Standard fork length',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'type' => 'select',
            'unit' => 'mm',
            'options' => json_encode([
                ['value' => '900', 'label' => ['tr' => '900 mm', 'en' => '900 mm', 'vs.' => '...']],
                ['value' => '1000', 'label' => ['tr' => '1000 mm', 'en' => '1000 mm', 'vs.' => '...']],
                ['value' => '1150', 'label' => ['tr' => '1150 mm', 'en' => '1150 mm', 'vs.' => '...']],
                ['value' => '1220', 'label' => ['tr' => '1220 mm', 'en' => '1220 mm', 'vs.' => '...']],
                ['value' => '1350', 'label' => ['tr' => '1350 mm', 'en' => '1350 mm', 'vs.' => '...']],
                ['value' => '1500', 'label' => ['tr' => '1500 mm', 'en' => '1500 mm', 'vs.' => '...']],
            ], JSON_UNESCAPED_UNICODE),
            'is_filterable' => 1,
            'is_searchable' => 0,
            'is_comparable' => 1,
            'is_visible' => 1,
            'sort_order' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. ÜRÜN DURUMU
        DB::table('shop_attributes')->insert([
            'attribute_id' => 7,
            'title' => json_encode([
                'tr' => 'Ürün Durumu',
                'en' => 'Product Condition',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'slug' => json_encode([
                'tr' => 'urun-durumu',
                'en' => 'product-condition',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'description' => json_encode([
                'tr' => 'Sıfır, ikinci el veya yenilenmiş',
                'en' => 'New, used or refurbished',
                'vs.' => '...'
            ], JSON_UNESCAPED_UNICODE),
            'type' => 'select',
            'options' => json_encode([
                ['value' => 'new', 'label' => ['tr' => 'Sıfır', 'en' => 'New', 'vs.' => '...']],
                ['value' => 'used', 'label' => ['tr' => 'İkinci El', 'en' => 'Used', 'vs.' => '...']],
                ['value' => 'refurbished', 'label' => ['tr' => 'Yenilenmiş', 'en' => 'Refurbished', 'vs.' => '...']],
            ], JSON_UNESCAPED_UNICODE),
            'is_filterable' => 1,
            'is_searchable' => 0,
            'is_comparable' => 0,
            'is_visible' => 1,
            'sort_order' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ 7 adet ortak attribute eklendi!');
    }
}
