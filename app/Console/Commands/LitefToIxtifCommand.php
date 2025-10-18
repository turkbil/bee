<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\DB;

/**
 * Convert LITEF products to İXTİF brand
 */
class LitefToIxtifCommand extends Command
{
    protected $signature = 'shop:litef-to-ixtif
                            {--tenant= : Specific tenant ID}
                            {--dry-run : Preview changes}
                            {--force : Skip confirmation}';

    protected $description = 'Convert LITEF products to İXTİF brand and update all references';

    protected int $updated = 0;
    protected array $changes = [];

    public function handle()
    {
        $this->info('🔄 LITEF → İXTİF Conversion Starting...');
        $this->newLine();

        if (!$this->option('force') && !$this->option('dry-run')) {
            if (!$this->confirm('This will update LITEF products. Continue?')) {
                $this->warn('Cancelled.');
                return 1;
            }
        }

        if ($tenantId = $this->option('tenant')) {
            $this->convertTenant($tenantId);
        } else {
            $this->convertAllTenants();
        }

        $this->displaySummary();

        return 0;
    }

    protected function convertAllTenants()
    {
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            $this->info("Processing Tenant: {$tenant->id}");
            tenancy()->initialize($tenant);
            $this->convertTenantProducts($tenant->id);
            tenancy()->end();
        }
    }

    protected function convertTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found!");
            return;
        }

        tenancy()->initialize($tenant);
        $this->convertTenantProducts($tenantId);
        tenancy()->end();
    }

    protected function convertTenantProducts($tenantId)
    {
        // Find all LITEF products
        $products = ShopProduct::where(function($q) {
            $q->where('title', 'LIKE', '%LITEF%')
              ->orWhere('title', 'LIKE', '%litef%')
              ->orWhere('title', 'LIKE', '%lıtef%')
              ->orWhere('title', 'LIKE', '%LİTEF%')
              ->orWhere('sku', 'LIKE', '%LITEF%')
              ->orWhere('sku', 'LIKE', '%litef%')
              ->orWhere('short_description', 'LIKE', '%LITEF%')
              ->orWhere('short_description', 'LIKE', '%litef%')
              ->orWhere('body', 'LIKE', '%LITEF%')
              ->orWhere('body', 'LIKE', '%litef%');
        })->get();

        $this->info("Found {$products->count()} LITEF products");

        $bar = $this->output->createProgressBar($products->count());
        $bar->setFormat('%current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($products as $product) {
            $changes = $this->convertProduct($product);

            if (!empty($changes) && !$this->option('dry-run')) {
                $product->save();
            }

            $this->changes[] = [
                'product_id' => $product->product_id,
                'sku' => $product->sku,
                'changes' => $changes,
            ];

            $bar->setMessage("Updated: {$product->sku}");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    protected function convertProduct(ShopProduct $product): array
    {
        $changes = [];

        // 1. Update SKU
        if ($this->containsLitef($product->sku)) {
            $oldSku = $product->sku;
            $product->sku = $this->replaceLitef($product->sku);
            $changes['sku'] = ['from' => $oldSku, 'to' => $product->sku];
        }

        // 2. Update Title (JSON support)
        if (is_array($product->title)) {
            $titleChanged = false;
            $newTitle = $product->title;

            foreach ($newTitle as $lang => $text) {
                if ($this->containsLitef($text)) {
                    $newTitle[$lang] = $this->replaceLitef($text);
                    $titleChanged = true;
                }
            }

            if ($titleChanged) {
                $changes['title'] = ['from' => $product->title, 'to' => $newTitle];
                $product->title = $newTitle;
            }
        } elseif ($this->containsLitef($product->title)) {
            $oldTitle = $product->title;
            $product->title = $this->replaceLitef($product->title);
            $changes['title'] = ['from' => $oldTitle, 'to' => $product->title];
        }

        // 3. Update Short Description
        if (is_array($product->short_description)) {
            $descChanged = false;
            $newDesc = $product->short_description;

            foreach ($newDesc as $lang => $text) {
                if ($this->containsLitef($text)) {
                    $newDesc[$lang] = $this->replaceLitef($text);
                    $descChanged = true;
                }
            }

            if ($descChanged) {
                $changes['short_description'] = true;
                $product->short_description = $newDesc;
            }
        } elseif ($this->containsLitef($product->short_description ?? '')) {
            $product->short_description = $this->replaceLitef($product->short_description);
            $changes['short_description'] = true;
        }

        // 4. Update Body
        if (is_array($product->body)) {
            $bodyChanged = false;
            $newBody = $product->body;

            foreach ($newBody as $lang => $text) {
                if ($this->containsLitef($text)) {
                    $newBody[$lang] = $this->replaceLitef($text);
                    $bodyChanged = true;
                }
            }

            if ($bodyChanged) {
                $changes['body'] = true;
                $product->body = $newBody;
            }
        } elseif ($this->containsLitef($product->body ?? '')) {
            $product->body = $this->replaceLitef($product->body);
            $changes['body'] = true;
        }

        // 5. Update Slug (regenerate from new title)
        if (!empty($changes['title'])) {
            $product->slug = null; // Will auto-regenerate
            $changes['slug'] = 'regenerated';
        }

        // 6. Update Brand ID to 1 (İXTİF)
        if ($product->brand_id !== 1) {
            $changes['brand_id'] = ['from' => $product->brand_id, 'to' => 1];
            $product->brand_id = 1;
        }

        if (!empty($changes)) {
            $this->updated++;
        }

        return $changes;
    }

    protected function containsLitef(string $text): bool
    {
        return preg_match('/(LITEF|litef|lıtef|LİTEF)/i', $text);
    }

    protected function replaceLitef(string $text): string
    {
        // Replace all variations with İXTİF
        return preg_replace('/(LITEF|litef|lıtef|LİTEF)/i', 'İXTİF', $text);
    }

    protected function displaySummary()
    {
        $this->newLine();
        $this->info('========================================');
        $this->info('📊 CONVERSION SUMMARY');
        $this->info('========================================');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No changes were made');
            $this->newLine();
        }

        $this->line("✅ Total Products Updated: <fg=green>{$this->updated}</>");

        // Count change types
        $skuChanges = 0;
        $titleChanges = 0;
        $brandChanges = 0;

        foreach ($this->changes as $change) {
            if (!empty($change['changes']['sku'])) $skuChanges++;
            if (!empty($change['changes']['title'])) $titleChanges++;
            if (!empty($change['changes']['brand_id'])) $brandChanges++;
        }

        $this->newLine();
        $this->table(
            ['Change Type', 'Count'],
            [
                ['SKU Updated', $skuChanges],
                ['Title Updated', $titleChanges],
                ['Brand ID → 1', $brandChanges],
                ['Description Updated', count(array_filter($this->changes, fn($c) => !empty($c['changes']['short_description']) || !empty($c['changes']['body'])))],
            ]
        );

        // Show first 5 examples
        $this->newLine();
        $this->line('<fg=yellow>Examples (first 5):</>');
        $examples = array_slice($this->changes, 0, 5);

        foreach ($examples as $example) {
            if (!empty($example['changes'])) {
                $this->line("  • {$example['sku']}");

                if (!empty($example['changes']['title'])) {
                    $fromTitle = is_array($example['changes']['title']['from'])
                        ? ($example['changes']['title']['from']['tr'] ?? 'N/A')
                        : $example['changes']['title']['from'];
                    $toTitle = is_array($example['changes']['title']['to'])
                        ? ($example['changes']['title']['to']['tr'] ?? 'N/A')
                        : $example['changes']['title']['to'];

                    $this->line("    Title: <fg=red>{$fromTitle}</> → <fg=green>{$toTitle}</>");
                }

                if (!empty($example['changes']['brand_id'])) {
                    $this->line("    Brand: {$example['changes']['brand_id']['from']} → <fg=green>1 (İXTİF)</>");
                }
            }
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('💡 Run without --dry-run to apply changes');
        }
    }
}
