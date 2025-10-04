# 📸 MediaManagement Module

Universal Media Management System - Spatie Media Library v11 tabanlı modern medya yönetimi

---

## 🎯 Özellikler

- ✅ **Universal Component**: Tek component, tüm modüllerde kullanılabilir
- ✅ **Multi-language Captions**: Her görsele çoklu dil desteği ile başlık/açıklama/alt_text
- ✅ **Automatic Thumbnails**: WebP formatında otomatik thumbnail oluşturma
- ✅ **Drag & Drop**: Sıralama ve dosya yükleme
- ✅ **Session-based Preview**: Model save edilmeden önce geçici önizleme
- ✅ **Tenant Support**: Multi-tenant yapı desteği

---

## 🚀 Hızlı Başlangıç

### Model'e Media Eklemek

```php
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Spatie\MediaLibrary\HasMedia;

class YourModel extends Model implements HasMedia
{
    use HasMediaManagement;

    // Opsiyonel: Özel media config
    protected array $mediaConfig = [
        'featured_image' => [
            'type' => 'image',
            'single_file' => true,
            'max_items' => 1,
            'conversions' => ['thumb', 'medium', 'large'],
        ],
        'gallery' => [
            'type' => 'image',
            'single_file' => false,
            'max_items' => 50,
            'conversions' => ['thumb', 'medium'],
        ],
    ];
}
```

### Blade Template'te Kullanım

```blade
@livewire('media-management::universal-media-component', [
    'modelClass' => 'Modules\\Announcement\\App\\Models\\Announcement',
    'modelId' => $announcement->id ?? null,
    'hasFeaturedImage' => true,
    'hasGallery' => true,
])
```

---

## 🎨 Helper Fonksiyonlar

### 1. Thumbnail Boyutları

```php
// Aspect ratio (CSS için)
media_aspect_ratio('thumb'); // '3/2'

// Genişlik
media_thumb_size('width'); // 300

// Yükseklik
media_thumb_size('height'); // 200

// Her ikisi
media_thumb_size('both'); // ['width' => 300, 'height' => 200]

// Oran (sayısal)
media_thumb_size('aspect'); // 1.5
```

### 2. Kalite ve Format

```php
// Kalite
media_quality('thumb'); // 85
media_quality('medium'); // 90

// Format
media_format('thumb'); // 'webp'
media_format('medium'); // 'webp'
```

### 3. Media URL'leri

```php
// Featured image
featured($model); // Original
featured($model, 'thumb'); // Thumbnail
featured($model, 'medium'); // Medium size

// Gallery
gallery($model); // Tüm gallery array
gallery($model, 'thumb'); // Thumbnails ile

// Tek media
thumb($media); // Thumbnail URL
media_url($media, 'medium'); // Medium URL
```

### 4. Responsive Images

```php
// Srcset (CDN için)
responsive_image($model, 'featured_image', 'responsive');
```

---

## ⚙️ Yapılandırma

### Thumbnail Boyutlarını Değiştirmek

`Modules/MediaManagement/config/config.php` dosyasını düzenle:

```php
'conversions' => [
    'thumb' => [
        'width' => 400,      // Genişlik (varsayılan: 300)
        'height' => 300,     // Yükseklik (varsayılan: 200)
        'format' => 'webp',  // Format (webp, jpg, png)
        'quality' => 90,     // Kalite 0-100 (varsayılan: 85)
        'queued' => false,   // Queue'ya gönder (varsayılan: false)
    ],
]
```

**ÖNEMLİ:** Config değişikliği sonrası:
```bash
php artisan config:clear
php artisan app:clear-all
```

### Aspect Ratio Otomatik Hesaplanır

Config'te `width: 400, height: 300` ayarlarsanız:
- CSS aspect-ratio: `4/3` (GCD ile basitleştirilir)
- Tüm thumbnail'ler otomatik bu orana göre gösterilir

---

## 📦 Conversion Tipleri

| Conversion | Boyut | Kalite | Format | Kullanım |
|-----------|-------|--------|--------|----------|
| `thumb` | 300x200 | 85% | WebP | Liste görünümleri, küçük önizlemeler |
| `medium` | 800x600 | 90% | WebP | Orta boy gösterimler |
| `large` | 1920x1080 | 90% | WebP | Büyük gösterimler |
| `responsive` | Çoklu | 90% | WebP | Responsive srcset (CDN) |

---

## 🔧 Özelleştirmeler

### Yeni Conversion Eklemek

```php
// config/config.php
'conversions' => [
    'thumbnail_small' => [
        'width' => 150,
        'height' => 150,
        'format' => 'webp',
        'quality' => 80,
        'queued' => false,
    ],
]
```

### Model'de Kullanmak

```php
protected array $mediaConfig = [
    'featured_image' => [
        'conversions' => ['thumb', 'thumbnail_small', 'medium'],
    ],
];
```

### Blade'de Kullanmak

```blade
<img src="{{ featured($model, 'thumbnail_small') }}"
     style="aspect-ratio: {{ media_aspect_ratio('thumbnail_small') }}">
```

---

## 🎯 En İyi Pratikler

### 1. Her Zaman Thumbnail Kullan
❌ **Kötü:**
```blade
<img src="{{ $media->getUrl() }}"> {{-- Tam boyut yükleniyor! --}}
```

✅ **İyi:**
```blade
<img src="{{ thumb($media) }}"> {{-- 4KB WebP yükleniyor --}}
```

### 2. Aspect Ratio Helper Kullan
❌ **Kötü:**
```blade
<img style="aspect-ratio: 3/2"> {{-- Hardcoded --}}
```

✅ **İyi:**
```blade
<img style="aspect-ratio: {{ media_aspect_ratio('thumb') }}"> {{-- Config'ten --}}
```

### 3. Config'ten Oku
❌ **Kötü:**
```php
$conversion->width(300)->height(200); // Hardcoded
```

✅ **İyi:**
```php
$config = media_conversion_config('thumb');
$conversion->width($config['width'])->height($config['height']);
```

---

## 📊 Performans

### Önce vs Sonra

| Durum | Dosya Boyutu | Format | Yükleme Hızı |
|-------|--------------|--------|--------------|
| **Önce** | 111KB | PNG | Yavaş |
| **Sonra** | 4KB | WebP | 27x Hızlı |

### Thumbnail Config Değişikliği

Eğer `width: 400, height: 300` yaparsanız:
- Aspect ratio otomatik `4/3` olur
- Tüm thumbnails bu boyutta oluşturulur
- Blade'de değişiklik yapmanıza gerek yok (helper otomatik güncellenir)

---

## 🐛 Sorun Giderme

### Thumbnails Oluşturulmuyor
```bash
php artisan media-library:regenerate
```

### Eski Thumbnails Kaldı
```bash
php artisan media-library:clear
php artisan media-library:regenerate
```

### Cache Temizleme
```bash
php artisan config:clear
php artisan cache:clear
php artisan app:clear-all
```

---

## 📚 Kaynaklar

- [Spatie Media Library Docs](https://spatie.be/docs/laravel-medialibrary/v11)
- MediaManagement Config: `Modules/MediaManagement/config/config.php`
- Helper Functions: `app/Helpers/MediaHelper.php`
