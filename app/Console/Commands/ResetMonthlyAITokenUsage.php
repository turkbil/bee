<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AITokenService;
use App\Models\Tenant;

class ResetMonthlyAITokenUsage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:reset-monthly-usage 
                            {--tenant-id= : Reset specific tenant only}
                            {--dry-run : Show what would be reset without actually doing it}';

    /**
     * The console command description.
     */
    protected $description = 'Reset monthly AI token usage for all tenants or specific tenant';

    /**
     * The AI token service instance.
     */
    protected AITokenService $aiTokenService;

    /**
     * Create a new command instance.
     */
    public function __construct(AITokenService $aiTokenService)
    {
        parent::__construct();
        $this->aiTokenService = $aiTokenService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 AI Token Aylık Kullanım Sıfırlama Başlatılıyor...');

        $tenantId = $this->option('tenant-id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODU: Değişiklikler yapılmayacak, sadece önizleme gösterilecek.');
        }

        try {
            if ($tenantId) {
                // Specific tenant reset
                $tenant = Tenant::find($tenantId);
                
                if (!$tenant) {
                    $this->error("❌ Tenant ID {$tenantId} bulunamadı!");
                    return 1;
                }

                $this->resetTenant($tenant, $dryRun);
            } else {
                // All tenants reset
                $this->resetAllTenants($dryRun);
            }

            if (!$dryRun) {
                $this->info('✅ Aylık token kullanım sıfırlama tamamlandı!');
            } else {
                $this->info('ℹ️  Dry run tamamlandı. Gerçek sıfırlama için --dry-run parametresini kaldırın.');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Hata oluştu: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Reset specific tenant
     */
    protected function resetTenant(Tenant $tenant, bool $dryRun): void
    {
        $this->info("🔄 Kiracı işleniyor: {$tenant->title} (ID: {$tenant->id})");

        if (!$tenant->ai_enabled) {
            $this->warn("⚠️  AI kullanımı devre dışı, atlanıyor...");
            return;
        }

        $currentUsage = $tenant->ai_tokens_used_this_month;
        $lastReset = $tenant->ai_monthly_reset_at;

        $this->table(
            ['Alan', 'Değer'],
            [
                ['Mevcut Aylık Kullanım', number_format($currentUsage) . ' token'],
                ['Son Sıfırlama', $lastReset ? $lastReset->format('d.m.Y H:i:s') : 'Hiç'],
                ['Token Bakiyesi', number_format($tenant->ai_tokens_balance) . ' token'],
                ['Aylık Limit', $tenant->ai_monthly_token_limit > 0 ? number_format($tenant->ai_monthly_token_limit) . ' token' : 'Sınırsız'],
            ]
        );

        if ($currentUsage > 0) {
            if (!$dryRun) {
                $tenant->resetMonthlyTokenUsage();
                $this->info("✅ Aylık kullanım sıfırlandı: {$currentUsage} → 0 token");
            } else {
                $this->info("🔍 Sıfırlanacak: {$currentUsage} → 0 token");
            }
        } else {
            $this->info("ℹ️  Zaten sıfır, işlem gerekmiyor.");
        }
    }

    /**
     * Reset all tenants
     */
    protected function resetAllTenants(bool $dryRun): void
    {
        $tenantsToReset = Tenant::where('ai_enabled', true)
            ->where('ai_tokens_used_this_month', '>', 0)
            ->get();

        if ($tenantsToReset->isEmpty()) {
            $this->info('ℹ️  Sıfırlanacak tenant bulunamadı. Tüm tenantların aylık kullanımı zaten sıfır.');
            return;
        }

        $this->info("📊 Toplam {$tenantsToReset->count()} tenant sıfırlanacak:");

        $totalUsageToReset = $tenantsToReset->sum('ai_tokens_used_this_month');

        // Summary table
        $summaryData = [];
        foreach ($tenantsToReset as $tenant) {
            $summaryData[] = [
                $tenant->id,
                $tenant->title,
                number_format($tenant->ai_tokens_used_this_month),
                number_format($tenant->ai_tokens_balance),
                $tenant->ai_last_used_at ? $tenant->ai_last_used_at->format('d.m.Y') : 'Hiç'
            ];
        }

        $this->table(
            ['ID', 'Kiracı Adı', 'Bu Ay Kullanım', 'Mevcut Bakiye', 'Son Kullanım'],
            $summaryData
        );

        $this->info("📈 Toplam sıfırlanacak kullanım: " . number_format($totalUsageToReset) . " token");

        if (!$dryRun) {
            if ($this->confirm('Devam etmek istediğinizden emin misiniz?')) {
                $resetCount = $this->aiTokenService->resetMonthlyUsage();
                $this->info("✅ {$resetCount} tenant'ın aylık kullanımı sıfırlandı!");
            } else {
                $this->info('❌ İşlem iptal edildi.');
            }
        }
    }
}