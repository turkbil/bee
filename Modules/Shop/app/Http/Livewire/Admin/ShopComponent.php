<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('admin.layout')]
class ShopComponent extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('shop::admin.livewire.shop-component');
    }
}
