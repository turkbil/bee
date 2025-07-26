# SlugHelper - Global Slug Yönetim Sistemi

Bu sistem tüm modüllerde slug unique kontrolü ve otomatik düzeltme işlemlerini sağlar.

## Özellikler

- ✅ **Çoklu Dil Desteği**: Her dil için ayrı unique kontrol
- ✅ **Otomatik Düzeltme**: Duplicate slug'lar otomatik sayı ile düzeltilir  
- ✅ **Title'dan Slug**: Boş slug'lar title'dan otomatik oluşturulur
- ✅ **Türkçe Karakter Desteği**: ğ→g, ş→s dönüşümleri
- ✅ **Model Agnostic**: Her model için çalışır
- ✅ **Validation Entegrasyonu**: Hazır validation kuralları ve mesajları

## Kullanım Örnekleri

### 1. Livewire Component'te Kullanım

```php
<?php

namespace Modules\Example\App\Http\Livewire\Admin;

use Livewire\Component;
use App\Helpers\SlugHelper;
use App\Traits\HasSlugManagement;
use Modules\Example\App\Models\ExampleModel;

class ExampleManageComponent extends Component
{
    use HasSlugManagement;
    
    public $availableLanguages = ['tr', 'en', 'ar'];
    public $multiLangInputs = [];
    public $modelId = null;
    
    protected function rules()
    {
        $rules = [
            // Diğer kurallar...
        ];
        
        // Slug validation kuralları ekle
        $slugRules = $this->getSlugValidationRules($this->availableLanguages);
        
        return array_merge($rules, $slugRules);
    }
    
    protected function getMessages()
    {
        $messages = [
            // Diğer mesajlar...
        ];
        
        // Slug validation mesajları ekle
        $slugMessages = $this->getSlugValidationMessages($this->availableLanguages);
        
        return array_merge($messages, $slugMessages);
    }
    
    public function save()
    {
        $this->validate($this->rules(), $this->getMessages());
        
        // Slug'ları işle
        $processedSlugs = $this->processMultiLanguageSlugs(
            ExampleModel::class,
            $this->multiLangInputs,
            $this->availableLanguages,
            $this->modelId
        );
        
        // Model'i kaydet
        $data = [
            'title' => $this->buildMultiLangData('title'),
            'slug' => $processedSlugs,
            'body' => $this->buildMultiLangData('body'),
        ];
        
        if ($this->modelId) {
            ExampleModel::findOrFail($this->modelId)->update($data);
        } else {
            ExampleModel::create($data);
        }
    }
    
    private function buildMultiLangData($field)
    {
        $data = [];
        foreach ($this->availableLanguages as $lang) {
            if (!empty($this->multiLangInputs[$lang][$field])) {
                $data[$lang] = $this->multiLangInputs[$lang][$field];
            }
        }
        return $data;
    }
}
```

### 2. Direkt SlugHelper Kullanımı

```php
use App\Helpers\SlugHelper;
use Modules\Portfolio\App\Models\Portfolio;

// Tek slug için unique kontrol
$uniqueSlug = SlugHelper::generateUniqueSlug(
    Portfolio::class,           // Model sınıfı
    'portfolyo-baslik',         // Base slug
    'tr',                       // Dil kodu
    'slug',                     // Slug column (varsayılan: slug)
    'portfolio_id',             // Primary key (varsayılan: model'in primaryKey'i)
    $excludeId                  // Hariç tutulacak ID (güncelleme için)
);

// Title'dan slug oluştur
$slugFromTitle = SlugHelper::generateFromTitle(
    Portfolio::class,
    'Örnek Portfolyo Başlığı',
    'tr',
    'slug',
    'portfolio_id',
    $excludeId
);

// Çoklu dil slug işleme
$processedSlugs = SlugHelper::processMultiLanguageSlugs(
    Portfolio::class,
    [
        'tr' => 'portfolyo',       // Bu slug var, düzeltilecek
        'en' => '',                // Boş, title'dan oluşturulacak
        'ar' => 'unique-slug'      // Unique, aynen kalacak
    ],
    [
        'tr' => 'Portfolyo Başlığı',
        'en' => 'Portfolio Title',
        'ar' => 'عنوان المحفظة'
    ]
);
```

### 3. Manuel Slug İşleme

```php
// Slug'ın unique olup olmadığını kontrol et
$isUnique = SlugHelper::isUnique(
    Portfolio::class,
    'test-slug',
    'tr',
    'slug',
    'portfolio_id',
    $excludeId
);

// Title'dan slug oluştur (unique yapmadan)
$basicSlug = SlugHelper::createSlugFromTitle('Örnek Başlık');
// Sonuç: "ornek-baslik"

// Slug'ı normalize et
$normalizedSlug = SlugHelper::normalizeSlug('ÖRNEK-SLUG!@#');
// Sonuç: "ornek-slug"
```

### 4. Validation Kuralları

```php
// Validation kuralları al
$rules = SlugHelper::getValidationRules(
    ['tr', 'en', 'ar'],         // Diller
    'multiLangInputs',          // Field prefix
    false                       // Required değil
);

// Validation mesajları al
$messages = SlugHelper::getValidationMessages(
    ['tr', 'en', 'ar'],
    'multiLangInputs'
);
```

## Farklı Modüllerde Kullanım

### Portfolio Modülü Örneği

```php
// Modules/Portfolio/app/Http/Livewire/Admin/PortfolioManageComponent.php

use App\Traits\HasSlugManagement;
use Modules\Portfolio\App\Models\Portfolio;

class PortfolioManageComponent extends Component
{
    use HasSlugManagement;
    
    // Save metodunda:
    $processedSlugs = $this->processMultiLanguageSlugs(
        Portfolio::class,
        $this->multiLangInputs,
        $this->availableLanguages,
        $this->portfolioId,
        'slug' // Portfolio tablosundaki slug column
    );
}
```

### Announcement Modülü Örneği

```php
// Modules/Announcement/app/Http/Livewire/Admin/AnnouncementManageComponent.php

use App\Traits\HasSlugManagement;
use Modules\Announcement\App\Models\Announcement;

class AnnouncementManageComponent extends Component
{
    use HasSlugManagement;
    
    // Save metodunda:
    $processedSlugs = $this->processMultiLanguageSlugs(
        Announcement::class,
        $this->multiLangInputs,
        $this->availableLanguages,
        $this->announcementId,
        'slug' // Announcement tablosundaki slug column
    );
}
```

## Özelleştirme

### Farklı Slug Column Adı

```php
// Eğer slug column adı farklıysa (örn: 'seo_slug')
$uniqueSlug = SlugHelper::generateUniqueSlug(
    Model::class,
    'slug',
    'tr',
    'seo_slug',    // Farklı column adı
    'id',
    $excludeId
);
```

### Farklı Primary Key

```php
// Eğer primary key farklıysa
$uniqueSlug = SlugHelper::generateUniqueSlug(
    Model::class,
    'slug',
    'tr',
    'slug',
    'custom_id',   // Farklı primary key
    $excludeId
);
```

## Avantajlar

1. **Tek Kaynak**: Tüm slug işlemleri tek yerden yönetilir
2. **Tutarlılık**: Her modülde aynı davranış
3. **Bakım Kolaylığı**: Değişiklikler tek yerden yapılır
4. **Test Edilebilirlik**: Merkezi sistem test edilir
5. **Performans**: Optimized sorgu yapıları
6. **Esneklik**: Her modül kendi ihtiyacına göre özelleştirebilir

## Migration Rehberi

Mevcut component'lerde slug sistemini değiştirmek için:

1. `use App\Helpers\SlugHelper;` ekle
2. `use App\Traits\HasSlugManagement;` ekle (opsiyonel)
3. Eski slug metodlarını kaldır
4. `processMultiLanguageSlugs()` kullan
5. Validation'ı güncelle

Bu sistem sayesinde tüm modüllerde slug yönetimi standartlaşır ve hata riski azalır.