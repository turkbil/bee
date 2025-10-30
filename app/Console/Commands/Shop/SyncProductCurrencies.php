<?php

namespace App\Console\Commands\Shop;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCurrency;

class SyncProductCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:sync-product-currencies
                            {--dry-run : Run without making changes}
                            {--force : Force sync even if currency_id is already set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product currencies: Update currency_id based on currency code field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Starting product currency sync...');
        $this->newLine();

        // Get all currencies from database
        $currencies = ShopCurrency::all()->keyBy('code');

        if ($currencies->isEmpty()) {
            $this->error('❌ No currencies found in database. Please create currencies first.');
            return Command::FAILURE;
        }

        $this->info('📦 Available currencies: ' . $currencies->pluck('code')->join(', '));
        $this->newLine();

        // Get products that need sync
        $query = ShopProduct::query();

        if (!$force) {
            $query->whereNull('currency_id');
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            $this->info('✅ No products need syncing.');
            return Command::SUCCESS;
        }

        $this->info("📊 Found {$products->count()} products to sync");
        $this->newLine();

        $stats = [
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $this->withProgressBar($products, function ($product) use ($currencies, $dryRun, &$stats) {
            try {
                // Get currency code from product
                $currencyCode = strtoupper($product->currency ?? 'TRY');

                // Find matching currency
                if (!isset($currencies[$currencyCode])) {
                    $this->newLine();
                    $this->warn("⚠️  Product #{$product->product_id}: Unknown currency '{$currencyCode}' - Defaulting to TRY");
                    $currencyCode = 'TRY';
                }

                if (!isset($currencies[$currencyCode])) {
                    $stats['skipped']++;
                    return;
                }

                $currency = $currencies[$currencyCode];

                if (!$dryRun) {
                    $product->update([
                        'currency_id' => $currency->currency_id,
                        'currency' => $currency->code,
                    ]);
                }

                $stats['updated']++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Product #{$product->product_id}: {$e->getMessage()}");
                $stats['errors']++;
            }
        });

        $this->newLine(2);

        // Summary
        $this->info('📊 Sync Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Updated', $stats['updated']],
                ['⚠️  Skipped', $stats['skipped']],
                ['❌ Errors', $stats['errors']],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('🔍 DRY RUN MODE - No changes were made');
            $this->info('Run without --dry-run to apply changes');
        }

        return Command::SUCCESS;
    }
}
