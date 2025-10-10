# Global SEO Service - Kullanım Kılavuzu

## 🎯 Amaç
PageSeoService artık global bir sistem olarak tasarlandı. Tüm modüllerde kullanılabilir ve her modülün SEO ihtiyaçlarına göre özelleştirilebilir.

## 📍 Dosya Konumu
```
app/Services/GlobalSeoService.php
```

## 🚀 Temel Kullanım

### Basit Kullanım
```php
use App\Services\GlobalSeoService;

// Varsayılan config
$config = GlobalSeoService::getSeoConfig();

// Page modülü için
$config = GlobalSeoService::getSeoConfig('page');

// Portfolio modülü için  
$config = GlobalSeoService::getSeoConfig('portfolio');
```

### Validation Kuralları
```php
// Varsayılan validation
$rules = GlobalSeoService::getSeoValidationRules();

// Modül-specific validation
$rules = GlobalSeoService::getSeoValidationRules('page');
$rules = GlobalSeoService::getSeoValidationRules('blog');
```

### SEO Skor Hesaplama
```php
$data = [
    'seo_title' => 'Örnek Başlık',
    'seo_description' => 'Örnek açıklama metni...',
    'seo_keywords' => 'anahtar1, anahtar2, anahtar3',
    'canonical_url' => 'https://example.com/sayfa'
];

// Varsayılan scoring
$score = GlobalSeoService::calculateSeoScore($data);

// Modül-specific scoring
$score = GlobalSeoService::calculateSeoScore($data, 'page');
```

## 📋 Metodlar

### Configuration Metodları
| Metod | Açıklama | Parametreler |
|-------|----------|--------------|
| `getSeoConfig($module)` | Modül SEO konfigürasyonu | `$module` (optional) |
| `getSeoValidationRules($module)` | Validation kuralları | `$module` (optional) |
| `getSeoLimits($module)` | Karakter limitleri | `$module` (optional) |
| `isFieldRequired($field, $module)` | Field zorunlu mu? | `$field`, `$module` (optional) |

### Utility Metodları
| Metod | Açıklama | Parametreler |
|-------|----------|--------------|
| `parseKeywords($keywords)` | String'i array'e çevir | `$keywords` |
| `stringifyKeywords($keywords)` | Array'i string'e çevir | `$keywords` |
| `getSeoFields($module)` | SEO field'larını al | `$module` (optional) |
| `getSeoValidationMessages($module)` | Validation mesajları | `$module` (optional) |

### Analysis Metodları
| Metod | Açıklama | Parametreler |
|-------|----------|--------------|
| `calculateSeoScore($data, $module)` | SEO skoru hesapla | `$data`, `$module` (optional) |
| `getModuleSpecificSeoData($module, $data)` | Modül-specific işlemler | `$module`, `$data` |

## 🎨 Kullanım Örnekleri

### 1. Page Modülü Entegrasyonu
```php
// PageManageComponent.php
use App\Services\GlobalSeoService;

protected function loadConfigurations()
{
    $this->seoConfig = GlobalSeoService::getSeoConfig('page');
}

protected function rules()
{
    $seoRules = GlobalSeoService::getSeoValidationRules('page');
    return array_merge($baseRules, $seoRules);
}
```

### 2. Portfolio Modülü için
```php
// PortfolioManageComponent.php
use App\Services\GlobalSeoService;

public function validateSeo()
{
    $rules = GlobalSeoService::getSeoValidationRules('portfolio');
    $messages = GlobalSeoService::getSeoValidationMessages('portfolio');
    
    $this->validate($rules, $messages);
}

public function calculatePortfolioSeoScore()
{
    $seoData = [
        'seo_title' => $this->portfolio_title,
        'seo_description' => $this->portfolio_description,
        'seo_keywords' => $this->portfolio_keywords,
        'canonical_url' => $this->canonical_url
    ];
    
    return GlobalSeoService::calculateSeoScore($seoData, 'portfolio');
}
```

### 3. Blog Modülü için
```php
// BlogController.php
use App\Services\GlobalSeoService;

public function store(Request $request)
{
    $rules = GlobalSeoService::getSeoValidationRules('blog');
    $messages = GlobalSeoService::getSeoValidationMessages('blog');
    
    $validated = $request->validate($rules, $messages);
    
    // Blog kaydetme işlemleri...
}
```

### 4. API Integration
```php
// SEO API Controller
public function analyzeSeo(Request $request)
{
    $module = $request->input('module', 'default');
    $seoData = $request->input('seo_data');
    
    $score = GlobalSeoService::calculateSeoScore($seoData, $module);
    
    return response()->json([
        'success' => true,
        'score' => $score,
        'module' => $module
    ]);
}
```

## 🔧 Configuration Sistemi

### Modül-Specific Config
```php
// config/page.php
return [
    'seo' => [
        'fields' => [
            'seo_title' => [
                'required' => true,
                'max_length' => 60,
                'type' => 'text'
            ],
            'seo_description' => [
                'required' => true,
                'max_length' => 160,
                'type' => 'textarea'
            ],
            'seo_keywords' => [
                'required' => false,
                'max_keywords' => 10,
                'type' => 'text'
            ]
        ],
        'scoring' => [
            'title' => ['min' => 30, 'max' => 60, 'weight' => 25],
            'description' => ['min' => 120, 'max' => 160, 'weight' => 25],
            'keywords' => ['min' => 3, 'max' => 10, 'weight' => 25],
            'canonical' => ['weight' => 25, 'optional_score' => 15]
        ],
        'validation_messages' => [
            'seo_title.required' => 'Sayfa SEO başlığı zorunludur.',
            'seo_description.required' => 'Sayfa SEO açıklaması zorunludur.'
        ]
    ]
];
```

### Default Config
```php
// config/seo.php
return [
    'default' => [
        'fields' => [
            'seo_title' => [
                'required' => true,
                'max_length' => 60,
                'type' => 'text'
            ],
            'seo_description' => [
                'required' => true,
                'max_length' => 160,
                'type' => 'textarea'
            ]
        ]
    ]
];
```

## 🎯 Avantajlar

✅ **Global Kullanım**: Tüm modüllerde tutarlı SEO sistemi
✅ **Modül-Specific**: Her modül kendi kurallarını tanımlayabilir
✅ **Flexible Scoring**: Modül bazında farklı scoring kuralları
✅ **Centralized Logic**: SEO mantığı tek yerden yönetiliyor
✅ **Backward Compatible**: Mevcut kod bozulmadan çalışır
✅ **Configurable**: Config dosyaları ile özelleştirilebilir

## 📝 Migration Guide

### Eski kullanım (PageSeoService):
```php
use Modules\Page\App\Services\PageSeoService;

$config = PageSeoService::getSeoConfig();
$rules = PageSeoService::getSeoValidationRules();
$score = PageSeoService::calculateSeoScore($data);
```

### Yeni kullanım (GlobalSeoService):
```php
use App\Services\GlobalSeoService;

$config = GlobalSeoService::getSeoConfig('page');
$rules = GlobalSeoService::getSeoValidationRules('page');
$score = GlobalSeoService::calculateSeoScore($data, 'page');
```

## 🔮 Gelecek Planları

- [ ] AI-powered SEO recommendations
- [ ] Multi-language SEO analysis
- [ ] Real-time SEO scoring
- [ ] Advanced meta tag management
- [ ] Schema.org integration
- [ ] SEO performance tracking

## 🚨 Önemli Notlar

1. **Config Priority**: Modül config > Default config
2. **Backward Compatibility**: Eski PageSeoService kodları çalışmaya devam eder
3. **Module Parameter**: İsteğe bağlı, varsayılan 'default'
4. **Scoring Flexibility**: Her modül kendi scoring kurallarını tanımlayabilir

## 📞 Destek

Bu sistem Turkbil Bee projesi için geliştirilmiştir. Herhangi bir sorun için proje deposuna issue açabilirsiniz.