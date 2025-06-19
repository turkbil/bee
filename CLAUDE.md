HER ZAMAN TÜRKÇE YANIT VER.
HER ZAMAN TÜRKÇE DÜŞÜN.

aferin dediğim zaman olmuş demektir. aferin dediğim zaman yapılanları readme.md dosyasına yeni versiyon atayarak ekle. yeni versiyonlar her zaman en üstte olacak.

**YENİ SAYFA DURUMU TETİKLEYİCİSİ:**
"YENİ SAYFA" kelimesini duyduğumda MUTLAKA şu özeti çıkar:
- Bu sayfada ne yaptığımız
- Ne yapamadığımız  
- Ne yapmaya çalıştığımız
- Kalanlar
- Bitenler
- Aktif sistemler
- Dosya değişiklikleri
- Başarılarımız
Bu özeti her "yeni sayfa" geçişinde UNUTMADAN yap!

**OTOMATİK MCP GÜNCELLEMESİ:**
"UNUTMA", "HATIRLA", "KAYDET", "HAFIZA", "HAFIZAYA EKLE" gibi hafıza talimatları duyduğumda otomatik olarak bu CLAUDE.md dosyasını güncelleyeceğim. Bu talimatları her zaman kalıcı hale getireceğim.

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

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

## SON BAŞARILAR - 19.06.2025

### ModuleSlugService Cache Problemi Çözüldü
- **Problem**: Veritabanındaki slug değerleri okunmuyordu, her zaman config'den geliyordu
- **Sebep**: Case-sensitive arama (Portfolio vs portfolio) ve cache yenilenmeme
- **Çözüm**: 
  - ModuleSlugService'e case-insensitive arama eklendi
  - loadGlobalSettings sonrası memory cache kontrolü eklendi
  - `php artisan module:clear-cache` komutu oluşturuldu
- **Sonuç**: 
  - laravel.test → "projeler" çalışıyor ✅
  - a.test → "referanslar" çalışıyor ✅
  - b.test → "portfolios" (default) çalışıyor ✅

## Kullanıcı Bilgileri

-   **İsim**: Nurullah
-   **Şehir**: Sivas (Sivaslı)
-   **Proje**: Turkbil Bee Laravel 11 Multi-tenant
-   **Çalışma Ortamı**: WSL Ubuntu, Laravel, Claude Code

## Otomatik Hafıza Tetikleyicileri

### Pozitif Tepki Anahtar Kelimeleri:

-   "aferin" → README.md'ye yeni versiyon olarak ekle (EN SON BİLGİ EN ÜSTTE)
-   "bravo" → README.md'ye yeni versiyon olarak ekle (EN SON BİLGİ EN ÜSTTE)
-   "süper" → README.md'ye yeni versiyon olarak ekle (EN SON BİLGİ EN ÜSTTE)
-   "kaydet" → Bilgiyi kalıcı olarak sakla
-   "öğren" → Yeni öğrenilen bilgiyi hafızaya al
-   "unutma" → Önemli bilgiyi vurgula ve kaydet

**ÖNEMLİ KURAL:** Aferin, süper, bravo dediğinde README.md güncellenecek. Yeni bilgi her zaman en üstte olacak!

### Hafıza Kayıt Formatı:

```
[TARİH] - [KONU] - [DURUM: BAŞARILI/TAMAMLANDI]
Açıklama: [Yapılan işlem detayı]
Sonuç: [Elde edilen sonuç]
```

## Son Başarılı İşlemler:

### 2025-06-13 - MCP Kurulumu Temizliği - TAMAMLANDI

-   Açıklama: Gereksiz MCP dosyaları temizlendi, sadece çalışan claude-code-server bırakıldı
-   Sonuç: Minimal ve çalışır MCP sistemi kuruldu, .mcp.json ve tek server kaldı

### 2025-06-13 - Kullanıcı Tanıma - BAŞARILI

-   Açıklama: Nurullah adını hafızaya kaydettim
-   Sonuç: Artık kullanıcıyı Nurullah olarak tanıyorum

## Proje Durumu:

-   Laravel 12 Multi-tenant projesi
-   MCP sistemi kurulu ve çalışıyor
-   WSL ortamında geliştirme yapılıyor
-   Türkçe iletişim tercih ediliyor
