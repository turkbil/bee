<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use Modules\AI\Database\Seeders\ComprehensiveSectorSeeder;
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
            
            // AI Provider'ları önce oluştur (diğer seeder'lar bunlara ihtiyaç duyabilir)
            $this->call(AIProviderSeeder::class);
            
            // Prompts artık ayrı seeder'da (AIPromptsSeeder.php)
            $this->call(AIPromptsSeeder::class);
            
            // AI ayarları bilgilendirme
            $this->createSettings();
            
            // Gizli özellikler seeder'ını çalıştır
            $this->call(AIHiddenFeaturesSeeder::class);
            
            // AI Features seeder'ını çalıştır (kategoriler otomatik kontrol edilir)
            $this->call(AIFeatureSeeder::class);
            
            // YENİ KREDİ SİSTEMİ - Token sistemi kaldırıldı, Credit sistemi kullanılıyor
            // AITokenPackageSeeder kaldırıldı - AICreditPackage sistemi kullanılıyor
            
            // AI Profil sistemi seeder'larını çalıştır - Kapsamlı 118+ sektör
            $this->call([
                // Tek comprehensive seeder (tüm sektörler hizmetleriyle beraber)
                ComprehensiveSectorSeeder::class,  // 118+ sektör (turizm, tarım, sanayi dahil)
                
                CleanAIProfileQuestionsSeeder::class,
                SectorCommonQuestionsSeeder::class,      // Ortak sorular (ID 5000-5999)
                CleanAITenantProfileSeeder::class,
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