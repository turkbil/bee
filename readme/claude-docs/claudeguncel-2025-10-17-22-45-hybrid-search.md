# 🚀 HYBRID SEARCH SYSTEM - Meilisearch + OpenAI Embeddings

**Tarih**: 2025-10-17 22:45
**Commit**: `0e3954ad`
**Durum**: ✅ Tamamlandı

---

## 📋 ÖZET

Akıllı ürün arama sistemi 3 katmanlı hibrit yapıya geçirildi:
1. **Meilisearch** (70%) - Typo-tolerant keyword search
2. **OpenAI Embeddings** (30%) - Semantic similarity search
3. **Hybrid Scoring** - Ağırlıklı sonuç birleştirme

**Sonuç**: "soguk depo" yazılsa bile "soğuk depo" ürünlerini bulacak + semantic anlam yakınlığı ile ilgili ürünleri önerecek.

---

## 🎯 PROBLEM

**Önceki Durum**:
- Manuel filtreleme sistemi
- Typo'lara karşı hassas
- Semantic anlam yok (örn: "cold storage" → "soğuk depo" bağlantısı yok)
- Her typo için manuel synonym ekleme gerekiyordu

**İstek**:
> "soguk hava deposunda transpalet istiyorum" → Meilisearch + Vector search ile akıllı arama

---

## ✅ YAPILAN İŞLEMLER

### FAZ 1: Meilisearch Entegrasyonu

#### 1.1 Kurulum
```bash
# Meilisearch kurulumu
brew install meilisearch

# Meilisearch başlat
brew services start meilisearch

# Laravel Scout kurulumu
composer require laravel/scout
composer require meilisearch/meilisearch-php
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

#### 1.2 .env Yapılandırması
```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=ikYJZrMzeMqklZGmuaTddcsmafIpxLR2fAlhwSDRmQY=
SCOUT_QUEUE=false
```

#### 1.3 ShopProduct Model Güncellemesi
**Dosya**: `Modules/Shop/app/Models/ShopProduct.php`

```php
use Laravel\Scout\Searchable;

class ShopProduct extends BaseModel
{
    use Searchable;

    public function toSearchableArray(): array
    {
        $locale = app()->getLocale();
        return [
            'product_id' => $this->product_id,
            'title' => $this->getTranslated('title', $locale),
            'slug' => $this->getTranslated('slug', $locale),
            'sku' => $this->sku ?? '',
            'category_id' => $this->category_id,
            'is_active' => $this->is_active,
        ];
    }

    public function searchableAs(): string
    {
        if (tenancy()->initialized) {
            return 'shop_products_tenant_' . tenant('id');
        }
        return 'shop_products';
    }

    public function getScoutKey(): mixed
    {
        return $this->product_id; // ⚠️ KRİTİK: 'id' değil 'product_id'
    }

    public function getScoutKeyName(): string
    {
        return 'product_id';
    }
}
```

#### 1.4 MeilisearchConfig Service
**Dosya**: `app/Services/Search/MeilisearchConfig.php`

**Özellikler**:
- ✅ Türkçe typo tolerance (soguk → soğuk)
- ✅ 15 synonym grubu
- ✅ Filterable/sortable/searchable attributes

**Synonyms**:
```php
'soguk' => ['soğuk', 'souk', 'cold'],
'depo' => ['warehouse', 'storage'],
'elektrik' => ['electric', 'elektirik'],
'transpalet' => ['trans palet', 'pallet truck'],
// ... 15 grup toplam
```

#### 1.5 İndeksleme
```bash
# Tüm ürünleri Meilisearch'e aktar
php artisan scout:import "Modules\Shop\App\Models\ShopProduct"

# Sonuç: 1,020 ürün indekslendi
```

#### 1.6 Test
```php
// TEST: "soguk depo" (typo var!)
$results = ShopProduct::search('soguk depo')->get();

// SONUÇ: ✅ 3 ürün bulundu!
// - İXTİF EPT20-20ETC - 2.0 Ton Soğuk Depo Transpalet
// - İXTİF EPT20-13ETC - 1.3 Ton Soğuk Depo Transpalet
// - ...
```

---

### FAZ 2: OpenAI Embeddings

#### 2.1 Kurulum
```bash
composer require openai-php/laravel
```

#### 2.2 Migration
**Dosya**: `database/migrations/2025_10_17_221722_add_embedding_to_shop_products.php`

```php
Schema::table('shop_products', function (Blueprint $table) {
    $table->json('embedding')->nullable()->after('body');
    $table->timestamp('embedding_generated_at')->nullable();
    $table->string('embedding_model', 50)->default('text-embedding-3-small');
});
```

```bash
php artisan migrate
```

#### 2.3 EmbeddingService
**Dosya**: `app/Services/AI/EmbeddingService.php`

**Özellikler**:
- ✅ OpenAI API entegrasyonu
- ✅ Caching (24 saat, md5 hash key)
- ✅ Cosine similarity hesaplama
- ✅ Error handling (zero vector fallback)

**Model**: `text-embedding-3-small` (1536 dimensions, $0.02/1M tokens)

**Kod Örneği**:
```php
$service = new EmbeddingService();

// Embedding oluştur
$embedding = $service->generate("soğuk depo transpalet");
// → [0.0234, -0.0156, 0.0789, ...] (1536 dim)

// Similarity hesapla
$similarity = $service->cosineSimilarity($vector1, $vector2);
// → 0.87 (0-1 arası)
```

#### 2.4 GenerateProductEmbeddings Command
**Dosya**: `app/Console/Commands/GenerateProductEmbeddings.php`

**Kullanım**:
```bash
# İlk 10 ürün için embedding oluştur
php artisan products:generate-embeddings --limit=10

# Tüm ürünler için (force)
php artisan products:generate-embeddings --limit=1000 --force
```

**Özellikler**:
- ✅ Batch processing
- ✅ Progress bar
- ✅ Rate limiting (20ms delay)
- ✅ Force flag (mevcut embeddinglari override)

#### 2.5 Test
```bash
# 2 ürün için test
php artisan products:generate-embeddings --limit=2

# SONUÇ:
# ✅ Product 1: 1536 dimensions generated
# ✅ Product 2: 1536 dimensions generated
```

---

### FAZ 3: Hybrid Search

#### 3.1 VectorSearchService
**Dosya**: `app/Services/AI/VectorSearchService.php`

**İşlev**:
1. Query için embedding oluştur
2. Tüm ürünlerin embeddinglerini al
3. Cosine similarity hesapla
4. Similarity'ye göre sırala
5. Top N döndür

```php
public function search(string $query, int $limit = 50): array
{
    $queryEmbedding = $this->embeddingService->generate($query);

    $products = ShopProduct::whereNotNull('embedding')
        ->where('is_active', true)
        ->get();

    foreach ($products as $product) {
        $similarity = $this->embeddingService->cosineSimilarity(
            $queryEmbedding,
            json_decode($product->embedding, true)
        );
        $results[] = ['product' => $product, 'similarity' => $similarity];
    }

    usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
    return array_slice($results, 0, $limit);
}
```

#### 3.2 HybridSearchService
**Dosya**: `app/Services/AI/HybridSearchService.php`

**Ağırlıklar**:
```php
private const KEYWORD_WEIGHT = 0.7;  // 70% Meilisearch
private const SEMANTIC_WEIGHT = 0.3; // 30% Vector
```

**Algoritma**:
```
1. KEYWORD SEARCH (Meilisearch)
   "soguk depo" → 50 sonuç (typo-tolerant)

2. SEMANTIC SEARCH (Vector)
   "soguk depo" → 50 sonuç (meaning-based)

3. POSITION-BASED SCORING
   keyword_score = 1 - (position / total)
   semantic_score = 1 - (position / total)

4. HYBRID SCORE
   hybrid_score = (keyword_score * 0.7) + (semantic_score * 0.3)

5. SORT & RETURN
   Top 10 sonuç
```

**Kod**:
```php
public function search(string $query, ?int $categoryId = null, int $limit = 10): array
{
    // 1. Meilisearch (keyword)
    $keywordResults = ShopProduct::search($query)
        ->where('is_active', true)
        ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
        ->take(50)
        ->get();

    // 2. Vector (semantic)
    $semanticResults = $this->vectorSearch->search($query, 50);

    // 3. Combine scores
    foreach ($hybridScores as &$scores) {
        $scores['hybrid_score'] =
            ($scores['keyword_score'] * self::KEYWORD_WEIGHT) +
            ($scores['semantic_score'] * self::SEMANTIC_WEIGHT);
    }

    // 4. Sort & return top N
    uasort($hybridScores, fn($a, $b) => $b['hybrid_score'] <=> $a['hybrid_score']);
    return array_slice($topProducts, 0, $limit);
}
```

---

### FINAL: ProductSearchService Entegrasyonu

**Dosya**: `app/Services/AI/ProductSearchService.php`

**Değişiklikler**:
```php
class ProductSearchService
{
    protected HybridSearchService $hybridSearch; // ← YENİ

    public function __construct(HybridSearchService $hybridSearch)
    {
        $this->hybridSearch = $hybridSearch;
        // ...
    }

    public function searchProducts(string $userMessage, array $options = []): array
    {
        // ...

        // 🚀 YENİ: Önce hybrid search dene
        try {
            $hybridResults = $this->hybridSearch->search(
                $normalizedMessage,
                $detectedCategory['category_id'] ?? null,
                10
            );

            if (!empty($hybridResults)) {
                return $this->formatResults(
                    array_column($hybridResults, 'product'),
                    'hybrid',
                    $detectedCategory
                );
            }
        } catch (\Exception $e) {
            // Fallback to manual search
        }

        // Fallback: Category → Exact → Phonetic
        // ...
    }
}
```

**Arama Akışı**:
```
User Query: "soguk hava deposunda transpalet istiyorum"
    ↓
Normalize: "soguk hava deposunda transpalet istiyorum"
    ↓
Detect Category: "transpalet" → category_id = 2
    ↓
HybridSearch:
  - Meilisearch: "soguk" → "soğuk" (typo fix) → 50 results
  - Vector: semantic similarity → 50 results
  - Hybrid score → Top 10
    ↓
✅ Return: 10 en iyi eşleşme
```

---

## 📊 TEST SONUÇLARI

### Test 1: Typo Tolerance (Meilisearch)
```bash
Input: "soguk depo"  # ← typo var!
Output: ✅ 3 ürün bulundu
- İXTİF EPT20-20ETC - 2.0 Ton Soğuk Depo Transpalet
- İXTİF EPT20-13ETC - 1.3 Ton Soğuk Depo Transpalet
```

### Test 2: Embedding Generation
```bash
php artisan products:generate-embeddings --limit=2

Output:
✅ Product 1: 1536 dimensions
✅ Product 2: 1536 dimensions
✅ Embedding generated - text_length: 234, dimensions: 1536
```

### Test 3: Hybrid Search (Integration)
```bash
Status: ⏳ Entegrasyon tamamlandı, production test bekliyor
Next Step: 1,020 ürün için embedding generate et
```

---

## 📁 OLUŞTURULAN DOSYALAR

### Yeni Dosyalar (8 adet)
```
app/Console/Commands/GenerateProductEmbeddings.php
app/Services/AI/EmbeddingService.php
app/Services/AI/HybridSearchService.php
app/Services/AI/VectorSearchService.php
app/Services/Search/MeilisearchConfig.php
config/openai.php
config/scout.php
database/migrations/2025_10_17_221722_add_embedding_to_shop_products.php
```

### Güncellenen Dosyalar (3 adet)
```
Modules/Shop/app/Models/ShopProduct.php
app/Services/AI/ProductSearchService.php
composer.json
```

---

## 🔧 SİSTEM DURUMU

| Metrik | Değer |
|--------|-------|
| Total Products | 1,020 |
| Meilisearch Indexed | 1,020 |
| Embeddings Generated | 2 (test) |
| Search Layers | 4 (hybrid, category, exact, phonetic) |
| Meilisearch Version | 1.23.0 |
| OpenAI Model | text-embedding-3-small |
| Embedding Dimensions | 1,536 |
| Embedding Cost | $0.02/1M tokens |

---

## 🚀 SONRAKİ ADIMLAR

### 1. Production Embedding Generation
```bash
# Tüm ürünler için embedding oluştur (1,020 products)
php artisan products:generate-embeddings --limit=1020

# Tahmin edilen süre: ~10-15 dakika (20ms delay * 1020 products)
# Tahmin edilen maliyet: ~$0.05-0.10
```

### 2. Chatbot Test
```bash
# Test sorgusu
"soguk hava deposunda transpalet istiyorum"

# Beklenen sonuç:
# ✅ "soguk" → "soğuk" (Meilisearch typo fix)
# ✅ "depo" → semantic match ile "cold storage" ürünleri
# ✅ "transpalet" → category detection
# ✅ Hybrid scoring ile en iyi 10 sonuç
```

### 3. Performance Monitoring
```bash
# Laravel log kontrol
tail -f storage/logs/laravel.log | grep "Hybrid"

# Beklenen log çıktısı:
# [INFO] 🔍 Hybrid search started - query: soguk depo, category_id: 2
# [INFO] ✅ Hybrid search completed - results: 10, top_product: İXTİF EPT20-20ETC
```

### 4. Cron Job (Opsiyonel)
```bash
# Yeni ürünler için otomatik embedding
# Laravel Task Scheduler'a ekle:

# app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('products:generate-embeddings --limit=50')
        ->daily()
        ->at('03:00'); // Gece 3'te yeni ürünler için embedding oluştur
}
```

---

## 🎯 KULLANIM ÖRNEĞİ

### Backend (ProductSearchService)
```php
$productSearchService = app(ProductSearchService::class);

$results = $productSearchService->searchProducts(
    "soguk hava deposunda transpalet istiyorum"
);

// Sonuç:
[
    'products' => [
        [
            'product_id' => 296,
            'title' => 'İXTİF EPT20-20ETC - 2.0 Ton Soğuk Depo Transpalet',
            'scores' => [
                'keyword_score' => 1.0,
                'semantic_score' => 0.87,
                'hybrid_score' => 0.961
            ]
        ],
        // ... 9 more
    ],
    'count' => 10,
    'search_layer' => 'hybrid',
    'detected_category' => [
        'category_id' => 2,
        'category_name' => 'Transpalet'
    ]
]
```

### Frontend (ChatWidget API)
```javascript
// Kullanıcı mesajı
const userMessage = "soguk hava deposunda transpalet istiyorum";

// API call
const response = await fetch('/api/ai/v1/chat', {
    method: 'POST',
    body: JSON.stringify({
        message: userMessage,
        conversation_id: conversationId
    })
});

// Hybrid search otomatik çalışır
// ProductSearchService → HybridSearchService → Meilisearch + Vector
```

---

## 🐛 HATALAR VE DÜZELTMELER

### Hata 1: Primary Key Mismatch
**Problem**: Scout search 0 sonuç döndürüyordu
**Sebep**: Model `product_id` kullanıyor ama Scout `id` arıyordu
**Çözüm**: `getScoutKey()` ve `getScoutKeyName()` override edildi

### Hata 2: OpenAI Facade Not Found
**Problem**: `Class "OpenAI\Laravel\Facades\OpenAI" not found`
**Sebep**: Facade düzgün register edilmemiş
**Çözüm**: Direct client kullanımı `OpenAI::client(config('openai.api_key'))`

### Hata 3: Autoload Cache
**Problem**: Yeni service'ler tanınmıyordu
**Çözüm**: `composer dump-autoload`

---

## 📚 KAYNAKLAR

- [Meilisearch Docs](https://www.meilisearch.com/docs)
- [Laravel Scout](https://laravel.com/docs/11.x/scout)
- [OpenAI Embeddings](https://platform.openai.com/docs/guides/embeddings)
- [openai-php/laravel](https://github.com/openai-php/laravel)

---

## ✅ CHECKLIST

- [x] Meilisearch kurulumu
- [x] Scout entegrasyonu
- [x] ShopProduct Searchable trait
- [x] MeilisearchConfig Turkish settings
- [x] 1,020 ürün indekslendi
- [x] Typo tolerance test ("soguk" → "soğuk") ✅
- [x] OpenAI embeddings service
- [x] Migration (embedding columns)
- [x] GenerateProductEmbeddings command
- [x] 2 test ürün için embedding ✅
- [x] VectorSearchService
- [x] HybridSearchService
- [x] ProductSearchService entegrasyonu
- [x] Git commit
- [ ] Production: 1,020 ürün için embedding (sonraki adım)
- [ ] Production test: Full chatbot flow
- [ ] Performance monitoring

---

**Commit**: `0e3954ad`
**Tarih**: 2025-10-17 22:45
**Süre**: ~3 saat
**Satır Değişikliği**: +1,238, -3

🤖 Generated with [Claude Code](https://claude.com/claude-code)
