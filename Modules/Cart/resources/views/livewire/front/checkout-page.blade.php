<div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900"
     @redirect-to-payment.window="console.log('🚀 Redirect event received:', $event.detail.url); window.location.href = $event.detail.url"
     x-data="{
    // Form Data
    contactFirstName: '{{ $contact_first_name }}',
    contactLastName: '{{ $contact_last_name }}',
    contactEmail: '{{ $contact_email }}',
    contactPhone: '{{ $contact_phone }}',

    // Fatura Profili
    billingProfileId: {{ $billing_profile_id ?? 'null' }},
    defaultBillingProfileId: {{ optional($billingProfiles->where('is_default', true)->first())->billing_profile_id ?? 'null' }},
    showNewBillingProfile: false,
    showList: false,
    newBillingProfileType: 'individual',

    // Adres
    shippingAddressId: @entangle('shipping_address_id').live,
    billingAddressId: @entangle('billing_address_id').live,
    defaultShippingAddressId: {{ optional($userAddresses->where('is_default_shipping', true)->first())->address_id ?? 'null' }},
    defaultBillingAddressId: {{ optional($userAddresses->where('is_default_billing', true)->first())->address_id ?? 'null' }},
    billingSameAsShipping: {{ $billing_same_as_shipping ? 'true' : 'false' }},
    requiresShipping: {{ $requiresShipping ? 'true' : 'false' }},

    // UI State
    showShippingForm: false,
    showNewShippingForm: false,
    showShippingList: false,
    showBillingAddressForm: {{ !$requiresShipping ? 'true' : 'false' }},
    showNewBillingForm: false,
    showBillingList: false,
    selectedPaymentMethodId: {{ $selectedPaymentMethodId ?? 'null' }},
    agreeAll: false,

    // Delete State
    showDeleteWarning: false,
    deleteTargetId: null,
    deleteTargetType: null,
    deleteTargetTitle: '',
    editBillingProfileMode: false,

    // Type Switch Warning
    showTypeSwitchWarning: false,
    pendingType: null,

    // Methods
    selectBillingProfile(profileId) {
        this.billingProfileId = profileId;
        $wire.selectBillingProfile(profileId);
    },

    syncToLivewire() {
        $wire.set('contact_first_name', this.contactFirstName);
        $wire.set('contact_last_name', this.contactLastName);
        $wire.set('contact_phone', this.contactPhone);
        $wire.set('billing_profile_id', this.billingProfileId);
        $wire.set('shipping_address_id', this.shippingAddressId);
        // Dijital ürün ise veya toggle kapalıysa fatura adresini kullan
        $wire.set('billing_address_id', (this.requiresShipping && this.billingSameAsShipping) ? this.shippingAddressId : this.billingAddressId);
        $wire.set('billing_same_as_shipping', this.billingSameAsShipping);
        $wire.set('selectedPaymentMethodId', this.selectedPaymentMethodId);
        $wire.set('agree_all', this.agreeAll);
    },

    async submitOrder() {
        this.syncToLivewire();
        await $wire.proceedToPayment();
    },

    confirmDelete() {
        if (this.deleteTargetType === 'billing_profile') {
            $wire.deleteBillingProfile(this.deleteTargetId);
        } else if (this.deleteTargetType === 'billing_address') {
            $wire.deleteAddress(this.deleteTargetId);
        } else if (this.deleteTargetType === 'shipping_address') {
            $wire.deleteAddress(this.deleteTargetId);
        }
        this.cancelDelete();
    },

    cancelDelete() {
        this.showDeleteWarning = false;
        this.deleteTargetId = null;
        this.deleteTargetType = null;
        this.deleteTargetTitle = '';
    },

    checkTypeSwitch(newType) {
        // Eğer aynı tip ise direkt değiştir
        if (this.newBillingProfileType === newType) {
            return;
        }

        // Veri girilmişse uyarı göster
        if (this.newBillingProfileType === 'individual' && newType === 'corporate') {
            // Bireysel'den Kurumsal'a geçiş - TC kimlik var mı?
            if ($wire.get('new_billing_profile_identity_number')) {
                this.showTypeSwitchWarning = true;
                this.pendingType = newType;
                return;
            }
        } else if (this.newBillingProfileType === 'corporate' && newType === 'individual') {
            // Kurumsal'dan Bireysel'e geçiş - Şirket bilgileri var mı?
            if ($wire.get('new_billing_profile_company_name') || $wire.get('new_billing_profile_tax_number') || $wire.get('new_billing_profile_tax_office')) {
                this.showTypeSwitchWarning = true;
                this.pendingType = newType;
                return;
            }
        }

        // Veri yoksa direkt değiştir
        this.switchType(newType);
    },

    confirmTypeSwitch() {
        if (this.pendingType) {
            this.switchType(this.pendingType);
        }
        this.showTypeSwitchWarning = false;
        this.pendingType = null;
    },

    cancelTypeSwitch() {
        this.showTypeSwitchWarning = false;
        this.pendingType = null;
    },

    switchType(newType) {
        this.newBillingProfileType = newType;
        $wire.set('new_billing_profile_type', newType);

        if (newType === 'individual') {
            // Kurumsal bilgileri temizle
            $wire.set('new_billing_profile_company_name', '');
            $wire.set('new_billing_profile_tax_number', '');
            $wire.set('new_billing_profile_tax_office', '');
        } else {
            // Bireysel bilgileri temizle
            $wire.set('new_billing_profile_identity_number', '');
        }
    }
}"
@address-saved.window="
    if ($event.detail.type === 'shipping') {
        showNewShippingForm = false;
        showShippingForm = false;
        shippingAddressId = $event.detail.addressId;
    } else {
        showNewBillingForm = false;
        billingAddressId = $event.detail.addressId;
    }
"
@billing-profile-saved.window="showNewBillingProfile = false; billingProfileId = $event.detail.profileId"
@close-billing-form.window="showNewBillingProfile = false; editBillingProfileMode = false"
>
    <style>
        [x-cloak] { display: none !important; }
        .card-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(229, 231, 235, 1);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .card-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: none;
        }
    </style>

    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Sipariş Tamamla</h1>
        </div>

        {{-- Boş sepet --}}
        @if(!$items || $items->count() === 0)
            <div class="max-w-md mx-auto text-center py-16" id="empty-cart-message">
                <div class="card-glass rounded-2xl p-8">
                    <i class="fa-solid fa-shopping-cart text-6xl text-gray-600 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sepetiniz Boş</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Checkout yapabilmek için sepetinize ürün eklemelisiniz.</p>
                    <a href="/cart" class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-blue-600 text-white dark:bg-gray-600 dark:text-white font-medium rounded-lg">
                        <i class="fa-solid fa-shopping-cart mr-2"></i>Sepete Git
                    </a>
                </div>
            </div>
        @else

        {{-- UNIVERSAL DELETE WARNING - Tüm delete işlemleri için tek modal --}}
        <div x-show="showDeleteWarning" x-cloak x-transition.duration.200ms
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full border border-gray-200 dark:border-gray-700 shadow-2xl">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-exclamation-triangle text-red-400 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Silme Onayı</h3>
                        <p class="text-sm text-gray-900 dark:text-gray-300 mb-4">
                            <strong class="text-gray-900 dark:text-white" x-text="deleteTargetTitle"></strong> silinecek.
                            <span class="text-red-400 font-medium block mt-1">Bu işlem geri alınamaz!</span>
                        </p>
                        <div class="flex gap-3">
                            <button type="button" @click="confirmDelete()"
                                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fa-solid fa-check mr-2"></i>Evet, Sil
                            </button>
                            <button type="button" @click="cancelDelete()"
                                    class="flex-1 px-4 py-2.5 bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fa-solid fa-times mr-2"></i>İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN LAYOUT: 2 COLUMN --}}
        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">

            {{-- ===================== SOL KOLON ===================== --}}
            <div style="flex: 2; min-width: 300px;">

                {{-- 1. Kullanıcı Bilgileri (Özet Bar) --}}
                @php
                    $hasContact = $contact_first_name && $contact_last_name && $contact_email && $contact_phone;
                @endphp
                <div x-data="{ editContact: {{ $hasContact ? 'false' : 'true' }} }">
                    {{-- Özet (dolu ise) --}}
                    @if($hasContact)
                    <div x-show="!editContact"
                         class="flex items-center justify-between py-3 px-4 mb-4 card-glass rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="text-gray-900 dark:text-white font-medium">{{ $contact_first_name }} {{ $contact_last_name }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $contact_email }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $contact_phone }}</span>
                        </p>
                        <button @click="editContact = true" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white p-1">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                    </div>
                    @endif

                    {{-- Form --}}
                    <div x-show="editContact" {!! $hasContact ? 'x-cloak' : '' !!}
                         class="card-glass rounded-2xl p-5 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300">İletişim Bilgileri</span>
                            <button x-show="contactFirstName && contactLastName && contactEmail && contactPhone"
                                    @click="editContact = false" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Ad <span class="text-red-500">*</span></label>
                                <input type="text" x-model="contactFirstName" value="{{ $contact_first_name }}"
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-sm">
                                @error('contact_first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Soyad <span class="text-red-500">*</span></label>
                                <input type="text" x-model="contactLastName" value="{{ $contact_last_name }}"
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-sm">
                                @error('contact_last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">E-posta <span class="text-red-500">*</span></label>
                                @auth
                                    <input type="email" x-model="contactEmail" value="{{ $contact_email }}" readonly
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-100 dark:bg-gray-700 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 text-sm cursor-not-allowed">
                                @else
                                    <input type="email" x-model="contactEmail" value="{{ $contact_email }}" placeholder="ornek@email.com"
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-sm">
                                @endauth
                                @error('contact_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Telefon <span class="text-red-500">*</span></label>
                                <input type="tel" x-model="contactPhone" value="{{ $contact_phone }}" placeholder="05XX XXX XX XX"
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-sm">
                                @error('contact_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. FATURA BİLGİLERİ --}}
                <div class="card-glass rounded-2xl p-6 mb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-file-invoice text-gray-700 dark:text-gray-500 mr-3"></i>
                            Fatura Bilgileri
                        </h2>
                        <button @click="editBillingProfileMode = false; showNewBillingProfile = !showNewBillingProfile; $wire.set('edit_billing_profile_id', null); $wire.set('new_billing_profile_title', ''); $wire.set('new_billing_profile_type', 'individual'); $wire.set('new_billing_profile_identity_number', ''); $wire.set('new_billing_profile_company_name', ''); $wire.set('new_billing_profile_tax_number', ''); $wire.set('new_billing_profile_tax_office', ''); newBillingProfileType = 'individual'; showTypeSwitchWarning = false; pendingType = null"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <i class="fa-solid fa-plus mr-1"></i>Ekle
                        </button>
                    </div>

                    {{-- Yeni Profil Formu --}}
                    <div x-show="showNewBillingProfile" x-cloak x-transition.duration.200ms class="space-y-4 pt-3 border-t border-gray-200 dark:border-gray-700 mb-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                <span x-show="!editBillingProfileMode">Yeni Profil</span>
                                <span x-show="editBillingProfileMode">Profil Düzenle</span>
                            </span>
                            <button @click="showNewBillingProfile = false; editBillingProfileMode = false; $wire.set('edit_billing_profile_id', null); $wire.set('new_billing_profile_title', ''); $wire.set('new_billing_profile_type', 'individual'); $wire.set('new_billing_profile_identity_number', ''); $wire.set('new_billing_profile_company_name', ''); $wire.set('new_billing_profile_tax_number', ''); $wire.set('new_billing_profile_tax_office', ''); showTypeSwitchWarning = false; pendingType = null"
                                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="checkTypeSwitch('individual')"
                                    :class="newBillingProfileType === 'individual' ? 'bg-blue-600 text-white dark:bg-gray-600 dark:text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-100 dark:bg-gray-700 dark:text-gray-600 dark:text-gray-400'"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg">
                                <i class="fa-solid fa-user mr-1.5"></i>Bireysel
                            </button>
                            <button type="button" @click="checkTypeSwitch('corporate')"
                                    :class="newBillingProfileType === 'corporate' ? 'bg-blue-600 text-white dark:bg-gray-600 dark:text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-100 dark:bg-gray-700 dark:text-gray-600 dark:text-gray-400'"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg">
                                <i class="fa-solid fa-building mr-1.5"></i>Kurumsal
                            </button>
                        </div>

                        {{-- Type Switch Warning --}}
                        <div x-show="showTypeSwitchWarning" x-cloak x-transition.duration.200ms
                             class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3">
                            <p class="text-sm text-yellow-400">
                                <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                                <span x-show="pendingType === 'individual'">
                                    <strong>Bireysel'e</strong> geçerseniz <strong>Kurumsal bilgiler silinecektir</strong> (Şirket ünvanı, Vergi no, Vergi dairesi).
                                </span>
                                <span x-show="pendingType === 'corporate'">
                                    <strong>Kurumsal'a</strong> geçerseniz <strong>Bireysel bilgiler silinecektir</strong> (TC Kimlik No).
                                </span>
                            </p>
                            <div class="flex gap-2 mt-3">
                                <button type="button" @click="confirmTypeSwitch()"
                                        class="px-4 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <i class="fa-solid fa-check mr-1"></i>Evet, Devam Et
                                </button>
                                <button type="button" @click="cancelTypeSwitch()"
                                        class="px-4 py-1.5 bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <i class="fa-solid fa-times mr-1"></i>İptal
                                </button>
                            </div>
                        </div>
                        <div x-show="newBillingProfileType === 'individual'">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İsim Soyisim <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="new_billing_profile_title" placeholder="Örn: Ahmet Yılmaz"
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_profile_title') border-red-500 @enderror">
                            @error('new_billing_profile_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div x-show="newBillingProfileType === 'individual'">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">TC Kimlik No <span class="text-gray-500">(Opsiyonel)</span></label>
                            <input type="text" wire:model="new_billing_profile_identity_number" placeholder="XXXXXXXXXXX" maxlength="11"
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_profile_identity_number') border-red-500 @enderror">
                            @error('new_billing_profile_identity_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div x-show="newBillingProfileType === 'corporate'" class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Şirket Ünvanı <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="new_billing_profile_company_name" placeholder="ABC Ltd. Şti."
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_profile_company_name') border-red-500 @enderror">
                                @error('new_billing_profile_company_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi No <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_billing_profile_tax_number" maxlength="10"
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_profile_tax_number') border-red-500 @enderror">
                                    @error('new_billing_profile_tax_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi Dairesi <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_billing_profile_tax_office"
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_profile_tax_office') border-red-500 @enderror">
                                    @error('new_billing_profile_tax_office') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="saveNewBillingProfile" wire:loading.attr="disabled" wire:target="saveNewBillingProfile"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 disabled:bg-gray-100 dark:bg-gray-700 disabled:cursor-wait text-gray-900 dark:text-white text-sm font-medium rounded-lg">
                                <span wire:loading.remove wire:target="saveNewBillingProfile"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                <span wire:loading wire:target="saveNewBillingProfile"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                            </button>
                        </div>
                    </div>

                    {{-- Mevcut Profiller --}}
                    @if($billingProfiles && count($billingProfiles) > 0)
                        {{-- Seçili Profil Özeti (Compact Minimal - Reactive) --}}
                        @foreach($billingProfiles as $profile)
                            <div wire:key="summary-{{ $profile->billing_profile_id }}"
                                 x-show="billingProfileId == {{ $profile->billing_profile_id }}"
                                 style="display: {{ $billing_profile_id == $profile->billing_profile_id ? 'flex' : 'none' }}"
                                 class="flex items-center justify-between gap-3 py-3 px-4 mb-3 bg-gray-100 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                                    @if($profile->is_default)
                                        <span class="mr-2 text-yellow-500 dark:text-yellow-400" title="Varsayılan Profil">★</span>
                                    @endif
                                    <span class="text-gray-900 dark:text-white font-medium">
                                        {{ $profile->isCorporate() ? $profile->company_name : $profile->title }}
                                    </span>
                                    <span class="mx-2 text-gray-400 dark:text-gray-600">•</span>
                                    <span>
                                        @if($profile->isCorporate())
                                            Vergi No: {{ $profile->tax_number }}
                                        @else
                                            TC: {{ $profile->identity_number ?? '-' }}
                                        @endif
                                    </span>
                                </p>
                                <button @click="showList = !showList"
                                        title="Profilleri Düzenle"
                                        class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-1 transition-colors duration-200">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            </div>
                        @endforeach

                        {{-- Profil Listesi (Collapsible) --}}
                        <div x-show="showList" x-cloak x-transition.duration.200ms class="space-y-2 mb-4">
                            @foreach($billingProfiles as $profile)
                                <div wire:key="billing-profile-{{ $profile->billing_profile_id }}"
                                     class="relative"
                                     x-data="{ isEditing: false }">
                                    <div @click="billingProfileId = {{ $profile->billing_profile_id }}; showList = false"
                                         class="p-3 rounded-xl border-2 transition-[border-color,background-color] duration-200 group cursor-pointer"
                                         :class="billingProfileId == {{ $profile->billing_profile_id }} ? 'border-gray-300 bg-gray-100 dark:bg-gray-800 dark:border-gray-400' : 'border-gray-200 bg-gray-50 dark:bg-slate-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500'">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                                        <i class="fa-solid {{ $profile->isCorporate() ? 'fa-building' : 'fa-user' }} text-gray-700 dark:text-gray-400"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                                {{ $profile->isCorporate() ? $profile->company_name : $profile->title }}
                                                            </span>
                                                            <span class="text-[10px] bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400 px-1.5 py-0.5 rounded flex-shrink-0">
                                                                {{ $profile->isCorporate() ? 'Kurumsal' : 'Bireysel' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-600 dark:text-gray-500 mt-0.5 truncate">
                                                            @if($profile->isCorporate())
                                                                {{ $profile->company_name }}
                                                            @else
                                                                {{ $profile->identity_number ? 'TC: ' . $profile->identity_number : '-' }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                                    {{-- Edit Button --}}
                                                    <button @click.stop="
                                                        if (isEditing) {
                                                            isEditing = false;
                                                            editBillingProfileMode = false;
                                                            $wire.set('edit_billing_profile_id', null);
                                                        } else {
                                                            isEditing = true;
                                                            editBillingProfileMode = true;
                                                            newBillingProfileType = '{{ $profile->type }}';
                                                            $wire.set('edit_billing_profile_id', {{ $profile->billing_profile_id }});
                                                            $wire.set('new_billing_profile_title', '{{ addslashes($profile->title) }}');
                                                            $wire.set('new_billing_profile_type', '{{ $profile->type }}');
                                                            $wire.set('new_billing_profile_identity_number', '{{ $profile->identity_number ?? '' }}');
                                                            $wire.set('new_billing_profile_company_name', '{{ addslashes($profile->company_name ?? '') }}');
                                                            $wire.set('new_billing_profile_tax_number', '{{ $profile->tax_number ?? '' }}');
                                                            $wire.set('new_billing_profile_tax_office', '{{ addslashes($profile->tax_office ?? '') }}');
                                                        }
                                                    "
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-blue-500/20 rounded text-blue-400 hover:text-blue-300"
                                                            title="Düzenle">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    {{-- Delete Button --}}
                                                    <button @click.stop="showDeleteWarning = true; deleteTargetId = {{ $profile->billing_profile_id }}; deleteTargetType = 'billing_profile'; deleteTargetTitle = '{{ $profile->title }}'"
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-red-500/20 rounded text-red-400 hover:text-red-300"
                                                            title="Sil">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                    {{-- Star: Varsayılan Profil (Sonraki sayfa yüklemelerinde otomatik gelir) --}}
                                                    <div x-show="defaultBillingProfileId == {{ $profile->billing_profile_id }}"
                                                         class="p-1.5 text-yellow-500"
                                                         title="Varsayılan Profil (Sonraki açılışta otomatik gelir)">
                                                        <i class="fas fa-star text-xs"></i>
                                                    </div>
                                                    <button x-show="defaultBillingProfileId != {{ $profile->billing_profile_id }}"
                                                            @click.stop="defaultBillingProfileId = {{ $profile->billing_profile_id }}; $wire.setDefaultBillingProfile({{ $profile->billing_profile_id }})"
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-yellow-500/20 rounded text-yellow-500 hover:text-yellow-400"
                                                            title="Varsayılan Yap (Sonraki açılışta otomatik gelir)">
                                                        <i class="far fa-star text-xs"></i>
                                                    </button>
                                                    {{-- Checkbox (Profil Seç) --}}
                                                    <div @click.stop="billingProfileId = {{ $profile->billing_profile_id }}; showList = false"
                                                         class="w-5 h-5 rounded-full border-2 flex items-center justify-center cursor-pointer hover:border-blue-500 transition-colors duration-200"
                                                         :class="billingProfileId == {{ $profile->billing_profile_id }} ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'"
                                                         title="Profil Seç">
                                                        <i class="fa-solid fa-check text-[10px] text-white transition-opacity duration-200"
                                                           :class="billingProfileId == {{ $profile->billing_profile_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>

                                    {{-- İnline Edit Form - Kartın Altında --}}
                                    <div x-show="isEditing" x-cloak x-transition.duration.200ms class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-800/80 dark:border-gray-700 space-y-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300">Profil Düzenle</span>
                                            <button @click="isEditing = false; editBillingProfileMode = false; $wire.set('edit_billing_profile_id', null)"
                                                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                        {{-- Tip Seçimi: Sadece yeni profil eklerken göster (Edit modunda gizle) --}}
                                        <div x-show="!editBillingProfileMode" x-cloak>
                                            <div class="flex gap-2 mb-3">
                                                <button type="button" @click="checkTypeSwitch('individual')"
                                                        :class="newBillingProfileType === 'individual' ? 'bg-blue-600 text-white dark:bg-gray-600 dark:text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-100 dark:bg-gray-700 dark:text-gray-600 dark:text-gray-400'"
                                                        class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200">
                                                    <i class="fa-solid fa-user mr-1.5"></i>Bireysel
                                                </button>
                                                <button type="button" @click="checkTypeSwitch('corporate')"
                                                        :class="newBillingProfileType === 'corporate' ? 'bg-blue-600 text-white dark:bg-gray-600 dark:text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-100 dark:bg-gray-700 dark:text-gray-600 dark:text-gray-400'"
                                                        class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200">
                                                    <i class="fa-solid fa-building mr-1.5"></i>Kurumsal
                                                </button>
                                            </div>
                                        </div>
                                        {{-- Tip Değişiklik Uyarısı: Sadece yeni profil eklerken göster (Edit modunda gizle) --}}
                                        <div x-show="!editBillingProfileMode && showTypeSwitchWarning" x-cloak x-transition.duration.200ms
                                             class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3 mb-3">
                                            <p class="text-sm text-yellow-400">
                                                <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                                                <span x-show="pendingType === 'individual'">
                                                    <strong>Bireysel'e</strong> geçerseniz <strong>Kurumsal bilgiler silinecektir</strong>.
                                                </span>
                                                <span x-show="pendingType === 'corporate'">
                                                    <strong>Kurumsal'a</strong> geçerseniz <strong>Bireysel bilgiler silinecektir</strong>.
                                                </span>
                                            </p>
                                            <div class="flex gap-2 mt-3">
                                                <button type="button" @click="confirmTypeSwitch()"
                                                        class="px-4 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                                    <i class="fa-solid fa-check mr-1"></i>Devam Et
                                                </button>
                                                <button type="button" @click="cancelTypeSwitch()"
                                                        class="px-4 py-1.5 bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                                    <i class="fa-solid fa-times mr-1"></i>İptal
                                                </button>
                                            </div>
                                        </div>
                                        <div x-show="newBillingProfileType === 'individual'">
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İsim Soyisim <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="new_billing_profile_title" placeholder="Örn: Ahmet Yılmaz"
                                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-100 dark:bg-gray-700 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                        </div>
                                        <div x-show="newBillingProfileType === 'individual'">
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">TC Kimlik No</label>
                                            <input type="text" wire:model="new_billing_profile_identity_number" maxlength="11"
                                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-100 dark:bg-gray-700 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                        </div>
                                        <div x-show="newBillingProfileType === 'corporate'" class="space-y-3">
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Şirket Ünvanı <span class="text-red-500">*</span></label>
                                                <input type="text" wire:model="new_billing_profile_company_name"
                                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-100 dark:bg-gray-700 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi No <span class="text-red-500">*</span></label>
                                                    <input type="text" wire:model="new_billing_profile_tax_number" maxlength="10"
                                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-100 dark:bg-gray-700 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi Dairesi <span class="text-red-500">*</span></label>
                                                    <input type="text" wire:model="new_billing_profile_tax_office"
                                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-100 dark:bg-gray-700 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <button wire:click="saveNewBillingProfile"
                                                    wire:loading.attr="disabled"
                                                    wire:target="saveNewBillingProfile"
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white text-sm font-medium rounded-lg">
                                                <span wire:loading.remove wire:target="saveNewBillingProfile"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                                <span wire:loading wire:target="saveNewBillingProfile"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div x-show="!showNewBillingProfile" @click="showNewBillingProfile = true"
                             class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-4 cursor-pointer hover:bg-gray-800">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Henüz fatura profili eklenmedi.
                                <span class="underline ml-1">Profil Ekle</span>
                            </p>
                        </div>
                    @endif
                    @error('billing_profile_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                @php
                    $shippingAddr = $shipping_address_id ? \Modules\Cart\App\Models\Address::find($shipping_address_id) : null;
                    $billingAddr = $billing_address_id ? \Modules\Cart\App\Models\Address::find($billing_address_id) : null;
                    $userAddresses = auth()->check() ? \Modules\Cart\App\Models\Address::where('user_id', auth()->id())->get() : collect();
                    $hasAddresses = $userAddresses->count() > 0;
                @endphp

                @if($requiresShipping)
                {{-- 3. TESLİMAT ADRESİ (Fiziksel ürünler için) --}}
                <div class="card-glass rounded-2xl p-6 mb-4">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-location-dot text-gray-700 dark:text-gray-500 mr-3"></i>
                            Teslimat Adresi
                        </h2>
                        <button @click="showNewShippingForm = !showNewShippingForm; $wire.set('edit_address_id', null)"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <i class="fa-solid fa-plus mr-1"></i>Ekle
                        </button>
                    </div>

                    {{-- Yeni Adres Formu (Header'ın hemen altında) --}}
                    <div x-show="showNewShippingForm" x-cloak x-transition.duration.200ms class="space-y-4 pt-3 border-t border-gray-200 dark:border-gray-700 mb-3"
                         x-data="{ editMode: @entangle('edit_address_id').live }">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300" x-text="editMode ? 'Adresi Düzenle' : 'Yeni Adres'"></span>
                            <button @click="showNewShippingForm = false; $wire.set('edit_address_id', null)" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="new_address_title" placeholder="Örn: Evim"
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_address_title') border-red-500 @enderror">
                            @error('new_address_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                <select wire:model="new_address_city" id="shipping_city"
                                        @change="
                                            $wire.set('new_address_district', '');
                                            fetch('/api/get-districts/' + $event.target.value)
                                                .then(r => r.json())
                                                .then(data => {
                                                    let select = document.getElementById('shipping_district');
                                                    select.innerHTML = '<option value=\'\'>Seçin</option>';
                                                    data.forEach(d => {
                                                        select.innerHTML += '<option value=\'' + d + '\'>' + d + '</option>';
                                                    });
                                                });
                                        "
                                        class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_address_city') border-red-500 @enderror">
                                    <option value="">Seçin</option>
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                                @error('new_address_city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                <select wire:model="new_address_district" id="shipping_district"
                                        class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_address_district') border-red-500 @enderror">
                                    <option value="">{{ empty($new_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                    @foreach($districts ?? [] as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                                @error('new_address_district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Posta Kodu</label>
                                <input type="text" wire:model="new_address_postal" placeholder="34000"
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                            <textarea wire:model="new_address_line" rows="2" placeholder="Mahalle, sokak, bina no, daire"
                                      class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm resize-none @error('new_address_line') border-red-500 @enderror"></textarea>
                            @error('new_address_line') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="saveNewAddress('shipping')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 disabled:bg-gray-100 dark:bg-gray-700 disabled:cursor-wait text-gray-900 dark:text-white text-sm font-medium rounded-lg">
                                <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                            </button>
                        </div>
                    </div>

                    @if($userAddresses->count() > 0)
                        {{-- Seçili Adres Özeti (Compact Minimal - Reactive) --}}
                        @foreach($userAddresses as $addr)
                            <div wire:key="shipping-summary-{{ $addr->address_id }}"
                                 x-show="shippingAddressId == {{ $addr->address_id }}"
                                 style="display: {{ $shipping_address_id == $addr->address_id ? 'flex' : 'none' }}"
                                 class="flex items-center justify-between gap-3 py-3 px-4 mb-3 bg-gray-100 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                                        @if($addr->is_default_shipping)
                                            <span class="mr-2 text-yellow-500 dark:text-yellow-400" title="Varsayılan Teslimat Adresi">★</span>
                                        @endif
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $addr->title }}</span>
                                        <span class="mx-2 text-gray-400 dark:text-gray-600">•</span>
                                        <span>{{ $addr->city }}</span>
                                    </p>
                                    <button @click="showShippingList = !showShippingList"
                                            title="Adresleri Düzenle"
                                            class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-1 transition-colors duration-200">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                </div>
                        @endforeach

                        {{-- Adres Listesi (Collapsible) --}}
                        <div x-show="showShippingList" x-cloak x-transition.duration.200ms class="space-y-2 mb-4">
                            @foreach($userAddresses as $addr)
                                <div wire:key="shipping-address-{{ $addr->address_id }}"
                                     class="relative"
                                     x-data="{ isEditing: false }">
                                    <div @click="shippingAddressId = {{ $addr->address_id }}; showShippingList = false"
                                         class="p-3 rounded-xl border-2 transition-[border-color,background-color] duration-200 group cursor-pointer"
                                         :class="shippingAddressId == {{ $addr->address_id }} ? 'border-gray-300 bg-gray-100 dark:bg-gray-800 dark:border-gray-400' : 'border-gray-200 bg-gray-50 dark:bg-slate-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500'">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                                    <i class="fa-solid fa-location-dot text-gray-700 dark:text-gray-400"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white block">{{ $addr->title }}</span>
                                                    <p class="text-xs text-gray-600 dark:text-gray-500 mt-0.5 truncate">
                                                        {{ $addr->address_line_1 }}, {{ $addr->district }}/{{ $addr->city }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                                {{-- Edit Button --}}
                                                <button @click.stop="
                                                    if (isEditing) {
                                                        isEditing = false;
                                                        $wire.set('edit_address_id', null);
                                                    } else {
                                                        isEditing = true;
                                                        $wire.set('edit_address_id', {{ $addr->address_id }});
                                                        $wire.set('new_address_title', '{{ addslashes($addr->title) }}');
                                                        $wire.set('new_address_phone', '{{ $addr->phone ?? '' }}');
                                                        $wire.set('new_address_line', '{{ addslashes($addr->address_line_1) }}');
                                                        $wire.set('new_address_city', '{{ $addr->city }}');
                                                        $wire.set('new_address_district', '{{ $addr->district }}');
                                                        $wire.set('new_address_postal', '{{ $addr->postal_code ?? '' }}');
                                                    }
                                                "
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-blue-500/20 rounded text-blue-400 hover:text-blue-300"
                                                        title="Düzenle">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                {{-- Delete Button --}}
                                                <button @click.stop="showDeleteWarning = true; deleteTargetId = {{ $addr->address_id }}; deleteTargetType = 'shipping_address'; deleteTargetTitle = '{{ $addr->title }}'"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-red-500/20 rounded text-red-400 hover:text-red-300"
                                                        title="Sil">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                                {{-- Star: Varsayılan Adres (Sonraki sayfa yüklemelerinde otomatik gelir) --}}
                                                <div x-show="defaultShippingAddressId == {{ $addr->address_id }}"
                                                     class="p-1.5 text-yellow-500"
                                                     title="Varsayılan Teslimat Adresi (Sonraki açılışta otomatik gelir)">
                                                    <i class="fas fa-star text-xs"></i>
                                                </div>
                                                <button x-show="defaultShippingAddressId != {{ $addr->address_id }}"
                                                        @click.stop="defaultShippingAddressId = {{ $addr->address_id }}; shippingAddressId = {{ $addr->address_id }}; $wire.setDefaultAddress({{ $addr->address_id }}, 'shipping')"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-yellow-500/20 rounded text-yellow-500 hover:text-yellow-400"
                                                        title="Varsayılan Yap (Sonraki açılışta otomatik gelir)">
                                                    <i class="far fa-star text-xs"></i>
                                                </button>
                                                {{-- Checkbox (Adres Seç) --}}
                                                <div @click.stop="shippingAddressId = {{ $addr->address_id }}; showShippingList = false"
                                                     class="w-5 h-5 rounded-full border-2 flex items-center justify-center cursor-pointer hover:border-blue-500 transition-colors duration-200"
                                                     :class="shippingAddressId == {{ $addr->address_id }} ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'"
                                                     title="Adres Seç">
                                                    <i class="fa-solid fa-check text-[10px] text-white transition-opacity duration-200"
                                                       :class="shippingAddressId == {{ $addr->address_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- İnline Edit Form - Kartın Altında --}}
                                    <div x-show="isEditing" x-cloak x-transition.duration.200ms class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-800/80 dark:border-gray-700 space-y-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300">Adresi Düzenle</span>
                                            <button @click="isEditing = false; $wire.set('edit_address_id', null)"
                                                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="new_address_title" placeholder="Örn: Evim"
                                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                        </div>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                                <select wire:model="new_address_city" id="shipping_city_edit_{{ $addr->address_id }}"
                                                        @change="
                                                            $wire.set('new_address_district', '');
                                                            fetch('/api/get-districts/' + $event.target.value)
                                                                .then(r => r.json())
                                                                .then(data => {
                                                                    let select = document.getElementById('shipping_district_edit_{{ $addr->address_id }}');
                                                                    select.innerHTML = '<option value=\'\'>Seçin</option>';
                                                                    data.forEach(d => {
                                                                        select.innerHTML += '<option value=\'' + d + '\'>' + d + '</option>';
                                                                    });
                                                                });
                                                        "
                                                        class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                    <option value="">Seçin</option>
                                                    @foreach($cities ?? [] as $city)
                                                        <option value="{{ $city }}">{{ $city }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                                <select wire:model="new_address_district" id="shipping_district_edit_{{ $addr->address_id }}"
                                                        class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                    <option value="">{{ empty($new_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                                    @foreach($districts ?? [] as $district)
                                                        <option value="{{ $district }}">{{ $district }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Posta Kodu</label>
                                                <input type="text" wire:model="new_address_postal" placeholder="34000"
                                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                                            <textarea wire:model="new_address_line" rows="2" placeholder="Mahalle, sokak, bina no, daire"
                                                      class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm resize-none"></textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button wire:click="saveNewAddress('shipping')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                                <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                                <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Adres Yok Mesajı --}}
                        <div @click="showNewShippingForm = true"
                             class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-4 cursor-pointer hover:bg-gray-800 transition-colors duration-200">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Henüz teslimat adresi eklenmedi.
                                <span class="underline ml-1">Adres Ekle</span>
                            </p>
                        </div>
                    @endif
                    @error('shipping_address_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    {{-- Fatura Adresi Checkbox --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-5 pt-5">
                        <label @click="billingSameAsShipping = !billingSameAsShipping" class="inline-flex items-center gap-3 cursor-pointer group">
                            {{-- Modern Checkbox --}}
                            <div class="relative flex-shrink-0">
                                <div class="w-5 h-5 rounded border-2 transition-colors duration-200"
                                     :class="billingSameAsShipping ? 'bg-blue-600 border-blue-600' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 group-hover:border-blue-400'">
                                    <svg x-show="billingSameAsShipping" class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-200">
                                Fatura adresi teslimat ile aynı
                            </span>
                        </label>

                        {{-- Farklı Fatura Adresi --}}
                        <div x-show="!billingSameAsShipping" x-cloak x-transition.duration.200ms class="mt-4 space-y-3">
                            {{-- Header --}}
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <i class="fa-solid fa-file-invoice-dollar text-gray-700 dark:text-gray-500 mr-3"></i>
                                    Fatura Adresi
                                </h2>
                                <button @click="showNewBillingForm = !showNewBillingForm; $wire.set('edit_billing_address_id', null)"
                                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fa-solid fa-plus mr-1"></i>Ekle
                                </button>
                            </div>

                            {{-- Yeni Fatura Adresi Formu (Header'ın hemen altında) --}}
                            <div x-show="showNewBillingForm" x-cloak x-transition.duration.200ms class="space-y-4 pt-3 border-t border-gray-200 dark:border-gray-700 mb-3"
                                 x-data="{ editMode: @entangle('edit_billing_address_id').live }">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-300" x-text="editMode ? 'Adresi Düzenle' : 'Yeni Adres'"></span>
                                    <button @click="showNewBillingForm = false; $wire.set('edit_billing_address_id', null)" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_billing_address_title" placeholder="Örn: Şirket"
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_address_title') border-red-500 @enderror">
                                    @error('new_billing_address_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                        <select wire:model="new_billing_address_city" id="billing_city"
                                                @change="
                                                    $wire.set('new_billing_address_district', '');
                                                    fetch('/api/get-districts/' + $event.target.value)
                                                        .then(r => r.json())
                                                        .then(data => {
                                                            let select = document.getElementById('billing_district');
                                                            select.innerHTML = '<option value=\'\'>Seçin</option>';
                                                            data.forEach(d => {
                                                                select.innerHTML += '<option value=\'' + d + '\'>' + d + '</option>';
                                                            });
                                                        });
                                                "
                                                class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_address_city') border-red-500 @enderror">
                                            <option value="">Seçin</option>
                                            @foreach($cities ?? [] as $city)
                                                <option value="{{ $city }}">{{ $city }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_billing_address_city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                        <select wire:model="new_billing_address_district" id="billing_district"
                                                class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_address_district') border-red-500 @enderror">
                                            <option value="">{{ empty($new_billing_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                            @foreach($billingDistricts ?? [] as $district)
                                                <option value="{{ $district }}">{{ $district }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_billing_address_district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Posta Kodu</label>
                                        <input type="text" wire:model="new_billing_address_postal"
                                               class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                                    <textarea wire:model="new_billing_address_line" rows="2" placeholder="Mahalle, sokak, bina no, daire"
                                              class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm resize-none @error('new_billing_address_line') border-red-500 @enderror"></textarea>
                                    @error('new_billing_address_line') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex justify-end">
                                    <button wire:click="saveNewAddress('billing')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 disabled:bg-gray-100 dark:bg-gray-700 disabled:cursor-wait text-gray-900 dark:text-white text-sm font-medium rounded-lg">
                                        <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                        <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                    </button>
                                </div>
                            </div>

                            @if($userAddresses->count() > 0)
                                {{-- Seçili Fatura Adresi Özeti (Compact Minimal - Reactive) --}}
                                @foreach($userAddresses as $addr)
                                    <div wire:key="billing-summary-{{ $addr->address_id }}"
                                         x-show="billingAddressId == {{ $addr->address_id }}"
                                         style="display: {{ $billing_address_id == $addr->address_id ? 'flex' : 'none' }}"
                                         class="flex items-center justify-between gap-3 py-3 px-4 mb-3 bg-gray-100 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                                                @if($addr->is_default_billing)
                                                    <span class="mr-2 text-yellow-500 dark:text-yellow-400" title="Varsayılan Fatura Adresi">★</span>
                                                @endif
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $addr->title }}</span>
                                                <span class="mx-2 text-gray-400 dark:text-gray-600">•</span>
                                                <span>{{ $addr->city }}</span>
                                            </p>
                                            <button @click="showBillingList = !showBillingList"
                                                    title="Adresleri Düzenle"
                                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-1 transition-colors duration-200">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </button>
                                        </div>
                                @endforeach

                                {{-- Fatura Adresi Listesi (Collapsible) --}}
                                <div x-show="showBillingList" x-cloak x-transition.duration.200ms class="space-y-2 mb-4">
                                    @foreach($userAddresses as $addr)
                                        <div wire:key="billing-address-{{ $addr->address_id }}"
                                             class="relative"
                                             x-data="{ isEditing: false }">
                                            <div @click="billingAddressId = {{ $addr->address_id }}; showBillingList = false"
                                                 class="p-3 rounded-xl border-2 transition-[border-color,background-color] duration-200 group cursor-pointer"
                                                 :class="billingAddressId == {{ $addr->address_id }} ? 'border-gray-300 bg-gray-100 dark:bg-gray-800 dark:border-gray-400' : 'border-gray-200 bg-gray-50 dark:bg-slate-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500'">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                                            <i class="fa-solid fa-file-invoice-dollar text-gray-700 dark:text-gray-400"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white block">{{ $addr->title }}</span>
                                                            <p class="text-xs text-gray-600 dark:text-gray-500 mt-0.5 truncate">
                                                                {{ $addr->address_line_1 }}, {{ $addr->district }}/{{ $addr->city }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                                        {{-- Edit Button --}}
                                                        <button @click.stop="
                                                            if (isEditing) {
                                                                isEditing = false;
                                                                $wire.set('edit_billing_address_id', null);
                                                            } else {
                                                                isEditing = true;
                                                                $wire.set('edit_billing_address_id', {{ $addr->address_id }});
                                                                $wire.set('new_billing_address_title', '{{ addslashes($addr->title) }}');
                                                                $wire.set('new_billing_address_phone', '{{ $addr->phone ?? '' }}');
                                                                $wire.set('new_billing_address_line', '{{ addslashes($addr->address_line_1) }}');
                                                                $wire.set('new_billing_address_city', '{{ $addr->city }}');
                                                                $wire.set('new_billing_address_district', '{{ $addr->district }}');
                                                                $wire.set('new_billing_address_postal', '{{ $addr->postal_code ?? '' }}');
                                                            }
                                                        "
                                                                class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-blue-500/20 rounded text-blue-400 hover:text-blue-300"
                                                                title="Düzenle">
                                                            <i class="fas fa-edit text-xs"></i>
                                                        </button>
                                                        {{-- Delete Button --}}
                                                        <button @click.stop="showDeleteWarning = true; deleteTargetId = {{ $addr->address_id }}; deleteTargetType = 'billing_address'; deleteTargetTitle = '{{ $addr->title }}'"
                                                                class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-red-500/20 rounded text-red-400 hover:text-red-300"
                                                                title="Sil">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                        {{-- Star: Varsayılan Adres (Sonraki sayfa yüklemelerinde otomatik gelir) --}}
                                                        <div x-show="defaultBillingAddressId == {{ $addr->address_id }}"
                                                             class="p-1.5 text-yellow-500"
                                                             title="Varsayılan Fatura Adresi (Sonraki açılışta otomatik gelir)">
                                                            <i class="fas fa-star text-xs"></i>
                                                        </div>
                                                        <button x-show="defaultBillingAddressId != {{ $addr->address_id }}"
                                                                @click.stop="defaultBillingAddressId = {{ $addr->address_id }}; billingAddressId = {{ $addr->address_id }}; $wire.setDefaultAddress({{ $addr->address_id }}, 'billing')"
                                                                class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-yellow-500/20 rounded text-yellow-500 hover:text-yellow-400"
                                                                title="Varsayılan Yap (Sonraki açılışta otomatik gelir)">
                                                            <i class="far fa-star text-xs"></i>
                                                        </button>
                                                        {{-- Checkbox (Adres Seç) --}}
                                                        <div @click.stop="billingAddressId = {{ $addr->address_id }}; showBillingList = false"
                                                             class="w-5 h-5 rounded-full border-2 flex items-center justify-center cursor-pointer hover:border-blue-500 transition-colors duration-200"
                                                             :class="billingAddressId == {{ $addr->address_id }} ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'"
                                                             title="Adres Seç">
                                                            <i class="fa-solid fa-check text-[10px] text-white transition-opacity duration-200"
                                                               :class="billingAddressId == {{ $addr->address_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- İnline Edit Form - Kartın Altında --}}
                                                <div x-show="isEditing" x-cloak x-transition.duration.200ms class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-800/80 dark:border-gray-700 space-y-3">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-300">Adresi Düzenle</span>
                                                        <button @click="isEditing = false; $wire.set('edit_billing_address_id', null)"
                                                                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                                            <i class="fa-solid fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                                                        <input type="text" wire:model="new_billing_address_title"
                                                               class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                    </div>
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                                        <div>
                                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                                            <select wire:model="new_billing_address_city" id="billing_city_edit_{{ $addr->address_id }}"
                                                                    @change="
                                                                        $wire.set('new_billing_address_district', '');
                                                                        fetch('/api/get-districts/' + $event.target.value)
                                                                            .then(r => r.json())
                                                                            .then(data => {
                                                                                let select = document.getElementById('billing_district_edit_{{ $addr->address_id }}');
                                                                                select.innerHTML = '<option value=\'\'>Seçin</option>';
                                                                                data.forEach(d => {
                                                                                    select.innerHTML += '<option value=\'' + d + '\'>' + d + '</option>';
                                                                                });
                                                                            });
                                                                    "
                                                                    class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                                <option value="">Seçin</option>
                                                                @foreach($cities ?? [] as $city)
                                                                    <option value="{{ $city }}">{{ $city }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                                            <select wire:model="new_billing_address_district" id="billing_district_edit_{{ $addr->address_id }}"
                                                                    class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                                <option value="">{{ empty($new_billing_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                                                @foreach($billingDistricts ?? [] as $district)
                                                                    <option value="{{ $district }}">{{ $district }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Posta Kodu</label>
                                                            <input type="text" wire:model="new_billing_address_postal"
                                                                   class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                                                        <textarea wire:model="new_billing_address_line" rows="2"
                                                                  class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm resize-none"></textarea>
                                                    </div>
                                                    <div class="flex justify-end">
                                                        <button wire:click="saveNewAddress('billing')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                                            <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                                            <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- Adres Yok Mesajı --}}
                                <div @click="showNewBillingForm = true"
                                     class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-4 cursor-pointer hover:bg-gray-800 transition-colors duration-200">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fa-solid fa-info-circle mr-2"></i>
                                        Henüz fatura adresi eklenmedi.
                                        <span class="underline ml-1">Adres Ekle</span>
                                    </p>
                                </div>
                            @endif
                            @error('billing_address_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                @else
                {{-- DİJİTAL ÜRÜNLER İÇİN - SADECE FATURA ADRESİ --}}
                <div class="card-glass rounded-2xl p-6 mb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-file-invoice text-gray-700 dark:text-gray-500 mr-3"></i>
                            Fatura Adresi
                        </h2>
                        <button @click="showNewBillingForm = !showNewBillingForm; $wire.set('edit_billing_address_id', null)"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <i class="fa-solid fa-plus mr-1"></i>Ekle
                        </button>
                    </div>

                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 mb-4">
                        <p class="text-sm text-blue-400">
                            <i class="fa-solid fa-info-circle mr-2"></i>
                            Dijital ürün satın alıyorsunuz. Teslimat adresi gerekmez, sadece fatura adresi yeterlidir.
                        </p>
                    </div>

                    {{-- Yeni Fatura Adresi Formu (Header'ın hemen altında) --}}
                    <div x-show="showNewBillingForm" x-cloak x-transition.duration.200ms class="space-y-4 pt-3 border-t border-gray-200 dark:border-gray-700 mb-3"
                         x-data="{ editMode: @entangle('edit_billing_address_id').live }">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300" x-text="editMode ? 'Adresi Düzenle' : 'Yeni Adres'"></span>
                            <button @click="showNewBillingForm = false; $wire.set('edit_billing_address_id', null)" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white" title="Kapat">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="new_billing_address_title" placeholder="Örn: Şirket"
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_address_title') border-red-500 @enderror">
                            @error('new_billing_address_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                <select wire:model="new_billing_address_city" id="billing_city_digital"
                                        @change="
                                            $wire.set('new_billing_address_district', '');
                                            fetch('/api/get-districts/' + $event.target.value)
                                                .then(r => r.json())
                                                .then(data => {
                                                    let select = document.getElementById('billing_district_digital');
                                                    select.innerHTML = '<option value=\'\'>Seçin</option>';
                                                    data.forEach(d => {
                                                        select.innerHTML += '<option value=\'' + d + '\'>' + d + '</option>';
                                                    });
                                                });
                                        "
                                        class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_address_city') border-red-500 @enderror">
                                    <option value="">Seçin</option>
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                                @error('new_billing_address_city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                <select wire:model="new_billing_address_district" id="billing_district_digital"
                                        class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('new_billing_address_district') border-red-500 @enderror">
                                    <option value="">{{ empty($new_billing_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                    @foreach($billingDistricts ?? [] as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                                @error('new_billing_address_district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Posta Kodu</label>
                                <input type="text" wire:model="new_billing_address_postal"
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                            <textarea wire:model="new_billing_address_line" rows="2" placeholder="Mahalle, sokak, bina no, daire"
                                      class="w-full px-3 py-2.5 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm resize-none @error('new_billing_address_line') border-red-500 @enderror"></textarea>
                            @error('new_billing_address_line') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="saveNewAddress('billing')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                            </button>
                        </div>
                    </div>

                    {{-- Seçili Fatura Adresi Özeti (Compact Minimal) --}}
                    @if($billingAddr)
                        <div class="flex items-center justify-between gap-3 py-3 px-4 mb-3 bg-gray-100 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                                <span class="text-gray-900 dark:text-white font-medium">{{ $billingAddr->title }}</span>
                                <span class="mx-2 text-gray-400 dark:text-gray-600">•</span>
                                <span>{{ $billingAddr->city }}</span>
                            </p>
                            <button @click="showBillingList = !showBillingList"
                                    title="Adresleri Düzenle"
                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-1 transition-colors duration-200">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                        </div>
                    @else
                        <div @click="showNewBillingForm = true"
                             class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-gray-300 dark:hover:border-gray-500 transition-colors duration-200 mb-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Fatura adresi seçilmedi.
                                <span class="underline ml-1">Adres Ekle</span>
                            </p>
                        </div>
                    @endif

                    {{-- Fatura Adresi Listesi (Collapsible) --}}
                    @if($userAddresses->count() > 0)
                        <div x-show="showBillingList" x-cloak x-transition.duration.200ms class="space-y-2 mb-4">
                            @foreach($userAddresses as $addr)
                                <div wire:key="billing-address-digital-{{ $addr->address_id }}"
                                     class="relative group">
                                    <div class="p-3 rounded-xl border-2 transition-[border-color,background-color] duration-200 cursor-pointer"
                                         :class="billingAddressId == {{ $addr->address_id }} ? 'border-gray-300 bg-gray-100 dark:bg-gray-800 dark:border-gray-400' : 'border-gray-200 bg-gray-50 dark:bg-slate-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500'"
                                         @click="billingAddressId = {{ $addr->address_id }}; showBillingList = false">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 flex-shrink-0">
                                                <i class="fa-solid fa-file-invoice text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white block">{{ $addr->title }}</span>
                                                <p class="text-xs text-gray-600 dark:text-gray-500 mt-0.5 truncate">
                                                    {{ $addr->address_line_1 }}, {{ $addr->district }} / {{ $addr->city }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                {{-- Edit Button --}}
                                                <button @click.stop="
                                                    if (isEditing) {
                                                        isEditing = false;
                                                        $wire.set('edit_billing_address_id', null);
                                                    } else {
                                                        isEditing = true;
                                                        $wire.set('edit_billing_address_id', {{ $addr->address_id }});
                                                        $wire.set('new_billing_address_title', '{{ addslashes($addr->title) }}');
                                                        $wire.set('new_billing_address_phone', '{{ $addr->phone ?? '' }}');
                                                        $wire.set('new_billing_address_line', '{{ addslashes($addr->address_line_1) }}');
                                                        $wire.set('new_billing_address_city', '{{ $addr->city }}');
                                                        $wire.set('new_billing_address_district', '{{ $addr->district }}');
                                                        $wire.set('new_billing_address_postal', '{{ $addr->postal_code ?? '' }}');
                                                    }
                                                "
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-blue-500/20 rounded text-blue-400 hover:text-blue-300"
                                                        title="Düzenle">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                {{-- Delete Button --}}
                                                <button @click.stop="showDeleteWarning = true; deleteTargetId = {{ $addr->address_id }}; deleteTargetType = 'billing_address'; deleteTargetTitle = '{{ $addr->title }}'"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-red-500/20 rounded text-red-400 hover:text-red-300"
                                                        title="Sil">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                                {{-- Star: Varsayılan Adres (Sonraki sayfa yüklemelerinde otomatik gelir) --}}
                                                <div x-show="defaultBillingAddressId == {{ $addr->address_id }}"
                                                     class="p-1.5 text-yellow-500"
                                                     title="Varsayılan Fatura Adresi (Sonraki açılışta otomatik gelir)">
                                                    <i class="fas fa-star text-xs"></i>
                                                </div>
                                                <button x-show="defaultBillingAddressId != {{ $addr->address_id }}"
                                                        @click.stop="defaultBillingAddressId = {{ $addr->address_id }}; billingAddressId = {{ $addr->address_id }}; $wire.setDefaultAddress({{ $addr->address_id }}, 'billing')"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 hover:bg-yellow-500/20 rounded text-yellow-500 hover:text-yellow-400"
                                                        title="Varsayılan Yap (Sonraki açılışta otomatik gelir)">
                                                    <i class="far fa-star text-xs"></i>
                                                </button>
                                                {{-- Checkbox (Adres Seç) --}}
                                                <div @click.stop="billingAddressId = {{ $addr->address_id }}; showBillingList = false"
                                                     class="w-5 h-5 rounded-full border-2 flex items-center justify-center cursor-pointer hover:border-blue-500 transition-colors duration-200"
                                                     :class="billingAddressId == {{ $addr->address_id }} ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'"
                                                     title="Adres Seç">
                                                    <i class="fa-solid fa-check text-[10px] text-white transition-opacity duration-200"
                                                       :class="billingAddressId == {{ $addr->address_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- İnline Edit Form - Kartın Altında --}}
                                    <div x-show="isEditing" x-cloak x-transition.duration.200ms class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-800/80 dark:border-gray-700 space-y-3">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300">Adresi Düzenle</span>
                                            <button @click="isEditing = false; $wire.set('edit_billing_address_id', null)"
                                                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="new_billing_address_title"
                                                   class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                        </div>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                                <select wire:model="new_billing_address_city" id="billing_city_edit_digital_{{ $addr->address_id }}"
                                                        @change="
                                                            $wire.set('new_billing_address_district', '');
                                                            fetch('/api/get-districts/' + $event.target.value)
                                                                .then(r => r.json())
                                                                .then(data => {
                                                                    let select = document.getElementById('billing_district_edit_digital_{{ $addr->address_id }}');
                                                                    select.innerHTML = '<option value=\'\'>Seçin</option>';
                                                                    data.forEach(d => {
                                                                        select.innerHTML += '<option value=\'' + d + '\'>' + d + '</option>';
                                                                    });
                                                                });
                                                        "
                                                        class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                    <option value="">Seçin</option>
                                                    @foreach($cities ?? [] as $city)
                                                        <option value="{{ $city }}">{{ $city }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                                <select wire:model="new_billing_address_district" id="billing_district_edit_digital_{{ $addr->address_id }}"
                                                        class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                                    <option value="">{{ empty($new_billing_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                                    @foreach($billingDistricts ?? [] as $district)
                                                        <option value="{{ $district }}">{{ $district }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Posta Kodu</label>
                                                <input type="text" wire:model="new_billing_address_postal"
                                                       class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                                            <textarea wire:model="new_billing_address_line" rows="2"
                                                      class="w-full px-3 py-2 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm resize-none"></textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button wire:click="saveNewAddress('billing')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                                <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                                <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @error('billing_address_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif

            </div>

            {{-- ===================== SAĞ KOLON ===================== --}}
            <div style="flex: 1; min-width: 280px;">
                <div class="card-glass rounded-2xl sticky top-6 overflow-hidden">

                    {{-- Sipariş Özeti Header --}}
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900/50">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-receipt text-gray-700 dark:text-gray-500 mr-3"></i>
                            Sipariş Özeti
                        </h2>
                    </div>

                    {{-- Fiyatlar --}}
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 space-y-3">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Ara Toplam ({{ $itemCount }} Ürün)</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format(round($subtotal), 0, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>KDV (%20)</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format(round($taxAmount), 0, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Toplam</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format(round($grandTotal), 0, ',', '.') }} ₺</span>
                        </div>
                    </div>

                    {{-- Ödeme Yöntemi --}}
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fa-solid fa-wallet text-gray-700 dark:text-gray-500 mr-2"></i>Ödeme Yöntemi
                        </h3>
                        <div class="space-y-3">
                            @foreach($paymentMethods as $method)
                                <div @click="selectedPaymentMethodId = {{ $method->payment_method_id }}" class="cursor-pointer">
                                    <div class="p-4 rounded-xl border-2 transition-all"
                                         :class="selectedPaymentMethodId === {{ $method->payment_method_id }} ? 'border-blue-500 bg-blue-500/10' : 'border-gray-200 dark:border-gray-700 hover:border-gray-500'">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors"
                                                 :class="selectedPaymentMethodId === {{ $method->payment_method_id }} ? 'bg-blue-500/20' : 'bg-gray-200 dark:bg-gray-700'">
                                                <i class="fa-solid {{ $method->gateway === 'paytr' ? 'fa-credit-card' : 'fa-money-bill-transfer' }}"
                                                   :class="selectedPaymentMethodId === {{ $method->payment_method_id }} ? 'text-blue-400' : 'text-gray-600 dark:text-gray-400'"></i>
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-sm font-semibold" :class="selectedPaymentMethodId === {{ $method->payment_method_id }} ? 'text-blue-400' : 'text-gray-900 dark:text-white'">{{ $method->getTranslated('title') }}</span>
                                                @if($method->gateway === 'paytr')
                                                    <p class="text-xs text-gray-500">Visa, Mastercard, Troy</p>
                                                @endif
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors duration-200"
                                                 :class="selectedPaymentMethodId === {{ $method->payment_method_id }} ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-gray-600'">
                                                <i class="fa-solid fa-check text-[10px] text-gray-900 dark:text-white" x-show="selectedPaymentMethodId === {{ $method->payment_method_id }}"></i>
                                            </div>
                                        </div>
                                        @if($method->gateway === 'manual')
                                            <div x-show="selectedPaymentMethodId === {{ $method->payment_method_id }}" x-cloak class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-400">
                                                <p><strong class="text-gray-900 dark:text-gray-200">Banka:</strong> Türkiye İş Bankası</p>
                                                <p><strong class="text-gray-900 dark:text-gray-200">Hesap:</strong> İXTİF A.Ş.</p>
                                                <p class="mt-2"><strong class="text-gray-900 dark:text-gray-200">IBAN:</strong></p>
                                                <code class="block bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg mt-1 text-xs font-mono">TR51 0006 4000 0011 0372 5092 58</code>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('selectedPaymentMethodId') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Sözleşmeler --}}
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            {{-- Modern Checkbox (Alpine only - no server roundtrip) --}}
                            <div class="relative flex-shrink-0 mt-0.5">
                                <div @click="agreeAll = !agreeAll"
                                     class="w-5 h-5 rounded border-2 transition-colors duration-200"
                                     :class="agreeAll ? 'bg-blue-600 border-blue-600' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 group-hover:border-blue-400'">
                                    <svg x-show="agreeAll" x-cloak class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-200">
                                <a href="/mesafeli-satis" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Ön Bilgilendirme Formu</a>'nu ve
                                <a href="/mesafeli-satis" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Mesafeli Satış Sözleşmesi</a>'ni kabul ediyorum.
                                <span class="text-red-500">*</span>
                            </span>
                        </label>
                        @error('agree_all') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Hatalar --}}
                    @if ($errors->any())
                        <div class="px-5 pt-4">
                            <div class="bg-red-900/20 border border-red-700 rounded-xl p-4">
                                <p class="text-sm text-red-300 font-semibold mb-2">
                                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>Lütfen eksikleri tamamlayın:
                                </p>
                                <ul class="text-sm text-red-400 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Validation Errors Summary --}}
                    @if ($errors->any())
                        <div class="mx-5 mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-exclamation-triangle text-red-400 text-xl mt-0.5"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-red-400 mb-2">Lütfen aşağıdaki hataları düzeltin:</p>
                                    <ul class="text-sm text-red-300 space-y-1 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Ödeme Butonu --}}
                    <div class="p-5">
                        <button
                            x-data="{ processing: false }"
                            @click="
                                if (!agreeAll || processing) return;
                                processing = true;
                                syncToLivewire();
                                $wire.proceedToPayment().then(response => {
                                    if (response && response.redirectUrl) {
                                        window.location.href = response.redirectUrl;
                                    } else {
                                        processing = false;
                                    }
                                }).catch(error => {
                                    console.error('Payment error:', error);
                                    processing = false;
                                });
                            "
                            :disabled="!agreeAll || processing"
                            :class="agreeAll && !processing ? 'bg-green-600 hover:bg-green-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed opacity-60'"
                            class="w-full py-4 rounded-xl font-bold text-lg text-white transition-all">

                            <template x-if="!processing">
                                <span>
                                    <i class="fas fa-lock mr-2"></i>
                                    Ödemeye Geç
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </span>
                            </template>
                            <template x-if="processing">
                                <span>
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    İşleniyor...
                                </span>
                            </template>
                        </button>

                        <p class="text-center text-xs text-gray-500 mt-4">
                            <i class="fas fa-shield-halved text-green-600 mr-1"></i>
                            256-bit SSL ile güvenli ödeme
                        </p>
                    </div>

                </div>
            </div>

        </div>
        @endif
    </div>

    {{-- MODALS --}}

    {{-- Teslimat Adresi Modal --}}
    @if($showShippingModal ?? false)
        <div class="fixed inset-0 z-[999999] overflow-hidden" @keydown.escape.window="$wire.closeShippingModal()">
            <div class="fixed inset-0 bg-black/60" wire:click="closeShippingModal"></div>
            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="w-screen max-w-lg">
                    <div class="flex h-full flex-col bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-900">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                <i class="fa-solid fa-truck mr-2 text-gray-600 dark:text-gray-400"></i>Teslimat Adresi
                            </h3>
                            <button wire:click="closeShippingModal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto px-6 py-4">
                            <livewire:cart::front.address-manager :userId="$customerId" addressType="shipping" :selectedAddressId="$shipping_address_id" :key="'shipping-'.$customerId" />
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-900">
                            <button wire:click="closeShippingModal" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white font-medium py-3 rounded-lg">
                                <i class="fa-solid fa-check mr-2"></i>Tamam
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Fatura Bilgileri Modal --}}
    @if($showBillingModal ?? false)
        <div class="fixed inset-0 z-[999999] overflow-hidden" @keydown.escape.window="$wire.closeBillingModal()">
            <div class="fixed inset-0 bg-black/60" wire:click="closeBillingModal"></div>
            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="w-screen max-w-md">
                    <div class="flex h-full flex-col bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-900">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                <i class="fa-solid fa-file-invoice mr-2 text-gray-600 dark:text-gray-400"></i>Fatura Bilgileri
                            </h3>
                            <button wire:click="closeBillingModal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto px-6 py-4">
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-3">Fatura Türü</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model.live="billing_type" value="individual" class="hidden peer">
                                        <div class="border-2 border-gray-300 dark:border-gray-600 peer-checked:border-gray-400 peer-checked:bg-gray-800 rounded-lg p-4 hover:border-gray-500">
                                            <div class="flex items-center justify-center">
                                                <i class="fa-solid fa-user text-2xl text-gray-600 dark:text-gray-400"></i>
                                            </div>
                                            <div class="text-center mt-2 text-sm font-medium text-gray-900 dark:text-white">Bireysel</div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model.live="billing_type" value="corporate" class="hidden peer">
                                        <div class="border-2 border-gray-300 dark:border-gray-600 peer-checked:border-gray-400 peer-checked:bg-gray-800 rounded-lg p-4 hover:border-gray-500">
                                            <div class="flex items-center justify-center">
                                                <i class="fa-solid fa-building text-2xl text-gray-600 dark:text-gray-400"></i>
                                            </div>
                                            <div class="text-center mt-2 text-sm font-medium text-gray-900 dark:text-white">Kurumsal</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @if($billing_type === 'individual')
                                <div>
                                    <label class="block text-sm text-gray-900 dark:text-gray-300 mb-1.5">TC Kimlik No <span class="text-xs text-gray-500">(Opsiyonel)</span></label>
                                    <input type="text" wire:model="billing_tax_number" maxlength="11"
                                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                            @endif
                            @if($billing_type === 'corporate')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm text-gray-900 dark:text-gray-300 mb-1.5">Şirket Ünvanı <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="billing_company_name"
                                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <label class="block text-sm text-gray-900 dark:text-gray-300 mb-1.5">Vergi No <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="billing_tax_number" maxlength="10"
                                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-900 dark:text-gray-300 mb-1.5">Vergi Dairesi <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="billing_tax_office"
                                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-900">
                            <button wire:click="closeBillingModal" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white font-medium py-3 rounded-lg">
                                <i class="fa-solid fa-check mr-2"></i>Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Fatura Adresi Modal --}}
    @if($showBillingAddressModal ?? false)
        <div class="fixed inset-0 z-[999999] overflow-hidden" @keydown.escape.window="$wire.closeBillingAddressModal()">
            <div class="fixed inset-0 bg-black/60" wire:click="closeBillingAddressModal"></div>
            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="w-screen max-w-lg">
                    <div class="flex h-full flex-col bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-900">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                <i class="fa-solid fa-file-invoice-dollar mr-2 text-gray-600 dark:text-gray-400"></i>Fatura Adresi
                            </h3>
                            <button wire:click="closeBillingAddressModal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto px-6 py-4">
                            <livewire:cart::front.address-manager :userId="$customerId" addressType="billing" :selectedAddressId="$billing_address_id" :key="'billing-addr-'.$customerId" />
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-900">
                            <button wire:click="closeBillingAddressModal" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white font-medium py-3 rounded-lg">
                                <i class="fa-solid fa-check mr-2"></i>Tamam
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PayTR iframe Modal --}}
    @if($showPaymentModal ?? false)
        @teleport('body')
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto">
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden z-[10000] my-8">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-credit-card text-gray-600 dark:text-gray-400 text-xl"></i>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Güvenli Ödeme</h3>
                    </div>
                    <button wire:click="closePaymentModal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-white">
                        <i class="fa-solid fa-times text-2xl"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 80px);">
                    @if($paymentIframeUrl)
                        <iframe src="{{ $paymentIframeUrl }}" id="paytriframe" frameborder="0" scrolling="no"
                                style="width: 100%; min-height: 600px;" class="rounded-lg"></iframe>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-spinner fa-spin text-4xl text-gray-600 dark:text-gray-400 mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400">Ödeme ekranı yükleniyor...</p>
                        </div>
                    @endif
                </div>
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-700/50">
                    <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock text-green-400"></i>
                        256-bit SSL şifreli güvenli ödeme - PayTR Güvencesiyle
                    </p>
                </div>
            </div>
        </div>
        @push('scripts')
        <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const iframe = document.getElementById('paytriframe');
                if (iframe) iFrameResize({ log: false, checkOrigin: false, heightCalculationMethod: 'bodyScroll' }, '#paytriframe');
            });
            Livewire.hook('message.processed', (message, component) => {
                const iframe = document.getElementById('paytriframe');
                if (iframe) iFrameResize({ log: false, checkOrigin: false, heightCalculationMethod: 'bodyScroll' }, '#paytriframe');
            });
        </script>
        @endpush
        @endteleport
    @endif

</div>

@script
<script>
    const storedCartId = localStorage.getItem('cart_id');
    if (storedCartId) {
        $wire.loadCartById(parseInt(storedCartId)).then(() => {
            const emptyMsg = document.getElementById('empty-cart-message');
            if (emptyMsg && $wire.items && $wire.items.length > 0) emptyMsg.style.display = 'none';
        });
    }
</script>
@endscript
