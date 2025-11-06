# Currency & Örnek Düzeltmeleri

## ⚠️ SORUNLAR

### 1. Hardcode Currency Sembolleri
**Mevcut (YANLIŞ):**
```
TRY → ₺
USD → $
EUR → €
```

**Düzeltme:**
Currency bilgileri `shop_currencies` tablosundan gelecek:
- `symbol`: ₺, $, €
- `format`: symbol_before / symbol_after
- `decimal_places`: Ondalık basamak sayısı

### 2. Hardcode Ürün Örnekleri AI'yı Şaşırtır!

**Mevcut (YANLIŞ):**
```markdown
⭐ **İXTİF EPT20 - 2 Ton Elektrikli Transpalet** [LINK:shop:ixtif-ept20]
Fiyat: ₺15.000
```

**Neden Yanlış:**
- AI "İXTİF EPT20" ürününün gerçekten var olduğunu düşünür
- Gerçek ürün yoksa hallüsinasyon yapar
- Placeholder kullanmalıyız!

**Düzeltme:**
```markdown
⭐ **{{ÜRÜN ADI}} - {{Kapasite}} Elektrikli Transpalet** [LINK:shop:{{slug}}]
Fiyat: {{currency_symbol}}{{price}}
```

---

## ✅ DÜZELTİLMİŞ FİYAT KURALLARI

### Currency Formatı (Dinamik)

**Ürün datası şu formatta gelecek:**
```json
{
  "title": "İXTİF EPT20 - 2 Ton",
  "slug": "ixtif-ept20-2-ton",
  "base_price": 15000,
  "currency": "TRY",
  "currency_symbol": "₺",
  "currency_format": "symbol_after",
  "decimal_places": 0
}
```

**AI Prompt Kuralı:**
```
💰 FİYAT GÖSTERME:
- Ürünün currency_symbol'ünü kullan (₺, $, €)
- currency_format'a göre yerleştir:
  - symbol_before: $1,350
  - symbol_after: 1.350 ₺
- decimal_places'e göre formatla:
  - 0: 15.000 ₺
  - 2: 15.000,50 ₺

**ÖRNEK (Placeholder ile):**
Fiyat: {{currency_symbol}}{{formatted_price}}

**GERÇEK OUTPUT (Backend işler):**
Fiyat: ₺15.000  (TRY, symbol_after)
Fiyat: $1,350   (USD, symbol_before)
```

---

## ✅ DÜZELTİLMİŞ ÖRNEK DİYALOG

**ESKİ (YANLIŞ - Hardcode):**
```markdown
⭐ **İXTİF EPT20 - 2 Ton Elektrikli Transpalet** [LINK:shop:ixtif-ept20]
Fiyat: ₺15.000
```

**YENİ (DOĞRU - Placeholder):**
```markdown
⭐ **{{product.title}}** [LINK:shop:{{product.slug}}]

- {{product.capacity}} kg taşıma kapasitesi
- {{product.feature_1}}
- {{product.feature_2}}

Fiyat: {{product.formatted_price}}

**PROMPT İÇİN NOT:**
- {{placeholder}} değerleri Meilisearch'ten gelen GERÇEK verilerle doldurulacak
- ASLA hardcode ürün adı/fiyat kullanma!
- Sadece Meilisearch sonuçlarını göster!
```

---

## 📋 BACKEND İŞLEMLERİ

### ProductSearchService (veya benzeri)
```php
// Meilisearch'ten gelen ürünü formatla
$formattedProducts = $products->map(function($product) {
    $currency = ShopCurrency::where('code', $product->currency)->first();
    
    return [
        'title' => $product->getTranslated('title', app()->getLocale()),
        'slug' => $product->getTranslated('slug', app()->getLocale()),
        'base_price' => $product->base_price,
        'currency' => $product->currency,
        'currency_symbol' => $currency->symbol ?? '₺',
        'currency_format' => $currency->format ?? 'symbol_after',
        'decimal_places' => $currency->decimal_places ?? 0,
        'formatted_price' => $this->formatPrice(
            $product->base_price, 
            $currency
        ),
    ];
});

// AI Context'e ekle
$aiContext['products'] = $formattedProducts;
```

### formatPrice() Metodu
```php
protected function formatPrice($price, $currency)
{
    // Binlik ayraç
    $formatted = number_format(
        $price, 
        $currency->decimal_places, 
        ',', 
        '.'
    );
    
    // Sembol yerleşimi
    if ($currency->format === 'symbol_before') {
        return $currency->symbol . $formatted;
    }
    
    return $formatted . ' ' . $currency->symbol;
}
```

---

## ✅ AI PROMPT İÇİN GÜNCEL KURAL

```markdown
💰 FİYAT GÖSTERME KURALLARI (KRİTİK!)

**⚠️ SADECE VERİLEN BİLGİYİ GÖSTER!**

**KURALLAR:**
1. ✅ Ürün fiyat datası varsa → formatted_price'ı AYNEN göster
2. ✅ Currency sembolü otomatik gelir (backend tarafından)
3. ❌ Fiyat datası yoksa → "Fiyat teklifi için iletişime geçin"
4. ❌ ASLA hafızandan fiyat kullanma!
5. ❌ ASLA tahmin yapma!
6. ❌ ASLA currency sembolü kendin ekleme!

**FORMAT:**
- Backend'den gelen formatted_price'ı AYNEN kullan
- Örnek: "Fiyat: ₺15.000" veya "Fiyat: $1,350"

**ÜRÜN GÖSTERME:**
```markdown
⭐ **{{product.title}}** [LINK:shop:{{product.slug}}]

- {{product.feature_1}}
- {{product.feature_2}}

Fiyat: {{product.formatted_price}}
```

**NOT:** 
- {{placeholder}} değerlerini değiştirme!
- Backend bu değerleri gerçek verilerle doldurur
- Sen sadece template'i takip et
```

---

## 🔧 YAPILACAKLAR

1. [ ] ProductSearchService: formatPrice() metodu ekle
2. [ ] ProductSearchService: Currency bilgisi çek (shop_currencies)
3. [ ] AI Context: formatted_price ekle
4. [ ] AI Prompt: Hardcode örnekleri kaldır, placeholder kullan
5. [ ] AI Prompt: Currency kuralını güncelle
6. [ ] Test: Farklı currency'ler ile test et (TRY, USD, EUR)

---

## ✅ SONUÇ

**Hardcode örnek KULLANMA:**
- ❌ "İXTİF EPT20" gibi gerçek (veya hayali) ürün adları
- ❌ "₺15.000" gibi sabit fiyatlar
- ❌ Sabit currency sembolleri

**Placeholder KULLAN:**
- ✅ {{product.title}}
- ✅ {{product.formatted_price}}
- ✅ {{product.slug}}
- ✅ Backend bunları gerçek verilerle doldurur
- ✅ AI sadece template'i takip eder, hallüsinasyon yapmaz!

**Currency Bilgisi:**
- ✅ shop_currencies tablosundan çekilir
- ✅ symbol, format, decimal_places kullanılır
- ✅ Backend formatPrice() ile işler
- ✅ AI sadece hazır formatted_price'ı gösterir
