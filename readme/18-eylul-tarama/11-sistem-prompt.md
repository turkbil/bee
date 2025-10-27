# 🤖 SİSTEM PROMPT - LARAVEL CMS PLATFORM

## SİSTEM TANIMI

Bu bir Laravel 11 tabanlı, multi-tenant, AI-powered modern CMS platformudur. Sistem modüler yapıda olup, her modül kendi MVC yapısına sahiptir.

## TEKNOLOJİ STACK

- **Backend**: Laravel 11, PHP 8.3
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Admin Panel**: Tabler.io, Bootstrap 5
- **Database**: MySQL 8 (multi-tenant)
- **Cache**: Redis
- **Queue**: Redis + Horizon
- **AI**: OpenAI GPT-4, Anthropic Claude 3, DeepSeek
- **Monitoring**: Telescope, Pulse
- **Container**: Docker

## MODÜL YAPISI

Sistemde 14 ana modül bulunmaktadır:

1. **AI Module**: AI içerik üretimi, çeviri, SEO optimizasyonu
2. **Page Module**: Sayfa yönetimi (master pattern)
3. **Portfolio Module**: Portföy ve proje yönetimi
4. **MenuManagement**: Menü ve navigasyon yönetimi
5. **LanguageManagement**: Çoklu dil sistemi
6. **SeoManagement**: SEO ayarları ve optimizasyon
7. **SettingManagement**: Sistem ve tenant ayarları
8. **ThemeManagement**: Tema yönetimi
9. **UserManagement**: Kullanıcı ve yetki yönetimi
10. **WidgetManagement**: Widget sistemi
11. **TenantManagement**: Multi-tenant yönetimi
12. **Studio**: Görsel editör ve tema düzenleyici
13. **Announcement**: Duyuru ve haber sistemi
14. **ModuleManagement**: Modül yönetimi

## DİL SİSTEMİ

İki katmanlı dil sistemi:

**Admin Dilleri (system_languages)**
- Merkezi veritabanında saklanır
- Admin panel dili
- Session: admin_locale

**Site Dilleri (site_languages)**
- Tenant veritabanında saklanır
- Frontend site dilleri
- Session: site_locale

Translation sistemi JSON tabanlı çalışır:
```php
$page->setTranslation('title', 'tr', 'Başlık');
$page->title; // Aktif locale'e göre döner
```

## MULTI-TENANT MİMARİ

Her tenant için:
- Ayrı veritabanı
- Ayrı subdomain (tenant.example.com)
- İzole cache ve session
- Modül bazlı yetkilendirme

Tenant switching otomatik olarak domain üzerinden yapılır.

## ROUTE YAPISI

```
/admin/* → Admin panel (auth required)
/api/* → API endpoints (token auth)
/{slug} → Dynamic page routing
```

## LIVEWIRE COMPONENTS

Tüm admin formları Livewire component'leri ile yönetilir:
- PageManageComponent
- MenuManageComponent
- SettingsComponent
- vb.

Wire:model ile two-way data binding sağlanır.

## AI ENTEGRASYONU

AI servisleri şu işlevler için kullanılır:
- İçerik üretimi
- Çeviri (context-aware)
- SEO optimizasyonu
- Meta tag önerileri
- Görsel analizi
- Otomatik tagging

Provider'lar:
- OpenAI (GPT-4, GPT-3.5)
- Anthropic (Claude 3)
- DeepSeek

## QUEUE SİSTEMİ

Redis tabanlı queue ile:
- AI içerik üretimi
- Toplu mail gönderimi
- Resim optimizasyonu
- Büyük import/export işlemleri

Horizon ile monitoring yapılır.

## GÜVENLİK

- Role-based access control (RBAC)
- API token authentication (Sanctum)
- CSRF protection
- XSS prevention
- SQL injection koruması
- Rate limiting

## CACHE STRATEJİSİ

Çok katmanlı cache:
1. Route cache
2. Config cache
3. View cache
4. Query cache (Redis)
5. Response cache

Tag-based cache invalidation kullanılır.

## KRİTİK DOSYA YOLLARI

```
/Modules/* → Modül dosyaları
/app/Services/* → Core servisler
/resources/views/admin/* → Admin panel view'ları
/resources/views/livewire/* → Livewire component view'ları
/config/* → Konfigürasyon dosyaları
/routes/* → Route tanımları
```

## ÖNEMLİ KURALLAR

1. **Her admin sayfasının tepesinde helper.blade.php include edilmeli**
2. **Page modülü pattern'i yeni modüllerde takip edilmeli**
3. **Tüm model'ler HasTranslations trait'i kullanmalı**
4. **Admin panel Tabler.io + Bootstrap, Frontend Tailwind CSS kullanmalı**
5. **AI servis duplikasyonları temizlenmeli (AIService_*.php silinmeli)**
6. **Production'da APP_DEBUG=false olmalı**
7. **Queue işlemleri asla manuel başlatılmamalı**

## TEST VE DEPLOYMENT

```bash
# Test için
php artisan app:clear-all && php artisan migrate:fresh --seed

# Cache temizleme
php artisan module:clear-cache && php artisan responsecache:clear

# Deployment
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

## MEVCUT SORUNLAR

1. **AI Service duplikasyonu** (15,000+ satır gereksiz kod)
2. **N+1 query problemleri**
3. **Cache strategy eksikliği**
4. **Büyük dosyalar** (2000+ satırlık service'ler)
5. **Security header'lar eksik**
6. **Test coverage düşük** (%5)

## GELİŞTİRME ÖNCELİKLERİ

1. Code cleanup ve refactoring
2. Performance optimization
3. Security improvements
4. Test coverage artırma
5. Microservices'e geçiş hazırlığı

## ÇALIŞMA KURALLARI

- **Türkçe iletişim kullan**
- **Extended reasoning yap**
- **Otomatik devam et, sorma**
- **CLAUDE.md dosyasındaki kurallara uy**
- **Asla manuel queue işlemi yapma**
- **Log temizleme: truncate -s 0 laravel.log**

Bu sistem, modern bir CMS platformu olarak tasarlanmış olup, AI yetenekleri ve multi-tenant yapısı ile enterprise kullanıma uygundur. Modüler yapısı sayesinde kolayca genişletilebilir ve özelleştirilebilir.