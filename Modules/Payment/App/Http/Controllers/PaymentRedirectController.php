<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Payment Redirect Controller
 *
 * PayTR iframe içinden merchant_ok_url olarak çağrılır.
 * Parent window'u /payment/success sayfasına yönlendirir.
 */
class PaymentRedirectController extends Controller
{
    /**
     * PayTR merchant_ok_url endpoint
     *
     * Bu sayfa iframe içinde açılır ve parent window'u yönlendirir.
     */
    public function redirect(Request $request)
    {
        \Log::info('🔄 PayTR redirect (merchant_ok_url)', [
            'query' => $request->query(),
            'user_agent' => $request->userAgent(),
        ]);

        // Intermediate redirect sayfasını göster
        return view('payment::front.payment-redirect');
    }
}
