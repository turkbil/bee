<?php

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Shop\App\Services\ShopCartService;
use Modules\Shop\App\Models\ShopCustomer;
use Modules\Shop\App\Models\ShopCustomerAddress;
use Modules\Shop\App\Models\ShopOrder;
use Modules\Shop\App\Models\ShopOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutPageNew extends Component
{
    public $cart;
    public $items;

    // Müşteri
    public $customer;
    public $customerId;

    // İletişim bilgileri
    public $contact_first_name = '';
    public $contact_last_name = '';
    public $contact_email = '';
    public $contact_phone = '';

    // Fatura bilgileri
    public $billing_type = 'individual'; // individual veya corporate
    public $billing_tax_number = ''; // TC (11 haneli) veya VKN (10 haneli)
    public $billing_company_name = '';
    public $billing_tax_office = '';

    // Fatura adresi
    public $billing_address_id;
    public $billing_same_as_shipping = true; // Varsayılan: Fatura adresi = Teslimat adresi

    // Teslimat adresi
    public $shipping_address_id;

    // Agreements (Simplified - Single Checkbox)
    public $agree_all = false; // Combines KVKK, distance selling, preliminary info

    // Summary
    public $subtotal = 0;
    public $taxAmount = 0;
    public $total = 0;
    public $creditCardFee = 0; // Kredi kartı komisyonu (%4,29)
    public $grandTotal = 0; // Komisyon dahil son toplam
    public $itemCount = 0;

    // Modal States (Hepsiburada Pattern)
    public $showShippingModal = false;
    public $showBillingModal = false; // Fatura bilgileri (vergi) modal
    public $showBillingAddressModal = false; // Fatura adresi modal

    protected $listeners = [
        'cartUpdated' => 'loadCart',
        'addressSelected' => 'handleAddressSelected',
    ];

    // İletişim bilgileri değiştiğinde customer'ı güncelle
    public function updated($propertyName)
    {
        // Sadece iletişim bilgileri değiştiğinde güncelle
        if (in_array($propertyName, ['contact_first_name', 'contact_last_name', 'contact_phone'])) {
            $this->updateCustomerInfo();
        }

        // Eğer "Fatura = Teslimat" checkbox'ı değişirse
        if ($propertyName === 'billing_same_as_shipping') {
            if ($this->billing_same_as_shipping && $this->shipping_address_id) {
                // Checkbox true → Fatura adresini teslimat adresi yap
                $this->billing_address_id = $this->shipping_address_id;
            }
        }
    }

    private function updateCustomerInfo()
    {
        if (!$this->customer) {
            return;
        }

        // Customer bilgilerini güncelle
        $this->customer->update([
            'first_name' => $this->contact_first_name,
            'last_name' => $this->contact_last_name,
            'phone' => $this->contact_phone,
        ]);
    }

    public function mount()
    {
        $this->loadCart();

        // Sepet boşsa sepet sayfasına yönlendir
        if (!$this->items || $this->items->count() === 0) {
            session()->flash('error', 'Sepetiniz boş. Lütfen ürün ekleyin.');
            return redirect()->route('shop.cart');
        }

        // Müşteri var mı kontrol et
        $this->loadOrCreateCustomer();
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

            if ($item->currency && $item->currency->code !== 'TRY') {
                $exchangeRate = $item->currency->exchange_rate ?? 1;
            }

            $subtotalTRY += ($item->subtotal ?? 0) * $exchangeRate;
        }

        $this->subtotal = $subtotalTRY;
        $taxRate = config('shop.tax_rate', 20) / 100;
        $this->taxAmount = $this->subtotal * $taxRate;
        $this->total = $this->subtotal + $this->taxAmount;

        // Kredi kartı komisyonu (%4,99)
        $this->creditCardFee = $this->total * 0.0499;
        $this->grandTotal = $this->total + $this->creditCardFee;
    }

    public function loadOrCreateCustomer()
    {
        // Kayıtlı kullanıcı var mı?
        if (Auth::check()) {
            // User name'i ad/soyad olarak ayır
            $fullName = Auth::user()->name ?? '';
            $nameParts = explode(' ', trim($fullName), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            $this->customer = ShopCustomer::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => Auth::user()->email,
                    'phone' => '',
                    'customer_type' => 'individual',
                    'billing_type' => 'individual',
                ]
            );
        } else {
            // Misafir - session'da customer_id var mı?
            $sessionCustomerId = session('guest_customer_id');

            if ($sessionCustomerId) {
                $this->customer = ShopCustomer::find($sessionCustomerId);
            }
        }

        if ($this->customer) {
            $this->customerId = $this->customer->customer_id;

            // Müşteri bilgilerini form'a doldur
            $this->contact_first_name = $this->customer->first_name;
            $this->contact_last_name = $this->customer->last_name;
            $this->contact_email = $this->customer->email;
            $this->contact_phone = $this->customer->phone;

            $this->billing_type = $this->customer->billing_type ?? 'individual';
            $this->billing_tax_number = $this->customer->tax_number;
            $this->billing_company_name = $this->customer->company_name;
            $this->billing_tax_office = $this->customer->tax_office;

            // Varsayılan adresleri yükle
            $this->loadDefaultAddresses();
        } else if (Auth::check()) {
            // Müşteri yok ama kullanıcı login - Customer oluştur
            $fullName = Auth::user()->name ?? '';
            $nameParts = explode(' ', trim($fullName), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            // Customer oluştur (telefon boş olabilir, ilk sipariş sırasında doldurulur)
            $this->customer = ShopCustomer::create([
                'user_id' => Auth::id(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => Auth::user()->email,
                'phone' => '', // Boş, kullanıcı girecek
                'customer_type' => 'individual',
                'billing_type' => 'individual',
            ]);

            $this->customerId = $this->customer->customer_id;

            // Form'a bilgileri doldur
            $this->contact_first_name = $firstName;
            $this->contact_last_name = $lastName;
            $this->contact_email = Auth::user()->email;
            // Telefon boş kalacak, kullanıcı girecek
        }
    }

    public function loadDefaultAddresses()
    {
        if (!$this->customerId) {
            return;
        }

        // Varsayılan fatura adresi
        $defaultBilling = ShopCustomerAddress::where('customer_id', $this->customerId)
            ->billing()
            ->defaultBilling()
            ->first();

        if ($defaultBilling) {
            $this->billing_address_id = $defaultBilling->address_id;
        }

        // Varsayılan teslimat adresi
        $defaultShipping = ShopCustomerAddress::where('customer_id', $this->customerId)
            ->shipping()
            ->defaultShipping()
            ->first();

        if ($defaultShipping) {
            $this->shipping_address_id = $defaultShipping->address_id;
        }
    }

    public function openBillingModal()
    {
        $this->showBillingModal = true;
    }

    public function closeBillingModal()
    {
        $this->showBillingModal = false;
    }

    public function openShippingModal()
    {
        $this->showShippingModal = true;
    }

    public function closeShippingModal()
    {
        $this->showShippingModal = false;
    }

    public function openBillingAddressModal()
    {
        $this->showBillingAddressModal = true;
    }

    public function closeBillingAddressModal()
    {
        $this->showBillingAddressModal = false;
    }

    public function handleAddressSelected($addressId, $addressType)
    {
        if ($addressType === 'billing') {
            $this->billing_address_id = $addressId;
            $this->showBillingAddressModal = false; // Modal'ı kapat
        } elseif ($addressType === 'shipping') {
            $this->shipping_address_id = $addressId;
            $this->showShippingModal = false; // Modal'ı kapat

            // Eğer "Fatura = Teslimat" seçiliyse, fatura adresini de güncelle
            if ($this->billing_same_as_shipping) {
                $this->billing_address_id = $addressId;
            }
        }
    }

    public function submitOrder()
    {
        // Dynamic validation based on billing type
        $rules = [
            'contact_first_name' => 'required|string|max:255',
            'contact_last_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'billing_address_id' => 'required',
            'shipping_address_id' => 'required',
            'agree_all' => 'accepted', // Single combined agreement
        ];

        // Fatura tipi kontrolü
        if ($this->billing_type === 'corporate') {
            // Kurumsal: Firma adı + VKN + Vergi dairesi ZORUNLU
            $rules['billing_company_name'] = 'required|string|max:255';
            $rules['billing_tax_office'] = 'required|string|max:255';
            $rules['billing_tax_number'] = 'required|string|size:10'; // VKN 10 haneli
        } else {
            // Bireysel: TCKN OPSİYONEL ama girilirse 11 haneli olmalı
            if (!empty($this->billing_tax_number)) {
                $rules['billing_tax_number'] = 'nullable|string|size:11'; // TCKN 11 haneli
            }
        }

        $this->validate($rules, [
            'contact_first_name.required' => 'Ad zorunludur',
            'contact_last_name.required' => 'Soyad zorunludur',
            'contact_phone.required' => 'Telefon zorunludur',
            'billing_address_id.required' => 'Fatura adresi seçmelisiniz',
            'shipping_address_id.required' => 'Teslimat adresi seçmelisiniz',
            'agree_all.accepted' => 'Ön Bilgilendirme Formu ve Mesafeli Satış Sözleşmesi\'ni kabul etmelisiniz',
            'billing_company_name.required' => 'Şirket ünvanı zorunludur',
            'billing_tax_office.required' => 'Vergi dairesi zorunludur',
            'billing_tax_number.required' => 'Vergi kimlik numarası zorunludur',
            'billing_tax_number.size' => 'Kurumsal için VKN 10 haneli, Bireysel için TCKN 11 haneli olmalıdır',
        ]);

        DB::beginTransaction();

        try {
            // Müşteri oluştur veya güncelle
            $customer = $this->createOrUpdateCustomer();

            // Adresleri al (snapshot için)
            $billingAddress = ShopCustomerAddress::find($this->billing_address_id);
            $shippingAddress = ShopCustomerAddress::find($this->shipping_address_id);

            // Sipariş oluştur
            $order = ShopOrder::create([
                'tenant_id' => tenant('id'),
                'customer_id' => $customer->customer_id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),

                // İletişim snapshot
                'customer_name' => $customer->full_name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'customer_company' => $customer->company_name,
                'customer_tax_office' => $customer->tax_office,
                'customer_tax_number' => $customer->tax_number,

                // Teslimat snapshot
                'shipping_address' => $shippingAddress->address_line_1 . ($shippingAddress->address_line_2 ? ' ' . $shippingAddress->address_line_2 : ''),
                'shipping_city' => $shippingAddress->city,
                'shipping_district' => $shippingAddress->district,
                'shipping_postal_code' => $shippingAddress->postal_code,

                'notes' => $shippingAddress->delivery_notes,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'shipping_cost' => 0, // Kargo ücreti yok
                'discount_amount' => 0, // İndirim yok
                'total_amount' => $this->grandTotal, // Kredi kartı komisyonu dahil
                'status' => 'pending',
                'payment_status' => 'pending',

                'agreed_kvkk' => $this->agree_all,
                'agreed_distance_selling' => $this->agree_all,
                'agreed_preliminary_info' => $this->agree_all,
                'agreed_marketing' => false, // Marketing removed from combined checkbox
            ]);

            // Sipariş kalemlerini oluştur
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

            // Sepeti temizle
            $cartService = app(ShopCartService::class);
            $cartService->clearCart();

            DB::commit();

            session()->flash('order_success', 'Siparişiniz başarıyla alındı! Sipariş numaranız: ' . $order->order_number);

            return redirect()->route('shop.index');

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    private function createOrUpdateCustomer()
    {
        $data = [
            'first_name' => $this->contact_first_name,
            'last_name' => $this->contact_last_name,
            'email' => $this->contact_email,
            'phone' => $this->contact_phone,
            'billing_type' => $this->billing_type,
            'tax_number' => $this->billing_tax_number,
            'company_name' => $this->billing_company_name,
            'tax_office' => $this->billing_tax_office,
            'accepts_marketing' => false, // Marketing removed from simplified checkout
        ];

        if ($this->customer) {
            $this->customer->update($data);
            return $this->customer;
        }

        // Yeni müşteri oluştur
        $customer = ShopCustomer::create(array_merge($data, [
            'user_id' => Auth::id(),
            'customer_type' => $this->billing_type === 'corporate' ? 'corporate' : 'individual',
        ]));

        // Misafir için session'a kaydet
        if (!Auth::check()) {
            session(['guest_customer_id' => $customer->customer_id]);
        }

        return $customer;
    }

    public function render()
    {
        return view('shop::livewire.front.checkout-page-new')
            ->layout('themes.ixtif.layouts.app');
    }
}
