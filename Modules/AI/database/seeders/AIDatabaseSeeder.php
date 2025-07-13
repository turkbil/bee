<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use Modules\AI\Database\Seeders\AIProfileSectorsCompleteSeeder;
use Modules\AI\Database\Seeders\AIProfileQuestionsSeeder;
use Modules\AI\Database\Seeders\AISectorSpecificQuestionsSeeder;

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
            
            // Prompts artık ayrı seeder'da (AIPromptsSeeder.php)
            $this->call(AIPromptsSeeder::class);
            
            // AI ayarlarını oluştur
            $this->createSettings();
            
            // Gizli özellikler seeder'ını çalıştır
            $this->call(AIHiddenFeaturesSeeder::class);
            
            // AI Features seeder'ını çalıştır (kategoriler otomatik kontrol edilir)
            $this->call(AIFeatureSeeder::class);
            
            // Token sistemini oluştur - SIRALA: packages -> purchases -> setup -> usage
            $this->call([
                AITokenPackageSeeder::class,
                AIPurchaseSeeder::class,
                AITenantSetupSeeder::class,
                AIUsageUpdateSeeder::class,
            ]);
            
            // AI Profil sistemi seeder'larını çalıştır - Sadece mevcut olanlar
            $this->call([
                AIProfileSectorsCompleteSeeder::class,
                AIProfileQuestionsSeeder::class,
                AISectorSpecificQuestionsSeeder::class,
            ]);
            
            // Seeder tamamlandıktan sonra da cache'leri temizle
            $this->clearAllAICache();
            
            $this->command->info('🔄 AI Cache temizlendi - Widget\'lar anlık veri çekecek!');
        } else {
            $this->command->info('Tenant contextinde çalışıyor, AI promptları central veritabanında saklanır.');
        }
    }

    /**
     * Varsayılan AI ayarlarını oluştur
     */
    private function createSettings(): void
    {
        // Önce tüm ayarları temizleme
        DB::table('ai_settings')->delete();

        // Ana tenant için API ayarlarını oluştur
        Setting::create([
            'api_key' => 'sk-2b5dd64c73b4429388c8de3055f0ba77',
            'model' => 'deepseek-chat',
            'max_tokens' => 8192,
            'temperature' => 0.7,
            'enabled' => true,
        ]);
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
            
            $this->command->info('🗑️ Tüm AI cache\'leri temizlendi (Token, Widget, Stats)');
            
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Cache temizleme sırasında hata: ' . $e->getMessage());
        }
    }
}