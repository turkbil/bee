<?php

namespace Modules\Cart\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Cart\App\Services\CartService;
use Modules\Cart\App\Models\Address;
use Modules\Cart\App\Models\BillingProfile;
use Modules\Cart\App\Models\Order;
use Modules\Cart\App\Models\OrderItem;
use Modules\Payment\App\Models\PaymentMethod;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PayTRPaymentService;
use Modules\Payment\App\Services\PayTRDirectService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CheckoutPage extends Component
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

    // Fatura Profili (Yeni Sistem)
    public $billing_profile_id;
    public $billingProfiles = []; // Kullanıcının fatura profilleri

    // Adresler
    public $userAddresses = []; // Kullanıcının adresleri

    // Yeni Fatura Profili Formu
    public $edit_billing_profile_id = null; // Edit mode için
    public $new_billing_profile_title = '';
    public $new_billing_profile_type = 'individual';
    public $new_billing_profile_identity_number = '';
    public $new_billing_profile_company_name = '';
    public $new_billing_profile_tax_number = '';
    public $new_billing_profile_tax_office = '';

    // Eski property'ler (Livewire state uyumluluğu için - deprecated)
    public $billing_type = 'individual';
    public $billing_tax_number = '';
    public $billing_company_name = '';
    public $billing_tax_office = '';

    // Fatura adresi
    public $billing_address_id;
    public $billing_same_as_shipping = true;

    // Teslimat adresi
    public $shipping_address_id;

    // Guest inline adres formu (Teslimat)
    public $shipping_address_line_1 = '';
    public $shipping_address_line_2 = '';
    public $shipping_city = '';
    public $shipping_district = '';
    public $shipping_postal_code = '';
    public $shipping_delivery_notes = '';

    // Yeni Adres Formu (Shipping - inline)
    public $edit_address_id = null; // Edit mode için address ID
    public $new_address_title = '';
    public $new_address_phone = '';
    public $new_address_line = '';
    public $new_address_city = '';
    public $new_address_district = '';
    public $new_address_postal = '';

    // Yeni Adres Formu (Billing - inline)
    public $edit_billing_address_id = null; // Edit mode için address ID
    public $new_billing_address_title = '';
    public $new_billing_address_phone = '';
    public $new_billing_address_line = '';
    public $new_billing_address_city = '';
    public $new_billing_address_district = '';
    public $new_billing_address_postal = '';

    // Şehir/İlçe listeleri
    public $cities = [];
    public $districts = [];
    public $billingDistricts = [];

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
    public $requiresShipping = true; // Sepette fiziksel ürün var mı?

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
        \Log::info('🟢 UPDATED CALLED', ['property' => $propertyName, 'value' => $this->$propertyName ?? 'null']);

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
            \Log::info('⚪ Ignored property', ['property' => $propertyName]);
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

        // 🔵 Seçilen fatura profili varsayılan olarak kaydet
        if ($propertyName === 'billing_profile_id' && $this->billing_profile_id && auth()->check()) {
            BillingProfile::where('user_id', auth()->id())->update(['is_default' => false]);
            BillingProfile::where('billing_profile_id', $this->billing_profile_id)->update(['is_default' => true]);
            \Log::info('✅ Default billing profile updated', ['profile_id' => $this->billing_profile_id]);
        }

        // 🔵 Seçilen teslimat adresi varsayılan olarak kaydet
        if ($propertyName === 'shipping_address_id' && $this->shipping_address_id && auth()->check()) {
            Address::where('user_id', auth()->id())->update(['is_default_shipping' => false]);
            Address::where('address_id', $this->shipping_address_id)->update(['is_default_shipping' => true]);
            \Log::info('✅ Default shipping address updated', ['address_id' => $this->shipping_address_id]);
        }

        // 🔵 Seçilen fatura adresi varsayılan olarak kaydet
        if ($propertyName === 'billing_address_id' && $this->billing_address_id && auth()->check()) {
            Address::where('user_id', auth()->id())->update(['is_default_billing' => false]);
            Address::where('address_id', $this->billing_address_id)->update(['is_default_billing' => true]);
            \Log::info('✅ Default billing address updated', ['address_id' => $this->billing_address_id]);
        }

        // Ödeme yöntemi veya taksit değişirse komisyon hesapla
        if (in_array($propertyName, ['selectedPaymentMethodId', 'selectedInstallment'])) {
            $this->calculatePaymentFees();
        }

        // İl seçilince ilçeleri yükle
        if ($propertyName === 'new_address_city') {
            \Log::info('🔵 City changed (shipping)', ['city' => $this->new_address_city]);
            $this->districts = $this->getDistrictsByCity($this->new_address_city);
            $this->new_address_district = '';
            \Log::info('✅ Districts loaded', ['count' => count($this->districts)]);
        }

        if ($propertyName === 'new_billing_address_city') {
            \Log::info('🔵 City changed (billing)', ['city' => $this->new_billing_address_city]);
            $this->billingDistricts = $this->getDistrictsByCity($this->new_billing_address_city);
            $this->new_billing_address_district = '';
            \Log::info('✅ Billing districts loaded', ['count' => count($this->billingDistricts)]);
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

        // User bilgilerini güncelle (sadece telefon - ad/soyad users tablosunda name olarak tutulur)
        if (!empty($this->contact_phone) && $this->customer->phone !== $this->contact_phone) {
            $this->customer->phone = $this->contact_phone;
            $this->customer->save();
        }
    }

    public function mount()
    {
        \Log::info('🔵 MOUNT CALLED', ['user_id' => Auth::id()]);

        try {
            $this->agree_all = false;

            // Subscription plan parametresi varsa sepete ekle
            if (request()->has('plan') && request()->has('cycle')) {
                // 🏢 Corporate: users parametresi varsa quantity ve user ID'leri sakla
                $quantity = 1;
                $targetUserIds = null;
                if (request()->has('users')) {
                    $userIds = array_filter(array_map('intval', explode(',', request('users'))));
                    $quantity = count($userIds);
                    $targetUserIds = array_values($userIds); // Re-index array
                }
                $this->addSubscriptionToCart(request('plan'), request('cycle'), $quantity, $targetUserIds);
                // 🔥 FIX: Redirect yapma, cart'ı yeniden yükle (yoksa totaller güncellenmiyor)
                // URL parametrelerini temizlemek için JavaScript redirect kullanılacak (view'da)
            }

            $this->loadCart();

            // 🛒 SEPET BOŞ İSE CART SAYFASINA YÖNLENDİR
            if (!$this->cart || $this->items->isEmpty()) {
                \Log::warning('⚠️ Checkout: Sepet boş, cart sayfasına yönlendiriliyor');
                session()->flash('warning', 'Sepetiniz boş. Lütfen ürün ekleyiniz.');
                return redirect()->route('cart.index');
            }

            // 🔥 ITEM'LARDAN DİREKT HESAPLA - Cart tablosuna güvenme!
            $this->subtotal = $this->items->sum('subtotal');
            $this->taxAmount = $this->items->sum('tax_amount');
            $this->total = $this->subtotal + $this->taxAmount;
            $this->grandTotal = $this->total;

            // Cart tablosunu da güncelle (senkron tut)
            $this->cart->subtotal = $this->subtotal;
            $this->cart->tax_amount = $this->taxAmount;
            $this->cart->total = $this->total;
            $this->cart->items_count = $this->items->sum('quantity');
            $this->cart->save();

            \Log::info('💰 Checkout totals from items', [
                'cart_id' => $this->cart->cart_id,
                'subtotal' => $this->subtotal,
                'tax' => $this->taxAmount,
                'total' => $this->total,
            ]);

            // 🔥 Toplam 0 ise cart sayfasına yönlendir
            if ($this->total <= 0) {
                \Log::warning('⚠️ Checkout: Sepet toplamı 0', [
                    'cart_id' => $this->cart->cart_id,
                    'items_count' => $this->items->count(),
                ]);
                session()->flash('warning', 'Sepet toplamı hesaplanamadı. Lütfen tekrar deneyin.');
                return redirect()->route('cart.index');
            }

            $this->loadOrCreateCustomer();
            $this->loadBillingProfiles(); // Fatura profillerini yükle
            $this->loadAddresses(); // Adresleri yükle
            $this->loadPaymentMethods();
            $this->loadAvailableGateways();
            $this->loadCities();

            \Log::info('✅ MOUNT COMPLETED');
        } catch (\Exception $e) {
            \Log::error('❌ MOUNT ERROR: ' . $e->getMessage());
            session()->flash('error', 'Checkout yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Subscription plan'ı sepete ekle
     * @param int $planId Plan ID
     * @param string $cycleKey Dönem (monthly, yearly vb)
     * @param int $quantity Adet (kurumsal için üye sayısı)
     * @param array|null $targetUserIds 🏢 Kurumsal için: subscription açılacak user ID'leri
     */
    protected function addSubscriptionToCart($planId, $cycleKey, $quantity = 1, $targetUserIds = null)
    {
        try {
            $plan = \Modules\Subscription\App\Models\SubscriptionPlan::findOrFail($planId);
            $bridge = app(\Modules\Subscription\App\Services\SubscriptionCartBridge::class);

            if (!$bridge->canAddToCart($plan)) {
                return;
            }

            $cartService = app(CartService::class);
            $sessionId = session()->getId();
            $customerId = auth()->check() ? auth()->id() : null;
            $cart = $cartService->findOrCreateCart($customerId, $sessionId);

            // Diğer subscription'ları temizle
            $existingSubscriptions = $cart->items()
                ->where('cartable_type', 'Modules\Subscription\App\Models\SubscriptionPlan')
                ->get();

            foreach ($existingSubscriptions as $item) {
                $cartService->removeItem($item);
            }

            // Eski item'lar silindikten sonra toplamları sıfırla
            if ($existingSubscriptions->count() > 0) {
                $cart->refresh();
                $cart->recalculateTotals();
            }

            // Subscription ekle (quantity ile)
            $options = $bridge->prepareSubscriptionForCart($plan, $cycleKey, true);

            // 🏢 Kurumsal: target_user_ids varsa metadata'ya ekle
            if ($targetUserIds && is_array($targetUserIds) && count($targetUserIds) > 0) {
                $options['metadata'] = array_merge($options['metadata'] ?? [], [
                    'type' => 'corporate_bulk',
                    'target_user_ids' => $targetUserIds,
                ]);
                \Log::info('🏢 Corporate subscription: target_user_ids added', [
                    'target_user_ids' => $targetUserIds,
                    'quantity' => $quantity,
                ]);
            }

            $cartService->addItem($cart, $plan, $quantity, $options);

            // 🔥 FIX: Cart'ı refresh et ve toplamları yeniden hesapla
            $cart->refresh();
            $cart->recalculateTotals();
            $this->cart = $cart;

            \Log::info('✅ Subscription auto-added to cart', [
                'plan_id' => $planId,
                'cycle_key' => $cycleKey,
                'quantity' => $quantity,
                'has_target_users' => !empty($targetUserIds),
                'cart_total' => $cart->total,
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Subscription auto-add error: ' . $e->getMessage());
        }
    }

    /**
     * Şehir listesini yükle (Central DB'den)
     */
    public function loadCities()
    {
        try {
            // Central DB'den illeri çek
            $cities = DB::connection('central')
                ->table('cities')
                ->orderBy('name')
                ->pluck('name')
                ->toArray();

            $this->cities = $cities;
        } catch (\Exception $e) {
            \Log::error('❌ Error loading cities from central DB', ['error' => $e->getMessage()]);
            $this->cities = [];
        }
    }

    /**
     * Fatura profillerini yükle
     */
    public function loadBillingProfiles()
    {
        if (!$this->customerId) {
            return;
        }

        $this->billingProfiles = BillingProfile::where('user_id', $this->customerId)
            ->orderBy('title', 'asc')
            ->get();

        // Varsayılan profili seç
        $defaultProfile = $this->billingProfiles->where('is_default', true)->first();
        if ($defaultProfile) {
            $this->billing_profile_id = $defaultProfile->billing_profile_id;
            $this->syncBillingProfileToLegacy($defaultProfile);
        } elseif ($this->billingProfiles->count() > 0) {
            // Varsayılan yoksa ilkini seç
            $firstProfile = $this->billingProfiles->first();
            $this->billing_profile_id = $firstProfile->billing_profile_id;
            $this->syncBillingProfileToLegacy($firstProfile);
        }

        \Log::info('📋 Billing profiles loaded', [
            'count' => $this->billingProfiles->count(),
            'selected' => $this->billing_profile_id
        ]);
    }

    /**
     * Seçili fatura profilini al
     */
    public function getSelectedBillingProfile()
    {
        if (!$this->billing_profile_id) {
            return null;
        }
        return BillingProfile::find($this->billing_profile_id);
    }

    /**
     * Kullanıcı adreslerini yükle
     */
    public function loadAddresses()
    {
        if (!$this->customerId) {
            $this->userAddresses = collect([]);
            return;
        }

        // Sadece dolu adresleri getir (boş address_line_1 veya city olanları gösterme)
        $this->userAddresses = Address::where('user_id', $this->customerId)
            ->where(function($q) {
                $q->whereNotNull('address_line_1')
                  ->where('address_line_1', '!=', '')
                  ->whereNotNull('city')
                  ->where('city', '!=', '');
            })
            ->orderBy('title', 'asc')
            ->get();

        \Log::info('📍 Addresses loaded', [
            'count' => $this->userAddresses->count(),
            'addresses' => $this->userAddresses->pluck('address_id', 'title')->toArray(),
            'shipping_selected' => $this->shipping_address_id,
            'billing_selected' => $this->billing_address_id
        ]);
    }

    /**
     * Fatura profili seçildiğinde
     */
    public function selectBillingProfile($profileId)
    {
        $this->billing_profile_id = $profileId;
    }

    /**
     * Fatura profilini varsayılan yap
     */
    public function setDefaultBillingProfile($profileId)
    {
        if (!Auth::check()) {
            return;
        }

        // Önce tüm profillerin is_default'unu false yap
        BillingProfile::where('user_id', Auth::id())->update(['is_default' => false]);

        // Seçili profili default yap
        $profile = BillingProfile::where('user_id', Auth::id())->find($profileId);
        if ($profile) {
            $profile->update(['is_default' => true]);
            $this->billing_profile_id = $profileId;

            // Profilleri yeniden yükle
            $this->billingProfiles = BillingProfile::where('user_id', Auth::id())
                ->orderBy('title', 'asc')
                ->get();
        }
    }

    /**
     * Billing Profile'ı legacy property'lere sync et (backward compatibility)
     */
    private function syncBillingProfileToLegacy($profile)
    {
        if (!$profile) {
            return;
        }

        $this->billing_type = $profile->type; // 'individual' or 'corporate'

        if ($profile->isCorporate()) {
            $this->billing_company_name = $profile->company_name;
            $this->billing_tax_number = $profile->tax_number;
            $this->billing_tax_office = $profile->tax_office;
        } else {
            // Bireysel - legacy alanları temizle
            $this->billing_company_name = '';
            $this->billing_tax_number = '';
            $this->billing_tax_office = '';
        }
    }

    /**
     * Yeni fatura profili kaydet
     */
    public function saveNewBillingProfile()
    {
        $rules = [
            'new_billing_profile_type' => 'required|in:individual,corporate',
        ];

        $messages = [];

        if ($this->new_billing_profile_type === 'corporate') {
            // Kurumsal - Şirket ünvanı zorunlu, title şirket ünvanından alınacak
            $rules['new_billing_profile_company_name'] = 'required|string|max:255';
            $rules['new_billing_profile_tax_number'] = 'required|string|size:10';
            $rules['new_billing_profile_tax_office'] = 'required|string|max:255';
            $messages['new_billing_profile_company_name.required'] = 'Şirket ünvanı zorunludur';
            $messages['new_billing_profile_tax_number.required'] = 'Vergi kimlik numarası zorunludur';
            $messages['new_billing_profile_tax_number.size'] = 'VKN 10 haneli olmalıdır';
            $messages['new_billing_profile_tax_office.required'] = 'Vergi dairesi zorunludur';
        } else {
            // Bireysel - Title zorunlu
            $rules['new_billing_profile_title'] = 'required|string|max:100';
            $messages['new_billing_profile_title.required'] = 'Kayıt adı zorunludur';

            // TC opsiyonel ama girilirse 11 haneli
            if (!empty($this->new_billing_profile_identity_number)) {
                $rules['new_billing_profile_identity_number'] = 'string|size:11';
                $messages['new_billing_profile_identity_number.size'] = 'TC Kimlik No 11 haneli olmalıdır';
            }
        }

        $this->validate($rules, $messages);

        // Edit mode mu, yoksa yeni kayıt mı?
        if ($this->edit_billing_profile_id) {
            // UPDATE - Mevcut profili güncelle
            $profile = BillingProfile::where('billing_profile_id', $this->edit_billing_profile_id)
                ->where('user_id', $this->customerId)
                ->first();

            if (!$profile) {
                session()->flash('error', 'Profil bulunamadı.');
                return;
            }

            $profile->update([
                'title' => $this->new_billing_profile_type === 'corporate'
                    ? $this->new_billing_profile_company_name
                    : $this->new_billing_profile_title,
                'type' => $this->new_billing_profile_type,
                'identity_number' => $this->new_billing_profile_type === 'individual' ? $this->new_billing_profile_identity_number : null,
                'company_name' => $this->new_billing_profile_type === 'corporate' ? $this->new_billing_profile_company_name : null,
                'tax_number' => $this->new_billing_profile_type === 'corporate' ? $this->new_billing_profile_tax_number : null,
                'tax_office' => $this->new_billing_profile_type === 'corporate' ? $this->new_billing_profile_tax_office : null,
            ]);

            session()->flash('success', 'Fatura profili başarıyla güncellendi!');
            \Log::info('✅ Billing profile updated', ['profile_id' => $profile->billing_profile_id]);
        } else {
            // CREATE - Yeni profil oluştur
            $isFirst = BillingProfile::where('user_id', $this->customerId)->count() === 0;

            $profile = BillingProfile::create([
                'user_id' => $this->customerId,
                'title' => $this->new_billing_profile_type === 'corporate'
                    ? $this->new_billing_profile_company_name
                    : $this->new_billing_profile_title,
                'type' => $this->new_billing_profile_type,
                'identity_number' => $this->new_billing_profile_type === 'individual' ? $this->new_billing_profile_identity_number : null,
                'company_name' => $this->new_billing_profile_type === 'corporate' ? $this->new_billing_profile_company_name : null,
                'tax_number' => $this->new_billing_profile_type === 'corporate' ? $this->new_billing_profile_tax_number : null,
                'tax_office' => $this->new_billing_profile_type === 'corporate' ? $this->new_billing_profile_tax_office : null,
                'is_default' => $isFirst, // İlk profil varsayılan olsun
            ]);

            session()->flash('success', 'Fatura profili başarıyla kaydedildi!');
            \Log::info('✅ New billing profile created', ['profile_id' => $profile->billing_profile_id]);
        }

        // Profili seç
        $this->billing_profile_id = $profile->billing_profile_id;
        $this->syncBillingProfileToLegacy($profile);

        // Listeyi yenile
        $this->loadBillingProfiles();

        // Formu temizle
        $this->reset([
            'edit_billing_profile_id',
            'new_billing_profile_title',
            'new_billing_profile_type',
            'new_billing_profile_identity_number',
            'new_billing_profile_company_name',
            'new_billing_profile_tax_number',
            'new_billing_profile_tax_office'
        ]);
        $this->new_billing_profile_type = 'individual'; // Reset to default

        // Alpine'a formu kapat sinyali gönder
        $this->dispatch('billing-profile-saved', profileId: $profile->billing_profile_id);
    }

    /**
     * Fatura profilini düzenle (form verilerini yükle)
     */
    public function editBillingProfile($profileId)
    {
        $profile = BillingProfile::where('billing_profile_id', $profileId)
            ->where('user_id', $this->customerId)
            ->first();

        if (!$profile) {
            session()->flash('error', 'Profil bulunamadı.');
            return;
        }

        // Form verilerini yükle
        $this->edit_billing_profile_id = $profile->billing_profile_id;
        $this->new_billing_profile_title = $profile->title;
        $this->new_billing_profile_type = $profile->type;
        $this->new_billing_profile_identity_number = $profile->identity_number ?? '';
        $this->new_billing_profile_company_name = $profile->company_name ?? '';
        $this->new_billing_profile_tax_number = $profile->tax_number ?? '';
        $this->new_billing_profile_tax_office = $profile->tax_office ?? '';

        \Log::info('📝 Editing billing profile', ['profile_id' => $profileId]);
    }

    /**
     * Fatura profilini sil
     */
    public function deleteBillingProfile($profileId)
    {
        try {
            $profile = BillingProfile::where('billing_profile_id', $profileId)
                ->where('user_id', $this->customerId)
                ->first();

            if (!$profile) {
                session()->flash('error', 'Profil bulunamadı.');
                return;
            }

            // Silme öncesi durumları kaydet
            $wasDefault = $profile->is_default;
            $wasSelected = ($this->billing_profile_id == $profileId);

            // Edit edilen profil siliniyorsa, edit formunu kapat
            if ($this->edit_billing_profile_id == $profileId) {
                $this->edit_billing_profile_id = null;
                $this->reset([
                    'new_billing_profile_title',
                    'new_billing_profile_type',
                    'new_billing_profile_identity_number',
                    'new_billing_profile_company_name',
                    'new_billing_profile_tax_number',
                    'new_billing_profile_tax_office'
                ]);
                $this->new_billing_profile_type = 'individual';

                // Alpine'a formu kapat sinyali gönder
                $this->dispatch('close-billing-form');
            }

            $profile->delete();

            // Listeyi yenile
            $this->loadBillingProfiles();

            // Kalan profiller varsa otomatik atama yap
            if ($this->billingProfiles && count($this->billingProfiles) > 0) {
                $firstProfile = $this->billingProfiles->first();

                // Silinen varsayılan ise → İlk profili varsayılan yap
                if ($wasDefault) {
                    BillingProfile::where('billing_profile_id', $firstProfile->billing_profile_id)
                        ->update(['is_default' => 1]);
                    \Log::info('✅ New default profile set', ['profile_id' => $firstProfile->billing_profile_id]);
                }

                // Silinen seçili ise → İlk profili seçili yap
                if ($wasSelected) {
                    $this->billing_profile_id = $firstProfile->billing_profile_id;
                    \Log::info('✅ New selected profile set', ['profile_id' => $firstProfile->billing_profile_id]);
                }

                // Listeyi tekrar yenile (varsayılan değişti)
                $this->loadBillingProfiles();
            } else {
                // Hiç profil kalmadıysa seçimi kaldır
                $this->billing_profile_id = null;
            }

            session()->flash('success', 'Fatura profili başarıyla silindi.');
            \Log::info('✅ Billing profile deleted', ['profile_id' => $profileId, 'was_default' => $wasDefault, 'was_selected' => $wasSelected]);
        } catch (\Exception $e) {
            session()->flash('error', 'Silme işlemi başarısız oldu.');
            \Log::error('❌ Error deleting billing profile', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Adresi sil
     */
    public function deleteAddress($addressId)
    {
        try {
            $address = Address::where('address_id', $addressId)
                ->where('user_id', $this->customerId)
                ->first();

            if (!$address) {
                session()->flash('error', 'Adres bulunamadı.');
                return;
            }

            // Silinen adresin durumlarını kaydet
            $wasDefaultShipping = $address->is_default_shipping;
            $wasDefaultBilling = $address->is_default_billing;
            $wasSelectedShipping = ($this->shipping_address_id == $addressId);
            $wasSelectedBilling = ($this->billing_address_id == $addressId);

            // Adresi sil
            $address->delete();
            \Log::info('🗑️ Address deleted', [
                'address_id' => $addressId,
                'was_default_shipping' => $wasDefaultShipping,
                'was_default_billing' => $wasDefaultBilling,
                'was_selected_shipping' => $wasSelectedShipping,
                'was_selected_billing' => $wasSelectedBilling,
            ]);

            // ===== OTOMATİK ATAMA MANTIGI (Fatura Profili gibi) =====

            // SHIPPING ADDRESS OTOMASYONU
            if ($wasDefaultShipping || $wasSelectedShipping) {
                $firstShippingAddress = Address::where('user_id', $this->customerId)
                    ->shipping()
                    ->orderBy('title', 'asc')
                    ->first();

                if ($firstShippingAddress) {
                    // Varsayılan silinmişse → İlk kalan varsayılan olsun
                    if ($wasDefaultShipping) {
                        $firstShippingAddress->setAsDefaultShipping();
                        \Log::info('⭐ Auto-assigned default shipping', ['address_id' => $firstShippingAddress->address_id]);
                    }

                    // Seçili silinmişse → İlk kalan seçili olsun
                    if ($wasSelectedShipping) {
                        $this->shipping_address_id = $firstShippingAddress->address_id;
                        \Log::info('✅ Auto-selected shipping', ['address_id' => $firstShippingAddress->address_id]);
                    }
                } else {
                    // Hiç adres kalmamış
                    $this->shipping_address_id = null;
                    \Log::warning('❌ No shipping addresses left');
                }
            }

            // BILLING ADDRESS OTOMASYONU
            if ($wasDefaultBilling || $wasSelectedBilling) {
                $firstBillingAddress = Address::where('user_id', $this->customerId)
                    ->billing()
                    ->orderBy('title', 'asc')
                    ->first();

                if ($firstBillingAddress) {
                    // Varsayılan silinmişse → İlk kalan varsayılan olsun
                    if ($wasDefaultBilling) {
                        $firstBillingAddress->setAsDefaultBilling();
                        \Log::info('⭐ Auto-assigned default billing', ['address_id' => $firstBillingAddress->address_id]);
                    }

                    // Seçili silinmişse → İlk kalan seçili olsun
                    if ($wasSelectedBilling) {
                        $this->billing_address_id = $firstBillingAddress->address_id;
                        \Log::info('✅ Auto-selected billing', ['address_id' => $firstBillingAddress->address_id]);
                    }
                } else {
                    // Hiç adres kalmamış
                    $this->billing_address_id = null;
                    \Log::warning('❌ No billing addresses left');
                }
            }

            session()->flash('success', 'Adres başarıyla silindi.');
            $this->loadAddresses(); // Adres listesini yenile
        } catch (\Exception $e) {
            session()->flash('error', 'Silme işlemi başarısız oldu.');
            \Log::error('❌ Error deleting address', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Adresi varsayılan yap (star toggle)
     */
    public function setDefaultAddress($addressId, $type = 'shipping')
    {
        try {
            $address = Address::where('address_id', $addressId)
                ->where('user_id', $this->customerId)
                ->first();

            if (!$address) {
                session()->flash('error', 'Adres bulunamadı.');
                return;
            }

            // Varsayılan yap
            if ($type === 'shipping') {
                $address->setAsDefaultShipping();
                $this->shipping_address_id = $addressId; // Otomatik seç
                \Log::info('⭐ Default shipping address set', ['address_id' => $addressId]);
            } else {
                $address->setAsDefaultBilling();
                $this->billing_address_id = $addressId; // Otomatik seç
                \Log::info('⭐ Default billing address set', ['address_id' => $addressId]);
            }

            session()->flash('success', 'Varsayılan adres güncellendi.');
            $this->loadAddresses(); // Adres listesini yenile
        } catch (\Exception $e) {
            session()->flash('error', 'İşlem başarısız oldu.');
            \Log::error('❌ Error setting default address', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Adresi düzenle (form verilerini yükle)
     */
    public function editAddress($addressId, $type = 'shipping')
    {
        $address = Address::where('address_id', $addressId)
            ->where('user_id', $this->customerId)
            ->first();

        if (!$address) {
            session()->flash('error', 'Adres bulunamadı.');
            return;
        }

        if ($type === 'shipping') {
            // Teslimat adresi düzenleme - form verilerini yükle
            $this->edit_address_id = $address->address_id;
            $this->new_address_title = $address->title;
            $this->new_address_phone = $address->phone ?? '';
            $this->new_address_line = $address->address_line_1;
            $this->new_address_city = $address->city;
            $this->new_address_postal = $address->postal_code ?? '';

            // İlçeleri yükle
            $this->districts = $this->getDistrictsByCity($address->city);
            $this->new_address_district = $address->district;

            \Log::info('📝 Editing shipping address', ['address_id' => $addressId]);
        } else {
            // Fatura adresi düzenleme - form verilerini yükle
            $this->edit_billing_address_id = $address->address_id;
            $this->new_billing_address_title = $address->title;
            $this->new_billing_address_phone = $address->phone ?? '';
            $this->new_billing_address_line = $address->address_line_1;
            $this->new_billing_address_city = $address->city;
            $this->new_billing_address_postal = $address->postal_code ?? '';

            // İlçeleri yükle
            $this->billingDistricts = $this->getDistrictsByCity($address->city);
            $this->new_billing_address_district = $address->district;

            \Log::info('📝 Editing billing address', ['address_id' => $addressId]);
        }

        // Edit modunu aktif et (Alpine için flag)
        $this->dispatch('address-edit-mode', addressId: $addressId, type: $type);
    }

    /**
     * İl seçilince ilçeleri yükle (Teslimat)
     */
    public function loadShippingDistricts()
    {
        if (empty($this->new_address_city)) {
            $this->districts = [];
            return;
        }

        $this->districts = $this->getDistrictsByCity($this->new_address_city);
        $this->new_address_district = '';
    }

    /**
     * İl seçilince ilçeleri yükle (Fatura)
     */
    public function loadBillingDistricts()
    {
        if (empty($this->new_billing_address_city)) {
            $this->billingDistricts = [];
            return;
        }

        $this->billingDistricts = $this->getDistrictsByCity($this->new_billing_address_city);
        $this->new_billing_address_district = '';
    }

    /**
     * Şehre göre ilçe listesi (Central DB'den)
     */
    private function getDistrictsByCity($city)
    {
        try {
            // Central DB'den seçili ilin ilçelerini çek
            $districts = DB::connection('central')
                ->table('districts')
                ->join('cities', 'districts.city_id', '=', 'cities.id')
                ->where('cities.name', $city)
                ->orderBy('districts.name')
                ->pluck('districts.name')
                ->toArray();

            return $districts;
        } catch (\Exception $e) {
            \Log::error('❌ Error loading districts from central DB', [
                'city' => $city,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Yeni adres kaydet (inline form)
     */
    public function saveNewAddress($type = 'shipping')
    {
        if ($type === 'shipping') {
            $this->validate([
                'new_address_title' => 'required|string|max:100',
                'new_address_line' => 'required|string|max:500',
                'new_address_city' => 'required|string|max:100',
                'new_address_district' => 'required|string|max:100',
            ], [
                'new_address_title.required' => 'Adres adı zorunludur',
                'new_address_line.required' => 'Adres zorunludur',
                'new_address_city.required' => 'İl zorunludur',
                'new_address_district.required' => 'İlçe zorunludur',
            ]);

            // Edit mode mu, yoksa yeni kayıt mı?
            if ($this->edit_address_id) {
                // UPDATE - Mevcut adresi güncelle
                $address = Address::where('address_id', $this->edit_address_id)
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$address) {
                    session()->flash('error', 'Adres bulunamadı.');
                    return;
                }

                $address->update([
                    'title' => $this->new_address_title,
                    'phone' => $this->new_address_phone ?? $this->contact_phone ?? '',
                    'address_line_1' => $this->new_address_line,
                    'city' => $this->new_address_city,
                    'district' => $this->new_address_district,
                    'postal_code' => $this->new_address_postal,
                ]);

                session()->flash('success', 'Adres başarıyla güncellendi!');
                \Log::info('✅ Address updated', ['address_id' => $address->address_id]);
            } else {
                // CREATE - Yeni adres oluştur
                $address = Address::create([
                    'user_id' => auth()->id(),
                    'title' => $this->new_address_title,
                    'first_name' => $this->contact_first_name ?? '',
                    'last_name' => $this->contact_last_name ?? '',
                    'phone' => $this->new_address_phone ?? $this->contact_phone ?? '',
                    'address_line_1' => $this->new_address_line,
                    'city' => $this->new_address_city,
                    'district' => $this->new_address_district,
                    'postal_code' => $this->new_address_postal,
                    'address_type' => 'both',
                    'is_default_shipping' => Address::where('user_id', auth()->id())->count() === 0,
                    'is_default_billing' => Address::where('user_id', auth()->id())->count() === 0,
                ]);

                session()->flash('success', 'Adres başarıyla kaydedildi!');
                \Log::info('✅ New address created', ['address_id' => $address->address_id]);
            }

            $this->shipping_address_id = $address->address_id;

            // Fatura adresi teslimat ile aynıysa
            if ($this->billing_same_as_shipping) {
                $this->billing_address_id = $address->address_id;
            }

            // Form temizle
            $this->reset(['edit_address_id', 'new_address_title', 'new_address_phone', 'new_address_line', 'new_address_city', 'new_address_district', 'new_address_postal']);

            // Adres listesini yenile
            $this->loadAddresses();

            // Alpine'a formu kapat sinyali gönder
            $this->dispatch('address-saved', type: 'shipping', addressId: $address->address_id);

        } else {
            // Billing address
            $this->validate([
                'new_billing_address_title' => 'required|string|max:100',
                'new_billing_address_line' => 'required|string|max:500',
                'new_billing_address_city' => 'required|string|max:100',
                'new_billing_address_district' => 'required|string|max:100',
            ], [
                'new_billing_address_title.required' => 'Adres adı zorunludur',
                'new_billing_address_line.required' => 'Adres zorunludur',
                'new_billing_address_city.required' => 'İl zorunludur',
                'new_billing_address_district.required' => 'İlçe zorunludur',
            ]);

            // Edit mode mu, yoksa yeni kayıt mı?
            if ($this->edit_billing_address_id) {
                // UPDATE - Mevcut adresi güncelle
                $address = Address::where('address_id', $this->edit_billing_address_id)
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$address) {
                    session()->flash('error', 'Adres bulunamadı.');
                    return;
                }

                $address->update([
                    'title' => $this->new_billing_address_title,
                    'phone' => $this->new_billing_address_phone ?? $this->contact_phone ?? '',
                    'address_line_1' => $this->new_billing_address_line,
                    'city' => $this->new_billing_address_city,
                    'district' => $this->new_billing_address_district,
                    'postal_code' => $this->new_billing_address_postal,
                ]);

                session()->flash('success', 'Fatura adresi başarıyla güncellendi!');
                \Log::info('✅ Billing address updated', ['address_id' => $address->address_id]);
            } else {
                // CREATE - Yeni adres oluştur
                $address = Address::create([
                    'user_id' => auth()->id(),
                    'title' => $this->new_billing_address_title,
                    'first_name' => $this->contact_first_name ?? '',
                    'last_name' => $this->contact_last_name ?? '',
                    'phone' => $this->new_billing_address_phone ?? $this->contact_phone ?? '',
                    'address_line_1' => $this->new_billing_address_line,
                    'city' => $this->new_billing_address_city,
                    'district' => $this->new_billing_address_district,
                    'postal_code' => $this->new_billing_address_postal,
                    'address_type' => 'both',
                    'is_default_billing' => Address::where('user_id', auth()->id())->count() === 0,
                ]);

                session()->flash('success', 'Fatura adresi başarıyla kaydedildi!');
                \Log::info('✅ New billing address created', ['address_id' => $address->address_id]);
            }

            $this->billing_address_id = $address->address_id;

            // Form temizle
            $this->reset(['edit_billing_address_id', 'new_billing_address_title', 'new_billing_address_phone', 'new_billing_address_line', 'new_billing_address_city', 'new_billing_address_district', 'new_billing_address_postal']);

            // Adres listesini yenile
            $this->loadAddresses();

            // Alpine'a formu kapat sinyali gönder
            $this->dispatch('address-saved', type: 'billing', addressId: $address->address_id);
        }
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Havale/EFT kapalıysa bank_transfer ve manual gateway'leri filtrele
        if (!setting('bank_transfer_enabled')) {
            $this->paymentMethods = $this->paymentMethods->filter(function ($method) {
                return !in_array($method->gateway, ['bank_transfer', 'manual']);
            })->values();
        }

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

    /**
     * Load cart by ID (localStorage'dan gelen cart_id için)
     */
    public function loadCartById(?int $cartId = null)
    {
        \Log::info('🔄 CheckoutPage: loadCartById called', ['cart_id' => $cartId]);

        if ($cartId) {
            $this->cart = \Modules\Cart\App\Models\Cart::find($cartId);
        }

        // Fallback: Cart bulunamazsa session/customer ile dene
        if (!$this->cart) {
            $this->loadCart();
        } else {
            $this->loadCartData();
        }
    }

    /**
     * Load cart by session/customer
     */
    public function loadCart()
    {
        $cartService = app(CartService::class);

        // Session ve customer bilgisi
        $sessionId = session()->getId();
        $customerId = auth()->check() ? auth()->id() : null;

        // Cart al - ÖNCE mevcut cart'ı bul, YOKSA yeni oluştur
        $this->cart = $cartService->getCart($customerId, $sessionId);

        // Cart yoksa yeni oluştur (CartWidget ve CartPage ile tutarlı)
        if (!$this->cart) {
            \Log::info('🛒 CheckoutPage: Cart not found, creating new cart', [
                'session_id' => $sessionId,
                'customer_id' => $customerId,
            ]);
            $this->cart = $cartService->findOrCreateCart($customerId, $sessionId);
        }

        // 🔥 FIX: Cart'ı refresh et (güncel totalleri çek)
        if ($this->cart) {
            $this->cart->refresh();
        }

        $this->loadCartData();
    }

    /**
     * Load cart data (items, totals)
     */
    protected function loadCartData()
    {
        if ($this->cart) {
            $this->items = $this->cart->items()->where('is_active', true)->get();
            $this->itemCount = $this->items->sum('quantity');

            \Log::info('✅ CheckoutPage: Cart loaded', [
                'cart_id' => $this->cart->id,
                'items_count' => $this->items->count(),
            ]);
        } else {
            $this->items = collect([]);
            $this->itemCount = 0;

            \Log::warning('⚠️ CheckoutPage: Could not create cart');
        }

        // Kargo gereksinimi kontrolü - Herhangi bir item fiziksel mi?
        // Eğer tüm itemlar dijital (subscription vb.) ise kargo gerekmez
        $this->requiresShipping = $this->items->contains(function ($item) {
            return $item->requiresShipping();
        });

        \Log::info('📦 Shipping requirement', ['requires_shipping' => $this->requiresShipping]);

        // TRY cinsinden toplam hesapla
        $subtotalTRY = 0;

        foreach ($this->items as $item) {
            $exchangeRate = 1;

            if ($item->currency && $item->currency->code !== 'TRY') {
                $exchangeRate = $item->currency->exchange_rate ?? 1;
            }

            $subtotalTRY += ($item->subtotal ?? 0) * $exchangeRate;
        }

        // Cart'tan subtotal ve tax_amount al (item bazlı tax hesaplama)
        // 🔥 FIX: Cart'ı refresh et (cache'lenmiş olabilir, güncel totalleri al)
        $this->cart->refresh();

        $this->subtotal = (float) $this->cart->subtotal;
        $this->taxAmount = (float) $this->cart->tax_amount;
        $this->total = (float) $this->cart->total;

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

        // Auth middleware checkout'a giriş için login gerektirir
        if (!Auth::check()) {
            \Log::warning('❌ User not authenticated for checkout');
            return;
        }

        // User bilgilerini al
        $user = Auth::user();
        $this->customer = $user;
        $this->customerId = $user->id;

        // Form'a bilgileri doldur
        $this->contact_first_name = $user->name ?? '';
        $this->contact_last_name = $user->surname ?? '';
        $this->contact_email = $user->email;
        $this->contact_phone = $user->phone ?? '';

        // Billing type varsayılan
        $this->billing_type = 'individual';

        \Log::info('✅ User loaded for checkout', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Varsayılan adresleri yükle
        $this->loadDefaultAddresses();
    }

    public function loadDefaultAddresses()
    {
        if (!$this->customerId) {
            \Log::warning('⚠️ loadDefaultAddresses: No user_id!');
            return;
        }

        \Log::info('📍 loadDefaultAddresses START', ['user_id' => $this->customerId]);

        // Varsayılan fatura adresi (sadece dolu adresler)
        $defaultBilling = Address::where('user_id', $this->customerId)
            ->billing()
            ->defaultBilling()
            ->where(function($q) {
                $q->whereNotNull('address_line_1')
                  ->where('address_line_1', '!=', '')
                  ->whereNotNull('city')
                  ->where('city', '!=', '');
            })
            ->first();

        if ($defaultBilling) {
            $this->billing_address_id = $defaultBilling->address_id;
            \Log::info('✅ Billing address loaded', ['address_id' => $defaultBilling->address_id]);
        } else {
            \Log::warning('❌ No default billing address found!');
        }

        // Varsayılan teslimat adresi (sadece dolu adresler)
        $defaultShipping = Address::where('user_id', $this->customerId)
            ->shipping()
            ->defaultShipping()
            ->where(function($q) {
                $q->whereNotNull('address_line_1')
                  ->where('address_line_1', '!=', '')
                  ->whereNotNull('city')
                  ->where('city', '!=', '');
            })
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

        // Adres seçimi - sadece fiziksel ürün varsa teslimat zorunlu
        if ($this->customerId) {
            // Fiziksel ürün varsa teslimat adresi zorunlu
            if ($this->requiresShipping) {
                $rules['shipping_address_id'] = 'required';
                // Kurumsal müşteri için fatura adresi zorunlu (bireysel için opsiyonel)
                if (!$this->billing_same_as_shipping && $this->billing_type === 'corporate') {
                    $rules['billing_address_id'] = 'required';
                }
            } else {
                // Dijital ürün - kurumsal için fatura adresi zorunlu, bireysel için opsiyonel
                if ($this->billing_type === 'corporate') {
                    $rules['billing_address_id'] = 'required';
                }
            }
            \Log::info('📍 Login user - Address validation', ['requires_shipping' => $this->requiresShipping, 'billing_type' => $this->billing_type]);
        } else {
            // Guest user için inline adres formu zorunlu (fiziksel ürünler için)
            if ($this->requiresShipping) {
                $rules['shipping_address_line_1'] = 'required|string|max:255';
                $rules['shipping_city'] = 'required|string|max:100';
                $rules['shipping_district'] = 'required|string|max:100';
            }
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

            // Login user için adres kontrolü
            if ($this->customerId) {
                if ($this->requiresShipping) {
                    // Fiziksel ürün → Her iki adres zorunlu
                    if (!$this->shipping_address_id || !$this->billing_address_id) {
                        throw new \Exception('Fiziksel ürünler için hem fatura hem teslimat adresi seçmelisiniz.');
                    }
                } else {
                    // Dijital ürün (abonelik) → Sadece fatura adresi zorunlu
                    if (!$this->billing_address_id) {
                        throw new \Exception('Lütfen fatura adresi seçiniz.');
                    }
                }
            }

            // Guest için adres oluştur (login user için atlanır)
            if (!$this->customerId) {
                // Guest user için inline form ile adres oluştur
                $shippingAddress = Address::create([
                    'user_id' => $customer->id,
                    'address_type' => 'shipping',
                    'first_name' => $this->contact_first_name,
                    'last_name' => $this->contact_last_name,
                    'phone' => $this->contact_phone,
                    'email' => $this->contact_email,
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
                    $billingAddress = Address::create([
                        'user_id' => $customer->id,
                        'address_type' => 'billing',
                        'first_name' => $this->contact_first_name,
                        'last_name' => $this->contact_last_name,
                        'phone' => $this->contact_phone,
                        'email' => $this->contact_email,
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

            // Fatura adresi = Teslimat adresi ise, otomatik ata
            if ($this->billing_same_as_shipping && $this->shipping_address_id) {
                $this->billing_address_id = $this->shipping_address_id;
            }

            // Adresleri al (snapshot için)
            $billingAddress = Address::find($this->billing_address_id);
            $shippingAddress = Address::find($this->shipping_address_id);

            // Sipariş oluştur
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => Order::generateOrderNumber(),

                // İletişim snapshot
                'customer_name' => $this->contact_first_name . ' ' . $this->contact_last_name,
                'customer_email' => $this->contact_email,
                'customer_phone' => $this->contact_phone,
                'customer_company' => $this->billing_company_name,
                'customer_tax_office' => $this->billing_tax_office,
                'customer_tax_number' => $this->billing_tax_number,

                // Adres snapshot (JSON)
                'billing_address' => $billingAddress ? $billingAddress->toSnapshot() : null,
                'shipping_address' => $shippingAddress ? $shippingAddress->toSnapshot() : null,

                'customer_notes' => $shippingAddress->delivery_notes ?? null,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => $this->grandTotal,
                'currency' => 'TRY',
                'status' => 'pending',
                'payment_status' => 'pending',
                'requires_shipping' => true,

                'agreed_terms' => $this->agree_all,
                'agreed_privacy' => $this->agree_all,
                'agreed_marketing' => false,

                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Sipariş kalemlerini oluştur
            foreach ($this->items as $item) {
                OrderItem::createFromCartItem($item, $order->order_id);
            }

            // Payment kaydı oluştur
            $payment = Payment::create([
                'payment_method_id' => $this->selectedPaymentMethodId,
                'payable_type' => Order::class,
                'payable_id' => $order->order_id,
                'gateway_transaction_id' => 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6)),
                'amount' => $this->grandTotal,
                'currency' => 'TRY',
                'exchange_rate' => 1,
                'amount_in_base_currency' => $this->grandTotal,
                'status' => 'pending',
                'installment_count' => $this->selectedInstallment,
                'installment_fee' => $this->installmentFee,
            ]);

            DB::commit();

            // ⚠️ SEPET TEMİZLENMEYECEK - Sadece ödeme başarılı olunca temizlenecek
            // PayTR callback başarı dönünce sepet temizlenecek

            // PayTR Direct API - Kart formu modal aç
            $paymentMethod = PaymentMethod::find($this->selectedPaymentMethodId);

            if ($paymentMethod && $paymentMethod->gateway === 'paytr') {
                // Dijital ürün için shipping yoksa billing kullan
                $addressForPayment = $shippingAddress ?? $billingAddress;

                // PayTR için adres zorunlu - boş olamaz!
                $paytrAddress = 'Türkiye'; // Fallback
                if ($addressForPayment) {
                    $addr = trim($addressForPayment->address_line_1 ?? '');
                    $city = trim($addressForPayment->city ?? '');
                    $district = trim($addressForPayment->district ?? '');

                    if (!empty($addr) && !empty($city)) {
                        $paytrAddress = $addr;
                        if (!empty($district)) {
                            $paytrAddress .= ', ' . $district;
                        }
                        $paytrAddress .= ', ' . $city;
                    }
                }

                \Log::info('💳 PayTR Session Hazırlanıyor', [
                    'billing_address_id' => $this->billing_address_id,
                    'shipping_address_id' => $this->shipping_address_id,
                    'address_for_payment' => $addressForPayment ? $addressForPayment->address_id : null,
                    'paytr_address' => $paytrAddress,
                ]);

                // Ödeme bilgilerini session'a kaydet (kart formu submit'inde kullanılacak)
                session([
                    'pending_payment_id' => $payment->payment_id,
                    'pending_customer' => [
                        'name' => trim($this->contact_first_name . ' ' . $this->contact_last_name),
                        'email' => $this->contact_email,
                        'phone' => $this->contact_phone,
                        'address' => $paytrAddress,
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
        // User bilgilerini güncelle (sadece telefon)
        $user = Auth::user();

        // Telefon boşsa güncelle
        if (empty($user->phone) && !empty($this->contact_phone)) {
            $user->phone = $this->contact_phone;
            $user->save();
        }

        return $user;
    }

    /**
     * Ödemeye Geç - Kredi kartı veya Havale sayfasına yönlendir
     */
    public function proceedToPayment()
    {
        \Log::info('💳 [CHECKOUT] proceedToPayment START', [
            'billing_profile_id' => $this->billing_profile_id,
            'selectedPaymentMethodId' => $this->selectedPaymentMethodId,
            'selectedGateway' => $this->selectedGateway,
            'agree_all' => $this->agree_all,
            'requiresShipping' => $this->requiresShipping,
        ]);

        // Önce validation yap
        $rules = [
            'contact_first_name' => 'required|string|max:255',
            'contact_last_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'billing_profile_id' => 'required|exists:billing_profiles,billing_profile_id',
            'agree_all' => 'accepted',
            'selectedPaymentMethodId' => 'required|exists:payment_methods,payment_method_id',
            'selectedGateway' => 'nullable|string|in:paytr,bank_transfer,manual', // Yeni gateway sistemi
        ];

        // Adres seçimi - sadece fiziksel ürün varsa teslimat zorunlu
        if ($this->customerId) {
            if ($this->requiresShipping) {
                $rules['shipping_address_id'] = 'required';
                // Kurumsal müşteri için fatura adresi zorunlu
                if (!$this->billing_same_as_shipping && $this->billing_type === 'corporate') {
                    $rules['billing_address_id'] = 'required';
                }
            } else {
                // Dijital ürün - kurumsal için fatura adresi zorunlu, bireysel için opsiyonel
                if ($this->billing_type === 'corporate') {
                    $rules['billing_address_id'] = 'required';
                }
            }
        } else {
            if ($this->requiresShipping) {
                $rules['shipping_address_line_1'] = 'required|string|max:255';
                $rules['shipping_city'] = 'required|string|max:100';
                $rules['shipping_district'] = 'required|string|max:100';
            }
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
                'contact_email.required' => 'E-posta adresi zorunludur',
                'contact_email.email' => 'Geçerli bir e-posta adresi giriniz',
                'contact_phone.required' => 'Telefon zorunludur',
                'billing_profile_id.required' => 'Fatura bilgileri seçmelisiniz',
                'billing_address_id.required' => 'Fatura adresi seçmelisiniz',
                'shipping_address_id.required' => 'Teslimat adresi seçmelisiniz',
                'shipping_address_line_1.required' => 'Adres zorunludur',
                'shipping_city.required' => 'İl zorunludur',
                'shipping_district.required' => 'İlçe zorunludur',
                'agree_all.accepted' => 'Sözleşmeleri kabul etmelisiniz',
                'selectedPaymentMethodId.required' => 'Ödeme yöntemi seçmelisiniz',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ [CHECKOUT] Validation FAILED', ['errors' => $e->errors()]);
            throw $e;
        }

        \Log::info('✅ [CHECKOUT] Validation passed');

        DB::beginTransaction();
        \Log::info('🔵 [CHECKOUT] Transaction started');

        try {
            // Müşteri oluştur/güncelle
            \Log::info('🔵 [CHECKOUT] Creating/updating customer...');
            $customer = $this->createOrUpdateCustomer();

            // SADECE Guest için adres oluştur (Login user ASLA buraya girmemeli!)
            if (!$this->customerId) {
                // Guest user için inline form ile adres oluştur
                $shippingAddress = Address::create([
                    'user_id' => $customer->id,
                    'address_type' => 'shipping',
                    'first_name' => $this->contact_first_name,
                    'last_name' => $this->contact_last_name,
                    'phone' => $this->contact_phone,
                    'email' => $this->contact_email,
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
                    $billingAddress = Address::create([
                        'user_id' => $customer->id,
                        'address_type' => 'billing',
                        'first_name' => $this->contact_first_name,
                        'last_name' => $this->contact_last_name,
                        'phone' => $this->contact_phone,
                        'email' => $this->contact_email,
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

            // Fatura adresi = Teslimat adresi ise, otomatik ata
            if ($this->billing_same_as_shipping && $this->shipping_address_id) {
                $this->billing_address_id = $this->shipping_address_id;
            }

            // Adresleri al
            $billingAddress = Address::find($this->billing_address_id);
            $shippingAddress = $this->shipping_address_id ? Address::find($this->shipping_address_id) : null;

            // Bireysel müşteri için varsayılan fatura adresi (adres seçilmemişse)
            $billingAddressData = null;
            if ($billingAddress) {
                $billingAddressData = $billingAddress->toSnapshot();
            } elseif ($this->billing_type === 'individual') {
                // Bireysel müşteri - varsayılan Türkiye adresi
                $billingAddressData = [
                    'country' => 'Türkiye',
                    'city' => null,
                    'district' => null,
                    'address_line_1' => null,
                ];
            }

            // Sipariş oluştur
            \Log::info('🔵 [CHECKOUT] Creating order...');
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => Order::generateOrderNumber(),

                'customer_name' => $this->contact_first_name . ' ' . $this->contact_last_name,
                'customer_email' => $this->contact_email,
                'customer_phone' => $this->contact_phone,
                'customer_company' => $this->billing_company_name,
                'customer_tax_office' => $this->billing_tax_office,
                'customer_tax_number' => $this->billing_tax_number,

                'billing_address' => $billingAddressData,
                'shipping_address' => $shippingAddress ? $shippingAddress->toSnapshot() : null,

                'customer_notes' => $shippingAddress?->delivery_notes ?? null,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => $this->grandTotal,
                'currency' => 'TRY',
                'status' => 'pending',
                'payment_status' => 'pending',
                'requires_shipping' => $this->requiresShipping,

                'agreed_terms' => $this->agree_all,
                'agreed_privacy' => $this->agree_all,
                'agreed_marketing' => false,

                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            \Log::info('✅ [CHECKOUT] Order created', [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
            ]);

            // Sipariş kalemleri
            \Log::info('🔵 [CHECKOUT] Creating order items...');
            foreach ($this->items as $item) {
                OrderItem::createFromCartItem($item, $order->order_id);
            }
            \Log::info('✅ [CHECKOUT] Order items created', ['count' => count($this->items)]);

            // 🆕 Subscription oluştur (eğer sepette subscription varsa)
            $this->createSubscriptionsFromOrder($order);

            // Payment kaydı - Gateway'i PaymentMethod'dan al
            $paymentMethod = PaymentMethod::find($this->selectedPaymentMethodId);
            $gateway = $paymentMethod?->gateway ?? 'paytr';

            \Log::info('🔵 [CHECKOUT] Creating payment record...', [
                'gateway' => $gateway,
                'amount' => $this->grandTotal,
            ]);

            // Payment number oluştur (PayTR merchant_oid olarak kullanılacak)
            $paymentNumber = 'PAY-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6));

            $payment = Payment::create([
                'payment_method_id' => $this->selectedPaymentMethodId,
                'payable_type' => Order::class,
                'payable_id' => $order->order_id,
                'gateway' => $gateway,
                'payment_number' => $paymentNumber,
                'gateway_transaction_id' => $paymentNumber, // PayTR merchant_oid ile eşleşecek
                'amount' => $this->grandTotal,
                'currency' => 'TRY',
                'exchange_rate' => 1,
                'amount_in_base_currency' => $this->grandTotal,
                'status' => 'pending',
                'installment_count' => $this->selectedInstallment,
                'installment_fee' => $this->installmentFee,
            ]);

            \Log::info('✅ [CHECKOUT] Payment record created', [
                'payment_id' => $payment->payment_id,
                'gateway' => $gateway,
            ]);

            // ✅ Commit - Order ve Payment oluşturuldu
            \Log::info('🔵 [CHECKOUT] Committing transaction...');
            DB::commit();
            \Log::info('✅ [CHECKOUT] Transaction committed');

            // Session'a bilgileri kaydet (payment sayfası için)
            session([
                'last_order_number' => $order->order_number,
                'payment_authorized_' . $order->order_number => true,
                'checkout_user_info' => [
                    'name' => trim($this->contact_first_name . ' ' . $this->contact_last_name),
                    'email' => $this->contact_email,
                    'phone' => $this->contact_phone,
                    'address' => $shippingAddress ? ($shippingAddress->address_line_1 . ', ' . $shippingAddress->city) : '',
                ],
            ]);

            // ✅ Gateway'e göre redirect - PayTR veya Bank Transfer
            // $paymentMethod ve $gateway zaten yukarıda tanımlandı

            if ($gateway === 'bank_transfer' || $gateway === 'manual') {
                // Havale/EFT sayfasına yönlendir
                $paymentUrl = route('payment.bank-transfer', ['orderNumber' => $order->order_number]);
                \Log::info('✅ Redirecting to BANK TRANSFER page', [
                    'order_number' => $order->order_number,
                    'gateway' => $gateway,
                    'payment_url' => $paymentUrl
                ]);
            } else {
                // PayTR ödeme sayfasına yönlendir
                $paymentUrl = route('payment.page', ['orderNumber' => $order->order_number]);
                \Log::info('✅ Redirecting to PayTR payment page', [
                    'order_number' => $order->order_number,
                    'payment_url' => $paymentUrl
                ]);
            }

            \Log::info('✅ [CHECKOUT] proceedToPayment COMPLETED', [
                'success' => true,
                'redirectUrl' => $paymentUrl,
            ]);

            return [
                'success' => true,
                'redirectUrl' => $paymentUrl
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ [CHECKOUT] proceedToPayment ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Sipariş oluşturulurken hata: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
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

    /**
     * Siparişteki subscription item'lardan subscription oluştur
     */
    private function createSubscriptionsFromOrder($order)
    {
        foreach ($this->items as $cartItem) {
            // Subscription item mı kontrol et
            if ($cartItem->cartable_type !== 'Modules\Subscription\App\Models\SubscriptionPlan') {
                continue;
            }

            // Cart metadata'dan cycle bilgilerini al
            $metadata = is_array($cartItem->metadata) ? $cartItem->metadata : json_decode($cartItem->metadata, true);
            $cycleKey = $metadata['cycle_key'] ?? null;

            if (!$cycleKey) {
                \Log::error('Subscription cart item has no cycle_key!', ['cart_item_id' => $cartItem->cart_item_id]);
                continue;
            }

            // Plan'ı al
            $plan = \Modules\Subscription\App\Models\SubscriptionPlan::find($cartItem->cartable_id);

            if (!$plan) {
                \Log::error('Subscription plan not found!', ['plan_id' => $cartItem->cartable_id]);
                continue;
            }

            // Plan'dan cycle bilgilerini al
            $cycle = $plan->getCycle($cycleKey);

            if (!$cycle) {
                \Log::error('Cycle not found in plan!', ['cycle_key' => $cycleKey, 'plan_id' => $plan->subscription_plan_id]);
                continue;
            }

            // Corporate Bulk mu kontrol et (Muzibu kurumsal toplu odeme)
            $isCorporateBulk = ($metadata['type'] ?? null) === 'corporate_bulk';

            if ($isCorporateBulk) {
                // Kurumsal toplu odeme - secilen her uye icin subscription olustur
                $this->createCorporateBulkSubscriptions($order, $plan, $cycle, $cycleKey, $metadata);
            } else {
                // Normal subscription - siparis sahibi icin olustur
                $this->createSingleSubscription($order->user_id, $plan, $cycle, $cycleKey);
            }
        }
    }

    /**
     * Kurumsal toplu subscription olustur (Muzibu)
     */
    private function createCorporateBulkSubscriptions($order, $plan, $cycle, $cycleKey, $metadata)
    {
        $selectedUserIds = $metadata['selected_user_ids'] ?? [];
        $corporateAccountId = $metadata['corporate_account_id'] ?? null;

        if (empty($selectedUserIds)) {
            \Log::error('Corporate bulk subscription: No user_ids in metadata!', [
                'order_id' => $order->order_id,
                'metadata' => $metadata
            ]);
            return;
        }

        \Log::info('🏢 Creating corporate bulk subscriptions', [
            'order_id' => $order->order_id,
            'corporate_account_id' => $corporateAccountId,
            'user_count' => count($selectedUserIds),
            'plan_id' => $plan->subscription_plan_id,
        ]);

        foreach ($selectedUserIds as $userId) {
            $this->createSingleSubscription($userId, $plan, $cycle, $cycleKey, [
                'purchased_by' => $order->user_id,
                'corporate_account_id' => $corporateAccountId,
                'is_corporate_subscription' => true,
            ]);
        }

        \Log::info('✅ Corporate bulk subscriptions created', [
            'order_id' => $order->order_id,
            'created_count' => count($selectedUserIds),
        ]);
    }

    /**
     * Tek kullanici icin subscription olustur
     */
    private function createSingleSubscription($userId, $plan, $cycle, $cycleKey, $extraMetadata = [])
    {
        // Trial kontrolu - kullanici daha once trial kullandi mi?
        $hasUsedTrial = \Modules\Subscription\App\Models\Subscription::userHasUsedTrial($userId);
        $trialDays = (!$hasUsedTrial && isset($cycle['trial_days']) && $cycle['trial_days'] > 0) ? $cycle['trial_days'] : 0;

        // Subscription olustur (status: pending_payment - odeme basarili olunca active/trial olacak)
        $subscription = \Modules\Subscription\App\Models\Subscription::create([
            'user_id' => $userId,
            'subscription_plan_id' => $plan->subscription_plan_id,
            'cycle_key' => $cycleKey,
            'cycle_metadata' => [
                'label' => $cycle['label'],
                'duration_days' => $cycle['duration_days'],
                'price' => $cycle['price'],
                'compare_price' => $cycle['compare_price'] ?? null,
                'trial_days' => $cycle['trial_days'] ?? null,
            ],
            'price_per_cycle' => $cycle['price'],
            'currency' => 'TRY',
            'has_trial' => $trialDays > 0,
            'trial_days' => $trialDays,
            'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
            'started_at' => now(),
            'current_period_start' => $trialDays > 0 ? now()->addDays($trialDays) : now(),
            'current_period_end' => $trialDays > 0
                ? now()->addDays($trialDays + $cycle['duration_days'])
                : now()->addDays($cycle['duration_days']),
            'next_billing_date' => now()->addDays($trialDays + $cycle['duration_days']),
            'expires_at' => $trialDays > 0
                ? now()->addDays($trialDays + $cycle['duration_days'])
                : now()->addDays($cycle['duration_days']),
            'status' => 'pending_payment', // Odeme basarili olunca active/trial olacak
            'auto_renew' => true,
            'billing_cycles_completed' => 0,
            'total_paid' => 0,
            'metadata' => !empty($extraMetadata) ? $extraMetadata : null,
        ]);

        \Log::info('✅ Subscription created', [
            'subscription_id' => $subscription->subscription_id,
            'user_id' => $userId,
            'plan_id' => $plan->subscription_plan_id,
            'cycle_key' => $cycleKey,
            'has_trial' => $subscription->has_trial,
            'is_corporate' => !empty($extraMetadata['is_corporate_subscription']),
        ]);

        return $subscription;
    }

    public function render()
    {
        // 🔥 NO-CACHE HEADERS - Browser cache'i engelle
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

        // Layout: Tenant temasından (header/footer için)
        // View: Module default (içerik fallback'ten)
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        // Tenant layout yoksa simple fallback
        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        // View her zaman module default (orta kısım fallback)
        return view('cart::livewire.front.checkout-page')
            ->layout($layoutPath);
    }
}
