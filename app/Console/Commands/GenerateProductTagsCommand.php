<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;

/**
 * Auto-generate tags for products based on title, category, and specs
 */
class GenerateProductTagsCommand extends Command
{
    protected $signature = 'shop:generate-tags
                            {--tenant= : Specific tenant ID}
                            {--force : Overwrite existing tags}
                            {--dry-run : Preview only}';

    protected $description = 'Auto-generate tags for all products (spare parts industry)';

    protected int $generated = 0;
    protected int $skipped = 0;

    // Industry-specific tag mapping
    protected array $equipmentTags = [
        'forklift' => ['forklift', 'forklift yedek parça', 'forklift parçası'],
        'akülü forklift' => ['elektrikli forklift', 'akülü forklift', 'Li-Ion forklift'],
        'transpalet' => ['transpalet', 'transpalet yedek parça', 'palet transpaleti'],
        'istif' => ['istif makinesi', 'istifleyici', 'stacker'],
        'reach' => ['reach truck', 'dar koridor', 'yüksek kaldırma'],
    ];

    protected array $partTypeTags = [
        'tekerlek' => ['tekerlek', 'wheel', 'yedek tekerlek'],
        'teker' => ['tekerlek', 'wheel', 'yedek tekerlek'],
        'hidrolik' => ['hidrolik', 'hydraulic', 'hidrolik sistem'],
        'batarya' => ['batarya', 'akü', 'battery'],
        'akü' => ['batarya', 'akü', 'battery'],
        'motor' => ['motor', 'elektrik motoru', 'tahrik motoru'],
        'çatal' => ['çatal', 'fork', 'çatal seti'],
        'fren' => ['fren', 'brake', 'fren sistemi'],
        'lastik' => ['lastik', 'kauçuk', 'lastik tekerlek'],
        'zincir' => ['zincir', 'chain', 'kaldırma zinciri'],
        'pompa' => ['pompa', 'hydraulic pump', 'hidrolik pompa'],
        'silindir' => ['silindir', 'cylinder', 'hidrolik silindir'],
        'filtre' => ['filtre', 'filter', 'yağ filtresi'],
        'direksiyon' => ['direksiyon', 'steering', 'yön kontrol'],
        'şarj' => ['şarj cihazı', 'charger', 'battery charger'],
        'rulman' => ['rulman', 'bearing', 'bilyalı rulman'],
        'conta' => ['conta', 'seal', 'sızdırmazlık'],
    ];

    protected array $materialTags = [
        'poliüretan' => ['poliüretan', 'PU', 'polyurethane'],
        'poliamid' => ['poliamid', 'nylon', 'PA'],
        'paslanmaz' => ['paslanmaz çelik', 'stainless steel', 'inox'],
        'çelik' => ['çelik', 'steel', 'metal'],
        'kauçuk' => ['kauçuk', 'rubber', 'lastik'],
        'Li-Ion' => ['Li-Ion', 'lithium', 'lityum batarya'],
        'AGM' => ['AGM batarya', 'AGM', 'akü'],
    ];

    protected array $industryTags = [
        'yedek parça',
        'endüstriyel',
        'depo ekipmanı',
        'lojistik',
        'malzeme taşıma',
        'İXTİF',
    ];

    public function handle()
    {
        $this->info('🏷️  Product Tags Generation Starting...');
        $this->newLine();

        if ($tenantId = $this->option('tenant')) {
            $this->generateForTenant($tenantId);
        } else {
            $this->generateForAllTenants();
        }

        $this->displaySummary();

        return 0;
    }

    protected function generateForAllTenants()
    {
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            $this->info("Processing Tenant: {$tenant->id}");
            tenancy()->initialize($tenant);
            $this->generateTenantTags($tenant->id);
            tenancy()->end();
        }
    }

    protected function generateForTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found!");
            return;
        }

        tenancy()->initialize($tenant);
        $this->generateTenantTags($tenantId);
        tenancy()->end();
    }

    protected function generateTenantTags($tenantId)
    {
        $query = ShopProduct::query();

        // If not force, only generate for products without tags
        if (!$this->option('force')) {
            $query->where(function($q) {
                $q->whereNull('tags')
                  ->orWhere('tags', '[]')
                  ->orWhereRaw('JSON_LENGTH(tags) = 0');
            });
        }

        $products = $query->get();

        $this->info("Found {$products->count()} products to process");

        $bar = $this->output->createProgressBar($products->count());
        $bar->setFormat('%current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($products as $product) {
            $tags = $this->generateTags($product);

            if (!empty($tags)) {
                if (!$this->option('dry-run')) {
                    $product->tags = $tags;
                    $product->save();
                }

                $this->generated++;
                $bar->setMessage("Generated: {$product->sku} ({" . count($tags) . "} tags)");
            } else {
                $this->skipped++;
                $bar->setMessage("Skipped: {$product->sku}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    protected function generateTags(ShopProduct $product): array
    {
        $tags = [];

        // Get title
        $title = is_array($product->title)
            ? ($product->title['tr'] ?? $product->title['en'] ?? '')
            : ($product->title ?? '');

        $titleLower = mb_strtolower($title);

        // 1. Industry base tags
        $tags = array_merge($tags, ['yedek parça', 'endüstriyel', 'İXTİF']);

        // 2. Equipment type tags (from title)
        foreach ($this->equipmentTags as $keyword => $equipmentTags) {
            if (str_contains($titleLower, $keyword)) {
                $tags = array_merge($tags, $equipmentTags);
                break; // Only one equipment type
            }
        }

        // 3. Part type tags (from title)
        foreach ($this->partTypeTags as $keyword => $partTags) {
            if (str_contains($titleLower, $keyword)) {
                $tags = array_merge($tags, $partTags);
            }
        }

        // 4. Material tags (from title)
        foreach ($this->materialTags as $keyword => $materialTags) {
            if (str_contains($titleLower, $keyword)) {
                $tags = array_merge($tags, $materialTags);
            }
        }

        // 5. Category-based tags
        if ($product->category) {
            $categoryName = is_array($product->category->name)
                ? ($product->category->name['tr'] ?? $product->category->name['en'] ?? '')
                : ($product->category->name ?? '');

            if (!empty($categoryName)) {
                $tags[] = mb_strtolower($categoryName);
            }
        }

        // 6. Technical specs tags
        if (!empty($product->technical_specs)) {
            $specs = $product->technical_specs;

            // Voltage
            if (!empty($specs['voltage'])) {
                $tags[] = $specs['voltage'];
                $tags[] = 'elektrikli';
            }

            // Battery type
            if (!empty($specs['battery_type'])) {
                $tags[] = $specs['battery_type'];
                $tags[] = 'batarya';
            }

            // Capacity
            if (!empty($specs['capacity'])) {
                if (str_contains($specs['capacity'], 'ton')) {
                    $tags[] = 'yük kapasitesi';
                }
            }

            // Lift height
            if (!empty($specs['lift_height'])) {
                $tags[] = 'kaldırma';
            }
        }

        // 7. SKU-based tags (if contains specific patterns)
        $sku = $product->sku;
        if (preg_match('/(\d+)x(\d+)/i', $sku)) {
            $tags[] = 'boyutlu';
        }

        // 8. Special features from title
        if (str_contains($titleLower, 'soğuk') || str_contains($titleLower, 'soguk')) {
            $tags[] = 'soğuk depo';
            $tags[] = 'cold storage';
        }

        if (str_contains($titleLower, 'paslanmaz')) {
            $tags[] = 'gıda sektörü';
            $tags[] = 'paslanmaz';
        }

        if (str_contains($titleLower, 'dar koridor')) {
            $tags[] = 'dar koridor';
            $tags[] = 'yüksek kaldırma';
        }

        // 9. Common industry tags
        $tags[] = 'depo ekipmanı';
        $tags[] = 'lojistik';
        $tags[] = 'malzeme taşıma';

        // Remove duplicates and empty
        $tags = array_values(array_unique(array_filter($tags)));

        // Limit to 15 tags max
        if (count($tags) > 15) {
            $tags = array_slice($tags, 0, 15);
        }

        return $tags;
    }

    protected function displaySummary()
    {
        $this->newLine();
        $this->info('========================================');
        $this->info('📊 TAGS GENERATION SUMMARY');
        $this->info('========================================');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE');
            $this->newLine();
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['✅ Generated', $this->generated],
                ['⏭️  Skipped', $this->skipped],
            ]
        );

        $total = $this->generated + $this->skipped;
        $successRate = $total > 0 ? round(($this->generated / $total) * 100, 2) : 0;

        $this->newLine();
        $this->info("Success Rate: {$successRate}%");

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('💡 Run without --dry-run to save tags');
        }
    }
}
