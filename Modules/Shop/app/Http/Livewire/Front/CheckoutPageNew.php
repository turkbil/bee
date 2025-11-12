<?php

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;
use Modules\Shop\App\Models\ShopCustomer;
use Modules\Shop\App\Models\ShopCustomerAddress;
use Modules\Shop\App\Models\ShopOrder;
use Modules\Shop\App\Models\ShopOrderItem;
use Modules\Payment\App\Models\PaymentMethod;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PayTRPaymentService;
use Modules\Payment\App\Services\PayTRDirectService;
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

    // Guest inline adres formu (Teslimat)
    public $shipping_address_line_1 = '';
    public $shipping_address_line_2 = '';
    public $shipping_city = '';
    public $shipping_district = '';
    public $shipping_postal_code = '';
    public $shipping_delivery_notes = '';

    // Agreements (Simplified - Single Checkbox)
    public $agree_all = false; // Combines KVKK, distance selling, preliminary info

    // Payment Method (OLD - deprecated)
    public $selectedPaymentMethodId = null;
    public $paymentMethods = [];
    public $selectedInstallment = 1; // Varsayılan tek çekim
    public $installmentFee = 0; // Taksit komisyonu

    // Payment Gateway (NEW - Settings based)
    public $selectedGateway = null; // 'paytr' veya 'bank_transfer'
    public $availableGateways = []; // Gateway listesi

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

    // Payment Iframe Modal
    public $showPaymentModal = false;
    public $paymentIframeUrl = '';

    // Card Form (PayTR Direct API)
    public $showCardForm = false;
    public $cc_owner = '';
    public $card_number = '';
    public $expiry_month = '';
    public $expiry_year = '';
    public $cvv = '';
    public $paytrPostUrl = '';
    public $paytrPostData = [];

    protected $listeners = [
        // 'cartUpdated' => 'loadCart', // ⚠️ KALDIRILDI - Sonsuz döngü önleme!
        'addressSelected' => 'handleAddressSelected',
    ];

    // İletişim bilgileri değiştiğinde customer'ı güncelle
    public function updated($propertyName)
    {
        // ⚠️ INFINITE LOOP GUARD: Metod içinde set edilen property'leri ignore et!
        $ignoreProperties = [
            'installmentFee',
            'creditCardFee',
            'grandTotal',
            'showPaymentModal',
            'paymentIframeUrl',
            'showCardForm',
            'paytrPostUrl',
            'paytrPostData',
            'subtotal',
            'taxAmount',
            'total',
            'itemCount'
        ];

        if (in_array($propertyName, $ignoreProperties)) {
            return; // Bu property'ler başka metodlar tarafından set ediliyor, ignore et!
        }

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

        // Ödeme yöntemi veya taksit değişirse komisyon hesapla
        if (in_array($propertyName, ['selectedPaymentMethodId', 'selectedInstallment'])) {
            $this->calculatePaymentFees();
        }
    }

    public function calculatePaymentFees()
    {
        if (!$this->selectedPaymentMethodId) {
            $this->installmentFee = 0;
            $this->creditCardFee = 0;
            $this->grandTotal = $this->total;
            return;
        }

        $paymentMethod = PaymentMethod::find($this->selectedPaymentMethodId);

        if (!$paymentMethod) {
            $this->installmentFee = 0;
            $this->creditCardFee = 0;
            $this->grandTotal = $this->total;
            return;
        }

        // Taksit ücreti hesapla
        if ($paymentMethod->supports_installment && $this->selectedInstallment > 1) {
            $this->installmentFee = $paymentMethod->calculateInstallmentFee($this->total, $this->selectedInstallment);
        } else {
            $this->installmentFee = 0;
            $this->selectedInstallment = 1; // Tek çekim
        }

        // Kredi kartı komisyonu kaldırıldı
        $this->creditCardFee = 0;

        // Genel toplam = KDV dahil toplam + taksit ücreti
        $this->grandTotal = $this->total + $this->installmentFee;
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
        \Log::info('🔵 MOUNT CALLED', ['user_id' => Auth::id()]);

        // BASİT TEST - Hata ayıklama için tüm işlemleri try-catch ile sarmala
        try {
            // ✅ Checkbox'ı sıfırla
            $this->agree_all = false;

            $this->loadCart();

            // Sepet boşsa sepet sayfasına yönlendir (modal gösterme, sayfa zaten boş UI gösteriyor)
            if (!$this->items || $this->items->count() === 0) {
                \Log::warning('⚠️ EMPTY CART - Redirecting to cart page');
                return redirect()->route('cart.index');
            }

            // Müşteri var mı kontrol et
            $this->loadOrCreateCustomer();

            // Ödeme yöntemlerini yükle (OLD - deprecated)
            $this->loadPaymentMethods();

            // Yeni gateway sistemi yükle
            $this->loadAvailableGateways();

            \Log::info('✅ MOUNT COMPLETED');
        } catch (\Exception $e) {
            \Log::error('❌ MOUNT ERROR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Checkout yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // İlk aktif ödeme yöntemini varsayılan olarak seç
        if ($this->paymentMethods->count() > 0 && !$this->selectedPaymentMethodId) {
            $this->selectedPaymentMethodId = $this->paymentMethods->first()->payment_method_id;
        }
    }

    /**
     * Yeni gateway sistemi - Settings tabanlı
     */
    public function loadAvailableGateways()
    {
        $gatewayManager = app(\Modules\Payment\App\Services\PaymentGatewayManager::class);
        $this->availableGateways = $gatewayManager->getAvailableGateways($this->total);

        // Tek gateway varsa otomatik seç
        if (count($this->availableGateways) === 1 && !$this->selectedGateway) {
            $this->selectedGateway = $this->availableGateways[0]['code'];
        }
    }

    public function loadCart()
    {
        $cartService = app(CartService::class);

        // Session ve customer bilgisi
        $sessionId = session()->getId();
        $customerId = auth()->check() ? auth()->id() : null;

        // Cart al
        $this->cart = $cartService->getCart($customerId, $sessionId);

        if ($this->cart) {
            $this->items = $this->cart->items()->where('is_active', true)->get();
            $this->itemCount = $this->items->sum('quantity');
        } else {
            $this->items = collect([]);
            $this->itemCount = 0;
        }

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

        // Kredi kartı komisyonu kaldırıldı
        $this->creditCardFee = 0;
        $this->grandTotal = $this->total;

        // ⚠️ Widget dispatch KALDIRıldı - Sonsuz döngü önleme!
        // Sadece sepet temizlendiğinde (proceedToPayment) dispatch edilecek
    }

    public function loadOrCreateCustomer()
    {
        \Log::info('🔍 loadOrCreateCustomer START', [
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'auth_email' => Auth::check() ? Auth::user()->email : null,
        ]);

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

            \Log::info('✅ Customer loaded/created', [
                'customer_id' => $this->customer->customer_id,
                'email' => $this->customer->email,
            ]);
        } else {
            // Misafir - session'da customer_id var mı?
            $sessionCustomerId = session('guest_customer_id');

            \Log::info('❌ Guest mode', ['session_customer_id' => $sessionCustomerId]);

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

            \Log::info('🔄 Loading default addresses', ['customer_id' => $this->customerId]);

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
            \Log::warning('⚠️ loadDefaultAddresses: No customerId!');
            return;
        }

        \Log::info('📍 loadDefaultAddresses START', ['customer_id' => $this->customerId]);

        // Varsayılan fatura adresi
        $defaultBilling = ShopCustomerAddress::where('customer_id', $this->customerId)
            ->billing()
            ->defaultBilling()
            ->first();

        if ($defaultBilling) {
            $this->billing_address_id = $defaultBilling->address_id;
            \Log::info('✅ Billing address loaded', ['address_id' => $defaultBilling->address_id]);
        } else {
            \Log::warning('❌ No default billing address found!');
        }

        // Varsayılan teslimat adresi
        $defaultShipping = ShopCustomerAddress::where('customer_id', $this->customerId)
            ->shipping()
            ->defaultShipping()
            ->first();

        if ($defaultShipping) {
            $this->shipping_address_id = $defaultShipping->address_id;
            \Log::info('✅ Shipping address loaded', ['address_id' => $defaultShipping->address_id]);
        } else {
            \Log::warning('❌ No default shipping address found!');
        }

        \Log::info('📍 loadDefaultAddresses END', [
            'billing_address_id' => $this->billing_address_id,
            'shipping_address_id' => $this->shipping_address_id,
        ]);
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
        \Log::info('🛒 submitOrder START', [
            'customerId' => $this->customerId,
            'billing_address_id' => $this->billing_address_id,
            'shipping_address_id' => $this->shipping_address_id,
            'contact_phone' => $this->contact_phone,
            'agree_all' => $this->agree_all,
            'selectedPaymentMethodId' => $this->selectedPaymentMethodId,
        ]);

        // Dynamic validation based on billing type
        $rules = [
            'contact_first_name' => 'required|string|max:255',
            'contact_last_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'agree_all' => 'accepted', // Single combined agreement
            'selectedPaymentMethodId' => 'required|exists:payment_methods,payment_method_id',
        ];

        // Login user için adres seçimi zorunlu
        if ($this->customerId) {
            $rules['billing_address_id'] = 'required';
            $rules['shipping_address_id'] = 'required';
            \Log::info('📍 Login user - Address validation required');
        } else {
            // Guest user için inline adres formu zorunlu
            $rules['shipping_address_line_1'] = 'required|string|max:255';
            $rules['shipping_city'] = 'required|string|max:100';
            $rules['shipping_district'] = 'required|string|max:100';
            \Log::info('📝 Guest user - Inline form validation');
        }

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

        try {
            $this->validate($rules, [
                'contact_first_name.required' => 'Ad zorunludur',
                'contact_last_name.required' => 'Soyad zorunludur',
                'contact_phone.required' => 'Telefon zorunludur',
                'billing_address_id.required' => 'Fatura adresi seçmelisiniz',
                'shipping_address_id.required' => 'Teslimat adresi seçmelisiniz',
                'shipping_address_line_1.required' => 'Adres zorunludur',
                'shipping_city.required' => 'İl zorunludur',
                'shipping_district.required' => 'İlçe zorunludur',
                'agree_all.accepted' => 'Ön Bilgilendirme Formu ve Mesafeli Satış Sözleşmesi\'ni kabul etmelisiniz',
                'billing_company_name.required' => 'Şirket ünvanı zorunludur',
                'billing_tax_office.required' => 'Vergi dairesi zorunludur',
                'billing_tax_number.required' => 'Vergi kimlik numarası zorunludur',
                'billing_tax_number.size' => 'Kurumsal için VKN 10 haneli, Bireysel için TCKN 11 haneli olmalıdır',
                'selectedPaymentMethodId.required' => 'Ödeme yöntemi seçmelisiniz',
            ]);

            \Log::info('✅ Validation passed!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Validation FAILED', [
                'errors' => $e->errors(),
                'rules' => array_keys($rules),
            ]);
            throw $e; // Re-throw to show errors to user
        }

        DB::beginTransaction();

        try {
            // Müşteri oluştur veya güncelle
            $customer = $this->createOrUpdateCustomer();

            // Guest için adres oluştur (login user için atlanır)
            if (!$this->customerId || !$this->shipping_address_id) {
                $shippingAddress = ShopCustomerAddress::create([
                    'customer_id' => $customer->customer_id,
                    'address_type' => 'shipping',
                    'address_line_1' => $this->shipping_address_line_1,
                    'address_line_2' => $this->shipping_address_line_2,
                    'city' => $this->shipping_city,
                    'district' => $this->shipping_district,
                    'postal_code' => $this->shipping_postal_code,
                    'delivery_notes' => $this->shipping_delivery_notes,
                    'is_default_shipping' => true,
                ]);

                $this->shipping_address_id = $shippingAddress->address_id;

                // Fatura adresi = Teslimat adresi (default)
                if ($this->billing_same_as_shipping) {
                    $billingAddress = ShopCustomerAddress::create([
                        'customer_id' => $customer->customer_id,
                        'address_type' => 'billing',
                        'address_line_1' => $this->shipping_address_line_1,
                        'address_line_2' => $this->shipping_address_line_2,
                        'city' => $this->shipping_city,
                        'district' => $this->shipping_district,
                        'postal_code' => $this->shipping_postal_code,
                        'is_default_billing' => true,
                    ]);

                    $this->billing_address_id = $billingAddress->address_id;
                }
            }

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

            // Payment kaydı oluştur
            $payment = Payment::create([
                'payment_method_id' => $this->selectedPaymentMethodId,
                'payable_type' => ShopOrder::class,
                'payable_id' => $order->order_id,
                'transaction_id' => 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6)),
                'amount' => $this->grandTotal,
                'currency' => 'TRY',
                'status' => 'pending',
                'installment_count' => $this->selectedInstallment,
                'installment_fee' => $this->installmentFee,
            ]);

            DB::commit();

            // Sepeti temizle (ödeme başlatıldı, geri dönüş yok)
            if ($this->cart) {
                $cartService = app(CartService::class);
                $cartService->clearCart($this->cart);
                $this->dispatch('cartUpdated');
            }

            // PayTR Direct API - Kart formu modal aç
            $paymentMethod = PaymentMethod::find($this->selectedPaymentMethodId);

            if ($paymentMethod && $paymentMethod->gateway === 'paytr') {
                // Ödeme bilgilerini session'a kaydet (kart formu submit'inde kullanılacak)
                session([
                    'pending_payment_id' => $payment->payment_id,
                    'pending_customer' => [
                        'name' => $customer->full_name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'address' => $shippingAddress->address_line_1 . ', ' . $shippingAddress->city,
                    ],
                    'pending_order_info' => [
                        'amount' => $this->grandTotal,
                        'description' => 'Sipariş No: ' . $order->order_number,
                        'items' => $this->items->map(function ($item) {
                            return [
                                'name' => $item->product->getTranslated('title', app()->getLocale()),
                                'price' => $item->unit_price,
                                'quantity' => $item->quantity,
                            ];
                        })->toArray(),
                    ],
                ]);

                // Kart formu modalını aç
                $this->showCardForm = true;
            } else {
                // Diğer ödeme yöntemleri için (Stripe vs.) - şimdilik redirect
                session()->flash('order_success', 'Siparişiniz başarıyla alındı! Sipariş numaranız: ' . $order->order_number);
                return redirect()->route('shop.order.success', $order->order_number);
            }

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
            'user_id' => Auth::id(), // ✅ Route'da auth middleware var, Auth::id() her zaman dolu
            'customer_type' => $this->billing_type === 'corporate' ? 'corporate' : 'individual',
        ]));

        return $customer;
    }

    /**
     * Test metodu - Livewire çalışıyor mu?
     */
    public function testButton()
    {
        \Log::info('🔥 TEST BUTTON CLICKED!');
        session()->flash('success', 'Test başarılı! Livewire çalışıyor.');
    }

    /**
     * Basit ödeme - Yeni sayfaya yönlendir
     */
    public function testPayment()
    {
        \Log::info('🧪 TEST PAYMENT START', [
            'user_id' => Auth::id(),
            'agree_all' => $this->agree_all ?? false,
            'items_count' => $this->items ? $this->items->count() : 0,
            'grandTotal' => $this->grandTotal
        ]);

        // TEST MOD - Validation KAPALI, direkt yönlendir
        try {
            // Basit sipariş numarası oluştur (ALFANUMERIK - PayTR kuralı!)
            // Format: T{tenant}TEST{timestamp}{random}
            $orderNumber = 'T' . tenant('id') . 'TEST' . date('YmdHis') . strtoupper(substr(md5(uniqid()), 0, 6));

            // Fiyat bilgilerini session'a kaydet
            session([
                'test_payment_amount' => $this->grandTotal,
                'test_payment_subtotal' => $this->subtotal,
                'test_payment_tax' => $this->taxAmount,
                'test_payment_item_count' => $this->itemCount,
                'last_order_number' => $orderNumber, // Ödeme başarılı sayfası için
            ]);

            // Sepeti temizle (ödeme başlıyor)
            if ($this->cart) {
                $cartService = app(CartService::class);
                $cartService->clearCart($this->cart);
                $this->dispatch('cartUpdated');
            }

            \Log::info('✅ TEST: Redirecting to payment page', [
                'order' => $orderNumber,
                'amount' => $this->grandTotal,
                'cart_cleared' => true
            ]);

            // Yeni ödeme sayfasına yönlendir
            return redirect()->route('shop.payment.page', ['orderNumber' => $orderNumber]);
        } catch (\Exception $e) {
            \Log::error('❌ TEST PAYMENT ERROR: ' . $e->getMessage());
            session()->flash('error', 'Test hatası: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Ödemeye Geç - PayTR iframe modalını aç
     */
    public function proceedToPayment()
    {
        \Log::info('💳 proceedToPayment START');

        // Önce validation yap
        $rules = [
            'contact_first_name' => 'required|string|max:255',
            'contact_last_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'agree_all' => 'accepted',
            'selectedPaymentMethodId' => 'required|exists:payment_methods,payment_method_id',
            'selectedGateway' => 'nullable|string|in:paytr,bank_transfer', // Yeni gateway sistemi
        ];

        // Adres kontrolü
        if ($this->customerId) {
            $rules['billing_address_id'] = 'required';
            $rules['shipping_address_id'] = 'required';
        } else {
            $rules['shipping_address_line_1'] = 'required|string|max:255';
            $rules['shipping_city'] = 'required|string|max:100';
            $rules['shipping_district'] = 'required|string|max:100';
        }

        // Fatura tipi kontrolü
        if ($this->billing_type === 'corporate') {
            $rules['billing_company_name'] = 'required|string|max:255';
            $rules['billing_tax_office'] = 'required|string|max:255';
            $rules['billing_tax_number'] = 'required|string|size:10';
        }

        try {
            $this->validate($rules, [
                'contact_first_name.required' => 'Ad zorunludur',
                'contact_last_name.required' => 'Soyad zorunludur',
                'contact_phone.required' => 'Telefon zorunludur',
                'billing_address_id.required' => 'Fatura adresi seçmelisiniz',
                'shipping_address_id.required' => 'Teslimat adresi seçmelisiniz',
                'shipping_address_line_1.required' => 'Adres zorunludur',
                'shipping_city.required' => 'İl zorunludur',
                'shipping_district.required' => 'İlçe zorunludur',
                'agree_all.accepted' => 'Sözleşmeleri kabul etmelisiniz',
                'selectedPaymentMethodId.required' => 'Ödeme yöntemi seçmelisiniz',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Validation FAILED', ['errors' => $e->errors()]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            // Müşteri oluştur/güncelle
            $customer = $this->createOrUpdateCustomer();

            // Guest için adres oluştur
            if (!$this->customerId || !$this->shipping_address_id) {
                $shippingAddress = ShopCustomerAddress::create([
                    'customer_id' => $customer->customer_id,
                    'address_type' => 'shipping',
                    'address_line_1' => $this->shipping_address_line_1,
                    'address_line_2' => $this->shipping_address_line_2,
                    'city' => $this->shipping_city,
                    'district' => $this->shipping_district,
                    'postal_code' => $this->shipping_postal_code,
                    'delivery_notes' => $this->shipping_delivery_notes,
                    'is_default_shipping' => true,
                ]);

                $this->shipping_address_id = $shippingAddress->address_id;

                if ($this->billing_same_as_shipping) {
                    $billingAddress = ShopCustomerAddress::create([
                        'customer_id' => $customer->customer_id,
                        'address_type' => 'billing',
                        'address_line_1' => $this->shipping_address_line_1,
                        'address_line_2' => $this->shipping_address_line_2,
                        'city' => $this->shipping_city,
                        'district' => $this->shipping_district,
                        'postal_code' => $this->shipping_postal_code,
                        'is_default_billing' => true,
                    ]);

                    $this->billing_address_id = $billingAddress->address_id;
                }
            }

            // Adresleri al
            $billingAddress = ShopCustomerAddress::find($this->billing_address_id);
            $shippingAddress = ShopCustomerAddress::find($this->shipping_address_id);

            // Sipariş oluştur
            $order = ShopOrder::create([
                'tenant_id' => tenant('id'),
                'customer_id' => $customer->customer_id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
                'customer_name' => $customer->full_name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'customer_company' => $customer->company_name,
                'customer_tax_office' => $customer->tax_office,
                'customer_tax_number' => $customer->tax_number,
                'shipping_address' => $shippingAddress->address_line_1 . ($shippingAddress->address_line_2 ? ' ' . $shippingAddress->address_line_2 : ''),
                'shipping_city' => $shippingAddress->city,
                'shipping_district' => $shippingAddress->district,
                'shipping_postal_code' => $shippingAddress->postal_code,
                'notes' => $shippingAddress->delivery_notes,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => $this->grandTotal,
                'status' => 'pending',
                'payment_status' => 'pending',
                'agreed_kvkk' => $this->agree_all,
                'agreed_distance_selling' => $this->agree_all,
                'agreed_preliminary_info' => $this->agree_all,
                'agreed_marketing' => false,
            ]);

            // Sipariş kalemleri
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

            // Payment kaydı
            $payment = Payment::create([
                'payment_method_id' => $this->selectedPaymentMethodId,
                'payable_type' => ShopOrder::class,
                'payable_id' => $order->order_id,
                'transaction_id' => 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6)),
                'amount' => $this->grandTotal,
                'currency' => 'TRY',
                'status' => 'pending',
                'installment_count' => $this->selectedInstallment,
                'installment_fee' => $this->installmentFee,
            ]);

            DB::commit();

            // PayTR iframe token al
            $paymentMethod = PaymentMethod::find($this->selectedPaymentMethodId);

            if ($paymentMethod && $paymentMethod->gateway === 'paytr') {
                // PayTRIframeService kullan
                $iframeService = app(\Modules\Payment\App\Services\PayTRIframeService::class);

                $userInfo = [
                    'name' => $customer->full_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $shippingAddress->address_line_1 . ', ' . $shippingAddress->city,
                ];

                $orderInfo = [
                    'amount' => $this->grandTotal,
                    'description' => 'Sipariş No: ' . $order->order_number,
                    'items' => $this->items->map(function ($item) {
                        return [
                            'name' => $item->product->getTranslated('title', app()->getLocale()),
                            'price' => $item->unit_price,
                            'quantity' => $item->quantity,
                        ];
                    })->toArray(),
                ];

                $result = $iframeService->prepareIframePayment($payment, $userInfo, $orderInfo);

                if ($result['success']) {
                    // Sipariş numarasını session'a kaydet (PayTR callback için)
                    session(['last_order_number' => $order->order_number]);

                    // Sepeti temizle (ödeme başladı)
                    if ($this->cart) {
                        $cartService = app(CartService::class);
                        $cartService->clearCart($this->cart);
                        $this->dispatch('cartUpdated');
                    }

                    DB::commit();

                    // ✅ PayTR iframe modal aç
                    $this->paymentIframeUrl = $result['iframe_url'];
                    $this->showPaymentModal = true;

                    \Log::info('✅ PayTR iframe modal opened', [
                        'url' => $result['iframe_url'],
                        'order_number' => $order->order_number
                    ]);
                } else {
                    DB::rollBack();
                    session()->flash('error', 'Ödeme hazırlanamadı: ' . $result['message']);
                    \Log::error('❌ PayTR token failed', ['message' => $result['message']]);
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ proceedToPayment ERROR', ['message' => $e->getMessage()]);
            session()->flash('error', 'Sipariş oluşturulurken hata: ' . $e->getMessage());
        }
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->paymentIframeUrl = '';
    }

    /**
     * Kart formu submit - PayTR Direct API
     */
    public function submitCardPayment()
    {
        // Kart bilgileri validasyonu
        $this->validate([
            'cc_owner' => 'required|string|max:50',
            'card_number' => 'required|string|size:16',
            'expiry_month' => 'required|string|in:01,02,03,04,05,06,07,08,09,10,11,12',
            'expiry_year' => 'required|string|min:2|max:2',
            'cvv' => 'required|string|size:3',
        ], [
            'cc_owner.required' => 'Kart sahibi adı zorunludur',
            'card_number.required' => 'Kart numarası zorunludur',
            'card_number.size' => 'Kart numarası 16 haneli olmalıdır',
            'expiry_month.required' => 'Son kullanma ayı zorunludur',
            'expiry_year.required' => 'Son kullanma yılı zorunludur',
            'cvv.required' => 'CVV kodu zorunludur',
            'cvv.size' => 'CVV 3 haneli olmalıdır',
        ]);

        // Session'dan pending payment bilgilerini al
        $paymentId = session('pending_payment_id');
        $userInfo = session('pending_customer');
        $orderInfo = session('pending_order_info');

        if (!$paymentId || !$userInfo || !$orderInfo) {
            session()->flash('error', 'Ödeme bilgileri bulunamadı. Lütfen tekrar deneyin.');
            $this->showCardForm = false;
            return;
        }

        $payment = Payment::find($paymentId);

        if (!$payment) {
            session()->flash('error', 'Ödeme kaydı bulunamadı.');
            $this->showCardForm = false;
            return;
        }

        // Kart bilgileri
        $cardInfo = [
            'cc_owner' => $this->cc_owner,
            'card_number' => $this->card_number,
            'expiry_month' => $this->expiry_month,
            'expiry_year' => $this->expiry_year,
            'cvv' => $this->cvv,
        ];

        // PayTR Direct API servisi
        $directService = app(PayTRDirectService::class);
        $result = $directService->prepareDirectPayment($payment, $userInfo, $orderInfo, $cardInfo);

        if ($result['success']) {
            // POST URL ve Data'yı component'e al
            $this->paytrPostUrl = $result['post_url'];
            $this->paytrPostData = $result['post_data'];

            // Session temizle
            session()->forget(['pending_payment_id', 'pending_customer', 'pending_order_info']);

            // Frontend'de otomatik form submit yapılacak (view'da)
        } else {
            session()->flash('error', 'Ödeme hazırlanamadı: ' . $result['message']);
            $this->showCardForm = false;
        }
    }

    public function render()
    {
        return view('shop::livewire.front.checkout-page-new')
            ->layout('themes.ixtif.layouts.app');
    }
}
