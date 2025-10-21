# 📸 Thumbmaker - Universal Image Resizing System

## 🎯 Nedir?

Thumbmaker, görselleri anında boyutlandıran, format dönüştüren ve optimize eden universal bir sistemdir. Intervention Image v3 kütüphanesi ile çalışır ve on-the-fly görsel işleme yapar.

**Özellikler:**
- ⚡ Anında boyutlandırma (w, h)
- 🎨 3 farklı scale modu (fit, fill, stretch)
- 🖼️ Format dönüştürme (WebP, JPG, PNG, GIF)
- 📦 30 günlük cache sistemi
- 🔒 Security: Allowed hosts whitelist
- 🌍 Multi-tenant desteği
- 🎯 Hizalama kontrolleri (9 pozisyon)

---

## 🚀 Hızlı Başlangıç

### Blade Template'de Kullanım

```blade
{{-- Basit kullanım: 400x300 WebP --}}
<img src="{{ thumb($media, 400, 300) }}" alt="Thumbnail">

{{-- Detaylı kullanım --}}
<img src="{{ thumb($media, 800, 600, [
    'quality' => 90,
    'scale' => 1,
    'alignment' => 'c',
    'format' => 'webp'
]) }}" alt="Optimized">

{{-- URL ile kullanım --}}
<img src="{{ thumb('https://example.com/image.jpg', 1200, null, ['format' => 'webp']) }}" alt="WebP">
```

### Direkt URL Kullanımı

```
/thumbmaker?src=https://ixtif.com/image.jpg&w=400&h=300&q=85&f=webp
```

---

## 📋 Parametreler

| Parametre | Açıklama | Değerler | Varsayılan |
|-----------|----------|----------|------------|
| `src` | Kaynak görsel URL'i (zorunlu) | URL string | - |
| `w` | Width - Genişlik (piksel) | 1-9999 | null |
| `h` | Height - Yükseklik (piksel) | 1-9999 | null |
| `q` | Quality - Kalite | 1-100 | 85 |
| `a` | Alignment - Hizalama (scale=1 için) | c, t, b, l, r, tl, tr, bl, br | c |
| `s` | Scale - Ölçeklendirme | 0 (fit), 1 (fill), 2 (stretch) | 0 |
| `f` | Format - Çıktı formatı | webp, jpg, png, gif | webp |
| `c` | Cache - Cache kullan | 0, 1 | 1 |

---

## 🎨 Scale Modları

### Mode 0: Fit (Sığdır) - Varsayılan
Görseli belirtilen boyuta **orantılı** olarak sığdırır. Boşluklar kalır.

```blade
{{ thumb($media, 400, 300, ['scale' => 0]) }}
```

**Kullanım:** Blog görselleri, galeri, genel kullanım

### Mode 1: Fill (Doldur)
Görseli **kırpar** ve belirtilen boyutu tam doldurur. Hizalama kontrolü ile hangi kısmın görüneceğini seçebilirsin.

```blade
{{ thumb($media, 400, 400, ['scale' => 1, 'alignment' => 'c']) }}
```

**Kullanım:** Kare thumbnail'ler, profil fotoğrafları, grid layout

### Mode 2: Stretch (Esnet)
Görseli **orantı bozmadan** tam boyuta esnetir. **Dikkat:** Görsel bozulabilir!

```blade
{{ thumb($media, 800, 200, ['scale' => 2]) }}
```

**Kullanım:** Nadiren kullanılır, özel durumlar için

---

## 🎯 Hizalama (Alignment)

**Sadece `scale=1` (Fill) modunda kullanılır!**

```
┌──────────────────┐
│ tl    t      tr  │  tl: Top Left
│                  │  t:  Top
│  l    c      r   │  c:  Center (varsayılan)
│                  │  l:  Left
│ bl    b      br  │  r:  Right
└──────────────────┘  bl: Bottom Left
                      b:  Bottom
                      br: Bottom Right
```

**Örnek:**
```blade
{{-- Görselin üst kısmını göster --}}
{{ thumb($media, 800, 600, ['scale' => 1, 'alignment' => 't']) }}

{{-- Görselin sağ alt köşesini göster --}}
{{ thumb($media, 400, 400, ['scale' => 1, 'alignment' => 'br']) }}
```

---

## 💡 Kullanım Örnekleri

### 1. Blog Featured Image (Responsive)

```blade
<picture>
    <source srcset="{{ thumb($featuredImage, 1200, 630, ['format' => 'webp']) }}" type="image/webp" media="(min-width: 768px)">
    <source srcset="{{ thumb($featuredImage, 768, 403, ['format' => 'webp']) }}" type="image/webp" media="(min-width: 480px)">
    <source srcset="{{ thumb($featuredImage, 480, 252, ['format' => 'webp']) }}" type="image/webp">
    <img src="{{ thumb($featuredImage, 1200, 630) }}"
         alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
         loading="lazy">
</picture>
```

### 2. Galeri Grid (Kare Thumbnail'ler)

```blade
<div class="grid grid-cols-3 gap-4">
    @foreach($galleryImages as $image)
        <div class="aspect-square overflow-hidden rounded">
            <img src="{{ thumb($image, 400, 400, ['scale' => 1, 'alignment' => 'c', 'quality' => 90]) }}"
                 alt="{{ $image->name }}"
                 class="w-full h-full object-cover"
                 loading="lazy">
        </div>
    @endforeach
</div>
```

### 3. Hero Banner (Full Width)

```blade
<section class="hero relative">
    <img src="{{ thumb($heroImage, 1920, 1080, ['scale' => 1, 'alignment' => 'c', 'quality' => 85]) }}"
         alt="Hero"
         class="w-full h-full object-cover">
</section>
```

### 4. Avatar/Profile Picture

```blade
<img src="{{ thumb($userAvatar, 150, 150, ['scale' => 1, 'alignment' => 'c', 'format' => 'webp']) }}"
     alt="{{ $user->name }}"
     class="rounded-full">
```

### 5. Lightbox Full Size

```blade
<a href="{{ thumb($media, 1920, 1920, ['quality' => 90]) }}" data-lightbox="gallery">
    <img src="{{ thumb($media, 400, 300) }}" alt="Thumbnail">
</a>
```

---

## ✨ Best Practices

### ✅ Yapılması Gerekenler

1. **WebP formatı kullan** (daha küçük dosya boyutu)
   ```blade
   {{ thumb($media, 800, 600, ['format' => 'webp']) }}
   ```

2. **loading="lazy" ekle** (sayfa hızı)
   ```blade
   <img src="..." loading="lazy">
   ```

3. **Thumbnail için scale=1 kullan** (kare görsel için)
   ```blade
   {{ thumb($media, 400, 400, ['scale' => 1]) }}
   ```

4. **Kalite 80-90 aralığında olsun** (optimize boyut)
   ```blade
   {{ thumb($media, 1200, 800, ['quality' => 85]) }}
   ```

5. **Cache her zaman aktif** (varsayılan zaten 1)
   ```blade
   {{ thumb($media, 800, 600) }}  // Cache otomatik aktif
   ```

6. **Responsive images kullan** (picture tag)
   ```blade
   <picture>
       <source srcset="{{ thumb($media, 1200, 800) }}" media="(min-width: 768px)">
       <img src="{{ thumb($media, 768, 512) }}" alt="...">
   </picture>
   ```

### ❌ Yapılmaması Gerekenler

1. **Gereksiz yüksek kalite kullanma**
   ```blade
   ❌ {{ thumb($media, 800, 600, ['quality' => 100]) }}  // Dosya çok büyük!
   ✅ {{ thumb($media, 800, 600, ['quality' => 85]) }}
   ```

2. **Orijinal boyuttan büyütme**
   ```blade
   ❌ {{ thumb($smallImage, 9999, 9999) }}  // Pikselleşir!
   ✅ {{ thumb($smallImage, 800, 600) }}
   ```

3. **Cache'i devre dışı bırakma**
   ```blade
   ❌ {{ thumb($media, 800, 600, ['cache' => 0]) }}  // Her istekte işlenir!
   ✅ {{ thumb($media, 800, 600) }}  // Cache kullan
   ```

4. **Scale=2 (stretch) kullanma**
   ```blade
   ❌ {{ thumb($media, 800, 200, ['scale' => 2]) }}  // Görsel bozulur!
   ✅ {{ thumb($media, 800, 200, ['scale' => 0]) }}  // Orantılı
   ```

5. **Dev boyutlar verme**
   ```blade
   ❌ {{ thumb($media, 9999, 9999) }}
   ✅ {{ thumb($media, 1920, 1080) }}  // Maksimum ekran boyutu
   ```

---

## 🔧 Helper Fonksiyonu

### Signature

```php
thumb($src, ?int $width = null, ?int $height = null, array $options = []): string
```

### Parametreler

- `$src`: Media objesi veya URL string
- `$width`: Genişlik (null = orantılı)
- `$height`: Yükseklik (null = orantılı)
- `$options`: Opsiyonel ayarlar array

### Options Array

```php
[
    'quality' => 85,        // 1-100
    'scale' => 0,           // 0=fit, 1=fill, 2=stretch
    'alignment' => 'c',     // c, t, b, l, r, tl, tr, bl, br
    'format' => 'webp',     // webp, jpg, png, gif
    'cache' => 1            // 0=hayır, 1=evet
]
```

---

## 🛡️ Security

### Allowed Hosts

Thumbmaker sadece whitelist'teki domainlerden görsel işler:

```php
private function isAllowedHost(string $url): bool
{
    $allowedHosts = [
        'ixtif.com',
        'www.ixtif.com',
        'ixtif.com.tr',
        'www.ixtif.com.tr',
        'tuufi.com',
        'www.tuufi.com',
        // ... diğer tenant domainleri
    ];
}
```

**Harici URL kullanıyorsan:** Domain'i `allowedHosts` array'ine ekle!

**Dosya:** `Modules/MediaManagement/app/Http/Controllers/ThumbmakerController.php:28`

---

## 📦 Cache Sistemi

### Cache Süresi
**30 gün** - Aynı parametrelerle yapılan istekler cache'ten dönülür

### Cache Key Format
```
thumbmaker:[sha256_hash]
```

Hash şunları içerir:
- src (kaynak URL)
- width
- height
- quality
- scale
- alignment
- format

### Cache Temizleme

```bash
# Tüm thumbmaker cache'ini temizle
php artisan cache:clear

# Belirli bir görselin cache'ini temizle
# (Şu an otomatik yöntem yok, cache:clear kullan)
```

---

## 🐛 Troubleshooting

### Problem: Görsel görünmüyor

**1. Kaynak URL kontrol et:**
```blade
{{ $media->getUrl() }}  // URL'yi yazdır
```

**2. Allowed hosts listesinde mi?**
`ThumbmakerController.php:28` - Domain'i ekle

**3. Tenant context var mı?**
Route'a `InitializeTenancy` middleware ekli mi kontrol et

### Problem: Kalite düşük

**Çözüm:** Quality parametresini artır
```blade
{{ thumb($media, 800, 600, ['quality' => 90]) }}
```

### Problem: Görsel bozuk/esnetilmiş

**Çözüm:** Scale modunu fit (0) yap
```blade
{{ thumb($media, 800, 600, ['scale' => 0]) }}
```

### Problem: Cache güncellenmiyor

**Çözüm:** Cache'i temizle
```bash
php artisan cache:clear
```

### Problem: Slow loading

**Çözüm 1:** loading="lazy" ekle
```blade
<img src="..." loading="lazy">
```

**Çözüm 2:** Boyut küçült
```blade
{{ thumb($media, 600, 400) }}  // 800x600 yerine
```

**Çözüm 3:** WebP kullan (daha küçük dosya)
```blade
{{ thumb($media, 800, 600, ['format' => 'webp']) }}
```

---

## 📚 İlgili Dosyalar

### Core Dosyalar
- **Controller**: `Modules/MediaManagement/app/Http/Controllers/ThumbmakerController.php`
- **Helper**: `app/helpers.php` (thumb() fonksiyonu)
- **Route**: `Modules/MediaManagement/routes/web.php`
- **Guide**: `/admin/mediamanagement/thumbmaker-guide` (Admin panel)

### Blade Kullanımı
- **Blog**: `Modules/Blog/resources/views/themes/ixtif/partials/show-content.blade.php`
- **Portfolio**: `Modules/Portfolio/resources/views/themes/ixtif/*`
- **Media Library**: `Modules/MediaManagement/resources/views/admin/livewire/media-library-manager.blade.php`

---

## 🎓 Admin Panel Guide

Detaylı kullanım kılavuzu admin panelde mevcut:

**URL:** `/admin/mediamanagement/thumbmaker-guide`

**İçerik:**
- Parametreler tablosu
- Hizalama şeması (görsel)
- Kullanım örnekleri (accordion)
- Best practices (yeşil/kırmızı kartlar)
- Dark mode uyumlu tasarım

---

## ⚙️ Teknik Detaylar

### Intervention Image v3

```php
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());
$image = $manager->read($imageContent);
```

### Scale Implementasyonu

```php
// Fit (0)
$image->scale(width: $width, height: $height);

// Fill (1)
$image->cover($width, $height, $alignment);

// Stretch (2)
$image->resize($width, $height);
```

### Format Encoding

```php
$image->toWebp(quality: $quality);
$image->toJpeg(quality: $quality);
$image->toPng();
$image->toGif();
```

---

## 📝 Changelog

### v1.0.0 (2025-01-21)
- ✅ Initial release
- ✅ Intervention Image v3 integration
- ✅ 3 scale modes (fit, fill, stretch)
- ✅ 4 format support (WebP, JPG, PNG, GIF)
- ✅ 9 alignment positions
- ✅ 30-day cache system
- ✅ Blade helper function
- ✅ Multi-tenant support
- ✅ Admin guide page
- ✅ Dark mode compatible UI

---

## 🤝 Katkı

Bu sistem **MediaManagement** modülünün bir parçasıdır. Geliştirmeler için:

1. `ThumbmakerController.php` - Core logic
2. `app/helpers.php` - Helper function
3. `thumbmaker-guide.blade.php` - Documentation

**NOT:** Değişiklik yaparken cache sistemini bozmamaya dikkat et!

---

## 📞 Destek

**Admin Guide:** `/admin/mediamanagement/thumbmaker-guide`
**Dokümantasyon:** `readme/thumbmaker/README.md`
**CLAUDE.md:** Ana proje talimatları

---

**Thumbmaker ile görsellerinizi optimize edin! 🚀**
