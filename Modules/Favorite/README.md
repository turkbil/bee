# 📄 Favorite Module - Laravel CMS

## 🌟 Genel Bakış

Favorite modülü, Laravel CMS için geliştirilmiş **master pattern** modülüdür. Modern mimari pattern'leri, çoklu dil desteği ve yüksek performans optimizasyonları içerir. Tüm yeni modüller bu modülü temel alır.

### ✨ Özellikler

- 🌍 **Çoklu Dil Desteği** (JSON-based, HasTranslations trait)
- 🏗️ **Repository Pattern** ile temiz mimari
- 🚀 **Queue-Based Bulk Operations** (tenant_isolated queue)
- 🔍 **Universal SEO Integration** (GlobalSeoService)
- 💾 **Advanced Caching System** (Smart CacheStrategy enum)
- 🧪 **259 Comprehensive Tests** (85%+ coverage)
- 📊 **API Ready** (Resources & Controllers hazır - route tanımı gerekli)
- 🔥 **Cache Warming Command** (Schedule'a eklenebilir)
- 🎯 **Performance Optimized** (Eager loading, indexes)
- 🎨 **Master Pattern** (Diğer modüller için şablon)

## 📁 Klasör Yapısı

```
Modules/Favorite/
├── app/
│   ├── Console/                 # Artisan commands
│   │   └── WarmFavoriteCacheCommand.php
│   ├── Contracts/               # Interfaces
│   │   └── FavoriteRepositoryInterface.php
│   ├── DataTransferObjects/     # DTO'lar
│   │   ├── FavoriteOperationResult.php
│   │   └── BulkOperationResult.php
│   ├── Enums/                   # Enum sınıfları
│   │   └── CacheStrategy.php
│   ├── Events/                  # Event classes
│   │   └── TranslationCompletedEvent.php
│   ├── Exceptions/              # Custom exceptions
│   │   ├── FavoriteException.php
│   │   ├── FavoriteNotFoundException.php
│   │   ├── FavoriteCreationException.php
│   │   └── HomefavoriteProtectionException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── PageTranslationController.php
│   │   │   ├── Api/
│   │   │   │   └── FavoriteApiController.php
│   │   │   └── Front/
│   │   │       └── FavoriteController.php
│   │   ├── Livewire/
│   │   │   ├── Admin/
│   │   │   │   ├── FavoriteComponent.php
│   │   │   │   └── FavoriteManageComponent.php
│   │   │   └── Traits/
│   │   │       ├── InlineEditTitle.php
│   │   │       ├── WithBulkActions.php
│   │   │       └── WithBulkActionsQueue.php
│   │   └── Resources/
│   │       ├── FavoriteResource.php
│   │       └── FavoriteCollection.php
│   ├── Jobs/                    # Background jobs
│   │   ├── BulkDeleteFavoritesJob.php
│   │   ├── BulkUpdateFavoritesJob.php
│   │   └── TranslateFavoriteJob.php
│   ├── Models/                  # Eloquent models
│   │   └── Favorite.php
│   ├── Observers/               # Model observers
│   │   └── FavoriteObserver.php
│   ├── Repositories/            # Repository implementations
│   │   └── FavoriteRepository.php
│   └── Services/                # Business logic
│       └── FavoriteService.php
├── config/
│   └── config.php               # Module configuration
├── database/
│   ├── factories/               # Model factories
│   │   └── FavoriteFactory.php
│   ├── migrations/              # Database migrations
│   │   ├── 2024_02_17_000001_create_favorites_table.php
│   │   └── 2024_12_30_add_optimizations_to_favorites_table.php
│   └── seeders/                 # Database seeders
│       ├── FavoriteSeeder.php
│       ├── FavoriteSeederCentral.php
│       └── FavoriteSeederTenant2.php
├── lang/                        # Translations
│   ├── ar/admin.php
│   ├── en/admin.php
│   └── tr/admin.php
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── helper.blade.php
│       │   └── livewire/
│       │       ├── favorite-component.blade.php
│       │       └── favorite-manage-component.blade.php
│       └── front/
│           └── favorites/
│               └── show.blade.php
├── routes/
│   ├── admin.php               # Admin routes
│   ├── api.php                 # API routes
│   └── web.php                 # Frontend routes
├── tests/                      # Test suite
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
├── Providers/
│   ├── EventServiceProvider.php
│   ├── FavoriteServiceProvider.php
│   └── RouteServiceProvider.php
├── composer.json
├── module.json
└── README.md                   # Bu dosya
```

## 🚀 Kurulum

### 1. Modül Kurulumu

```bash
# Modülü etkinleştir
php artisan module:enable Favorite

# Migration'ları çalıştır
php artisan module:migrate Favorite

# Seed data oluştur
php artisan module:seed Favorite
```

### 2. Cache Konfigürasyonu

`.env` dosyanıza ekleyin:

```env
FAVORITE_CACHE_ENABLED=true
FAVORITE_ADMIN_PER_PAGE=10
FAVORITE_AI_TRANSLATION=true
FAVORITE_BULK_OPERATIONS=true
FAVORITE_INLINE_EDITING=true
```

## 📖 Kullanım

### Admin Panel

```
URL: /admin/favorite
```

#### Özellikler:
- 📝 CRUD operasyonları
- 🌍 Çoklu dil yönetimi
- 🔍 Universal SEO tab'ı
- 📦 Bulk operations (toplu işlemler)
- ✏️ Inline title editing
- 🤖 AI translation desteği

### API Endpoints

> ⚠️ **Not**: API Controller ve Resources hazır ancak `routes/api.php` henüz tanımlanmamıştır. API kullanmak için:
> 1. `Modules/Favorite/routes/api.php` dosyasını doldur
> 2. Route'ları `FavoriteApiController` ile eşleştir

#### Planlanan Public Endpoints

```http
GET /api/v1/favorites                    # Tüm duyurular
GET /api/v1/favorites/slug/{slug}        # Slug ile duyuru
```

#### Planlanan Protected Endpoints (Auth Required)

```http
POST   /api/v1/favorites                 # Yeni duyuru
GET    /api/v1/favorites/{id}            # ID ile duyuru
PUT    /api/v1/favorites/{id}            # Duyuru güncelle
DELETE /api/v1/favorites/{id}            # Duyuru sil
PATCH  /api/v1/favorites/{id}/toggle     # Aktif/Pasif
POST   /api/v1/favorites/bulk/delete     # Toplu silme
```

**API Durumu**: 🟡 Hazır (Route tanımı bekleniyor)

### Artisan Commands

```bash
# Cache warming (manuel)
php artisan favorite:warm-cache

# Opsiyonlar
php artisan favorite:warm-cache --tenant=2  # Belirli tenant
php artisan favorite:warm-cache --favorites=20  # İlk 20 sayfa
php artisan favorite:warm-cache --urls      # URL'leri de cache'le
php artisan favorite:warm-cache --force     # Cache'i zorla yenile
```

#### Scheduled Cache Warming (Önerilen)

```php
// app/Console/Kernel.php - schedule() metoduna ekle
$schedule->command('favorite:warm-cache --urls')->hourly();
// Her saat başı tüm sayfaları ve URL'leri cache'e yükler
```

**Faydası**: İlk ziyaretçi cold start yaşamaz, her zaman sıcak cache.

## 🎯 Master Pattern Olma Rolü

Favorite modülü, tüm Laravel CMS modülleri için **referans şablon** olarak tasarlanmıştır.

### Diğer Modüllere Taşınan Pattern'ler

#### 1. Kod Pattern'i
- ✅ Service Layer (readonly class, SOLID principles)
- ✅ Repository Pattern (Interface binding)
- ✅ DTOs (OperationResult, BulkOperationResult)
- ✅ Custom Exceptions (ModuleNotFoundException, etc.)
- ✅ Cache Strategy Enum (PUBLIC_CACHED, ADMIN_FRESH)
- ✅ Modern PHP 8.3+ (declare(strict_types=1))

#### 2. Tasarım Pattern'i
- ✅ Form yapısı (floating labels, pretty switches)
- ✅ Multi-language tabs
- ✅ Universal SEO tab integration
- ✅ Tab completion tracking
- ✅ Inline editing support

#### 3. Test Pattern'i
- ✅ Unit Tests (Repository, Service, Observer, Model)
- ✅ Feature Tests (Admin, API, Cache, Bulk, Permission)
- ✅ Test structure (phpunit.xml, run-tests.sh)

**Detay**: `readme/claude-docs/claude_modulpattern.md`

---

## 🏗️ Mimari

### Repository Pattern

```php
// Interface
interface FavoriteRepositoryInterface {
    public function findById(int $id): ?Favorite;
    public function findBySlug(string $slug, string $locale): ?Favorite;
    public function create(array $data): Favorite;
    public function update(int $id, array $data): Favorite;
    public function delete(int $id): bool;
}

// Usage
class FavoriteService {
    public function __construct(
        private readonly FavoriteRepositoryInterface $favoriteRepository
    ) {}
}
```

### Service Layer

```php
// Business logic encapsulation
$favoriteService = app(FavoriteService::class);

$result = $favoriteService->createPage([
    'title' => ['tr' => 'Başlık', 'en' => 'Title'],
    'slug' => ['tr' => 'baslik', 'en' => 'title'],
    'body' => ['tr' => 'İçerik', 'en' => 'Content'],
]);

if ($result->success) {
    $favorite = $result->data;
}
```

### DTO Pattern

```php
readonly class FavoriteOperationResult {
    public function __construct(
        public bool $success,
        public string $message,
        public string $type = 'success',
        public ?Favorite $data = null,
        public ?array $meta = null
    ) {}
}
```

## 🌍 Çoklu Dil Sistemi

### JSON Column Structure

```json
{
  "title": {
    "tr": "Türkçe Başlık",
    "en": "English Title",
    "ar": "العنوان العربي"
  },
  "slug": {
    "tr": "turkce-baslik",
    "en": "english-title",
    "ar": "arabic-title"
  }
}
```

### Usage in Code

```php
// Get translated value
$title = $favorite->getTranslated('title', 'tr');

// Set translated value
$favorite->title = [
    'tr' => 'Yeni Başlık',
    'en' => 'New Title'
];
```

## 🧪 Testing

### Run Tests

```bash
# All tests
./Modules/Favorite/run-tests.sh

# Unit tests only
vendor/bin/phpunit Modules/Favorite/tests/Unit

# Feature tests only
vendor/bin/phpunit Modules/Favorite/tests/Feature

# With coverage
./Modules/Favorite/run-tests.sh coverage
```

### Test Coverage

- **Unit Tests**: 139 tests
- **Feature Tests**: 120 tests
- **Total**: 259 tests
- **Coverage**: ~85%

## ⚡ Performans Optimizasyonları

### 1. Database Indexes

```sql
-- Generated columns for JSON fields
title_tr_generated VARCHAR(255) GENERATED
slug_tr_generated VARCHAR(255) GENERATED

-- Composite indexes
INDEX favorites_active_deleted_created_idx
INDEX favorites_homefavorite_active_deleted_idx

-- Fulltext search
FULLTEXT INDEX ft_title_tr
FULLTEXT INDEX ft_title_en
```

### 2. Cache Strategies

```php
enum CacheStrategy {
    case PUBLIC_CACHED;    // Frontend - 1 hour
    case ADMIN_FRESH;      // Admin - no cache
    case API_CACHED;       // API - 5 minutes
}
```

### 3. Eager Loading

```php
Favorite::with(['seoSetting', 'activities'])->paginate();
```

## 🔐 Güvenlik

### Input Validation

```php
// HTML/CSS/JS validation
SecurityValidationService::validateHtml($content);
SecurityValidationService::validateCss($css);
SecurityValidationService::validateJs($js);
```

### Content Protection

```php
// Favorite modülünde is_homefavorite özelliği yoktur
// Duyurular ana sayfa olamaz
```

### Permission System

```php
// Route middleware
->middleware('module.permission:favorite,view')
->middleware('module.permission:favorite,create')
->middleware('module.permission:favorite,update')
->middleware('module.permission:favorite,delete')
```

## 📝 Configuration

### config/config.php

```php
return [
    'name' => 'Favorite',

    // Routes
    'slugs' => [
        'index' => 'favorite',
        'show' => 'favorite',
    ],

    // Cache
    'cache' => [
        'enabled' => true,
        'ttl' => [
            'list' => 3600,
            'detail' => 7200,
            'homefavorite' => 1800,
        ],
    ],

    // Features
    'features' => [
        'ai_translation' => true,
        'bulk_operations' => true,
        'inline_editing' => true,
    ],
];
```

## 🎯 Best Practices

### 1. Always Use Service Layer

```php
// ❌ Wrong
$favorite = Favorite::create($data);

// ✅ Correct
$result = $favoriteService->createPage($data);
```

### 2. Handle Exceptions

```php
try {
    $result = $favoriteService->updatePage($id, $data);
} catch (FavoriteNotFoundException $e) {
    // Handle not found
} catch (HomefavoriteProtectionException $e) {
    // Handle homefavorite protection
}
```

### 3. Use Cache Wisely

```php
// Frontend - long cache
$favorite = Cache::remember('favorite_' . $id, 7200, fn() => ...);

// Admin - always fresh
$favorite = Favorite::find($id); // No cache
```

## 🐛 Troubleshooting

### Common Issues

#### 1. Migration Fails

```bash
# Reset and re-run
php artisan migrate:rollback --step=2
php artisan module:migrate Favorite
```

#### 2. Cache Not Working

```bash
# Clear all caches
php artisan app:clear-all
php artisan favorite:warm-cache --force
```

#### 3. Tests Failing

```bash
# Reset test database
php artisan migrate:fresh --env=testing
php artisan db:seed --env=testing
```

#### 4. Virtual Column Index Hatası (MySQL 5.7)

**Problem**: Migration'da virtual column index hataları
```bash
# Çözüm: MySQL 8.0+ kullan veya indexes'i commented out bırak
```

**MySQL Versiyonu Kontrol**:
```bash
mysql --version
# MySQL 8.0+ ise virtual column indexes aktifleştirebilirsin
```

## 📚 API Documentation

### Response Format

```json
{
  "success": true,
  "data": {
    "type": "favorites",
    "id": 1,
    "attributes": {
      "title": "Önemli Duyuru",
      "slug": "onemli-duyuru",
      "is_active": true
    },
    "links": {
      "self": "/api/v1/favorites/1",
      "frontend": "/favorite/onemli-duyuru"
    },
    "meta": {
      "locale": "tr",
      "word_count": 250,
      "read_time": 2
    }
  },
  "meta": {
    "timestamp": "2024-12-30T10:00:00Z"
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Sayfa bulunamadı",
  "errors": {
    "favorite_id": ["Favorite not found with ID: 999"]
  }
}
```

## 📋 Bilinen İyileştirme Alanları

### Yapılabilir Optimizasyonlar

#### 1. API Routes (Düşük Öncelik)
- **Durum**: API Controller ve Resources hazır, route tanımı yok
- **Aksiyon**: `routes/api.php` dosyasını doldur veya API'yi kullanmayacaksan temizle

#### 2. Config Modülarizasyonu (Orta Öncelik)
- **Durum**: `config/config.php` 213 satır (şişkin)
- **Öneri**: Alt dosyalara böl (`cache.php`, `seo.php`, `validation.php`)

#### 3. ~~Tenant Theme Mapping~~ ✅ Zaten Dinamik
- **Durum**: ThemeService otomatik `tenant()->theme` kolonundan çekiyor
- **Aksiyon**: Gerekmiyor - sistem zaten dinamik

#### 4. Virtual Column Indexes (MySQL 8.0+)
- **Durum**: Migration'da commented out
- **Öneri**: MySQL 8.0+ kullanıyorsan aktifleştir (JSON search performance)

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing`)
5. Create Pull Request

**Pattern Uygunluğu**: Yeni özellikler master pattern'ı koruyacak şekilde eklenmelidir.

## 📄 License

This module is proprietary software. All rights reserved.

## 👥 Credits

- **Author**: Laravel CMS Team
- **Version**: 1.0.0
- **Laravel**: 12.x
- **PHP**: 8.3+
- **Pattern Status**: ✅ Master Pattern Module

## 📞 Support

For support, please contact: support@laravelcms.com

---

**Last Updated**: October 1, 2025
**Module Status**: 🟢 Production Ready (95/100)
**API Status**: 🟡 Ready (Route definition pending)
**Test Coverage**: 85%+
