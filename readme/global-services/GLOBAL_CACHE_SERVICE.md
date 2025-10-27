# Global Cache Service - Kullanım Kılavuzu

## 🎯 Amaç
PageCacheService artık global bir sistem olarak tasarlandı. Tüm modüllerde model-agnostic cache sistemi kullanılabilir ve her modülün cache ihtiyaçlarına göre özelleştirilebilir.

## 📍 Dosya Konumu
```
app/Services/GlobalCacheService.php
```

## 🚀 Temel Kullanım

### Model Cache (Herhangi Bir Model İçin)
```php
use App\Services\GlobalCacheService;
use Modules\Page\App\Models\Page;
use Modules\Portfolio\App\Models\Portfolio;

// Page modeli için SEO ile birlikte
$page = GlobalCacheService::getModelWithRelations(
    Page::class, 
    $pageId, 
    ['seoSetting']
);

// Portfolio modeli için kategori ile birlikte
$portfolio = GlobalCacheService::getModelWithRelations(
    Portfolio::class, 
    $portfolioId, 
    ['category']
);

// Blog modeli için yazar ve yorumlar ile birlikte
$blog = GlobalCacheService::getModelWithRelations(
    Blog::class, 
    $blogId, 
    ['author', 'comments']
);
```

### Cache Temizleme
```php
// Belirli model instance'ını temizle
GlobalCacheService::clearModelCache(Page::class, $pageId);

// Model sınıfının tüm cache'lerini temizle
GlobalCacheService::clearModelCache(Page::class);

// Tüm cache'i temizle
GlobalCacheService::clearAllCache();
```

### Cache Kontrolü
```php
// Cache'de var mı kontrol et
$hasCached = GlobalCacheService::hasCached(Page::class, $pageId, ['seoSetting']);

// Cache istatistikleri
$stats = GlobalCacheService::getCacheStats();
```

## 📋 API Referansı

### Temel Metodlar
| Metod | Açıklama | Parametreler |
|-------|----------|-------------|
| `getModelWithRelations()` | Model'i relation'larla cache'le | `$modelClass`, `$modelId`, `$relations` |
| `clearModelCache()` | Model cache'ini temizle | `$modelClass`, `$modelId` (optional) |
| `clearAllCache()` | Tüm cache'i temizle | - |
| `hasCached()` | Cache durumu kontrol | `$modelClass`, `$modelId`, `$relations` |
| `getCacheStats()` | Cache istatistikleri | - |

### Backward Compatibility (Page Modülü İçin)
| Metod | Açıklama | Parametreler |
|-------|----------|-------------|
| `getPageWithSeo()` | Page için SEO cache | `$pageId` |
| `clearCache()` | Page cache temizle | `$pageId` (optional) |

## 🎨 Kullanım Örnekleri

### 1. Page Modülü (Mevcut Kullanım)
```php
// Eski API uyumluluğu - değişiklik gerekmiyor
$page = GlobalCacheService::getPageWithSeo($pageId);

// Yeni API ile de kullanılabilir
$page = GlobalCacheService::getModelWithRelations(
    \Modules\Page\App\Models\Page::class,
    $pageId,
    ['seoSetting']
);
```

### 2. Portfolio Modülü
```php
use App\Services\GlobalCacheService;
use Modules\Portfolio\App\Models\Portfolio;

// Portfolio ManageComponent'te
class PortfolioManageComponent extends Component
{
    protected function getCachedPortfolioWithCategory()
    {
        if (!$this->portfolioId) {
            return null;
        }
        
        return GlobalCacheService::getModelWithRelations(
            Portfolio::class,
            $this->portfolioId,
            ['category', 'tags']
        );
    }
    
    public function save()
    {
        // Kaydetme işlemleri...
        
        // Cache'i temizle
        GlobalCacheService::clearModelCache(Portfolio::class, $this->portfolioId);
    }
}
```

### 3. Blog Modülü
```php
use App\Services\GlobalCacheService;
use Modules\Blog\App\Models\Blog;

// BlogRepository'de
class BlogRepository
{
    public function findByIdWithRelations(int $id): ?Blog
    {
        // Admin panelinde global cache kullan
        if (request()->is('admin*')) {
            return GlobalCacheService::getModelWithRelations(
                Blog::class,
                $id,
                ['author', 'categories', 'seoSetting']
            );
        }
        
        // Public sayfalar için Laravel cache
        return Cache::remember("blog_{$id}", 3600, function() use ($id) {
            return Blog::with(['author', 'categories', 'seoSetting'])->find($id);
        });
    }
}
```

### 4. Announcement Modülü
```php
use App\Services\GlobalCacheService;
use Modules\Announcement\App\Models\Announcement;

// Announcement Service'te
class AnnouncementService
{
    public function getAnnouncementForEdit(int $id): ?Announcement
    {
        return GlobalCacheService::getModelWithRelations(
            Announcement::class,
            $id,
            ['user', 'attachments']
        );
    }
    
    public function updateAnnouncement(int $id, array $data): bool
    {
        $announcement = Announcement::find($id);
        $result = $announcement->update($data);
        
        if ($result) {
            // Cache'i temizle
            GlobalCacheService::clearModelCache(Announcement::class, $id);
        }
        
        return $result;
    }
}
```

## 🔧 Cache Key Sistemi

### Otomatik Key Üretimi
```php
// Basit model cache
$key = "Page_123"

// Relation'lı model cache
$key = "Page_123_with_md5hash"

// Hash relation'ların sıralı combination'ından üretilir
$relations = ['seoSetting', 'tags'];
// key: "Page_123_with_a1b2c3d4..."
```

### Cache Pattern Matching
```php
// Belirli model için tüm cache'leri bul
$pattern = "Page_123"; // Page_123, Page_123_with_*, vs.

// Model sınıfı için tüm cache'leri bul  
$pattern = "Page_"; // Page_1, Page_2, Page_123_with_*, vs.
```

## 🎯 Avantajlar

### ✅ Model-Agnostic Design
- Herhangi bir Eloquent model ile çalışır
- Relation desteği esnek ve güçlü
- Otomatik cache key yönetimi

### ✅ Performance Optimization
- Request başına tek model instance
- Duplicate sorgu engelleme
- Memory-efficient caching

### ✅ Flexible Relation Loading
- İstediğiniz relation'ları seçin
- Cache farklı relation kombinasyonları
- Automatic eager loading

### ✅ Easy Cache Management
- Model-specific cache temizleme
- Pattern-based cache clearing
- Cache statistics tracking

### ✅ Backward Compatibility
- Page modülü için eski API'ler çalışır
- Smooth migration path
- Zero breaking changes

## 📊 Cache Statistics

### Cache Bilgileri
```php
$stats = GlobalCacheService::getCacheStats();

// Örnek output:
[
    'total_cached_items' => 15,
    'models' => [
        'Page' => 5,
        'Portfolio' => 7,
        'Blog' => 3
    ],
    'memory_usage' => 8388608 // bytes
]
```

## 🚨 Best Practices

### 1. Admin Panelinde Kullanım
```php
// ✅ DOĞRU - Admin panelinde her zaman global cache kullan
if (request()->is('admin*')) {
    return GlobalCacheService::getModelWithRelations($model, $id, $relations);
}

// ❌ YANLIŞ - Admin'de Laravel cache kullanma
return Cache::remember($key, $ttl, function() { ... });
```

### 2. Cache Temizleme
```php
// ✅ DOĞRU - Model güncellendiğinde cache'i temizle
public function update(int $id, array $data): bool
{
    $result = $this->model->where('id', $id)->update($data);
    
    if ($result) {
        GlobalCacheService::clearModelCache(YourModel::class, $id);
    }
    
    return $result;
}
```

### 3. Relation Seçimi
```php
// ✅ DOĞRU - Sadece ihtiyacınız olan relation'ları yükleyin
$model = GlobalCacheService::getModelWithRelations(
    Model::class, 
    $id, 
    ['seoSetting'] // Sadece SEO gerekiyorsa
);

// ❌ YANLIŞ - Gereksiz relation'lar yükleme
$model = GlobalCacheService::getModelWithRelations(
    Model::class, 
    $id, 
    ['seoSetting', 'comments', 'tags', 'categories'] // Hepsi gerekli değilse
);
```

## 🔮 Gelecek Geliştirmeler

- [ ] TTL-based cache expiration
- [ ] Cache warming strategies
- [ ] Cache hit/miss analytics
- [ ] Distributed cache support
- [ ] Automatic cache invalidation on model events

## 📝 Migration Guide

### Eski kullanım (PageCacheService):
```php
use Modules\Page\App\Services\PageCacheService;

$page = PageCacheService::getPageWithSeo($pageId);
PageCacheService::clearCache($pageId);
```

### Yeni kullanım (GlobalCacheService):
```php
use App\Services\GlobalCacheService;

// Backward compatible - değişiklik gerekmiyor
$page = GlobalCacheService::getPageWithSeo($pageId);
GlobalCacheService::clearCache($pageId);

// Yeni API ile daha güçlü kullanım
$page = GlobalCacheService::getModelWithRelations(
    \Modules\Page\App\Models\Page::class,
    $pageId,
    ['seoSetting']
);
```

## 🚨 Önemli Notlar

1. **Request Scope**: Cache sadece tek request boyunca geçerli
2. **Memory Management**: Cache automatic garbage collection ile temizlenir
3. **Thread Safety**: Static property'ler request-specific
4. **Model Compatibility**: Tüm Eloquent model'lar desteklenir

## 📞 Destek

Bu sistem Turkbil Bee projesi için geliştirilmiştir. Performance kritik uygulamalarda model cache sistemi olarak kullanılabilir.