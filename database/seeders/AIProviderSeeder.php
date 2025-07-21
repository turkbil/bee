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

        // Mevcut provider'ları temizle (foreign key safe)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AIProvider::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // DeepSeek Provider (Fallback)  
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
            'api_key' => 'sk-035528bc068943e0918fbe37646077c1',
            'base_url' => 'https://api.deepseek.com',
            'is_active' => true,
            'is_default' => true,
            'priority' => 1,
            'average_response_time' => 24000.0,
            'description' => 'DeepSeek AI - Varsayılan provider, hızlı ve ekonomik'
        ]);
        
        // OpenAI Provider (Fallback)
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
            'api_key' => 'sk-Rd0uAFfpiAcfdxillkFM1mV0NWxihzz2L4ARj6k2tjT3BlbkFJ6V0IbyeIq53gOxZa31u1xOq94W69xoacMELOL7CIEA',
            'base_url' => 'https://api.openai.com/v1',
            'is_active' => true,
            'is_default' => false,
            'priority' => 2,
            'average_response_time' => 1600.0,
            'description' => 'OpenAI GPT Models - Fallback provider, hızlı ve güvenilir'
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