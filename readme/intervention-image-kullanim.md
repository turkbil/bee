# 🖼️ Intervention Image Kullanım Rehberi

## ✅ Kurulum Tamamlandı
- intervention/image: v3.11.4
- intervention/image-laravel: v1.5.6
- Driver: GD (Imagick alternatifi mevcut)
- Config: config/image.php

---

## 📚 Temel Kullanım

### 1. Laravel Facade ile Kullanım
```php
use Intervention\Image\Laravel\Facades\Image;

// Resim okuma
$image = Image::read('public/foo.jpg');

// Resize
$image->scale(height: 300);

// Kaydetme
$image->save('public/bar.jpg');
```

### 2. ImageManager ile Kullanım
```php
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());
$image = $manager->read('test.jpg');
```

### 3. Imagick Driver Kullanımı
```php
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

$manager = new ImageManager(new Driver());
```

---

## 🎨 Yaygın İşlemler

### Resize & Scale
```php
// Genişlik bazlı
$image->scale(width: 300);

// Yükseklik bazlı
$image->scale(height: 200);

// Tam boyut
$image->resize(width: 300, height: 200);

// Cover (kırp)
$image->cover(300, 200);

// Contain (sığdır)
$image->contain(300, 200);
```

### Thumbnail
```php
$image->cover(150, 150)->save('thumb.jpg');
```

### Watermark
```php
$watermark = Image::read('watermark.png');
$image->place($watermark, 'bottom-right', 10, 10);
```

### Renk İşlemleri
```php
// Arka plan
$image->fill('ff6b6b');

// Gri tonlama
$image->greyscale();

// Blur
$image->blur(10);

// Brightness
$image->brightness(20);

// Contrast
$image->contrast(20);
```

### Kırpma
```php
// Merkez kırpma
$image->crop(300, 200);

// Pozisyonlu kırpma
$image->crop(300, 200, 10, 10);
```

### Döndürme
```php
$image->rotate(90);
$image->rotate(-45, 'ffffff'); // Arka plan renkli
```

---

## 🔧 Laravel Entegrasyonu

### Controller'da Kullanım
```php
use Intervention\Image\Laravel\Facades\Image;

public function upload(Request $request)
{
    $image = Image::read($request->file('photo'));

    // Resize
    $image->scale(width: 800);

    // Kaydet
    $path = storage_path('app/public/uploads/photo.jpg');
    $image->save($path);

    return response()->json(['path' => $path]);
}
```

### Multiple Sizes (Thumbnail + Original)
```php
$image = Image::read($file);

// Original
$image->save(storage_path('app/public/original.jpg'));

// Medium
$image->scale(width: 800)->save(storage_path('app/public/medium.jpg'));

// Thumbnail
$image->cover(200, 200)->save(storage_path('app/public/thumb.jpg'));
```

### Response olarak Döndürme
```php
use Intervention\Image\Laravel\Facades\Image;

Route::get('/image/{filename}', function ($filename) {
    $image = Image::read(storage_path("app/public/{$filename}"));
    $image->scale(width: 400);

    return $image->response();
});
```

---

## ⚙️ Config Ayarları

### config/image.php
```php
return [
    // Driver seçimi
    'driver' => \Intervention\Image\Drivers\Gd\Driver::class,
    // veya
    // 'driver' => \Intervention\Image\Drivers\Imagick\Driver::class,

    'options' => [
        // Otomatik döndürme (EXIF'e göre)
        'autoOrientation' => true,

        // Animasyon decode
        'decodeAnimation' => true,

        // Blending rengi
        'blendingColor' => 'ffffff',

        // Meta data silme
        'strip' => false,
    ]
];
```

---

## 🎯 Gelişmiş Örnekler

### Aspect Ratio Koruyarak Resize
```php
$image = Image::read('photo.jpg');

// En fazla 800x600, oran korunur
$image->scale(width: 800)
      ->scale(height: 600)
      ->save('resized.jpg');
```

### Çoklu Format Kaydetme
```php
$image = Image::read('photo.jpg');

$image->toJpeg(quality: 90)->save('photo.jpg');
$image->toPng()->save('photo.png');
$image->toWebp(quality: 85)->save('photo.webp');
$image->toAvif(quality: 85)->save('photo.avif');
```

### EXIF Data Okuma
```php
$image = Image::read('photo.jpg');
$exif = $image->exif();

// Kamera modeli
$camera = $exif->make ?? 'Unknown';

// GPS koordinatları
$gps = $exif->gps ?? null;
```

### Responsive Images Oluşturma
```php
$image = Image::read($file);

$sizes = [
    'xs' => 320,
    'sm' => 640,
    'md' => 1024,
    'lg' => 1920,
];

foreach ($sizes as $name => $width) {
    $image->scale(width: $width)
          ->save(storage_path("app/public/{$name}.jpg"));
}
```

---

## 🚀 Performance İpuçları

1. **Format Seçimi**: WebP veya AVIF kullanın (daha küçük dosya)
2. **Quality**: JPEG için 80-85 optimal
3. **Lazy Loading**: Büyük resimleri lazy load yapın
4. **Cache**: İşlenmiş resimleri cache'leyin
5. **Queue**: Toplu işlemleri queue'ya atın

---

## 🔍 Hata Ayıklama

### GD vs Imagick Test
```php
// Hangi driver kullanılıyor?
$manager = app(\Intervention\Image\ImageManager::class);
echo get_class($manager->driver()); // GD veya Imagick
```

### Extension Kontrolü
```php
echo extension_loaded('gd') ? 'GD yüklü' : 'GD yok';
echo extension_loaded('imagick') ? 'Imagick yüklü' : 'Imagick yok';
echo extension_loaded('exif') ? 'EXIF yüklü' : 'EXIF yok';
```

---

## 📖 Resmi Dokümantasyon
https://image.intervention.io/v3

## 🎉 Sistem Durumu
✅ Intervention Image v3.11.4 tam çalışır durumda!
✅ GD ve Imagick driver'ları mevcut
✅ EXIF desteği aktif
