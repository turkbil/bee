# Google Shopping Feed - Teknik Döküman

## 📂 DOSYA YAPISI

```
Modules/Shop/app/Http/Controllers/
├── GoogleShoppingFeedController.php (MEVCUT - Güncellenecek)

Modules/Shop/app/Services/
├── GoogleProductCategoryMapper.php (YENİ - Oluşturulacak)

Modules/Shop/storage/
├── google-categories.json (YENİ - Kategori taxonomy)

routes/web.php
├── Route::get('productfeed', ...) (MEVCUT - Değişmeyecek)
```

---

## 🔧 YAPILACAK DEĞİŞİKLİKLER

### 1. GoogleShoppingFeedController.php

**Eklenecek Alanlar:**

```php
// Görseller (ZORUNLU)
$imageUrl = $product->getFirstMediaUrl('featured_image');
if ($imageUrl) {
    $xml .= '<g:image_link>' . htmlspecialchars($imageUrl) . '</g:image_link>';
}

// Ek Görseller (Önerilen)
$galleryImages = $product->getMedia('gallery');
foreach ($galleryImages->take(10) as $media) {
    $xml .= '<g:additional_image_link>' . htmlspecialchars($media->getUrl()) . '</g:additional_image_link>';
}

// GTIN - Barkod (Önerilen)
if ($product->barcode) {
    $xml .= '<g:gtin>' . htmlspecialchars($product->barcode) . '</g:gtin>';
}

// MPN - Model Numarası (Önerilen)
if ($product->model_number) {
    $xml .= '<g:mpn>' . htmlspecialchars($product->model_number) . '</g:mpn>';
}

// Identifier Exists (GTIN/MPN yoksa ZORUNLU)
if (!$product->barcode && !$product->model_number) {
    $xml .= '<g:identifier_exists>no</g:identifier_exists>';
}

// Google Product Category (Önerilen)
$googleCategory = GoogleProductCategoryMapper::map($product->category_id);
if ($googleCategory) {
    $xml .= '<g:google_product_category>' . $googleCategory . '</g:google_product_category>';
}

// Product Type - Kendi Kategoriniz (Önerilen)
if ($product->category) {
    $categoryTitle = json_decode($product->category->title, true);
    $categoryName = $categoryTitle['tr'] ?? 'Genel';
    $xml .= '<g:product_type>' . htmlspecialchars($categoryName) . '</g:product_type>';
}

// Dinamik Stok Kontrolü
if ($product->stock_tracking) {
    $availability = $product->current_stock > 0 ? 'in stock' : 'out of stock';
    if (!$product->stock_tracking && $product->allow_backorder) {
        $availability = 'backorder';
    }
} else {
    $availability = 'in stock';
}
$xml .= '<g:availability>' . $availability . '</g:availability>';

// İndirim Fiyatı (varsa)
if ($product->compare_at_price && $product->compare_at_price > $product->base_price) {
    $regularPrice = number_format($product->compare_at_price, 2, '.', '');
    $salePrice = number_format($product->base_price, 2, '.', '');
    $xml .= '<g:price>' . $regularPrice . ' ' . $currency . '</g:price>';
    $xml .= '<g:sale_price>' . $salePrice . ' ' . $currency . '</g:sale_price>';
} else {
    $price = number_format($product->base_price, 2, '.', '');
    $xml .= '<g:price>' . $price . ' ' . $currency . '</g:price>';
}
```

**Değiştirilecek Query:**

```php
// ŞU AN:
$products = DB::table('shop_products')
    ->select('product_id', 'title', 'slug', 'short_description', 'base_price', 'currency', 'condition')
    ->where('is_active', 1)
    ->whereNotNull('base_price')
    ->where('base_price', '>', 0)
    ->limit(100)
    ->get();

// YENİ:
$products = DB::table('shop_products')
    ->leftJoin('shop_categories', 'shop_products.category_id', '=', 'shop_categories.category_id')
    ->select(
        'shop_products.*',
        'shop_categories.title as category_title'
    )
    ->where('shop_products.is_active', 1)
    ->whereNotNull('shop_products.base_price')
    ->where('shop_products.base_price', '>', 0)
    ->get();

// Eloquent kullanarak görselleri almak için
use Modules\Shop\App\Models\ShopProduct;

$products = ShopProduct::with(['media', 'category'])
    ->where('is_active', 1)
    ->whereNotNull('base_price')
    ->where('base_price', '>', 0)
    ->get();
```

---

### 2. GoogleProductCategoryMapper.php (YENİ SERVİS)

**Amaç:** Shop kategorilerini Google Shopping kategorileriyle eşleştirmek

```php
<?php

namespace Modules\Shop\App\Services;

class GoogleProductCategoryMapper
{
    // Manuel mapping (örnek)
    private static $categoryMap = [
        // iXtif Kategori ID => Google Category ID
        1 => 'Vehicles & Parts > Vehicle Parts & Accessories > Motor Vehicle Parts > Motor Vehicle Towing',
        2 => 'Business & Industrial > Material Handling > Forklifts',
        3 => 'Business & Industrial > Material Handling > Pallet Jacks & Stackers',
        // ... daha fazla
    ];

    public static function map(int $categoryId): ?string
    {
        return self::$categoryMap[$categoryId] ?? null;
    }
}
```

**Alternatif:** JSON dosyasından okuma

```php
public static function map(int $categoryId): ?string
{
    $mappings = json_decode(
        file_get_contents(storage_path('app/google-categories.json')),
        true
    );

    return $mappings[$categoryId] ?? null;
}
```

---

### 3. google-categories.json (Kategori Taxonomy)

```json
{
  "1": "Business & Industrial > Material Handling > Pallet Jacks & Stackers > 499954",
  "2": "Business & Industrial > Material Handling > Forklifts > 499950",
  "3": "Vehicles & Parts > Vehicle Parts & Accessories > Motor Vehicle Parts",
  "categories": {
    "transpalet": {
      "id": "499954",
      "path": "Business & Industrial > Material Handling > Pallet Jacks & Stackers"
    },
    "forklift": {
      "id": "499950",
      "path": "Business & Industrial > Material Handling > Forklifts"
    }
  }
}
```

**Google Taxonomy Referansı:**
https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt

---

## 🧪 TEST ADIMI

### Test Feed URL:
```bash
curl -s https://ixtif.com/productfeed | head -100
```

### Google Feed Validator:
https://support.google.com/merchants/answer/7052112

### Örnek Feed Çıktısı (İyileştirilmiş):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title>iXtif</title>
<link>https://ixtif.com</link>
<description>Endüstriyel Ekipman</description>

<item>
  <g:id>1</g:id>
  <g:title>Transpalet 2.0 Ton Standart Çatal</g:title>
  <g:description>2.0 ton kapasiteli manuel transpalet...</g:description>
  <g:link>https://ixtif.com/shop/transpalet-2-ton-standart-catal</g:link>
  <g:image_link>https://ixtif.com/storage/media/transpalet-main.jpg</g:image_link>
  <g:additional_image_link>https://ixtif.com/storage/media/transpalet-2.jpg</g:additional_image_link>
  <g:price>15000.00 TRY</g:price>
  <g:availability>in stock</g:availability>
  <g:condition>new</g:condition>
  <g:brand>iXtif</g:brand>
  <g:gtin>1234567890123</g:gtin>
  <g:mpn>TP-2000-STD</g:mpn>
  <g:google_product_category>Business &amp; Industrial &gt; Material Handling &gt; Pallet Jacks &amp; Stackers</g:google_product_category>
  <g:product_type>Transpalet &gt; Manuel &gt; 2 Ton</g:product_type>
</item>

</channel>
</rss>
```

---

## ⚠️ DİKKAT EDİLECEKLER

### 1. MediaManagement Entegrasyonu
```php
// ShopProduct modelinde HasMediaManagement trait var
// Kullanımı:
$product = ShopProduct::find(1);
$featuredImage = $product->getFirstMediaUrl('featured_image');
$galleryImages = $product->getMedia('gallery');
```

### 2. Performance
```php
// Eloquent eager loading kullan (N+1 problemi önleme)
$products = ShopProduct::with(['media', 'category', 'brand'])
    ->where('is_active', 1)
    ->get();
```

### 3. Cache (İsteğe Bağlı)
```php
// Feed 1 saat cache'le (günlük fetch yeterli)
$xml = Cache::remember('google_shopping_feed', 3600, function() {
    // Feed generation logic
});
```

### 4. Limit Kaldır
```php
// 100 ürün limiti kaldır (tüm aktif ürünleri göster)
->limit(100) // BU SATIRI SİL
```

---

## 📊 BEKLENEN SONUÇ

### Öncesi (Mevcut):
- 7 alan (id, title, description, link, price, availability, condition, brand)
- Eksik: Görseller, GTIN/MPN, kategori, stok kontrolü

### Sonrası (İyileştirilmiş):
- 13+ alan (tüm zorunlu + önerilen alanlar)
- Görseller ✅
- GTIN/MPN ✅
- Google kategori ✅
- Dinamik stok ✅
- İndirim fiyatı ✅
- Ek görseller ✅

### Google Merchant Center Onay Süresi:
- İlk feed yükleme: 1-3 gün
- Ürün onayı: 24-72 saat
- Hata varsa: Hemen bildirim

---

## 🔗 KAYNAKLAR

- Google Merchant Center: https://merchants.google.com
- Feed Specification: https://support.google.com/merchants/answer/7052112
- Product Data Spec: https://support.google.com/merchants/answer/7052112
- Category Taxonomy: https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt
