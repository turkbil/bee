<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AI\ProductPlaceholderService;
use Illuminate\Support\Facades\Log;

/**
 * Generate Product Placeholder Command
 *
 * Background worker to generate AI placeholders without blocking HTTP requests
 */
class GeneratePlaceholderCommand extends Command
{
    protected $signature = 'app:generate-placeholder {productId}';
    protected $description = 'Generate AI placeholder for product (background worker)';

    public function handle(ProductPlaceholderService $service): int
    {
        $productId = $this->argument('productId');

        $this->info("🔄 Generating placeholder for product {$productId}...");

        try {
            // Force generate (bypass cache check)
            $result = $service->forceGenerate($productId);

            if ($result['success']) {
                $this->info("✅ Placeholder generated successfully!");
                Log::info('Background placeholder generated', [
                    'product_id' => $productId,
                    'conversation_count' => count($result['conversation'])
                ]);
                return Command::SUCCESS;
            } else {
                $this->error("❌ Generation failed: " . ($result['error'] ?? 'Unknown'));
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
            Log::error('Background placeholder generation failed', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return Command::FAILURE;
        }
    }
}
