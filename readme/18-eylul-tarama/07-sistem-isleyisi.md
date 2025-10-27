# 🔧 SİSTEM İŞLEYİŞİ DOKÜMANTASYONU

## 1. 🌐 DİL SİSTEMİ İŞLEYİŞİ

### Dil Yapılandırması
```php
// İki ayrı dil sistemi var:

1. ADMIN DİLLERİ (system_languages)
   - Tablo: system_languages
   - Session: admin_locale
   - Middleware: SetLocaleMiddleware
   - Helper: get_admin_languages()
   - Default: tr

2. SITE DİLLERİ (site_languages)
   - Tablo: Tenant DB → languages
   - Session: site_locale
   - Middleware: SiteSetLocaleMiddleware
   - Helper: get_site_languages()
   - Default: tenant ayarına göre
```

### Dil Seçimi Flow
```
User Request
    ↓
Middleware (SetLocaleMiddleware)
    ↓
1. Session'da locale var mı? → Kullan
2. User preference var mı? → Kullan
3. Browser language? → Match et
4. Default language → Kullan
    ↓
app()->setLocale($locale)
```

### Translation Sistemi
```php
// Model Translation (JSON)
$page->setTranslation('title', 'en', 'English Title');
$page->setTranslation('title', 'tr', 'Türkçe Başlık');

// Otomatik dil algılama
$page->title; // Aktif locale'e göre

// Spesifik dil
$page->getTranslation('title', 'en');
```

---

## 2. 🏗️ MODÜL SİSTEMİ

### Modül Yapısı
```
Modules/
└── ModuleName/
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   ├── Livewire/
    │   │   └── Middleware/
    │   ├── Models/
    │   ├── Services/
    │   └── Providers/
    ├── config/
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── lang/
    ├── resources/
    │   └── views/
    ├── routes/
    │   ├── web.php
    │   ├── api.php
    │   └── admin.php
    └── module.json
```

### Modül Aktivasyon Flow
```
1. module.json okunur
2. ServiceProvider register edilir
3. Routes yüklenir (admin/web/api)
4. Migrations çalıştırılır
5. Assets publish edilir
6. Cache temizlenir
```

### Modül İzin Sistemi
```php
// Her modül için tenant bazlı izinler
module_tenant_settings:
- tenant_id
- module_name
- is_active
- settings (JSON)

// Check permission
if (tenant_can_use_module('AI')) {
    // Modül kullanılabilir
}
```

---

## 3. 🏢 MULTI-TENANT SİSTEMİ

### Tenant İzolasyonu
```php
// Central Database
- tenants (ana tenant bilgileri)
- domains (tenant domainleri)
- users (central users)
- module_tenant_settings

// Tenant Database (Her tenant için ayrı)
- users (tenant users)
- pages, posts, etc.
- tenant specific data
```

### Tenant Switching Flow
```
Domain Request (subdomain.example.com)
    ↓
TenancyMiddleware
    ↓
1. Domain lookup → tenant bulunur
2. Database connection switch
3. Config override (cache, filesystem, etc.)
4. Session isolation
5. Cache prefix set
    ↓
Tenant context ready
```

### Tenant Oluşturma
```php
// 1. Tenant kaydı
$tenant = Tenant::create(['name' => 'New Company']);

// 2. Domain ataması
$tenant->domains()->create(['domain' => 'company.example.com']);

// 3. Database oluşturma
Artisan::call('tenants:migrate', ['--tenants' => [$tenant->id]]);

// 4. Seed data
Artisan::call('tenants:seed', ['--tenants' => [$tenant->id]]);

// 5. Module permissions
$tenant->modules()->attach(['Page', 'Portfolio']);
```

---

## 4. 🎨 ADMIN/FRONTEND YAPILANDIRMASI

### Admin Panel
```
Route: /admin/*
Middleware: auth, admin, tenant
Layout: resources/views/admin/layout.blade.php
CSS Framework: Tabler.io + Bootstrap 5
JS: Alpine.js + Livewire

Standart yapı:
- Helper.blade.php (üst bilgi)
- DataTable (listing)
- Tabs (detay)
- Modal (işlemler)
```

### Frontend
```
Route: /* (dynamic routing)
Middleware: web, tenant, locale
Layout: Tema bazlı
CSS: Tailwind CSS
JS: Alpine.js

Theme sistemi:
- themes tablosu (merkezi)
- tenant_themes (tenant seçimi)
- Blade inheritance
```

---

## 5. 🔗 SLUG VE URL YÖNETİMİ

### Slug Sistemi
```php
// Otomatik slug oluşturma
Str::slug($title); // "örnek-başlık" → "ornek-baslik"

// Unique slug
$slug = SlugService::createSlug(Page::class, 'slug', $title);

// Multilingual slug
'slugs' => [
    'tr' => 'hakkimizda',
    'en' => 'about-us'
]
```

### Dynamic Routing
```php
// Route tanımı
Route::get('{slug}', [PageController::class, 'show'])
    ->where('slug', '.*');

// Resolver
1. Check page slugs
2. Check category slugs
3. Check custom routes
4. 404 if not found
```

### URL Builder
```php
// Multi-language URL
url_for('page.show', ['slug' => $page->slug, 'locale' => 'en']);
// Output: /en/about-us

// Tenant aware URL
tenant_url('page.show', ['slug' => $page->slug]);
// Output: https://tenant.example.com/about-us
```

---

## 6. 📊 TAB SİSTEMİ (Admin Panel)

### Tab Yapısı
```blade
{{-- Ana tab container --}}
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#general">
            Genel
        </a>
    </li>
</ul>

{{-- Tab içerikleri --}}
<div class="tab-content">
    <div class="tab-pane active" id="general">
        {{-- Form fields --}}
    </div>
</div>
```

### Dynamic Tab Loading
```javascript
// Lazy loading for performance
document.querySelector('[data-bs-toggle="tab"]').addEventListener('shown.bs.tab', function (e) {
    loadTabContent(e.target.getAttribute('href'));
});
```

---

## 7. 🔄 LIVEWIRE COMPONENT SİSTEMİ

### Component Lifecycle
```php
class PageManageComponent extends Component
{
    // 1. Mount - ilk yükleme
    public function mount($id = null) {
        if ($id) {
            $this->loadPage($id);
        }
    }

    // 2. Render - her update'te
    public function render() {
        return view('livewire.page-manage');
    }

    // 3. Updated - property değişiminde
    public function updated($propertyName) {
        $this->validateOnly($propertyName);
    }
}
```

### Wire:model Binding
```blade
{{-- Two-way data binding --}}
<input wire:model="page.title" type="text">

{{-- Lazy update (on blur) --}}
<input wire:model.lazy="page.content">

{{-- Debounce (500ms) --}}
<input wire:model.debounce.500ms="search">
```

---

## 8. 🗄️ CACHE STRATEJİSİ

### Cache Layers
```php
// 1. Route Cache (production)
php artisan route:cache

// 2. Config Cache
php artisan config:cache

// 3. View Cache
php artisan view:cache

// 4. Query Cache
Cache::tags(['pages'])->remember("page.{$id}", 3600, function() {
    return Page::find($id);
});

// 5. Response Cache
middleware('cache.response:3600');
```

### Cache Invalidation
```php
// Tag based invalidation
Cache::tags(['pages'])->flush();

// Specific key
Cache::forget("page.{$id}");

// Pattern deletion (Redis)
Cache::deletePattern('page.*');
```

---

## 9. 🔐 PERMISSION SİSTEMİ

### Role-Permission Yapısı
```php
// Roles
- super-admin (tüm yetkiler)
- admin (tenant admin)
- editor (içerik yönetimi)
- user (normal kullanıcı)

// Permissions
- module.view
- module.create
- module.edit
- module.delete

// Check
if ($user->can('page.edit')) {
    // İzin var
}
```

### Middleware Kontrolü
```php
Route::group(['middleware' => ['can:page.edit']], function () {
    // Protected routes
});
```

---

## 10. 🚀 QUEUE SİSTEMİ

### Queue Configuration
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'redis'),

'connections' => [
    'redis' => [
        'driver' => 'redis',
        'queue' => 'default',
        'retry_after' => 90,
    ],
],
```

### Job Processing
```php
// Job dispatch
ProcessAIContent::dispatch($data)
    ->onQueue('ai')
    ->delay(now()->addSeconds(10));

// Job class
class ProcessAIContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle() {
        // Process
    }

    public function failed($exception) {
        // Handle failure
    }
}
```

### Horizon Monitoring
```bash
# Start Horizon
php artisan horizon

# Status check
php artisan horizon:status

# Pause/Continue
php artisan horizon:pause
php artisan horizon:continue
```

---

## 11. 🔍 SEO SİSTEMİ

### SEO Data Structure
```php
// Global SEO (her sayfa için)
seo_settings:
- entity_type: "App\Models\Page"
- entity_id: 1
- meta_title: JSON per language
- meta_description: JSON per language
- meta_keywords: JSON per language
- og_image: media_id
- schema_markup: JSON-LD

// Usage
$page->seoSettings->meta_title;
$page->seoSettings->getTranslation('meta_description', 'en');
```

### SEO Rendering
```blade
{{-- Layout head --}}
<title>{{ $seo->meta_title ?? config('app.name') }}</title>
<meta name="description" content="{{ $seo->meta_description }}">
<meta property="og:title" content="{{ $seo->og_title }}">
<meta property="og:image" content="{{ $seo->og_image_url }}">

{{-- Schema.org --}}
<script type="application/ld+json">
{!! $seo->schema_markup !!}
</script>
```

---

## 12. 📤 API SİSTEMİ

### API Authentication
```php
// Token based (Sanctum)
Route::post('/api/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (Hash::check($request->password, $user->password)) {
        $token = $user->createToken('api')->plainTextToken;
        return ['token' => $token];
    }
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pages', PageApiController::class);
});
```

### API Response Format
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Page Title",
        "translations": {
            "en": {...},
            "tr": {...}
        }
    },
    "meta": {
        "total": 100,
        "per_page": 15,
        "current_page": 1
    }
}
```

---

## 📋 SYSTEM FLOW DIAGRAM

```
User Request
    ↓
Domain Resolution → Tenant Identification
    ↓
Middleware Stack
    ├── TenancyMiddleware → Database Switch
    ├── SetLocaleMiddleware → Language Set
    ├── AuthMiddleware → User Check
    └── ModuleAccessMiddleware → Permission Check
    ↓
Route Resolution
    ├── Static Routes
    ├── Module Routes
    └── Dynamic Routes (Slug based)
    ↓
Controller/Livewire Component
    ↓
Service Layer → Business Logic
    ↓
Model/Repository → Data Access
    ↓
Cache Layer → Performance
    ↓
View Rendering
    ↓
Response
```

Bu dokümantasyon, sistemin tüm kritik bileşenlerinin nasıl çalıştığını ve birbirleriyle nasıl etkileşime girdiğini açıklamaktadır.