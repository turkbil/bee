<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\TenantHelpers;

class AITenantSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('Bu seeder sadece central veritabanında çalışır.');
            return;
        }

        $now = Carbon::now();

        // Tüm tenant'ları al (central veritabanından)
        $allTenants = DB::table('tenants')->get();
        
        if ($allTenants->isEmpty()) {
            $this->command->warn('Hiç tenant bulunamadı! Önce tenant oluşturun.');
            return;
        }

        // Paketleri al
        $proPackage = DB::table('ai_token_packages')->where('name', 'Profesyonel')->first();
        $testPackage = DB::table('ai_token_packages')->where('name', 'Başlangıç')->first();
        
        if (!$proPackage || !$testPackage) {
            $this->command->error('Token paketleri bulunamadı! Önce AITokenPackageSeeder çalıştırın.');
            return;
        }

        // Mevcut kullanım verilerini temizle (purchases'a dokunma!)
        DB::table('ai_token_usage')->delete();

        foreach ($allTenants as $tenant) {
            // Tüm tenant'larda AI'yi aktif hale getir
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'ai_enabled' => true,
                    'ai_monthly_token_limit' => 0, // Sınırsız
                    'updated_at' => $now
                ]);

            // AI token bakiyelerini purchase verilerine göre güncelle
            $totalPurchases = DB::table('ai_token_purchases')
                ->where('tenant_id', $tenant->id)
                ->where('status', 'completed')
                ->sum('token_amount');
                
            if ($totalPurchases > 0) {
                // Tenant'a satın aldığı tokenlerden bir kısmını kullanmış gibi göster
                $usedTokens = $tenant->id == 1 ? rand(50000, 100000) : rand(20, 60);
                $usedTokens = min($usedTokens, $totalPurchases); // Satın alınanlardan fazla kullanılmamış olsun
                
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'ai_tokens_balance' => $totalPurchases - $usedTokens,
                        'ai_tokens_used_this_month' => min($usedTokens, rand(1000, 20000)),
                        'ai_last_used_at' => $now->subHours(rand(1, 168)),
                        'updated_at' => $now
                    ]);

                // Kullanım geçmişi oluştur
                $historyCount = $tenant->id == 1 ? rand(40, 60) : rand(5, 15);
                $this->createUsageHistory($tenant->id, $historyCount, $now);
            }
        }

        $this->command->info('AI Tenant ayarları tamamlandı:');
        $this->command->info('- Tüm tenant\'larda AI aktif hale getirildi');
        $this->command->info('- Token bakiyeleri purchase verilerine göre güncellendi');
        $this->command->info('- Kullanım geçmişleri oluşturuldu');
    }

    /**
     * Kullanım geçmişi oluştur
     */
    private function createUsageHistory(int $tenantId, int $usageCount, Carbon $now): void
    {
        $usageTypes = [
            'content_generation',
            'seo_analysis', 
            'text_summarization',
            'chat_completion',
            'code_generation',
            'translation',
            'text_optimization'
        ];
        
        $purposes = [
            'production',
            'content_creation',
            'analysis',
            'testing',
            'optimization',
            'research',
            'automation'
        ];

        for ($i = 0; $i < $usageCount; $i++) {
            $tokensUsed = $tenantId == 1 ? rand(100, 500) : rand(20, 80);
            $promptTokens = rand(5, intval($tokensUsed * 0.4)); // Prompt tokens max %40
            $completionTokens = $tokensUsed - $promptTokens;
            
            DB::table('ai_token_usage')->insert([
                'tenant_id' => $tenantId,
                'user_id' => 1,
                'tokens_used' => $tokensUsed,
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'usage_type' => $usageTypes[array_rand($usageTypes)],
                'model' => 'deepseek-chat',
                'purpose' => $purposes[array_rand($purposes)],
                'description' => $tenantId == 1 ? 
                    'Premium kullanıcı - yoğun AI kullanımı' : 
                    'Standart kullanıcı - test amaçlı kullanım',
                'reference_id' => null,
                'metadata' => json_encode([
                    'source' => 'seeder',
                    'tenant_id' => $tenantId,
                    'usage_pattern' => $tenantId == 1 ? 'heavy' : 'light'
                ]),
                'used_at' => $now->subHours(rand(1, 720)), // Son 30 gün içinde
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }
}