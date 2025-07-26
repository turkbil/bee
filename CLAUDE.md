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
- 🔄 **Portfolio Modülü**: Hazırlanabilir
- 🔄 **Announcement Modülü**: Hazırlanabilir
- 🔄 **Diğer Modüller**: İhtiyaç halinde eklenebilir

**Detaylı dokümantasyon**: `SlugHelper_README.md`

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

### UI Pattern Sistemi (STANDART ŞABLONLAR):
- **Form Sayfası**: `Modules/Page/manage` sayfasını pattern al
  - Form floating labels kullan
  - Pretty checkbox/switch kullan
  - **Selectbox**: Choices.js kullan (Portfolio manage örneği)
- **Sortable Liste**: Portfolio kategori listesini pattern al
  - JS ile drag-drop efekti
- **Tablo Listeleme**: Portfolio listesini pattern al
  - DataTable formatında
- **Basit Liste**: ModuleManagement page sayfasını pattern al
  - Basit liste görünümü
- **KURAL**: Yeni çalışmalar bu pattern'ları temel alsın!

