# Global Tab Service

GlobalTabService, tüm modüllerin tab sistemi için ortak altyapı sağlar. Her modül kendi tab konfigürasyonunu tanımlayabilir ve sistemin sunduğu özelliklerden faydalanabilir.

## 📁 Dosya Konumu
```
/app/Services/GlobalTabService.php
```

## 🎯 Özellikler

### ✅ Modül-Agnostic Tasarım
- Her modül için ayrı tab konfigürasyonu
- Varsayılan tab yapısı (basic, seo, code)
- Esnek konfigürasyon sistemi

### ✅ Tab Completion Sistemi
- Her tab için completion durumu
- Progress yüzdesi hesaplama
- Gerekli alan kontrolü

### ✅ JavaScript Entegrasyonu
- Tab navigation için config
- LocalStorage desteği
- Real-time validation

### ✅ Çoklu Modül Desteği
- `page`, `portfolio`, `announcement` gibi modüller
- Her modül için özelleştirilebilir tab'lar
- Modül-specific storage keys

## 📖 Kullanım Örnekleri

### Temel Kullanım
```php
use App\Services\GlobalTabService;

// Page modülü için tab'ları al
$tabs = GlobalTabService::getAllTabs('page');

// Tab completion durumunu hesapla
$status = GlobalTabService::getTabCompletionStatus($formData, 'page');

// JavaScript config oluştur
$jsConfig = GlobalTabService::getJavaScriptConfig('page');
```

### Livewire Component'te Kullanım
```php
use App\Services\GlobalTabService;

class ModuleManageComponent extends Component 
{
    protected function loadConfigurations()
    {
        $this->tabConfig = GlobalTabService::getAllTabs('page');
        $this->activeTab = GlobalTabService::getDefaultTabKey('page');
    }
    
    protected function updateTabCompletionStatus()
    {
        $this->tabCompletionStatus = GlobalTabService::getTabCompletionStatus($allData, 'page');
    }
}
```

### Service Sınıflarında Kullanım
```php
use App\Services\GlobalTabService;

class ModuleService 
{
    public function prepareFormData(int $id, string $language): array
    {
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'module_name');
        
        return [
            'data' => $data,
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('module_name')
        ];
    }
}
```

## 🔧 API Referansı

### getAllTabs(string $module = 'default'): array
Modül için tüm tab'ları döndürür.

**Örnek Response:**
```php
[
    [
        'key' => 'basic',
        'name' => 'Temel Bilgiler',
        'icon' => 'fas fa-file-text',
        'required_fields' => ['title', 'slug', 'content']
    ],
    [
        'key' => 'seo',
        'name' => 'SEO',
        'icon' => 'fas fa-search',
        'required_fields' => ['seo_title']
    ]
]
```

### getTabCompletionStatus(array $data, string $module = 'default'): array
Tab completion durumunu hesaplar.

**Örnek Response:**
```php
[
    'basic' => [
        'complete' => true,
        'progress' => 100,
        'required_count' => 3,
        'completed_count' => 3
    ],
    'seo' => [
        'complete' => false,
        'progress' => 50,
        'required_count' => 2,
        'completed_count' => 1
    ]
]
```

### getJavaScriptConfig(string $module = 'default'): array
JavaScript için gerekli konfigürasyonu oluşturur.

**Örnek Response:**
```php
[
    'storage_key' => 'page_active_tab',
    'save_active_tab' => true,
    'restore_on_load' => true,
    'real_time_validation' => true,
    'submit_button_states' => true,
    'tabs' => [...],
    'module' => 'page'
]
```

## 🎨 Modül Konfigürasyonu

### Config Dosyası Örneği
```php
// config/page.php (modül-specific)
return [
    'tabs' => [
        [
            'key' => 'basic',
            'name' => __('admin.basic_information'),
            'icon' => 'fas fa-file-text',
            'required_fields' => ['title', 'slug', 'body']
        ],
        [
            'key' => 'seo',
            'name' => 'SEO',
            'icon' => 'fas fa-search',
            'required_fields' => ['seo_title', 'seo_description']
        ],
        [
            'key' => 'media',
            'name' => 'Medya',
            'icon' => 'fas fa-images',
            'required_fields' => []
        ]
    ],
    'form' => [
        'persistence' => [
            'storage_key' => 'page_active_tab',
            'save_active_tab' => true,
            'restore_on_load' => true
        ],
        'validation' => [
            'real_time' => true,
            'submit_button_states' => true
        ]
    ]
];
```

### Yeni Modül Ekleme
```php
// Portfolio modülü için
$tabs = GlobalTabService::getAllTabs('portfolio');
$status = GlobalTabService::getTabCompletionStatus($data, 'portfolio');
```

## 🔄 Migration Guide

### Eski PageTabService'ten Geçiş
```php
// ESKİ KULLANIM
use Modules\Page\App\Services\PageTabService;
$tabs = PageTabService::getAllTabs();
$status = PageTabService::getTabCompletionStatus($data);

// YENİ KULLANIM  
use App\Services\GlobalTabService;
$tabs = GlobalTabService::getAllTabs('page');
$status = GlobalTabService::getTabCompletionStatus($data, 'page');
```

### Provider Güncellemesi
```php
// PageServiceProvider.php - KALDIR
$this->app->singleton(\Modules\Page\App\Services\PageTabService::class);

// GlobalTabService otomatik yüklenir, ek kayıt gerekmez
```

## 🎯 Avantajlar

### ✅ Kod Tekrarını Önler
- Tab sistemi mantığı tek yerde
- Tüm modüller aynı API'yi kullanır
- Standart tab completion algoritması

### ✅ Tutarlı UX
- Tüm modüllerde aynı tab davranışı
- Standart progress gösterimi
- Unified JavaScript interaction

### ✅ Kolay Genişletme
- Yeni modül eklemek kolay
- Tab özelleştirme esnekliği
- Backward compatibility

### ✅ Maintenance-Friendly
- Tek dosyada tüm tab mantığı
- Centralized bug fixes
- Easier testing

## 🧪 Test Örnekleri

```php
// Tab sayısı kontrolü
$tabs = GlobalTabService::getAllTabs('page');
$this->assertCount(3, $tabs);

// Completion durumu
$data = ['title' => 'Test', 'seo_title' => ''];
$status = GlobalTabService::getTabCompletionStatus($data, 'page');
$this->assertTrue($status['basic']['complete']);
$this->assertFalse($status['seo']['complete']);

// JavaScript config
$jsConfig = GlobalTabService::getJavaScriptConfig('page');
$this->assertEquals('page_active_tab', $jsConfig['storage_key']);
```

---

**Sonuç:** PageTabService başarıyla GlobalTabService'a dönüştürüldü. Artık tüm modüller bu ortak tab sistemini kullanabilir! 🎉