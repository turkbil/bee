# 🔐 PayTR Ödeme Sistemi Entegrasyon Hazırlığı

**Tarih:** 2025-11-09
**Tenant:** ixtif.com (Tenant ID: 2)
**Modül:** Shop
**Durum:** Hazırlık Aşaması

---

## 📊 MEVCUT SİSTEM ANALİZİ

### ✅ Mevcut Veritabanı Yapısı

Shop modülü **zaten PayTR desteği için hazır altyapıya sahip**:

#### 1. **shop_payment_methods** Tablosu
- ✅ `gateway_name` kolonu var (paytr, stripe, iyzico)
- ✅ `gateway_config` (JSON) kolonu var (merchant_id, merchant_key, merchant_salt)
- ✅ `gateway_mode` kolonu var (test, live)
- ✅ `payment_type` enum kolonu var (credit_card, debit_card vb.)
- ✅ Taksit desteği altyapısı var
- ✅ Komisyon (fixed_fee, percentage_fee) kolonları var

#### 2. **shop_payments** Tablosu
- ✅ `gateway_name` kolonu var (paytr)
- ✅ `gateway_transaction_id` kolonu var (PayTR merchant_oid)
- ✅ `gateway_payment_id` kolonu var (PayTR payment token)
- ✅ `gateway_response` (JSON) kolonu var (tüm PayTR response)
- ✅ `status` enum kolonu var (pending, processing, completed, failed, cancelled, refunded)
- ✅ Kart bilgileri (masked) kolonları var
- ✅ Taksit bilgileri kolonları var

#### 3. **shop_orders** Tablosu
- ✅ `payment_status` enum kolonu var (pending, paid, refunded, failed)
- ✅ `payment_method_id` foreign key var
- ✅ IP & User Agent kolonları var
- ✅ Müşteri snapshot bilgileri var

---

## 🎯 PAYTR ENTEGRASYON NOKTALARI

### 📍 1. Checkout Flow (CheckoutPageNew.php)

**Mevcut Akış:**
```php
CheckoutPageNew::submitOrder()
  ↓
1. Validation (adres, iletişim, fatura bilgileri)
2. ShopCustomer create/update
3. ShopOrder create (status: pending, payment_status: pending)
4. ShopOrderItem'ler create
5. Cart temizle
6. Redirect → shop.order.success
```

**PayTR Entegrasyonu Sonrası:**
```php
CheckoutPageNew::submitOrder()
  ↓
1. Validation
2. ShopCustomer create/update
3. ShopOrder create (status: pending, payment_status: pending)
4. ShopOrderItem'ler create
5. ⭐ PayTR iframe oluştur (PayTRService::createPaymentFrame())
6. ⭐ ShopPayment create (status: pending, gateway_name: paytr)
7. ⭐ Redirect → PayTR iframe sayfası
8. Kullanıcı ödeme yapar
9. PayTR callback → shop.payment.callback
10. Callback success → Order status update → Redirect shop.order.success
11. Callback failed → Redirect shop.payment.failed
```

---

### 📍 2. Gerekli Yeni Dosyalar/Servisler

#### **A. PayTR Servisi**
```
Modules/Shop/app/Services/PayTRService.php
```
**Görevler:**
- `createPaymentFrame()` - İframe oluşturma
- `verifyCallback()` - Callback doğrulama
- `parseResponse()` - Yanıt parse
- `getPaymentStatus()` - Ödeme durumu sorgulama
- `refundPayment()` - İade işlemi

#### **B. Payment Controller**
```
Modules/Shop/app/Http/Controllers/Front/PaymentController.php
```
**Route'lar:**
- `GET /shop/payment/frame/{order}` - PayTR iframe sayfası
- `POST /shop/payment/callback` - PayTR callback (IPN)
- `GET /shop/payment/success` - Başarılı ödeme redirect
- `GET /shop/payment/failed` - Başarısız ödeme redirect

#### **C. Payment Model (Opsiyonel)**
```
Modules/Shop/app/Models/ShopPayment.php
Modules/Shop/app/Models/ShopPaymentMethod.php
```
**Not:** Tablo zaten var, model oluşturmak yeterli.

#### **D. Livewire Component (PayTR Iframe)**
```
Modules/Shop/app/Http/Livewire/Front/PaymentFrame.php
Modules/Shop/resources/views/livewire/front/payment-frame.blade.php
```
**Görev:** PayTR iframe'ini güvenli şekilde göstermek.

---

### 📍 3. Config/Env Ayarları

**.env Eklemeleri:**
```bash
# PayTR Credentials
PAYTR_MERCHANT_ID=your_merchant_id
PAYTR_MERCHANT_KEY=your_merchant_key
PAYTR_MERCHANT_SALT=your_merchant_salt
PAYTR_MODE=test  # test veya live
PAYTR_TIMEOUT=30

# PayTR URLs
PAYTR_API_URL=https://www.paytr.com/odeme/api/get-token
PAYTR_IFRAME_URL=https://www.paytr.com/odeme/guvenli
```

**config/shop.php Eklemeleri:**
```php
'payment' => [
    'default_gateway' => env('PAYMENT_GATEWAY', 'paytr'),
    'paytr' => [
        'merchant_id' => env('PAYTR_MERCHANT_ID'),
        'merchant_key' => env('PAYTR_MERCHANT_KEY'),
        'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
        'mode' => env('PAYTR_MODE', 'test'),
        'timeout' => env('PAYTR_TIMEOUT', 30),
        'api_url' => env('PAYTR_API_URL'),
        'iframe_url' => env('PAYTR_IFRAME_URL'),
        'max_installment' => 12,
        'no_installment' => false, // Taksit kapalı mı?
    ],
],
```

---

## 🏗️ DETAYLI ENTEGRASYON ADIMLARI

### Adım 1: Model'leri Oluştur
```bash
# ShopPayment model
php artisan make:model Shop/ShopPayment
# ShopPaymentMethod model
php artisan make:model Shop/ShopPaymentMethod
```

### Adım 2: PayTR Service
```bash
# Service oluştur
touch Modules/Shop/app/Services/PayTRService.php
```

**PayTRService Yapısı:**
```php
class PayTRService {
    public function createPaymentFrame(ShopOrder $order, array $customerData): array
    {
        // 1. PayTR token oluştur
        // 2. Hash üret (HMAC)
        // 3. API'ye token request at
        // 4. Iframe URL döndür
    }

    public function verifyCallback(Request $request): bool
    {
        // 1. Gelen hash'i doğrula
        // 2. merchant_oid kontrol et
        // 3. status kontrol et (success/failed)
    }

    public function handleCallback(Request $request): ShopPayment
    {
        // 1. Callback verify
        // 2. ShopPayment güncelle (status, gateway_response)
        // 3. ShopOrder güncelle (payment_status, status)
        // 4. OK yanıtı döndür
    }
}
```

### Adım 3: Payment Controller
```bash
php artisan make:controller Shop/PaymentController
```

**PaymentController Yapısı:**
```php
class PaymentController extends Controller {
    public function frame($orderNumber)
    {
        // Order bul
        // PayTR iframe oluştur
        // View'e iframe URL'i gönder
    }

    public function callback(Request $request)
    {
        // PayTR callback'i işle
        // Order güncelle
        // OK/FAIL döndür
    }

    public function success(Request $request)
    {
        // Başarılı ödeme redirect
        // Session flash message
        // Redirect → shop.order.success
    }

    public function failed(Request $request)
    {
        // Başarısız ödeme redirect
        // Error message
        // Redirect → shop.checkout (tekrar dene)
    }
}
```

### Adım 4: Route Eklemeleri
**routes/web.php veya Modules/Shop/routes/web.php**
```php
// PayTR Payment Routes
Route::middleware(['tenant'])->prefix('shop/payment')->group(function () {
    Route::get('/frame/{order_number}', [PaymentController::class, 'frame'])
        ->name('shop.payment.frame');

    Route::post('/callback', [PaymentController::class, 'callback'])
        ->name('shop.payment.callback');

    Route::get('/success', [PaymentController::class, 'success'])
        ->name('shop.payment.success');

    Route::get('/failed', [PaymentController::class, 'failed'])
        ->name('shop.payment.failed');
});
```

### Adım 5: CheckoutPageNew Güncellemesi
**Modules/Shop/app/Http/Livewire/Front/CheckoutPageNew.php**

**submitOrder() metodunu güncelle:**
```php
public function submitOrder()
{
    // ... mevcut validation ...

    DB::beginTransaction();

    try {
        // Customer oluştur
        $customer = $this->createOrUpdateCustomer();

        // Order oluştur
        $order = ShopOrder::create([
            // ... mevcut alan'lar ...
            'payment_status' => 'pending', // ⭐ Ödeme bekleniyor
        ]);

        // Order items oluştur
        // ...

        // ⭐ PayTR ödeme başlat
        $paytrService = app(PayTRService::class);

        $paymentData = [
            'customer_name' => $customer->full_name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_address' => $shippingAddress->address_line_1,
            'merchant_oid' => $order->order_number, // Benzersiz sipariş no
            'payment_amount' => $this->grandTotal * 100, // Kuruş cinsinden
            'currency' => 'TRY',
            'test_mode' => config('shop.payment.paytr.mode') === 'test' ? 1 : 0,
            'no_installment' => 0, // Taksit açık
            'max_installment' => 12,
            'user_basket' => json_encode($this->getBasketItems()),
        ];

        $paymentFrame = $paytrService->createPaymentFrame($order, $paymentData);

        if (!$paymentFrame['success']) {
            throw new \Exception('PayTR iframe oluşturulamadı: ' . $paymentFrame['error']);
        }

        // ShopPayment kayıt oluştur
        ShopPayment::create([
            'order_id' => $order->order_id,
            'payment_method_id' => null, // PayTR payment method ID buraya
            'payment_number' => 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'amount' => $this->grandTotal,
            'currency' => 'TRY',
            'status' => 'pending',
            'gateway_name' => 'paytr',
            'gateway_payment_id' => $paymentFrame['token'],
            'gateway_response' => json_encode($paymentFrame),
        ]);

        // Sepeti temizleme (callback'te yapılacak, şimdilik beklemeye al)
        // $cartService->clearCart();

        DB::commit();

        // ⭐ PayTR iframe sayfasına yönlendir
        return redirect()->route('shop.payment.frame', $order->order_number);

    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'Ödeme başlatılırken hata: ' . $e->getMessage());
    }
}
```

### Adım 6: View Oluştur (Payment Frame)
**Modules/Shop/resources/views/front/payment-frame.blade.php**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>Güvenli Ödeme - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div style="text-align: center; padding: 20px;">
        <h2>Ödeme sayfasına yönlendiriliyorsunuz...</h2>
        <p>Güvenli ödeme ekranı yükleniyor.</p>
    </div>

    <!-- PayTR iFrame -->
    <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
    <iframe
        src="{{ $iframeUrl }}"
        id="paytriframe"
        frameborder="0"
        scrolling="no"
        style="width: 100%;">
    </iframe>

    <script>
        iFrameResize({}, '#paytriframe');
    </script>
</body>
</html>
```

---

## 🔒 GÜVENLİK KONTROL LİSTESİ

### ✅ PayTR Callback Güvenliği
- [ ] **Hash Doğrulama:** merchant_salt + merchant_oid + status + total_amount
- [ ] **IP Whitelist:** PayTR IP'lerinden gelen istekleri kontrol et
- [ ] **Double-spend Prevention:** Aynı order_id için birden fazla ödeme engelle
- [ ] **CSRF Protection:** Callback route'u `csrf` middleware'den muaf tut
- [ ] **Log Everything:** Tüm callback isteklerini logla

### ✅ Veritabanı Güvenliği
- [ ] **Transaction Kullan:** DB::beginTransaction() + commit/rollback
- [ ] **Status Kontrolü:** Ödeme zaten completed ise tekrar işleme
- [ ] **Amount Validation:** Gelen tutar ile order tutarı eşleşiyor mu?

### ✅ Test Senaryoları
- [ ] Başarılı ödeme (test kartı: 4355084355084358)
- [ ] Başarısız ödeme (yetersiz bakiye)
- [ ] Timeout (30 saniye)
- [ ] Duplicate callback (aynı ödeme 2 kez)
- [ ] Geçersiz hash (güvenlik testi)

---

## 📋 VERİTABANI SEED VERİLERİ

**PayTR Payment Method Örneği:**
```php
DB::table('shop_payment_methods')->insert([
    'title' => json_encode(['tr' => 'Kredi Kartı (PayTR)', 'en' => 'Credit Card (PayTR)']),
    'slug' => 'paytr-credit-card',
    'payment_type' => 'credit_card',
    'gateway_name' => 'paytr',
    'gateway_mode' => 'test', // veya 'live'
    'gateway_config' => json_encode([
        'merchant_id' => config('shop.payment.paytr.merchant_id'),
        'merchant_key' => config('shop.payment.paytr.merchant_key'),
        'merchant_salt' => config('shop.payment.paytr.merchant_salt'),
    ]),
    'supports_installment' => true,
    'max_installments' => 12,
    'supported_currencies' => json_encode(['TRY']),
    'is_active' => true,
    'percentage_fee' => 4.99, // Komisyon %4.99
    'sort_order' => 1,
]);
```

---

## 🧪 TEST ORTAMI AYARLARI

### PayTR Test Credentials
```bash
# .env.testing
PAYTR_MERCHANT_ID=test_merchant_id
PAYTR_MERCHANT_KEY=test_merchant_key
PAYTR_MERCHANT_SALT=test_merchant_salt
PAYTR_MODE=test
```

### PayTR Test Kartları
```
Başarılı Ödeme:
- Kart No: 4355084355084358
- Son Kullanma: 12/26
- CVV: 000

Başarısız Ödeme:
- Kart No: 5406675406675403
- Son Kullanma: 12/26
- CVV: 000
```

---

## 📊 WORKFLOW DİYAGRAMI

```
[Kullanıcı]
    ↓
[Checkout Formu Doldur]
    ↓
[submitOrder()]
    ↓
[Order + Payment Create (pending)]
    ↓
[PayTR iframe token oluştur]
    ↓
[Redirect → shop.payment.frame]
    ↓
[PayTR iframe sayfası]
    ↓
[Kullanıcı kart bilgisi girer]
    ↓
    ├─→ [Başarılı]
    │       ↓
    │   [PayTR Callback → shop.payment.callback]
    │       ↓
    │   [Payment status: completed]
    │   [Order status: confirmed, payment_status: paid]
    │       ↓
    │   [Cart temizle]
    │       ↓
    │   [Redirect → shop.payment.success]
    │       ↓
    │   [Flash message + Order details]
    │       ↓
    │   [Redirect → shop.order.success]
    │
    └─→ [Başarısız]
            ↓
        [PayTR Callback → shop.payment.callback]
            ↓
        [Payment status: failed]
        [Order status: pending, payment_status: failed]
            ↓
        [Redirect → shop.payment.failed]
            ↓
        [Error message]
            ↓
        [Redirect → shop.checkout (tekrar dene)]
```

---

## 🚀 ENTEGRASYON SONRASI KONTROLLER

### ✅ Fonksiyonel Testler
- [ ] Checkout → PayTR iframe → Başarılı ödeme → Order success
- [ ] Checkout → PayTR iframe → Başarısız ödeme → Checkout (retry)
- [ ] Callback hash doğrulama
- [ ] Duplicate payment engelleme
- [ ] Amount validation
- [ ] Taksit seçenekleri görünüyor mu?

### ✅ Veritabanı Testleri
- [ ] shop_orders.payment_status doğru güncelleniyor mu?
- [ ] shop_payments.status doğru güncelleniyor mu?
- [ ] shop_payments.gateway_response JSON kaydediliyor mu?
- [ ] shop_carts temizleniyor mu (sadece başarılı ödemede)?

### ✅ Güvenlik Testleri
- [ ] Geçersiz hash → Callback reddediliyor mu?
- [ ] Farklı IP'den callback → Reddediliyor mu? (opsiyonel)
- [ ] Duplicate callback → İkinci kez işlenmiyor mu?
- [ ] CSRF token bypass (callback route exempt)

### ✅ UI/UX Testleri
- [ ] Iframe responsive çalışıyor mu?
- [ ] Loading state gösteriliyor mu?
- [ ] Hata mesajları kullanıcıya net iletiyor mu?
- [ ] Başarılı ödeme sonrası sipariş detayları görünüyor mu?

---

## 📚 PAYTR API DOKÜMANTASYONU

**Resmi Dokümantasyon:**
- https://www.paytr.com/entegrasyon/odeme-formu
- https://dev.paytr.com/

**Hash Algoritması:**
```php
$hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount .
            $user_basket . $no_installment . $max_installment . $currency . $test_mode;
$paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));
```

**Callback Hash Doğrulama:**
```php
$hash = base64_encode(hash_hmac('sha256', $merchant_oid . $merchant_salt . $status . $total_amount, $merchant_key, true));

if ($hash !== $request->input('hash')) {
    return response('FAILED: Invalid hash', 400);
}
```

---

## 🎯 SONRAKİ ADIMLAR

1. ✅ Bu dokümantasyonu oku ve onayla
2. 📝 .env dosyasına PayTR credentials ekle (test mode)
3. 🏗️ PayTRService oluştur
4. 🎛️ PaymentController oluştur
5. 🔗 Route'ları ekle
6. 🖼️ Payment frame view oluştur
7. ♻️ CheckoutPageNew'i güncelle
8. 🧪 Test ortamında dene
9. 🚀 Canlıya al (merchant_id, key, salt değiştir + mode: live)

---

**Hazırlayan:** Claude Code
**Versiyon:** 1.0
**Son Güncelleme:** 2025-11-09
