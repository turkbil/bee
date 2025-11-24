# Pricing & Checkout Sayfalar1 - Plan

**Tarih:** 2025-11-24 19:53
**Durum:** Plan
**Öncelik:** Dü_ük

---

## 1. Pricing Sayfas1

### Hedef
Plan paketlerini kar_1la_t1ran ve seçim yapt1ran sayfa.

---

### Özellikler

#### A. Plan Kartlar1
- **Basic Plan**: Temel özellikler, ayl1k º49
- **Premium Plan**: Tüm özellikler, ayl1k º99
- **Free Plan**: S1n1rl1 özellikler, ücretsiz

#### Görsel Tasar1m
- 3 kolon layout
- Premium plan öne ç1kar1lm1_ (recommended badge)
- Her kart:
  - Plan ad1
  - Fiyat (büyük)
  - Özellikler listesi (checkmark)
  - "Seç" butonu

#### B. Özellik Kar_1la_t1rma Tablosu
- Tüm planlar1n özelliklerini kar_1la_t1r
- Checkbox / X icon ile göster

#### C. Kupon Uygulama
- Kupon kodu input
- "Uygula" butonu
- 0ndirim hesaplama
- Geçersiz kupon uyar1s1

#### D. Ödeme Periyodu Toggle
- Ayl1k / Y1ll1k seçim
- Y1ll1k seçilirse %20 indirim göster

---

### Teknik Detaylar

#### Plan Listesi
```php
$plans = SubscriptionPlan::where('is_active', true)
    ->orderBy('price', 'asc')
    ->get();
```

#### Kupon Dorulama
```php
$coupon = Coupon::where('code', $code)
    ->where('is_active', true)
    ->where('starts_at', '<=', now())
    ->where('expires_at', '>=', now())
    ->first();

if (!$coupon) {
    return 'Geçersiz kupon';
}

$discount = CouponService::calculateDiscount($coupon, $price);
```

---

## 2. Checkout Sayfas1

### Hedef
Seçilen plan1 sat1n alma sayfas1.

---

### Özellikler

#### A. Sipari_ Özeti
- Seçilen plan
- Fiyat
- Kupon indirimi (varsa)
- Toplam tutar

#### B. Fatura Bilgileri
- Ad Soyad
- Email
- Telefon
- Adres (opsiyonel)
- TC Kimlik / Vergi No

#### Kurumsal Kullan1c1lar 0çin
- Kurum sahibinin fatura bilgileri otomatik doldurulur
- `CorporateService::getBillingAddress()`

#### C. Ödeme Formu (PayTR)
- Kredi kart1 bilgileri
- PayTR iframe entegrasyonu
- Güvenlik bilgilendirmesi

#### D. Sözle_me Onay1
- Kullan1m _artlar1 checkbox
- Mesafeli sat1_ sözle_mesi checkbox
- KVKK ayd1nlatma metni

---

### Teknik Detaylar

#### PayTR Entegrasyonu
```php
use Modules\Payment\App\Services\PayTRService;

$payment = PayTRService::createPayment([
    'user_id' => auth()->id(),
    'plan_id' => $planId,
    'amount' => $total,
    'coupon_id' => $couponId,
]);

// iframe URL'i döndür
return $payment['iframe_url'];
```

#### Ödeme Callback
```php
Route::post('/payment/paytr/callback', function (Request $request) {
    $result = PayTRService::handleCallback($request->all());

    if ($result['status'] === 'success') {
        // Abonelik olu_tur
        SubscriptionService::createFromPayment($result['payment_id']);
    }
});
```

#### Fatura Bilgisi (Kurumsal)
```php
if (CorporateService::isMember(auth()->user())) {
    $billingAddress = CorporateService::getBillingAddress(auth()->user());

    // Form otomatik doldur
    $this->name = $billingAddress->name;
    $this->phone = $billingAddress->phone;
    $this->address = $billingAddress->address;
}
```

---

## Dosyalar

### Livewire Components
- `app/Http/Livewire/Subscription/PricingComponent.php`
- `app/Http/Livewire/Subscription/CheckoutComponent.php`

### Blade Views
- `resources/views/livewire/subscription/pricing-component.blade.php`
- `resources/views/livewire/subscription/checkout-component.blade.php`

### Servisler
- `Modules/Subscription/app/Services/SubscriptionService.php`
- `Modules/Coupon/app/Services/CouponService.php`
- `Modules/Payment/app/Services/PayTRService.php`
- `app/Services/Auth/CorporateService.php`

---

## Yakla_1m

### Ad1m 1: Pricing Component
```php
class PricingComponent extends Component
{
    public $plans;
    public $selectedPeriod = 'monthly'; // monthly, yearly
    public $couponCode = '';
    public $discount = 0;

    public function applyCoupon()
    {
        $this->discount = CouponService::validate($this->couponCode);
    }

    public function selectPlan($planId)
    {
        return redirect()->route('checkout', ['plan' => $planId, 'coupon' => $this->couponCode]);
    }

    public function render()
    {
        $this->plans = SubscriptionPlan::active()->get();
        return view('livewire.subscription.pricing-component');
    }
}
```

### Ad1m 2: Checkout Component
```php
class CheckoutComponent extends Component
{
    public $plan;
    public $coupon;
    public $name;
    public $email;
    public $phone;
    public $address;

    public function mount($planId, $couponCode = null)
    {
        $this->plan = SubscriptionPlan::findOrFail($planId);
        $this->coupon = $couponCode ? Coupon::where('code', $couponCode)->first() : null;

        // Kurumsal fatura bilgisi
        if (CorporateService::isMember(auth()->user())) {
            $this->loadBillingAddress();
        }
    }

    public function processPayment()
    {
        $this->validate();

        $payment = PayTRService::createPayment([
            'user_id' => auth()->id(),
            'plan_id' => $this->plan->id,
            'amount' => $this->calculateTotal(),
            'coupon_id' => $this->coupon?->id,
        ]);

        return redirect($payment['iframe_url']);
    }

    public function render()
    {
        return view('livewire.subscription.checkout-component');
    }
}
```

---

## Teknik Notlar

### PayTR Test Bilgileri
```
Kart No: 9792 0300 0000 0005
CVV: 000
SKT: 12/26
3D ^ifre: 123456
```

### Fiyat Hesaplama
```php
public function calculateTotal()
{
    $price = $this->plan->price;

    // Y1ll1k indirimi
    if ($this->selectedPeriod === 'yearly') {
        $price = $price * 12 * 0.8; // %20 indirim
    }

    // Kupon indirimi
    if ($this->coupon) {
        $discount = CouponService::calculateDiscount($this->coupon, $price);
        $price -= $discount;
    }

    return $price;
}
```

### Responsive Tasar1m
```blade
<!-- Plan Kartlar1 -->
<div class="row row-cols-1 row-cols-md-3 g-4">
    @foreach($plans as $plan)
        <div class="col">
            <div class="card h-100">
                <!-- Kart içerii -->
            </div>
        </div>
    @endforeach
</div>
```

---

## Beklenen Sonuç

### Pricing Sayfas1
-  3 plan kart1 gösteriliyor
-  Özellik kar_1la_t1rma tablosu çal1_1yor
-  Kupon uygulama çal1_1yor
-  Ayl1k/Y1ll1k toggle çal1_1yor

### Checkout Sayfas1
-  Sipari_ özeti doru
-  Fatura bilgileri otomatik dolduruluyor (kurumsal)
-  PayTR entegrasyonu çal1_1yor
-  Ödeme callback ba_ar1l1
-  Abonelik olu_turuluyor

---

## Test Senaryolar1

1. **Plan Seçimi**: Pricing ’ Checkout geçi_i
2. **Kupon**: Geçerli kupon uygulan1yor mu?
3. **Y1ll1k 0ndirim**: %20 indirim hesaplan1yor mu?
4. **Kurumsal Fatura**: Bilgiler otomatik dolduruluyor mu?
5. **PayTR Test**: Test kart1yla ödeme yap1labiliyor mu?
6. **Callback**: Ödeme sonras1 abonelik olu_uyor mu?
7. **Ba_ar1s1z Ödeme**: Hata mesaj1 gösteriliyor mu?

---

**NOT:** Önce her iki sayfa için HTML taslak haz1rlanacak!
