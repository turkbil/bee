<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\AI\App\Services\FileLearningService;

class AILearningManage extends Command
{
    protected $signature = 'ai:learning
        {action : Action to perform (stats, add-promoted, list-promoted, add-synonym, list-synonyms, reset)}
        {--tenant= : Tenant ID}
        {--keyword= : Keyword for promoted product}
        {--pattern= : Product pattern for promoted product}
        {--tonnage= : Tonnage for promoted product}
        {--priority=1 : Priority (1=highest)}
        {--reason= : Reason for promotion}
        {--term= : Term for synonym}
        {--canonical= : Canonical form for synonym}';

    protected $description = 'Manage AI file-based learning system';

    public function handle()
    {
        $tenantId = $this->option('tenant') ?? 2;
        $action = $this->argument('action');

        // Initialize tenant
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found!");
            return 1;
        }

        tenancy()->initialize($tenant);
        $this->info("Tenant {$tenantId} initialized.");

        $service = new FileLearningService();

        switch ($action) {
            case 'stats':
                $this->showStats($service);
                break;

            case 'add-promoted':
                $this->addPromotedProduct($service);
                break;

            case 'list-promoted':
                $this->listPromotedProducts($service);
                break;

            case 'add-synonym':
                $this->addSynonym($service);
                break;

            case 'list-synonyms':
                $this->listSynonyms($service);
                break;

            case 'reset':
                if ($this->confirm('Are you sure you want to reset all learning data?')) {
                    $service->resetLearningData();
                    $this->info('Learning data reset successfully!');
                }
                break;

            case 'context':
                $this->showLearningContext($service);
                break;

            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: stats, add-promoted, list-promoted, add-synonym, list-synonyms, reset, context');
                return 1;
        }

        return 0;
    }

    private function showStats(FileLearningService $service): void
    {
        $stats = $service->getStats();

        $this->info("\n=== AI Learning Stats ===");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Tenant ID', $stats['tenant_id'] ?? 'N/A'],
                ['Version', $stats['version']],
                ['Synonyms', $stats['synonyms_count']],
                ['Promoted Products', $stats['promoted_products_count']],
                ['Popular Searches', $stats['popular_searches_count']],
                ['Common Questions', $stats['common_questions_count']],
                ['Blacklist', $stats['blacklist_count']],
                ['History', $stats['history_count']],
                ['Created', $stats['created_at'] ?? 'N/A'],
                ['Updated', $stats['updated_at'] ?? 'N/A'],
            ]
        );
    }

    private function addPromotedProduct(FileLearningService $service): void
    {
        $keyword = $this->option('keyword');
        $pattern = $this->option('pattern');

        if (!$keyword || !$pattern) {
            $this->error('--keyword and --pattern are required!');
            $this->info('Example: php artisan ai:learning add-promoted --keyword=transpalet --pattern=F4 --tonnage=1.5 --reason="En önemli ürün"');
            return;
        }

        $success = $service->addPromotedProduct(
            $keyword,
            $pattern,
            (int) $this->option('priority'),
            $this->option('reason') ?? '',
            $this->option('tonnage')
        );

        if ($success) {
            $this->info("✅ Promoted product added: {$keyword} → {$pattern}");
        } else {
            $this->error('Failed to add promoted product!');
        }
    }

    private function listPromotedProducts(FileLearningService $service): void
    {
        $products = $service->getPromotedProducts();

        if (empty($products)) {
            $this->info('No promoted products found.');
            return;
        }

        $this->info("\n=== Promoted Products ===");

        $rows = [];
        foreach ($products as $product) {
            $rows[] = [
                $product['keyword'],
                $product['product_pattern'],
                $product['tonnage'] ?? '-',
                $product['priority'],
                $product['reason'] ?? '-',
                $product['added_at'] ?? '-',
            ];
        }

        $this->table(
            ['Keyword', 'Pattern', 'Tonnage', 'Priority', 'Reason', 'Added'],
            $rows
        );
    }

    private function addSynonym(FileLearningService $service): void
    {
        $term = $this->option('term');
        $canonical = $this->option('canonical');

        if (!$term || !$canonical) {
            $this->error('--term and --canonical are required!');
            $this->info('Example: php artisan ai:learning add-synonym --term="trans palet" --canonical="transpalet"');
            return;
        }

        $success = $service->addSynonym($term, $canonical);

        if ($success) {
            $this->info("✅ Synonym added: {$term} → {$canonical}");
        } else {
            $this->error('Failed to add synonym (might be blacklisted)!');
        }
    }

    private function listSynonyms(FileLearningService $service): void
    {
        $synonyms = $service->getSynonyms();

        if (empty($synonyms)) {
            $this->info('No synonyms found.');
            return;
        }

        $this->info("\n=== Synonyms ===");

        $rows = [];
        foreach ($synonyms as $term => $canonical) {
            $rows[] = [$term, $canonical];
        }

        $this->table(['Term', 'Canonical'], $rows);
    }

    private function showLearningContext(FileLearningService $service): void
    {
        $context = $service->buildLearningContext();

        if (empty($context)) {
            $this->info('No learning context available.');
            return;
        }

        $this->info("\n=== Learning Context ===");
        $this->line($context);
    }
}
