# 📋 Shop-Cart-Payment Entegrasyon TODO Listesi

**Oluşturulma**: 2025-11-14
**Durum**: 🔴 Kritik - Acil müdahale gerekli

---

## 🔴 KRİTİK - BUGÜN YAPILMASI GEREKENLER

### 1. Currency Dönüşüm Sistemi (2-3 saat)
```bash
# Dosyalar:
Modules/Cart/app/Services/CurrencyConversionService.php  # OLUŞTUR
Modules/Cart/app/Services/CartService.php                # GÜNCELLE
```

- [ ] CurrencyConversionService oluştur
  - [ ] convertToTRY($amount, $fromCurrency) metodu
  - [ ] getRateFromCache() - Redis cache kullan
  - [ ] updateRatesFromAPI() - TCMB API entegrasyonu

- [ ] CartService::setPricing() güncelle
  - [ ] Currency kontrolü ekle
  - [ ] Dönüşüm uygula
  - [ ] Log ekle

- [ ] Test senaryoları
  - [ ] USD ürün ekle → TL fiyat kontrolü
  - [ ] EUR ürün ekle → TL fiyat kontrolü
  - [ ] TL ürün ekle → Dönüşüm olmamalı

---

### 2. CartItem Display Fields Migration (1 saat)
```bash
php artisan make:migration add_display_fields_to_cart_items
```

```php
// Migration içeriği:
$table->string('item_title')->nullable();
$table->string('item_image')->nullable();
$table->string('item_sku')->nullable();
$table->string('original_currency', 3)->default('TRY');
$table->decimal('original_price', 15, 2)->nullable();
$table->decimal('conversion_rate', 10, 4)->nullable();
```

- [ ] Migration oluştur
- [ ] Central ve Tenant migration'ları kopyala
- [ ] Migration çalıştır
- [ ] Model fillable güncelle

---

### 3. AddToCartButton Güncelleme (30 dk)
```php
// Modules/Shop/app/Http/Livewire/Front/AddToCartButton.php
// setPricing() çağrısına display field'ları ekle:

$options = [
    'item_title' => $product->getTranslated('title'),
    'item_image' => $product->getFirstMediaUrl('main'),
    'item_sku' => $product->sku,
    'original_currency' => $product->currency,
];
```

- [ ] Display field'ları doldur
- [ ] Currency bilgisini geç
- [ ] Test et

---

## 🟡 ÖNEMLİ - BU HAFTA

### 4. Shop-Cart Bridge Service (2 saat)
```bash
# Dosya:
Modules/Shop/app/Services/ShopCartBridgeService.php
```

- [ ] prepareProductForCart() metodu
- [ ] validateStock() metodu
- [ ] calculateTaxRate() metodu
- [ ] getProductDisplay() metodu

---

### 5. Universal Address System (3 saat)
```bash
# Dosyalar:
Modules/Cart/app/Contracts/AddressableInterface.php
Modules/Cart/app/Models/CartAddress.php
Modules/Cart/database/migrations/create_cart_addresses_table.php
```

- [ ] AddressableInterface tanımla
- [ ] CartAddress modeli oluştur
- [ ] Migration hazırla
- [ ] Shop modülünde implement et

---

### 6. Payment Integration Fix (2 saat)
```bash
# Dosyalar:
Modules/Payment/app/Services/UniversalPaymentService.php
Modules/Cart/app/Services/OrderCreationService.php
```

- [ ] Universal payment interface
- [ ] Order creation Cart'a taşı
- [ ] Payment callback'leri düzelt
- [ ] Test flow'u kur

---

## 🟢 NORMAL - SONRAKI SPRINT

### 7. Stok Kontrol Sistemi
- [ ] Real-time stok kontrolü
- [ ] Reserve stock on add to cart
- [ ] Release stock on timeout
- [ ] Low stock alerts

### 8. Session Management
- [ ] Guest cart → User cart merge
- [ ] Cart expiry (30 gün)
- [ ] Abandoned cart recovery
- [ ] Cart sharing (wishlist gibi)

### 9. Multi-Currency Support
- [ ] Sepette multi-currency
- [ ] User preferred currency
- [ ] Currency switcher widget
- [ ] Historical rate tracking

### 10. Advanced Tax System
- [ ] Product-based tax rates
- [ ] Location-based tax
- [ ] Tax exemption support
- [ ] B2B tax handling

---

## 🧪 TEST PLANI

### Unit Tests
```bash
# Test dosyaları oluştur:
tests/Unit/Cart/CurrencyConversionTest.php
tests/Unit/Cart/CartServiceTest.php
tests/Unit/Shop/ShopCartBridgeTest.php
```

### Integration Tests
```bash
tests/Feature/AddToCartFlowTest.php
tests/Feature/CheckoutFlowTest.php
tests/Feature/PaymentFlowTest.php
```

### Manual Test Checklist
- [ ] USD ürün ekle
- [ ] Sepeti görüntüle
- [ ] Miktar değiştir
- [ ] Checkout'a git
- [ ] Address ekle/seç
- [ ] Payment yap
- [ ] Order oluştur

---

## 📊 İLERLEME TAKİBİ

| Görev | Durum | Tahmini Süre | Gerçek Süre | Notlar |
|-------|-------|--------------|-------------|---------|
| Currency Service | ✅ Tamamlandı | 3 saat | 1 saat | Kritik - DONE |
| Display Fields | ✅ Tamamlandı | 1 saat | 30 dk | Kritik - DONE |
| Bridge Service | ✅ Tamamlandı | 2 saat | 45 dk | DONE |
| Address System | 🟡 Bekliyor | 3 saat | - | Sonraki sprint |
| Payment Fix | 🟡 Bekliyor | 2 saat | - | Sonraki sprint |

---

## 🚨 RİSKLER ve ENGELLEYENLER

### Riskler
1. **Currency API**: TCMB API yavaş olabilir → Cache zorunlu
2. **Migration**: Live sistem etkilenebilir → Maintenance mode gerekli
3. **Payment**: PayTR entegrasyonu test ortamı yok → Sandbox kurulumu gerekli

### Engelleyenler
1. Shop modülündeki legacy kod temizlenmeli
2. Cart modülü dokümantasyonu eksik
3. Payment gateway credentials eksik

---

## 📝 NOTLAR

### Dikkat Edilecekler
- Her değişiklik sonrası cache clear
- Migration'lar hem central hem tenant'ta
- Test ortamı: ixtif.com (Tenant ID: 2)
- Currency rate'ler günlük güncellenmeli

### Referans Dosyalar
```bash
# Önemli dosyalar:
Modules/Cart/app/Services/CartService.php
Modules/Shop/app/Http/Livewire/Front/AddToCartButton.php
Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php
Modules/Payment/app/Services/PayTRPaymentService.php
```

---

**Son Güncelleme**: 2025-11-14
**Hazırlayan**: Claude AI Assistant