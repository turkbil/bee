# 📄 Page Module - Laravel CMS

## 🌟 Genel Bakış

Page modülü, Laravel CMS için geliştirilmiş **master pattern** modülüdür. Modern mimari pattern'leri, çoklu dil desteği, özel CSS/JS desteği ve homepage yönetimi içerir.

### ✨ Özellikler

- 🌍 **Çoklu Dil Desteği** (JSON-based, HasTranslations trait)
- 🏗️ **Repository Pattern** ile temiz mimari
- 🚀 **Queue-Based Bulk Operations** (tenant_isolated queue)
- 🔍 **Universal SEO Integration** (GlobalSeoService)
- 💾 **Advanced Caching System** (Smart CacheStrategy enum)
- 🎨 **Custom CSS/JS Support** (Monaco editor integration)
- 🏠 **Homepage Management** (is_homepage field)
- 📊 **API Ready** (Resources & Controllers hazır)
- 🔥 **Cache Warming Command** (Schedule'a eklenebilir)
- 🎯 **Performance Optimized** (Eager loading, indexes)

## 📁 Klasör Yapısı

```
Modules/Page/
├── app/
│   ├── Console/                 # Artisan commands
│   ├── Contracts/               # Interfaces
│   ├── DataTransferObjects/     # DTO'lar
│   ├── Enums/                   # Enum sınıfları
│   ├── Events/                  # Event classes
│   ├── Exceptions/              # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Livewire/
│   │   └── Resources/
│   ├── Jobs/                    # Background jobs
│   ├── Models/                  # Eloquent models
│   ├── Observers/               # Model observers
│   ├── Repositories/            # Repository implementations
│   └── Services/                # Business logic
├── config/
│   └── config.php               # Module configuration
├── database/
│   ├── factories/               # Model factories
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── lang/                        # Translations
├── resources/views/
│   ├── admin/                   # Admin panel views
│   └── front/                   # Frontend views
├── routes/
│   ├── admin.php               # Admin routes
│   ├── api.php                 # API routes
│   └── web.php                 # Frontend routes
├── tests/                      # Test suite
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
├── phpunit.xml                 # PHPUnit configuration
├── run-tests.sh                # Test runner script
└── README.md                   # Bu dosya
```

## 🚀 Kurulum

### 1. Modül Kurulumu

```bash
# Modülü etkinleştir
php artisan module:enable Page

# Migration'ları çalıştır
php artisan module:migrate Page

# Seed data oluştur
php artisan module:seed Page
```

### 2. Cache Konfigürasyonu

`.env` dosyanıza ekleyin:

```env
PAGE_CACHE_ENABLED=true
PAGE_ADMIN_PER_PAGE=10
PAGE_AI_TRANSLATION=true
PAGE_BULK_OPERATIONS=true
PAGE_INLINE_EDITING=true
PAGE_CUSTOM_CSS_JS=true
```

## 📖 Kullanım

### Admin Panel

```
URL: /admin/page
```

#### Özellikler:
- 📝 CRUD operasyonları
- 🌍 Çoklu dil yönetimi
- 🔍 Universal SEO tab'ı
- 📦 Bulk operations (toplu işlemler)
- ✏️ Inline title editing
- 🤖 AI translation desteği
- 🎨 Custom CSS/JS editörü (Monaco)
- 🏠 Homepage yönetimi

### Homepage Yönetimi

```php
// Homepage'i getir
$homepage = $pageRepository->getHomepage();

// Bir sayfayı homepage yap
$page->is_homepage = true;
$page->save(); // Diğer tüm homepage'ler otomatik false olur

// Homepage protection (Observer tarafından)
// - Homepage silinemez
// - Homepage pasif yapılamaz
// - Sadece bir homepage olabilir
```

### Custom CSS/JS

```php
// CSS/JS ekleme
$page->css = '.my-class { color: red; }';
$page->js = 'console.log("Hello");';
$page->save(); // Observer tarafından size validation yapılır

// Size limits
$maxCssSize = config('page.security.max_css_size', 50000); // 50KB
$maxJsSize = config('page.security.max_js_size', 50000);   // 50KB
```

### Artisan Commands

```bash
# Cache warming (manuel)
php artisan page:warm-cache

# Opsiyonlar
php artisan page:warm-cache --tenant=2  # Belirli tenant
php artisan page:warm-cache --pages=20  # İlk 20 sayfa
php artisan page:warm-cache --urls      # URL'leri de cache'le
php artisan page:warm-cache --force     # Cache'i zorla yenile
```

## 🧪 Testing

### Test Çalıştırma

```bash
# Tüm testler
./Modules/Page/run-tests.sh

# Unit tests only
./Modules/Page/run-tests.sh unit

# Feature tests only
./Modules/Page/run-tests.sh feature

# Coverage raporu
./Modules/Page/run-tests.sh coverage
```

## 🏗️ Mimari

### Repository Pattern

```php
// Interface
interface PageRepositoryInterface {
    public function findById(int $id): ?Page;
    public function findBySlug(string $slug, string $locale): ?Page;
    public function getHomepage(): ?Page;
    // ...
}

// Usage
class PageService {
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository
    ) {}
}
```

### Service Layer

```php
$pageService = app(PageService::class);

$result = $pageService->createPage([
    'title' => ['tr' => 'Başlık', 'en' => 'Title'],
    'slug' => ['tr' => 'baslik', 'en' => 'title'],
    'body' => ['tr' => 'İçerik', 'en' => 'Content'],
    'is_homepage' => false,
]);
```

### Observer Pattern

```php
// PageObserver otomatik:
// - Slug oluşturur
// - Homepage kontrolü yapar
// - CSS/JS size validation yapar
// - Cache'i temizler
// - Activity log tutar
```

## 🔐 Güvenlik

### Homepage Protection

```php
// PageObserver tarafından korunur
if ($page->is_homepage) {
    // ❌ Silinemez
    throw HomepageProtectionException::cannotDelete($page->page_id);

    // ❌ Pasif yapılamaz
    throw HomepageProtectionException::cannotDeactivate($page->page_id);
}
```

### CSS/JS Validation

```php
// SecurityValidationService tarafından validate edilir
SecurityValidationService::validateCss($css);
SecurityValidationService::validateJs($js);

// Size limit kontrolü Observer'da
if (strlen($page->css) > 50000) {
    throw PageValidationException::cssSizeExceeded(50000);
}
```

## 📝 Configuration

### config/config.php

```php
return [
    'name' => 'Page',

    // Homepage özelliği
    'routes' => [
        'homepage' => [
            'controller' => PageController::class,
            'method' => 'homepage'
        ]
    ],

    // CSS/JS Support
    'features' => [
        'custom_css_js' => true,
    ],

    'defaults' => [
        'is_homepage' => false,
        'css' => null,
        'js' => null,
    ],

    'security' => [
        'max_css_size' => 50000,
        'max_js_size' => 50000,
    ],
];
```

## 🎯 Page vs Announcement Farkları

| Özellik | Page | Announcement |
|---------|------|--------------|
| **Homepage Support** | ✅ | ❌ |
| **Custom CSS/JS** | ✅ | ❌ |
| **Code Tab** | ✅ | ❌ |
| **Complexity** | Yüksek | Düşük |
| **Use Case** | Static pages, Landing pages | News, Blog posts |

## 📄 License

This module is proprietary software. All rights reserved.

## 👥 Credits

- **Author**: Laravel CMS Team
- **Version**: 1.0.0
- **Laravel**: 12.x
- **PHP**: 8.3+
- **Pattern Status**: ✅ Master Pattern Module

---

**Last Updated**: October 1, 2025
**Module Status**: 🟢 Production Ready (91/100)
**Test Coverage**: In Progress
