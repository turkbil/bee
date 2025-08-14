<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class UniversalContentLengthPromptsSeeder extends Seeder
{
    /**
     * Universal Content Length Prompts - İçerik uzunluğu ayarları
     * UNIVERSAL-INPUT-SYSTEM-V3-PROFESSIONAL-ROADMAP.md uyumlu
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            return;
        }
        $contentLengthPrompts = [
            // 1. ÇOK KISA İÇERİK
            [
                'prompt_id' => 90011,
                'name' => 'Çok Kısa İçerik',
                'content' => 'İçeriği çok kısa tutun:
- Maksimum 50-75 kelime
- Sadece ana nokta
- Tek paragraf
- Doğrudan mesaj
- Gereksiz detay yok',
                'prompt_type' => 'content_length',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => false,
                'is_system' => false,
                'priority' => 5,
                'ai_weight' => 95,
            ],

            // 2. KISA İÇERİK
            [
                'prompt_id' => 90012,
                'name' => 'Kısa İçerik',
                'content' => 'İçeriği kısa ve öz tutun:
- 100-200 kelime arası
- 2-3 ana nokta
- Kısa paragraflar
- Özet bilgi
- Hızlı okunabilir',
                'prompt_type' => 'content_length',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => false,
                'is_system' => false,
                'priority' => 4,
                'ai_weight' => 90,
            ],

            // 3. NORMAL İÇERİK
            [
                'prompt_id' => 90013,
                'name' => 'Normal İçerik',
                'content' => 'İçeriği dengeli uzunlukta hazırla:
- 300-500 kelime arası
- 4-6 ana nokta
- Dengeli paragraflar
- Yeterli detay
- Kapsamlı bilgi',
                'prompt_type' => 'content_length',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => true,
                'is_system' => false,
                'priority' => 3,
                'ai_weight' => 85,
            ],

            // 4. UZUN İÇERİK
            [
                'prompt_id' => 90014,
                'name' => 'Uzun İçerik',
                'content' => 'İçeriği detaylı ve kapsamlı hazırla:
- 600-1000 kelime arası
- 7-10 ana nokta
- Detaylı açıklamalar
- Örnekler ekle
- Derinlemesine bilgi',
                'prompt_type' => 'content_length',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => false,
                'is_system' => false,
                'priority' => 2,
                'ai_weight' => 80,
            ],

            // 5. ÇOK UZUN/DETAYLI İÇERİK
            [
                'prompt_id' => 90015,
                'name' => 'Çok Detaylı İçerik',
                'content' => 'İçeriği çok detaylı ve kapsamlı hazırla:
- 1000+ kelime
- Tüm yönleriyle açıkla
- Bolca örnek ver
- Vaka analizleri ekle
- Uzman seviyesi bilgi
- Referanslar dahil et',
                'prompt_type' => 'content_length',
                'prompt_category' => 'response_format',
                'is_active' => true,
                'is_common' => false,
                'is_system' => false,
                'priority' => 1,
                'ai_weight' => 75,
            ]
        ];

        // Insert content length prompts
        foreach ($contentLengthPrompts as $prompt) {
            DB::table('ai_prompts')->updateOrInsert(
                ['prompt_id' => $prompt['prompt_id']],
                array_merge($prompt, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('✅ Universal Content Length Prompts seeder başarıyla tamamlandı.');
        $this->command->info('📊 Toplam ' . count($contentLengthPrompts) . ' içerik uzunluğu prompt\'u eklendi.');
    }
}