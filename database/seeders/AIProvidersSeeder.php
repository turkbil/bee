<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIProvider;
use App\Helpers\TenantHelpers;

class AIProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AI Providers sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }

        // OpenAI Provider - 1. sıra (varsayılan)
        AIProvider::create([
            'name' => 'openai',
            'display_name' => 'OpenAI',
            'service_class' => 'OpenAIService',
            'default_model' => 'gpt-4o-mini',
            'available_models' => [
                'gpt-4o-mini',
                'gpt-4o',
                'gpt-4-turbo',
                'gpt-4',
                'gpt-3.5-turbo',
                'gpt-3.5-turbo-16k'
            ],
            'default_settings' => [
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0
            ],
            'api_key' => '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',
            'base_url' => 'https://api.openai.com/v1',
            'is_active' => true,
            'is_default' => true, // 1. sıra - varsayılan provider
            'priority' => 100, // En yüksek öncelik
            'average_response_time' => 1591.37,
            'description' => 'OpenAI GPT modelleri - 1. tercih (en hızlı yanıt süresi 1.6s)'
        ]);

        // Claude Provider - 2. sıra  
        AIProvider::create([
            'name' => 'claude',
            'display_name' => 'Claude (Anthropic)',
            'service_class' => 'ClaudeService',
            'default_model' => 'claude-3-haiku-20240307',
            'available_models' => [
                'claude-3-haiku-20240307',
                'claude-3-sonnet-20240229',
                'claude-3-opus-20240229',
                'claude-3-5-sonnet-20240620'
            ],
            'default_settings' => [
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'top_p' => 1,
                'top_k' => 40
            ],
            'api_key' => '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',
            'base_url' => 'https://api.anthropic.com/v1',
            'is_active' => true,
            'is_default' => false, // 2. sıra
            'priority' => 90, // İkinci öncelik
            'average_response_time' => 2660.77,
            'description' => 'Claude AI - 2. tercih (yanıt süresi 2.7s)'
        ]);

        // DeepSeek Provider - 3. sıra
        AIProvider::create([
            'name' => 'deepseek',
            'display_name' => 'DeepSeek',
            'service_class' => 'DeepSeekService',
            'default_model' => 'deepseek-chat',
            'available_models' => [
                'deepseek-chat',
                'deepseek-coder'
            ],
            'default_settings' => [
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0
            ],
            'api_key' => null, // Mevcut sistemden alınacak (ai_get_api_key())
            'base_url' => 'https://api.deepseek.com/v1',
            'is_active' => true,
            'is_default' => false, // 3. sıra
            'priority' => 70, // Üçüncü öncelik
            'average_response_time' => 23995.57,
            'description' => 'DeepSeek AI - 3. tercih (yavaş ama ekonomik 24s)'
        ]);

        echo "✅ AI Provider'ları sırayla eklendi:\n";
        echo "1. OpenAI (varsayılan, priority: 100, 1.6s)\n";
        echo "2. Claude (priority: 90, 2.7s)\n";
        echo "3. DeepSeek (priority: 70, 24s)\n";
    }
}