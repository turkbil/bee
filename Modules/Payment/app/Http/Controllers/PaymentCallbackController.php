<?php

namespace Modules\Payment\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PayTRPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected $paytrService;

    public function __construct(PayTRPaymentService $paytrService)
    {
        $this->paytrService = $paytrService;
    }

    /**
     * Get tenant theme layout path
     */
    protected function getLayoutPath(): string
    {
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        return $layoutPath;
    }

    /**
     * PayTR callback (POST)
     */
    public function paytr(Request $request)
    {
        Log::info('PayTR Callback Alındı', [
            'post_data' => $request->all(),
            'ip' => $request->ip()
        ]);

        $result = $this->paytrService->verifyCallback($request->all());

        if ($result['success']) {
            // PayTR'ye başarı dönüşü (zorunlu)
            return response('OK', 200);
        } else {
            Log::error('PayTR Callback Doğrulama Hatası', [
                'message' => $result['message'],
                'post_data' => $request->all()
            ]);

            return response('FAILED: ' . $result['message'], 400);
        }
    }

    /**
     * Başarılı ödeme sonrası yönlendirme (GET)
     */
    public function success(Payment $payment)
    {
        // Kullanıcıya başarı sayfası göster
        return view('payment::front.payment-success', [
            'payment' => $payment,
            'order' => $payment->payable, // ShopOrder, Subscription vs.
            'layoutPath' => $this->getLayoutPath(),
        ]);
    }

    /**
     * Başarısız ödeme sonrası yönlendirme (GET)
     */
    public function fail(Payment $payment)
    {
        // Kullanıcıya hata sayfası göster
        return view('payment::front.payment-fail', [
            'payment' => $payment,
            'order' => $payment->payable,
            'layoutPath' => $this->getLayoutPath(),
        ]);
    }
}
