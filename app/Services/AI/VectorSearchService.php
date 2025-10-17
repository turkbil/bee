<?php

namespace App\Services\AI;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;

class VectorSearchService
{
    private EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    /**
     * Semantic search with vector similarity
     */
    public function search(string $query, int $limit = 50): array
    {
        // Generate query embedding
        $queryEmbedding = $this->embeddingService->generate($query);

        // Get products WITH embeddings
        $products = ShopProduct::whereNotNull('embedding')
            ->where('is_active', true)
            ->get();

        if ($products->isEmpty()) {
            Log::warning('No products with embeddings found');
            return [];
        }

        $results = [];

        foreach ($products as $product) {
            $productEmbedding = json_decode($product->embedding, true);
            if (!$productEmbedding) continue;

            $similarity = $this->embeddingService->cosineSimilarity(
                $queryEmbedding,
                $productEmbedding
            );

            $results[] = [
                'product' => $product,
                'similarity' => $similarity,
            ];
        }

        // Sort by similarity
        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // Top N
        $topResults = array_slice($results, 0, $limit);

        Log::info('✅ Vector search completed', [
            'total_products' => count($products),
            'query' => $query,
            'top_similarity' => $topResults[0]['similarity'] ?? 0,
        ]);

        return array_map(fn($r) => [
            'product' => $r['product']->toArray(),
            'score' => $r['similarity'],
        ], $topResults);
    }
}
