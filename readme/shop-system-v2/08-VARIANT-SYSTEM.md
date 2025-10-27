# 🔀 HİBRİT VARIANT SİSTEMİ

## 🎯 AMAÇ

Shop modülü **iki farklı variant yaklaşımını** aynı anda destekler:

1. **Product-Based Variants (Karmaşık Varyantlar)**: Her varyant ayrı bir `ShopProduct` kaydıdır
2. **Simple Variants (Basit Varyantlar)**: `shop_product_variants` tablosunda sadece fiyat/stok farklılıkları

---

## 📊 SİSTEM MİMARİSİ

### Product-Based Variants (Ana Sistem)

**Ne zaman kullanılır:**
- Varyantlar **farklı teknik özelliklere** sahipse
- Her varyantın **kendi açıklaması ve SEO'su** olacaksa
- Varyantlar **farklı görsellere** sahipse

**Örnek:**
```
F4 201 Transpalet
├─ F4 201 - Standart Çatal (1150x560 mm)
├─ F4 201 - Geniş Çatal (1150x685 mm)
├─ F4 201 - Kısa Çatal (900x560 mm)
├─ F4 201 - Uzun Çatal (1500x560 mm)
└─ F4 201 - Genişletilmiş Batarya (4x 24V/20Ah)
```

**Database Yapısı:**
```sql
shop_products:
  product_id       | parent_product_id | is_master_product | variant_type
  12               | NULL              | 1                 | NULL            (Master)
  13               | 12                | 0                 | standart-catal  (Child)
  14               | 12                | 0                 | genis-catal     (Child)
  15               | 12                | 0                 | kisa-catal      (Child)
```

**Model İlişkileri:**
```php
// ShopProduct.php
public function parentProduct(): BelongsTo
{
    return $this->belongsTo(ShopProduct::class, 'parent_product_id', 'product_id');
}

public function childProducts()
{
    return $this->hasMany(ShopProduct::class, 'parent_product_id', 'product_id');
}

public function isVariant(): bool
{
    return !is_null($this->parent_product_id);
}
```

---

### Simple Variants (Yardımcı Sistem)

**Ne zaman kullanılır:**
- Sadece **fiyat ve stok** farklılıkları varsa
- Açıklama/SEO/görsel farklılığı **gerekmiyorsa**

**Örnek:**
```
iPhone 15 Pro
├─ 128 GB - 54.999 TL
├─ 256 GB - 59.999 TL
└─ 512 GB - 69.999 TL
```

**Database Yapısı:**
```sql
shop_product_variants:
  variant_id | product_id | title          | price    | stock
  1          | 100        | {"tr":"128GB"} | 54999.00 | 10
  2          | 100        | {"tr":"256GB"} | 59999.00 | 5
  3          | 100        | {"tr":"512GB"} | 69999.00 | 3
```

---

## 🛠️ KULLANIM ÖRNEKLERİ

### 1. Product-Based Variant Seeder

```php
// Master Product
$productId = DB::table('shop_products')->insertGetId([
    'sku' => 'F4-201',
    'parent_product_id' => null,
    'is_master_product' => true,
    'title' => json_encode(['tr' => 'F4 201 Li-Ion Transpalet'], JSON_UNESCAPED_UNICODE),
    'slug' => json_encode(['tr' => 'f4-201-transpalet'], JSON_UNESCAPED_UNICODE),
    // ... diğer alanlar
]);

// Child Products (Variants)
foreach ($variants as $variant) {
    $childId = DB::table('shop_products')->insertGetId([
        'sku' => $variant['sku'],
        'parent_product_id' => $productId,  // Master'a bağlı
        'is_master_product' => false,
        'variant_type' => Str::slug($variant['name']),
        'title' => json_encode(['tr' => $variant['name']], JSON_UNESCAPED_UNICODE),
        'slug' => json_encode(['tr' => $parentSlug . '-' . Str::slug($variant['name'])], JSON_UNESCAPED_UNICODE),
        // ... diğer alanlar
    ]);
}
```

### 2. Simple Variant Seeder

```php
// Ana Ürün
$productId = DB::table('shop_products')->insertGetId([
    'sku' => 'IPHONE-15-PRO',
    'title' => json_encode(['tr' => 'iPhone 15 Pro'], JSON_UNESCAPED_UNICODE),
    'slug' => json_encode(['tr' => 'iphone-15-pro'], JSON_UNESCAPED_UNICODE),
    'base_price' => 54999.00,
    // ...
]);

// Basit Varyantlar
$variants = [
    ['title' => '128 GB', 'price' => 54999, 'stock' => 10],
    ['title' => '256 GB', 'price' => 59999, 'stock' => 5],
    ['title' => '512 GB', 'price' => 69999, 'stock' => 3],
];

foreach ($variants as $variant) {
    DB::table('shop_product_variants')->insert([
        'product_id' => $productId,
        'title' => json_encode(['tr' => $variant['title']], JSON_UNESCAPED_UNICODE),
        'price' => $variant['price'],
        'stock' => $variant['stock'],
        'is_active' => 1,
    ]);
}
```

---

## 🎨 FRONTEND GÖSTERİMİ

### Product-Based Variants (show.blade.php)

```blade
{{-- Line 255-338 --}}
@if($siblingVariants->count() > 0)
    <section id="variants" class="bg-white dark:bg-gray-900 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($siblingVariants as $variant)
                <a href="{{ $variantUrl }}">
                    <h3>{{ $variantTitle }}</h3>
                    <p>{{ $variantDescription }}</p>
                    <span>{{ $variant->sku }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endif
```

### Simple Variants

```blade
{{-- Basit varyant seçici --}}
@if($item->variants->count() > 0)
    <div class="variant-selector">
        <label>Seçenekler:</label>
        <select name="variant_id">
            @foreach($item->variants as $variant)
                <option value="{{ $variant->variant_id }}" data-price="{{ $variant->price }}">
                    {{ $variant->getTranslated('title') }} - {{ $variant->price }} TL
                </option>
            @endforeach
        </select>
    </div>
@endif
```

---

## 📋 CONTROLLER LOJIK

```php
// ShopController.php - show() method
public function show(string $slug)
{
    $product = ShopProduct::query()
        ->with(['variants', 'seoSetting'])
        ->where('slug->tr', $slug)
        ->firstOrFail();

    $parentProduct = null;
    $siblingVariants = collect();

    if ($product->isVariant()) {
        // Bu bir child product (variant)
        $parentProduct = $product->parentProduct;

        if ($parentProduct) {
            // Diğer varyantları getir (bu ürün hariç)
            $siblingVariants = $parentProduct->childProducts()
                ->where('product_id', '!=', $product->product_id)
                ->active()
                ->published()
                ->get();
        }
    } else {
        // Bu bir master product veya normal product
        // Child products'ları getir
        $siblingVariants = $product->childProducts()
            ->active()
            ->published()
            ->get();
    }

    return view('shop::front.show', [
        'item' => $product,
        'parentProduct' => $parentProduct,
        'siblingVariants' => $siblingVariants,
    ]);
}
```

---

## 🔍 KARAR AĞACI

```
Ürüne varyant ekleyeceksin
       |
       ├─── Varyantlar SADECE fiyat/stok farklılığı mı?
       |         |
       |         ├─── EVET → Simple Variants (shop_product_variants)
       |         |              Örnek: iPhone 128GB/256GB/512GB
       |         |
       |         └─── HAYIR → Devam et
       |
       └─── Varyantların farklı özellikleri var mı?
                 |
                 ├─── Farklı çatal uzunluğu? → Product-Based Variants
                 ├─── Farklı batarya kapasitesi? → Product-Based Variants
                 ├─── Farklı görseller? → Product-Based Variants
                 ├─── Farklı açıklamalar? → Product-Based Variants
                 └─── Farklı SEO gereksinimleri? → Product-Based Variants
```

---

## ✅ AVANTAJLAR

### Product-Based Variants

✅ Her varyant için ayrı SEO
✅ Her varyant için ayrı slug/URL
✅ Her varyant için ayrı görsel galeri
✅ Her varyant için detaylı açıklama
✅ Arama motorlarında her varyant ayrı indexlenir
✅ Analytics'te her varyant ayrı takip edilir

### Simple Variants

✅ Hızlı kurulum
✅ Az veri saklama
✅ Basit yönetim
✅ Fiyat/stok güncellemeleri kolay

---

## ⚠️ DİKKAT EDİLMESİ GEREKENLER

### Product-Based Variants

1. **SKU benzersiz olmalı**
   ```php
   'sku' => 'F4-201-STD',  // ✅ Doğru
   'sku' => 'F4-201',       // ❌ Master ile aynı
   ```

2. **Slug benzersiz olmalı**
   ```php
   'slug' => ['tr' => 'f4-201-standart-catal'],  // ✅ Doğru
   'slug' => ['tr' => 'f4-201'],                 // ❌ Master ile aynı
   ```

3. **Parent ID doğru bağlanmalı**
   ```php
   'parent_product_id' => $masterProductId,  // ✅ Doğru
   'parent_product_id' => null,               // ❌ Yanlış (master gibi görünür)
   ```

4. **is_master_product bayrakları doğru set edilmeli**
   ```php
   // Master
   'is_master_product' => true,
   'parent_product_id' => null,

   // Child
   'is_master_product' => false,
   'parent_product_id' => $masterId,
   ```

### Simple Variants

1. **JSON formatı her zaman kullanılmalı**
   ```php
   'title' => json_encode(['tr' => '128 GB'], JSON_UNESCAPED_UNICODE),  // ✅
   'title' => '128 GB',                                                  // ❌
   ```

2. **product_id doğru bağlanmalı**
   ```php
   'product_id' => $parentProductId,  // ✅
   ```

---

## 📦 ÖRNEK SEEDER YAPISI

### F4 201 Transpalet (Product-Based)

```
F4_201_Transpalet_Seeder.php
├─ 1) Marka güncelle (İXTİF)
├─ 2) Kategori güncelle (Transpaletler)
├─ 3) Eski kayıtları temizle (SKU: F4-201%)
├─ 4) Master Product ekle
│     - sku: F4-201
│     - is_master_product: true
│     - parent_product_id: NULL
│     - Full data (description, specs, images, FAQ, etc.)
├─ 5) Child Products ekle (loop)
│     - sku: F4-201-STD, F4-201-WIDE, etc.
│     - is_master_product: false
│     - parent_product_id: $masterProductId
│     - variant_type: 'standart-catal', 'genis-catal', etc.
│     - Minimal data (title, slug, short_description)
└─ 6) İstatistik göster
```

---

## 🚀 ÖZET

| Özellik | Product-Based | Simple Variants |
|---------|--------------|-----------------|
| Ayrı URL | ✅ Evet | ❌ Hayır |
| Ayrı SEO | ✅ Evet | ❌ Hayır |
| Ayrı Görsel | ✅ Evet | ❌ Hayır |
| Ayrı Açıklama | ✅ Evet | ❌ Hayır |
| Fiyat Farklılığı | ✅ Evet | ✅ Evet |
| Stok Takibi | ✅ Evet | ✅ Evet |
| Karmaşıklık | Yüksek | Düşük |
| Kullanım Alanı | Teknik ürünler | Basit seçenekler |

---

**🎉 İki sistemin birlikte kullanımı, hem karmaşık hem basit varyant ihtiyaçlarını karşılar!**
