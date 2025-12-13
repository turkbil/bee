<?php

declare(strict_types=1);

namespace Modules\Cart\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\App\Models\Order;

/**
 * OrderHistoryController
 *
 * Kullanıcının sipariş geçmişi yönetimi.
 * Fiziksel ürün, dijital ürün ve abonelik siparişlerini destekler.
 */
class OrderHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Kullanıcının tüm siparişlerini listele
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cart::front.my-orders', compact('orders'));
    }

    /**
     * Sipariş detay sayfası
     */
    public function show(int $orderId)
    {
        $user = Auth::user();

        $order = Order::where('order_id', $orderId)
            ->where('user_id', $user->id)
            ->with(['items', 'payments'])
            ->firstOrFail();

        // Ödeme bilgisi
        $payment = $order->payments()->latest()->first();

        return view('cart::front.order-detail', compact('order', 'payment'));
    }

    /**
     * Sipariş numarası ile detay göster
     */
    public function showByNumber(string $orderNumber)
    {
        $user = Auth::user();

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with(['items', 'payments'])
            ->firstOrFail();

        // Ödeme bilgisi
        $payment = $order->payments()->latest()->first();

        return view('cart::front.order-detail', compact('order', 'payment'));
    }
}
