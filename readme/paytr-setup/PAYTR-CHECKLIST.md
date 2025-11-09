# 🎯 PayTR Entegrasyon Checklist

**Hızlı Başlangıç Kılavuzu**

---

## 📋 HAZIRLIK AŞAMASI (PRE-INTEGRATION)

### ✅ Analiz Tamamlandı
- [x] Shop modülü yapısı incelendi
- [x] Mevcut ödeme altyapısı kontrol edildi
- [x] Database schema uygunluğu doğrulandı
- [x] Entegrasyon noktaları belirlendi

### 📊 Mevcut Altyapı Durumu
- [x] `shop_payment_methods` tablosu → **HAZIR**
- [x] `shop_payments` tablosu → **HAZIR**
- [x] `shop_orders` tablosu → **HAZIR**
- [x] Checkout flow (CheckoutPageNew) → **MODİFİKASYON GEREKLİ**

---

## 🛠️ ENTEGRASYON ADIMLARI

### 1️⃣ Ortam Ayarları
- [ ] PayTR hesabı oluştur (https://www.paytr.com)
- [ ] Test merchant credentials al
- [ ] `.env` dosyasına ekle:
  ```bash
  PAYTR_MERCHANT_ID=test_xxxxx
  PAYTR_MERCHANT_KEY=test_xxxxx
  PAYTR_MERCHANT_SALT=test_xxxxx
  PAYTR_MODE=test
  ```

### 2️⃣ Config Ayarları
- [ ] `config/shop.php` dosyasına PayTR config ekle
- [ ] Test/Live mode switcher ekle

### 3️⃣ Model Oluşturma
- [ ] `ShopPayment` model oluştur
  ```bash
  php artisan make:model Shop/ShopPayment
  ```
- [ ] `ShopPaymentMethod` model oluştur
  ```bash
  php artisan make:model Shop/ShopPaymentMethod
  ```

### 4️⃣ Service Oluşturma
- [ ] `PayTRService.php` oluştur (`Modules/Shop/app/Services/`)
- [ ] `createPaymentFrame()` metodu yaz
- [ ] `verifyCallback()` metodu yaz
- [ ] `handleCallback()` metodu yaz
- [ ] Hash algoritmasını implement et

### 5️⃣ Controller Oluşturma
- [ ] `PaymentController.php` oluştur
  ```bash
  php artisan make:controller Shop/PaymentController
  ```
- [ ] `frame()` metodu → PayTR iframe sayfası
- [ ] `callback()` metodu → PayTR IPN handler
- [ ] `success()` metodu → Başarılı ödeme redirect
- [ ] `failed()` metodu → Başarısız ödeme redirect

### 6️⃣ Route Tanımlamaları
- [ ] `routes/web.php` veya `Modules/Shop/routes/web.php` güncelle
- [ ] `shop.payment.frame` route ekle
- [ ] `shop.payment.callback` route ekle (CSRF exempt!)
- [ ] `shop.payment.success` route ekle
- [ ] `shop.payment.failed` route ekle

### 7️⃣ View Oluşturma
- [ ] `payment-frame.blade.php` oluştur
- [ ] PayTR iframe embed et
- [ ] Loading state ekle
- [ ] Responsive design kontrol et

### 8️⃣ CheckoutPageNew Güncelleme
- [ ] `submitOrder()` metodunu güncelle
- [ ] PayTR iframe oluşturma logic ekle
- [ ] `ShopPayment` create ekle (status: pending)
- [ ] Cart temizleme logic'i callback'e taşı
- [ ] Redirect → `shop.payment.frame`

### 9️⃣ Middleware Ayarları
- [ ] `VerifyCsrfToken.php` → `shop.payment.callback` exempt ekle
  ```php
  protected $except = [
      'shop/payment/callback',
  ];
  ```

### 🔟 Seed Data Ekleme
- [ ] PayTR payment method seed'i oluştur
- [ ] Test database'e ekle
- [ ] Canlı database'e ekle (production)

---

## 🧪 TEST AŞAMASI

### Unit Tests
- [ ] PayTRService hash üretimi test
- [ ] PayTRService callback verify test
- [ ] Amount validation test
- [ ] Duplicate payment engelleme test

### Integration Tests
- [ ] Checkout flow end-to-end test
- [ ] PayTR iframe loading test
- [ ] Callback handling test (success)
- [ ] Callback handling test (failed)

### Manual Tests
- [ ] Test kartı ile başarılı ödeme
  - Kart: 4355084355084358
  - CVV: 000
  - Tarih: 12/26
- [ ] Test kartı ile başarısız ödeme
  - Kart: 5406675406675403
- [ ] Timeout senaryosu (30 saniye bekle)
- [ ] Duplicate callback testi
- [ ] Geçersiz hash testi

### Database Tests
- [ ] `shop_orders.payment_status` güncelleniyor mu?
- [ ] `shop_payments.status` güncelleniyor mu?
- [ ] `shop_payments.gateway_response` kaydediliyor mu?
- [ ] `shop_carts` temizleniyor mu?

### Security Tests
- [ ] Hash doğrulama çalışıyor mu?
- [ ] Geçersiz hash reddediliyor mu?
- [ ] CSRF token bypass (callback route)
- [ ] SQL injection koruması
- [ ] XSS koruması

---

## 🚀 CANLI YAYINA ALMA

### Pre-Production
- [ ] Tüm testler başarılı
- [ ] Code review yapıldı
- [ ] Security audit yapıldı
- [ ] Performance test yapıldı

### Production Deployment
- [ ] `.env` dosyasına canlı credentials ekle
  ```bash
  PAYTR_MERCHANT_ID=live_xxxxx
  PAYTR_MERCHANT_KEY=live_xxxxx
  PAYTR_MERCHANT_SALT=live_xxxxx
  PAYTR_MODE=live
  ```
- [ ] Config cache temizle
  ```bash
  php artisan config:clear
  php artisan config:cache
  ```
- [ ] Route cache
  ```bash
  php artisan route:cache
  ```
- [ ] OPcache reset
  ```bash
  curl -k https://ixtif.com/opcache-reset.php
  ```

### Post-Deployment
- [ ] Canlıda test kartı ile deneme
- [ ] Gerçek kart ile test (küçük miktar)
- [ ] Log monitoring (1 saat)
- [ ] Error rate kontrol
- [ ] Payment success rate kontrol

---

## 📊 MONİTORİNG & LOGGING

### Log Points
- [ ] PayTR API request/response
- [ ] Callback istekleri (hash, status, amount)
- [ ] Payment status değişiklikleri
- [ ] Failed payment sebepleri

### Monitoring
- [ ] Payment success rate (target: >95%)
- [ ] Average payment time (target: <30s)
- [ ] Callback response time (target: <5s)
- [ ] Error rate (target: <5%)

---

## 🔧 SORUN GİDERME (TROUBLESHOOTING)

### Sık Karşılaşılan Sorunlar

**1. Hash Mismatch**
- [ ] merchant_salt doğru mu?
- [ ] Hash algoritması doğru mu? (HMAC SHA256)
- [ ] String concatenation sırası doğru mu?

**2. Callback Gelmiyor**
- [ ] URL doğru mu? (https://)
- [ ] CSRF exempt mi?
- [ ] Firewall engellemiyor mu?

**3. Payment Status Güncellenmiyor**
- [ ] Callback handler çalışıyor mu?
- [ ] Transaction rollback olmamış mı?
- [ ] Log'larda hata var mı?

**4. Duplicate Payment**
- [ ] Order ID unique kontrolü var mı?
- [ ] Payment status check var mı?
- [ ] Race condition koruması var mı?

---

## 📚 REFERANSLAR

- [Detaylı Entegrasyon Dökümanı](./PAYTR-ENTEGRASYON-HAZIRLIGI.md)
- [PayTR Resmi Döküman](https://www.paytr.com/entegrasyon/odeme-formu)
- [PayTR Dev Portal](https://dev.paytr.com/)

---

## 📞 DESTEK

**PayTR Destek:**
- Email: info@paytr.com
- Tel: 0850 305 0 305

**Internal:**
- Shop Module: `Modules/Shop/`
- Logs: `storage/logs/laravel.log`
- Database: `shop_*` tablolar

---

**Oluşturma Tarihi:** 2025-11-09
**Son Güncelleme:** 2025-11-09
**Versiyon:** 1.0
