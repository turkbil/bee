<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class ShopBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Brand'ler SADECE tenant database'lerde olmalı
        if (TenantHelpers::isCentral()) {
            $this->command->info('📦 ShopBrandSeeder: sadece tenant database için, atlanıyor...');
            return;
        }

        // Tenant context kontrolü
        if (!tenancy()->initialized) {
            $this->command->error('❌ Tenant context not initialized for ShopBrandSeeder!');
            return;
        }

        // Duplicate check - eğer zaten İXTİF markası varsa skip
        $existingBrand = DB::table('shop_brands')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) = 'İXTİF'")
            ->first();

        if ($existingBrand) {
            $this->command->warn("⚠️  İXTİF markası zaten mevcut (ID: {$existingBrand->brand_id}). Atlanıyor...");
            return;
        }

        // İXTİF markasını ekle
        $brandId = DB::table('shop_brands')->insertGetId([
            'title' => json_encode([
                'tr' => 'İXTİF',
                'en' => 'IXTIF',
                'ar' => 'إكستيف'
            ], JSON_UNESCAPED_UNICODE),

            'slug' => json_encode([
                'tr' => 'ixtif',
                'en' => 'ixtif',
                'ar' => 'ixtif'
            ], JSON_UNESCAPED_UNICODE),

            'description' => json_encode([
                'tr' => 'İXTİF, lojistik ve depolama ekipmanlarında yenilikçi çözümler sunan lider markadır. İstif makineleri, transpaletler ve forkliftler ile güvenli ve verimli malzeme taşıma sistemleri sağlar.',
                'en' => 'IXTIF is a leading brand offering innovative solutions in logistics and warehouse equipment. It provides safe and efficient material handling systems with stackers, pallet trucks and forklifts.',
                'ar' => 'إكستيف هي علامة تجارية رائدة تقدم حلولاً مبتكرة في معدات اللوجستية والمستودعات. توفر أنظمة مناولة مواد آمنة وفعالة مع الرافعات الشوكية وعربات البليت والرافعات.'
            ], JSON_UNESCAPED_UNICODE),

            'logo_url' => null,
            'website_url' => 'https://ixtif.com',
            'country_code' => 'TR',
            'founded_year' => 2010,
            'headquarters' => 'İstanbul, Türkiye',

            'certifications' => json_encode([
                ['name' => 'CE', 'year' => 2012],
                ['name' => 'ISO 9001', 'year' => 2015],
                ['name' => 'ISO 14001', 'year' => 2018],
            ], JSON_UNESCAPED_UNICODE),

            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,

            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ İXTİF markası eklendi (ID: {$brandId})");
    }
}
