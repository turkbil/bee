# 📄 ReviewSystem Module - Laravel CMS

## 🌟 Genel Bakış

ReviewSystem modülü, Laravel CMS için geliştirilmiş **master pattern** modülüdür. Modern mimari pattern'leri, çoklu dil desteği ve yüksek performans optimizasyonları içerir. Tüm yeni modüller bu modülü temel alır.

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
Modules/ReviewSystem/
├── app/
│   ├── Console/                 # Artisan commands
│   │   └── WarmReviewSystemCacheCommand.php
│   ├── Contracts/               # Interfaces
│   │   └── ReviewSystemRepositoryInterface.php
│   ├── DataTransferObjects/     # DTO'lar
│   │   ├── ReviewSystemOperationResult.php
│   │   └── BulkOperationResult.php
│   ├── Enums/                   # Enum sınıfları
│   │   └── CacheStrategy.php
│   ├── Events/                  # Event classes
│   │   └── TranslationCompletedEvent.php
│   ├── Exceptions/              # Custom exceptions
│   │   ├── ReviewSystemException.php
│   │   ├── ReviewSystemNotFoundException.php
│   │   ├── ReviewSystemCreationException.php
│   │   └── HomereviewsystemProtectionException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── PageTranslationController.php
│   │   │   ├── Api/
│   │   │   │   └── ReviewSystemApiController.php
│   │   │   └── Front/
│   │   │       └── ReviewSystemController.php
│   │   ├── Livewire/
│   │   │   ├── Admin/
│   │   │   │   ├── ReviewSystemComponent.php
│   │   │   │   └── ReviewSystemManageComponent.php
│   │   │   └── Traits/
│   │   │       ├── InlineEditTitle.php
│   │   │       ├── WithBulkActions.php
│   │   │       └── WithBulkActionsQueue.php
│   │   └── Resources/
│   │       ├── ReviewSystemResource.php
│   │       └── ReviewSystemCollection.php
│   ├── Jobs/                    # Background jobs
│   │   ├── BulkDeleteReviewSystemsJob.php
│   │   ├── BulkUpdateReviewSystemsJob.php
│   │   └── TranslateReviewSystemJob.php
│   ├── Models/                  # Eloquent models
│   │   └── ReviewSystem.php
│   ├── Observers/               # Model observers
│   │   └── ReviewSystemObserver.php
│   ├── Repositories/            # Repository implementations
│   │   └── ReviewSystemRepository.php
│   └── Services/                # Business logic
│       └── ReviewSystemService.php
├── config/
│   └── config.php               # Module configuration
├── database/
│   ├── factories/               # Model factories
│   │   └── ReviewSystemFactory.php
│   ├── migrations/              # Database migrations
│   │   ├── 2024_02_17_000001_create_reviewsystems_table.php
│   │   └── 2024_12_30_add_optimizations_to_reviewsystems_table.php
│   └── seeders/                 # Database seeders
│       ├── ReviewSystemSeeder.php
│       ├── ReviewSystemSeederCentral.php
│       └── ReviewSystemSeederTenant2.php
├── lang/                        # Translations
│   ├── ar/admin.php
│   ├── en/admin.php
│   └── tr/admin.php
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── helper.blade.php
│       │   └── livewire/
│       │       ├── reviewsystem-component.blade.php
│       │       └── reviewsystem-manage-component.blade.php
│       └── front/
│           └── reviewsystems/
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
│   ├── ReviewSystemServiceProvider.php
│   └── RouteServiceProvider.php
├── composer.json
├── module.json
└── README.md                   # Bu dosya
```

## 🚀 Kurulum

### 1. Modül Kurulumu

```bash
# Modülü etkinleştir
php artisan module:enable ReviewSystem

# Migration'ları çalıştır
php artisan module:migrate ReviewSystem

# Seed data oluştur
php artisan module:seed ReviewSystem
```

### 2. Cache Konfigürasyonu

`.env` dosyanıza ekleyin:

```env
REVIEWSYSTEM_CACHE_ENABLED=true
REVIEWSYSTEM_ADMIN_PER_PAGE=10
REVIEWSYSTEM_AI_TRANSLATION=true
REVIEWSYSTEM_BULK_OPERATIONS=true
REVIEWSYSTEM_INLINE_EDITING=true
```

## 📖 Kullanım

### Admin Panel

```
URL: /admin/reviewsystem
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
> 1. `Modules/ReviewSystem/routes/api.php` dosyasını doldur
> 2. Route'ları `ReviewSystemApiController` ile eşleştir

#### Planlanan Public Endpoints

```http
GET /api/v1/reviewsystems                    # Tüm duyurular
GET /api/v1/reviewsystems/slug/{slug}        # Slug ile duyuru
```

#### Planlanan Protected Endpoints (Auth Required)

```http
POST   /api/v1/reviewsystems                 # Yeni duyuru
GET    /api/v1/reviewsystems/{id}            # ID ile duyuru
PUT    /api/v1/reviewsystems/{id}            # Duyuru güncelle
DELETE /api/v1/reviewsystems/{id}            # Duyuru sil
PATCH  /api/v1/reviewsystems/{id}/toggle     # Aktif/Pasif
POST   /api/v1/reviewsystems/bulk/delete     # Toplu silme
```

**API Durumu**: 🟡 Hazır (Route tanımı bekleniyor)

### Artisan Commands

```bash
# Cache warming (manuel)
php artisan reviewsystem:warm-cache

# Opsiyonlar
php artisan reviewsystem:warm-cache --tenant=2  # Belirli tenant
php artisan reviewsystem:warm-cache --reviewsystems=20  # İlk 20 sayfa
php artisan reviewsystem:warm-cache --urls      # URL'leri de cache'le
php artisan reviewsystem:warm-cache --force     # Cache'i zorla yenile
```

#### Scheduled Cache Warming (Önerilen)

```php
// app/Console/Kernel.php - schedule() metoduna ekle
$schedule->command('reviewsystem:warm-cache --urls')->hourly();
// Her saat başı tüm sayfaları ve URL'leri cache'e yükler
```

**Faydası**: İlk ziyaretçi cold start yaşamaz, her zaman sıcak cache.

## 🎯 Master Pattern Olma Rolü

ReviewSystem modülü, tüm Laravel CMS modülleri için **referans şablon** olarak tasarlanmıştır.

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
interface ReviewSystemRepositoryInterface {
    public function findById(int $id): ?ReviewSystem;
    public function findBySlug(string $slug, string $locale): ?ReviewSystem;
    public function create(array $data): ReviewSystem;
    public function update(int $id, array $data): ReviewSystem;
    public function delete(int $id): bool;
}

// Usage
class ReviewSystemService {
    public function __construct(
        private readonly ReviewSystemRepositoryInterface $reviewsystemRepository
    ) {}
}
```

### Service Layer

```php
// Business logic encapsulation
$reviewsystemService = app(ReviewSystemService::class);

$result = $reviewsystemService->createPage([
    'title' => ['tr' => 'Başlık', 'en' => 'Title'],
    'slug' => ['tr' => 'baslik', 'en' => 'title'],
    'body' => ['tr' => 'İçerik', 'en' => 'Content'],
]);

if ($result->success) {
    $reviewsystem = $result->data;
}
```

### DTO Pattern

```php
readonly class ReviewSystemOperationResult {
    public function __construct(
        public bool $success,
        public string $message,
        public string $type = 'success',
        public ?ReviewSystem $data = null,
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
$title = $reviewsystem->getTranslated('title', 'tr');

// Set translated value
$reviewsystem->title = [
    'tr' => 'Yeni Başlık',
    'en' => 'New Title'
];
```

## 🧪 Testing

### Run Tests

```bash
# All tests
./Modules/ReviewSystem/run-tests.sh

# Unit tests only
vendor/bin/phpunit Modules/ReviewSystem/tests/Unit

# Feature tests only
vendor/bin/phpunit Modules/ReviewSystem/tests/Feature

# With coverage
./Modules/ReviewSystem/run-tests.sh coverage
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
INDEX reviewsystems_active_deleted_created_idx
INDEX reviewsystems_homereviewsystem_active_deleted_idx

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
ReviewSystem::with(['seoSetting', 'activities'])->paginate();
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
// ReviewSystem modülünde is_homereviewsystem özelliği yoktur
// Duyurular ana sayfa olamaz
```

### Permission System

```php
// Route middleware
->middleware('module.permission:reviewsystem,view')
->middleware('module.permission:reviewsystem,create')
->middleware('module.permission:reviewsystem,update')
->middleware('module.permission:reviewsystem,delete')
```

## 📝 Configuration

### config/config.php

```php
return [
    'name' => 'ReviewSystem',

    // Routes
    'slugs' => [
        'index' => 'reviewsystem',
        'show' => 'reviewsystem',
    ],

    // Cache
    'cache' => [
        'enabled' => true,
        'ttl' => [
            'list' => 3600,
            'detail' => 7200,
            'homereviewsystem' => 1800,
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
$reviewsystem = ReviewSystem::create($data);

// ✅ Correct
$result = $reviewsystemService->createPage($data);
```

### 2. Handle Exceptions

```php
try {
    $result = $reviewsystemService->updatePage($id, $data);
} catch (ReviewSystemNotFoundException $e) {
    // Handle not found
} catch (HomereviewsystemProtectionException $e) {
    // Handle homereviewsystem protection
}
```

### 3. Use Cache Wisely

```php
// Frontend - long cache
$reviewsystem = Cache::remember('reviewsystem_' . $id, 7200, fn() => ...);

// Admin - always fresh
$reviewsystem = ReviewSystem::find($id); // No cache
```

## 🐛 Troubleshooting

### Common Issues

#### 1. Migration Fails

```bash
# Reset and re-run
php artisan migrate:rollback --step=2
php artisan module:migrate ReviewSystem
```

#### 2. Cache Not Working

```bash
# Clear all caches
php artisan app:clear-all
php artisan reviewsystem:warm-cache --force
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
    "type": "reviewsystems",
    "id": 1,
    "attributes": {
      "title": "Önemli Duyuru",
      "slug": "onemli-duyuru",
      "is_active": true
    },
    "links": {
      "self": "/api/v1/reviewsystems/1",
      "frontend": "/reviewsystem/onemli-duyuru"
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
    "reviewsystem_id": ["ReviewSystem not found with ID: 999"]
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
