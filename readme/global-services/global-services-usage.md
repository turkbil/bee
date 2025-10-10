# Global Servisler Kullanım Kılavuzu

Bu dokümant, Page modülünden global hale getirilen servislerin nasıl kullanılacağını açıklar.

## 📚 Detaylı Dokümantasyon

Her global servis için ayrı ayrı detaylı dokümantasyon mevcuttur:

- **[Global Content Editor](/Users/nurullah/Desktop/cms/laravel/readme/GLOBAL_CONTENT_EDITOR.md)** - HugeRTE editor component'i
- **[Global SEO Service](/Users/nurullah/Desktop/cms/laravel/readme/GLOBAL_SEO_SERVICE.md)** - SEO yönetim sistemi
- **[Global Tab Service](/Users/nurullah/Desktop/cms/laravel/readme/GLOBAL_TAB_SERVICE.md)** - Tab yönetim sistemi
- **[AI Assistant Panel](/Users/nurullah/Desktop/cms/laravel/readme/ai-assistant/README.md)** - AI asistan panel sistemi
- **[Global Cache Service](/Users/nurullah/Desktop/cms/laravel/readme/GLOBAL_CACHE_SERVICE.md)** - Model cache yönetim sistemi

## 1. Global Content Editor Component

**Dosya Konumu**: `/resources/views/admin/components/content-editor.blade.php`

### Kullanım:
```blade
@include('admin.components.content-editor', [
    'fieldName' => 'body',           // Alan adı (opsiyonel, varsayılan: 'body')
    'modelPath' => 'multiLangInputs', // Model yolu (opsiyonel, varsayılan: 'multiLangInputs')
    'label' => 'İçerik',             // Label metni (opsiyonel, varsayılan: 'İçerik')
    'lang' => $lang                  // Dil kodu (zorunlu)
])
```

### Özellikler:
- HugeRTE editor entegrasyonu
- Çoklu dil desteği
- Livewire wire:model desteği
- Otomatik unique ID üretimi

---

## 2. GlobalSeoService

**Dosya Konumu**: `/app/Services/GlobalSeoService.php`

### Kullanım:

#### Service Injection:
```php
use App\Services\GlobalSeoService;

// Constructor'da inject
public function __construct(
    protected GlobalSeoService $seoService
) {}
```

#### Direkt Kullanım:
```php
use App\Services\GlobalSeoService;

// SEO skoru hesaplama
$seoScore = GlobalSeoService::calculateSeoScore($seoData, 'page');

// SEO validasyon
$validation = GlobalSeoService::validateSeoData($seoData, 'page');

// Modül konfigürasyonu alma
$config = GlobalSeoService::getSeoConfig('page');
```

### Metodlar:

#### `calculateSeoScore(array $seoData, string $module = 'default'): array`
SEO skorunu hesaplar.

**Parametreler:**
- `$seoData`: SEO verileri array'i
- `$module`: Modül adı (page, portfolio, vs.)

**Dönüş:**
```php
[
    'total_score' => 85,
    'details' => [
        'title' => ['score' => 20, 'max' => 20],
        'description' => ['score' => 15, 'max' => 20],
        // ...
    ]
]
```

#### `validateSeoData(array $seoData, string $module = 'default'): array`
SEO verilerini doğrular.

#### `getSeoConfig(string $module = 'default'): array`
Modül-spesifik SEO konfigürasyonunu getirir.

---

## 3. GlobalTabService

**Dosya Konumu**: `/app/Services/GlobalTabService.php`

### Kullanım:

```php
use App\Services\GlobalTabService;

// Tab tamamlanma durumu
$tabStatus = GlobalTabService::getTabCompletionStatus($data, 'page');

// Tüm tab'ları alma
$tabs = GlobalTabService::getAllTabs('page');

// Aktif tab belirleme
$activeTab = GlobalTabService::getActiveTab($tabStatus);
```

### Metodlar:

#### `getTabCompletionStatus(array $data, string $module = 'default'): array`
Tab tamamlanma durumunu hesaplar.

**Dönüş:**
```php
[
    'general' => true,
    'content' => true,
    'seo' => false,
    'settings' => true
]
```

#### `getAllTabs(string $module = 'default'): array`
Modül için tanımlı tüm tab'ları getirir.

#### `getActiveTab(array $tabStatus): string`
İlk tamamlanmamış tab'ı döner.

---

## 4. GlobalSeoRepository

**Dosya Konumu**: `/app/Repositories/GlobalSeoRepository.php`  
**Interface**: `/app/Contracts/GlobalSeoRepositoryInterface.php`

### Service Provider'da Binding:

```php
// Modules/Page/Providers/PageServiceProvider.php
$this->app->bind(
    \App\Contracts\GlobalSeoRepositoryInterface::class,
    \App\Repositories\GlobalSeoRepository::class
);
```

### Kullanım:

#### Constructor Injection:
```php
use App\Contracts\GlobalSeoRepositoryInterface;

public function __construct(
    protected GlobalSeoRepositoryInterface $seoRepository
) {}
```

#### Livewire Component'te:
```php
public function mount()
{
    $this->seoRepository = app(\App\Contracts\GlobalSeoRepositoryInterface::class);
}
```

### Metodlar:

#### `getSeoData(Model $model, string $language): array`
Model için SEO verilerini getirir.

#### `saveSeoData(Model $model, string $language, array $seoData): bool`
Model için SEO verilerini kaydeder.

#### `calculateSeoScore(array $seoData, string $module = 'default'): array`
SEO skorunu hesaplar.

#### `deleteSeoData(Model $model, string $language): bool`
Model için SEO verilerini siler.

---

## 5. Modül Entegrasyonu

### Yeni Modülde Kullanım:

1. **Service Provider'da binding ekle:**
```php
$this->app->bind(
    \App\Contracts\GlobalSeoRepositoryInterface::class,
    \App\Repositories\GlobalSeoRepository::class
);
```

2. **Livewire Component'te kullan:**
```php
use App\Services\GlobalSeoService;
use App\Services\GlobalTabService;
use App\Contracts\GlobalSeoRepositoryInterface;

class YourManageComponent extends Component
{
    protected GlobalSeoRepositoryInterface $seoRepository;

    public function mount()
    {
        $this->seoRepository = app(GlobalSeoRepositoryInterface::class);
    }

    public function save()
    {
        // SEO verileri kaydet
        $this->seoRepository->saveSeoData($model, $language, $seoData);
        
        // Tab durumunu güncelle
        $this->tabCompletionStatus = GlobalTabService::getTabCompletionStatus($data, 'your_module');
    }
}
```

3. **Blade template'te content editor kullan:**
```blade
@include('admin.components.content-editor', [
    'fieldName' => 'body',
    'modelPath' => 'multiLangInputs',
    'label' => 'İçerik',
    'lang' => $lang
])
```

---

## 6. Konfigürasyon

### Modül-spesifik SEO konfigürasyonu:
`config/your_module.php` dosyasında:

```php
return [
    'seo' => [
        'title_max_length' => 60,
        'description_max_length' => 160,
        'keywords_max_count' => 10,
        'scoring' => [
            'title' => 20,
            'description' => 20,
            'keywords' => 15,
            // ...
        ]
    ]
];
```

### Tab konfigürasyonu:
```php
return [
    'tabs' => [
        'general' => 'Genel',
        'content' => 'İçerik', 
        'seo' => 'SEO',
        'settings' => 'Ayarlar'
    ]
];
```

Bu global servisler sayesinde tüm modüller tutarlı SEO, tab ve içerik editörü deneyimi yaşayacak.