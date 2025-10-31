<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Shop\App\Models\ShopCurrency;

class UpdateCurrencyRates extends Command
{
    protected $signature = 'currency:update {--dry-run : Sadece göster, güncelleme}';
    protected $description = 'TCMB günlük döviz kurlarını çek ve güncelle';

    public function handle()
    {
        $this->info('🔄 Döviz kurları güncelleniyor...');

        try {
            // TCMB XML'den kurları çek
            $response = Http::timeout(10)->get('https://www.tcmb.gov.tr/kurlar/today.xml');

            if (!$response->successful()) {
                $this->error('❌ TCMB API\'ye bağlanılamadı!');
                return 1;
            }

            $xml = simplexml_load_string($response->body());

            if (!$xml) {
                $this->error('❌ XML parse hatası!');
                return 1;
            }

            // Sadece is_auto_update = true olan currency'leri güncelle
            $currencies = ShopCurrency::where('is_auto_update', true)->get();

            if ($currencies->isEmpty()) {
                $this->warn('⚠️ Otomatik güncellenecek currency bulunamadı!');
                $this->info('💡 Admin panelden "Otomatik Güncelle" seçeneğini aktifleştirin.');
                return 0;
            }

            $updated = 0;
            $isDryRun = $this->option('dry-run');

            foreach ($currencies as $currency) {
                // TRY base currency, skip
                if ($currency->code === 'TRY') {
                    continue;
                }

                // XML'den kur değerini bul
                $currencyNode = null;
                foreach ($xml->Currency as $node) {
                    if ((string)$node['CurrencyCode'] === $currency->code) {
                        $currencyNode = $node;
                        break;
                    }
                }

                if (!$currencyNode) {
                    $this->warn("⚠️ {$currency->code} için kur bulunamadı!");
                    continue;
                }

                // ForexSelling (Döviz Satış) kullan
                $newRate = (float) str_replace(',', '.', (string)$currencyNode->ForexSelling);
                $oldRate = (float) $currency->exchange_rate;
                $change = $newRate - $oldRate;
                $changePercent = $oldRate > 0 ? (($change / $oldRate) * 100) : 0;

                if ($isDryRun) {
                    $this->line(sprintf(
                        '  %s: %.4f TL → %.4f TL (%s%.4f TL, %s%.2f%%)',
                        $currency->code,
                        $oldRate,
                        $newRate,
                        $change >= 0 ? '+' : '',
                        $change,
                        $changePercent >= 0 ? '+' : '',
                        $changePercent
                    ));
                } else {
                    $currency->update([
                        'exchange_rate' => $newRate,
                        'last_updated_at' => now(),
                    ]);

                    $this->info(sprintf(
                        '✅ %s: %.4f → %.4f TL (%s%.2f%%)',
                        $currency->code,
                        $oldRate,
                        $newRate,
                        $changePercent >= 0 ? '+' : '',
                        $changePercent
                    ));

                    $updated++;
                }
            }

            if ($isDryRun) {
                $this->info("\n💡 Gerçek güncelleme için: php artisan currency:update");
            } else {
                $this->info("\n🎉 {$updated} adet kur güncellendi!");

                Log::info('Currency rates updated', [
                    'updated_count' => $updated,
                    'timestamp' => now(),
                ]);
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Hata: ' . $e->getMessage());
            Log::error('Currency update failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
}
