<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'TRY',
                'symbol' => '₺',
                'name' => 'Turkish Lira',
                'name_translations' => json_encode([
                    'tr' => 'Türk Lirası',
                    'en' => 'Turkish Lira',
                ]),
                'exchange_rate' => 1.0000, // Base currency
                'is_active' => true,
                'is_default' => true,
                'decimal_places' => 2,
                'format' => 'symbol_after',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'US Dollar',
                'name_translations' => json_encode([
                    'tr' => 'Amerikan Doları',
                    'en' => 'US Dollar',
                ]),
                'exchange_rate' => 34.5000, // 1 USD = 34.5 TRY (güncel kurla güncellenmeli)
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'format' => 'symbol_before',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'name' => 'Euro',
                'name_translations' => json_encode([
                    'tr' => 'Euro',
                    'en' => 'Euro',
                ]),
                'exchange_rate' => 37.5000, // 1 EUR = 37.5 TRY (güncel kurla güncellenmeli)
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'format' => 'symbol_after',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($currencies as $currency) {
            \DB::connection('tenant')->table('shop_currencies')->updateOrInsert(
                ['code' => $currency['code']],
                $currency
            );
        }

        $this->command->info('✅ Shop currencies seeded successfully!');
    }
}
