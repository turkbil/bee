<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use Modules\AI\Database\Seeders\CleanAIProfileQuestionsSeeder;
use Modules\AI\Database\Seeders\CleanAITenantProfileSeeder;

class AIDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (TenantHelpers::isCentral()) {
            // ÖNEMLİ: Seeder başlamadan önce tüm AI cache'leri temizle
            $this->clearAllAICache();
            
            // Sadece temel provider'ları koru
            $this->call(AIProviderSeeder::class);
            
            // 🔥 GÜNCEL MODEL KREDİ ORANLARI (x5 Markup - Ağustos 2025)
            $this->call(UpdatedModelCreditRatesSeeder::class);
            
            // AI ayarları bilgilendirme
            $this->createSettings();
            
            // AI Feature Kategorilerini ekle (18 kategori)
            $this->call(AIFeatureCategoriesSeeder::class);
            
            // 🎯 SADELEŞEN AI FEATURE SİSTEMİ V3 - TEK BLOG FEATURE (10.08.2025)
            // Universal Input System V3 uyumlu sadeleştirilmiş seeder sistemi
            
            // 🎯 SYSTEM PROMPTS (Ortak Özellikler, Gizli Özellikler, Şartlı Yanıtlar)
            $this->call(\Modules\AI\Database\Seeders\AISystemPromptsSeeder::class);
            
            // 🎯 UNIVERSAL SYSTEM PROMPTS (Tüm AI feature'larda kullanılabilir)
            $this->call(\Modules\AI\Database\Seeders\UniversalContentLengthPromptsSeeder::class);
            $this->call(\Modules\AI\Database\Seeders\UniversalWritingTonePromptsSeeder::class);
            
            // Modern Blog Content Seeder (All phases in one) - ÖNCELİKLE FEATURE'LARI OLUŞTUR
            $this->call(\Modules\AI\Database\Seeders\ModernBlogContentSeeder::class);
            
            // Translation Feature Seeder (Page modülü toplu çeviri)
            $this->call(\Modules\AI\Database\Seeders\TranslationFeatureSeeder::class);
            
            // SEO Advanced Input System Seeder (SEO expert prompts)
            $this->call(\Modules\AI\Database\Seeders\SeoAdvancedInputSystemSeeder::class);
            
            // 🚀 ENTERPRISE SEO PROMPT SYSTEM V5.0
            $this->call(\Modules\AI\Database\Seeders\SeoEnterprisePromptSeeder::class);
            
            // 🎯 UNIVERSAL INPUT SYSTEM V3 - SEEDER'LAR (Feature'lar oluşturulduktan SONRA)
            $this->call(\Modules\AI\Database\Seeders\BlogWriterUniversalInputSeeder::class);
            $this->call(\Modules\AI\Database\Seeders\TranslationUniversalInputSeeder::class);
            
            // Sadece temel AI profil seeder'larını çalıştır
            $this->call([
                CleanAIProfileQuestionsSeeder::class,
                SectorCommonQuestionsSeeder::class,
                CleanAITenantProfileSeeder::class,
                TestCreditPurchaseSeeder::class,
            ]);
            
            // Seeder tamamlandıktan sonra da cache'leri temizle
            $this->clearAllAICache();
            
            // $this->command->info('🔄 AI Cache temizlendi - Widget\'lar anlık veri çekecek!');
        } else {
            // $this->command->info('Tenant contextinde çalışıyor, AI promptları central veritabanında saklanır.');
        }
    }

    /**
     * Varsayılan AI ayarları artık config/ai.php'de
     */
    private function createSettings(): void
    {
        // AI ayarları artık config/ai.php dosyasında
        // Global settings config-based, provider-specific settings ai_providers tablosunda
        // $this->command->info('AI ayarları config/ai.php dosyasından yönetiliyor.');
    }

    /**
     * Tüm AI ile ilgili cache'leri temizle
     */
    private function clearAllAICache(): void
    {
        try {
            // Tüm tenant'lar için cache'leri temizle
            $tenantIds = DB::table('tenants')->pluck('id')->toArray();
            $tenantIds[] = 'default'; // Default tenant için
            
            foreach ($tenantIds as $tenantId) {
                // Token cache'leri
                Cache::forget("ai_token_balance_{$tenantId}");
                Cache::forget("ai_total_purchased_{$tenantId}");
                Cache::forget("ai_total_used_{$tenantId}");
                Cache::forget("ai_token_stats_{$tenantId}");
                Cache::forget("ai_widget_stats_{$tenantId}");
                
                // Widget cache'leri
                Cache::forget("ai_widget_data_{$tenantId}");
                Cache::forget("ai_statistics_{$tenantId}");
                Cache::forget("ai_usage_stats_{$tenantId}");
                Cache::forget("ai_monthly_usage_{$tenantId}");
                Cache::forget("ai_daily_usage_{$tenantId}");
                
                // Diğer AI cache'leri
                Cache::forget("ai_features_{$tenantId}");
                Cache::forget("ai_prompts_{$tenantId}");
                Cache::forget("ai_packages_{$tenantId}");
            }
            
            // Global AI cache'leri
            Cache::forget('ai_global_stats');
            Cache::forget('ai_system_prompts');
            Cache::forget('ai_token_packages');
            Cache::forget('ai_features_list');
            
            // Cache pattern'leri ile temizleme
            $cachePatterns = [
                'ai_*',
                'token_*', 
                'widget_*',
                'stats_*'
            ];
            
            foreach ($cachePatterns as $pattern) {
                if (method_exists(Cache::store(), 'flush')) {
                    // Redis cache için pattern silme
                    $keys = Cache::store()->connection()->keys($pattern);
                    if (!empty($keys)) {
                        Cache::store()->connection()->del($keys);
                    }
                }
            }
            
            // $this->command->info('🗑️ Tüm AI cache\'leri temizlendi (Token, Widget, Stats)');
            
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Cache temizleme sırasında hata: ' . $e->getMessage());
        }
    }
}