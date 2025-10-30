<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopCurrency;
use Modules\Shop\App\Services\TcmbExchangeRateService;

class UpdateCurrencyRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update-rates {--force : Force update all currencies, ignore auto_update flag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rates from TCMB (only auto_update currencies)';

    /**
     * Execute the console command.
     */
    public function handle(TcmbExchangeRateService $tcmbService): int
    {
        $this->info('🔄 Fetching exchange rates from TCMB...');

        $result = $tcmbService->fetchRates();

        if (!$result['success']) {
            $this->error('❌ Failed to fetch rates: ' . $result['message']);
            return self::FAILURE;
        }

        $tcmbRates = $result['rates'];
        $this->info('✅ Fetched ' . count($tcmbRates) . ' exchange rates from TCMB');

        // Force flag varsa tüm currency'leri güncelle, yoksa sadece auto_update olanları
        $query = ShopCurrency::whereIn('code', array_keys($tcmbRates));

        if (!$this->option('force')) {
            $query->where('is_auto_update', true);
        }

        $currencies = $query->get();

        if ($currencies->isEmpty()) {
            $this->warn('⚠️ No currencies found for auto-update. Use --force to update all currencies.');
            return self::SUCCESS;
        }

        $this->info('🔧 Updating ' . $currencies->count() . ' currencies...');

        $updatedCount = 0;
        foreach ($currencies as $currency) {
            if (isset($tcmbRates[$currency->code])) {
                $oldRate = $currency->exchange_rate;
                $newRate = $tcmbRates[$currency->code];

                $currency->exchange_rate = $newRate;
                $currency->save();

                $change = $newRate - $oldRate;
                $changePercent = $oldRate > 0 ? ($change / $oldRate) * 100 : 0;
                $arrow = $change > 0 ? '📈' : ($change < 0 ? '📉' : '➖');

                $this->line(sprintf(
                    '  %s %s: %.4f → %.4f (%+.4f / %+.2f%%)',
                    $arrow,
                    $currency->code,
                    $oldRate,
                    $newRate,
                    $change,
                    $changePercent
                ));

                $updatedCount++;
            }
        }

        $this->info('');
        $this->info("✅ Successfully updated $updatedCount currencies!");
        $this->info("💰 USD: ₺" . number_format($tcmbRates['USD'] ?? 0, 4));
        $this->info("💶 EUR: ₺" . number_format($tcmbRates['EUR'] ?? 0, 4));

        return self::SUCCESS;
    }
}
