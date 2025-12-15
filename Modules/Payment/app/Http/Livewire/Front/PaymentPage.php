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

            // 3. PayTR token al (eğer yoksa)
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
        // Zaten token varsa kullan
        if ($this->payment->gateway_response) {
            $gatewayResponse = json_decode($this->payment->gateway_response, true);
            if (isset($gatewayResponse['token'])) {
                $this->paymentIframeUrl = 'https://www.paytr.com/odeme/guvenli/' . $gatewayResponse['token'];
                return;
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

            \Log::info('🔍 PayTR Token Request', [
                'userInfo' => $userInfo,
                'orderInfo' => $orderInfo,
            ]);

            $result = $iframeService->prepareIframePayment($this->payment, $userInfo, $orderInfo);

            if ($result['success']) {
                // Token'ı kaydet
                $this->payment->gateway_response = json_encode(['token' => $result['token']]);
                $this->payment->save();

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
