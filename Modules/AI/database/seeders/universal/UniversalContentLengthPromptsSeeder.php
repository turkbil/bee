<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders\Universal;

use App\Helpers\TenantHelpers;
use Illuminate\Database\Seeder;
use Modules\AI\app\Models\Prompt;

/**
 * Universal Content Length Prompts Seeder
 * 
 * Tüm AI feature'larda kullanılabilir içerik uzunluğu prompt'larını oluşturur.
 * Bu prompt'lar input'lardan seçilebilir ve her feature'da kullanılabilir.
 */
class UniversalContentLengthPromptsSeeder extends Seeder
{
    /**
     * İçerik uzunluğu prompt ID aralığı: 11021-11040
     */
    private const CONTENT_LENGTH_ID_START = 11021;
    
    public function run(): void
    {
        $this->command->info('📏 Universal Content Length Prompts seeding başlıyor...');
        
        TenantHelpers::central(function() {
            $this->clearExistingPrompts();
            $this->createContentLengthPrompts();
        });
        
        $this->command->info('✅ Universal Content Length Prompts başarıyla oluşturuldu!');
    }
    
    private function clearExistingPrompts(): void
    {
        Prompt::whereBetween('prompt_id', [self::CONTENT_LENGTH_ID_START, self::CONTENT_LENGTH_ID_START + 19])->delete();
        $this->command->info('🧹 Mevcut content length prompts temizlendi');
    }
    
    private function createContentLengthPrompts(): void
    {
        $contentLengths = $this->getContentLengthPrompts();
        
        foreach ($contentLengths as $index => $length) {
            $promptId = self::CONTENT_LENGTH_ID_START + $index;
            
            Prompt::create([
                'id' => $promptId,
                'prompt_id' => $promptId,
                'name' => $length['name'],
                'content' => $length['content'],
                'prompt_type' => 'content_length',
                'prompt_category' => 'system_common',
                'priority' => 2, // Yüksek öncelik
                'ai_weight' => 70,
                'is_system' => true,
                'is_active' => true,
            ]);
            
            $this->command->info("📐 {$length['name']} içerik uzunluğu oluşturuldu (ID: {$promptId})");
        }
    }
    
    private function getContentLengthPrompts(): array
    {
        return [
            [
                'name' => 'Çok Kısa',
                'content' => 'Çok kısa ve özlü içerik üret. 30-80 kelime arası olsun. Ana fikri net şekilde ver. Gereksiz detaylara girmeden, temel mesajı vurgula. Hızlı okunabilir ve anlaşılır ol.',
            ],
            [
                'name' => 'Kısa',
                'content' => 'Kısa ve öz içerik üret. 80-200 kelime arası olsun. Ana mesajı detaylandır ama fazla uzatma. Okuyucu ilgisini canlı tut. Net ve etkili bir şekilde sonuçlandır.',
            ],
            [
                'name' => 'Normal',
                'content' => 'Normal uzunlukta dengeli içerik üret. 200-500 kelime arası olsun. Konuyu yeterince açıkla, örnekler ver. Okuyucu dostu ve kapsamlı ol. Dengeli yaklaşım sergile.',
            ],
            [
                'name' => 'Uzun',
                'content' => 'Kapsamlı ve ayrıntılı içerik üret. 500-1000 kelime arası olsun. Konuyu derinlemesine işle. Alt başlıklar kullan, örnekler ekle. Sistematik yaklaşım sergile. Detaylı açıklamalar sun.',
            ],
            [
                'name' => 'Detaylı',
                'content' => 'En detaylı ve kapsamlı içerik üret. 1000+ kelime olsun. Her açıdan ele al, alt konuları da işle. Örnekler, vaka çalışmaları ve ayrıntılı açıklamalar ekle. Uzman seviyesinde rehber niteliğinde ol.',
            ],
        ];
    }
}