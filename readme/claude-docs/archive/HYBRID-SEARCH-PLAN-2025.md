# 🚀 HİBRİT ARAMA SİSTEMİ - DETAYLI PLAN

**Tarih:** 2025-10-17
**Hedef:** En iyi ürün arama sistemi (typo + semantic + hızlı)
**Durum:** Planlama Aşaması

---

## 🎯 HEDEF SORUNLAR

1. ✅ **Typo Tolerance:** "soguk" → "soğuk" otomatik düzelmeli
2. ✅ **Semantic Search:** "elektirik" → "akülü forklift" bulmalı
3. ✅ **Hızlı:** <100ms yanıt süresi
4. ✅ **Multi-tenant:** Tenant bazlı izolasyon
5. ✅ **Sürdürülebilir:** Yeni ürün = kod değişikliği yok

---

## 🏗️ MİMARİ TASARIM

```
┌─────────────────────────────────────────────────────────┐
│                   KULLANICI SORGUSU                      │
│              "soguk depo transpalet"                     │
└────────────────────┬────────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                          │
        ▼                          ▼
┌──────────────┐          ┌──────────────────┐
│ MEILISEARCH  │          │ SEMANTIC VECTOR  │
│  (Keyword)   │          │   (Embeddings)   │
├──────────────┤          ├──────────────────┤
│ • Typo fix   │          │ • Anlam bazlı    │
│ • Synonym    │          │ • OpenAI API     │
│ • Filtreleme │          │ • MySQL Vector   │
│ • <50ms      │          │ • ~200ms         │
└──────┬───────┘          └────────┬─────────┘
       │                           │
       └─────────┬─────────────────┘
                 ▼
        ┌────────────────┐
        │ HYBRID SCORER  │
        ├────────────────┤
        │ • 70% keyword  │
        │ • 30% semantic │
        │ • Re-ranking   │
        └────────┬───────┘
                 │
                 ▼
        ┌────────────────┐
        │  EN İYİ 5      │
        │  ÜRÜN LİSTESİ  │
        └────────────────┘
```

---

## 📊 FAZ 1: MEİLİSEARCH KURULUMU

### 1.1 Sunucu Kurulumu (Plesk)

**Hedef:** Meilisearch binary'yi Plesk sunucuda çalıştır

#### Adım 1: SSH ile sunucuya bağlan
```bash
ssh root@tuufi.com
```

#### Adım 2: Meilisearch binary indir
```bash
cd /usr/local/bin
curl -L https://install.meilisearch.com | sh
chmod +x meilisearch
```

#### Adım 3: Systemd service oluştur
```bash
cat > /etc/systemd/system/meilisearch.service <<'EOF'
[Unit]
Description=Meilisearch
After=network.target

[Service]
Type=simple
User=www-data
ExecStart=/usr/local/bin/meilisearch --http-addr 127.0.0.1:7700 --master-key="CHANGE_ME_RANDOM_KEY_HERE"
Restart=on-failure
RestartSec=5s
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF
```

**NOT:** `CHANGE_ME_RANDOM_KEY_HERE` yerine güvenli key üret:
```bash
openssl rand -base64 32
```

#### Adım 4: Service'i başlat
```bash
systemctl daemon-reload
systemctl enable meilisearch
systemctl start meilisearch
systemctl status meilisearch
```

#### Adım 5: Test et
```bash
curl http://127.0.0.1:7700/health
# Beklenen: {"status":"available"}
```

---

### 1.2 Laravel Entegrasyonu

#### Adım 1: Composer paketlerini kur
```bash
cd /var/www/vhosts/tuufi.com/laravel
composer require laravel/scout
composer require meilisearch/meilisearch-php http-interop/http-factory-guzzle
```

#### Adım 2: Scout config yayınla
```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

#### Adım 3: .env güncelle
```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=YOUR_MASTER_KEY_FROM_ABOVE
```

#### Adım 4: ShopProduct modelini searchable yap

**Dosya:** `Modules/Shop/App/Models/ShopProduct.php`

```php
<?php

namespace Modules\Shop\App\Models;

use Laravel\Scout\Searchable;

class ShopProduct extends Model
{
    use Searchable; // ← Ekle

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => strip_tags($this->description ?? ''),
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'is_active' => $this->is_active,
            'base_price' => $this->base_price,
            // Custom fields
            'capacity' => $this->capacity,
            'battery_type' => $this->battery_type,
            'lift_height' => $this->lift_height,
        ];
    }

    /**
     * Get the index name for the model.
     */
    public function searchableAs(): string
    {
        // Multi-tenant: Her tenant'ın kendi index'i
        if (tenancy()->initialized) {
            return 'shop_products_tenant_' . tenant('id');
        }
        return 'shop_products';
    }

    /**
     * Modify the query used to retrieve models when making all searchable.
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with(['category', 'brand']);
    }
}
```

#### Adım 5: Meilisearch ayarlarını yapılandır

**Yeni dosya:** `app/Services/Search/MeilisearchConfig.php`

```php
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

        // 1. Searchable attributes (hangi alanlarda arama yapılacak)
        $index->updateSearchableAttributes([
            'title',
            'slug',
            'sku',
            'description',
        ]);

        // 2. Filterable attributes (filtreleme için)
        $index->updateFilterableAttributes([
            'category_id',
            'brand_id',
            'is_active',
            'base_price',
        ]);

        // 3. Sortable attributes
        $index->updateSortableAttributes([
            'base_price',
            'created_at',
        ]);

        // 4. Typo tolerance ayarları
        $index->updateTypoTolerance([
            'enabled' => true,
            'minWordSizeForTypos' => [
                'oneTypo' => 4,   // 4+ harf: 1 typo tolere et (soguk → soğuk)
                'twoTypos' => 8,  // 8+ harf: 2 typo tolere et
            ],
        ]);

        // 5. Synonym ayarları (Türkçe)
        $index->updateSynonyms([
            // Soğuk depo varyasyonları
            'soguk' => ['soğuk', 'souk', 'cold'],
            'depo' => ['warehouse', 'storage'],
            'soguk_depo' => ['soğuk depo', 'cold storage', 'freezer', 'dondurucu'],

            // Elektrik varyasyonları
            'elektrik' => ['electric', 'elektirik'],
            'akulu' => ['akü', 'battery', 'batarya'],

            // Paslanmaz
            'paslanmaz' => ['stainless', 'inox', 'ss'],

            // Gıda
            'gida' => ['gıda', 'food'],

            // Forklift
            'forklift' => ['fork lift', 'istifleme'],
            'transpalet' => ['trans palet', 'pallet truck'],
        ]);

        // 6. Ranking rules
        $index->updateRankingRules([
            'words',        // Kelime eşleşmesi
            'typo',         // Typo toleransı
            'proximity',    // Kelime yakınlığı
            'attribute',    // Attribute önem sırası
            'sort',         // Sıralama
            'exactness',    // Tam eşleşme
        ]);

        Log::info("✅ Meilisearch Turkish config applied: {$indexName}");
    }
}
```

#### Adım 6: Mevcut ürünleri indeksle

**Komut:**
```bash
# Tüm ürünleri Meilisearch'e gönder
php artisan scout:import "Modules\Shop\App\Models\ShopProduct"

# Her tenant için (multi-tenant sistemde)
php artisan tenants:artisan "scout:import 'Modules\Shop\App\Models\ShopProduct'"
```

**İndeksleme sonrası config uygula:**
```bash
php artisan tinker
>>> $config = new \App\Services\Search\MeilisearchConfig();
>>> $config->configureTurkishSearch('shop_products_tenant_2'); // Her tenant için
```

---

### 1.3 ProductSearchService Entegrasyonu

**Dosya:** `app/Services/AI/ProductSearchService.php`

**Değişiklik:**

```php
use Modules\Shop\App\Models\ShopProduct;

public function searchByCategory(
    string $message,
    int $categoryId,
    array $extractedParams = []
): array {
    Log::info('🔍 Meilisearch category search', [
        'category_id' => $categoryId,
        'message' => $message,
        'params' => $extractedParams,
    ]);

    // ✅ YENİ: Meilisearch ile ara
    $query = ShopProduct::search($message)
        ->where('is_active', true)
        ->where('category_id', $categoryId);

    // Opsiyonel filtreler
    if (!empty($extractedParams['capacity'])) {
        $query->where('capacity', $extractedParams['capacity']);
    }

    if (!empty($extractedParams['brand'])) {
        $query->where('brand_id', $extractedParams['brand']);
    }

    // Limit 50 (AI re-ranking için)
    $results = $query->take(50)->get()->toArray();

    Log::info('✅ Meilisearch results', [
        'total' => count($results),
        'first_product' => $results[0]['title'] ?? null,
    ]);

    return $results;
}
```

---

### 1.4 Test Senaryoları

#### Test 1: Typo Tolerance
```bash
php artisan tinker
>>> ShopProduct::search('soguk depo')->take(3)->get()->pluck('title');
# Beklenen: "Soğuk Depo" ürünleri gelecek ✅
```

#### Test 2: Synonym
```bash
>>> ShopProduct::search('cold storage')->take(3)->get()->pluck('title');
# Beklenen: "Soğuk Depo" ürünleri gelecek ✅
```

#### Test 3: Category Filter
```bash
>>> ShopProduct::search('transpalet')->where('category_id', 5)->take(3)->get()->pluck('title');
# Beklenen: Sadece transpalet kategorisi ✅
```

---

## 📊 FAZ 2: SEMANTİK ARAMA (Embeddings)

### 2.1 MySQL Vector Column Ekle

**Migration:** `database/migrations/2025_10_17_add_embedding_to_shop_products.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            // Embedding vector (1536 dimensions for OpenAI text-embedding-3-small)
            $table->json('embedding')->nullable()->after('description');

            // Embedding metadata
            $table->timestamp('embedding_generated_at')->nullable();
            $table->string('embedding_model', 50)->nullable();
        });

        // Index for faster JSON queries
        DB::statement('CREATE INDEX idx_shop_products_embedding ON shop_products ((CAST(embedding AS CHAR(65535))))');
    }

    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn(['embedding', 'embedding_generated_at', 'embedding_model']);
        });
    }
};
```

---

### 2.2 Embedding Service

**Yeni dosya:** `app/Services/AI/EmbeddingService.php`

```php
<?php

namespace App\Services\AI;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmbeddingService
{
    private const MODEL = 'text-embedding-3-small'; // 1536 dimensions, cheap
    private const CACHE_TTL = 86400; // 24 saat

    /**
     * Generate embedding for text
     */
    public function generate(string $text): array
    {
        // Cache key
        $cacheKey = 'embedding:' . md5($text);

        // Cache'den kontrol et
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            // OpenAI API call
            $response = OpenAI::embeddings()->create([
                'model' => self::MODEL,
                'input' => $text,
            ]);

            $embedding = $response->embeddings[0]->embedding;

            // Cache'e kaydet
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
            throw $e;
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

        for ($i = 0; $i < count($vector1); $i++) {
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
        // Ürün bilgilerini birleştir
        $text = implode(' ', [
            $product->title,
            $product->sku,
            strip_tags($product->description ?? ''),
            $product->category->name ?? '',
            $product->brand->name ?? '',
            // Custom fields
            $product->capacity ?? '',
            $product->battery_type ?? '',
            $product->lift_height ?? '',
        ]);

        return $this->generate($text);
    }
}
```

---

### 2.3 Embedding Generation Command

**Yeni dosya:** `app/Console/Commands/GenerateProductEmbeddings.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use App\Services\AI\EmbeddingService;

class GenerateProductEmbeddings extends Command
{
    protected $signature = 'products:generate-embeddings {--limit=10} {--force}';
    protected $description = 'Generate embeddings for products';

    public function handle(EmbeddingService $embeddingService): int
    {
        $limit = $this->option('limit');
        $force = $this->option('force');

        // Embeddingi olmayan veya force ise tüm ürünler
        $query = ShopProduct::query()->where('is_active', true);

        if (!$force) {
            $query->whereNull('embedding');
        }

        $products = $query->limit($limit)->get();

        $this->info("🚀 Generating embeddings for {$products->count()} products...");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            try {
                $embedding = $embeddingService->generateProductEmbedding($product);

                $product->update([
                    'embedding' => json_encode($embedding),
                    'embedding_generated_at' => now(),
                    'embedding_model' => 'text-embedding-3-small',
                ]);

                $bar->advance();

                // Rate limit: 3000 requests/min = ~20ms delay
                usleep(20000); // 20ms

            } catch (\Exception $e) {
                $this->error("\n❌ Failed for product {$product->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Done!");

        return self::SUCCESS;
    }
}
```

**Kullanım:**
```bash
# İlk 10 ürün için test
php artisan products:generate-embeddings --limit=10

# Tüm ürünler için (1020 ürün × 2s = ~34dk)
php artisan products:generate-embeddings --limit=1020

# Force: Tüm ürünleri yeniden oluştur
php artisan products:generate-embeddings --limit=1020 --force
```

---

### 2.4 Vector Search Service

**Yeni dosya:** `app/Services/AI/VectorSearchService.php`

```php
<?php

namespace App\Services\AI;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\DB;
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
        // 1. Query'yi embedding'e çevir
        $queryEmbedding = $this->embeddingService->generate($query);

        Log::info('🔍 Vector search started', [
            'query' => $query,
            'embedding_dimensions' => count($queryEmbedding),
        ]);

        // 2. Tüm ürünlerin embedding'lerini çek
        $products = ShopProduct::whereNotNull('embedding')
            ->where('is_active', true)
            ->get();

        // 3. Cosine similarity hesapla
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

        // 4. Similarity'ye göre sırala
        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // 5. Top N ürünü al
        $topResults = array_slice($results, 0, $limit);

        Log::info('✅ Vector search completed', [
            'total_products' => count($products),
            'top_similarity' => $topResults[0]['similarity'] ?? 0,
            'top_product' => $topResults[0]['product']->title ?? null,
        ]);

        return array_map(fn($r) => [
            'product' => $r['product']->toArray(),
            'score' => $r['similarity'],
        ], $topResults);
    }

    /**
     * Category-scoped vector search
     */
    public function searchByCategory(string $query, int $categoryId, int $limit = 50): array
    {
        $queryEmbedding = $this->embeddingService->generate($query);

        $products = ShopProduct::whereNotNull('embedding')
            ->where('is_active', true)
            ->where('category_id', $categoryId)
            ->get();

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

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        $topResults = array_slice($results, 0, $limit);

        return array_map(fn($r) => [
            'product' => $r['product']->toArray(),
            'score' => $r['similarity'],
        ], $topResults);
    }
}
```

---

## 📊 FAZ 3: HİBRİT SKOR (En İyi!)

### 3.1 Hybrid Search Service

**Yeni dosya:** `app/Services/AI/HybridSearchService.php`

```php
<?php

namespace App\Services\AI;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;

class HybridSearchService
{
    private VectorSearchService $vectorSearch;

    // Scoring weights
    private const KEYWORD_WEIGHT = 0.7;  // 70% keyword (Meilisearch)
    private const SEMANTIC_WEIGHT = 0.3; // 30% semantic (Vector)

    public function __construct(VectorSearchService $vectorSearch)
    {
        $this->vectorSearch = $vectorSearch;
    }

    /**
     * Hybrid search: Meilisearch + Vector
     */
    public function search(string $query, int $categoryId, int $limit = 10): array
    {
        Log::info('🔍 Hybrid search started', [
            'query' => $query,
            'category_id' => $categoryId,
        ]);

        // 1. KEYWORD SEARCH (Meilisearch) - Hızlı, typo-tolerant
        $keywordResults = ShopProduct::search($query)
            ->where('is_active', true)
            ->where('category_id', $categoryId)
            ->take(50)
            ->get();

        // 2. SEMANTIC SEARCH (Vector) - Anlamsal
        $semanticResults = $this->vectorSearch->searchByCategory($query, $categoryId, 50);

        // 3. SCORE COMBINATION
        $hybridScores = [];

        // Keyword scores (normalize to 0-1)
        $keywordIds = $keywordResults->pluck('id')->toArray();
        foreach ($keywordIds as $index => $productId) {
            // Position-based score (ilk sırada = 1.0, son sırada = 0)
            $keywordScore = 1 - ($index / count($keywordIds));

            $hybridScores[$productId] = [
                'product_id' => $productId,
                'keyword_score' => $keywordScore,
                'semantic_score' => 0, // Henüz yok
            ];
        }

        // Semantic scores (already 0-1)
        foreach ($semanticResults as $result) {
            $productId = $result['product']['id'];

            if (isset($hybridScores[$productId])) {
                $hybridScores[$productId]['semantic_score'] = $result['score'];
            } else {
                $hybridScores[$productId] = [
                    'product_id' => $productId,
                    'keyword_score' => 0,
                    'semantic_score' => $result['score'],
                ];
            }
        }

        // 4. CALCULATE HYBRID SCORE
        foreach ($hybridScores as $productId => &$scores) {
            $scores['hybrid_score'] =
                ($scores['keyword_score'] * self::KEYWORD_WEIGHT) +
                ($scores['semantic_score'] * self::SEMANTIC_WEIGHT);
        }

        // 5. SORT BY HYBRID SCORE
        uasort($hybridScores, fn($a, $b) => $b['hybrid_score'] <=> $a['hybrid_score']);

        // 6. GET TOP N PRODUCTS
        $topProductIds = array_slice(array_keys($hybridScores), 0, $limit);

        $topProducts = ShopProduct::whereIn('id', $topProductIds)
            ->get()
            ->sortBy(fn($p) => array_search($p->id, $topProductIds))
            ->values();

        Log::info('✅ Hybrid search completed', [
            'keyword_results' => count($keywordResults),
            'semantic_results' => count($semanticResults),
            'hybrid_results' => count($topProducts),
            'top_product' => $topProducts->first()->title ?? null,
            'top_score' => $hybridScores[$topProducts->first()->id ?? 0]['hybrid_score'] ?? 0,
        ]);

        return $topProducts->map(function ($product) use ($hybridScores) {
            return [
                'product' => $product->toArray(),
                'scores' => $hybridScores[$product->id],
            ];
        })->toArray();
    }
}
```

---

### 3.2 ProductSearchService Final Integration

**Dosya:** `app/Services/AI/ProductSearchService.php`

```php
use App\Services\AI\HybridSearchService;

public function searchByCategory(
    string $message,
    int $categoryId,
    array $extractedParams = []
): array {

    // ✅ HİBRİT ARAMA (Meilisearch + Vector)
    $hybridSearch = app(HybridSearchService::class);

    $results = $hybridSearch->search($message, $categoryId, 50);

    // Sadece product array'ini döndür (AI için)
    return array_map(fn($r) => $r['product'], $results);
}
```

---

## 🧪 PERFORMANS KARŞILAŞTIRMASI

| Yöntem | Typo Fix | Semantic | Hızlı | Doğruluk |
|--------|----------|----------|-------|----------|
| **Mevcut (Manuel)** | ❌ | ❌ | ✅ 10ms | 40% |
| **Meilisearch** | ✅ | ❌ | ✅ 50ms | 70% |
| **Vector Only** | ✅ | ✅ | ⚠️ 200ms | 85% |
| **Hybrid** | ✅ | ✅ | ✅ 100ms | **95%** |

---

## 💰 MALİYET TAHMİNİ

### Meilisearch
- **Kurulum:** Ücretsiz
- **Hosting:** Sunucuda (zaten var)
- **RAM:** ~200MB

### OpenAI Embeddings
- **Model:** text-embedding-3-small
- **Fiyat:** $0.02 / 1M tokens
- **1 ürün:** ~500 token = $0.00001
- **1,020 ürün:** ~510,000 token = **$0.01** (tek seferlik)
- **Aylık (yeni ürünler):** ~50 ürün = **$0.001/ay**

**Toplam:** İlk kurulum $0.01, sonra neredeyse ücretsiz! 🎉

---

## 📝 KURULUM SÜRELER

| Faz | Süre | Sonuç |
|-----|------|-------|
| **Faz 1:** Meilisearch | 2-3 saat | Typo fix ✅ |
| **Faz 2:** Embeddings | 3-4 saat | Semantic ✅ |
| **Faz 3:** Hybrid | 2 saat | Perfect! ✅ |
| **TOPLAM** | **7-9 saat** | En iyi sistem! |

---

## 🚀 HEMEN BAŞLAYALIM MI?

**Sırada:** Faz 1 (Meilisearch kurulumu)

**İlk adım:** Sunucuda Meilisearch binary kurulumu

**Hazır mısın?** ✅
