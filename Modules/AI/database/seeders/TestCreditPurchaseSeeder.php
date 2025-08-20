<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AICreditPackage;
use Modules\AI\App\Models\AICreditPurchase;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;

class TestCreditPurchaseSeeder extends Seeder
{
    /**
     * Test kredi satın alma işlemleri
     */
    public function run(): void
    {
        // Sadece central database'de çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }

        Log::info('🛒 Test Credit Purchase Seeder başlıyor...');

        // Paketleri al
        $smallestPackage = AICreditPackage::orderBy('credit_amount')->first();
        $largestPackage = AICreditPackage::orderBy('credit_amount', 'desc')->first();

        if (!$smallestPackage || !$largestPackage) {
            Log::error('Kredi paketleri bulunamadı!');
            return;
        }

        Log::info("En küçük paket: {$smallestPackage->name} ({$smallestPackage->credit_amount} kredi)");
        Log::info("En büyük paket: {$largestPackage->name} ({$largestPackage->credit_amount} kredi)");

        // Tenant 1'e 5 adet Enterprise paketi
        $this->addPurchasesToTenant(1, $largestPackage, 5);

        // Diğer tenant'lara 1'er adet Başlangıç paketi
        for ($tenantId = 2; $tenantId <= 4; $tenantId++) {
            $this->addPurchasesToTenant($tenantId, $smallestPackage, 1);
        }

        Log::info('✅ Test Credit Purchase Seeder tamamlandı');
    }

    private function addPurchasesToTenant(int $tenantId, AICreditPackage $package, int $count): void
    {
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            Log::warning("Tenant {$tenantId} bulunamadı!");
            return;
        }

        $totalCredits = 0;
        
        for ($i = 1; $i <= $count; $i++) {
            // Satın alma kaydı oluştur
            $purchase = AICreditPurchase::create([
                'tenant_id' => $tenantId,
                'package_id' => $package->id,
                'credit_amount' => $package->credit_amount,
                'price_paid' => $package->price,
                'currency' => $package->currency,
                'status' => 'completed',
                'payment_method' => 'admin_grant',
                'purchased_at' => now(),
                'notes' => "Test için eklendi - {$package->name} #{$i}"
            ]);

            $totalCredits += $package->credit_amount;
            
            Log::info("Purchase ID {$purchase->id} oluşturuldu: Tenant {$tenantId} - {$package->name} #{$i}");
        }

        // Tenant'ın kredi bakiyesini güncelle - update methodu kullan
        $oldBalance = $tenant->ai_credits_balance;
        $newBalance = $oldBalance + $totalCredits;
        
        $tenant->update([
            'ai_credits_balance' => $newBalance
        ]);

        // Kontrol et
        $updatedTenant = Tenant::find($tenantId);
        Log::info("Tenant {$tenantId} kredi güncellendi: {$oldBalance} → {$updatedTenant->ai_credits_balance} (+{$totalCredits})");
    }
}