<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\TenantHelpers;

class AITokenPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('Bu seeder sadece central veritabanında çalışır.');
            return;
        }

        $now = Carbon::now();

        // Mevcut paketleri temizle (foreign key constraint nedeniyle delete kullanıyoruz)
        DB::table('ai_token_packages')->delete();

        // AI Token Paketleri - TokenHelper ile formatlanmış
        $packages = [
            [
                'name' => 'Başlangıç',
                'token_amount' => 1000,
                'price' => 5.00,
                'currency' => 'TRY',
                'description' => 'Günlük 50 token kullanım (20 gün)',
                'features' => json_encode([
                    'Temel AI asistan',
                    'Sınırsız soru-cevap',
                    '7/24 destek'
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Standart',
                'token_amount' => 5000,
                'price' => 20.00,
                'currency' => 'TRY',
                'description' => 'Günlük 250 token kullanım (20 gün)',
                'features' => json_encode([
                    'Gelişmiş AI asistan',
                    'Sınırsız soru-cevap',
                    'Dosya analizi',
                    'Çeviri desteği',
                    '7/24 destek'
                ]),
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Profesyonel',
                'token_amount' => 15000,
                'price' => 50.00,
                'currency' => 'TRY',
                'description' => 'Günlük 750 token kullanım (20 gün)',
                'features' => json_encode([
                    'Premium AI asistan',
                    'Sınırsız soru-cevap',
                    'Dosya analizi',
                    'Çeviri desteği',
                    'Kod üretimi',
                    'İçerik oluşturma',
                    'Öncelikli destek'
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Kurumsal',
                'token_amount' => 50000,
                'price' => 150.00,
                'currency' => 'TRY',
                'description' => 'Günlük 2.5K token kullanım (20 gün)',
                'features' => json_encode([
                    'Enterprise AI asistan',
                    'Sınırsız kullanım',
                    'Toplu dosya işleme',
                    'API erişimi',
                    'Özel model desteği',
                    'Raporlama',
                    'Özel destek'
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Unlimited',
                'token_amount' => 100000,
                'price' => 250.00,
                'currency' => 'TRY',
                'description' => 'Günlük 5K token kullanım (20 gün)',
                'features' => json_encode([
                    'Unlimited AI asistan',
                    'Sınırsız kullanım',
                    'Toplu dosya işleme',
                    'Full API erişimi',
                    'Özel model eğitimi',
                    'Analitik dashboard',
                    'Dedicated destek',
                    'White-label çözüm'
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('ai_token_packages')->insert($packages);

        $this->command->info('✅ AI Token paketleri başarıyla oluşturuldu!');
        foreach ($packages as $package) {
            $formattedTokens = \App\Helpers\TokenHelper::format($package['token_amount']);
            $this->command->info("📦 {$package['name']}: {$formattedTokens} token - {$package['price']} {$package['currency']}");
        }
    }
}