<?php

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Shop\App\Services\ShopCartService;
use Modules\Shop\App\Models\ShopOrder;
use Modules\Shop\App\Models\ShopOrderItem;

class CheckoutPage extends Component
{
    public $cart;
    public $items;

    // Form Fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $company = '';
    public $tax_office = '';
    public $tax_number = '';
    public $address = '';
    public $city = '';
    public $district = '';
    public $postal_code = '';
    public $notes = '';

    // Agreements
    public $agree_kvkk = false;
    public $agree_distance_selling = false;
    public $agree_preliminary_info = false;
    public $agree_marketing = false;

    // Summary
    public $subtotal = 0;
    public $taxAmount = 0;
    public $total = 0;
    public $itemCount = 0;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();

        // Sepet boşsa sepet sayfasına yönlendir
        if (!$this->items || $this->items->count() === 0) {
            session()->flash('error', 'Sepetiniz boş. Lütfen ürün ekleyin.');
            return redirect()->route('shop.cart');
        }
    }

    public function loadCart()
    {
        $cartService = app(ShopCartService::class);

        $this->cart = $cartService->getCurrentCart();
        $this->items = $cartService->getItems();
        $this->itemCount = (int) ($this->cart->items_count ?? 0);

        // TRY cinsinden toplam hesapla
        $subtotalTRY = 0;

        foreach ($this->items as $item) {
            $exchangeRate = 1;

            // USD veya başka currency ise TRY'ye çevir
            if ($item->currency && $item->currency->code !== 'TRY') {
                $exchangeRate = $item->currency->exchange_rate ?? 1;
            }

            $subtotalTRY += ($item->subtotal ?? 0) * $exchangeRate;
        }

        $this->subtotal = $subtotalTRY;
        $taxRate = config('shop.tax_rate', 20) / 100;
        $this->taxAmount = $this->subtotal * $taxRate;
        $this->total = $this->subtotal + $this->taxAmount;
    }

    public function submitOrder()
    {
        // Validation
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'agree_kvkk' => 'accepted',
            'agree_distance_selling' => 'accepted',
            'agree_preliminary_info' => 'accepted',
        ], [
            'name.required' => 'Ad Soyad zorunludur',
            'email.required' => 'E-posta zorunludur',
            'email.email' => 'Geçerli bir e-posta adresi giriniz',
            'phone.required' => 'Telefon zorunludur',
            'address.required' => 'Adres zorunludur',
            'city.required' => 'Şehir zorunludur',
            'agree_kvkk.accepted' => 'KVKK Aydınlatma Metni\'ni kabul etmelisiniz',
            'agree_distance_selling.accepted' => 'Mesafeli Satış Sözleşmesi\'ni kabul etmelisiniz',
            'agree_preliminary_info.accepted' => 'Ön Bilgilendirme Formu\'nu kabul etmelisiniz',
        ]);

        // Create Order
        $order = ShopOrder::create([
            'tenant_id' => tenant('id'),
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'customer_name' => $this->name,
            'customer_email' => $this->email,
            'customer_phone' => $this->phone,
            'customer_company' => $this->company,
            'customer_tax_office' => $this->tax_office,
            'customer_tax_number' => $this->tax_number,
            'shipping_address' => $this->address,
            'shipping_city' => $this->city,
            'shipping_district' => $this->district,
            'shipping_postal_code' => $this->postal_code,
            'notes' => $this->notes,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'total' => $this->total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'agreed_kvkk' => $this->agree_kvkk,
            'agreed_distance_selling' => $this->agree_distance_selling,
            'agreed_preliminary_info' => $this->agree_preliminary_info,
            'agreed_marketing' => $this->agree_marketing,
        ]);

        // Create Order Items
        foreach ($this->items as $item) {
            $price = $item->unit_price;

            if ($item->currency && $item->currency->code !== 'TRY') {
                $exchangeRate = $item->currency->exchange_rate ?? 1;
                $price = $price * $exchangeRate;
            }

            ShopOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'unit_price' => $price,
                'subtotal' => $price * $item->quantity,
                'product_title' => $item->product->getTranslated('title', app()->getLocale()),
                'product_sku' => $item->product->sku,
            ]);
        }

        // Clear cart
        $cartService = app(ShopCartService::class);
        $cartService->clearCart();

        // Redirect to success page
        session()->flash('order_success', 'Siparişiniz başarıyla alındı! Sipariş numaranız: ' . $order->order_number);

        return redirect()->route('shop.index');
    }

    public function render()
    {
        return view('shop::livewire.front.checkout-page')
            ->layout('themes.ixtif.layouts.app');
    }
}
