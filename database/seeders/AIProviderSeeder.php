<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIProvider;
use App\Helpers\TenantHelpers;

class AIProviderSeeder extends Seeder
{
    public function run()
    {
        // AI Providers sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }

        // Mevcut provider'ları temizle
        AIProvider::truncate();
        
        // DeepSeek Provider
        AIProvider::create([
            'name' => 'deepseek',
            'display_name' => 'DeepSeek AI',
            'service_class' => 'DeepSeekService',
            'default_model' => 'deepseek-chat',
            'available_models' => ['deepseek-chat'],
            'default_settings' => [
                'temperature' => 0.7,
                'max_tokens' => 800,
                'top_p' => 0.9
            ],
            'base_url' => 'https://api.deepseek.com',
            'is_active' => true,
            'is_default' => true,
            'priority' => 1,
            'average_response_time' => 24000.0,
            'description' => 'DeepSeek AI - Mevcut sistem provider'
        ]);
        
        // OpenAI Provider
        AIProvider::create([
            'name' => 'openai',
            'display_name' => 'OpenAI',
            'service_class' => 'OpenAIService',
            'default_model' => 'gpt-4o-mini',
            'available_models' => ['gpt-4o-mini', 'gpt-4o', 'gpt-3.5-turbo'],
            'default_settings' => [
                'temperature' => 0.7,
                'max_tokens' => 800,
                'top_p' => 1.0
            ],
            'api_key' => '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',
            'base_url' => 'https://api.openai.com/v1',
            'is_active' => true,
            'is_default' => false,
            'priority' => 2,
            'average_response_time' => 1600.0,
            'description' => 'OpenAI GPT Models - Hızlı ve güvenilir'
        ]);
        
        // Claude Provider
        AIProvider::create([
            'name' => 'claude',
            'display_name' => 'Claude AI',
            'service_class' => 'ClaudeService',
            'default_model' => 'claude-3-haiku-20240307',
            'available_models' => ['claude-3-haiku-20240307', 'claude-3-sonnet-20240229'],
            'default_settings' => [
                'temperature' => 0.3,
                'max_tokens' => 800
            ],
            'api_key' => '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',
            'base_url' => 'https://api.anthropic.com/v1',
            'is_active' => true,
            'is_default' => false,
            'priority' => 3,
            'average_response_time' => 2700.0,
            'description' => 'Claude AI - Anthropic modeli'
        ]);
        
        $this->command->info('AI Providers seeded successfully!');
    }
}