<?php

namespace Modules\Shop\App\Http\Controllers\Front;

use Illuminate\Http\RedirectResponse;

class CheckoutRedirectController
{
    public function redirect(): RedirectResponse
    {
        return redirect()->route('cart.index');
    }
}
