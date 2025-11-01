# 🔄 Karşılaştırma Matrisi + Varyant + Opsiyon Sistemi

**Tarih:** 2025-11-01
**Konu:** Karmaşık sistemlerin çözümü

---

## 1️⃣ KARŞILAŞTIRMA MATRİSİ SORUNU

### Kullanıcı Sorusu:
> "Ürünlerdeki içerikler bir olmayabilir. JSON'lar farklı olabilir. Bir yöntemi var mı?"

### SORUN ANALİZİ:

**Senaryo:**
```
F4 Transpalet:
  - Kapasite: 1500 kg
  - Batarya: 24V Li-Ion
  - Kaldırma Yüksekliği: 105 mm

CPD20 Forklift:
  - Kapasite: 2000 kg
  - Batarya: 80V Li-Ion
  - Kaldırma Yüksekliği: 4500 mm
  - Mast Tipi: Triplex  ← F4'te YOK!
```

→ Nasıl karşılaştıracağız?

---

## ✅ ÇÖZÜM: HYBRID KARŞILAŞTIRMA SİSTEMİ

### A. KATEGORİ BAZLI STANDART ALANLAR

**Her kategori için "Karşılaştırılabilir Alanlar" tanımla:**

```json
// /readme/shop-system-v4/COMPARISON-FIELDS.json

{
  "transpalet": {
    "comparable_fields": [
      {"key": "capacity", "label": "Kapasite", "unit": "kg", "type": "numeric"},
      {"key": "battery_voltage", "label": "Batarya Voltajı", "unit": "V", "type": "numeric"},
      {"key": "battery_type", "label": "Batarya Tipi", "unit": "", "type": "text"},
      {"key": "weight", "label": "Ağırlık", "unit": "kg", "type": "numeric"},
      {"key": "fork_length", "label": "Çatal Uzunluğu", "unit": "mm", "type": "numeric"},
      {"key": "max_speed", "label": "Maksimum Hız", "unit": "km/h", "type": "numeric"},
      {"key": "lift_height", "label": "Kaldırma Yüksekliği", "unit": "mm", "type": "numeric"}
    ]
  },

  "forklift": {
    "comparable_fields": [
      {"key": "capacity", "label": "Kapasite", "unit": "kg", "type": "numeric"},
      {"key": "battery_voltage", "label": "Batarya Voltajı", "unit": "V", "type": "numeric"},
      {"key": "lift_height", "label": "Kaldırma Yüksekliği", "unit": "mm", "type": "numeric"},
      {"key": "mast_type", "label": "Mast Tipi", "unit": "", "type": "text"},
      {"key": "fork_length", "label": "Çatal Uzunluğu", "unit": "mm", "type": "numeric"},
      {"key": "max_speed", "label": "Maksimum Hız", "unit": "km/h", "type": "numeric"}
    ]
  }
}
```

### B. ÜRÜN VERİLERİ (Standart Alanlara Mapping)

**F4 Transpalet:**
```json
{
  "product_id": 245,
  "category": "transpalet",
  "comparison_data": {
    "capacity": 1500,
    "battery_voltage": 24,
    "battery_type": "Li-Ion",
    "weight": 120,
    "fork_length": 1150,
    "max_speed": 4.5,
    "lift_height": 105
  }
}
```

**F4-201 Transpalet (2 Ton):**
```json
{
  "product_id": 241,
  "category": "transpalet",
  "comparison_data": {
    "capacity": 2000,
    "battery_voltage": 24,
    "battery_type": "Li-Ion",
    "weight": 140,
    "fork_length": 1150,
    "max_speed": 4.0,
    "lift_height": 105
  }
}
```

**CPD20 Forklift:**
```json
{
  "product_id": 180,
  "category": "forklift",
  "comparison_data": {
    "capacity": 2000,
    "battery_voltage": 80,
    "battery_type": "Li-Ion",
    "lift_height": 4500,
    "mast_type": "Triplex",
    "fork_length": 1070,
    "max_speed": 18
  }
}
```

### C. DİNAMİK KARŞILAŞTIRMA MOTORU

**Backend (Laravel Controller):**

```php
<?php

namespace App\Services;

class ProductComparisonService
{
    public function compare(array $productIds): array
    {
        $products = ShopProduct::whereIn('product_id', $productIds)->get();

        // Ortak kategori mi kontrol et
        $categories = $products->pluck('category_id')->unique();

        if ($categories->count() > 1) {
            // Farklı kategoriler → Sadece UNIVERSAL alanları karşılaştır
            return $this->compareUniversal($products);
        }

        // Aynı kategori → Kategori-spesifik alanları karşılaştır
        return $this->compareByCategory($products);
    }

    private function compareByCategory($products)
    {
        $category = $products->first()->category->slug; // 'transpalet'

        // COMPARISON-FIELDS.json'dan tanımları al
        $fields = $this->getComparableFields($category);

        $comparison = [];

        foreach ($fields as $field) {
            $comparison[$field['key']] = [
                'label' => $field['label'],
                'unit' => $field['unit'],
                'type' => $field['type'],
                'values' => []
            ];

            foreach ($products as $product) {
                $value = $product->comparison_data[$field['key']] ?? '-';

                $comparison[$field['key']]['values'][$product->product_id] = [
                    'value' => $value,
                    'formatted' => $this->formatValue($value, $field),
                    'is_best' => false  // Sonra hesaplanacak
                ];
            }

            // "Best" değeri belirle (numeric için en yüksek/en düşük)
            if ($field['type'] === 'numeric') {
                $this->markBestValue($comparison[$field['key']], $field['key']);
            }
        }

        return $comparison;
    }

    private function compareUniversal($products)
    {
        // Farklı kategorilerdeki ürünler
        // Sadece EVRENSEL alanları karşılaştır:
        // - Kapasite
        // - Ağırlık
        // - Batarya Voltajı
        // - Fiyat

        $universalFields = [
            'capacity', 'weight', 'battery_voltage', 'base_price'
        ];

        // Yukarıdakine benzer mantık...
    }

    private function markBestValue(&$field, $fieldKey)
    {
        $values = array_column($field['values'], 'value');

        // "Best" tanımı field'a göre değişir:
        $betterHigher = ['capacity', 'battery_voltage', 'lift_height', 'max_speed'];
        $betterLower = ['weight', 'base_price', 'charging_time'];

        if (in_array($fieldKey, $betterHigher)) {
            $bestValue = max($values);
        } elseif (in_array($fieldKey, $betterLower)) {
            $bestValue = min($values);
        } else {
            return; // Best yok (text field)
        }

        foreach ($field['values'] as $productId => &$data) {
            if ($data['value'] == $bestValue) {
                $data['is_best'] = true;
            }
        }
    }

    private function formatValue($value, $field)
    {
        if ($value === '-' || $value === null) {
            return '-';
        }

        if ($field['type'] === 'numeric' && $field['unit']) {
            return $value . ' ' . $field['unit'];
        }

        return $value;
    }
}
```

### D. FRONTEND (Blade Template)

```blade
{{-- resources/views/shop/compare.blade.php --}}

<div class="comparison-table">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Özellik</th>
                @foreach($products as $product)
                <th>
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->title }}">
                    <h5>{{ $product->title }}</h5>
                    <p class="price">{{ $product->base_price }} TL</p>
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($comparison as $fieldKey => $field)
            <tr>
                <td><strong>{{ $field['label'] }}</strong></td>

                @foreach($products as $product)
                <td class="{{ $field['values'][$product->product_id]['is_best'] ? 'best-value' : '' }}">
                    {{ $field['values'][$product->product_id]['formatted'] }}

                    @if($field['values'][$product->product_id]['is_best'])
                        <i class="fas fa-check-circle text-success"></i>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

### E. ÖRNEK ÇIKTI (F4 vs F4-201)

| Özellik | F4 1.5 Ton | F4-201 2.0 Ton |
|---------|------------|----------------|
| **Kapasite** | 1500 kg | **2000 kg** ✅ |
| **Ağırlık** | **120 kg** ✅ | 140 kg |
| **Batarya** | 24V Li-Ion | 24V Li-Ion |
| **Hız** | **4.5 km/h** ✅ | 4.0 km/h |
| **Fiyat** | **45,000 TL** ✅ | 52,000 TL |

### F. CROSS-CATEGORY KARŞILAŞTIRMA (Farklı Kategoriler)

**F4 Transpalet vs CPD20 Forklift:**

| Özellik | F4 Transpalet | CPD20 Forklift |
|---------|---------------|----------------|
| **Kapasite** | 1500 kg | **2000 kg** ✅ |
| **Ağırlık** | **120 kg** ✅ | 2800 kg |
| **Batarya** | 24V Li-Ion | 80V Li-Ion |
| **Kaldırma Yüksekliği** | 105 mm | **4500 mm** ✅ |
| **Mast Tipi** | - | Triplex |
| **Çatal Uzunluğu** | 1150 mm | 1070 mm |

→ Eksik alanlar "-" olarak gösterilir, sorun çıkmaz!

---

## 2️⃣ VARYANT SİSTEMİ (SEO Uyumlu)

### Kullanıcı Sorusu:
> "Varyantlar Google'da sorun çıkarmamalı. Neyin varyant olacağını nasıl karar verelim?"

### SORUN ANALİZİ:

**F4 Durumu:**
```
F4 Ana Ürün (1.5 Ton Li-Ion Transpalet)
├── F4-1500-1150x560 (1150×560 mm Çatal)
├── F4-1500-1220x685 (1220×685 mm Çatal)
├── F4-1500-1000x560 (1000×560 mm Çatal)
├── F4-1500-1350x685 (1350×685 mm Çatal)
├── F4-1500-900x560 (900×560 mm Çatal)
└── F4-1500-1500x685 (1500×685 mm Çatal)
```

**Google SEO Sorunları:**
1. **Duplicate Content** → Her varyant ayrı sayfa, içerik %95 aynı
2. **Canonical Tag** → Hangi varyant "ana" sayfa?
3. **URL Structure** → `/shop/f4-1500-1150x560` vs `/shop/f4-1500` hangisi?
4. **Schema.org** → Varyantlar nasıl işaretlenir?

---

## ✅ ÇÖZÜM: GOOGLE UYUMLU VARYANT SİSTEMİ

### A. VARYANT TANIMLAMA KURALLARI

**VARYANT OLUR:**
```
✅ Aynı ürünün BOYUT/RENK/MİKTAR farkları
✅ Temel özellikler AYNI (motor, platform, teknoloji)
✅ Fiyat/Stok FARKLI olabilir
✅ SKU farklı ama ailesi aynı
```

**Örnekler:**
- ✅ Fork uzunluğu (900mm, 1150mm, 1220mm)
- ✅ Fork genişliği (560mm, 685mm)
- ✅ Batarya kapasitesi (20Ah, 40Ah - aynı voltaj)
- ✅ Renk (sanayi ürünlerinde nadir)

**AYRI ÜRÜN OLUR:**
```
❌ Kapasite farkı (1.5 ton vs 2.0 ton) → FARKLI ÜRÜN!
❌ Enerji tipi farkı (Li-Ion vs Kurşun Asit) → FARKLI ÜRÜN!
❌ Platform farkı (F4 vs F5) → FARKLI ÜRÜN!
❌ Marka farkı (EP vs Toyota) → FARKLI ÜRÜN!
```

**F4 Örneği:**
```
F4 1.5 Ton → Ana Ürün
  ├── Fork 1150×560 → VARYANT ✅
  ├── Fork 1220×685 → VARYANT ✅
  └── Çift Batarya Opsiyonu → VARYANT ✅

F4-201 2.0 Ton → AYRI ÜRÜN ❌ (Kapasite farklı!)
```

### B. DATABASE YAPISI

**Ana Ürün (Master):**
```json
{
  "product_id": 245,
  "sku": "F4-1500",
  "is_master_product": true,
  "parent_product_id": null,
  "variant_type": null,
  "title": "F4 1.5 Ton Lityum Akülü Transpalet",
  "slug": "f4-15-ton-lityum-transpalet",

  "available_variants": [
    {
      "type": "fork_size",
      "label": "Çatal Ölçüsü",
      "options": [
        {"value": "1150x560", "label": "1150×560 mm", "sku_suffix": "1150x560"},
        {"value": "1220x685", "label": "1220×685 mm", "sku_suffix": "1220x685"},
        {"value": "1000x560", "label": "1000×560 mm", "sku_suffix": "1000x560"}
      ]
    },
    {
      "type": "battery_count",
      "label": "Batarya Sayısı",
      "options": [
        {"value": "single", "label": "Tek Batarya (20Ah)", "sku_suffix": "1B"},
        {"value": "dual", "label": "Çift Batarya (2×20Ah)", "sku_suffix": "2B"}
      ]
    }
  ]
}
```

**Varyant Ürünler:**
```json
{
  "product_id": 246,
  "sku": "F4-1500-1150x560",
  "is_master_product": false,
  "parent_product_id": 245,  // ← Ana ürüne bağlı
  "variant_type": "fork_size",
  "variant_value": "1150x560",
  "title": "F4 1.5 Ton - 1150×560 mm Çatal",
  "slug": "f4-15-ton-1150x560",  // ← Farklı slug ama canonical master'a

  "base_price": 45000,  // ← Varyanta göre fiyat değişebilir
  "current_stock": 5
}
```

### C. SEO KURALLARI

#### 1. **URL YAPISI**

**✅ ÖNERİLEN:**
```
Ana Ürün:  /shop/f4-15-ton-lityum-transpalet
Varyant:   /shop/f4-15-ton-lityum-transpalet?variant=1150x560

VEYA

Ana Ürün:  /shop/f4-15-ton-lityum-transpalet
Varyant:   /shop/f4-15-ton-lityum-transpalet/1150x560
```

**❌ KULLANMA:**
```
/shop/f4-1500-1150x560  ← Ayrı sayfa gibi görünür!
```

#### 2. **CANONICAL TAG**

**Tüm varyantlar ana ürüne canonical atar:**

```html
<!-- f4-15-ton-lityum-transpalet?variant=1150x560 sayfasında -->
<link rel="canonical" href="https://ixtif.com/shop/f4-15-ton-lityum-transpalet">
```

#### 3. **SCHEMA.ORG (Structured Data)**

```json
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "F4 1.5 Ton Lityum Akülü Transpalet",
  "sku": "F4-1500",
  "offers": {
    "@type": "AggregateOffer",
    "lowPrice": "45000",
    "highPrice": "52000",
    "priceCurrency": "TRY",
    "offerCount": "6",
    "offers": [
      {
        "@type": "Offer",
        "sku": "F4-1500-1150x560",
        "price": "45000",
        "availability": "https://schema.org/InStock"
      },
      {
        "@type": "Offer",
        "sku": "F4-1500-1220x685",
        "price": "47000",
        "availability": "https://schema.org/OutOfStock"
      }
    ]
  },
  "hasVariant": [
    {
      "@type": "Product",
      "name": "F4 - 1150×560 mm Çatal",
      "sku": "F4-1500-1150x560"
    },
    {
      "@type": "Product",
      "name": "F4 - 1220×685 mm Çatal",
      "sku": "F4-1500-1220x685"
    }
  ]
}
```

#### 4. **HREFLANG (Çoklu Dil)**

```html
<!-- Ana ürün -->
<link rel="alternate" hreflang="tr" href="https://ixtif.com/shop/f4-15-ton-lityum-transpalet">
<link rel="alternate" hreflang="en" href="https://ixtif.com/en/shop/f4-15-ton-lithium-pallet-truck">

<!-- Varyant da aynı -->
<link rel="alternate" hreflang="tr" href="https://ixtif.com/shop/f4-15-ton-lityum-transpalet?variant=1150x560">
<link rel="alternate" hreflang="en" href="https://ixtif.com/en/shop/f4-15-ton-lithium-pallet-truck?variant=1150x560">
```

#### 5. **META TAGS**

**Ana Ürün:**
```html
<title>F4 1.5 Ton Lityum Akülü Transpalet - 6 Çatal Seçeneği | İXTİF</title>
<meta name="description" content="F4 kompakt transpalet: 1.5 ton kapasite, çift Li-Ion batarya, 6 farklı çatal uzunluğu. Dar koridorlarda maksimum çeviklik.">
<meta name="robots" content="index, follow">
```

**Varyant:**
```html
<title>F4 - 1150×560 mm Çatal | İXTİF</title>
<meta name="description" content="F4 1.5 ton transpalet 1150×560 mm çatal seçeneği. Stokta mevcut, hızlı teslimat.">
<meta name="robots" content="noindex, follow">  ← Varyantlar index edilmemeli!
```

### D. FRONTEND (Varyant Seçici)

```blade
{{-- Product Show Page --}}

<div class="product-variants">
    <h3>Çatal Ölçüsü Seçin:</h3>

    <div class="variant-selector">
        @foreach($product->getVariants('fork_size') as $variant)
        <label class="variant-option {{ $variant->current_stock > 0 ? '' : 'out-of-stock' }}">
            <input type="radio"
                   name="variant_fork"
                   value="{{ $variant->product_id }}"
                   data-price="{{ $variant->base_price }}"
                   data-sku="{{ $variant->sku }}"
                   {{ $variant->current_stock > 0 ? '' : 'disabled' }}>

            <span class="variant-label">{{ $variant->variant_value }}</span>
            <span class="variant-price">{{ number_format($variant->base_price) }} TL</span>

            @if($variant->current_stock > 0)
                <span class="badge bg-success">Stokta</span>
            @else
                <span class="badge bg-danger">Tükendi</span>
            @endif
        </label>
        @endforeach
    </div>

    <div class="selected-variant-info mt-3">
        <p><strong>Seçili Model:</strong> <span id="selected-sku">F4-1500-1150x560</span></p>
        <p><strong>Fiyat:</strong> <span id="selected-price">45,000 TL</span></p>
        <button class="btn btn-primary btn-lg">Sepete Ekle</button>
    </div>
</div>
```

### E. GOOGLE SEARCH CONSOLE KONTROLÜ

**Sorunsuz Varyant Sistemi:**
```
✅ Ana ürün index edilir
✅ Varyantlar noindex (canonical ile ana ürüne işaret)
✅ Duplicate content yok
✅ Structured data hatasız
✅ Mobile-friendly
```

**Kontrol:**
```bash
# Google Search Console → Coverage Report
# "Excluded" altında:
# - "Duplicate, submitted URL not selected as canonical" OLMAMALI
# - "Noindex" olarak varyantlar görünmeli ✅
```

---

## 3️⃣ OPSİYON/AKSESUAR SİSTEMİ

### Kullanıcı Geri Bildirimi:
> "Opsiyonlar olmalılar. Mevcut sistemde vardı."

### VARYANT vs OPSİYON FARKI

**VARYANT:**
- ✅ Ürünün KENDİ parçası (fork, batarya sayısı)
- ✅ Ürün OLMADAN alınamaz
- ✅ Fiyat + stok varyanta göre değişir

**OPSİYON/AKSESUAR:**
- ✅ Ürüne EKSTRA alınan parça
- ✅ Ürün OLMADAN da alınabilir (yedek parça)
- ✅ Ayrı SKU, ayrı fiyat, ayrı stok

### F4 Örneği:

**VARYANTLAR:**
```
- Fork ölçüsü (1150mm, 1220mm) → Transpalet OLMADAN anlamsız
- Batarya sayısı (1x, 2x) → Transpalet OLMADAN anlamsız
```

**OPSİYONLAR:**
```
- İkinci Li-Ion batarya → Sonradan eklenebilir, ayrı ürün
- Stabilizasyon tekerlekleri → Sonradan eklenebilir
- Hızlı şarj cihazı → Ayrı ürün
- Yedek fork takımı → Ayrı ürün
```

### DATABASE YAPISI

**Ana Ürün:**
```json
{
  "product_id": 245,
  "sku": "F4-1500",
  "title": "F4 1.5 Ton Lityum Akülü Transpalet",

  "available_options": [
    {
      "option_id": 1,
      "category": "battery",
      "name": "İkinci Li-Ion Batarya (24V 20Ah)",
      "sku": "F4-BAT-EXTRA",
      "price": 8500,
      "stock": 15,
      "description": "Kesintisiz operasyon için yedek batarya. Sıcak takas özelliği.",
      "image": "/storage/accessories/f4-battery.jpg",
      "compatible_with": [245, 241],  // F4 ve F4-201 ile uyumlu
      "icon": "fa-battery-half"
    },
    {
      "option_id": 2,
      "category": "wheels",
      "name": "Stabilizasyon Tekerlekleri",
      "sku": "F4-STAB-WHEELS",
      "price": 2400,
      "stock": 8,
      "description": "Bozuk zeminlerde dengeyi artırır, yük salınımını azaltır.",
      "image": "/storage/accessories/f4-stab-wheels.jpg",
      "compatible_with": [245],
      "icon": "fa-shield-alt"
    },
    {
      "option_id": 3,
      "category": "charging",
      "name": "Hızlı Şarj Cihazı (24V 40A)",
      "sku": "CHG-24V-40A",
      "price": 5200,
      "stock": 12,
      "description": "Şarj süresini %50 kısaltır (2-3 saat → 1-1.5 saat).",
      "image": "/storage/accessories/charger-24v.jpg",
      "compatible_with": [245, 241, 180, 182],  // Tüm 24V ürünler
      "icon": "fa-plug"
    }
  ]
}
```

### FRONTEND (Opsiyonlar Bölümü)

```blade
{{-- Product Show Page - Opsiyonlar --}}

<section class="product-options mt-5">
    <h3>Opsiyonel Aksesuarlar</h3>
    <p class="text-muted">F4 transpaletinizi bu aksesuarlarla güçlendirin:</p>

    <div class="row">
        @foreach($product->available_options as $option)
        <div class="col-md-4">
            <div class="card option-card">
                <img src="{{ $option['image'] }}" class="card-img-top" alt="{{ $option['name'] }}">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas {{ $option['icon'] }}"></i>
                        {{ $option['name'] }}
                    </h5>
                    <p class="card-text">{{ $option['description'] }}</p>
                    <p class="price">{{ number_format($option['price']) }} TL</p>

                    @if($option['stock'] > 0)
                        <button class="btn btn-outline-primary add-option"
                                data-option-id="{{ $option['option_id'] }}"
                                data-sku="{{ $option['sku'] }}"
                                data-price="{{ $option['price'] }}">
                            <i class="fas fa-plus"></i> Ekle
                        </button>
                        <span class="badge bg-success">Stokta ({{ $option['stock'] }})</span>
                    @else
                        <button class="btn btn-outline-secondary" disabled>
                            Tükendi
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Sepet Özeti --}}
<div class="cart-summary sticky-top">
    <h4>Sepet Özeti</h4>
    <ul>
        <li>F4 1.5 Ton - 1150×560 Çatal <strong>45,000 TL</strong></li>
        <li class="option-item" style="display:none;">
            + İkinci Batarya <strong id="option-1-price">8,500 TL</strong>
            <button class="remove-option" data-option-id="1">×</button>
        </li>
    </ul>
    <hr>
    <p><strong>Toplam:</strong> <span id="cart-total">45,000 TL</span></p>
    <button class="btn btn-success btn-lg w-100">Sepete Ekle</button>
</div>
```

### SEO İÇİN OPSİYON KURALLARI

**1. Ayrı Ürün Sayfası:**
```
✅ /shop/aksesuar/f4-ikinci-batarya
✅ /shop/aksesuar/stabilizasyon-tekerlekleri

← Aksesuar kendi sayfasına sahip, index edilir!
```

**2. "Related Products" Olarak Ana Ürüne Link:**
```blade
{{-- f4-15-ton-lityum-transpalet sayfasında --}}
<section class="related-accessories">
    <h3>Bu Ürünle Birlikte Alınanlar</h3>
    <a href="/shop/aksesuar/f4-ikinci-batarya">İkinci Batarya</a>
    <a href="/shop/aksesuar/stabilizasyon-tekerlekleri">Stab. Tekerlekleri</a>
</section>
```

**3. Breadcrumb:**
```html
Ana Sayfa > Shop > Aksesuarlar > İkinci Li-Ion Batarya
```

→ Google bu yapıyı anlayabilir, duplicate content riski yok!

---

## 📋 ÖZET TABLO

| Özellik | Varyant | Opsiyon/Aksesuar |
|---------|---------|------------------|
| **Tanım** | Ürünün farklı versiyonu | Ürüne ekstra alınan parça |
| **Örnek** | Fork 1150mm vs 1220mm | İkinci batarya, şarj cihazı |
| **URL** | `?variant=1150x560` | Ayrı sayfa `/aksesuar/...` |
| **Canonical** | Ana ürüne işaret | Kendi sayfası canonical |
| **Index** | Noindex | Index |
| **SKU** | `F4-1500-1150x560` | `F4-BAT-EXTRA` |
| **Fiyat** | Varyanta göre değişir | Sabit |
| **Stok** | Varyant bazlı | Aksesuar bazlı |
| **Sepet** | Tek seçim (radio) | Çoklu (checkbox) |

---

## SON KONTROL LİSTESİ

### Karşılaştırma Matrisi:
- [ ] COMPARISON-FIELDS.json oluşturuldu
- [ ] ProductComparisonService yazıldı
- [ ] Frontend karşılaştırma tablosu hazır
- [ ] Cross-category karşılaştırma destekleniyor
- [ ] "Best value" işaretleme çalışıyor

### Varyant Sistemi:
- [ ] Varyant tanımlama kuralları net
- [ ] is_master_product + parent_product_id ilişkisi kuruldu
- [ ] Canonical tag'ler doğru
- [ ] Schema.org hasVariant eklendi
- [ ] Varyantlar noindex
- [ ] Frontend varyant seçici çalışıyor

### Opsiyon/Aksesuar:
- [ ] available_options JSON field'ı dolu
- [ ] Her aksesuar ayrı ürün sayfasına sahip
- [ ] "Bu ürünle birlikte alınanlar" bölümü var
- [ ] Sepete ekleme çoklu seçim destekliyor
- [ ] Uyumluluk kontrolü (compatible_with) çalışıyor

---

**Versiyon:** V4.2
**Tarih:** 2025-11-01
