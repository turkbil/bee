# 🧩 PayTR Kod Şablonları (Code Templates)

**Hızlı başlangıç için hazır kod örnekleri**

---

## 📝 1. PayTRService.php

**Dosya:** `Modules/Shop/app/Services/PayTRService.php`

```php
<?php

namespace Modules\Shop\App\Services;

use Modules\Shop\App\Models\ShopOrder;
use Modules\Shop\App\Models\ShopPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PayTRService
{
    protected $merchantId;
    protected $merchantKey;
    protected $merchantSalt;
    protected $testMode;
    protected $apiUrl;
    protected $iframeUrl;

    public function __construct()
    {
        $this->merchantId = config('shop.payment.paytr.merchant_id');
        $this->merchantKey = config('shop.payment.paytr.merchant_key');
        $this->merchantSalt = config('shop.payment.paytr.merchant_salt');
        $this->testMode = config('shop.payment.paytr.mode') === 'test';
        $this->apiUrl = config('shop.payment.paytr.api_url');
        $this->iframeUrl = config('shop.payment.paytr.iframe_url');
    }

    /**
     * PayTR ödeme iframe'i oluştur
     *
     * @param ShopOrder $order
     * @param array $paymentData
     * @return array
     */
    public function createPaymentFrame(ShopOrder $order, array $paymentData): array
    {
        try {
            // Sepet bilgisi (PayTR formatı)
            $userBasket = $this->prepareUserBasket($order);

            // Hash oluştur
            $hashStr = $this->merchantId .
                       $paymentData['user_ip'] .
                       $order->order_number .
                       $paymentData['customer_email'] .
                       $paymentData['payment_amount'] .
                       $userBasket .
                       ($paymentData['no_installment'] ?? 0) .
                       ($paymentData['max_installment'] ?? 12) .
                       'TRY' .
                       ($this->testMode ? '1' : '0');

            $token = base64_encode(hash_hmac('sha256', $hashStr . $this->merchantSalt, $this->merchantKey, true));

            // API request payload
            $postData = [
                'merchant_id' => $this->merchantId,
                'user_ip' => $paymentData['user_ip'],
                'merchant_oid' => $order->order_number,
                'email' => $paymentData['customer_email'],
                'payment_amount' => $paymentData['payment_amount'], // Kuruş cinsinden
                'paytr_token' => $token,
                'user_basket' => $userBasket,
                'debug_on' => $this->testMode ? 1 : 0,
                'no_installment' => $paymentData['no_installment'] ?? 0,
                'max_installment' => $paymentData['max_installment'] ?? 12,
                'user_name' => $paymentData['customer_name'],
                'user_address' => $paymentData['customer_address'],
                'user_phone' => $paymentData['customer_phone'],
                'merchant_ok_url' => route('shop.payment.success'),
                'merchant_fail_url' => route('shop.payment.failed'),
                'timeout_limit' => config('shop.payment.paytr.timeout', 30),
                'currency' => 'TRY',
                'test_mode' => $this->testMode ? 1 : 0,
            ];

            // Log request
            Log::info('PayTR iframe request', [
                'order_number' => $order->order_number,
                'amount' => $paymentData['payment_amount'],
            ]);

            // API'ye istek at
            $response = Http::asForm()->post($this->apiUrl, $postData);

            if (!$response->successful()) {
                throw new \Exception('PayTR API request failed: ' . $response->body());
            }

            $result = $response->json();

            if ($result['status'] === 'success') {
                $iframeUrl = $this->iframeUrl . '/' . $result['token'];

                Log::info('PayTR iframe created successfully', [
                    'order_number' => $order->order_number,
                    'token' => $result['token'],
                ]);

                return [
                    'success' => true,
                    'token' => $result['token'],
                    'iframe_url' => $iframeUrl,
                ];
            } else {
                throw new \Exception('PayTR error: ' . $result['reason']);
            }

        } catch (\Exception $e) {
            Log::error('PayTR iframe creation failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * PayTR callback doğrulama
     *
     * @param Request $request
     * @return bool
     */
    public function verifyCallback(Request $request): bool
    {
        $merchantOid = $request->input('merchant_oid');
        $status = $request->input('status');
        $totalAmount = $request->input('total_amount');
        $hash = $request->input('hash');

        // Hash oluştur
        $hashStr = $merchantOid . $this->merchantSalt . $status . $totalAmount;
        $calculatedHash = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

        if ($hash !== $calculatedHash) {
            Log::error('PayTR callback hash mismatch', [
                'merchant_oid' => $merchantOid,
                'received_hash' => $hash,
                'calculated_hash' => $calculatedHash,
            ]);
            return false;
        }

        return true;
    }

    /**
     * PayTR callback işle
     *
     * @param Request $request
     * @return array
     */
    public function handleCallback(Request $request): array
    {
        try {
            // Hash doğrulama
            if (!$this->verifyCallback($request)) {
                return [
                    'success' => false,
                    'message' => 'FAILED: Invalid hash',
                ];
            }

            $merchantOid = $request->input('merchant_oid'); // Order number
            $status = $request->input('status'); // success veya failed
            $totalAmount = $request->input('total_amount'); // Kuruş cinsinden
            $paymentType = $request->input('payment_type'); // card, eft vb.
            $installmentCount = $request->input('installment_count', 1);

            // Order bul
            $order = ShopOrder::where('order_number', $merchantOid)->first();

            if (!$order) {
                Log::error('PayTR callback: Order not found', [
                    'merchant_oid' => $merchantOid,
                ]);
                return [
                    'success' => false,
                    'message' => 'FAILED: Order not found',
                ];
            }

            // Amount validation
            $expectedAmount = (int) ($order->total_amount * 100); // TRY → Kuruş
            if ((int) $totalAmount !== $expectedAmount) {
                Log::error('PayTR callback: Amount mismatch', [
                    'merchant_oid' => $merchantOid,
                    'expected' => $expectedAmount,
                    'received' => $totalAmount,
                ]);
                return [
                    'success' => false,
                    'message' => 'FAILED: Amount mismatch',
                ];
            }

            // Duplicate payment check
            $existingPayment = ShopPayment::where('order_id', $order->order_id)
                ->where('status', 'completed')
                ->first();

            if ($existingPayment) {
                Log::warning('PayTR callback: Duplicate payment attempt', [
                    'merchant_oid' => $merchantOid,
                ]);
                return [
                    'success' => true,
                    'message' => 'OK: Already processed',
                ];
            }

            \DB::beginTransaction();

            // Payment status güncelle
            $payment = ShopPayment::where('order_id', $order->order_id)
                ->where('gateway_name', 'paytr')
                ->first();

            if ($payment) {
                $payment->update([
                    'status' => $status === 'success' ? 'completed' : 'failed',
                    'gateway_transaction_id' => $merchantOid,
                    'gateway_response' => json_encode($request->all()),
                    'installment_count' => $installmentCount,
                    'paid_at' => $status === 'success' ? now() : null,
                    'failed_at' => $status === 'failed' ? now() : null,
                ]);
            }

            // Order status güncelle
            if ($status === 'success') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'paid_amount' => $order->total_amount,
                    'remaining_amount' => 0,
                ]);

                // Sepeti temizle (sadece başarılı ödemede)
                $cartService = app(\Modules\Shop\App\Services\ShopCartService::class);
                $cartService->clearCart();

                Log::info('PayTR payment successful', [
                    'order_number' => $merchantOid,
                    'amount' => $totalAmount,
                ]);
            } else {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning('PayTR payment failed', [
                    'order_number' => $merchantOid,
                    'reason' => $request->input('failed_reason_msg'),
                ]);
            }

            \DB::commit();

            return [
                'success' => true,
                'message' => 'OK',
            ];

        } catch (\Exception $e) {
            \DB::rollBack();

            Log::error('PayTR callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'FAILED: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sepet bilgisini PayTR formatına çevir
     *
     * @param ShopOrder $order
     * @return string
     */
    protected function prepareUserBasket(ShopOrder $order): string
    {
        $basket = [];

        foreach ($order->items as $item) {
            $basket[] = [
                $item->product_title, // Ürün adı
                number_format($item->unit_price, 2, '.', ''), // Birim fiyat
                $item->quantity, // Adet
            ];
        }

        return base64_encode(json_encode($basket));
    }
}
```

---

## 🎛️ 2. PaymentController.php

**Dosya:** `Modules/Shop/app/Http/Controllers/Front/PaymentController.php`

```php
<?php

namespace Modules\Shop\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shop\App\Models\ShopOrder;
use Modules\Shop\App\Services\PayTRService;

class PaymentController extends Controller
{
    protected $paytrService;

    public function __construct(PayTRService $paytrService)
    {
        $this->paytrService = $paytrService;
    }

    /**
     * PayTR ödeme iframe sayfası
     *
     * @param string $orderNumber
     * @return \Illuminate\View\View
     */
    public function frame(string $orderNumber)
    {
        $order = ShopOrder::where('order_number', $orderNumber)->firstOrFail();

        // Ödeme zaten tamamlanmışsa sipariş sayfasına yönlendir
        if ($order->payment_status === 'paid') {
            return redirect()->route('shop.order.success', $orderNumber);
        }

        // PayTR iframe oluştur
        $paymentData = [
            'user_ip' => request()->ip(),
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'customer_address' => $order->shipping_address,
            'payment_amount' => (int) ($order->total_amount * 100), // Kuruş
            'no_installment' => 0, // Taksit açık
            'max_installment' => 12,
        ];

        $paymentFrame = $this->paytrService->createPaymentFrame($order, $paymentData);

        if (!$paymentFrame['success']) {
            return redirect()
                ->route('shop.checkout')
                ->with('error', 'Ödeme sayfası oluşturulamadı: ' . $paymentFrame['error']);
        }

        return view('shop::front.payment-frame', [
            'order' => $order,
            'iframeUrl' => $paymentFrame['iframe_url'],
        ]);
    }

    /**
     * PayTR callback (IPN)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        $result = $this->paytrService->handleCallback($request);

        if ($result['success']) {
            return response('OK', 200);
        } else {
            return response($result['message'], 400);
        }
    }

    /**
     * Başarılı ödeme redirect sayfası
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        // PayTR'den gelen merchant_oid parametresi
        $orderNumber = $request->query('merchant_oid');

        if (!$orderNumber) {
            return redirect()->route('shop.index')->with('error', 'Sipariş numarası bulunamadı');
        }

        $order = ShopOrder::where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->route('shop.index')->with('error', 'Sipariş bulunamadı');
        }

        // Order success sayfasına yönlendir
        return redirect()->route('shop.order.success', $orderNumber)
            ->with('success', 'Ödemeniz başarıyla alındı!');
    }

    /**
     * Başarısız ödeme redirect sayfası
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failed(Request $request)
    {
        $orderNumber = $request->query('merchant_oid');
        $failedReason = $request->query('failed_reason_msg', 'Ödeme işlemi başarısız oldu');

        return redirect()->route('shop.checkout')
            ->with('error', 'Ödeme başarısız: ' . $failedReason)
            ->with('order_number', $orderNumber);
    }
}
```

---

## 🗃️ 3. ShopPayment Model

**Dosya:** `Modules/Shop/app/Models/ShopPayment.php`

```php
<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopPayment extends Model
{
    use SoftDeletes;

    protected $table = 'shop_payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'payment_number',
        'payment_type',
        'amount',
        'currency',
        'exchange_rate',
        'amount_in_base_currency',
        'status',
        'gateway_name',
        'gateway_transaction_id',
        'gateway_payment_id',
        'gateway_response',
        'card_brand',
        'card_last_four',
        'card_holder_name',
        'installment_count',
        'installment_fee',
        'bank_name',
        'bank_account_name',
        'bank_reference',
        'receipt_file',
        'refund_for_payment_id',
        'refund_reason',
        'is_verified',
        'verified_by_user_id',
        'verified_at',
        'paid_at',
        'failed_at',
        'refunded_at',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_in_base_currency' => 'decimal:2',
        'installment_fee' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * İlişki: Sipariş
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id', 'order_id');
    }

    /**
     * İlişki: Ödeme yöntemi
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(ShopPaymentMethod::class, 'payment_method_id', 'payment_method_id');
    }

    /**
     * Scope: Başarılı ödemeler
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: PayTR ödemeleri
     */
    public function scopePaytr($query)
    {
        return $query->where('gateway_name', 'paytr');
    }
}
```

---

## 🗃️ 4. ShopPaymentMethod Model

**Dosya:** `Modules/Shop/app/Models/ShopPaymentMethod.php`

```php
<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopPaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'shop_payment_methods';
    protected $primaryKey = 'payment_method_id';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'payment_type',
        'gateway_name',
        'gateway_mode',
        'gateway_config',
        'fixed_fee',
        'percentage_fee',
        'min_amount',
        'max_amount',
        'supports_installment',
        'installment_options',
        'max_installments',
        'supported_currencies',
        'icon',
        'logo_url',
        'sort_order',
        'is_active',
        'requires_verification',
        'is_manual',
        'available_for_b2c',
        'available_for_b2b',
        'customer_group_ids',
        'instructions',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'gateway_config' => 'array',
        'fixed_fee' => 'decimal:2',
        'percentage_fee' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'supports_installment' => 'boolean',
        'installment_options' => 'array',
        'supported_currencies' => 'array',
        'is_active' => 'boolean',
        'requires_verification' => 'boolean',
        'is_manual' => 'boolean',
        'available_for_b2c' => 'boolean',
        'available_for_b2b' => 'boolean',
        'customer_group_ids' => 'array',
        'instructions' => 'array',
    ];

    /**
     * Çoklu dil desteği
     */
    public function getTranslated(string $field, string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $data = $this->{$field};

        if (is_array($data) && isset($data[$locale])) {
            return $data[$locale];
        }

        return $data[$locale] ?? ($data['en'] ?? '');
    }

    /**
     * Scope: Aktif ödeme yöntemleri
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: PayTR gateway
     */
    public function scopePaytr($query)
    {
        return $query->where('gateway_name', 'paytr');
    }
}
```

---

## 🖼️ 5. Payment Frame View

**Dosya:** `Modules/Shop/resources/views/front/payment-frame.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Güvenli Ödeme - {{ setting('site_name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .payment-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .payment-header p {
            color: #6b7280;
            font-size: 14px;
        }
        .order-info {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .order-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .order-info-label {
            color: #6b7280;
            font-size: 14px;
        }
        .order-info-value {
            color: #1f2937;
            font-weight: 500;
            font-size: 14px;
        }
        .loading-state {
            text-align: center;
            padding: 40px 20px;
        }
        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <!-- Header -->
        <div class="payment-header">
            <h1>🔐 Güvenli Ödeme</h1>
            <p>PayTR güvenli ödeme altyapısı ile işleminiz korunmaktadır.</p>
        </div>

        <!-- Order Info -->
        <div class="order-info">
            <div class="order-info-row">
                <span class="order-info-label">Sipariş No:</span>
                <span class="order-info-value">{{ $order->order_number }}</span>
            </div>
            <div class="order-info-row">
                <span class="order-info-label">Toplam Tutar:</span>
                <span class="order-info-value">{{ number_format($order->total_amount, 2) }} TRY</span>
            </div>
            <div class="order-info-row">
                <span class="order-info-label">Müşteri:</span>
                <span class="order-info-value">{{ $order->customer_name }}</span>
            </div>
        </div>

        <!-- Loading State -->
        <div class="loading-state" id="loading-state">
            <div class="spinner"></div>
            <p style="color: #6b7280;">Ödeme sayfası yükleniyor...</p>
        </div>

        <!-- PayTR iFrame -->
        <div id="iframe-container" style="display: none;">
            <iframe
                src="{{ $iframeUrl }}"
                id="paytriframe"
                frameborder="0"
                scrolling="no"
                style="width: 100%; min-height: 600px;">
            </iframe>
        </div>
    </div>

    <!-- PayTR iFrame Resizer -->
    <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>

    <script>
        // iFrame yüklendiğinde loading state'i gizle
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading-state').style.display = 'none';
                document.getElementById('iframe-container').style.display = 'block';

                // iFrame auto-resize
                iFrameResize({
                    log: false,
                    checkOrigin: false,
                    heightCalculationMethod: 'lowestElement'
                }, '#paytriframe');
            }, 1000);
        });
    </script>
</body>
</html>
```

---

## 🌍 6. Route Tanımlamaları

**Dosya:** `routes/web.php` (en üstte, wildcard'lardan önce)

```php
// 💳 PAYTR PAYMENT ROUTES (Wildcard'dan önce!)
Route::middleware(['tenant'])->prefix('shop/payment')->group(function () {
    Route::get('/frame/{order_number}', [\Modules\Shop\App\Http\Controllers\Front\PaymentController::class, 'frame'])
        ->name('shop.payment.frame');

    Route::post('/callback', [\Modules\Shop\App\Http\Controllers\Front\PaymentController::class, 'callback'])
        ->name('shop.payment.callback');

    Route::get('/success', [\Modules\Shop\App\Http\Controllers\Front\PaymentController::class, 'success'])
        ->name('shop.payment.success');

    Route::get('/failed', [\Modules\Shop\App\Http\Controllers\Front\PaymentController::class, 'failed'])
        ->name('shop.payment.failed');
});
```

**CSRF Exception:** `app/Http/Middleware/VerifyCsrfToken.php`

```php
protected $except = [
    'shop/payment/callback', // PayTR callback CSRF'den muaf
];
```

---

## ⚙️ 7. Config Dosyası

**Dosya:** `config/shop.php` (ekle veya güncelle)

```php
return [
    // ... mevcut ayarlar ...

    'payment' => [
        'default_gateway' => env('PAYMENT_GATEWAY', 'paytr'),

        'paytr' => [
            'merchant_id' => env('PAYTR_MERCHANT_ID'),
            'merchant_key' => env('PAYTR_MERCHANT_KEY'),
            'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
            'mode' => env('PAYTR_MODE', 'test'), // test veya live
            'timeout' => env('PAYTR_TIMEOUT', 30), // Saniye
            'api_url' => env('PAYTR_API_URL', 'https://www.paytr.com/odeme/api/get-token'),
            'iframe_url' => env('PAYTR_IFRAME_URL', 'https://www.paytr.com/odeme/guvenli'),
            'max_installment' => 12,
            'no_installment' => false,
        ],
    ],
];
```

---

## 📄 8. .env Örneği

**Dosya:** `.env`

```bash
# PayTR Payment Gateway
PAYTR_MERCHANT_ID=test_merchant_id
PAYTR_MERCHANT_KEY=test_merchant_key
PAYTR_MERCHANT_SALT=test_merchant_salt
PAYTR_MODE=test
PAYTR_TIMEOUT=30
```

---

## 🗂️ 9. Database Seeder

**Dosya:** `Modules/Shop/database/seeders/PayTRPaymentMethodSeeder.php`

```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayTRPaymentMethodSeeder extends Seeder
{
    public function run()
    {
        DB::table('shop_payment_methods')->insert([
            'title' => json_encode([
                'tr' => 'Kredi Kartı (PayTR)',
                'en' => 'Credit Card (PayTR)',
            ]),
            'slug' => 'paytr-credit-card',
            'description' => json_encode([
                'tr' => 'Güvenli 3D ödeme ile kredi kartı veya banka kartı',
                'en' => 'Credit or debit card with secure 3D payment',
            ]),
            'payment_type' => 'credit_card',
            'gateway_name' => 'paytr',
            'gateway_mode' => config('shop.payment.paytr.mode'),
            'gateway_config' => json_encode([
                'merchant_id' => config('shop.payment.paytr.merchant_id'),
                'merchant_key' => config('shop.payment.paytr.merchant_key'),
                'merchant_salt' => config('shop.payment.paytr.merchant_salt'),
            ]),
            'fixed_fee' => 0,
            'percentage_fee' => 4.99, // %4.99 komisyon
            'min_amount' => 10,
            'max_amount' => null,
            'supports_installment' => true,
            'max_installments' => 12,
            'supported_currencies' => json_encode(['TRY']),
            'icon' => 'fa fa-credit-card',
            'logo_url' => null,
            'sort_order' => 1,
            'is_active' => true,
            'requires_verification' => false,
            'is_manual' => false,
            'available_for_b2c' => true,
            'available_for_b2b' => true,
            'customer_group_ids' => null,
            'instructions' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

---

## ✅ Tüm Kod Şablonları Hazır!

**Kullanım:**
1. Dosyaları kopyala-yapıştır
2. Namespace'leri kontrol et
3. Config ayarlarını yap
4. Test et!

**Sıradaki Adım:** [PAYTR-CHECKLIST.md](./PAYTR-CHECKLIST.md) dosyasını takip ederek entegrasyonu tamamla.
