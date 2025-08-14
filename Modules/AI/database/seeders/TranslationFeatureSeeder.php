<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

/**
 * 🌍 ÇEVİRİ FEATURE SEEDER - V3 UNIVERSAL INPUT SYSTEM
 * 
 * Bu seeder, Page modülü için toplu çeviri özelliği sağlar.
 * Tenant'ın dillerine göre dinamik çalışır.
 * 
 * FEATURE:
 * - Çok Dilli İçerik Çevirici (ID: 301) - Page modülü toplu çeviri
 * 
 * NASIL ÇALIŞIR:
 * 1. Page modülünde tüm dillerdeki içerikleri toplar
 * 2. Hedef dillere birebir çevirir
 * 3. JSON yapısını koruyarak kaydeder
 * 
 * ÖZELLİKLER:
 * - Tenant languages'dan dinamik dil seçimi
 * - JSON field yapısını koruma
 * - SEO settings çevirisi
 * - Toplu işlem desteği
 */
class TranslationFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            return;
        }

        $this->command->info('🌍 Çeviri Feature\'ı ekleniyor...');
        
        // Varolan çeviri feature'ını temizle
        $this->clearExistingTranslationFeatures();
        
        // Expert Prompts ekle
        $this->seedTranslationExpertPrompts();
        
        // Çeviri feature'ını oluştur
        $this->seedTranslationFeature();
        
        // Feature-Prompt Relations
        $this->seedFeaturePromptRelations();
        
        $this->command->info('✅ Çeviri Feature\'ı başarıyla eklendi!');
    }
    
    /**
     * Varolan çeviri feature'larını temizle
     */
    private function clearExistingTranslationFeatures(): void
    {
        AIFeature::where('id', 301)->delete();
        Prompt::whereIn('prompt_id', [20001, 20002, 20003])->delete();
        DB::table('ai_feature_prompt_relations')->where('feature_id', 301)->delete();
        
        $this->command->warn('🧹 Varolan çeviri feature\'ı temizlendi.');
    }
    
    /**
     * Translation Expert Prompts
     */
    private function seedTranslationExpertPrompts(): void
    {
        $expertPrompts = [
            [
                'prompt_id' => 20001,
                'name' => 'Profesyonel Çeviri Uzmanı',
                'content' => 'Sen uzman bir çevirmensin. Web içeriği, SEO metinleri ve pazarlama materyalleri çevirisi konusunda deneyimlisin.

## Çeviri Prensipleri:
- **Bağlamsal doğruluk** - Kelime kelime değil, anlam odaklı çeviri
- **Kültürel adaptasyon** - Hedef kitleye uygun ifadeler
- **SEO koruması** - Anahtar kelimeleri koruyarak çeviri
- **Marka tutarlılığı** - Marka ses tonunu koruma
- **Yerelleştirme** - Yerel dil özelliklerini kullanma

## Çeviri Kuralları:
1. HTML/Markdown formatlarını koru
2. URL slug\'ları hedef dile uygun oluştur
3. Meta açıklamaları karakter limitine uy
4. Özel isimleri çevirme
5. Teknik terimleri standart karşılıklarıyla kullan',
                'prompt_type' => 'feature',
                'prompt_category' => 'expert_knowledge',
                'priority' => 1,
                'ai_weight' => 90,
                'is_system' => false,
                'is_active' => true,
            ],
            [
                'prompt_id' => 20002,
                'name' => 'SEO Odaklı Çeviri Uzmanı',
                'content' => 'Sen SEO odaklı içerik çevirisi yapan bir uzmansın. Çevirilerin arama motorlarında başarılı olmasını sağlarsın.

## SEO Çeviri Stratejileri:
- **Anahtar kelime lokalizasyonu** - Hedef dilde arama hacmi yüksek kelimeler
- **Meta etiket optimizasyonu** - Title ve description karakter limitleri
- **URL yapısı** - SEO dostu slug oluşturma
- **İç bağlantı metinleri** - Anlamlı anchor text\'ler
- **Başlık hiyerarşisi** - H1-H6 yapısını koruma

## Teknik Detaylar:
- Title: Max 60 karakter
- Description: Max 155 karakter
- Keywords: Virgülle ayrılmış, hedef dilde popüler terimler
- Alt text: Görsel açıklamalarını hedef dile uyarla
- Schema markup: Yapısal veriyi koru',
                'prompt_type' => 'feature',
                'prompt_category' => 'expert_knowledge',
                'priority' => 2,
                'ai_weight' => 85,
                'is_system' => false,
                'is_active' => true,
            ],
            [
                'prompt_id' => 20003,
                'name' => 'Teknik İçerik Çevirmeni',
                'content' => 'Sen teknik dokümantasyon ve yazılım içeriği çeviren bir uzmansın. Kod örnekleri ve teknik terminolojiyi doğru aktarırsın.

## Teknik Çeviri Kuralları:
- **Kod blokları** - Yorumlar hariç değiştirme
- **API referansları** - Orijinal formatı koru
- **Değişken isimleri** - İngilizce bırak
- **Komut satırı** - Terminal komutlarını çevirme
- **Hata mesajları** - Standart çevirileri kullan

## Terminoloji Yönetimi:
1. Framework/library isimleri değişmez
2. Programlama terimleri standart karşılıklar
3. Versiyon numaraları aynen kalır
4. Dosya uzantıları değişmez
5. Teknik kısaltmalar açıklanır',
                'prompt_type' => 'feature',
                'prompt_category' => 'expert_knowledge',
                'priority' => 3,
                'ai_weight' => 80,
                'is_system' => false,
                'is_active' => true,
            ]
        ];
        
        foreach ($expertPrompts as $prompt) {
            Prompt::updateOrInsert(
                ['prompt_id' => $prompt['prompt_id']],
                array_merge($prompt, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        
        $this->command->info('🧠 3 Translation Expert Prompt eklendi.');
    }
    
    /**
     * Translation Feature
     */
    private function seedTranslationFeature(): void
    {
        $feature = AIFeature::create([
            'id' => 301,
            'ai_feature_category_id' => 3, // Translation category
            'name' => 'Çok Dilli İçerik Çevirici',
            'slug' => 'cok-dilli-icerik-cevirici',
            'description' => 'Page modülündeki içerikleri toplu olarak diğer dillere çevirir. Tenant\'ın aktif dillerini otomatik algılar.',
            'icon' => 'ti ti-language',
            'emoji' => '🌍',
            'quick_prompt' => 'Sen profesyonel bir çevirmensin. Verilen içeriği hedef dile birebir çevir. JSON yapısını koru, SEO ayarlarını da çevir.',
            'response_template' => json_encode([
                'format' => 'multilingual_json',
                'preserve_structure' => true,
                'fields' => [
                    'title' => 'translated',
                    'slug' => 'localized',
                    'body' => 'translated',
                    'excerpt' => 'translated',
                    'seo_title' => 'translated_60_char',
                    'seo_description' => 'translated_155_char',
                    'seo_keywords' => 'localized_keywords'
                ]
            ]),
            'helper_function' => 'ai_cevir_sayfa',
            'helper_examples' => json_encode([
                'basic' => 'ai_cevir_sayfa($pageId, "tr", ["en", "de"])',
                'with_options' => 'ai_cevir_sayfa($pageId, "tr", ["en"], ["preserve_format" => true])'
            ]),
            'module_type' => 'page',
            'category' => 'translation',
            'supported_modules' => json_encode(['page', 'portfolio', 'announcement', 'blog']),
            'status' => 'active',
            'response_format' => 'json',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->command->info('⚡ Çok Dilli İçerik Çevirici feature\'ı oluşturuldu (ID: 301)');
    }
    
    /**
     * Feature-Prompt Relations
     */
    private function seedFeaturePromptRelations(): void
    {
        $relations = [
            [
                'feature_id' => 301,
                'prompt_id' => 20001, // Profesyonel Çeviri Uzmanı
                'role' => 'primary',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'feature_id' => 301,
                'prompt_id' => 20002, // SEO Odaklı Çeviri Uzmanı
                'role' => 'supportive',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'feature_id' => 301,
                'prompt_id' => 20003, // Teknik İçerik Çevirmeni
                'role' => 'secondary',
                'priority' => 3,
                'is_active' => true,
            ],
            // Universal prompts bağlantıları
            [
                'feature_id' => 301,
                'prompt_id' => 90013, // Normal İçerik (Content Length)
                'role' => 'supportive',
                'priority' => 4,
                'is_active' => true,
            ]
        ];
        
        foreach ($relations as $relation) {
            DB::table('ai_feature_prompt_relations')->updateOrInsert(
                [
                    'feature_id' => $relation['feature_id'],
                    'prompt_id' => $relation['prompt_id'],
                ],
                array_merge($relation, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        
        $this->command->info('🔗 5 Feature-Prompt relation oluşturuldu.');
    }
}