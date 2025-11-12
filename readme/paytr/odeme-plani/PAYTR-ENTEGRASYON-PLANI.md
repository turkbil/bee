# 💳 PAYTR ÖDEME SİSTEMİ ENTEGRASYON PLANI

**Proje**: Tuufi Multi-Tenant E-Ticaret Platformu
**Modül**: Payment Module (Generic Payment Gateway)
**Gateway**: PayTR + Havale/EFT
**Tarih**: 2025-11-12
**Durum**: Planlama Tamamlandı - Kodlamaya Hazır ✅

---

## 🎯 PROJE AMAÇLARI

1. **Multi-Tenant Payment System**: Her tenant kendi ödeme ayarlarını yönetsin
2. **Generic Architecture**: Shop, UserManagement, Subscription - hepsi aynı payment service'i kullansın
3. **Plug-and-Play Gateways**: İlerde İyzico, Stripe eklemek 5 dakika sürsün
4. **Settings Management Integration**: Tüm ayarlar tenant-aware settings'de
5. **Manual Payment Support**: Havale/EFT banka hesapları yönetimi

---

## 📊 MİMARİ KARAR

### **Polymorphic Payment Model**
```
Payment Model (polymorphic)
├── ShopOrder (payable)
├── Membership (payable) - Gelecekte
├── Subscription (payable) - Gelecekte
└── Invoice (payable) - Gelecekte
```

### **Settings Structure**
```
Payment Gateway Ayarları (Central DB)
├── PayTR Gateway ✅ (Aktif - Tam Ayarlar)
├── Havale/EFT ✅ (Aktif - Tam Ayarlar)
├── Stripe ⏳ (Placeholder - Gelecekte)
├── İyzico ⏳ (Placeholder - Gelecekte)
└── PayPal ⏳ (Placeholder - Gelecekte)
```

### **Database Structure**
```
CENTRAL DB (tuufi_com):
├── settings_groups (Gateway grupları)
└── settings (Gateway ayarları tanımları)

TENANT DB (ixtif_db):
├── settings_values (Tenant-specific credentials)
├── bank_accounts (Havale/EFT hesapları)
├── payments (Ödeme kayıtları)
└── shop_orders (Siparişler)
```

---

## 🗂️ DOSYA YAPISI

```
Modules/Payment/
├── database/
│   ├── migrations/
│   │   ├── 2025_11_12_010000_create_payment_gateway_settings.php ← YENİ
│   │   └── tenant/
│   │       └── 2025_11_12_020000_create_bank_accounts_table.php ← YENİ
│   └── seeders/
│       ├── IyzicoGatewaySeeder.php ← Gelecek (Placeholder)
│       ├── StripeGatewaySeeder.php ← Gelecek (Placeholder)
│       └── PayPalGatewaySeeder.php ← Gelecek (Placeholder)
│
├── App/
│   ├── Models/
│   │   ├── Payment.php ← Mevcut (Polymorphic)
│   │   ├── PaymentMethod.php ← Mevcut
│   │   └── BankAccount.php ← YENİ
│   │
│   ├── Services/
│   │   ├── PaymentGatewayManager.php ← YENİ (Gateway seçim logic)
│   │   ├── PayTRIframeService.php ← GÜNCELLEME (Settings entegrasyonu)
│   │   └── PayTRCallbackService.php ← YENİ (Callback logic)
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PayTRCallbackController.php ← YENİ (Generic callback)
│   │   │   └── BankTransferController.php ← YENİ (Havale success page)
│   │   └── Livewire/
│   │       └── Admin/
│   │           └── BankAccountComponent.php ← YENİ (Banka hesap yönetimi)
│   │
│   └── Contracts/
│       └── Payable.php ← YENİ (Interface)
│
└── routes/
    └── web.php ← GÜNCELLEME (Callback routes)

Modules/Shop/
└── App/
    ├── Models/
    │   └── ShopOrder.php ← GÜNCELLEME (implements Payable)
    └── Http/Livewire/Front/
        └── CheckoutPageNew.php ← GÜNCELLEME (Gateway selection)
```

---

## 📋 YAPILACAKLAR LİSTESİ

### **ADIM 1: DATABASE MIGRATIONS** 🗄️

#### 1.1. Payment Gateway Settings (Central DB)
**Dosya**: `Modules/Payment/database/migrations/2025_11_12_010000_create_payment_gateway_settings.php`

**İçerik**:
- Ana grup: "Payment Gateway Ayarları"
- PayTR alt grubu (Tam ayarlar):
  - Aktif/Pasif, Display Name, Description, Sort Order, Logo
  - Merchant ID, Key, Salt
  - Test Mode, Max Installment, Currency
- Havale/EFT alt grubu (Tam ayarlar):
  - Aktif/Pasif, Display Name, Description, Sort Order
  - Approval Days, Auto Cancel Days
- Placeholder gruplar (Gelecek için):
  - Stripe (Sadece 1 disabled checkbox)
  - İyzico (Sadece 1 disabled checkbox)
  - PayPal (Sadece 1 disabled checkbox)

#### 1.2. Bank Accounts Table (Tenant DB)
**Dosya**: `Modules/Payment/database/migrations/tenant/2025_11_12_020000_create_bank_accounts_table.php`

**Kolonlar**:
- bank_account_id (PK)
- bank_name, branch_name, branch_code
- account_holder_name, account_number, iban, swift_code
- currency (TRY, USD, EUR, GBP)
- is_active, sort_order
- description (Müşteriye gösterilecek not)
- timestamps, soft_deletes

---

### **ADIM 2: MODELS** 🏗️

#### 2.1. BankAccount Model
**Dosya**: `Modules/Payment/App/Models/BankAccount.php`

**Özellikler**:
- Scopes: active(), byCurrency()
- Accessors: formatted_iban, formatted_account_number
- Soft deletes

#### 2.2. Payable Interface
**Dosya**: `Modules/Payment/App/Contracts/Payable.php`

**Metodlar**:
```php
interface Payable
{
    public function getPayableAmount(): float;
    public function getPayableDescription(): string;
    public function getPayableCustomer(): array;
}
```

#### 2.3. ShopOrder (implements Payable)
**Dosya**: `Modules/Shop/App/Models/ShopOrder.php`

**Güncelleme**:
- implements Payable
- getPayableAmount(), getPayableDescription(), getPayableCustomer()

---

### **ADIM 3: SERVICES** 🔧

#### 3.1. PaymentGatewayManager Service
**Dosya**: `Modules/Payment/App/Services/PaymentGatewayManager.php`

**Metodlar**:
```php
getAvailableGateways(float $amount): array  // Checkout'ta gösterilecek gateway listesi
isGatewayAvailable(string $gateway, float $amount): bool  // Gateway kullanılabilir mi?
getGatewayService(string $gatewayCode)  // Gateway'e göre service döndür
```

**Logic**:
- PayTR kontrolü: enabled, credentials dolu mu, tutar limiti
- Havale/EFT kontrolü: enabled, en az 1 aktif banka hesabı var mı
- Sıralama: sort_order'a göre

#### 3.2. PayTRIframeService (Settings Entegrasyonu)
**Dosya**: `Modules/Payment/App/Services/PayTRIframeService.php`

**Değişiklikler**:
```php
// ESKI: gateway_config'den al
$merchantId = $config['merchant_id'];

// YENİ: Settings'den al (tenant-aware)
$merchantId = setting('paytr_merchant_id');
$merchantKey = setting('paytr_merchant_key');
$merchantSalt = setting('paytr_merchant_salt');
$testMode = setting('paytr_test_mode', false);
$maxInstallment = setting('paytr_max_installment', 0);
$currency = setting('paytr_currency', 'TL');
```

#### 3.3. PayTRCallbackService
**Dosya**: `Modules/Payment/App/Services/PayTRCallbackService.php`

**Logic**:
- Hash kontrolü (güvenlik)
- Duplicate kontrolü (aynı ödeme birden fazla gelebilir)
- Payment status güncelle (paid/failed)
- Payable model güncelle (ShopOrder, Membership vb.)
- Event dispatch (OrderPaid, PaymentFailed)

---

### **ADIM 4: CONTROLLERS** 🎮

#### 4.1. PayTRCallbackController (Generic)
**Dosya**: `Modules/Payment/App/Http/Controllers/PayTRCallbackController.php`

**Flow**:
1. merchant_oid al
2. Tenant ID parse et (T2-ORD-xxx formatından)
3. Tenant context'e gir (tenancy()->initialize())
4. Payment bul (transaction_id ile)
5. Hash kontrolü (settings'den key/salt al)
6. Duplicate kontrolü
7. Status success → Order onayla
8. Status failed → Order iptal et
9. "OK" döndür (ZORUNLU!)

**Route**: `/payment/callback/paytr` (POST, no auth/session)

#### 4.2. BankTransferController
**Dosya**: `Modules/Payment/App/Http/Controllers/BankTransferController.php`

**Metodlar**:
- showBankAccounts(): Havale seçilince banka hesaplarını göster
- confirmBankTransfer(): Havale yapıldı butonuna basınca order pending yap

---

### **ADIM 5: LIVEWIRE COMPONENTS** ⚡

#### 5.1. BankAccountComponent (Admin)
**Dosya**: `Modules/Payment/App/Http/Livewire/Admin/BankAccountComponent.php`

**Özellikler**:
- Liste: Tüm banka hesapları (card view)
- Modal: Yeni hesap ekle / düzenle
- Actions: Edit, Delete, Toggle Active
- Form fields: bank_name, iban, currency, is_active, description

#### 5.2. CheckoutPageNew (Güncelleme)
**Dosya**: `Modules/Shop/App/Http/Livewire/Front/CheckoutPageNew.php`

**Değişiklikler**:
- `$selectedGateway` property ekle
- `mount()`: Tek gateway varsa otomatik seç
- `proceedToPayment()`: Gateway'e göre service seç
- Validation: selectedGateway required

**View Değişiklikleri**:
- Gateway seçim radio buttons
- Havale seçilince banka hesapları göster
- IBAN kopyala butonu

---

### **ADIM 6: ROUTES** 🛣️

**Dosya**: `Modules/Payment/routes/web.php`

```php
// PayTR Callback (Tenant-aware, no auth)
Route::post('/payment/callback/paytr', [PayTRCallbackController::class, 'handle'])
    ->name('payment.callback.paytr');

// Success/Fail Pages
Route::get('/shop/order/success/{orderNumber}', [OrderSuccessController::class, 'show'])
    ->name('shop.order.success');

Route::get('/shop/order/failed/{orderNumber}', [OrderFailedController::class, 'show'])
    ->name('shop.order.failed');

// Admin: Banka Hesapları
Route::middleware('auth')->group(function () {
    Route::get('/admin/payment/bank-accounts', BankAccountComponent::class)
        ->name('admin.payment.bank-accounts');
});
```

---

## 🔐 GÜVENLİK KONTROL LİSTESİ

- [ ] PayTR Hash kontrolü ZORUNLU (callback'te)
- [ ] Duplicate payment kontrolü (aynı ödeme birden fazla gelebilir)
- [ ] Tenant isolation (tenant context doğru girilsin)
- [ ] Settings encryption (merchant_key, salt şifreli)
- [ ] IBAN validation (format kontrolü)
- [ ] CSRF protection (callback hariç)
- [ ] SQL injection prevention (Eloquent kullan)
- [ ] XSS prevention (Blade escape kullan)

---

## 🧪 TEST SENARYOLARI

### **Test 1: PayTR Test Ödeme**
1. Admin: PayTR ayarlarını gir (test mode ON)
2. Checkout: PayTR seç, sipariş ver
3. PayTR iframe açılsın
4. Test kartı ile ödeme yap
5. Callback gelsin (hash doğru mu?)
6. Order status "paid" olsun
7. Success sayfası açılsın

### **Test 2: Havale/EFT**
1. Admin: 2 banka hesabı ekle (1 TL, 1 USD)
2. Admin: Havale/EFT aktif et
3. Checkout: Havale seç
4. 2 hesap gösterilsin
5. IBAN kopyala çalışsın
6. Siparişi tamamla
7. Order status "pending_payment" olsun
8. Admin: Manuel onay yapsın
9. Order status "paid" olsun

### **Test 3: Multi-Gateway Seçim**
1. Admin: PayTR + Havale/EFT aktif
2. Checkout: 2 seçenek gösterilsin
3. Radio button seçimi çalışsın
4. Her gateway için farklı akış çalışsın

### **Test 4: Tenant Isolation**
1. Tenant 1: PayTR credentials gir
2. Tenant 2: Farklı PayTR credentials gir
3. Her tenant kendi callback'ini alsın
4. Settings karışmasın

---

## ⚠️ KRİTİK NOTLAR

### **1. Tenant Context (Callback)**
```php
// Order number'da tenant ID olmalı!
$orderNumber = 'T' . tenant('id') . '-ORD-20251112-A1B2C3';

// Callback'te parse et
preg_match('/^T(\d+)-/', $merchantOid, $matches);
$tenantId = $matches[1];
tenancy()->initialize(Tenant::find($tenantId));
```

### **2. PayTR Async Callback**
- merchant_ok_url: Müşteri yönlendirilir (sipariş henüz onaylanmadı!)
- Bildirim URL: PayTR buraya POST yapar (sipariş onaylanır!)
- merchant_ok_url'de sipariş ONAYLAMA!

### **3. Settings Cache**
```php
// Settings helper otomatik cache ediyor (1 saat)
setting('paytr_merchant_id');  // İlk: DB'den, Sonra: Cache'den
```

### **4. Duplicate Payment**
```php
// Aynı ödeme birden fazla gelebilir (ağ sorunu)
if (in_array($payment->status, ['paid', 'failed'])) {
    return response('OK'); // Zaten işlenmiş
}
```

---

## 📈 İLERDE EKLENEBİLECEKLER

1. **İyzico Gateway** (Seeder hazır, 5 dakikada ekle)
2. **Stripe Gateway** (Placeholder hazır)
3. **PayPal Gateway** (Placeholder hazır)
4. **Subscription Payment** (Polymorphic hazır)
5. **Membership Payment** (Polymorphic hazır)
6. **Payment Installment Detail** (PayTR taksit bilgileri)
7. **Refund System** (PayTR iade API'si)
8. **Payment Analytics** (Dashboard, raporlar)

---

## 🚀 KODLAMAYA BAŞLAMA SIRASI

### **PHASE 1: Database & Models** (30 dk)
1. ✅ Migration: Payment Gateway Settings
2. ✅ Migration: Bank Accounts Table
3. ✅ Model: BankAccount
4. ✅ Interface: Payable
5. ✅ ShopOrder: implements Payable

### **PHASE 2: Services** (45 dk)
6. ✅ PaymentGatewayManager
7. ✅ PayTRIframeService (Settings entegrasyonu)
8. ✅ PayTRCallbackService

### **PHASE 3: Controllers & Routes** (30 dk)
9. ✅ PayTRCallbackController
10. ✅ Routes: Callback + Success/Fail

### **PHASE 4: Livewire & UI** (1 saat)
11. ✅ BankAccountComponent (Admin)
12. ✅ CheckoutPageNew (Gateway selection)
13. ✅ Havale/EFT detail page

### **PHASE 5: Test & Debug** (1 saat)
14. ✅ Test PayTR ödeme
15. ✅ Test Havale/EFT
16. ✅ Test Multi-gateway
17. ✅ Test Tenant isolation

---

## ✅ BAŞARIYLA TAMAMLANDI

**Toplam Tahmini Süre**: ~3.5 saat
**Zorluk Seviyesi**: Orta
**Risk Seviyesi**: Düşük (Mevcut sistem üzerine ekleme)

---

**KODLAMAYA BAŞLAYALIM! 🚀**
