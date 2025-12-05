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
    public $new_address_title = '';
    public $new_address_phone = '';
    public $new_address_line = '';
    public $new_address_city = '';
    public $new_address_district = '';
    public $new_address_postal = '';

    // Yeni Adres Formu (Billing - inline)
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
            // Subscription plan parametresi varsa sepete ekle ve URL'den temizle
            if (request()->has('plan') && request()->has('cycle')) {
                $this->addSubscriptionToCart(request('plan'), request('cycle'));
                // URL'den parametreleri temizle (güvenlik)
                return $this->redirect(route('cart.checkout'), navigate: true);
            }

            $this->agree_all = false;
            $this->loadCart();
            $this->loadOrCreateCustomer();
            $this->loadBillingProfiles(); // Fatura profillerini yükle
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
     */
    protected function addSubscriptionToCart($planId, $cycleKey)
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
            $cart->items()
                ->where('cartable_type', 'Modules\Subscription\App\Models\SubscriptionPlan')
                ->each(function ($item) use ($cartService) {
                    $cartService->removeItem($item);
                });

            // Subscription ekle
            $options = $bridge->prepareSubscriptionForCart($plan, $cycleKey, true);
            $cartService->addItem($cart, $plan, 1, $options);

            \Log::info('✅ Subscription auto-added to cart', [
                'plan_id' => $planId,
                'cycle_key' => $cycleKey,
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
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
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
     * Fatura profili seçildiğinde
     */
    public function selectBillingProfile($profileId)
    {
        $this->billing_profile_id = $profileId;
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

            // Seçili profil siliniyorsa, seçimi kaldır
            if ($this->billing_profile_id == $profileId) {
                $this->billing_profile_id = null;
            }

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

            session()->flash('success', 'Fatura profili başarıyla silindi.');
            \Log::info('✅ Billing profile deleted', ['profile_id' => $profileId]);
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

            // Seçili adres siliniyorsa, seçimi kaldır
            if ($this->shipping_address_id == $addressId) {
                $this->shipping_address_id = null;
            }
            if ($this->billing_address_id == $addressId) {
                $this->billing_address_id = null;
            }

            $address->delete();

            // Adresleri yenile
            $this->loadDefaultAddresses();

            session()->flash('success', 'Adres başarıyla silindi.');
            \Log::info('✅ Address deleted', ['address_id' => $addressId]);
        } catch (\Exception $e) {
            session()->flash('error', 'Silme işlemi başarısız oldu.');
            \Log::error('❌ Error deleting address', ['error' => $e->getMessage()]);
        }
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

            $this->shipping_address_id = $address->address_id;

            // Fatura adresi teslimat ile aynıysa
            if ($this->billing_same_as_shipping) {
                $this->billing_address_id = $address->address_id;
            }

            // Form temizle
            $this->reset(['new_address_title', 'new_address_phone', 'new_address_line', 'new_address_city', 'new_address_district', 'new_address_postal']);

            // Alpine'a formu kapat sinyali gönder
            $this->dispatch('address-saved', type: 'shipping', addressId: $address->address_id);

            session()->flash('success', 'Adres başarıyla kaydedildi!');

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

            $this->billing_address_id = $address->address_id;

            // Form temizle
            $this->reset(['new_billing_address_title', 'new_billing_address_phone', 'new_billing_address_line', 'new_billing_address_city', 'new_billing_address_district', 'new_billing_address_postal']);

            // Alpine'a formu kapat sinyali gönder
            $this->dispatch('address-saved', type: 'billing', addressId: $address->address_id);

            session()->flash('success', 'Fatura adresi başarıyla kaydedildi!');
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

        // Varsayılan fatura adresi
        $defaultBilling = Address::where('user_id', $this->customerId)
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
        $defaultShipping = Address::where('user_id', $this->customerId)
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

        // Adres seçimi - sadece fiziksel ürün varsa teslimat zorunlu
        if ($this->customerId) {
            // Fiziksel ürün varsa teslimat adresi zorunlu
            if ($this->requiresShipping) {
                $rules['shipping_address_id'] = 'required';
                // Fatura adresi sadece "teslimat ile aynı" kapalıysa zorunlu
                if (!$this->billing_same_as_shipping) {
                    $rules['billing_address_id'] = 'required';
                }
            } else {
                // Dijital ürün - sadece fatura adresi zorunlu
                $rules['billing_address_id'] = 'required';
            }
            \Log::info('📍 Login user - Address validation', ['requires_shipping' => $this->requiresShipping]);
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

            // Guest için adres oluştur (login user için atlanır)
            if (!$this->customerId || !$this->shipping_address_id) {
                $shippingAddress = Address::create([
                    'user_id' => $customer->id,
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
                    $billingAddress = Address::create([
                        'user_id' => $customer->id,
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
                'transaction_id' => 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6)),
                'amount' => $this->grandTotal,
                'currency' => 'TRY',
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

            // ⚠️ SEPET TEMİZLENMEYECEK - Sadece ödeme başarılı olunca temizlenecek
            // PayTR callback başarı dönünce sepet temizlenecek

            \Log::info('✅ TEST: Redirecting to payment page', [
                'order' => $orderNumber,
                'amount' => $this->grandTotal,
                'cart_cleared' => false // ARTIK TEMİZLENMİYOR
            ]);

            // 🔐 Session authorization ekle - sadece bu kullanıcı erişebilsin
            session()->put('payment_authorized_' . $orderNumber, true);

            // Yeni ödeme sayfasına yönlendir
            return redirect()->route('payment.page', ['orderNumber' => $orderNumber]);
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
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'billing_profile_id' => 'required|exists:billing_profiles,billing_profile_id',
            'agree_all' => 'accepted',
            'selectedPaymentMethodId' => 'required|exists:payment_methods,payment_method_id',
            'selectedGateway' => 'nullable|string|in:paytr,bank_transfer', // Yeni gateway sistemi
        ];

        // Adres seçimi - sadece fiziksel ürün varsa teslimat zorunlu
        if ($this->customerId) {
            if ($this->requiresShipping) {
                $rules['shipping_address_id'] = 'required';
                // Fatura adresi sadece "teslimat ile aynı" kapalıysa zorunlu
                if (!$this->billing_same_as_shipping) {
                    $rules['billing_address_id'] = 'required';
                }
            } else {
                // Dijital ürün - sadece fatura adresi zorunlu
                $rules['billing_address_id'] = 'required';
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
            \Log::error('❌ Validation FAILED', ['errors' => $e->errors()]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            // Müşteri oluştur/güncelle
            $customer = $this->createOrUpdateCustomer();

            // Guest için adres oluştur
            if (!$this->customerId || !$this->shipping_address_id) {
                $shippingAddress = Address::create([
                    'user_id' => $customer->id,
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
                    $billingAddress = Address::create([
                        'user_id' => $customer->id,
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

            // Fatura adresi = Teslimat adresi ise, otomatik ata
            if ($this->billing_same_as_shipping && $this->shipping_address_id) {
                $this->billing_address_id = $this->shipping_address_id;
            }

            // Adresleri al
            $billingAddress = Address::find($this->billing_address_id);
            $shippingAddress = $this->shipping_address_id ? Address::find($this->shipping_address_id) : null;

            // Sipariş oluştur
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => Order::generateOrderNumber(),

                'customer_name' => $this->contact_first_name . ' ' . $this->contact_last_name,
                'customer_email' => $this->contact_email,
                'customer_phone' => $this->contact_phone,
                'customer_company' => $this->billing_company_name,
                'customer_tax_office' => $this->billing_tax_office,
                'customer_tax_number' => $this->billing_tax_number,

                'billing_address' => $billingAddress ? $billingAddress->toSnapshot() : null,
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

            // Sipariş kalemleri
            foreach ($this->items as $item) {
                OrderItem::createFromCartItem($item, $order->order_id);
            }

            // 🆕 Subscription oluştur (eğer sepette subscription varsa)
            $this->createSubscriptionsFromOrder($order);

            // Payment kaydı
            $payment = Payment::create([
                'payment_method_id' => $this->selectedPaymentMethodId,
                'payable_type' => Order::class,
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

                    // 🔐 Session authorization ekle - ödeme sayfası için
                    session()->put('payment_authorized_' . $order->order_number, true);

                    // ⚠️ SEPET TEMİZLENMEYECEK - Sadece ödeme başarılı olunca temizlenecek
                    // PayTR callback başarı dönünce sepet temizlenecek

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

            // Trial kontrolü - kullanıcı daha önce trial kullandı mı?
            $hasUsedTrial = \Modules\Subscription\App\Models\Subscription::userHasUsedTrial($order->user_id);
            $trialDays = (!$hasUsedTrial && isset($cycle['trial_days']) && $cycle['trial_days'] > 0) ? $cycle['trial_days'] : 0;

            // Subscription oluştur (status: pending_payment - ödeme başarılı olunca active/trial olacak)
            $subscription = \Modules\Subscription\App\Models\Subscription::create([
                'customer_id' => $order->user_id,
                'plan_id' => $plan->subscription_plan_id,
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
                'status' => 'pending_payment', // Ödeme başarılı olunca active/trial olacak
                'auto_renew' => true,
                'billing_cycles_completed' => 0,
                'total_paid' => 0,
            ]);

            \Log::info('✅ Subscription created from order', [
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $order->order_id,
                'plan_id' => $plan->subscription_plan_id,
                'cycle_key' => $cycleKey,
                'has_trial' => $subscription->has_trial,
                'trial_days' => $trialDays,
            ]);
        }
    }

    public function render()
    {
        // Tema-aware view ve layout
        $theme = tenant()->theme ?? 'ixtif';

        // Önce tema-specific view'ı dene, yoksa default kullan
        $viewPath = "themes.{$theme}.cart.checkout";
        $defaultViewPath = 'cart::livewire.front.checkout-page';

        // Layout - Tema-aware
        $layoutPath = "themes.{$theme}.layouts.app";

        if (view()->exists($viewPath)) {
            return view($viewPath)->layout($layoutPath);
        }

        return view($defaultViewPath)->layout($layoutPath);
    }
}
