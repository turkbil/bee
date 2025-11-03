<?php

namespace Modules\Shop\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Modules\Shop\App\Models\ShopOrder;

class OrderController
{
    /**
     * Sipariş onay sayfası (sipariş sonrası)
     */
    public function success(string $orderNumber)
    {
        $order = ShopOrder::where('order_number', $orderNumber)
            ->with(['items.product', 'customer'])
            ->firstOrFail();

        return view('shop::front.order-success', compact('order'));
    }
}
