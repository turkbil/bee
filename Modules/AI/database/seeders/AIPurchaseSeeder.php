<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Tenant;
use Modules\AI\App\Models\AITokenPackage;
use Modules\AI\App\Models\AITokenPurchase;
use App\Helpers\TenantHelpers;
use App\Services\AITokenService;

class AIPurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            // $this->command->info('Bu seeder sadece central veritabanında çalışır.');
            return;
        }

        // Mevcut satın almaları temizle
        DB::table('ai_token_purchases')->delete();

        // Tenant'ları al
        $tenants = Tenant::whereIn('id', [1, 2, 3, 4])->get();
        
        if ($tenants->isEmpty()) {
            $this->command->error('Test için gerekli tenant\'lar bulunamadı (ID: 1,2,3,4)');
            return;
        }

        // Token paketlerini al
        $smallestPackage = DB::table('ai_token_packages')->where('name', 'Başlangıç')->first();
        $largestPackage = DB::table('ai_token_packages')->where('name', 'Unlimited')->first();

        if (!$smallestPackage || !$largestPackage) {
            $this->command->error('Token paketleri bulunamadı. Önce AITokenPackageSeeder\'ı çalıştırın.');
            return;
        }

        $now = Carbon::now();
        $purchases = [];

        foreach ($tenants as $tenant) {
            if ($tenant->id == 1) {
                // Tenant 1: En yüksek paketten (Unlimited) 5 adet
                for ($i = 1; $i <= 5; $i++) {
                    $purchases[] = [
                        'tenant_id' => $tenant->id,
                        'user_id' => null,
                        'package_id' => $largestPackage->id,
                        'token_amount' => $largestPackage->token_amount,
                        'price_paid' => $largestPackage->price,
                        'amount' => $largestPackage->price,
                        'currency' => $largestPackage->currency,
                        'status' => 'completed',
                        'payment_method' => 'credit_card',
                        'payment_transaction_id' => 'TXN_' . $tenant->id . '_' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'payment_data' => json_encode(['gateway' => 'test', 'reference' => 'TEST_REF_' . $i]),
                        'notes' => 'Unlimited paketi satın alımı',
                        'purchased_at' => $now->copy()->subDays(rand(1, 30)),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            } else {
                // Tenant 2,3,4: En küçük paketten (Başlangıç) 1'er adet
                $purchases[] = [
                    'tenant_id' => $tenant->id,
                    'user_id' => null,
                    'package_id' => $smallestPackage->id,
                    'token_amount' => $smallestPackage->token_amount,
                    'price_paid' => $smallestPackage->price,
                    'amount' => $smallestPackage->price,
                    'currency' => $smallestPackage->currency,
                    'status' => 'completed',
                    'payment_method' => 'credit_card',
                    'payment_transaction_id' => 'TXN_' . $tenant->id . '_001',
                    'payment_data' => json_encode(['gateway' => 'test', 'reference' => 'TEST_REF_' . $tenant->id]),
                    'notes' => 'Başlangıç paketi satın alımı',
                    'purchased_at' => $now->copy()->subDays(rand(1, 15)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Purchases'ı direkt database'e insert et
        DB::table('ai_token_purchases')->insert($purchases);
        $createdPurchases = $purchases;

        // $this->command->info('✅ AI Token satın alma verileri başarıyla oluşturuldu!');
        // $this->command->info("🎯 Tenant 1: Unlimited paketi x5 (" . ($largestPackage->token_amount * 5) . " token)");
        // $this->command->info("🧪 Tenant 2,3,4: Başlangıç paketi x1 (" . $smallestPackage->token_amount . " token her biri)");
        // $this->command->info("📊 Toplam " . count($createdPurchases) . " satın alma kaydı oluşturuldu.");
        // $this->command->info("💰 Token bakiyeleri otomatik olarak güncellendi!");
    }
}