<?php

namespace App\Services\Search;

use MeiliSearch\Client;
use Illuminate\Support\Facades\Log;

class MeilisearchConfig
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('scout.meilisearch.host'),
            config('scout.meilisearch.key')
        );
    }

    /**
     * Türkçe typo tolerance + synonym ayarları
     */
    public function configureTurkishSearch(string $indexName): void
    {
        $index = $this->client->index($indexName);

        Log::info('🔧 Configuring Meilisearch index', ['index' => $indexName]);

        // 1. Searchable attributes (hangi alanlarda arama yapılacak)
        $index->updateSearchableAttributes([
            'title',
            'slug',
            'sku',
            'model_number',
            'description',
            'body',
            'category_name',
            'brand_name',
            'tags',
        ]);

        // 2. Filterable attributes (filtreleme için)
        $index->updateFilterableAttributes([
            'product_id',
            'category_id',
            'brand_id',
            'is_active',
            'is_featured',
            'base_price',
        ]);

        // 3. Sortable attributes
        $index->updateSortableAttributes([
            'base_price',
            'created_at',
        ]);

        // 4. Typo tolerance ayarları (EN ÖNEMLİ!)
        $index->updateTypoTolerance([
            'enabled' => true,
            'minWordSizeForTypos' => [
                'oneTypo' => 4,   // 4+ harf: 1 typo tolere et (soguk → soğuk)
                'twoTypos' => 8,  // 8+ harf: 2 typo tolere et
            ],
            'disableOnWords' => [], // Hiçbir kelimeyi exclude etme
            'disableOnAttributes' => [], // Hiçbir attribute'u exclude etme
        ]);

        // 5. Synonym ayarları (Türkçe)
        $index->updateSynonyms([
            // Soğuk depo varyasyonları
            'soguk' => ['soğuk', 'souk', 'cold'],
            'depo' => ['warehouse', 'storage'],
            'soguk_depo' => ['soğuk depo', 'soguk depo', 'cold storage', 'freezer', 'dondurucu'],
            'etc' => ['extreme temperature', 'soğuk depo', 'soguk depo'],

            // Elektrik varyasyonları
            'elektrik' => ['electric', 'elektirik', 'elektiric'],
            'akulu' => ['akü', 'akülü', 'battery', 'batarya'],

            // Paslanmaz
            'paslanmaz' => ['stainless', 'inox', 'ss', 'paslanmz'],

            // Gıda
            'gida' => ['gıda', 'food'],

            // Forklift
            'forklift' => ['fork lift', 'istifleme'],
            'transpalet' => ['trans palet', 'pallet truck', 'el arabası'],

            // Kapasite
            'ton' => ['t', 'tonne', 'tonaj'],
            'kg' => ['kilogram', 'kilo'],

            // Enerji
            'dizel' => ['diesel', 'mazot'],
            'lpg' => ['liquefied petroleum gas'],
            'agm' => ['absorbed glass mat'],
            'liion' => ['li-ion', 'lithium-ion', 'lityum'],
        ]);

        // 6. Ranking rules (arama sonuç sıralaması)
        $index->updateRankingRules([
            'words',        // Kelime eşleşmesi (en önemli)
            'typo',         // Typo toleransı
            'proximity',    // Kelime yakınlığı
            'attribute',    // Attribute önem sırası (title > slug > description)
            'sort',         // Sıralama
            'exactness',    // Tam eşleşme bonusu
        ]);

        // 7. Display attributes (hangi alanlar dönecek)
        $index->updateDisplayedAttributes([
            'product_id',
            'title',
            'slug',
            'sku',
            'model_number',
            'description',
            'category_id',
            'brand_id',
            'category_name',
            'brand_name',
            'base_price',
            'is_active',
            'is_featured',
            'tags',
        ]);

        // 8. Distinct attribute (duplicate prevention)
        $index->updateDistinctAttribute('product_id');

        // 9. Pagination settings
        $index->updatePagination([
            'maxTotalHits' => 1000, // Max 1000 ürün dön
        ]);

        Log::info('✅ Meilisearch Turkish config applied', [
            'index' => $indexName,
            'typo_enabled' => true,
            'synonyms_count' => 15,
        ]);
    }

    /**
     * Get index stats
     */
    public function getIndexStats(string $indexName): array
    {
        try {
            $index = $this->client->index($indexName);
            $stats = $index->stats();

            return [
                'numberOfDocuments' => $stats['numberOfDocuments'] ?? 0,
                'isIndexing' => $stats['isIndexing'] ?? false,
                'fieldDistribution' => $stats['fieldDistribution'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('❌ Failed to get index stats', [
                'index' => $indexName,
                'error' => $e->getMessage(),
            ]);

            return [
                'numberOfDocuments' => 0,
                'isIndexing' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete index
     */
    public function deleteIndex(string $indexName): bool
    {
        try {
            $this->client->deleteIndex($indexName);
            Log::info('🗑️ Index deleted', ['index' => $indexName]);
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Failed to delete index', [
                'index' => $indexName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get all indexes
     */
    public function getAllIndexes(): array
    {
        try {
            $indexes = $this->client->getAllIndexes();
            return $indexes->getResults();
        } catch (\Exception $e) {
            Log::error('❌ Failed to get indexes', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
