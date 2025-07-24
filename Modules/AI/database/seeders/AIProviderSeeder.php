<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIProvider;
use App\Helpers\TenantHelpers;

class AIProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AI Providers sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            $this->command->info('AIProviderSeeder sadece central veritabanında çalışır, tenant\'ta atlanıyor...');
            return;
        }

        $this->command->info('AIProviderSeeder central veritabanında çalışıyor...');

        $providers = [
            [
                'name' => 'deepseek',
                'display_name' => 'DeepSeek',
                'service_class' => 'DeepSeekService',
                'default_model' => 'deepseek-chat',
                'available_models' => [
                    'deepseek-chat' => [
                        'name' => 'DeepSeek Chat',
                        'input_cost' => 0.07,
                        'output_cost' => 0.27
                    ],
                    'deepseek-reasoner' => [
                        'name' => 'DeepSeek Reasoner',
                        'input_cost' => 0.14,
                        'output_cost' => 0.95
                    ]
                ],
                'default_settings' => [
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                    'top_p' => 0.9
                ],
                'api_key' => 'sk-deepseek-placeholder-key-for-testing-purposes',
                'base_url' => 'https://api.deepseek.com',
                'is_active' => true,
                'is_default' => false,
                'priority' => 80,
                'average_response_time' => 0,
                'description' => 'DeepSeek AI - Yüksek performanslı AI modeli',
                'token_cost_multiplier' => 0.5000, // DeepSeek en ucuz
                'tokens_per_request_estimate' => 120,
                'cost_structure' => [
                    'chat' => ['input' => 0.07, 'output' => 0.27],
                    'reasoning' => ['input' => 0.14, 'output' => 0.95]
                ],
                'tracks_usage' => true
            ],
            [
                'name' => 'openai',
                'display_name' => 'OpenAI GPT',
                'service_class' => 'OpenAIService',
                'default_model' => 'gpt-4o-mini',
                'available_models' => [
                    'gpt-4o-mini' => [
                        'name' => 'GPT-4o Mini',
                        'input_cost' => 0.150,
                        'output_cost' => 0.600
                    ],
                    'gpt-4o' => [
                        'name' => 'GPT-4o',
                        'input_cost' => 2.50,
                        'output_cost' => 10.00
                    ],
                    'gpt-3.5-turbo' => [
                        'name' => 'GPT-3.5 Turbo',
                        'input_cost' => 0.50,
                        'output_cost' => 1.50
                    ]
                ],
                'default_settings' => [
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                    'top_p' => 0.9
                ],
                'api_key' => 'sk-Rd0uAFfpiAcfdxillkFM1mV0NWxihzz2L4ARj6k2tjT3BlbkFJ6V0IbyeIq53gOxZa31u1xOq94W69xoacMELOL7CIEA',
                'base_url' => 'https://api.openai.com/v1',
                'is_active' => true, // API key eklendiği için aktif
                'is_default' => true,
                'priority' => 100,
                'average_response_time' => 0,
                'description' => 'OpenAI GPT modelleri - Güçlü dil modeli',
                'token_cost_multiplier' => 1.0000, // OpenAI baseline
                'tokens_per_request_estimate' => 100,
                'cost_structure' => [
                    'gpt-4o-mini' => ['input' => 0.150, 'output' => 0.600],
                    'gpt-4o' => ['input' => 2.50, 'output' => 10.00],
                    'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50]
                ],
                'tracks_usage' => true
            ],
            [
                'name' => 'anthropic',
                'display_name' => 'Claude',
                'service_class' => 'AnthropicService',
                'default_model' => 'claude-3-haiku-20240307',
                'available_models' => [
                    'claude-3-haiku-20240307' => [
                        'name' => 'Claude 3 Haiku',
                        'input_cost' => 0.25,
                        'output_cost' => 1.25
                    ],
                    'claude-3-5-sonnet-20241022' => [
                        'name' => 'Claude 3.5 Sonnet',
                        'input_cost' => 3.00,
                        'output_cost' => 15.00
                    ]
                ],
                'default_settings' => [
                    'temperature' => 0.7,
                    'max_tokens' => 4000
                ],
                'api_key' => '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',
                'base_url' => 'https://api.anthropic.com',
                'is_active' => true, // API key eklendiği için aktif
                'is_default' => false,
                'priority' => 80,
                'average_response_time' => 0,
                'description' => 'Anthropic Claude - Güvenli ve akıllı AI asistan',
                'token_cost_multiplier' => 1.2000, // Claude biraz daha pahalı
                'tokens_per_request_estimate' => 90,
                'cost_structure' => [
                    'claude-3-haiku' => ['input' => 0.25, 'output' => 1.25],
                    'claude-3.5-sonnet' => ['input' => 3.00, 'output' => 15.00]
                ],
                'tracks_usage' => true
            ]
        ];

        foreach ($providers as $provider) {
            AIProvider::updateOrCreate(
                ['name' => $provider['name']],
                $provider
            );
        }

        $this->command->info('AI Provider\'lar başarıyla oluşturuldu!');
    }
}