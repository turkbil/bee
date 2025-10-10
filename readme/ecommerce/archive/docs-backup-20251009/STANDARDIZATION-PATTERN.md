# 🎯 E-Ticaret Migration Standardizasyon Pattern'i

## 📋 Referans: Portfolio Modülü Master Pattern

### 1. PRIMARY KEY İSİMLENDİRMESİ
```php
// ❌ YANLIŞ
$table->id()->comment('Kategori benzersiz ID');

// ✅ DOĞRU - Anlamlı primary key
$table->id('category_id');
$table->id('brand_id');
$table->id('product_id');
```

### 2. KOLON İSİMLENDİRMESİ

#### 2.1. Title/Name Alanı
```php
// ❌ YANLIŞ
$table->json('name')->comment('Kategori adı ({"tr":"Forklift","en":"Forklift"})');

// ✅ DOĞRU - Portfolio pattern'e göre "title"
$table->json('title')->comment('Kategori başlığı: {"tr": "Elektronik", "en": "Electronics", "vs.": "..."}');
```

#### 2.2. Slug Alanı
```php
// ❌ YANLIŞ - String slug
$table->string('slug')->unique()->comment('URL-dostu benzersiz slug');

// ✅ DOĞRU - JSON slug (çoklu dil)
$table->json('slug')->comment('Çoklu dil slug: {"tr": "elektronik", "en": "electronics", "vs.": "..."}');
```

#### 2.3. Description Alanı
```php
// ❌ YANLIŞ - Sektörel örnek
$table->json('description')->nullable()->comment('Kategori açıklaması (JSON çoklu dil)');

// ✅ DOĞRU - Genel açıklayıcı + vs.
$table->json('description')->nullable()->comment('Kategori açıklaması: {"tr": "Açıklama metni", "en": "Description text", "vs.": "..."}');
```

### 3. COMMENT YAPISI

#### 3.1. Genel Kurallar
- ❌ Sektörel örnekler kullanma (Forklift, EP Equipment, Transpalet)
- ✅ Genel, açıklayıcı örnekler kullan (Elektronik, Giyim, Ayakkabı)
- ✅ Çoklu dil için: `{"tr": "...", "en": "...", "vs.": "..."}`
- ✅ "vs." ile dinamik dil desteğini göster

#### 3.2. Örnekler
```php
// ❌ YANLIŞ - Sektörel
->comment('Marka adı ({"tr":"EP Equipment","en":"EP Equipment"})')

// ✅ DOĞRU - Genel + vs.
->comment('Marka başlığı: {"tr": "Marka Adı", "en": "Brand Name", "vs.": "..."}')

// ❌ YANLIŞ - Yetersiz
->comment('Ürün adı (JSON çoklu dil)')

// ✅ DOĞRU - Açıklayıcı + örnek + vs.
->comment('Ürün başlığı: {"tr": "Ürün Adı", "en": "Product Name", "vs.": "..."}')
```

### 4. JSON SLUG INDEX (MySQL 8.0+ / MariaDB 10.5+)

```php
// Migration sonuna eklenecek (Portfolio pattern)
use Illuminate\Support\Facades\DB;

// JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Tablo oluşturulduktan sonra
if (DB::getDriverName() === 'mysql') {
    $version = DB::selectOne('SELECT VERSION() as version')->version;

    // MySQL 8.0+ veya MariaDB 10.5+ kontrolü
    $isMariaDB = stripos($version, 'MariaDB') !== false;

    if ($isMariaDB) {
        // MariaDB için versiyon kontrolü (10.5+)
        preg_match('/(\d+\.\d+)/', $version, $matches);
        $mariaVersion = isset($matches[1]) ? (float) $matches[1] : 0;
        $supportsJsonIndex = $mariaVersion >= 10.5;
    } else {
        // MySQL için versiyon kontrolü (8.0+)
        $majorVersion = (int) explode('.', $version)[0];
        $supportsJsonIndex = $majorVersion >= 8;
    }

    if ($supportsJsonIndex) {
        // Config'den sistem dillerini al
        $systemLanguages = config('modules.system_languages', ['tr', 'en']);

        foreach ($systemLanguages as $locale) {
            DB::statement("
                ALTER TABLE shop_categories
                ADD INDEX shop_categories_slug_{$locale} (
                    (CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) AS CHAR(255)) COLLATE utf8mb4_unicode_ci)
                )
            ");
        }
    }
}
```

### 5. FOREIGN KEY YAPISISI

```php
// ❌ YANLIŞ - Standart id referansı
$table->foreignId('parent_id')->nullable();
$table->foreign('parent_id')
      ->references('id')
      ->on('shop_categories')
      ->onDelete('cascade');

// ✅ DOĞRU - Anlamlı primary key referansı
$table->unsignedBigInteger('parent_id')->nullable();
$table->foreign('parent_id')
      ->references('category_id')  // Anlamlı primary key
      ->on('shop_categories')
      ->onDelete('cascade');
```

### 6. INDEX YAPISI

```php
// Portfolio pattern'e göre
$table->index('created_at');
$table->index('updated_at');
$table->index('deleted_at');
$table->index(['is_active', 'deleted_at', 'sort_order'], 'shop_categories_active_deleted_sort_idx');
```

### 7. TABLO YAPISI ŞEKİLLENDİRMESİ

```php
Schema::create('shop_categories', function (Blueprint $table) {
    // 1. Primary Key
    $table->id('category_id');

    // 2. Foreign Keys / Relations
    $table->unsignedBigInteger('parent_id')->nullable();

    // 3. Basic Info (JSON fields)
    $table->json('title')->comment('...');
    $table->json('slug')->comment('...');
    $table->json('description')->nullable()->comment('...');

    // 4. Media
    $table->string('image_url')->nullable()->comment('...');

    // 5. Status & Display
    $table->boolean('is_active')->default(true)->index();
    $table->integer('sort_order')->default(0)->index();

    // 6. Timestamps
    $table->timestamps();
    $table->softDeletes();

    // 7. Foreign Keys
    $table->foreign('parent_id')
          ->references('category_id')
          ->on('shop_categories')
          ->onDelete('cascade');

    // 8. Indexes
    $table->index('created_at');
    $table->index('updated_at');
    $table->index('deleted_at');
});

// 9. JSON Slug Index (sonradan)
// ... (yukarıdaki JSON index kodu)

// NOT: SEO Yönetimi
// SEO kolonları (seo_title, seo_description, seo_keywords) KALDIRILDI
// Universal SEO sistemi (SeoManagement modülü) kullanılacak
```

### 8. FIELD TİPLERİ GÜNCELLEMELERİ

| Eski | Yeni | Açıklama |
|------|------|----------|
| `json('name')` | `json('title')` | Portfolio pattern'e uyum |
| `string('slug')` | `json('slug')` | Çoklu dil desteği |
| `id()` | `id('tablename_id')` | Anlamlı primary key |
| `json('seo_title')` | ❌ Kaldırıldı | Universal SEO sistemi kullanılacak |
| `json('seo_description')` | ❌ Kaldırıldı | Universal SEO sistemi kullanılacak |
| `json('seo_keywords')` | ❌ Kaldırıldı | Universal SEO sistemi kullanılacak |
| Comment: `"tr":"..."` | Comment: `"tr":"...", "vs.":"..."` | Dinamik dil gösterimi |
| Sektörel örnek | Genel örnek | Evrensel kullanım |

### 9. ÖNCELIK SIRASI

1. ✅ Primary key isimlerini düzelt (`id('category_id')`)
2. ✅ `name` → `title` dönüşümü
3. ✅ `slug` → JSON slug dönüşümü
4. ✅ SEO kolonlarını kaldır (seo_title, seo_description, seo_keywords)
5. ✅ Comment'leri güncelle (sektörel → genel, + "vs.")
6. ✅ Foreign key referanslarını güncelle
7. ✅ JSON slug index ekle
8. ✅ Timestamp index'leri ekle

### 10. ÖRNEK DÖNÜŞÜM

#### ÖNCESİ:
```php
Schema::create('shop_categories', function (Blueprint $table) {
    $table->id()->comment('Kategori benzersiz ID');
    $table->foreignId('parent_id')->nullable();
    $table->json('name')->comment('Kategori adı ({"tr":"Forklift","en":"Forklift"})');
    $table->string('slug')->unique()->comment('URL-dostu slug');
    $table->json('description')->nullable()->comment('Kategori açıklaması (JSON çoklu dil)');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

#### SONRASI:
```php
use Illuminate\Support\Facades\DB;

Schema::create('shop_categories', function (Blueprint $table) {
    $table->id('category_id');
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->json('title')->comment('Kategori başlığı: {"tr": "Elektronik", "en": "Electronics", "vs.": "..."}');
    $table->json('slug')->comment('Çoklu dil slug: {"tr": "elektronik", "en": "electronics", "vs.": "..."}');
    $table->json('description')->nullable()->comment('Kategori açıklaması: {"tr": "Açıklama metni", "en": "Description text", "vs.": "..."}');
    $table->boolean('is_active')->default(true)->index();
    $table->integer('sort_order')->default(0)->index();
    $table->timestamps();
    $table->softDeletes();

    // Foreign key
    $table->foreign('parent_id')
          ->references('category_id')
          ->on('shop_categories')
          ->onDelete('cascade');

    // İlave indeksler
    $table->index('created_at');
    $table->index('updated_at');
    $table->index('deleted_at');
    $table->index(['is_active', 'deleted_at', 'sort_order'], 'shop_categories_active_deleted_sort_idx');
});

// JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+)
if (DB::getDriverName() === 'mysql') {
    // ... (yukarıdaki kod)
}
```

## 🎯 SONUÇ

Bu standardizasyon:
- ✅ Portfolio pattern'ini takip eder
- ✅ Çoklu dil desteğini dinamik yapar
- ✅ Performans optimizasyonu sağlar (JSON indexes)
- ✅ Kod okunabilirliğini artırır
- ✅ Gelecek genişlemelere hazır
- ✅ Universal SEO sistemine geçiş için hazırlık (SEO kolonları kaldırıldı)

**NOT: SEO Yönetimi**
- SEO ayarları artık tablo bazlı değil, Universal SEO sistemi ile yönetilir
- SeoManagement modülü üzerinden tüm entity'lere (Product, Category, Brand, vb.) SEO desteği sağlanır
- Model'larda SEO kolonları bulunmaz (seo_title, seo_description, seo_keywords)
