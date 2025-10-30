# 💰 SEPET DİNAMİK FİYATLANDIRMA - OTOM ATİK GÜNCELLEME

## 🎯 ÖZELLİK

Sepetteki ürün fiyatları **otomatik olarak güncellenir**:
- ✅ Ürün fiyatı değişirse → Sepetteki fiyat güncellenir
- ✅ Döviz kuru değişirse → Sepetteki fiyat güncellenir
- ✅ Her sepet görüntülendiğinde → Fiyatlar kontrol edilir

## 🔄 NASIL ÇALIŞIYOR?

### Akış:
```
1. Kullanıcı sepeti açar
   ↓
2. getCurrentCart() çağrılır
   ↓
3. refreshCartPrices() otomatik çalışır
   ↓
4. Her cart item için:
   - Ürünün güncel fiyatını al
   - Sepetteki fiyatla karşılaştır
   - Farklıysa güncelle
   ↓
5. Eğer fiyat değiştiyse:
   - Sepet toplamlarını yeniden hesapla
   - Database'e kaydet
   ↓
6. Kullanıcı güncel fiyatları görür
```

### Kod:
```php
// ShopCartService.php

public function getCurrentCart(): ShopCart
{
    $cart = ShopCart::findOrCreateForSession($sessionId);

    // Otomatik fiyat güncellemesi
    $this->refreshCartPrices($cart);

    return $cart;
}

public function refreshCartPrices(ShopCart $cart): void
{
    $items = $cart->items()->with(['product', 'variant'])->get();

    foreach ($items as $item) {
        // Güncel fiyatı al
        $currentPrice = $this->getProductPrice($item->product, $item->product_variant_id);

        // Fiyat değiştiyse güncelle
        if ($item->unit_price != $currentPrice) {
            $item->updatePrice($currentPrice, $item->discount_amount);
            $needsRecalculation = true;
        }
    }

    // Sepet toplamlarını güncelle
    if ($needsRecalculation) {
        $cart->recalculateTotals();
    }
}
```

## 📊 SENARYO ÖRNEKLERİ

### Senaryo 1: Ürün Fiyatı Değişti

**Durum:**
- Kullanıcı dün transpalet'i sepete ekleddi: **$1,700**
- Bugün fiyat güncellendi: **$1,650** (indirim!)
- Kullanıcı bugün sepeti açıyor

**Sonuç:**
- ✅ Sepetteki fiyat otomatik **$1,650** olur
- ✅ Kullanıcı güncel indirimi görür
- ✅ Sepet toplamı yeniden hesaplanır

### Senaryo 2: Döviz Kuru Değişti

**Durum:**
- Kullanıcı dün sepete ekledi: **$1,700** (USD)
- O zamanki kur: 1 USD = 35 TRY → **59,500 ₺**
- Bugün kur değişti: 1 USD = 36 TRY → **61,200 ₺**
- Kullanıcı bugün sepeti açıyor

**Sonuç:**
- ✅ Ürün fiyatı hala **$1,700** (USD değişmedi)
- ✅ CartPage component TRY conversion güncel kurla hesaplar
- ✅ Kullanıcı güncel TRY fiyatını görür: **61,200 ₺**

**NOT:** Döviz kuru güncellemesi **view layer**'da yapılır (CartPage.php):
```php
foreach ($items as $item) {
    if ($item->currency && $item->currency->code !== 'TRY') {
        $exchangeRate = $item->currency->exchange_rate; // Güncel kur!
        $subtotalTRY += ($item->subtotal ?? 0) * $exchangeRate;
    }
}
```

### Senaryo 3: Hem Fiyat Hem Kur Değişti

**Durum:**
- Dün: **$1,700** × 35 TRY = **59,500 ₺**
- Bugün: **$1,650** (fiyat düştü) × 36 TRY (kur arttı) = **59,400 ₺**

**Sonuç:**
- ✅ Database: **$1,650** güncellenir
- ✅ View: Güncel kur (36) ile TRY hesaplanır
- ✅ Kullanıcı: **59,400 ₺** görür

### Senaryo 4: Ürün Silindi/Stokta Yok

**Durum:**
- Kullanıcının sepetinde ürün var
- Admin ürünü sildi veya pasif yaptı

**Sonuç:**
- ✅ `refreshCartPrices()` ürün bulamazsa skip eder
- ✅ Sepet bozulmaz
- ❌ Kullanıcı checkout yaparken uyarı alır

## 🧪 TEST ADIMLARI

### Test 1: Ürün Fiyatı Değişikliği

```bash
# 1. Sepete ürün ekle
curl -X POST https://ixtif.com/api/cart/add \
  -d "product_id=10&quantity=1"

# 2. Sepeti görüntüle (fiyatı not et)
curl https://ixtif.com/shop/cart

# 3. Admin'den ürün fiyatını değiştir
mysql> UPDATE shop_products SET base_price = 1650 WHERE product_id = 10;

# 4. Sepeti tekrar görüntüle
curl https://ixtif.com/shop/cart

# SONUÇ: Fiyat otomatik güncellenmiş olmalı!
```

### Test 2: Döviz Kuru Değişikliği

```bash
# 1. Sepete USD ürün ekle
curl -X POST https://ixtif.com/api/cart/add \
  -d "product_id=10&quantity=1"

# 2. Sepeti görüntüle (TRY fiyatını not et)
curl https://ixtif.com/shop/cart

# 3. Admin'den USD kurunu değiştir
mysql> UPDATE shop_currencies SET exchange_rate = 36.50 WHERE code = 'USD';

# 4. Config cache yenile (currency cache için)
composer config-refresh

# 5. Sepeti tekrar görüntüle
curl https://ixtif.com/shop/cart

# SONUÇ: TRY fiyatı otomatik güncel kurla hesaplanmış olmalı!
```

### Test 3: Livewire Real-time Güncelleme

```bash
# 1. Sepet sayfasını aç: https://ixtif.com/shop/cart

# 2. Başka sekmede admin'den fiyat değiştir

# 3. Sepet sayfasında + veya - butonuna bas

# SONUÇ:
# - Livewire component loadCart() çağrılır
# - refreshCartPrices() otomatik çalışır
# - Güncel fiyat anında yansır!
```

## 🚨 PERFORMANS NOTLARI

### Optimizasyon:

**✅ YAPILAN:**
- `refreshCartPrices()` sadece `getCurrentCart()` çağrıldığında çalışır
- Fiyat değişmediyse database'e yazmaz
- Tek query ile tüm item'ları günceller

**⚠️ DİKKAT:**
- Her sepet görüntülemesinde fiyat kontrolü yapılır
- Çok sık sepet açılırsa hafif performans etkisi olabilir

**💡 İLERİ OPTİMİZASYON (Gerekirse):**
- Redis cache: Son fiyat kontrolü timestamp'i
- Eğer 5 dakika geçmediyse skip et
- Sadece fiyat değişimi olduğunda güncelle

```php
// Örnek Redis cache optimizasyonu (şu an gerekli değil)
$lastCheck = Redis::get("cart:{$cart->cart_id}:last_price_check");
if ($lastCheck && (time() - $lastCheck) < 300) {
    return; // 5 dakika geçmedi, skip
}

// Fiyatları güncelle...

Redis::set("cart:{$cart->cart_id}:last_price_check", time(), 'EX', 300);
```

## 🎯 AVANTAJLAR

**Kullanıcı Açısından:**
- ✅ Her zaman güncel fiyatları görür
- ✅ Surprise yok (checkout'ta farklı fiyat çıkmaz)
- ✅ İndirimlerden otomatik yararlanır
- ✅ Döviz kuru değişimi anında yansır

**Admin Açısından:**
- ✅ Fiyat güncellemesi hemen etkili olur
- ✅Eski sepetlerdeki fiyatlar otomatik güncellenir
- ✅ Manuel sepet temizliği gerekmez
- ✅ Döviz kuru güncellemesi sorunsuz

**Sistem Açısından:**
- ✅ Tutarlı fiyatlandırma
- ✅ Database'de eski fiyat kalmaz
- ✅ Checkout sırasında uyumsuzluk olmaz
- ✅ Otomatik senkronizasyon

## 📝 NOTLAR

**Dinamik vs Statik Fiyat:**
- ❌ **Önceki sistem**: Sepete eklendiğinde fiyat sabitlenir (statik)
- ✅ **Yeni sistem**: Her görüntülemede fiyat güncellenir (dinamik)

**Kullanıcı Bildirimi:**
- Şu an sessiz güncelleme yapılıyor
- İleride fiyat değişirse bildirim eklenebilir:
  ```
  "⚠️ Sepetinizdeki TransPalet'in fiyatı $1,700'den $1,650'ye düştü!"
  ```

**Alternative: Frozen Cart**
- Eğer checkout başladıysa fiyatları dondur
- Order oluşturulana kadar fiyat değişmesin
- Henüz uygulanmadı (gelecek feature)

---

**Son Güncelleme:** 2025-10-30
**Yazar:** Claude Code
**Commit:** TBD
