<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AICreditPackage;

class AICreditPackageSeeder extends Seeder
{
    /**
     * Kredi paketlerini seed et
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Başlangıç',
                'description' => 'Küçük projeler ve bireysel kullanım için ideal',
                'credit_amount' => 100,
                'price' => 400.00,
                'currency' => 'TRY',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    '100 kredi',
                    'Temel AI özellikleri',
                    'Email desteği',
                    '30 gün geçerlilik',
                    '~20-30 makale yazabilir'
                ]
            ],
            [
                'name' => 'Standart',
                'description' => 'Küçük işletmeler ve orta ölçekli projeler için',
                'credit_amount' => 500,
                'price' => 1800.00,
                'currency' => 'TRY',
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    '500 kredi',
                    'Tüm AI özellikleri',
                    'Öncelikli email desteği',
                    '60 gün geçerlilik',
                    '%25 bonus kredi',
                    '~100-150 makale yazabilir'
                ]
            ],
            [
                'name' => 'Premium',
                'description' => 'Büyük işletmeler ve yoğun kullanım için',
                'credit_amount' => 1500,
                'price' => 4800.00,
                'currency' => 'TRY',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    '1500 kredi',
                    'Tüm AI özellikleri',
                    'Canlı destek',
                    '90 gün geçerlilik',
                    '%50 bonus kredi',
                    'API erişimi',
                    '~300-450 makale yazabilir'
                ]
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Büyük organizasyonlar ve sınırsız kullanım için',
                'credit_amount' => 5000,
                'price' => 14000.00,
                'currency' => 'TRY',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
                'features' => [
                    '5000 kredi',
                    'Tüm AI özellikleri',
                    'Özel destek temsilcisi',
                    '120 gün geçerlilik',
                    '%100 bonus kredi',
                    'Özel API erişimi',
                    'Özel entegrasyonlar',
                    '~1000+ makale yazabilir'
                ]
            ]
        ];

        foreach ($packages as $package) {
            AICreditPackage::updateOrCreate(
                ['name' => $package['name']], 
                $package
            );
        }

        $this->command->info('AI Credit Packages seeded successfully!');
    }
}