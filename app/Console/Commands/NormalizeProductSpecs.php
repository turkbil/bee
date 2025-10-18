<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\DB;

/**
 * Normalize Product Specifications
 *
 * Extract structured data (voltage, battery_type, capacity, lift_height)
 * from product descriptions and populate technical_specs fields
 */
class NormalizeProductSpecs extends Command
{
    protected $signature = 'shop:normalize-specs
                            {--tenant= : Specific tenant ID}
                            {--dry-run : Preview changes without applying}
                            {--force : Skip confirmation}';

    protected $description = 'Extract structured specs from descriptions into technical_specs';

    protected int $updated = 0;
    protected int $skipped = 0;
    protected array $stats = [];

    public function handle()
    {
        $this->info('🔧 Product Specs Normalization Starting...');
        $this->newLine();

        if (!$this->option('force') && !$this->option('dry-run')) {
            if (!$this->confirm('This will update product data. Continue?')) {
                $this->warn('Cancelled by user.');
                return 1;
            }
        }

        if ($tenantId = $this->option('tenant')) {
            $this->normalizeTenant($tenantId);
        } else {
            $this->normalizeAllTenants();
        }

        $this->displayStats();

        return 0;
    }

    protected function normalizeAllTenants()
    {
        $tenants = \App\Models\Tenant::all();

        $bar = $this->output->createProgressBar($tenants->count());
        $bar->setFormat('Tenants: %current%/%max% [%bar%] %percent:3s%%');

        foreach ($tenants as $tenant) {
            $this->line("\nProcessing Tenant: {$tenant->id} - {$tenant->name}");
            tenancy()->initialize($tenant);
            $this->normalizeTenantProducts($tenant->id);
            tenancy()->end();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    protected function normalizeTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found!");
            return;
        }

        $this->info("Processing Tenant: {$tenant->id} - {$tenant->name}");
        tenancy()->initialize($tenant);
        $this->normalizeTenantProducts($tenantId);
        tenancy()->end();
    }

    protected function normalizeTenantProducts($tenantId)
    {
        $products = ShopProduct::all();

        $bar = $this->output->createProgressBar($products->count());
        $bar->setFormat('Products: %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->setMessage('Starting...');

        $this->stats[$tenantId] = [
            'total' => $products->count(),
            'updated' => 0,
            'skipped' => 0,
            'extracted' => [
                'voltage' => 0,
                'battery_type' => 0,
                'capacity' => 0,
                'lift_height' => 0,
                'battery_capacity' => 0,
                'fork_length' => 0,
                'dimensions' => 0,
                'weight' => 0,
                'max_speed' => 0,
            ],
        ];

        foreach ($products as $product) {
            $extracted = $this->extractSpecs($product);

            if (!empty($extracted)) {
                if (!$this->option('dry-run')) {
                    $this->updateProduct($product, $extracted);
                }

                $this->stats[$tenantId]['updated']++;

                foreach ($extracted as $field => $value) {
                    $this->stats[$tenantId]['extracted'][$field]++;
                }

                $bar->setMessage("Updated: {$product->sku}");
            } else {
                $this->stats[$tenantId]['skipped']++;
                $bar->setMessage("Skipped: {$product->sku}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function extractSpecs(ShopProduct $product): array
    {
        $specs = $product->technical_specs ?? [];
        $extracted = [];

        // Get combined text from all description fields
        $description = is_array($product->body)
            ? ($product->body['tr'] ?? $product->body['en'] ?? '')
            : ($product->body ?? '');

        $shortDesc = is_array($product->short_description)
            ? ($product->short_description['tr'] ?? $product->short_description['en'] ?? '')
            : ($product->short_description ?? '');

        $title = is_array($product->title)
            ? ($product->title['tr'] ?? $product->title['en'] ?? '')
            : ($product->title ?? '');

        $combinedText = $title . ' ' . $shortDesc . ' ' . $description;

        // 1. Extract Voltage (48V, 24V, etc.)
        if (empty($specs['voltage'])) {
            if (preg_match('/(\d+)\s*V(?:olt)?(?!\w)/i', $combinedText, $matches)) {
                $extracted['voltage'] = $matches[1] . 'V';
            }
        }

        // 2. Extract Battery Type (Li-Ion, AGM, Lead-Acid, etc.)
        if (empty($specs['battery_type'])) {
            if (preg_match('/(Li-?Ion|Lithium(?:-Ion)?|AGM|Lead-Acid|Gel|Sulu\s+Akü)/i', $combinedText, $matches)) {
                $batteryType = $matches[1];
                // Normalize
                if (preg_match('/li-?ion|lithium/i', $batteryType)) {
                    $extracted['battery_type'] = 'Li-Ion';
                } elseif (preg_match('/agm/i', $batteryType)) {
                    $extracted['battery_type'] = 'AGM';
                } elseif (preg_match('/lead-acid/i', $batteryType)) {
                    $extracted['battery_type'] = 'Lead-Acid';
                } elseif (preg_match('/gel/i', $batteryType)) {
                    $extracted['battery_type'] = 'Gel';
                } elseif (preg_match('/sulu/i', $batteryType)) {
                    $extracted['battery_type'] = 'Sulu Akü';
                } else {
                    $extracted['battery_type'] = $batteryType;
                }
            }
        }

        // 3. Extract Battery Capacity (85Ah, 30Ah, etc.)
        if (empty($specs['battery_capacity'])) {
            if (preg_match('/(\d+)\s*Ah\b/i', $combinedText, $matches)) {
                $extracted['battery_capacity'] = $matches[1] . 'Ah';
            }
        }

        // 4. Extract Capacity (2 ton, 1.5 ton, 1500 kg, etc.)
        if (empty($specs['capacity'])) {
            // Try ton first
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*ton\b/i', $combinedText, $matches)) {
                $capacity = str_replace(',', '.', $matches[1]);
                $extracted['capacity'] = $capacity . ' ton';
            }
            // Try kg
            elseif (preg_match('/(\d+)\s*kg\b/i', $combinedText, $matches)) {
                $kg = (int)$matches[1];
                // Convert to ton if >= 1000 kg
                if ($kg >= 1000) {
                    $ton = $kg / 1000;
                    $extracted['capacity'] = $ton . ' ton';
                } else {
                    $extracted['capacity'] = $kg . ' kg';
                }
            }
            // Try lb (pounds)
            elseif (preg_match('/(\d+)\s*lb\b/i', $combinedText, $matches)) {
                $lb = (int)$matches[1];
                $extracted['capacity'] = $lb . ' lb';
            }
        }

        // 5. Extract Lift Height (3m, 5 metre, 3000 mm, etc.)
        if (empty($specs['lift_height'])) {
            // Try meters
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*m(?:etre)?(?:\s+kaldırma)?/i', $combinedText, $matches)) {
                $height = str_replace(',', '.', $matches[1]);
                $extracted['lift_height'] = $height . 'm';
            }
            // Try mm (convert to m if > 1000)
            elseif (preg_match('/(\d+)\s*mm(?:\s+kaldırma)?/i', $combinedText, $matches)) {
                $mm = (int)$matches[1];
                if ($mm >= 1000) {
                    $m = $mm / 1000;
                    $extracted['lift_height'] = $m . 'm';
                } else {
                    $extracted['lift_height'] = $mm . 'mm';
                }
            }
        }

        // 6. Extract Fork Length (1150 mm, etc.)
        if (empty($specs['fork_length'])) {
            if (preg_match('/(\d+)\s*mm\s+çatal/i', $combinedText, $matches)) {
                $extracted['fork_length'] = $matches[1] . 'mm';
            } elseif (preg_match('/çatal.*?(\d+)\s*mm/i', $combinedText, $matches)) {
                $extracted['fork_length'] = $matches[1] . 'mm';
            }
        }

        // 7. Extract Dimensions (LxWxH format)
        if (empty($specs['dimensions'])) {
            if (preg_match('/(\d+)\s*[xX×]\s*(\d+)\s*[xX×]\s*(\d+)\s*mm/i', $combinedText, $matches)) {
                $extracted['dimensions'] = "{$matches[1]}×{$matches[2]}×{$matches[3]} mm";
            }
        }

        // 8. Extract Weight
        if (empty($specs['weight'])) {
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*kg(?:\s+ağırlık)?/i', $combinedText, $matches)) {
                $weight = str_replace(',', '.', $matches[1]);
                $extracted['weight'] = $weight . ' kg';
            }
        }

        // 9. Extract Max Speed
        if (empty($specs['max_speed'])) {
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*km\/s/i', $combinedText, $matches)) {
                $speed = str_replace(',', '.', $matches[1]);
                $extracted['max_speed'] = $speed . ' km/h';
            }
        }

        return $extracted;
    }

    protected function updateProduct(ShopProduct $product, array $extracted)
    {
        $specs = $product->technical_specs ?? [];

        // Merge extracted data with existing specs
        foreach ($extracted as $field => $value) {
            $specs[$field] = $value;
        }

        $product->technical_specs = $specs;
        $product->save();
    }

    protected function displayStats()
    {
        $this->newLine();
        $this->info('========================================');
        $this->info('📊 NORMALIZATION STATS');
        $this->info('========================================');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No changes were made');
            $this->newLine();
        }

        foreach ($this->stats as $tenantId => $data) {
            $this->line("🏢 <fg=yellow>Tenant {$tenantId}</>");
            $this->line("📦 Total Products: <fg=cyan>{$data['total']}</>");
            $this->line("✅ Updated: <fg=green>{$data['updated']}</>");
            $this->line("⏭️  Skipped: <fg=gray>{$data['skipped']}</>");
            $this->newLine();

            $this->line('<fg=yellow>Extracted Fields:</>');
            $this->table(
                ['Field', 'Extracted Count'],
                [
                    ['Voltage', $data['extracted']['voltage']],
                    ['Battery Type', $data['extracted']['battery_type']],
                    ['Battery Capacity', $data['extracted']['battery_capacity']],
                    ['Capacity', $data['extracted']['capacity']],
                    ['Lift Height', $data['extracted']['lift_height']],
                ]
            );

            $this->newLine();
            $this->line(str_repeat('─', 60));
            $this->newLine();
        }

        $totalUpdated = array_sum(array_column($this->stats, 'updated'));
        $totalSkipped = array_sum(array_column($this->stats, 'skipped'));

        $this->info("🎯 Total Updated: {$totalUpdated}");
        $this->info("⏭️  Total Skipped: {$totalSkipped}");

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('💡 Run without --dry-run to apply changes');
        }
    }
}
