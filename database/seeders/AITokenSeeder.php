<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AITokenPackage;

class AITokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AI Token paketleri oluştur
        $packages = [
            [
                'name' => 'Başlangıç Paketi',
                'token_amount' => 1000,
                'price' => 29.99,
                'currency' => 'TRY',
                'description' => 'Küçük projeler için ideal başlangıç paketi',
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    '1.000 AI Token',
                    'Temel Chat Desteği',
                    'Haftalık Sınır: 250 Token'
                ]
            ],
            [
                'name' => 'Standart Paket',
                'token_amount' => 5000,
                'price' => 129.99,
                'currency' => 'TRY',
                'description' => 'Orta ölçekli projeler için en popüler paket',
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    '5.000 AI Token',
                    'Gelişmiş Chat Desteği',
                    'Haftalık Sınır: 1.250 Token',
                    'Öncelikli Destek'
                ]
            ],
            [
                'name' => 'Pro Paket',
                'token_amount' => 15000,
                'price' => 349.99,
                'currency' => 'TRY',
                'description' => 'Büyük projeler ve kurumsal kullanım için',
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    '15.000 AI Token',
                    'Tam AI Desteği',
                    'Haftalık Sınır: 3.750 Token',
                    'Premium Destek',
                    'API Erişimi'
                ]
            ],
            [
                'name' => 'Kurumsal Paket',
                'token_amount' => 50000,
                'price' => 999.99,
                'currency' => 'TRY',
                'description' => 'Kurumsal müşteriler için sınırsız kullanım',
                'is_active' => true,
                'sort_order' => 4,
                'features' => [
                    '50.000 AI Token',
                    'Sınırsız AI Desteği',
                    'Özel Destek Kanalı',
                    'API Erişimi',
                    'Özel Entegrasyonlar',
                    'SLA Garantisi'
                ]
            ],
            [
                'name' => 'Test Paketi',
                'token_amount' => 100,
                'price' => 9.99,
                'currency' => 'TRY',
                'description' => 'AI özelliklerini test etmek için',
                'is_active' => true,
                'sort_order' => 0,
                'features' => [
                    '100 AI Token',
                    'Temel Chat',
                    '7 Gün Geçerli'
                ]
            ]
        ];

        foreach ($packages as $packageData) {
            AITokenPackage::create($packageData);
        }

        $this->command->info('AI Token paketleri oluşturuldu.');
    }
}