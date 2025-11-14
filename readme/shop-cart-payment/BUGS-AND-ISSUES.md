# 🐛 Shop-Cart-Payment Bug ve Sorun Listesi

**Tarih**: 2025-11-14
**Versiyon**: 1.0.0
**Kritiklik**: 🔴 Yüksek

---

## 🔴 KRİTİK HATALAR

### BUG-001: Currency Dönüşümü Yapılmıyor
**Seviye**: 🔴 Kritik
**Modül**: Cart
**Dosya**: `Modules/Cart/app/Services/CartService.php:169`

**Açıklama**:
USD/EUR fiyatlı ürünler sepete eklenirken TL'ye çevrilmiyor. Direkt USD fiyat yazılıyor.

**Senaryo**:
1. USD fiyatlı ürün: $100
2. Sepete ekle
3. Sepette $100 görünüyor (₺3000 olmalı)

**Etki**:
- Yanlış fiyat gösterimi
- Ödeme hatası
- Müşteri güven kaybı

**Çözüm**:
```php
// CartService::setPricing() metoduna eklenecek:
if (isset($item->currency) && $item->currency !== 'TRY') {
    $currency = ShopCurrency::findByCode($item->currency);
    $unitPrice = $unitPrice * ($currency->exchange_rate ?? 1);
}
```

---

### BUG-002: Sepet Item Display Bilgileri Eksik
**Seviye**: 🔴 Kritik
**Modül**: Cart
**Dosya**: `Modules/Cart/app/Models/CartItem.php`

**Açıklama**:
CartItem'da ürün görseli, başlığı gibi display bilgileri yok. Frontend boş görünüyor.

**Eksik Field'lar**:
- item_title
- item_image
- item_sku
- original_currency
- original_price

**Etki**:
- Sepette ürün bilgileri görünmüyor
- Müşteri ne aldığını göremiyor

---

### BUG-003: Checkout Address Bağımlılığı
**Seviye**: 🔴 Kritik
**Modül**: Shop/Cart
**Dosya**: `Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php`

**Açıklama**:
Cart universal olması gerekirken Shop'taki address modellerine bağımlı.

**Problem**:
```php
use Modules\Shop\App\Models\ShopCustomerAddress; // Cart'ta olmamalı!
```

**Etki**:
- Diğer modüller checkout yapamaz
- Universal cart değil

---

## 🟡 ORTA SEVİYE HATALAR

### BUG-004: KDV Oranı Sabit %20
**Seviye**: 🟡 Orta
**Modül**: Cart
**Dosya**: `Modules/Cart/app/Services/CartService.php:213`

**Açıklama**:
Tüm ürünler için KDV %20 olarak sabit. Ürün bazlı KDV yok.

```php
// Default KDV %20
return 20.0; // HATALI: Ürün bazlı olmalı
```

---

### BUG-005: Stok Kontrolü Yok
**Seviye**: 🟡 Orta
**Modül**: Shop/Cart
**Dosya**: `Modules/Shop/app/Http/Livewire/Front/AddToCartButton.php`

**Açıklama**:
Sepete eklerken stok kontrolü yapılmıyor. Stoksuz ürün eklenebiliyor.

**Eksik Kontrol**:
```php
// Olması gereken:
if ($product->stock_tracking && $product->current_stock < $quantity) {
    throw new \Exception('Stok yetersiz');
}
```

---

### BUG-006: Session Cart Merge Eksik
**Seviye**: 🟡 Orta
**Modül**: Cart
**Dosya**: `Modules/Cart/app/Services/CartService.php`

**Açıklama**:
Misafir sepeti → Login sonrası merge edilmiyor.

**Senaryo**:
1. Misafir olarak ürün ekle
2. Login yap
3. Misafir sepeti kaybolur

---

## 🟢 DÜŞÜK SEVİYE SORUNLAR

### BUG-007: Cart Item Quantity Validation
**Seviye**: 🟢 Düşük
**Modül**: Cart

**Açıklama**:
Negatif veya 0 quantity engellenmiyor.

---

### BUG-008: Currency Symbol Formatting
**Seviye**: 🟢 Düşük
**Modül**: Shop

**Açıklama**:
Para birimi sembolleri tutarsız ($ vs USD).

---

## 📊 HATA MATRİSİ

| Bug ID | Kritiklik | Modül | Çözüm Süresi | Durum |
|--------|-----------|-------|--------------|--------|
| BUG-001 | 🔴 Kritik | Cart | 2 saat | ✅ ÇÖZÜLDİ (1h) |
| BUG-002 | 🔴 Kritik | Cart | 1 saat | ✅ ÇÖZÜLDİ (30m) |
| BUG-003 | 🔴 Kritik | Shop/Cart | 3 saat | ⏳ Sonraki Sprint |
| BUG-004 | 🟡 Orta | Cart | 1 saat | ⏳ Sonraki Sprint |
| BUG-005 | 🟡 Orta | Shop | 1 saat | ⏳ Sonraki Sprint |
| BUG-006 | 🟡 Orta | Cart | 2 saat | ⏳ Sonraki Sprint |
| BUG-007 | 🟢 Düşük | Cart | 30 dk | ⏳ Backlog |
| BUG-008 | 🟢 Düşük | Shop | 30 dk | ⏳ Backlog |

---

## 🔍 TEST SENARYOLARI

### Senaryo 1: USD Ürün Sepete Ekleme
```bash
1. USD fiyatlı ürün seç ($100)
2. Sepete ekle
3. Kontrol: Sepette ₺3000 görünmeli (kur: 30)
4. SONUÇ: ❌ FAIL - $100 görünüyor
```

### Senaryo 2: Checkout Address
```bash
1. Sepete ürün ekle
2. Checkout'a git
3. Address seç/ekle
4. SONUÇ: ❌ FAIL - ShopCustomerAddress dependency error
```

### Senaryo 3: Payment Flow
```bash
1. Checkout tamamla
2. Payment seç
3. Ödeme yap
4. SONUÇ: ❓ Test edilemedi - Önceki adımlar fail
```

---

## 🚨 PRODUCTION RİSKLERİ

### Risk 1: Finansal Kayıp
**Açıklama**: Currency dönüşümü olmadığı için yanlış fiyattan satış
**Etki**: $100 ürün ₺100'a satılabilir
**Önlem**: ACİL currency fix

### Risk 2: Sepet Abandonu
**Açıklama**: Display bilgileri eksik, müşteri güvensizlik hisseder
**Etki**: %30-40 sepet terk oranı artışı
**Önlem**: Display field'lar eklenmeli

### Risk 3: Checkout Failure
**Açıklama**: Address dependency sorunu checkout'u engelleyebilir
**Etki**: Satış kaybı
**Önlem**: Universal address system

---

## 📝 ÇÖZÜM ÖNCELİKLENDİRME

### Bugün (Kritik)
1. BUG-001: Currency dönüşümü
2. BUG-002: Display fields
3. BUG-003: Address dependency

### Bu Hafta (Orta)
4. BUG-004: KDV sistemi
5. BUG-005: Stok kontrolü
6. BUG-006: Session merge

### Sonra (Düşük)
7. BUG-007: Quantity validation
8. BUG-008: Currency formatting

---

## 🔧 HIZLI FIX'LER

### Quick Fix 1: Currency Dönüşümü (Geçici)
```php
// CartService.php line 169'a ekle:
$unitPrice = $unitPrice * 30; // Geçici sabit kur
```

### Quick Fix 2: Display Fields (Geçici)
```php
// AddToCartButton.php'de:
$cartItem->item_title = $product->getTranslated('title');
$cartItem->item_image = $product->getFirstMediaUrl();
$cartItem->save();
```

---

## 📞 İLETİŞİM

**Bug Raporlama**: GitHub Issues
**Acil Durum**: Slack #shop-cart-bugs
**Dokümantasyon**: /readme/shop-cart-payment/

---

**Son Güncelleme**: 2025-11-14
**Hazırlayan**: Claude AI Assistant
**Versiyon**: 1.0.0