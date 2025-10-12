<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

/**
 * Shop Tax Seeder
 *
 * Türkiye KDV oranlarını ve vergi kurallarını oluşturur.
 * NOT: Bu seeder sadece CENTRAL database'de çalışmalıdır!
 */
class ShopTaxSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant context'te çalışmayı engelle
        if (!TenantHelpers::isCentral()) {
            $this->command->warn('⚠️  ShopTaxSeeder sadece central database için, atlanıyor...');
            return;
        }

        $this->command->info('💰 Shop Tax Seeder başlatılıyor...');

        // 1. STANDART KDV (%20) - Forklift/Transpalet
        $taxId = DB::table('shop_taxes')->insertGetId([
            'title' => json_encode([
                'tr' => 'KDV %20 (Standart)',
                'en' => 'VAT 20% (Standard)',
            ], JSON_UNESCAPED_UNICODE),
            'code' => 'TR-VAT-20',
            'description' => json_encode([
                'tr' => 'Türkiye standart KDV oranı - Forklift, transpalet ve endüstriyel ekipmanlar için geçerli',
                'en' => 'Turkey standard VAT rate - Applicable for forklifts, pallet trucks and industrial equipment',
            ], JSON_UNESCAPED_UNICODE),
            'rate' => 20.00,
            'tax_type' => 'vat',
            'applies_to' => 'products',
            'is_compound' => false,
            'country_codes' => json_encode(json_decode(<<<'JSON'
                [
                    "TR"
                ]
JSON
            , true), JSON_UNESCAPED_UNICODE),
            'excluded_regions' => null,
            'priority' => 1,
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Standart KDV %20 eklendi (Tax ID: ' . $taxId . ')');

        // 2. TAX RATES - Tüm Türkiye için aynı oran
        DB::table('shop_tax_rates')->insert([
            'tax_id' => $taxId,
            'country_code' => 'TR',
            'state_code' => null,
            'city' => null,
            'postal_code' => null,
            'rate' => 20.00,
            'priority' => 1,
            'valid_from' => null,
            'valid_until' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Türkiye geneli %20 KDV oranı eklendi!');
    }
}
