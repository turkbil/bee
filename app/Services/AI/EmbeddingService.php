<?php

namespace App\Services\AI;

use OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmbeddingService
{
    private const MODEL = 'text-embedding-3-small'; // 1536 dimensions, $0.02/1M tokens
    private const CACHE_TTL = 86400; // 24 hours

    private $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('openai.api_key'));
    }

    /**
     * Generate embedding for text
     */
    public function generate(string $text): array
    {
        if (empty(trim($text))) {
            return array_fill(0, 1536, 0.0);
        }

        // Cache key
        $cacheKey = 'embedding:' . md5($text);

        // Check cache
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            // OpenAI API call
            $response = $this->client->embeddings()->create([
                'model' => self::MODEL,
                'input' => $text,
            ]);

            $embedding = $response->embeddings[0]->embedding;

            // Cache it
            Cache::put($cacheKey, $embedding, self::CACHE_TTL);

            Log::info('✅ Embedding generated', [
                'text_length' => strlen($text),
                'dimensions' => count($embedding),
            ]);

            return $embedding;

        } catch (\Exception $e) {
            Log::error('❌ Embedding generation failed', [
                'error' => $e->getMessage(),
            ]);

            // Return zero vector on error
            return array_fill(0, 1536, 0.0);
        }
    }

    /**
     * Calculate cosine similarity between two vectors
     */
    public function cosineSimilarity(array $vector1, array $vector2): float
    {
        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;

        $length = min(count($vector1), count($vector2));

        for ($i = 0; $i < $length; $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $norm1 += $vector1[$i] * $vector1[$i];
            $norm2 += $vector2[$i] * $vector2[$i];
        }

        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($norm1) * sqrt($norm2));
    }

    /**
     * Generate embedding for product
     */
    public function generateProductEmbedding($product): array
    {
        $locale = app()->getLocale();

        // Get translated fields
        $title = is_array($product->title) ? ($product->title[$locale] ?? '') : $product->title;
        $description = is_array($product->short_description)
            ? ($product->short_description[$locale] ?? '')
            : $product->short_description;
        $body = is_array($product->body) ? ($product->body[$locale] ?? '') : $product->body;

        // Combine text for embedding
        $text = implode(' ', array_filter([
            $title,
            $product->sku,
            strip_tags($description ?? ''),
            strip_tags($body ?? ''),
            $product->category?->title[$locale] ?? '',
            $product->brand?->name ?? '',
        ]));

        return $this->generate($text);
    }
}
