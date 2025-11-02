# 🛒 GUEST CHECKOUT (ÜYE OLMADAN ALIŞVERİŞ) ANALİZ & PLAN

**Tarih:** 2025-11-02
**Tenant:** ixtif.com (ID: 2)
**Durum:** Mevcut sistem analiz edildi, iyileştirme planı hazırlandı

---

## 📊 MEVCUT DURUM ANALİZİ

### ✅ ŞU AN ÇALIŞAN SİSTEM

#### 1. **Sepet Sistemi** (`/shop/cart`)
**Dosya:** `Modules/Shop/app/Http/Livewire/Front/CartPage.php`

**Özellikler:**
- ✅ **Auth gerektirmiyor** - Herkes sepet görebilir
- ✅ **Session-based cart** - ShopCartService kullanıyor
- ✅ **Misafir ekleme yapabiliyor** - Ürünleri sepete ekleyebilir
- ✅ **Miktar değiştirme** - Artır/azalt/sil yapılabiliyor
- ✅ **Fiyat hesaplama** - TRY'ye çevirme + KDV (%20)
- ✅ **Currency dönüşümü** - USD → TRY otomatik
- ✅ **WhatsApp butonu** - "Sepet hakkında soru sormak istiyorum"
- ✅ **KVKK/GDPR banner** - Gizlilik politikası bildirim var

**İşleyiş:**
1. Misafir kullanıcı ürün ekler → Session'da `cart_id` oluşur
2. Sepet sayfası açılır → Tüm ürünler listelenir
3. "Sipariş Ver" butonuna basar → `/shop/checkout` sayfasına gider

**Cart Sayfası Kontrol Noktaları:**
- ❌ **Auth kontrolü YOK** (herkes girebilir)
- ✅ **Sepet boşsa** → "Sepetiniz Boş" mesajı gösterir
- ✅ **KVKK aydınlatma** → Gizlilik politikası linki var

---

#### 2. **Checkout Sistemi** (`/shop/checkout`)
**Dosya:** `Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php`

**Özellikler:**
- ✅ **Misafir checkout destekli!** - `session('guest_customer_id')` var
- ✅ **Login kullanıcı destekli** - `Auth::check()` varsa user bilgileri gelir
- ✅ **Dinamik müşteri oluşturma** - İlk siparişte customer oluşur
- ✅ **Guest müşteri session'da** - `guest_customer_id` kaydediliyor
- ✅ **İletişim form** - Ad/Soyad/Telefon/Email
- ✅ **Fatura tipi** - Bireysel (TCKN opsiyonel) / Kurumsal (VKN + Firma zorunlu)
- ✅ **Adres sistemi** - Teslimat + Fatura adresi modal
- ✅ **Sözleşme checkbox** - KVKK + Mesafeli Satış + Ön Bilgilendirme (tek checkbox)
- ✅ **Kredi kartı komisyonu** - %4.99 otomatik ekleniyor
- ✅ **Sipariş oluşturma** - `ShopOrder` + `ShopOrderItem` kaydediliyor
- ✅ **Sepet temizleme** - Sipariş sonrası sepet otomatik boşalıyor

**Guest Checkout Akışı:**
1. Kullanıcı `/shop/checkout` açar
2. `Auth::check() == false` → Guest mode
3. Session'da `guest_customer_id` varsa → Müşteri bilgileri yüklensin
4. Session'da customer yoksa → Form boş, ilk siparişte oluşturulacak
5. Kullanıcı formu doldurur (Ad/Soyad/Tel/Email + Adresler)
6. "Sipariş Ver" → `submitOrder()` tetiklenir
7. **Customer oluşturulur:** `ShopCustomer::create()` → `session(['guest_customer_id' => ...])` kaydedilir
8. **Order oluşturulur:** `ShopOrder::create()` + `ShopOrderItem::create()`
9. Sepet temizlenir → Başarı mesajı → `/shop/index` redirect

**Kontrol Noktaları:**
- ✅ **Sepet boşsa** → `/shop/cart` redirect (mount() metodunda)
- ✅ **Guest müşteri ilk siparişte oluşur** - `createOrUpdateCustomer()`
- ✅ **Session'da müşteri saklanır** - `session(['guest_customer_id' => ...])`
- ✅ **Validation var** - Ad/Soyad/Tel/Adres/Sözleşme zorunlu
- ✅ **DB transaction** - Sipariş + Kalemler atomic olarak kaydediliyor

---

## 🎯 MEVCUT SİSTEMİN DURUMU

### ✅ **GUEST CHECKOUT ZATEN ÇALIŞIYOR!**

**Misafir kullanıcı şu anda:**
1. ✅ Ürün ekleyebiliyor (sepete atma)
2. ✅ Sepet sayfasını görebiliyor (`/shop/cart`)
3. ✅ Checkout sayfasını açabiliyor (`/shop/checkout`)
4. ✅ Form doldurarak sipariş verebiliyor
5. ✅ Customer otomatik oluşuyor (ilk siparişte)
6. ✅ Session'da `guest_customer_id` saklanıyor (sonraki siparişler için)
7. ✅ Order kaydediliyor (sipariş numarası oluşuyor)

---

## ⚠️ TESPİT EDİLEN SORUNLAR VE EKSİKLER

### 1. **CHECKOUT SAYFASI PLACEHOLDER DEĞİL Mİ?**
**Sorun:** `checkout-simple.blade.php` içinde "Yapım Aşamasında" mesajı görünüyor!

**Analiz:**
- ❌ `/shop/checkout` route'u **CheckoutPageNew** component'ini çağırıyor
- ❌ Ama eski `checkout-simple.blade.php` hala repository'de duruyor (kullanılmıyor mu?)
- ✅ Gerçek checkout sayfası: `checkout-page-new.blade.php` (CheckoutPageNew component)

**Doğrulama Gerekli:**
```bash
# Hangi view kullanılıyor?
curl -I https://ixtif.com/shop/checkout
# → Livewire CheckoutPageNew render olmalı
```

### 2. **Guest Customer Sonraki Siparişlerde Hatırlayamayabilir**
**Sorun:** Session temizlenirse, guest müşteri kayboluyor

**Mevcut Durum:**
- ✅ Session'da `guest_customer_id` var
- ❌ Cookie veya uzun süreli session yok
- ❌ Email ile guest customer bulma sistemi yok

**Senaryolar:**
- **Senaryo 1:** Misafir sipariş veriyor → Session'da `guest_customer_id` saklanıyor → Tekrar geldiğinde oturum varsa hatırlanıyor ✅
- **Senaryo 2:** Misafir sipariş veriyor → Tarayıcıyı kapatıyor → Tekrar geldiğinde session silinmiş → **Bilgileri KAYBOLMUŞ** ❌

**Çözüm:** Cookie veya email-based customer bulma eklenebilir.

---

### 3. **Ödeme Entegrasyonu Eksik**
**Sorun:** Sipariş oluşuyor ama ödeme alınmıyor!

**Mevcut Durum:**
- ✅ Sipariş `pending` statüsünde kaydediliyor
- ❌ Kredi kartı ödeme entegrasyonu YOK
- ❌ Kullanıcı ödeme sayfasına yönlendirilmiyor
- ❌ Sipariş sonrası "Ödeme Bekleniyor" sayfası yok

**Eksik:**
- **iyzico / PayTR / Stripe** entegrasyonu yok
- Sipariş oluşturulduktan sonra **ödeme gateway'ine redirect** yok
- **Callback/Webhook** sistemi yok (ödeme onaylandığında order güncelleme)

---

### 4. **Sipariş Onay Sayfası Yok**
**Sorun:** Sipariş verildiğinde doğru bilgilendirme yapılmıyor

**Mevcut Durum:**
- ✅ Flash message: "Siparişiniz başarıyla alındı! Sipariş numaranız: ORD-XXXXX"
- ❌ Sipariş onay sayfası yok (`/shop/order/success`)
- ❌ Sipariş detay sayfası yok (`/shop/order/{order_number}`)
- ❌ Email onayı gönderilmiyor (sipariş onay email'i)
- ❌ Admin panel bildirimi yok (yeni sipariş geldi)

**Eksik:**
- **Sipariş onay sayfası** - Sipariş özeti + Ödeme bilgisi + İletişim
- **Email onayı** - Müşteriye otomatik email gönderme
- **Admin bildirimi** - Yeni sipariş geldiğinde admin'e bildirim

---

### 5. **GDPR/KVKK Eksikleri**
**Sorun:** Sepet sayfasında KVKK var, checkout'ta net değil

**Mevcut Durum:**
- ✅ Cart sayfasında: KVKK banner var (Gizlilik Politikası + KVKK Aydınlatma linki)
- ✅ Checkout'ta: Single checkbox var (`agree_all` - KVKK + Mesafeli Satış + Ön Bilgilendirme)
- ❌ Checkout'ta KVKK metni detaylı gösterilmiyor
- ❌ Kullanıcı sözleşmeleri **inline** olarak okuyamıyor

**İyileştirme:**
- Sözleşme checkbox'ının yanına **modal** ekle ("Sözleşmeyi Oku")
- Kullanıcı checkbox'a basmadan önce metni görebilsin

---

### 6. **Guest Kullanıcı Sipariş Takibi Yapamıyor**
**Sorun:** Misafir sipariş verdikten sonra takip edemiyor

**Eksik:**
- ❌ "Siparişimi Takip Et" sayfası yok
- ❌ Email + Sipariş numarası ile sorgulama sistemi yok
- ❌ Guest kullanıcı login olmadan sipariş durumunu göremez

**Çözüm:**
- `/shop/order/track` sayfası ekle
- Form: Email + Order Number → Sipariş detayını göster

---

### 7. **Adres Sistemi Eksik (Guest İçin)**
**Sorun:** Guest kullanıcı için adres formu tam değil

**Mevcut Durum:**
- ✅ Login kullanıcı: `ShopCustomerAddress` modelinden adres seçiyor
- ❌ **Guest kullanıcı: Adres formu YOK!**
- ❌ Guest kullanıcı modal açtığında ne olacak?

**Analiz:**
```php
// CheckoutPageNew.php - Line 215
public function loadDefaultAddresses()
{
    if (!$this->customerId) {
        return; // Guest için adres yüklenmiyor!
    }
    ...
}
```

**Sorun:** Guest kullanıcının `customer_id` yok → Adres modal boş!

**Çözüm:**
- Guest kullanıcı için **inline adres formu** ekle
- Modal yerine direkt checkout sayfasında form göster
- İlk siparişte adres de kaydedilsin (`ShopCustomerAddress::create()`)

---

## 🚀 İYİLEŞTİRME PLANI

### 🎯 PHASE 1: MEVCUT SİSTEMİ ÇALIŞTIR (Öncelik: YÜK

**Hedef:** Sistemin çalışır hale gelmesini sağla, basit ödeme ekle

#### 1.1. **Checkout Sayfası Doğrulama**
- [ ] `/shop/checkout` açıldığında hangi view render oluyor? (Placeholder mı, yoksa CheckoutPageNew mi?)
- [ ] `checkout-simple.blade.php` kullanılıyor mu? (Kullanılıyorsa sil veya arşivle)
- [ ] CheckoutPageNew component'i düzgün çalışıyor mu?

#### 1.2. **Guest Adres Formu Ekle**
- [ ] Guest kullanıcı için **inline adres formu** ekle (modal yerine)
- [ ] Teslimat adresi: `address_line_1`, `city`, `district`, `postal_code`, `delivery_notes`
- [ ] Fatura adresi: "Fatura = Teslimat" checkbox var, ayrıca fatura adresi formu gösterme (opsiyonel)
- [ ] İlk siparişte adres `ShopCustomerAddress::create()` ile kaydet

**Kod Değişikliği:**
```php
// CheckoutPageNew.php - submitOrder() içinde
if (!$this->customerId) {
    // Guest için adres oluştur
    $shippingAddress = ShopCustomerAddress::create([
        'customer_id' => $customer->customer_id,
        'address_type' => 'shipping',
        'address_line_1' => $this->shipping_address_line_1,
        'city' => $this->shipping_city,
        // ...
    ]);
}
```

#### 1.3. **Basit Ödeme Sistemi (Manual Ödeme)**
- [ ] Sipariş oluştuktan sonra `/shop/order/success/{order_number}` sayfasına yönlendir
- [ ] Sayfa içeriği:
  - Sipariş numarası
  - Sipariş özeti (ürünler + toplam)
  - Banka hesap bilgileri (havale/EFT için)
  - "Ödemeyi yaptıktan sonra sipariş numaranızla birlikte WhatsApp'tan bilgi verin"
  - WhatsApp butonu

**Yeni Route:**
```php
Route::get('/shop/order/success/{order_number}', [OrderController::class, 'success'])->name('shop.order.success');
```

#### 1.4. **Email Onay Sistemi (Laravel Mail)**
- [ ] Sipariş oluşturulduğunda müşteriye email gönder
- [ ] Email içeriği:
  - Sipariş numarası
  - Sipariş özeti
  - Banka bilgileri (havale/EFT)
  - İletişim bilgileri
- [ ] `php artisan make:mail OrderConfirmationMail`

---

### 🎯 PHASE 2: GELİŞMİŞ ÖDEMELER (iyzico/PayTR)

**Hedef:** Kredi kartı ile online ödeme

#### 2.1. **iyzico Entegrasyonu**
- [ ] Composer: `composer require iyzico/iyzipay-php`
- [ ] Config: `config/iyzico.php` ekle (API key, secret key, sandbox mode)
- [ ] Ödeme servisi: `app/Services/IyzicoPaymentService.php`
- [ ] Sipariş oluştuktan sonra iyzico checkout başlat
- [ ] Callback route: `/shop/payment/callback` (iyzico webhook)
- [ ] Ödeme başarılı → Order status: `paid`
- [ ] Ödeme başarısız → Order status: `payment_failed`

#### 2.2. **Ödeme Durumu Sayfası**
- [ ] `/shop/payment/pending/{order_number}` - Ödeme bekleniyor
- [ ] `/shop/payment/success/{order_number}` - Ödeme başarılı
- [ ] `/shop/payment/failed/{order_number}` - Ödeme başarısız

---

### 🎯 PHASE 3: KULLANICI DENEYİMİ İYİLEŞTİRMELERİ

#### 3.1. **Guest Sipariş Takip Sistemi**
- [ ] Route: `/shop/order/track`
- [ ] Form: Email + Order Number
- [ ] Sipariş detay sayfası göster (guest için auth gerektirmeyen)

#### 3.2. **Guest Session Hatırlama (Cookie-based)**
- [ ] Guest müşteri oluşturulduğunda cookie kaydet
- [ ] Cookie: `guest_customer_token` (random hash)
- [ ] Database: `shop_customers.guest_token` field ekle
- [ ] Tekrar geldiğinde cookie'den müşteri bul

#### 3.3. **Admin Sipariş Yönetimi**
- [ ] Admin panelde sipariş listesi (`/admin/shop/orders`)
- [ ] Sipariş detay sayfası
- [ ] Status güncelleme (pending → processing → shipped → delivered)
- [ ] Email bildirimleri (status değişince müşteriye email)

---

## 📋 ACİL YAPILACAKLAR (PHASE 1)

### ✅ **İlk 5 Adım (Öncelikli)**

1. **Checkout sayfasını test et**
   - `/shop/checkout` aç → CheckoutPageNew render oluyor mu?
   - Guest kullanıcı formu görebiliyor mu?

2. **Guest adres formunu ekle**
   - Modal yerine inline form
   - Teslimat adresi: 5 field (address_line_1, city, district, postal_code, delivery_notes)
   - Fatura adresi: Checkbox ("Fatura = Teslimat")

3. **Sipariş onay sayfası oluştur**
   - `/shop/order/success/{order_number}`
   - Sipariş özeti + Banka bilgileri + WhatsApp butonu

4. **Email onay sistemi**
   - `OrderConfirmationMail` oluştur
   - Sipariş verildiğinde email gönder

5. **Test et**
   - Misafir kullanıcı ile sepet → checkout → sipariş
   - Email geldi mi?
   - Sipariş veritabanına kaydedildi mi?

---

## 🔍 TEST SENARYOLARI

### Senaryo 1: Guest Kullanıcı İlk Sipariş
1. Misafir kullanıcı ürün ekler → Sepete gider
2. "Sipariş Ver" → Checkout sayfası açılır
3. İletişim bilgilerini doldurur (Ad/Soyad/Tel/Email)
4. Teslimat adresini doldurur (inline form)
5. Fatura bilgilerini doldurur (Bireysel/Kurumsal)
6. Sözleşmeyi kabul eder (single checkbox)
7. "Sipariş Ver" → Sipariş oluşur
8. **Beklenen:**
   - ✅ `ShopCustomer` kaydedilir
   - ✅ `session('guest_customer_id')` set edilir
   - ✅ `ShopCustomerAddress` kaydedilir (teslimat + fatura)
   - ✅ `ShopOrder` + `ShopOrderItem` kaydedilir
   - ✅ Email gönderilir
   - ✅ `/shop/order/success/ORD-XXX` redirect olur

### Senaryo 2: Guest Kullanıcı İkinci Sipariş (Session Canlı)
1. Misafir kullanıcı tekrar ürün ekler
2. Checkout açılır → **Bilgileri otomatik doldu** (session'dan)
3. Adresleri otomatik yüklendi (customer_id var)
4. Sipariş verir → Yeni order oluşur

### Senaryo 3: Guest Kullanıcı Session Kaybetti
1. Misafir kullanıcı session kaybetti (tarayıcı kapandı)
2. Checkout açılır → Bilgiler boş (customer_id yok)
3. **Sorun:** Önceki adresleri göremez

**Çözüm (Phase 3):** Cookie-based guest token sistemi

---

## 📂 DOSYA YAPISI

### Değiştirilecek Dosyalar
- `Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php` - Guest adres formu ekle
- `Modules/Shop/resources/views/livewire/front/checkout-page-new.blade.php` - Inline adres formu UI

### Oluşturulacak Dosyalar
- `Modules/Shop/app/Http/Controllers/Front/OrderController.php` - Sipariş onay sayfası
- `Modules/Shop/resources/views/front/order-success.blade.php` - Sipariş onay view
- `app/Mail/OrderConfirmationMail.php` - Email template
- `resources/views/emails/orders/confirmation.blade.php` - Email HTML

### Route Değişiklikleri
- `routes/web.php` ekle:
  ```php
  Route::get('/shop/order/success/{order_number}', [OrderController::class, 'success'])->name('shop.order.success');
  ```

---

## ⚠️ NOTLAR

### Veritabanı
- ✅ `shop_customers` tablosu var (guest + user ikisi de destekleniyor)
- ✅ `shop_customer_addresses` tablosu var
- ✅ `shop_orders` tablosu var
- ✅ `shop_order_items` tablosu var
- ❓ Migration kontrol et (tablo şemaları doğru mu?)

### Kritik Kontroller
- [ ] `shop_orders` tablosunda `order_number` unique mi?
- [ ] `shop_customers` tablosunda `email` unique değil (guest + user aynı email kullanabilir)
- [ ] `session('guest_customer_id')` hangi session driver kullanıyor? (`file` / `redis` / `database`)
- [ ] Email gönderimi için `.env` SMTP config var mı?

---

## 🎯 ÖNERİLEN PLAN

### Hemen Yapılacak (1-2 saat)
1. Checkout sayfasını test et (canlı sitede aç, çalışıyor mu?)
2. Guest adres formunu ekle (inline form, modal kaldır)
3. Sipariş onay sayfası oluştur (`/shop/order/success`)

### Kısa Vadede (1-2 gün)
4. Email onay sistemi (OrderConfirmationMail)
5. Admin panel sipariş listesi (görüntüleme + status güncelleme)

### Orta Vadede (1 hafta)
6. iyzico/PayTR entegrasyonu (kredi kartı ödeme)
7. Guest sipariş takip sistemi (`/shop/order/track`)

### Uzun Vadede (2+ hafta)
8. Cookie-based guest müşteri hatırlama
9. Gelişmiş admin sipariş yönetimi (kargo entegrasyonu, fatura)
10. Email otomasyonları (sipariş durumu değişince bildirim)

---

## 📊 ÖNCELİK SIRASI

| # | Görev | Öncelik | Süre | Bağımlılık |
|---|-------|---------|------|------------|
| 1 | Checkout test | 🔴 Kritik | 15dk | - |
| 2 | Guest adres formu | 🔴 Kritik | 1h | Checkout test |
| 3 | Sipariş onay sayfası | 🔴 Kritik | 30dk | - |
| 4 | Email onay | 🟠 Yüksek | 1h | SMTP config |
| 5 | Admin sipariş listesi | 🟠 Yüksek | 2h | - |
| 6 | Ödeme entegrasyonu | 🟡 Orta | 4h | iyzico API key |
| 7 | Guest sipariş takip | 🟡 Orta | 1h | - |
| 8 | Cookie-based guest | 🟢 Düşük | 2h | - |

---

## 🎤 KULLANICI SORULARI

**Kullanıcıya soralım:**

1. **Checkout sayfası şu anda çalışıyor mu?**
   - `/shop/checkout` açıldığında ne görünüyor?
   - "Yapım aşamasında" mı, yoksa form mu?

2. **Ödeme sistemi ne olsun?**
   - İlk etapta **manuel ödeme** (banka havalesi) yeter mi?
   - Yoksa hemen **kredi kartı entegrasyonu** (iyzico/PayTR) gerekli mi?

3. **Email gönderimi aktif mi?**
   - `.env` dosyasında SMTP ayarları var mı?
   - Sipariş onay email'i gönderelim mi?

4. **Admin panelde sipariş yönetimi var mı?**
   - `/admin/shop/orders` gibi bir sayfa var mı?
   - Yoksa oluşturalım mı?

5. **Guest müşteri session kaybederse ne olsun?**
   - Cookie-based hatırlama sistemine ihtiyaç var mı?
   - Yoksa her seferinde bilgileri yeniden mi girsin?

---

## ✅ SONUÇ

**Mevcut durum:** Guest checkout sistemi **%80 hazır**, sadece birkaç eksik var!

**İhtiyaç duyulan:**
1. Guest adres formu (inline)
2. Sipariş onay sayfası
3. Email onay sistemi
4. Ödeme entegrasyonu (manuel veya otomatik)

**Tahmini süre:** 3-4 saat (Phase 1 için)

---

**📌 Sonraki adım:** Kullanıcıya yukarıdaki soruları sor, öncelikleri netleştir, sonra kodlamaya başla!
