<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIProvider;
use Modules\AI\App\Models\AIModelCreditRate;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;

class UpdatedModelCreditRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Güncel fiyat listesi dengeli markup ile hesaplanmış hali (Ağustos 2025)
     * Pahalı modeller: x2 markup, Orta segment: x3 markup, Ucuz modeller: x5-6 markup
     */
    public function run(): void
    {
        // AI Model Credit Rates sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }

        Log::info('🔥 Starting Updated Model-Based Credit Rates Seeder (Balanced Markup)');

        $this->seedAnthropicUpdatedRates();
        $this->seedDeepSeekUpdatedRates();
        $this->seedOpenAIUpdatedRates();

        Log::info('✅ Updated Model-Based Credit Rates Seeder completed');
    }

    private function seedAnthropicUpdatedRates(): void
    {
        $anthropic = AIProvider::where('name', 'anthropic')->first();
        if (!$anthropic) {
            Log::warning('Anthropic provider not found, skipping rates');
            return;
        }

        $rates = [
            // EN UCUZ CLAUDE MODELLER - Yüksek Markup (x6)
            [
                'model_name' => 'claude-3-haiku-20240307',
                'input_rate' => 1.5,   // $0.25 x6 (EN UCUZ!)
                'output_rate' => 7.5,  // $1.25 x6
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.001,
                'notes' => 'Claude 3 Haiku - En ekonomik seçenek',
                'is_default' => true // Anthropic için optimal seçim
            ],

            // ORTA SEGMENT CLAUDE MODELLER - Orta Markup (x3-4)
            [
                'model_name' => 'claude-haiku-3.5',
                'input_rate' => 3.2,   // $0.80 x4
                'output_rate' => 16.0,  // $4 x4
                'markup_percentage' => 300.0, // x4 markup
                'base_cost_per_request' => 0.002,
                'notes' => 'Claude Haiku 3.5 - Gelişmiş ekonomik model'
            ],
            [
                'model_name' => 'claude-3-5-sonnet-20241022',
                'input_rate' => 9.0,   // $3 x3
                'output_rate' => 45.0,  // $15 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.003,
                'notes' => 'Claude 3.5 Sonnet - Mevcut popüler model'
            ],
            [
                'model_name' => 'claude-sonnet-3.7',
                'input_rate' => 9.0,   // $3 x3
                'output_rate' => 45.0,  // $15 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.003,
                'notes' => 'Claude Sonnet 3.7 - Gelişmiş sürüm'
            ],
            [
                'model_name' => 'claude-sonnet-4',
                'input_rate' => 9.0,   // $3 x3
                'output_rate' => 45.0,  // $15 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.003,
                'notes' => 'Claude Sonnet 4 - En yeni Sonnet'
            ],

            // PREMIUM CLAUDE MODELLER - Düşük Markup (x2)
            [
                'model_name' => 'claude-3-opus-20240229',
                'input_rate' => 30.0,  // $15 x2
                'output_rate' => 150.0, // $75 x2
                'markup_percentage' => 100.0, // x2 markup
                'base_cost_per_request' => 0.008,
                'notes' => 'Claude 3 Opus - Legacy premium'
            ],
            [
                'model_name' => 'claude-opus-4',
                'input_rate' => 30.0,  // $15 x2
                'output_rate' => 150.0, // $75 x2
                'markup_percentage' => 100.0, // x2 markup
                'base_cost_per_request' => 0.008,
                'notes' => 'Claude Opus 4 - Premium model'
            ],
            [
                'model_name' => 'claude-opus-4.1',
                'input_rate' => 30.0,  // $15 x2
                'output_rate' => 150.0, // $75 x2
                'markup_percentage' => 100.0, // x2 markup
                'base_cost_per_request' => 0.008,
                'notes' => 'Claude Opus 4.1 - En gelişmiş model'
            ],
        ];

        foreach ($rates as $rate) {
            AIModelCreditRate::updateOrCreate(
                [
                    'provider_id' => $anthropic->id,
                    'model_name' => $rate['model_name']
                ],
                [
                    'credit_per_1k_input_tokens' => $rate['input_rate'],
                    'credit_per_1k_output_tokens' => $rate['output_rate'],
                    'markup_percentage' => $rate['markup_percentage'],
                    'base_cost_usd' => $rate['base_cost_per_request'],
                    'is_active' => true,
                    'is_default' => $rate['is_default'] ?? false
                ]
            );
        }

        Log::info('✅ Anthropic updated model rates seeded (8 models)');
    }

    private function seedDeepSeekUpdatedRates(): void
    {
        $deepseek = AIProvider::where('name', 'deepseek')->first();
        if (!$deepseek) {
            Log::warning('DeepSeek provider not found, skipping rates');
            return;
        }

        $rates = [
            // EN UCUZ DEEPSEEK MODELLER - Normal saat (x6 markup)
            [
                'model_name' => 'deepseek-chat',
                'input_rate' => 1.62,   // $0.27 x6 (Standard saat)
                'output_rate' => 6.60,  // $1.10 x6
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.0005,
                'notes' => 'DeepSeek Chat - En ucuz genel model (Standard saat)',
                'is_default' => true // DeepSeek için optimal seçim
            ],
            [
                'model_name' => 'deepseek-chat-discount',
                'input_rate' => 0.81,   // $0.135 x6 (Discount saat %50 off)
                'output_rate' => 3.30,  // $0.55 x6
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.0003,
                'notes' => 'DeepSeek Chat - İndirimli saat (%50 off UTC 16:30-00:30)'
            ],
            [
                'model_name' => 'deepseek-coder',
                'input_rate' => 1.62,   // deepseek-chat ile aynı fiyat
                'output_rate' => 6.60,
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.0005,
                'notes' => 'DeepSeek Coder - Kod geliştirme odaklı'
            ],

            // REASONING MODELLER - Orta Segment (x6 markup)
            [
                'model_name' => 'deepseek-reasoner',
                'input_rate' => 3.30,   // $0.55 x6 (Standard cache miss)
                'output_rate' => 13.14, // $2.19 x6
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.0007,
                'notes' => 'DeepSeek Reasoner - Mantıksal düşünme (Standard saat)'
            ],
            [
                'model_name' => 'deepseek-reasoner-discount',
                'input_rate' => 0.81,   // $0.135 x6 (Discount %75 off!)
                'output_rate' => 3.30,  // $0.55 x6 (Discount %75 off!)
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.0003,
                'notes' => 'DeepSeek Reasoner - İndirimli saat (%75 off UTC 16:30-00:30)'
            ],
        ];

        foreach ($rates as $rate) {
            AIModelCreditRate::updateOrCreate(
                [
                    'provider_id' => $deepseek->id,
                    'model_name' => $rate['model_name']
                ],
                [
                    'credit_per_1k_input_tokens' => $rate['input_rate'],
                    'credit_per_1k_output_tokens' => $rate['output_rate'],
                    'markup_percentage' => $rate['markup_percentage'],
                    'base_cost_usd' => $rate['base_cost_per_request'],
                    'is_active' => true,
                    'is_default' => $rate['is_default'] ?? false
                ]
            );
        }

        Log::info('✅ DeepSeek updated model rates seeded (3 models)');
    }

    private function seedOpenAIUpdatedRates(): void
    {
        $openai = AIProvider::where('name', 'openai')->first();
        if (!$openai) {
            Log::warning('OpenAI provider not found, skipping rates');
            return;
        }

        $rates = [
            // EN UCUZ MODELLER - Yüksek Markup (x6-8)
            [
                'model_name' => 'gpt-5-nano',
                'input_rate' => 0.3,     // $0.05 x6 (EN UCUZ!)
                'output_rate' => 2.4,    // $0.40 x6
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.001,
                'notes' => 'GPT-5 Nano - En ucuz ve hızlı model'
            ],
            [
                'model_name' => 'gpt-4.1-nano',
                'input_rate' => 0.6,     // $0.10 x6
                'output_rate' => 2.4,    // $0.40 x6
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.001,
                'notes' => 'GPT-4.1 Nano - Ucuz ve hızlı'
            ],
            [
                'model_name' => 'gpt-4o-mini',
                'input_rate' => 0.9,     // $0.15 x6
                'output_rate' => 3.6,    // $0.60 x6
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.002,
                'notes' => 'GPT-4o Mini - En popüler ekonomik model',
                'is_default' => true // OpenAI için optimal seçim
            ],

            // ORTA SEGMENT MODELLER - Orta Markup (x3-4)
            [
                'model_name' => 'gpt-5-mini',
                'input_rate' => 1.0,     // $0.25 x4
                'output_rate' => 8.0,    // $2.00 x4
                'markup_percentage' => 300.0, // x4 markup
                'base_cost_per_request' => 0.003,
                'notes' => 'GPT-5 Mini - Dengeli performans/fiyat'
            ],
            [
                'model_name' => 'gpt-4.1-mini',
                'input_rate' => 1.6,     // $0.40 x4
                'output_rate' => 6.4,    // $1.60 x4
                'markup_percentage' => 300.0, // x4 markup
                'base_cost_per_request' => 0.003,
                'notes' => 'GPT-4.1 Mini - Gelişmiş mini model'
            ],
            [
                'model_name' => 'o1-mini',
                'input_rate' => 4.4,     // $1.10 x4
                'output_rate' => 17.6,   // $4.40 x4
                'markup_percentage' => 300.0, // x4 markup
                'base_cost_per_request' => 0.005,
                'notes' => 'O1 Mini - Reasoning mini model'
            ],
            [
                'model_name' => 'o3-mini',
                'input_rate' => 4.4,     // $1.10 x4
                'output_rate' => 17.6,   // $4.40 x4
                'markup_percentage' => 300.0, // x4 markup
                'base_cost_per_request' => 0.005,
                'notes' => 'O3 Mini - Yeni reasoning mini'
            ],
            [
                'model_name' => 'o4-mini',
                'input_rate' => 4.4,     // $1.10 x4
                'output_rate' => 17.6,   // $4.40 x4
                'markup_percentage' => 300.0, // x4 markup
                'base_cost_per_request' => 0.005,
                'notes' => 'O4 Mini - En yeni reasoning mini'
            ],

            // PREMIUM MODELLER - Düşük Markup (x2-3)
            [
                'model_name' => 'gpt-5',
                'input_rate' => 3.75,    // $1.25 x3
                'output_rate' => 30.0,   // $10.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.006,
                'notes' => 'GPT-5 - En gelişmiş model'
            ],
            [
                'model_name' => 'gpt-5-chat-latest',
                'input_rate' => 3.75,    // $1.25 x3
                'output_rate' => 30.0,   // $10.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.006,
                'notes' => 'GPT-5 Chat Latest - En güncel'
            ],
            [
                'model_name' => 'gpt-4.1',
                'input_rate' => 6.0,     // $2.00 x3
                'output_rate' => 24.0,   // $8.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.005,
                'notes' => 'GPT-4.1 - Gelişmiş GPT-4'
            ],
            [
                'model_name' => 'gpt-4o',
                'input_rate' => 7.5,     // $2.50 x3
                'output_rate' => 30.0,   // $10.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.005,
                'notes' => 'GPT-4o - Mevcut en iyi GPT-4'
            ],
            [
                'model_name' => 'o3',
                'input_rate' => 6.0,     // $2.00 x3
                'output_rate' => 24.0,   // $8.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.008,
                'notes' => 'O3 - Yeni nesil reasoning'
            ],

            // ÇOK PAHALI MODELLER - Minimum Markup (x2)
            [
                'model_name' => 'o1',
                'input_rate' => 30.0,    // $15.00 x2
                'output_rate' => 120.0,  // $60.00 x2
                'markup_percentage' => 100.0, // x2 markup
                'base_cost_per_request' => 0.015,
                'notes' => 'O1 - En gelişmiş reasoning'
            ],
            [
                'model_name' => 'o3-pro',
                'input_rate' => 40.0,    // $20.00 x2
                'output_rate' => 160.0,  // $80.00 x2
                'markup_percentage' => 100.0, // x2 markup
                'base_cost_per_request' => 0.025,
                'notes' => 'O3 Pro - Professional O3'
            ],
            [
                'model_name' => 'o3-deep-research',
                'input_rate' => 20.0,    // $10.00 x2
                'output_rate' => 80.0,   // $40.00 x2
                'markup_percentage' => 100.0, // x2 markup
                'base_cost_per_request' => 0.015,
                'notes' => 'O3 Deep Research - Araştırma odaklı'
            ],
            [
                'model_name' => 'o1-pro',
                'input_rate' => 300.0,   // $150.00 x2
                'output_rate' => 1200.0, // $600.00 x2
                'markup_percentage' => 100.0, // x2 markup
                'base_cost_per_request' => 0.100,
                'notes' => 'O1 Pro - En pahalı professional model'
            ],

            // ÖZEL MODELLER
            [
                'model_name' => 'gpt-4o-2024-05-13',
                'input_rate' => 15.0,    // $5.00 x3
                'output_rate' => 45.0,   // $15.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.008,
                'notes' => 'GPT-4o Legacy - Eski sürüm'
            ],
            [
                'model_name' => 'gpt-4o-audio-preview',
                'input_rate' => 7.5,     // $2.50 x3
                'output_rate' => 30.0,   // $10.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.005,
                'notes' => 'GPT-4o Audio - Ses işleme'
            ],
            [
                'model_name' => 'gpt-4o-realtime-preview',
                'input_rate' => 15.0,    // $5.00 x3
                'output_rate' => 60.0,   // $20.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.010,
                'notes' => 'GPT-4o Realtime - Gerçek zamanlı'
            ],
            [
                'model_name' => 'computer-use-preview',
                'input_rate' => 9.0,     // $3.00 x3
                'output_rate' => 36.0,   // $12.00 x3
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.008,
                'notes' => 'Computer Use - Bilgisayar kontrolü'
            ],
            [
                'model_name' => 'gpt-image-1',
                'input_rate' => 15.0,    // $5.00 x3
                'output_rate' => 0.0,    // Image generation
                'markup_percentage' => 200.0, // x3 markup
                'base_cost_per_request' => 0.010,
                'notes' => 'GPT Image 1 - Görüntü üretimi'
            ],
            [
                'model_name' => 'text-embedding-ada-002',
                'input_rate' => 0.6,     // $0.1 x6
                'output_rate' => 0.0,    // Embedding
                'markup_percentage' => 500.0, // x6 markup
                'base_cost_per_request' => 0.0005,
                'notes' => 'Text Embeddings - Vektör üretimi'
            ],
        ];

        foreach ($rates as $rate) {
            AIModelCreditRate::updateOrCreate(
                [
                    'provider_id' => $openai->id,
                    'model_name' => $rate['model_name']
                ],
                [
                    'credit_per_1k_input_tokens' => $rate['input_rate'],
                    'credit_per_1k_output_tokens' => $rate['output_rate'],
                    'markup_percentage' => $rate['markup_percentage'],
                    'base_cost_usd' => $rate['base_cost_per_request'],
                    'is_active' => true,
                    'is_default' => $rate['is_default'] ?? false
                ]
            );
        }

        Log::info('✅ OpenAI updated model rates seeded (10 models)');
    }
}