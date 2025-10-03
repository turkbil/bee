<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIProviderModel;
use Illuminate\Support\Facades\Log;

/**
 * AI Provider Models Operation Rates Seeder
 *
 * Hybrid kredi sistemi için operation-based fiyatlandırma ekler.
 *
 * FİYATLANDIRMA MANTIĞI:
 * - Chat: Token bazlı, %83 indirimli (sohbet ucuz olmalı)
 * - SEO: Sabit 1 kredi (öneri amaçlı, hızlı)
 * - Çeviri: Tier sistemi (kaliteli çeviri, makul fiyat)
 * - İçerik: Tier sistemi (taslak üretimi, kullanıcı düzenler)
 * - PDF: Tier sistemi (özet/analiz, manuel okumadan hızlı)
 *
 * NOT: AI sonuçları %100 doğru değil, kullanıcı kontrol/düzenleme yapar.
 */
class AIProviderModelsOperationRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder sadece central database'de çalışmalı
        if (!\App\Helpers\TenantHelpers::isCentral()) {
            Log::info('⏭️ AIProviderModelsOperationRatesSeeder sadece central database\'de çalışır, atlanıyor.');
            return;
        }

        Log::info('🎯 AI Provider Models Operation Rates Seeder başlatıldı');

        // Tüm aktif modeller için operation_rates ekle
        $models = AIProviderModel::where('is_active', true)->get();

        if ($models->isEmpty()) {
            Log::warning('⚠️ Aktif AI model bulunamadı. Önce AIProviderModelsSeeder çalıştırın.');
            $this->command->warn('⚠️ No active AI models found. Run AIProviderModelsSeeder first.');
            return;
        }

        $operationRates = $this->getOperationRates();

        $updated = 0;
        foreach ($models as $model) {
            // Model spesifik ayarlar (opsiyonel)
            $modelSpecificRates = $this->getModelSpecificRates($model, $operationRates);

            $model->update([
                'operation_rates' => $modelSpecificRates
            ]);

            $updated++;

            Log::info("✅ Operation rates eklendi: {$model->provider->name} - {$model->model_name}");
        }

        $this->command->info("✅ {$updated} model için operation rates eklendi.");
        Log::info("🎉 Operation rates seeder tamamlandı. {$updated} model güncellendi.");
    }

    /**
     * Varsayılan operation rates yapısı
     */
    private function getOperationRates(): array
    {
        return [
            'chat' => [
                'type' => 'token_multiplier',
                'multiplier' => 0.17,
                'min_credits' => 0.01,
                'description' => 'Chat - token bazlı %83 indirimli (sohbet ucuz olmalı)'
            ],

            'seo_recommendations' => [
                'type' => 'fixed',
                'amount' => 1,
                'description' => 'SEO önerileri - sabit ücret (kullanıcı önerileri düzenler)'
            ],

            'translation' => [
                'type' => 'tier',
                'tiers' => [
                    [
                        'max_tokens' => 2000,
                        'credits' => 2,
                        'description' => 'Kısa metin (~500 kelime)'
                    ],
                    [
                        'max_tokens' => 6000,
                        'credits' => 4,
                        'description' => 'Orta metin (~1500 kelime)'
                    ],
                    [
                        'max_tokens' => 20000,
                        'credits' => 8,
                        'description' => 'Uzun metin (~5000 kelime)'
                    ]
                ],
                'description' => 'Çeviri - token bazlı paketli fiyat (kaliteli çeviri)'
            ],

            'content_generation' => [
                'type' => 'word_tier',
                'tiers' => [
                    [
                        'max_words' => 200,
                        'credits' => 2,
                        'description' => 'Çok kısa içerik'
                    ],
                    [
                        'max_words' => 500,
                        'credits' => 3,
                        'description' => 'Kısa makale'
                    ],
                    [
                        'max_words' => 1000,
                        'credits' => 6,
                        'description' => 'Orta makale'
                    ],
                    [
                        'max_words' => 2000,
                        'credits' => 10,
                        'description' => 'Uzun makale'
                    ],
                    [
                        'max_words' => 5000,
                        'credits' => 20,
                        'description' => 'Çok uzun içerik'
                    ]
                ],
                'description' => 'İçerik üretimi - kelime bazlı paketli (taslak üretimi)'
            ],

            'pdf_analysis' => [
                'type' => 'page_tier',
                'tiers' => [
                    [
                        'max_pages' => 5,
                        'credits' => 5,
                        'description' => '1-5 sayfa'
                    ],
                    [
                        'max_pages' => 10,
                        'credits' => 8,
                        'description' => '6-10 sayfa'
                    ],
                    [
                        'max_pages' => 20,
                        'credits' => 15,
                        'description' => '11-20 sayfa'
                    ],
                    [
                        'max_pages' => 50,
                        'credits' => 30,
                        'description' => '21-50 sayfa'
                    ],
                    [
                        'max_pages' => 100,
                        'credits' => 50,
                        'description' => '51-100 sayfa (maksimum)'
                    ]
                ],
                'description' => 'PDF analizi - sayfa bazlı paketli (özet/analiz)'
            ],

            'default' => [
                'type' => 'token_full',
                'multiplier' => 1.0,
                'description' => 'Varsayılan - standart token hesaplama'
            ]
        ];
    }

    /**
     * Model spesifik rate ayarları (opsiyonel)
     *
     * Bazı modeller için farklı fiyatlandırma yapılabilir
     */
    private function getModelSpecificRates(AIProviderModel $model, array $defaultRates): array
    {
        $rates = $defaultRates;

        // Örnek: GPT-4o-mini için chat daha ucuz olabilir
        if (str_contains(strtolower($model->model_name), 'mini')) {
            $rates['chat']['multiplier'] = 0.15; // Daha fazla indirim
            $rates['chat']['description'] .= ' (mini model - ekstra indirimli)';
        }

        // Örnek: Claude modelleri için içerik üretimi biraz daha pahalı (daha kaliteli)
        if (str_contains(strtolower($model->provider->name), 'anthropic')) {
            foreach ($rates['content_generation']['tiers'] as &$tier) {
                $tier['credits'] = (int) ceil($tier['credits'] * 1.2); // %20 daha pahalı
            }
            $rates['content_generation']['description'] .= ' (Claude - premium kalite)';
        }

        return $rates;
    }
}
