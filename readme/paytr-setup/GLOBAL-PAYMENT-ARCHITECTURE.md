# 🌍 Global Payment Modülü Mimarisi

**Polymorphic İlişki ile Merkezi Ödeme Sistemi**

---

## 🎯 MİMARİ HEDEF

**Sorun:** Şu anda PayTR sadece Shop modülüne özgü tasarlanmış durumda.

**Çözüm:** Polymorphic ilişki kullanarak **tüm modüller için ortak ödeme altyapısı**.

### Kullanım Senaryoları:
- ✅ **Shop Modülü** → Ürün satış ödemeleri
- ✅ **Membership Modülü** → Üyelik/abonelik ödemeleri
- ✅ **Booking Modülü** → Rezervasyon ödemeleri (gelecekte)
- ✅ **Donation Modülü** → Bağış ödemeleri (gelecekte)
- ✅ **Invoice Modülü** → Fatura ödemeleri (gelecekte)

---

## 🏗️ YENİ MODÜL YAPISI

### Payment Modülü (Global)

```
Modules/Payment/
├── app/
│   ├── Models/
│   │   ├── Payment.php                    # Ana ödeme kaydı (polymorphic)
│   │   ├── PaymentMethod.php              # Ödeme yöntemleri (PayTR, Stripe, vb.)
│   │   ├── PaymentTransaction.php         # İşlem logları (opsiyonel)
│   │   └── PaymentRefund.php              # İade kayıtları (opsiyonel)
│   │
│   ├── Services/
│   │   ├── PaymentService.php             # Ana ödeme servisi (facade)
│   │   ├── Gateways/
│   │   │   ├── PaymentGatewayInterface.php # Gateway contract
│   │   │   ├── PayTRGateway.php           # PayTR implementasyonu
│   │   │   ├── StripeGateway.php          # Stripe (gelecekte)
│   │   │   └── IyzicoGateway.php          # Iyzico (gelecekte)
│   │   └── PaymentFactory.php             # Gateway factory pattern
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── PaymentController.php      # Global payment controller
│   │   └── Middleware/
│   │       └── VerifyPaymentCallback.php  # Callback security
│   │
│   ├── Enums/
│   │   ├── PaymentStatus.php              # pending, completed, failed, refunded
│   │   ├── PaymentType.php                # purchase, subscription, donation, refund
│   │   └── PaymentGateway.php             # paytr, stripe, iyzico
│   │
│   └── Contracts/
│       └── Payable.php                    # Ödeme yapılabilir interface
│
├── database/
│   └── migrations/
│       ├── 001_create_payment_methods_table.php
│       ├── 002_create_payments_table.php  # Polymorphic ilişki
│       ├── 003_create_payment_transactions_table.php
│       └── tenant/                        # Tenant migrations (aynı dosyalar)
│
├── resources/
│   └── views/
│       ├── payment-frame.blade.php        # Gateway iframe sayfası
│       ├── payment-success.blade.php      # Başarılı ödeme
│       └── payment-failed.blade.php       # Başarısız ödeme
│
├── routes/
│   └── web.php                            # Global payment routes
│
└── config/
    └── payment.php                        # Global payment config
```

---

## 🗄️ VERİTABANI ŞEMASI (Polymorphic)

### 1. `payment_methods` Tablosu (Global)

```php
Schema::create('payment_methods', function (Blueprint $table) {
    $table->id('payment_method_id');

    // Basic Info
    $table->json('title')->comment('{"tr":"Kredi Kartı","en":"Credit Card"}');
    $table->string('slug')->unique()->comment('paytr-credit-card');
    $table->json('description')->nullable();

    // Gateway Info
    $table->enum('gateway', ['paytr', 'stripe', 'iyzico', 'paypal', 'manual'])
          ->comment('Ödeme gateway');
    $table->enum('gateway_mode', ['test', 'live'])->default('test');
    $table->json('gateway_config')->nullable()->comment('API keys, merchant IDs');

    // Payment Type Support
    $table->boolean('supports_purchase')->default(true)->comment('Satış ödemeleri');
    $table->boolean('supports_subscription')->default(false)->comment('Abonelik ödemeleri');
    $table->boolean('supports_donation')->default(false)->comment('Bağış ödemeleri');

    // Fees & Limits
    $table->decimal('fixed_fee', 10, 2)->default(0);
    $table->decimal('percentage_fee', 5, 2)->default(0);
    $table->decimal('min_amount', 10, 2)->nullable();
    $table->decimal('max_amount', 14, 2)->nullable();

    // Installment
    $table->boolean('supports_installment')->default(false);
    $table->integer('max_installments')->default(1);
    $table->json('installment_options')->nullable();

    // Currency
    $table->json('supported_currencies')->comment('["TRY","USD","EUR"]');

    // Display
    $table->string('icon')->nullable();
    $table->string('logo_url')->nullable();
    $table->integer('sort_order')->default(0);

    // Status
    $table->boolean('is_active')->default(true);
    $table->boolean('requires_verification')->default(false);

    // Timestamps
    $table->timestamps();
    $table->softDeletes();

    // Indexes
    $table->index('gateway');
    $table->index('is_active');
    $table->index('sort_order');
});
```

---

### 2. `payments` Tablosu (Polymorphic)

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id('payment_id');

    // ⭐ POLYMORPHIC İLİŞKİ (Hangi modelden ödeme?)
    $table->morphs('payable'); // payable_id, payable_type
    // Örnekler:
    // - payable_type: "Modules\Shop\App\Models\ShopOrder", payable_id: 123
    // - payable_type: "Modules\Membership\App\Models\Subscription", payable_id: 45
    // - payable_type: "Modules\Booking\App\Models\Reservation", payable_id: 67

    // Payment Method
    $table->foreignId('payment_method_id')->nullable()
          ->constrained('payment_methods', 'payment_method_id')
          ->onDelete('set null');

    // Payment Info
    $table->string('payment_number')->unique()->comment('PAY-2024-00001');
    $table->enum('payment_type', ['purchase', 'subscription', 'donation', 'refund', 'deposit'])
          ->default('purchase');

    // Amount
    $table->decimal('amount', 12, 2)->comment('Ödeme tutarı');
    $table->string('currency', 3)->default('TRY');
    $table->decimal('exchange_rate', 10, 4)->default(1);
    $table->decimal('amount_in_base_currency', 12, 2);

    // Status
    $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])
          ->default('pending');

    // Gateway Info
    $table->enum('gateway', ['paytr', 'stripe', 'iyzico', 'paypal', 'manual'])
          ->comment('Kullanılan gateway');
    $table->string('gateway_transaction_id')->nullable()->comment('Gateway merchant_oid');
    $table->string('gateway_payment_id')->nullable()->comment('Gateway token');
    $table->json('gateway_response')->nullable()->comment('Tüm gateway response');

    // Card Info (masked)
    $table->string('card_brand')->nullable();
    $table->string('card_last_four', 4)->nullable();
    $table->string('card_holder_name')->nullable();

    // Installment
    $table->integer('installment_count')->default(1);
    $table->decimal('installment_fee', 8, 2)->default(0);

    // Refund
    $table->foreignId('refund_for_payment_id')->nullable()
          ->constrained('payments', 'payment_id')
          ->onDelete('set null');
    $table->text('refund_reason')->nullable();

    // Verification
    $table->boolean('is_verified')->default(false);
    $table->foreignId('verified_by_user_id')->nullable();
    $table->timestamp('verified_at')->nullable();

    // Important Dates
    $table->timestamp('paid_at')->nullable();
    $table->timestamp('failed_at')->nullable();
    $table->timestamp('refunded_at')->nullable();

    // Additional Info
    $table->text('notes')->nullable();
    $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

    // IP & Browser
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();

    // Timestamps
    $table->timestamps();
    $table->softDeletes();

    // Indexes
    $table->index(['payable_type', 'payable_id']); // ⭐ Polymorphic index
    $table->index('payment_number');
    $table->index('status');
    $table->index('gateway');
    $table->index('gateway_transaction_id');
    $table->index('paid_at');
});
```

---

## 🧩 POLYMORPHIC İLİŞKİ KULLANIMI

### Payable Contract (Interface)

**Dosya:** `Modules/Payment/app/Contracts/Payable.php`

```php
<?php

namespace Modules\Payment\App\Contracts;

interface Payable
{
    /**
     * Ödeme tutarını döndür (kuruş cinsinden)
     */
    public function getPaymentAmount(): int;

    /**
     * Para birimini döndür
     */
    public function getPaymentCurrency(): string;

    /**
     * Ödeme açıklamasını döndür
     */
    public function getPaymentDescription(): string;

    /**
     * Müşteri bilgilerini döndür
     */
    public function getPaymentCustomer(): array;

    /**
     * Sepet/ürün bilgisini döndür (PayTR formatı)
     */
    public function getPaymentBasket(): array;

    /**
     * Ödeme başarılı olduğunda tetiklenir
     */
    public function onPaymentCompleted(\Modules\Payment\App\Models\Payment $payment): void;

    /**
     * Ödeme başarısız olduğunda tetiklenir
     */
    public function onPaymentFailed(\Modules\Payment\App\Models\Payment $payment): void;
}
```

---

### Shop Order → Payable Implementation

**Dosya:** `Modules/Shop/app/Models/ShopOrder.php`

```php
<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Payment\App\Contracts\Payable;
use Modules\Payment\App\Models\Payment;

class ShopOrder extends Model implements Payable
{
    // ... mevcut kod ...

    /**
     * Polymorphic ilişki: Ödemeler
     */
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Payable interface implementasyonu
     */
    public function getPaymentAmount(): int
    {
        return (int) ($this->total_amount * 100); // TRY → Kuruş
    }

    public function getPaymentCurrency(): string
    {
        return $this->currency ?? 'TRY';
    }

    public function getPaymentDescription(): string
    {
        return "Sipariş ödemesi: {$this->order_number}";
    }

    public function getPaymentCustomer(): array
    {
        return [
            'name' => $this->customer_name,
            'email' => $this->customer_email,
            'phone' => $this->customer_phone,
            'address' => $this->shipping_address,
        ];
    }

    public function getPaymentBasket(): array
    {
        $basket = [];
        foreach ($this->items as $item) {
            $basket[] = [
                $item->product_title,
                number_format($item->unit_price, 2, '.', ''),
                $item->quantity,
            ];
        }
        return $basket;
    }

    public function onPaymentCompleted(Payment $payment): void
    {
        $this->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'paid_amount' => $this->total_amount,
            'remaining_amount' => 0,
        ]);

        // Sepeti temizle
        $cartService = app(\Modules\Shop\App\Services\ShopCartService::class);
        $cartService->clearCart();

        // Email gönder (opsiyonel)
        // Mail::to($this->customer_email)->send(new OrderConfirmedMail($this));
    }

    public function onPaymentFailed(Payment $payment): void
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }
}
```

---

### Membership Subscription → Payable Implementation

**Dosya:** `Modules/Membership/app/Models/Subscription.php` (örnek)

```php
<?php

namespace Modules\Membership\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Payment\App\Contracts\Payable;
use Modules\Payment\App\Models\Payment;

class Subscription extends Model implements Payable
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_number',
        'amount',
        'currency',
        'status', // pending, active, cancelled, expired
        'starts_at',
        'ends_at',
    ];

    /**
     * Polymorphic ilişki: Ödemeler
     */
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Payable interface implementasyonu
     */
    public function getPaymentAmount(): int
    {
        return (int) ($this->amount * 100); // TRY → Kuruş
    }

    public function getPaymentCurrency(): string
    {
        return $this->currency ?? 'TRY';
    }

    public function getPaymentDescription(): string
    {
        return "Üyelik ödemesi: {$this->subscription_number}";
    }

    public function getPaymentCustomer(): array
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone ?? '',
            'address' => $this->user->address ?? '',
        ];
    }

    public function getPaymentBasket(): array
    {
        return [
            [
                'Üyelik Paketi: ' . $this->plan->name,
                number_format($this->amount, 2, '.', ''),
                1,
            ]
        ];
    }

    public function onPaymentCompleted(Payment $payment): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(), // Örnek: 1 aylık
        ]);

        // Kullanıcıya üyelik rolü ata
        // $this->user->assignRole('premium_member');
    }

    public function onPaymentFailed(Payment $payment): void
    {
        $this->update([
            'status' => 'failed',
        ]);
    }
}
```

---

## 🛠️ PAYMENT SERVICE (Facade Pattern)

**Dosya:** `Modules/Payment/app/Services/PaymentService.php`

```php
<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Contracts\Payable;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Models\PaymentMethod;
use Modules\Payment\App\Services\Gateways\PaymentGatewayInterface;
use Modules\Payment\App\Services\PaymentFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $factory;

    public function __construct(PaymentFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Ödeme başlat (herhangi bir model için)
     *
     * @param Payable $payable (ShopOrder, Subscription, vb.)
     * @param PaymentMethod $paymentMethod
     * @param array $options
     * @return array
     */
    public function initiatePayment(Payable $payable, PaymentMethod $paymentMethod, array $options = []): array
    {
        try {
            DB::beginTransaction();

            // Payment kaydı oluştur (polymorphic)
            $payment = Payment::create([
                'payable_id' => $payable->id ?? $payable->getKey(),
                'payable_type' => get_class($payable),
                'payment_method_id' => $paymentMethod->payment_method_id,
                'payment_number' => $this->generatePaymentNumber(),
                'payment_type' => $options['payment_type'] ?? 'purchase',
                'amount' => $payable->getPaymentAmount() / 100, // Kuruş → TRY
                'currency' => $payable->getPaymentCurrency(),
                'amount_in_base_currency' => $payable->getPaymentAmount() / 100,
                'status' => 'pending',
                'gateway' => $paymentMethod->gateway,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Gateway instance oluştur
            $gateway = $this->factory->make($paymentMethod->gateway);

            // Gateway'e ödeme başlat
            $paymentData = [
                'payment' => $payment,
                'payable' => $payable,
                'payment_method' => $paymentMethod,
                'customer' => $payable->getPaymentCustomer(),
                'basket' => $payable->getPaymentBasket(),
                'options' => $options,
            ];

            $result = $gateway->initiatePayment($paymentData);

            if ($result['success']) {
                // Gateway response'u kaydet
                $payment->update([
                    'gateway_payment_id' => $result['token'] ?? null,
                    'gateway_response' => $result,
                ]);

                DB::commit();

                Log::info('Payment initiated successfully', [
                    'payment_id' => $payment->payment_id,
                    'payable_type' => get_class($payable),
                    'gateway' => $paymentMethod->gateway,
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'redirect_url' => $result['redirect_url'] ?? route('payment.frame', $payment->payment_number),
                    'iframe_url' => $result['iframe_url'] ?? null,
                ];
            } else {
                throw new \Exception($result['error'] ?? 'Payment initiation failed');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment initiation failed', [
                'payable_type' => get_class($payable),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Ödeme numarası üret
     */
    protected function generatePaymentNumber(): string
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Callback işle (gateway'den gelen bildirim)
     */
    public function handleCallback(string $gateway, array $data): array
    {
        $gatewayInstance = $this->factory->make($gateway);
        return $gatewayInstance->handleCallback($data);
    }
}
```

---

## 🔌 GATEWAY INTERFACE & FACTORY

**Dosya:** `Modules/Payment/app/Services/Gateways/PaymentGatewayInterface.php`

```php
<?php

namespace Modules\Payment\App\Services\Gateways;

interface PaymentGatewayInterface
{
    /**
     * Ödeme başlat
     */
    public function initiatePayment(array $data): array;

    /**
     * Callback doğrula
     */
    public function verifyCallback(array $data): bool;

    /**
     * Callback işle
     */
    public function handleCallback(array $data): array;

    /**
     * İade işlemi
     */
    public function refund(string $transactionId, float $amount): array;
}
```

---

**Dosya:** `Modules/Payment/app/Services/PaymentFactory.php`

```php
<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Services\Gateways\PaymentGatewayInterface;
use Modules\Payment\App\Services\Gateways\PayTRGateway;
use Modules\Payment\App\Services\Gateways\StripeGateway;

class PaymentFactory
{
    public function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'paytr' => app(PayTRGateway::class),
            'stripe' => app(StripeGateway::class),
            // 'iyzico' => app(IyzicoGateway::class),
            default => throw new \InvalidArgumentException("Unsupported gateway: {$gateway}"),
        };
    }
}
```

---

## 📋 KULLANIM ÖRNEKLERİ

### Örnek 1: Shop Checkout

```php
// CheckoutPageNew.php
public function submitOrder()
{
    // ... order oluştur ...

    // PaymentService kullan
    $paymentService = app(\Modules\Payment\App\Services\PaymentService::class);
    $paymentMethod = PaymentMethod::where('gateway', 'paytr')->active()->first();

    $result = $paymentService->initiatePayment($order, $paymentMethod, [
        'payment_type' => 'purchase',
        'no_installment' => 0,
        'max_installment' => 12,
    ]);

    if ($result['success']) {
        return redirect($result['redirect_url']);
    } else {
        session()->flash('error', $result['error']);
    }
}
```

---

### Örnek 2: Membership Subscription

```php
// MembershipController.php
public function subscribe(Request $request)
{
    // Subscription oluştur
    $subscription = Subscription::create([
        'user_id' => auth()->id(),
        'plan_id' => $request->plan_id,
        'subscription_number' => 'SUB-' . uniqid(),
        'amount' => 99.00,
        'currency' => 'TRY',
        'status' => 'pending',
    ]);

    // PaymentService kullan
    $paymentService = app(\Modules\Payment\App\Services\PaymentService::class);
    $paymentMethod = PaymentMethod::where('gateway', 'paytr')->active()->first();

    $result = $paymentService->initiatePayment($subscription, $paymentMethod, [
        'payment_type' => 'subscription',
    ]);

    if ($result['success']) {
        return redirect($result['redirect_url']);
    }
}
```

---

## 🎯 MİGRASYON PLANI

### Adım 1: Payment Modülünü Oluştur
```bash
php artisan module:make Payment
```

### Adım 2: Migration'ları Oluştur
```bash
# Payment methods
php artisan make:migration create_payment_methods_table --path=Modules/Payment/database/migrations

# Payments (polymorphic)
php artisan make:migration create_payments_table --path=Modules/Payment/database/migrations

# Tenant migration'ları da oluştur (aynı içerik)
cp Modules/Payment/database/migrations/*_create_*.php Modules/Payment/database/migrations/tenant/
```

### Adım 3: Shop Modülünü Güncelle
- `ShopOrder` model'ine `Payable` interface implement et
- `shop_orders` tablosundan `payment_method_id` kaldır (artık payments tablosunda)
- `shop_payments` tablosunu **SİL** (artık global `payments` kullanılacak)

### Adım 4: Test
```bash
php artisan migrate
php artisan test
```

---

## 📊 KARŞILAŞTIRMA

### ❌ Eski Yapı (Shop-specific)
```
shop_orders (order_id, payment_status)
  ↓
shop_payments (payment_id, order_id, gateway_name)
  ↓
PayTR sadece Shop için çalışır
```

### ✅ Yeni Yapı (Global)
```
ANY MODEL (ShopOrder, Subscription, Invoice, vb.)
  ↓ implements Payable
payments (payment_id, payable_type, payable_id)
  ↓ polymorphic
PayTR, Stripe, Iyzico TÜM modüller için çalışır
```

---

## 🚀 AVANTAJLAR

1. ✅ **Tek Yerden Yönetim** - Tüm ödemeler `payments` tablosunda
2. ✅ **Gateway Bağımsız** - PayTR, Stripe, Iyzico aynı interface
3. ✅ **Modül Bağımsız** - Shop, Membership, Booking hepsi kullanabilir
4. ✅ **Kolay Genişleme** - Yeni gateway eklemek 1 sınıf yazmak
5. ✅ **Merkezi Raporlama** - Tüm ödemeleri tek yerden sorgula
6. ✅ **SOLID Prensipleri** - Interface, Factory, Strategy pattern

---

## 📝 SONRAKI ADIMLAR

1. ✅ Bu mimariyi onayla
2. 📦 Payment modülünü oluştur
3. 🗄️ Migration'ları yaz
4. 🧩 PayTRGateway'i implement et
5. 🛒 ShopOrder'ı Payable yap
6. 🧪 Test et
7. 📚 Dokümante et

---

**Onay bekliyor! Bu mimari ile devam edelim mi?**
