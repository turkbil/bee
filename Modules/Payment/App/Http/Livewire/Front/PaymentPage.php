<?php

namespace Modules\Payment\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Models\Order;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PayTRIframeService;
use Illuminate\Support\Facades\Auth;

class PaymentPage extends Component
{
    public $orderNumber;
    public $order;
    public $payment;
    public $paymentIframeUrl;
    public $error;

    public function mount($orderNumber)
    {
        try {
            $this->orderNumber = $orderNumber;

            // 1. Sipariş kontrolü
            $this->order = Order::where('order_number', $orderNumber)->first();

            if (!$this->order) {
                $this->error = 'Sipariş bulunamadı.';
                return;
            }

            // 2. Payment kaydını bul
            $this->payment = Payment::where('payable_type', Order::class)
                ->where('payable_id', $this->order->order_id)
                ->first();

            if (!$this->payment) {
                $this->error = 'Ödeme kaydı bulunamadı.';
                return;
            }

            // 3. Ödeme durumu kontrolü - tamamlanmış veya başarısız ise session temizle
            $sessionKey = 'paytr_merchant_oid_' . $this->payment->payment_id;
            if (in_array($this->payment->status, ['completed', 'failed', 'refunded'])) {
                session()->forget($sessionKey);
                \Log::info('🧹 PaymentPage: Ödeme tamamlanmış/başarısız, session temizlendi', [
                    'payment_id' => $this->payment->payment_id,
                    'status' => $this->payment->status,
                ]);

                if ($this->payment->status === 'completed') {
                    $this->error = 'Bu ödeme zaten tamamlanmış.';
                    return;
                }
                // Failed durumunda kullanıcı tekrar deneyebilir - yeni token alınacak
            }

            // 4. PayTR token al (eğer yoksa)
            $this->preparePayTRToken();

            \Log::info('💳 PaymentPage mounted', [
                'order_number' => $orderNumber,
                'order_id' => $this->order->order_id ?? null,
                'payment_id' => $this->payment->payment_id ?? null,
                'iframe_url' => $this->paymentIframeUrl ?? null,
                'error' => $this->error ?? null,
            ]);
        } catch (\Exception $e) {
            $this->error = 'Sayfa yüklenirken hata: ' . $e->getMessage();
            \Log::error('❌ PaymentPage mount error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    protected function preparePayTRToken()
    {
        // Session key: Bu ödeme için merchantOid
        $sessionKey = 'paytr_merchant_oid_' . $this->payment->payment_id;

        // Zaten token varsa ve expire olmamışsa kullan
        if ($this->payment->gateway_response) {
            $gatewayResponse = json_decode($this->payment->gateway_response, true);
            if (isset($gatewayResponse['token'])) {
                // Token yaşını kontrol et (PayTR token'ları ~30 dk geçerli, 25 dk'da yenile)
                $tokenExpired = false;
                if (isset($gatewayResponse['token_created_at'])) {
                    $tokenAge = now()->diffInMinutes($gatewayResponse['token_created_at']);
                    if ($tokenAge >= 25) {
                        $tokenExpired = true;
                    }
                } else {
                    // Timestamp yoksa (eski kayıt) token'ı expired kabul et
                    $tokenExpired = true;
                    \Log::info('⚠️ PaymentPage: Token timestamp yok, expired kabul edildi', [
                        'payment_id' => $this->payment->payment_id,
                    ]);
                }

                if ($tokenExpired) {
                    // ÖNEMLİ: Token expire olduğunda eski merchantOid'i de temizle
                    // Çünkü PayTR aynı merchantOid ile yeni token vermiyor!
                    session()->forget($sessionKey);
                    \Log::info('⏰ PaymentPage: Token expired, session temizlendi, yeni token alınacak', [
                        'payment_id' => $this->payment->payment_id,
                    ]);
                }

                if (!$tokenExpired) {
                    $this->paymentIframeUrl = 'https://www.paytr.com/odeme/guvenli/' . $gatewayResponse['token'];
                    \Log::info('♻️ PaymentPage: Mevcut token kullanılıyor', [
                        'payment_id' => $this->payment->payment_id,
                        'session_merchant_oid' => session($sessionKey),
                    ]);
                    return;
                }
                // Token expired ise devam et, yeni token alınacak
            }
        }

        // Token yoksa yeni al
        try {
            $iframeService = app(PayTRIframeService::class);

            // Session'dan user bilgilerini al (checkout'tan geldi)
            $userInfo = session('checkout_user_info', [
                'name' => $this->order->customer_name ?? 'Müşteri',
                'email' => $this->order->customer_email ?? 'test@test.com',
                'phone' => $this->order->customer_phone ?? '5551234567',
                'address' => 'Türkiye',
            ]);

            // Order items'dan ürün bilgilerini al
            $items = $this->order->items->map(function ($item) {
                return [
                    'name' => $item->item_title ?? 'Ürün',
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                ];
            })->toArray();

            $orderInfo = [
                'order_number' => $this->order->order_number, // PayTR callback için zorunlu!
                'amount' => $this->order->total_amount,
                'description' => 'Sipariş: ' . $this->order->order_number,
                'items' => $items,
            ];

            // Session'dan mevcut merchantOid var mı kontrol et (sayfa yenilemelerinde aynı ID kullan)
            $existingMerchantOid = session($sessionKey);

            \Log::info('🔍 PayTR Token Request', [
                'userInfo' => $userInfo,
                'orderInfo' => $orderInfo,
                'existing_merchant_oid' => $existingMerchantOid,
            ]);

            // prepareIframePayment'a mevcut merchantOid'i gönder (varsa)
            $result = $iframeService->prepareIframePayment($this->payment, $userInfo, $orderInfo, $existingMerchantOid);

            if ($result['success']) {
                // Token'ı ve oluşturma zamanını kaydet (expire kontrolü için)
                $this->payment->gateway_response = json_encode([
                    'token' => $result['token'],
                    'token_created_at' => now()->toISOString(),
                ]);
                $this->payment->save();

                // merchantOid'i session'a kaydet (sonraki sayfa yenilemelerinde aynı ID kullanılacak)
                if (isset($result['merchant_oid'])) {
                    session([$sessionKey => $result['merchant_oid']]);
                    \Log::info('💾 PaymentPage: merchantOid session\'a kaydedildi', [
                        'payment_id' => $this->payment->payment_id,
                        'merchant_oid' => $result['merchant_oid'],
                    ]);
                }

                $this->paymentIframeUrl = 'https://www.paytr.com/odeme/guvenli/' . $result['token'];
            } else {
                $this->error = 'Ödeme hazırlanamadı: ' . ($result['message'] ?? 'Bilinmeyen hata');
                \Log::error('❌ PayTR Token Failed', ['result' => $result]);
            }
        } catch (\Exception $e) {
            $this->error = 'Ödeme servisi hatası: ' . $e->getMessage();
            \Log::error('❌ PayTR Exception', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        // Layout: Tenant temasından (header/footer için)
        // View: Module default (içerik fallback'ten)
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        // Tenant layout yoksa simple fallback
        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        // View her zaman module default (orta kısım fallback)
        return view('payment::livewire.front.payment-page')
            ->layout($layoutPath, [
                'pageTitle' => 'Ödeme',
                'metaDescription' => 'Güvenli ödeme sayfası',
            ]);
    }
}
