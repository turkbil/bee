<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Modules\AI\App\Models\AITokenPurchase;
use Modules\AI\App\Models\AITokenUsage;
use App\Services\AITokenService;

class FixAITokenBalances extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:fix-token-balances 
                          {--tenant=* : Specific tenant IDs to fix}
                          {--dry-run : Only show what would be done}';

    /**
     * The console command description.
     */
    protected $description = 'Fix AI token balances for tenants based on completed purchases and usage';

    /**
     * Execute the console command.
     */
    public function handle(AITokenService $tokenService)
    {
        $isDryRun = $this->option('dry-run');
        $specificTenants = $this->option('tenant');
        
        $this->info('🔧 AI Token Bakiye Düzeltme İşlemi Başlatılıyor...');
        
        if ($isDryRun) {
            $this->warn('⚠️ DRY RUN MODE - Sadece rapor oluşturulacak, değişiklik yapılmayacak!');
        }
        
        // Tenant sorgusu
        $tenantsQuery = Tenant::query();
        if (!empty($specificTenants)) {
            $tenantsQuery->whereIn('id', $specificTenants);
        }
        
        $tenants = $tenantsQuery->get();
        
        if ($tenants->isEmpty()) {
            $this->error('❌ Hiçbir tenant bulunamadı!');
            return 1;
        }
        
        $this->info("📊 {$tenants->count()} tenant kontrol edilecek...");
        $this->newLine();
        
        $fixedCount = 0;
        $totalTokensFixed = 0;
        
        foreach ($tenants as $tenant) {
            $this->processtenant($tenant, $tokenService, $isDryRun, $fixedCount, $totalTokensFixed);
        }
        
        $this->newLine();
        $this->info("✅ İşlem tamamlandı!");
        $this->info("📈 Düzeltilen tenant sayısı: {$fixedCount}");
        $this->info("🎯 Toplam eklenen token: " . number_format($totalTokensFixed));
        
        return 0;
    }
    
    private function processtenant(Tenant $tenant, AITokenService $tokenService, bool $isDryRun, int &$fixedCount, int &$totalTokensFixed)
    {
        // Mevcut bakiye
        $currentBalance = $tenant->ai_tokens_balance ?? 0;
        
        // Toplam satın alınmış token (completed purchases)
        $totalPurchased = AITokenPurchase::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('token_amount');
        
        // Toplam kullanılmış token
        $totalUsed = AITokenUsage::where('tenant_id', $tenant->id)
            ->sum('tokens_used');
        
        // Hesaplanması gereken bakiye
        $expectedBalance = $totalPurchased - $totalUsed;
        $difference = $expectedBalance - $currentBalance;
        
        // Durum bilgisi
        $status = $difference == 0 ? '✅' : ($difference > 0 ? '🔧' : '⚠️');
        
        $this->line("$status Tenant {$tenant->id} ({$tenant->title}):");
        $this->line("   📦 Satın alınmış: " . number_format($totalPurchased) . " token");
        $this->line("   📊 Kullanılmış: " . number_format($totalUsed) . " token");
        $this->line("   💰 Mevcut bakiye: " . number_format($currentBalance) . " token");
        $this->line("   🎯 Hesaplanan bakiye: " . number_format($expectedBalance) . " token");
        
        if ($difference != 0) {
            $action = $difference > 0 ? 'EKLENMELİ' : 'ÇIKARILMALI';
            $this->line("   🔄 Düzeltme: " . number_format(abs($difference)) . " token $action");
            
            if (!$isDryRun) {
                // Bakiye düzeltme
                $tenant->update([
                    'ai_tokens_balance' => $expectedBalance,
                    'ai_enabled' => $totalPurchased > 0 ? true : $tenant->ai_enabled
                ]);
                
                $this->line("   ✅ Bakiye güncellendi!");
                $fixedCount++;
                $totalTokensFixed += abs($difference);
            } else {
                $this->line("   ⏸️ (DRY RUN - Gerçek günceleme yapılmadı)");
            }
        } else {
            $this->line("   ✅ Bakiye zaten doğru!");
        }
        
        $this->newLine();
    }
}