<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Otonom Sistemler Ana Kategorisi Oluşturur
 *
 * EP PDF'deki "5-Otonom" klasörü için gerekli
 */
class OtonomKategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori zaten varsa atla
        $exists = DB::table('shop_categories')
            ->where('category_id', 186)
            ->exists();

        if ($exists) {
            $this->command->warn('⚠️  Otonom kategorisi zaten mevcut (category_id = 186)');
            return;
        }

        DB::table('shop_categories')->insert([
            'category_id' => 186,
            'parent_id' => null, // Ana kategori
            'title' => json_encode([
                'tr' => 'OTONOM SİSTEMLER',
                'en' => 'AUTONOMOUS SYSTEMS'
            ]),
            'slug' => json_encode([
                'tr' => 'otonom-sistemler',
                'en' => 'autonomous-systems'
            ]),
            'description' => json_encode([
                'tr' => 'AGV ve AMR otonom malzeme taşıma sistemleri. Akıllı ve sürücüsüz teknolojilerle deponuzu geleceğe taşıyın.',
                'en' => 'AGV and AMR autonomous material handling systems. Take your warehouse to the future with smart and driverless technologies.'
            ]),
            'icon_class' => 'fa-solid fa-robot',
            'level' => 1, // Ana kategori
            'path' => '186',
            'sort_order' => 16, // Order Picker (15) ve Tow Truck (16) arasına
            'is_active' => 1,
            'show_in_menu' => 1,
            'show_in_homepage' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Otonom Sistemler kategorisi oluşturuldu (category_id = 186)');
    }
}
