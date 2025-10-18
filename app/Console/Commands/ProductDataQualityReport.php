<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\DB;

/**
 * Product Data Quality Report
 *
 * Analyze product data completeness and identify missing structured fields
 * that exist in descriptions but not in custom_technical_specs
 */
class ProductDataQualityReport extends Command
{
    protected $signature = 'shop:data-quality-report
                            {--tenant= : Specific tenant ID}
                            {--export= : Export to file path}
                            {--format=text : Output format (text|json|csv)}';

    protected $description = 'Generate data quality report for shop products';

    protected array $report = [];
    protected array $patterns = [];

    public function handle()
    {
        $this->info('🔍 Product Data Quality Analysis Starting...');
        $this->newLine();

        // Define search patterns for common specs
        $this->patterns = [
            'voltage' => [
                'pattern' => '/(\d+)V\b/i',
                'field' => 'voltage',
                'examples' => ['48V', '24V', '12V']
            ],
            'battery_type' => [
                'pattern' => '/(Li-Ion|AGM|Lead-Acid|Lithium|Gel)/i',
                'field' => 'battery_type',
                'examples' => ['Li-Ion', 'AGM']
            ],
            'battery_capacity' => [
                'pattern' => '/(\d+)Ah\b/i',
                'field' => 'battery_capacity',
                'examples' => ['85Ah', '30Ah']
            ],
            'capacity' => [
                'pattern' => '/(\d+\.?\d*)\s*(ton|kg)\b/i',
                'field' => 'capacity',
                'examples' => ['2 ton', '1500 kg']
            ],
            'lift_height' => [
                'pattern' => '/(\d+\.?\d*)\s*(m|metre|meter)\b/i',
                'field' => 'lift_height',
                'examples' => ['3m', '5 metre']
            ],
        ];

        if ($tenantId = $this->option('tenant')) {
            $this->analyzeTenant($tenantId);
        } else {
            $this->analyzeAllTenants();
        }

        $this->displayReport();

        if ($exportPath = $this->option('export')) {
            $this->exportReport($exportPath);
        }

        return 0;
    }

    protected function analyzeAllTenants()
    {
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            $this->info("Analyzing Tenant: {$tenant->id} - {$tenant->name}");
            tenancy()->initialize($tenant);
            $this->analyzeTenantProducts($tenant->id);
            tenancy()->end();
        }
    }

    protected function analyzeTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found!");
            return;
        }

        tenancy()->initialize($tenant);
        $this->analyzeTenantProducts($tenantId);
        tenancy()->end();
    }

    protected function analyzeTenantProducts($tenantId)
    {
        $products = ShopProduct::with('category', 'brand')->get();

        $this->report[$tenantId] = [
            'tenant_id' => $tenantId,
            'total_products' => $products->count(),
            'field_stats' => [],
            'missing_structured_data' => [],
            'data_in_description' => [],
            'recommendations' => [],
        ];

        $missingVoltage = 0;
        $missingBatteryType = 0;
        $missingCapacity = 0;
        $missingLiftHeight = 0;
        $missingDescription = 0;
        $missingShortDescription = 0;
        $missingTags = 0;
        $missingFeatures = 0;

        $voltageInDescription = 0;
        $batteryInDescription = 0;
        $capacityInDescription = 0;
        $liftHeightInDescription = 0;

        $examples = [
            'voltage_in_desc_not_field' => [],
            'battery_in_desc_not_field' => [],
            'capacity_in_desc_not_field' => [],
        ];

        foreach ($products as $product) {
            $specs = $product->custom_technical_specs ?? [];
            $description = is_array($product->description)
                ? ($product->description['tr'] ?? $product->description['en'] ?? '')
                : ($product->description ?? '');
            $shortDesc = is_array($product->short_description)
                ? ($product->short_description['tr'] ?? $product->short_description['en'] ?? '')
                : ($product->short_description ?? '');

            $combinedText = $description . ' ' . $shortDesc;

            // Check missing fields
            if (empty($specs['voltage'])) {
                $missingVoltage++;

                // Check if voltage exists in description
                if (preg_match($this->patterns['voltage']['pattern'], $combinedText, $matches)) {
                    $voltageInDescription++;
                    if (count($examples['voltage_in_desc_not_field']) < 5) {
                        $examples['voltage_in_desc_not_field'][] = [
                            'id' => $product->id,
                            'sku' => $product->sku,
                            'title' => is_array($product->title) ? ($product->title['tr'] ?? '') : $product->title,
                            'found_in_text' => $matches[0],
                            'text_preview' => substr($combinedText, max(0, strpos($combinedText, $matches[0]) - 50), 150),
                        ];
                    }
                }
            }

            if (empty($specs['battery_type'])) {
                $missingBatteryType++;

                if (preg_match($this->patterns['battery_type']['pattern'], $combinedText, $matches)) {
                    $batteryInDescription++;
                    if (count($examples['battery_in_desc_not_field']) < 5) {
                        $examples['battery_in_desc_not_field'][] = [
                            'id' => $product->id,
                            'sku' => $product->sku,
                            'title' => is_array($product->title) ? ($product->title['tr'] ?? '') : $product->title,
                            'found_in_text' => $matches[0],
                        ];
                    }
                }
            }

            if (empty($specs['capacity'])) {
                $missingCapacity++;

                if (preg_match($this->patterns['capacity']['pattern'], $combinedText, $matches)) {
                    $capacityInDescription++;
                    if (count($examples['capacity_in_desc_not_field']) < 5) {
                        $examples['capacity_in_desc_not_field'][] = [
                            'id' => $product->id,
                            'sku' => $product->sku,
                            'title' => is_array($product->title) ? ($product->title['tr'] ?? '') : $product->title,
                            'found_in_text' => $matches[0],
                        ];
                    }
                }
            }

            if (empty($specs['lift_height'])) {
                $missingLiftHeight++;

                if (preg_match($this->patterns['lift_height']['pattern'], $combinedText, $matches)) {
                    $liftHeightInDescription++;
                }
            }

            if (empty($description)) $missingDescription++;
            if (empty($shortDesc)) $missingShortDescription++;
            if (empty($product->tags)) $missingTags++;
            if (empty($product->custom_features) || count($product->custom_features) === 0) $missingFeatures++;
        }

        $total = $products->count();

        $this->report[$tenantId]['field_stats'] = [
            'voltage' => [
                'missing' => $missingVoltage,
                'missing_percent' => round(($missingVoltage / $total) * 100, 2),
                'in_description' => $voltageInDescription,
                'can_be_extracted' => $voltageInDescription,
            ],
            'battery_type' => [
                'missing' => $missingBatteryType,
                'missing_percent' => round(($missingBatteryType / $total) * 100, 2),
                'in_description' => $batteryInDescription,
                'can_be_extracted' => $batteryInDescription,
            ],
            'capacity' => [
                'missing' => $missingCapacity,
                'missing_percent' => round(($missingCapacity / $total) * 100, 2),
                'in_description' => $capacityInDescription,
                'can_be_extracted' => $capacityInDescription,
            ],
            'lift_height' => [
                'missing' => $missingLiftHeight,
                'missing_percent' => round(($missingLiftHeight / $total) * 100, 2),
                'in_description' => $liftHeightInDescription,
                'can_be_extracted' => $liftHeightInDescription,
            ],
            'description' => [
                'missing' => $missingDescription,
                'missing_percent' => round(($missingDescription / $total) * 100, 2),
            ],
            'short_description' => [
                'missing' => $missingShortDescription,
                'missing_percent' => round(($missingShortDescription / $total) * 100, 2),
            ],
            'tags' => [
                'missing' => $missingTags,
                'missing_percent' => round(($missingTags / $total) * 100, 2),
            ],
            'features' => [
                'missing' => $missingFeatures,
                'missing_percent' => round(($missingFeatures / $total) * 100, 2),
            ],
        ];

        $this->report[$tenantId]['examples'] = $examples;

        // Generate recommendations
        $recommendations = [];

        if ($voltageInDescription > 0) {
            $recommendations[] = "🔧 {$voltageInDescription} products have voltage in description but not in structured field - Can be auto-extracted!";
        }

        if ($batteryInDescription > 0) {
            $recommendations[] = "🔧 {$batteryInDescription} products have battery type in description but not in structured field - Can be auto-extracted!";
        }

        if ($capacityInDescription > 0) {
            $recommendations[] = "🔧 {$capacityInDescription} products have capacity in description but not in structured field - Can be auto-extracted!";
        }

        if ($missingDescription > 10) {
            $recommendations[] = "⚠️  {$missingDescription} products missing description - Manual content needed!";
        }

        if ($missingTags > $total * 0.5) {
            $recommendations[] = "⚠️  {$missingTags} products missing tags - Add tags for better search!";
        }

        $this->report[$tenantId]['recommendations'] = $recommendations;
    }

    protected function displayReport()
    {
        $this->newLine();
        $this->info('========================================');
        $this->info('📊 DATA QUALITY REPORT');
        $this->info('========================================');
        $this->newLine();

        foreach ($this->report as $tenantId => $data) {
            $this->line("🏢 <fg=yellow>Tenant {$tenantId}</>");
            $this->line("📦 Total Products: <fg=cyan>{$data['total_products']}</>");
            $this->newLine();

            $this->line('<fg=yellow>Field Completeness:</>');
            $this->table(
                ['Field', 'Missing', 'Missing %', 'In Description', 'Can Extract'],
                [
                    [
                        'Voltage',
                        $data['field_stats']['voltage']['missing'],
                        $data['field_stats']['voltage']['missing_percent'] . '%',
                        $data['field_stats']['voltage']['in_description'],
                        $data['field_stats']['voltage']['can_be_extracted'],
                    ],
                    [
                        'Battery Type',
                        $data['field_stats']['battery_type']['missing'],
                        $data['field_stats']['battery_type']['missing_percent'] . '%',
                        $data['field_stats']['battery_type']['in_description'],
                        $data['field_stats']['battery_type']['can_be_extracted'],
                    ],
                    [
                        'Capacity',
                        $data['field_stats']['capacity']['missing'],
                        $data['field_stats']['capacity']['missing_percent'] . '%',
                        $data['field_stats']['capacity']['in_description'],
                        $data['field_stats']['capacity']['can_be_extracted'],
                    ],
                    [
                        'Lift Height',
                        $data['field_stats']['lift_height']['missing'],
                        $data['field_stats']['lift_height']['missing_percent'] . '%',
                        $data['field_stats']['lift_height']['in_description'],
                        $data['field_stats']['lift_height']['can_be_extracted'],
                    ],
                    [
                        'Description',
                        $data['field_stats']['description']['missing'],
                        $data['field_stats']['description']['missing_percent'] . '%',
                        '-',
                        '-',
                    ],
                    [
                        'Short Description',
                        $data['field_stats']['short_description']['missing'],
                        $data['field_stats']['short_description']['missing_percent'] . '%',
                        '-',
                        '-',
                    ],
                    [
                        'Tags',
                        $data['field_stats']['tags']['missing'],
                        $data['field_stats']['tags']['missing_percent'] . '%',
                        '-',
                        '-',
                    ],
                    [
                        'Features',
                        $data['field_stats']['features']['missing'],
                        $data['field_stats']['features']['missing_percent'] . '%',
                        '-',
                        '-',
                    ],
                ]
            );

            $this->newLine();
            $this->line('<fg=yellow>Examples (Voltage in Description but not in Field):</>');
            if (!empty($data['examples']['voltage_in_desc_not_field'])) {
                foreach ($data['examples']['voltage_in_desc_not_field'] as $example) {
                    $this->line("  • {$example['sku']} - {$example['title']}");
                    $this->line("    Found: <fg=green>{$example['found_in_text']}</>");
                    if (isset($example['text_preview'])) {
                        $this->line("    Context: <fg=gray>{$example['text_preview']}</>");
                    }
                }
            } else {
                $this->line('  <fg=gray>No examples found</>');
            }

            $this->newLine();
            $this->line('<fg=yellow>📋 Recommendations:</>');
            foreach ($data['recommendations'] as $rec) {
                $this->line("  {$rec}");
            }

            $this->newLine();
            $this->line(str_repeat('─', 60));
            $this->newLine();
        }
    }

    protected function exportReport($path)
    {
        $format = $this->option('format');

        switch ($format) {
            case 'json':
                file_put_contents($path, json_encode($this->report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                break;

            case 'csv':
                // CSV export implementation
                $this->exportCsv($path);
                break;

            default:
                // Text export
                $this->exportText($path);
                break;
        }

        $this->info("📄 Report exported to: {$path}");
    }

    protected function exportText($path)
    {
        $output = "PRODUCT DATA QUALITY REPORT\n";
        $output .= "Generated: " . now()->toDateTimeString() . "\n";
        $output .= str_repeat('=', 80) . "\n\n";

        foreach ($this->report as $tenantId => $data) {
            $output .= "Tenant {$tenantId}\n";
            $output .= "Total Products: {$data['total_products']}\n\n";

            foreach ($data['field_stats'] as $field => $stats) {
                $output .= sprintf(
                    "%-20s Missing: %4d (%5.2f%%)  In Desc: %4d  Can Extract: %4d\n",
                    ucfirst($field),
                    $stats['missing'] ?? 0,
                    $stats['missing_percent'] ?? 0,
                    $stats['in_description'] ?? 0,
                    $stats['can_be_extracted'] ?? 0
                );
            }

            $output .= "\nRecommendations:\n";
            foreach ($data['recommendations'] as $rec) {
                $output .= "  - {$rec}\n";
            }

            $output .= "\n" . str_repeat('-', 80) . "\n\n";
        }

        file_put_contents($path, $output);
    }

    protected function exportCsv($path)
    {
        $fp = fopen($path, 'w');

        fputcsv($fp, ['Tenant ID', 'Field', 'Missing Count', 'Missing %', 'In Description', 'Can Extract']);

        foreach ($this->report as $tenantId => $data) {
            foreach ($data['field_stats'] as $field => $stats) {
                fputcsv($fp, [
                    $tenantId,
                    ucfirst($field),
                    $stats['missing'] ?? 0,
                    $stats['missing_percent'] ?? 0,
                    $stats['in_description'] ?? 0,
                    $stats['can_be_extracted'] ?? 0,
                ]);
            }
        }

        fclose($fp);
    }
}
