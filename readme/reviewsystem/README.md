# ReviewSystem Modülü - Kullanım Kılavuzu

## 📋 Genel Bakış

ReviewSystem, Laravel multi-tenant projeler için geliştirilmiş **universal** bir yorum ve puanlama sistemidir.

### ✨ Özellikler

- ⭐ **5 Yıldız Puanlama Sistemi**
- 💬 **Yorum Yazma ve Yanıtlama**
- ✅ **Admin Onay Sistemi**
- 🔄 **Polymorphic İlişkiler** (Her model'e eklenebilir)
- 📊 **Google Schema.org Uyumlu** (Rich Results)
- 🎨 **Alpine.js ile Interaktif UI**
- 🌐 **Multi-tenant Destekli**
- 🔒 **Auth Korumalı API**

---

## 📦 Kurulum

### 1. Model'e Trait Ekle

Yorum/puan almak istediğiniz model'e trait'leri ekleyin:

```php
<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\ReviewSystem\App\Traits\HasReviews;
use Modules\ReviewSystem\App\Traits\HasRatings;

class ShopProduct extends Model
{
    use HasReviews, HasRatings;

    // Model kodları...
}
```

---

## 🎨 Frontend Kullanımı

### 1. Rating Stars (Yıldız Puanlama)

Ürün/içerik sayfanızda yıldız gösterimi ve puanlama:

```blade
{{-- Örnek: Ürün detay sayfası --}}
<div class="product-rating">
    @include('reviewsystem::components.rating-stars', [
        'model' => $product,
        'readonly' => false,      // false = kullanıcı puan verebilir
        'showCount' => true,      // Ortalama puan ve sayı göster
        'size' => 'lg'            // sm, md, lg
    ])
</div>
```

**Parametreler:**
- `model` (zorunlu): Puanlanan model instance
- `readonly` (default: false): true = sadece gösterim, false = interaktif
- `showCount` (default: true): Ortalama puan ve toplam sayı gösterilsin mi?
- `size` (default: 'md'): Yıldız boyutu (sm, md, lg)

---

### 2. Review List (Yorum Listesi)

Yorumları listeler ve yorum formu gösterir:

```blade
{{-- Örnek: Ürün detay sayfası --}}
<div class="product-reviews mt-8">
    @include('reviewsystem::components.review-list', [
        'model' => $product,
        'showForm' => true,
        'perPage' => 10
    ])
</div>
```

**Parametreler:**
- `model` (zorunlu): Yorumlanan model instance
- `showForm` (default: true): Yorum formu gösterilsin mi?
- `perPage` (default: 10): Sayfa başına yorum sayısı

**Not:** Kullanıcı giriş yapmadıysa form yerine "Giriş yapın" mesajı gösterilir.

---

### 3. Schema Markup (Google SEO)

Google arama sonuçlarında **yıldızlı** gösterim için:

```blade
{{-- Örnek: Ürün detay sayfası <head> içinde --}}
@include('reviewsystem::components.schema-markup', [
    'model' => $product,
    'productName' => $product->getTranslated('title', app()->getLocale()),
    'productDescription' => $product->getTranslated('description', app()->getLocale()),
    'productImage' => thumb($product->media->first(), 800, 600),
    'productPrice' => $product->price,
    'productCurrency' => 'TRY',
    'productAvailability' => 'InStock'  // InStock, OutOfStock, PreOrder
])
```

**Parametreler:**
- `model` (zorunlu): Model instance
- `productName` (opsiyonel): Ürün adı (varsayılan: $model->title)
- `productDescription` (opsiyonel): Açıklama
- `productImage` (opsiyonel): Ürün görseli URL
- `productPrice` (opsiyonel): Fiyat
- `productCurrency` (default: 'TRY'): Para birimi
- `productAvailability` (default: 'InStock'): Stok durumu

**Availability Değerleri:**
- `InStock` - Stokta var
- `OutOfStock` - Stokta yok
- `PreOrder` - Ön sipariş
- `Discontinued` - Üretim durduruldu
- `LimitedAvailability` - Sınırlı stok

---

## 🔧 Backend Kullanımı

### Model Methodları

Trait'ler eklendikten sonra kullanılabilir methodlar:

#### HasRatings Trait

```php
// Ortalama puan (0-5)
$product->averageRating();  // 4.5

// Toplam puan sayısı
$product->ratingsCount();  // 127

// Kullanıcının verdiği puan
$product->userRating($userId);  // 5 veya null

// Kullanıcı puan vermiş mi?
$product->hasRatingByUser($userId);  // true/false

// Puan dağılımı (5⭐ → 1⭐)
$product->ratingsDistribution();
// [5 => 80, 4 => 30, 3 => 10, 2 => 5, 1 => 2]
```

#### HasReviews Trait

```php
// Tüm yorumlar (relationship)
$product->reviews();

// Onaylı yorumlar
$product->approvedReviews();

// Onay bekleyen yorumlar
$product->pendingReviews();

// Toplam yorum sayısı
$product->reviewsCount();  // 45

// Kullanıcı yorum yapmış mı?
$product->hasReviewByUser($userId);  // true/false
```

---

## 🛠️ API Endpoints

### 1. Puan Ver

**Endpoint:** `POST /api/reviews/rating`

**Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {token}
Accept: application/json
```

**Body:**
```json
{
    "model_class": "Modules\\Shop\\App\\Models\\ShopProduct",
    "model_id": 123,
    "rating_value": 5
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Puanınız kaydedildi",
    "data": {
        "average_rating": 4.7,
        "ratings_count": 128
    }
}
```

---

### 2. Yorum Ekle

**Endpoint:** `POST /api/reviews/add`

**Body:**
```json
{
    "model_class": "Modules\\Shop\\App\\Models\\ShopProduct",
    "model_id": 123,
    "review_body": "Harika bir ürün!",
    "rating_value": 5
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Yorumunuz onay bekliyor",
    "data": {
        "id": 456,
        "review_body": "Harika bir ürün!",
        "rating_value": 5,
        "is_approved": false,
        "created_at": "2025-11-10T05:15:30"
    }
}
```

---

### 3. Yorumları Getir

**Endpoint:** `GET /api/reviews/{model_class}/{model_id}`

**Örnek:** `/api/reviews/Modules-Shop-App-Models-ShopProduct/123`

**Response:**
```json
{
    "success": true,
    "data": {
        "reviews": [...],
        "aggregate_rating": {
            "average_rating": 4.7,
            "ratings_count": 128
        },
        "schema_markup": {
            "@type": "AggregateRating",
            "ratingValue": "4.7",
            "bestRating": "5",
            "worstRating": "1",
            "ratingCount": 128
        }
    }
}
```

---

## 👨‍💼 Admin Panel

### Menü Erişimi

Admin panelde:
- **Yorum ve Puan** → **Manuel Ekle** ⭐ (YENİ!)
- **Yorum ve Puan** → Tüm Yorumlar
- **Yorum ve Puan** → Onay Bekleyenler
- **Yorum ve Puan** → İstatistikler

### Admin URL'leri

- **Manuel ekle:** `/admin/reviewsystem/add` ⭐
- **Düzenle:** `/admin/reviewsystem/edit/{id}` ⭐
- Tüm yorumlar: `/admin/reviewsystem`
- Onay bekleyen: `/admin/reviewsystem/pending`
- İstatistikler: `/admin/reviewsystem/statistics`

---

### 🎯 Manuel Yorum/Puan Ekleme

Admin panelden herhangi bir ürün/içerik için yorum ve puan ekleyebilirsiniz:

**Adımlar:**
1. Admin panelde **Yorum ve Puan → Manuel Ekle** menüsüne gidin
2. Form alanlarını doldurun:
   - **Model Tipi:** Yorum yapılacak model (ürün, sayfa, blog vb.)
   - **Model ID:** O model'in veritabanı ID'si
   - **Kullanıcı:** Kayıtlı kullanıcı adına ekle (opsiyonel)
   - **Yazar Adı:** Guest yorum için (kullanıcı yoksa zorunlu)
   - **Puan:** 1-5 arası yıldız (opsiyonel)
   - **Yorum:** Yorum metni (zorunlu)
   - **Onaylı:** Hemen yayınlansın mı?
3. Kaydet

**Kullanım Senaryoları:**
- Dışarıdan gelen yorumları sisteme aktarmak
- Test amaçlı yorum oluşturmak
- Müşteriden gelen yorumu manuel girmek
- Import işlemleri için toplu yorum eklemek

**Özellikler:**
- ✅ Kullanıcı adına veya guest olarak ekleme
- ✅ Yıldız puanı (rating) dahil edebilme
- ✅ Onaylı/onaysız durumu seçme
- ✅ Düzenleme desteği
- ✅ Model varlık kontrolü
- ✅ Real-time validation

---

### Admin İşlemleri

**Yorumları Onaylama (Programatik):**
```php
use Modules\ReviewSystem\App\Services\ReviewService;

$reviewService = app(ReviewService::class);
$reviewService->approveReview($reviewId);
```

**Manuel Yorum Ekleme (Programatik):**
```php
use Modules\ReviewSystem\App\Models\Review;
use Modules\ReviewSystem\App\Models\Rating;

// Yorum ekle
Review::create([
    'reviewable_type' => 'Modules\Shop\App\Models\ShopProduct',
    'reviewable_id' => 123,
    'user_id' => 1, // veya null (guest için)
    'author_name' => 'Ahmet Yılmaz',
    'review_body' => 'Harika bir ürün!',
    'rating_value' => 5,
    'is_approved' => true,
    'approved_at' => now(),
    'approved_by' => auth()->id(),
]);

// Puan ekle (ayrı kayıt)
Rating::updateOrCreate(
    [
        'user_id' => 1,
        'ratable_type' => 'Modules\Shop\App\Models\ShopProduct',
        'ratable_id' => 123,
    ],
    ['rating_value' => 5]
);
```

---

## 🎯 Örnek Kullanım Senaryosu

### Shop Ürün Sayfası

```blade
@extends('front.layout')

@section('content')
<div class="product-detail">
    {{-- Ürün Bilgileri --}}
    <h1>{{ $product->getTranslated('title', app()->getLocale()) }}</h1>

    {{-- Rating Stars (Hemen başlıkta göster) --}}
    <div class="mb-4">
        @include('reviewsystem::components.rating-stars', [
            'model' => $product,
            'size' => 'lg'
        ])
    </div>

    {{-- Ürün açıklaması, fiyat, vs. --}}
    <div class="product-info">
        <p>{{ $product->getTranslated('description', app()->getLocale()) }}</p>
        <div class="price">{{ number_format($product->price, 2) }} TL</div>
    </div>

    {{-- Yorumlar Bölümü --}}
    <div class="product-reviews mt-12">
        <h2>Müşteri Yorumları</h2>
        @include('reviewsystem::components.review-list', [
            'model' => $product,
            'showForm' => true
        ])
    </div>
</div>
@endsection

@section('head')
    {{-- Google Schema.org Markup --}}
    @include('reviewsystem::components.schema-markup', [
        'model' => $product,
        'productName' => $product->getTranslated('title', app()->getLocale()),
        'productPrice' => $product->price,
        'productImage' => thumb($product->media->first(), 800, 600)
    ])
@endsection
```

---

## 🔍 Google Rich Results Test

Schema markup'ınızı test edin:

1. Ürün sayfanızı canlıya alın
2. Google Rich Results Test'e gidin: https://search.google.com/test/rich-results
3. URL'nizi girin
4. "AggregateRating" görmeli ve ⭐ yıldızlar görünmelidir

**Gereksinimler:**
- En az 1 rating olmalı
- Schema markup doğru formatta olmalı
- `@@ ` escape karakterleri otomatik eklenir (Blade @ direktifi ile çakışmayı önlemek için)

---

## 📊 Database Yapısı

### Tablolar

**ratings:**
- Polymorphic (her model'e eklenebilir)
- 1-5 arası integer puan
- User başına 1 puan (unique constraint)

**reviews:**
- Polymorphic (her model'e eklenebilir)
- Text yorum + opsiyonel rating
- Admin onay sistemi
- Parent-child ilişkisi (yoruma yanıt)
- Guest yorum desteği (author_name)

### Migrations

Migrations otomatik olarak hem central hem tenant database'lere uygulanır:
- `database/migrations/` - Central
- `database/migrations/tenant/` - Tenant'lar

---

## ⚠️ Önemli Notlar

1. **Alpine.js Gerekli:** Frontend component'ler Alpine.js kullanır
2. **CSRF Token:** API istekleri CSRF token gerektirir
3. **Auth Middleware:** Rating/review ekleme auth gerektirir
4. **Admin Onay:** Yorumlar varsayılan olarak onay bekler
5. **Cache:** Rating/review değişikliklerinde cache otomatik temizlenir
6. **Multi-tenant:** Her tenant'ın kendi yorumları var

---

## 🚀 Geliştirme Notları

### Yeni Model'e Ekleme

1. Model'e trait'leri ekle
2. Frontend'de component'leri kullan
3. Schema markup ekle (SEO için)

### Custom Styling

Component'ler Tailwind CSS kullanır. Override etmek için:

```css
/* Custom CSS */
.rating-stars-wrapper .fa-star {
    /* Yıldız stilleri */
}

.review-item {
    /* Yorum kartı stilleri */
}
```

---

## 📞 Destek

Sorun olursa:
1. Cache'leri temizle: `php artisan view:clear && php artisan route:clear`
2. OPcache reset: `curl https://domain.com/opcache-reset.php`
3. Migrations çalıştı mı kontrol et: `php artisan migrate:status`

---

**Oluşturulma Tarihi:** 2025-11-10
**Versiyon:** 1.0.0
**Multi-tenant Uyumlu:** ✅
**Google Schema.org Uyumlu:** ✅
