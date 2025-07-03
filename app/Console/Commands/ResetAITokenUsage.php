<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class ResetAITokenUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:reset-token-usage 
                            {--force : Onay istemeden işlemi gerçekleştir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AI token kullanım verilerini sıfırlar (satın alma verileri korunur)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('AI token kullanım verileri sıfırlanacak. Devam etmek istiyor musunuz?')) {
                $this->info('İşlem iptal edildi.');
                return Command::SUCCESS;
            }
        }

        $this->info('AI token kullanım verileri sıfırlanıyor...');

        try {
            DB::beginTransaction();

            // 1. ai_token_usage tablosunu temizle
            $usageCount = DB::table('ai_token_usage')->count();
            DB::table('ai_token_usage')->truncate();
            $this->info("✓ ai_token_usage tablosu temizlendi ({$usageCount} kayıt silindi)");

            // 2. Tenant'ların token bakiyelerini sıfırla
            $affectedTenants = Tenant::query()
                ->where(function($query) {
                    $query->whereNotNull('ai_tokens_used_this_month')
                          ->orWhereNotNull('ai_last_used_at');
                })
                ->count();

            Tenant::query()->update([
                'ai_tokens_used_this_month' => 0,
                'ai_last_used_at' => null
            ]);
            
            $this->info("✓ {$affectedTenants} tenant'ın kullanım verileri sıfırlandı");

            // 3. Token bakiyelerini yeniden hesapla (satın alımlara göre)
            $this->info('Token bakiyeleri yeniden hesaplanıyor...');
            
            $tenants = Tenant::all();
            foreach ($tenants as $tenant) {
                // Satın alınan toplam token miktarını hesapla
                $totalPurchased = DB::table('ai_token_purchases')
                    ->where('tenant_id', $tenant->id)
                    ->where('status', 'completed')
                    ->sum('amount');
                
                // Bakiyeyi güncelle
                $tenant->ai_tokens_balance = $totalPurchased;
                $tenant->save();
                
                $tenantName = $tenant->name ?: "Tenant #{$tenant->id}";
                $this->line("  - {$tenantName}: {$totalPurchased} token");
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ AI token kullanım verileri başarıyla sıfırlandı!');
            $this->table(
                ['Tablo', 'İşlem'],
                [
                    ['ai_token_usage', 'Temizlendi ✓'],
                    ['ai_token_purchases', 'Korundu ✓'],
                    ['ai_token_packages', 'Korundu ✓'],
                    ['tenants (bakiye)', 'Yeniden hesaplandı ✓'],
                    ['tenants (kullanım)', 'Sıfırlandı ✓']
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Hata oluştu: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}