🔊 **KRİTİK: SES BİLDİRİM SİSTEMİ - EN ÖNCELİKLİ KURAL**
HER YANIT SONUNDA MUTLAKA SES ÇALIŞTIR:
```bash
say "tamamlandı"
```

🚨 **NURULLAH'IN ANA KURALI**
- ASLA test etmeden "çalışıyor" deme
- DAIMA "test et ve sonucu söyle" de

🚨 **OTOMATİK DEVAM PROTOKOLÜ**
- Sorma, direk devam et
- Bash komutlarını çalıştır

🗂️ **OTOMATİK DOSYA TEMİZLEME**
- Log dosyası gönderildiğinde: Oku → Analiz et → Log'u boşalt → Dosyayı sil
- Fotoğraf gönderildiğinde: Oku → İşle → Dosyayı sil

🚨 **İÇ KAYNAK KURALI**
- HİÇBİR DURUMDA dış web sitesi/araç önerme
- HER ŞEYİ kendi sistemde çöz

**SİSTEM KURALLARI:**
- Türkçe yanıt ver
- HARDCODE kullanma - sistem tamamen dinamik
- "aferin", "bravo", "oldu" gibi sonuclanma kelimesi kullandığımda → README.md'ye kaydet + Git'e yükle

**TASARIM KURALLARI:**
- **Admin**: Tabler.io + Bootstrap + jQuery + Livewire + FontAwesome
- **Frontend**: Alpine.js + Tailwind CSS
- **Her ikisinde dark/light mod var!**
- **ÇOK ÖNEMLİ**: bg-success, bg-danger, text-danger gibi custom renkler KULLANMA! Dark modda sorun çıkarır
- Framework'ün kendi renk sistemini kullan

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## SlugHelper - Global Slug Yönetim Sistemi

**Konum**: `app/Helpers/SlugHelper.php` ve `app/Traits/HasSlugManagement.php`

Tüm modüllerde slug unique kontrolü ve otomatik düzeltme sistemi:

### Özellikler
- ✅ **Çoklu Dil Desteği**: Her dil için ayrı unique kontrol
- ✅ **Otomatik Düzeltme**: Duplicate slug'lar otomatik sayı ile düzeltilir (`iletisim` → `iletisim-1`)
- ✅ **Title'dan Slug**: Boş slug'lar title'dan otomatik oluşturulur
- ✅ **Türkçe Karakter Desteği**: ğ→g, ş→s dönüşümleri
- ✅ **Model Agnostic**: Her model için çalışır (Page, Portfolio, Announcement, vs.)
- ✅ **Validation Entegrasyonu**: Hazır validation kuralları ve mesajları

### Kullanım Örneği
```php
use App\Helpers\SlugHelper;
use App\Traits\HasSlugManagement;

class ExampleManageComponent extends Component
{
    use HasSlugManagement;
    
    // Save metodunda:
    $processedSlugs = $this->processMultiLanguageSlugs(
        ExampleModel::class,
        $this->multiLangInputs,
        $this->availableLanguages,
        $this->modelId
    );
}
```

### Mevcut Entegrasyonlar
- ✅ **Page Modülü**: `PageManageComponent` tamamen entegre edildi
- ✅ **Announcement Modülü**: `AnnouncementManageComponent` tamamen entegre edildi (Page pattern uygulandı)
- 🔄 **Portfolio Modülü**: Hazırlanabilir
- 🔄 **Diğer Modüller**: İhtiyaç halinde eklenebilir

**Detaylı dokümantasyon**: `SlugHelper_README.md`

## 🎯 MODÜL MODERNLEŞTIRME PATTERN'I - PAGE PATTERN UYGULAMASI

**Kural**: Page modülü artık bizim standart pattern'imiz. Yeni modül geliştirme veya modernleştirme yaparken Page'i baz al.

### 📖 PATTERN KAVRAMLARI:
- **"Pattern uygula"** = Hem kod hem tasarım pattern'ı birlikte uygulanır
- **"Kod pattern'ı uygula"** = Sadece backend/service layer pattern'ı
- **"Tasarım pattern'ı uygula"** = Sadece frontend/UI pattern'ı
- **"Page pattern'ı"** = Page modülünün tüm yapısını (kod+tasarım) baz alma

### ✅ ANNOUNCEMENT MODÜLÜ MODERNLEŞTIRME ÇALIŞMASI (BAŞARILI)

**Kılavuz İlkeler:**
1. **Kod ve Tasarım Pattern'ini Birlikte Uygula** - "Pattern" dendiğinde hem kod yapısı hem UI/UX tasarımı dahil
2. **JSON Multi-Language Desteği** - HasTranslations trait ile `{"tr":"text","en":"text"}` formatı
3. **Modern Laravel 12 + PHP 8.3+** - declare(strict_types=1), readonly classes, SOLID principles
4. **Global Service Integration** - GlobalSeoService, GlobalTabService entegrasyonu
5. **Module-Specific Config** - Her modülün kendi tab/seo konfigürasyonu (config/tabs.php)

### 🏗️ KOD PATTERN'I TAŞINAN ÖĞELER:

#### Backend Architecture:
```
✅ Migration yapısı: JSON multi-language columns (title, slug, body)
✅ Model yapısı: HasTranslations trait, SEO relationships
✅ Service Layer: Readonly classes, SOLID principles, modern PHP 8.3+
✅ Repository Pattern: Interface binding, dependency injection
✅ DTO Classes: PageOperationResult → AnnouncementOperationResult
✅ Exception Classes: Custom module exceptions
✅ Cache Strategy: Smart caching enums
```

#### Component Architecture:
```php
✅ Livewire 3.5+ patterns:
   - Computed properties (#[Computed])
   - Modern dependency injection (boot method)
   - Multi-language state management
   - SEO data caching sistem (seoDataCache, allLanguagesSeoData)
   - Tab completion tracking
   - Language switching logic
```

#### Validation & Language:
```php
✅ Validation Rules: Multi-language field validation
✅ SlugHelper Integration: Automatic slug generation
✅ Language File Structure: 
   - Module-specific lang files
   - Global admin.php key additions
   - Validation.php attributes for nested fields
```

#### Configuration:
```php
✅ Module Config: config/tabs.php structure
✅ Service Provider: Modern binding patterns
✅ Global Service Integration: TabService, SeoService
```

### 🎨 TASARIM PATTERN'I TAŞINAN ÖĞELER:

#### UI/UX Components:
```html
✅ Form Layout Pattern (Page/manage → Announcement/manage):
   - Floating label inputs
   - Pretty checkbox/switch components  
   - Tab-based organization (basic, seo)
   - Language switcher tabs
   - Responsive grid layout (col-md-6, col-lg-4 patterns)
```

#### Form Elements:
```html
✅ Input Components:
   - Form floating labels: <div class="form-floating">
   - Pretty switches: class="form-check form-switch"
   - TinyMCE integration: standardized editor setup
   - Choices.js selectboxes: standardized dropdown styling
```

#### Language System UI:
```html
✅ Multi-Language Tabs:
   - Bootstrap nav-tabs structure
   - Language flag icons (if available)
   - Active language highlighting
   - Seamless language switching UX
```

#### SEO Panel Design:
```html
✅ SEO Tab Structure:
   - SEO title input with character counter
   - Meta description textarea with character limit
   - Keywords input with tag-like styling
   - Canonical URL input
   - SEO score indicators (if available)
```

#### Button & Action Patterns:
```html
✅ Action Buttons:
   - Save button styling: btn btn-primary
   - Save & Continue: btn btn-success  
   - Cancel button: btn btn-secondary
   - Studio Editor integration button
   - Consistent button positioning and spacing
```

#### Layout & Spacing:
```html
✅ Page Structure:
   - Card-based layout: class="card"
   - Consistent padding: p-3, p-4 patterns
   - Proper spacing: mb-3, mt-2 utilities
   - Responsive breakpoints
```

#### JavaScript Integration:
```javascript
✅ Frontend Interactions:
   - Language switching JavaScript
   - TinyMCE content synchronization
   - Form validation feedback
   - Tab switching animations
   - Auto-save functionality patterns
```

### 🔧 ÖZELLEŞTIRMELER (Module-Specific):

#### Announcement'a Özel Değişiklikler:
```
❌ Homepage alanı kaldırıldı (announcements homepage olamaz)
❌ Code tab kaldırıldı (announcements'ta kod alanı yok)
✅ Announcement-specific validation rules
✅ Announcement-specific language keys
✅ Module-specific tab configuration
```

### 📋 PATTERN UYGULAMA REHBERİ:

#### 1. Tasarım Pattern'ı Uygularken:
```bash
# Page modülünün Blade dosyalarını incele:
- resources/views/admin/livewire/page-manage-component.blade.php
- Form yapısını, CSS class'larını, JavaScript entegrasyonlarını kopyala
- Module-specific customization'ları yap (homepage kaldır vs.)
```

#### 2. Kod Pattern'ı Uygularken:
```bash
# Page modülünün PHP dosyalarını incele:
- app/Models/Page.php → HasTranslations, SEO relationship
- app/Services/PageService.php → Readonly, SOLID principles  
- app/Http/Livewire/Admin/PageManageComponent.php → Modern Livewire
- config/tabs.php → Tab configuration
```

#### 3. Her İkisini Birlikte Uygularken:
```bash
# Announcement örneğindeki gibi:
1. Migration'ı düzenle (JSON columns)
2. Model'i güncelle (HasTranslations trait)
3. Service layer'ı modernleştir
4. Component'i yeniden yaz (Page pattern)
5. Blade template'ini Page'den kopyala ve uyarla
6. Config dosyalarını oluştur
7. Language dosyalarını güncelle
```

### 🔧 Teknik Uygulamalar:

#### Migration Pattern:
```php
// Announcement için homepage kolonu kaldırıldı (çünkü announcements homepage olamaz)
// Multi-language JSON kolonları: title, slug, body
$table->json('title');
$table->json('slug'); 
$table->json('body');
```

#### Model Pattern:
```php
// HasTranslations trait kullanımı
use App\Traits\HasTranslations;
protected $translatable = ['title', 'slug', 'body'];

// SEO relationship
public function seoSetting(): MorphOne
```

#### Service Pattern:
```php
// SOLID principles - readonly classes
readonly class AnnouncementService
{
    public function __construct(
        private AnnouncementRepositoryInterface $repository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}
}
```

#### Component Pattern:
```php
// Livewire 3.5+ computed properties
public function availableSiteLanguages(): Collection
public function adminLocale(): string  
public function siteLocale(): string

// Modern dependency injection
public function boot() {
    $this->service = app(AnnouncementService::class);
}
```

#### Tab Configuration Pattern:
```php
// Modules/Announcement/config/tabs.php
return [
    'tabs' => [
        ['key' => 'basic', 'name' => 'Temel Bilgiler'],
        ['key' => 'seo', 'name' => 'SEO']
        // Code tab yok - Announcement'ta kod alanı olmaz
    ]
];
```

### 🚨 Kritik Sorun Çözümleri:

#### 1. Double-Encoded JSON Sorunu:
**Problem**: JSON veriler string olarak saklanıp getTranslated() doğru çalışmıyordu
**Çözüm**: HasTranslations trait'inde JSON decode kontrolü eklendi
```php
if (is_string($translations)) {
    $decoded = json_decode($translations, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $translations = $decoded;
    }
}
```

#### 2. Validation Hatalarının Türkçe Çevirisi:
**Problem**: Nested field validation hataları İngilizce çıkıyordu
**Çözüm**: 
- `lang/tr/validation.php` attributes'a field çevirileri eklendi
- `multiLangInputs.*.title => 'Başlık'` formatında

#### 3. Module-Specific Language Keys:
**Problem**: Blade'de `admin.announcement_url_slug` bulunamıyordu  
**Çözüm**: Global `lang/tr/admin.php`'ye modül-specific key'ler eklendi

### 📋 Checklist - Yeni Modül Pattern Uygulaması:

#### 🔹 Migration:
- [ ] JSON multi-language kolonları (title, slug, body)
- [ ] Module-specific kolonlar (homepage varsa kaldır vs.)
- [ ] Proper indexes ve foreign keys

#### 🔹 Model:
- [ ] HasTranslations trait ekle
- [ ] $translatable array tanımla  
- [ ] SEO morphOne relationship
- [ ] Modern fillable/casts tanımları

#### 🔹 Service Layer:
- [ ] Readonly service class
- [ ] Repository pattern dependency injection
- [ ] GlobalSeoService entegrasyonu
- [ ] SOLID principles uygulaması

#### 🔹 Component (Livewire):
- [ ] Computed properties (availableSiteLanguages, locales)
- [ ] Modern dependency injection (boot method)
- [ ] Multi-language input handling
- [ ] SEO data cache sistemi
- [ ] Tab completion tracking

#### 🔹 Configuration:
- [ ] Module config/tabs.php oluştur
- [ ] Module-specific tab configuration
- [ ] GlobalTabService entegrasyonu

#### 🔹 Language Files:
- [ ] Module lang dosyaları oluştur/güncelle  
- [ ] Global admin.php'ye module keys ekle
- [ ] Validation.php attributes güncelle

#### 🔹 Blade Templates:
- [ ] Page pattern'ındaki blade yapısını kopyala
- [ ] Module-specific customization'lar yap
- [ ] Language switcher entegre et
- [ ] Tab system entegre et

### 🎯 Sonuç:
Page pattern'ı başarıyla Announcement'a uyguladık. Bu metodoloji ile tüm modüller modernleştirilebilir.

## Project Overview

This is **Turkbil Bee**, a sophisticated Laravel 11 multi-tenant application with a modular architecture. The project uses domain-based tenancy where each tenant has isolated databases, storage, and Redis caching.

## Architecture

### Modular System

-   Uses `nwidart/laravel-modules` for modular architecture
-   Each module in `/Modules/` has its own controllers, models, views, routes, and migrations
-   Modules follow consistent structure: `app/`, `config/`, `database/`, `resources/`, `routes/`, `tests/`
-   Active modules: Page, Portfolio, UserManagement, ModuleManagement, SettingManagement, TenantManagement, WidgetManagement, ThemeManagement, Studio, Announcement, AI

### Multi-Tenancy

-   Uses `stancl/tenancy` for domain-based tenancy
-   Separate databases per tenant with prefix `tenant{id}`
-   Central database for tenant management
-   Redis caching with tenant-specific prefixes
-   Filesystem isolation per tenant

### Dynamic Routing

-   Slug-based routing through `DynamicRouteService`
-   Module-specific URL patterns configurable per tenant
-   Catch-all routes for content management in modules
-   Admin routes separated from public routes

### Widget & Theme System

-   Modular widget architecture for reusable content blocks
-   Theme support with customizable templates
-   Visual editor (Studio module) for page building
-   Widget embed system with preview functionality

### AI Feature System - İki Katmanlı Prompt Yapısı

**Sistem Tasarımı:**
-   **Quick Prompt**: Feature'ın NE yapacağını kısa söyler ("Sen çeviri uzmanısın")
-   **Expert Prompt**: NASIL yapacağının detayları (ai_prompts tablosundan referans)
-   **Response Template**: Her feature'ın sabit yanıt formatı (JSON şablon)

**Veritabanı Yapısı:**
-   `ai_features.quick_prompt`: Kısa, hızlı prompt
-   `ai_features.expert_prompt_id`: ai_prompts tablosuna foreign key
-   `ai_features.response_template`: JSON format şablonu

**Kullanım Örneği:**
```
Çeviri Feature:
- Quick: "Sen bir çeviri uzmanısın. Verilen metni hedef dile çevir."
- Expert: "İçerik Üretim Uzmanı" (detaylı teknik prompt)
- Template: {"format": "translated_text", "show_original": true}

SEO Analiz Feature:
- Quick: "Sen bir SEO analiz uzmanısın. İçeriği analiz et."
- Expert: "SEO İçerik Uzmanı" (teknik SEO bilgileri)
- Template: {"sections": ["Anahtar Kelime", "İçerik", "Başlık", "Duygu"], "scoring": true}
```

**Sabit Yanıt Formatı Mantığı:**
-   Her feature hep aynı düzende sonuç verir
-   Tutarlı kullanıcı deneyimi
-   Template JSON'da sections, format, scoring gibi özellikler

## 🎯 AI FEATURE ÇALIŞMA PRENSİPLERİ - 06.07.2025

### Prompt Hierarchy (Sıralı Çalışma Düzeni)
```
1. Gizli Sistem Prompt'u (her zaman ilk)    → Temel sistem kuralları
2. Quick Prompt (Feature'ın ne yapacağı)    → "Sen bir çeviri uzmanısın..."
3. Expert Prompt'lar (Priority sırasına göre) → Detaylı teknik bilgiler  
4. Response Template (Yanıt formatı)         → Sabit çıktı şablonu
5. Gizli Bilgi Tabanı                       → AI'ın gizli bilgi deposu
6. Şartlı Yanıtlar                          → Sadece sorulunca anlatılır
```

### Template Sistemi Mantığı
- **Quick Prompt**: Feature'ın NE yapacağını kısa söyler
- **Expert Prompt**: NASIL yapacağının detayları (ai_prompts tablosundan)
- **Response Template**: Her feature'ın sabit yanıt formatı (JSON)
- **Priority System**: Expert prompt'lar öncelik sırasına göre çalışır

### Çalışma Prensipleri  
- ✅ Ortak özellikler önce (sistem prompt'ları)
- ✅ Sonra gizli özellikler (hidden knowledge)
- ✅ Ardından şartlı özellikler (conditional responses)
- ✅ Feature-specific prompt'lar priority'ye göre
- ✅ En son template'e uygun yanıt formatı
- ✅ SIFIR HARDCODE - Her şey dinamik
- ✅ Sınırsız feature, sınırsız prompt desteği

### Başarılı Uygulamalar
- 40 AI feature'ının tamamına template sistemi uygulandı
- Professional business-case örnekleri eklendi
- Helper function documentation sistemi
- Seeder optimizasyonu ve temizleme (10K+ satır kod temizlendi)

### 🎯 NURULLAH'IN HELPER KURALLARI - 23.07.2025
- **KRİTİK**: Helper dosyalarında CSS ve JavaScript kodu görülmek istenmiyor
- **Global Sistem**: Ortak CSS/JS kodlar main.css ve main.js'te kullanılacak
- **Helper İçeriği**: Sadece module-specific işlevler kalmalı
- **Temizlik**: Helper'a kod ekleme, sadece mevcut kodları kullan

## Development Commands

### Start Development Environment

```bash
composer run dev
```

This runs concurrent servers: PHP dev server, queue worker, log monitoring, and Vite.

### Module Management

```bash
php artisan module:list                    # List all modules
php artisan module:make {name}             # Create new module
php artisan module:enable {name}           # Enable module
php artisan module:disable {name}          # Disable module
php artisan module:migrate                 # Run module migrations
```

### Tenancy Commands

```bash
php artisan tenants:list                   # List all tenants
php artisan tenants:migrate                # Run tenant migrations
php artisan tenants:seed                   # Seed tenant databases
```

### Testing

```bash
php artisan test                           # Run PHPUnit tests
./vendor/bin/phpunit                       # Direct PHPUnit execution
```

### Asset Management

```bash
npm run dev                                # Vite development server
npm run build                              # Production build
```

### Custom Application Commands

```bash
php artisan app:clear-all                  # Clear all caches and storage
php artisan create:module-permissions      # Create module permissions
php artisan assign:module-permissions      # Assign permissions to users
php artisan theme:publish                  # Publish theme assets
```

## Key Files and Directories

### Core Application

-   `/app/Services/` - Business logic (ModuleSlugService, DynamicRouteService)
-   `/app/Providers/TenancyProvider.php` - Multi-tenancy configuration
-   `/app/Helpers/` - Global helper functions (autoloaded)
-   `/config/tenancy.php` - Multi-tenancy settings
-   `/config/modules.php` - Module system configuration

### Module Structure (each module follows this pattern)

-   `app/Http/Controllers/` - Controllers (Admin, Front)
-   `app/Models/` - Eloquent models
-   `app/Http/Livewire/` - Livewire components
-   `database/migrations/` - Both central and tenant migrations
-   `resources/views/` - Blade templates with theme support
-   `routes/` - Web, API, and admin routes

### Widget System

-   Widget blocks in `Modules/WidgetManagement/Resources/views/blocks/`
-   Widget configurations with settings and item schemas
-   Preview system with server-side rendering

## Important Patterns

### Module Development

-   Follow existing module structure when creating new modules
-   Use tenant-aware models and migrations
-   Implement proper permission checks for multi-tenancy
-   Utilize the widget system for reusable content blocks

### Database Considerations

-   Central migrations go in main `database/migrations/`
-   Tenant-specific migrations go in module `database/migrations/tenant/`
-   Use tenant-aware models that extend base tenant model

### File Storage

-   Use `TenantStorageHelper` for tenant-isolated file operations
-   All media uploads are tenant-specific
-   File paths include tenant context

### Permissions

-   Role-based permissions using Spatie Laravel Permission
-   Module-specific permissions with tenant isolation
-   Dynamic permission assignment per module

## Technology Stack

-   **Backend**: Laravel 11, PHP 8.2+, MySQL, Redis
-   **Frontend**: Livewire 3.5, Tailwind CSS, Alpine.js, Tabler.io theme
-   **Development**: Laravel Telescope, Debugbar, Pail for logging
-   **Build**: Vite for asset compilation
-   **Testing**: PHPUnit

## Development Notes

-   The project uses Turkish naming and comments in some areas
-   Version history and changelog are maintained in README.md
-   Concurrent development tools are configured in composer.json "dev" script
-   Redis is required for proper caching and session management
-   Each tenant requires database creation permissions for MySQL

# important-instruction-reminders

Do what has been asked; nothing more, nothing less.
NEVER create files unless they're absolutely necessary for achieving your goal.
ALWAYS prefer editing an existing file to creating a new one.
NEVER proactively create documentation files (\*.md) or README files. Only create documentation files if explicitly requested by the User.

# NURULLAH'IN HAFıZASı - Otomatik Kayıt Sistemi

## KRİTİK SİSTEM BİLGİSİ

### Dil Sistemi (İKİ AYRI TABLO):
- **Admin**: `system_languages` + `admin_locale` session
- **Site**: `site_languages` + `site_locale` session  
- Karışık işlem yapma!

### Tenant Sistemi (ÇOK ÖNEMLİ):
- **Central Domain**: laravel.test (bu da bir tenant!)
- **Migrations**: 
  - `migrations/` → Central tablolar
  - `migrations/tenant/` → Tenant tablolar
- **Tenant tabloları central'da da var** (central da tenant olduğu için)
- **KRİTİK**: Add/remove migrate dosyası YAPMA! Create dosyasını düzenle
- **Neden**: Local çalışıyor, veriyi silebiliriz
- **UNUTMA**: Migration değiştiğinde → Seeder + Model + Controller + Blade + Component'ları da güncelle

### 🎯 MODÜL PATTERN SİSTEMİ - KAPSAMLI TANIMLAMA

#### **"PATTERN UYGULA" KOMUTU:**
- **Pattern**: Hem kod hem tasarım pattern'ı aynı anda uygulanır
- **Kod Pattern'i**: Sadece backend/logic yapısı uygulanır  
- **Tasarım Pattern'i**: Sadece frontend/UI yapısı uygulanır

#### **PAGE MODÜLÜ = MASTER PATTERN**
Page modülü artık **standart şablon** olarak kullanılır. Tüm yeni modüller Page pattern'ına uyar.

#### **KOD PATTERN YAPISI (Page → Diğer Modüller):**
- ✅ **Aynı dosya yapısı**: Models, Services, Controllers, Livewire, etc.
- ✅ **Aynı çoklu dil sistemi**: JSON field yapısı, getTranslated() methodları
- ✅ **Aynı SEO sistemi**: seoDataCache, allLanguagesSeoData patterns
- ✅ **Aynı validation sistemi**: MultiLang rules, SlugHelper entegrasyonu
- ✅ **Aynı component yapısı**: switchLanguage(), save(), load methodları
- ✅ **Modern PHP standards**: declare(strict_types=1), readonly, DTOs, Exceptions

#### **TASARIM PATTERN YAPISI (Page → Diğer Modüller):**
- **Form Sayfası**: `Modules/Page/manage` sayfasını pattern al
  - Form floating labels kullan
  - Pretty checkbox/switch kullan
  - **Selectbox**: Choices.js kullan (Portfolio manage örneği)
  - **Dil Sekmeleri**: Page'deki dil değiştirme UI'ı aynı şekilde
  - **SEO Paneli**: Page'deki SEO tab yapısı aynı şekilde
- **Sortable Liste**: Portfolio kategori listesini pattern al
  - JS ile drag-drop efekti
- **Tablo Listeleme**: Portfolio listesini pattern al
  - DataTable formatında
- **Basit Liste**: ModuleManagement page sayfasını pattern al
  - Basit liste görünümü

#### **PATTERN UYGULAMA ÖRNEKLERİ:**
```bash
# Her ikisini de uygula
"Page pattern'ını Announcement'a uygula"

# Sadece kod
"Page kod pattern'ını Announcement'a uygula" 

# Sadece tasarım
"Page tasarım pattern'ını Announcement'a uygula"
```

#### **ÖZEL DURUMLAR:**
- **Homepage alanı**: Sadece Page modülünde olur, diğer modüllerde olmaz
- **Modül-specific alanlar**: Her modülün kendine özel alanları olabilir
- **Core pattern**: Çoklu dil + SEO + Modern PHP her modülde ZORUNLU

#### **KURAL**: Yeni çalışmalar bu pattern'ları temel alsın!

## 🚀 MODERN MODÜL PATTERN - Page Modülü (Laravel 12 + PHP 8.3+)

**Konum**: `Modules/Page/` - **Bizim Standart Pattern**

### ✅ SOLID Architecture (10/10 Kalite)

#### **1. Response DTO'ları (Modern PHP 8.3+)**
```php
// PageOperationResult.php - readonly class pattern
readonly class PageOperationResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public string $type = 'success',
        public ?Page $data = null,
        public ?array $meta = null
    ) {}

    public static function success(string $message, ?Page $data = null): self
    {
        return new self(success: true, message: $message, data: $data);
    }
}
```

#### **2. Cache Strategy Enum**
```php
// CacheStrategy.php - Smart caching
enum CacheStrategy: string
{
    case ADMIN_FRESH = 'admin_fresh';
    case PUBLIC_CACHED = 'public_cached';

    public function shouldCache(): bool
    {
        return match($this) {
            self::ADMIN_FRESH => false,
            self::PUBLIC_CACHED => true,
        };
    }

    public static function fromRequest(): self
    {
        return request()->is('admin*') ? self::ADMIN_FRESH : self::PUBLIC_CACHED;
    }
}
```

#### **3. Custom Exception Sınıfları**
```php
// PageException.php - Abstract base
abstract class PageException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?array $context = null
    ) {}
}

// PageNotFoundException.php - Specific exceptions
class PageNotFoundException extends PageException
{
    public static function withId(int $id): self
    {
        return new self(
            message: "Page with ID {$id} not found",
            context: ['page_id' => $id]
        );
    }
}
```

#### **4. Modern Service (Exception-First)**
```php
// PageService.php
declare(strict_types=1);

readonly class PageService
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getPage(int $id): Page
    {
        return $this->pageRepository->findById($id) 
            ?? throw PageNotFoundException::withId($id);
    }

    public function createPage(array $data): PageOperationResult
    {
        try {
            $page = $this->pageRepository->create($data);
            
            return PageOperationResult::success(
                message: __('page::admin.page_created_successfully'),
                data: $page
            );
        } catch (Throwable $e) {
            throw PageCreationException::withDatabaseError($e->getMessage());
        }
    }
}
```

#### **5. Modern Repository (Smart Caching)**
```php
// PageRepository.php
declare(strict_types=1);

readonly class PageRepository implements PageRepositoryInterface
{
    public function findById(int $id): ?Page
    {
        $strategy = CacheStrategy::fromRequest();
        
        if (!$strategy->shouldCache()) {
            return $this->model->where('page_id', $id)->first();
        }
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('page_id', $id)->first()
            );
    }
}
```

#### **6. Modern Livewire Component (3.5+)**
```php
// PageComponent.php
declare(strict_types=1);

#[Layout('admin.layout')]
class PageComponent extends Component
{
    private PageService $pageService;
    
    // Livewire 3.5+ dependency injection pattern
    public function boot(PageService $pageService): void
    {
        $this->pageService = $pageService;
    }

    #[Computed]
    public function availableSiteLanguages(): array
    {
        return $this->availableSiteLanguages ??= TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }

    public function toggleActive(int $id): void
    {
        try {
            $result = $this->pageService->togglePageStatus($id);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.error'),
                'message' => $result->message,
                'type' => $result->type,
            ]);
        } catch (\Exception $e) {
            // Error handling
        }
    }
}
```

### 🎯 **YENİ MODÜL OLUŞTURURKEN:**

1. **Response DTO'ları oluştur** (XxxOperationResult, BulkOperationResult)
2. **Cache Strategy** enum'unu kopyala ve adapt et
3. **Custom Exception'lar** modülüne özel oluştur
4. **Service Layer**: `declare(strict_types=1)`, readonly class, exception-first
5. **Repository**: Smart caching, modern PHP syntax
6. **Livewire**: `#[Computed]`, boot() dependency injection, type declarations

### 📊 **Kalite Standartları:**
- ✅ **SOLID Principles**: %100 uyumlu
- ✅ **Modern PHP 8.3+**: declare, readonly, match, nullsafe operator
- ✅ **Laravel 12**: Dependency injection, modern patterns
- ✅ **Exception-First**: Defensive programming
- ✅ **Smart Caching**: Performance optimization
- ✅ **Type Safety**: Strict types everywhere

### 🚨 **KRİTİK KURAL:**
**Her yeni modül Page modülünü pattern alacak! Aynı dosya yapısı, aynı modern kod standartları.**

