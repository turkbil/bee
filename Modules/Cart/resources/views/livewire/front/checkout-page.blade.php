<div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900" x-data="{
    // Form Data
    contactFirstName: '{{ $contact_first_name }}',
    contactLastName: '{{ $contact_last_name }}',
    contactEmail: '{{ $contact_email }}',
    contactPhone: '{{ $contact_phone }}',

    // Fatura Profili
    billingProfileId: {{ $billing_profile_id ?? 'null' }},
    showNewBillingProfile: false,
    newBillingProfileType: 'individual',

    // Adres
    shippingAddressId: {{ $shipping_address_id ?? 'null' }},
    billingAddressId: {{ $billing_address_id ?? 'null' }},
    billingSameAsShipping: {{ $billing_same_as_shipping ? 'true' : 'false' }},
    requiresShipping: {{ $requiresShipping ? 'true' : 'false' }},

    // UI State
    showShippingForm: false,
    showNewShippingForm: false,
    showBillingAddressForm: {{ !$requiresShipping ? 'true' : 'false' }},
    showNewBillingForm: false,
    paymentMethod: 'card',
    agreeAll: {{ $agree_all ? 'true' : 'false' }},

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
        $wire.set('agree_all', this.agreeAll);
    },

    async submitOrder() {
        this.syncToLivewire();
        await $wire.proceedToPayment();
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
>
    <style>
        [x-cloak] { display: none !important; }
        .card-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
                    <h2 class="text-2xl font-bold text-white mb-2">Sepetiniz Boş</h2>
                    <p class="text-gray-400 mb-6">Checkout yapabilmek için sepetinize ürün eklemelisiniz.</p>
                    <a href="/cart" class="inline-flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg">
                        <i class="fa-solid fa-shopping-cart mr-2"></i>Sepete Git
                    </a>
                </div>
            </div>
        @else

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
                    <div x-show="!editContact" x-cloak
                         class="flex items-center justify-between py-3 px-4 mb-4 card-glass rounded-xl">
                        <p class="text-sm text-gray-400">
                            <span class="text-white font-medium" x-text="contactFirstName + ' ' + contactLastName"></span>
                            <span class="mx-2">•</span>
                            <span x-text="contactEmail"></span>
                            <span class="mx-2">•</span>
                            <span x-text="contactPhone"></span>
                        </p>
                        <button @click="editContact = true" class="text-gray-400 hover:text-white p-1">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                    </div>
                    @endif

                    {{-- Form --}}
                    <div x-show="editContact" {!! $hasContact ? 'x-cloak' : '' !!}
                         class="card-glass rounded-2xl p-5 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-300">İletişim Bilgileri</span>
                            <button x-show="contactFirstName && contactLastName && contactEmail && contactPhone"
                                    @click="editContact = false" class="text-gray-400 hover:text-white">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Ad <span class="text-red-500">*</span></label>
                                <input type="text" x-model="contactFirstName"
                                       class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm">
                                @error('contact_first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Soyad <span class="text-red-500">*</span></label>
                                <input type="text" x-model="contactLastName"
                                       class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm">
                                @error('contact_last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">E-posta <span class="text-red-500">*</span></label>
                                @auth
                                    <input type="email" x-model="contactEmail" readonly
                                           class="w-full px-3 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-400 text-sm cursor-not-allowed">
                                @else
                                    <input type="email" x-model="contactEmail" placeholder="ornek@email.com"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm">
                                @endauth
                                @error('contact_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Telefon <span class="text-red-500">*</span></label>
                                <input type="tel" x-model="contactPhone" placeholder="05XX XXX XX XX"
                                       class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm">
                                @error('contact_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. FATURA BİLGİLERİ --}}
                <div class="card-glass rounded-2xl p-6 mb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fa-solid fa-file-invoice text-gray-500 mr-3"></i>
                            Fatura Bilgileri
                        </h2>
                        <button @click="showNewBillingProfile = !showNewBillingProfile"
                                class="text-sm text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-700">
                            <i class="fa-solid fa-plus mr-1"></i>Ekle
                        </button>
                    </div>

                    {{-- Mevcut Profiller --}}
                    @if($billingProfiles && count($billingProfiles) > 0)
                        <div class="space-y-2 mb-4">
                            @foreach($billingProfiles as $profile)
                                <label class="block cursor-pointer" @click="selectBillingProfile({{ $profile->billing_profile_id }})">
                                    <div class="p-3 rounded-xl border-2 transition-all"
                                         :class="billingProfileId == {{ $profile->billing_profile_id }} ? 'border-gray-400 bg-gray-800' : 'border-gray-700 hover:border-gray-500'">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-700">
                                                    <i class="fa-solid {{ $profile->isCorporate() ? 'fa-building' : 'fa-user' }} text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-medium text-white">{{ $profile->title }}</span>
                                                        <span class="text-[10px] bg-gray-700 text-gray-400 px-1.5 py-0.5 rounded">
                                                            {{ $profile->isCorporate() ? 'Kurumsal' : 'Bireysel' }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        @if($profile->isCorporate())
                                                            {{ $profile->company_name }}
                                                        @else
                                                            {{ $profile->identity_number ? 'TC: ' . $profile->identity_number : '-' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                 :class="billingProfileId == {{ $profile->billing_profile_id }} ? 'border-white bg-white' : 'border-gray-600'">
                                                <i class="fa-solid fa-check text-[10px] text-gray-900"
                                                   :class="billingProfileId == {{ $profile->billing_profile_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div x-show="!showNewBillingProfile" @click="showNewBillingProfile = true"
                             class="bg-gray-800/50 rounded-xl p-4 border border-gray-700 mb-4 cursor-pointer hover:bg-gray-800">
                            <p class="text-sm text-gray-400">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Henüz fatura profili eklenmedi.
                                <span class="underline ml-1">Profil Ekle</span>
                            </p>
                        </div>
                    @endif

                    {{-- Yeni Profil Formu --}}
                    <div x-show="showNewBillingProfile" x-cloak x-transition class="space-y-4 pt-3 border-t border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Yeni Profil</span>
                            <button @click="showNewBillingProfile = false" class="text-gray-400 hover:text-white">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="newBillingProfileType = 'individual'; $wire.set('new_billing_profile_type', 'individual'); $wire.set('new_billing_profile_company_name', ''); $wire.set('new_billing_profile_tax_number', ''); $wire.set('new_billing_profile_tax_office', '')"
                                    :class="newBillingProfileType === 'individual' ? 'bg-gray-600 text-white' : 'bg-gray-700 text-gray-400'"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg">
                                <i class="fa-solid fa-user mr-1.5"></i>Bireysel
                            </button>
                            <button type="button" @click="newBillingProfileType = 'corporate'; $wire.set('new_billing_profile_type', 'corporate'); $wire.set('new_billing_profile_identity_number', '')"
                                    :class="newBillingProfileType === 'corporate' ? 'bg-gray-600 text-white' : 'bg-gray-700 text-gray-400'"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg">
                                <i class="fa-solid fa-building mr-1.5"></i>Kurumsal
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Profil Adı <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="new_billing_profile_title" placeholder="Örn: Kişisel"
                                   class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_profile_title') border-red-500 @enderror">
                            @error('new_billing_profile_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div x-show="newBillingProfileType === 'individual'">
                            <label class="block text-xs text-gray-400 mb-1">TC Kimlik No <span class="text-gray-500">(Opsiyonel)</span></label>
                            <input type="text" wire:model="new_billing_profile_identity_number" placeholder="XXXXXXXXXXX" maxlength="11"
                                   class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_profile_identity_number') border-red-500 @enderror">
                            @error('new_billing_profile_identity_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div x-show="newBillingProfileType === 'corporate'" class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Şirket Ünvanı <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="new_billing_profile_company_name" placeholder="ABC Ltd. Şti."
                                       class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_profile_company_name') border-red-500 @enderror">
                                @error('new_billing_profile_company_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Vergi No <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_billing_profile_tax_number" maxlength="10"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_profile_tax_number') border-red-500 @enderror">
                                    @error('new_billing_profile_tax_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Vergi Dairesi <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_billing_profile_tax_office"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_profile_tax_office') border-red-500 @enderror">
                                    @error('new_billing_profile_tax_office') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="saveNewBillingProfile" wire:loading.attr="disabled" wire:target="saveNewBillingProfile"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-500 disabled:bg-gray-700 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                <span wire:loading.remove wire:target="saveNewBillingProfile"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                <span wire:loading wire:target="saveNewBillingProfile"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                            </button>
                        </div>
                    </div>
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
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fa-solid fa-location-dot text-gray-500 mr-3"></i>
                            Teslimat Adresi
                        </h2>
                        <button @click="showShippingForm = !showShippingForm; showNewShippingForm = {{ $hasAddresses ? 'false' : 'true' }}"
                                class="text-sm text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-700">
                            <i class="fa-solid fa-{{ $shippingAddr ? 'pen' : 'plus' }} mr-1"></i>{{ $shippingAddr ? 'Değiştir' : 'Ekle' }}
                        </button>
                    </div>

                    {{-- Seçili Adres Özeti --}}
                    <div x-show="!showShippingForm">
                        @if($shippingAddr)
                            <p class="text-sm text-gray-400">
                                <span class="text-white font-medium">{{ $shippingAddr->title }}</span>
                                <span class="mx-2">•</span>
                                {{ $shippingAddr->address_line_1 }}, {{ $shippingAddr->district }}/{{ $shippingAddr->city }}
                            </p>
                        @else
                            <div @click="showShippingForm = true; showNewShippingForm = true"
                                 class="bg-gray-800/50 rounded-xl p-4 border border-gray-700 cursor-pointer hover:bg-gray-800">
                                <p class="text-sm text-gray-400">
                                    <i class="fa-solid fa-info-circle mr-2"></i>
                                    Teslimat adresi seçilmedi.
                                    <span class="underline ml-1">Adres Ekle</span>
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Adres Seçim/Ekleme --}}
                    <div x-show="showShippingForm" x-cloak class="space-y-3">
                        @if($userAddresses->count() > 0)
                            <div class="space-y-2">
                                @foreach($userAddresses as $addr)
                                    <label class="block cursor-pointer" @click="shippingAddressId = {{ $addr->address_id }}">
                                        <div class="p-3 rounded-xl border-2 transition-all"
                                             :class="shippingAddressId == {{ $addr->address_id }} ? 'border-gray-400 bg-gray-800' : 'border-gray-700 hover:border-gray-500'">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="text-sm font-medium text-white">{{ $addr->title }}</span>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $addr->address_line_1 }}, {{ $addr->district }} / {{ $addr->city }}</p>
                                                </div>
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                     :class="shippingAddressId == {{ $addr->address_id }} ? 'border-white bg-white' : 'border-gray-600'">
                                                    <i class="fa-solid fa-check text-[10px] text-gray-900"
                                                       :class="shippingAddressId == {{ $addr->address_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <button x-show="!showNewShippingForm" @click="showNewShippingForm = true"
                                class="text-sm text-gray-400 hover:text-white">
                            <i class="fa-solid fa-plus mr-1"></i>Adres Ekle
                        </button>

                        {{-- Yeni Adres Formu --}}
                        <div x-show="showNewShippingForm" x-cloak class="space-y-3 pt-3 border-t border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-300">Yeni Adres</span>
                                <button @click="showNewShippingForm = false; showShippingForm = false" class="text-gray-400 hover:text-white">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_address_title" placeholder="Örn: Evim"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_address_title') border-red-500 @enderror">
                                    @error('new_address_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Telefon</label>
                                    <input type="tel" wire:model="new_address_phone" placeholder="05XX XXX XX XX"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                    <select wire:model.live="new_address_city"
                                            class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_address_city') border-red-500 @enderror">
                                        <option value="">Seçin</option>
                                        @foreach($cities ?? [] as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_address_city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                    <select wire:model="new_address_district"
                                            class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_address_district') border-red-500 @enderror">
                                        <option value="">{{ empty($new_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                        @foreach($districts ?? [] as $district)
                                            <option value="{{ $district }}">{{ $district }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_address_district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Posta Kodu</label>
                                    <input type="text" wire:model="new_address_postal" placeholder="34000"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                                <textarea wire:model="new_address_line" rows="2" placeholder="Mahalle, sokak, bina no, daire"
                                          class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm resize-none @error('new_address_line') border-red-500 @enderror"></textarea>
                                @error('new_address_line') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex justify-end">
                                <button wire:click="saveNewAddress('shipping')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                        class="px-4 py-2 bg-gray-600 hover:bg-gray-500 disabled:bg-gray-700 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                    <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                    <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                </button>
                            </div>
                        </div>

                        @if($hasAddresses)
                        <div x-show="!showNewShippingForm" class="flex justify-end pt-3">
                            <button @click="showShippingForm = false"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white text-sm font-medium rounded-lg">
                                <i class="fa-solid fa-check mr-1"></i>Tamam
                            </button>
                        </div>
                        @endif
                    </div>
                    @error('shipping_address_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    {{-- Fatura Adresi Toggle --}}
                    <div class="border-t border-gray-700 mt-5 pt-5">
                        <label @click="billingSameAsShipping = !billingSameAsShipping" class="inline-flex items-center gap-2 cursor-pointer">
                            <div class="relative w-9 h-5">
                                <div class="absolute inset-0 rounded-full transition-colors"
                                     :class="billingSameAsShipping ? 'bg-gray-500' : 'bg-gray-600'"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-gray-300 shadow-sm transition-transform"
                                     :class="billingSameAsShipping ? 'translate-x-4' : ''"></div>
                            </div>
                            <span class="text-sm text-gray-400">Fatura adresi teslimat ile aynı</span>
                        </label>

                        {{-- Farklı Fatura Adresi --}}
                        <div x-show="!billingSameAsShipping" x-cloak x-transition class="mt-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-200">
                                    <i class="fa-solid fa-file-invoice-dollar text-gray-500 mr-2"></i>Fatura Adresi
                                </h3>
                                <button @click="showNewBillingForm = !showNewBillingForm"
                                        class="text-sm text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-700">
                                    <i class="fa-solid fa-plus mr-1"></i>Ekle
                                </button>
                            </div>

                            @if($userAddresses->count() > 0)
                                <div x-show="!showNewBillingForm" class="space-y-2">
                                    @foreach($userAddresses as $addr)
                                        <label class="block cursor-pointer" @click="billingAddressId = {{ $addr->address_id }}">
                                            <div class="p-3 rounded-xl border-2 transition-all"
                                                 :class="billingAddressId == {{ $addr->address_id }} ? 'border-gray-400 bg-gray-800' : 'border-gray-700 hover:border-gray-500'">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="text-sm font-medium text-white">{{ $addr->title }}</span>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $addr->address_line_1 }}, {{ $addr->district }} / {{ $addr->city }}</p>
                                                    </div>
                                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                         :class="billingAddressId == {{ $addr->address_id }} ? 'border-white bg-white' : 'border-gray-600'">
                                                        <i class="fa-solid fa-check text-[10px] text-gray-900"
                                                           :class="billingAddressId == {{ $addr->address_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div x-show="!showNewBillingForm" @click="showNewBillingForm = true"
                                     class="bg-gray-800/50 rounded-xl p-4 border border-gray-700 cursor-pointer hover:bg-gray-800">
                                    <p class="text-sm text-gray-400">
                                        <i class="fa-solid fa-info-circle mr-2"></i>
                                        Fatura adresi seçilmedi.
                                        <span class="underline ml-1">Adres Ekle</span>
                                    </p>
                                </div>
                            @endif

                            {{-- Yeni Fatura Adresi Formu --}}
                            <div x-show="showNewBillingForm" x-cloak x-transition class="space-y-3 pt-3 border-t border-gray-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-300">Yeni Adres</span>
                                    <button @click="showNewBillingForm = false" class="text-gray-400 hover:text-white" title="Kapat">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="new_billing_address_title" placeholder="Örn: Şirket"
                                               class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_address_title') border-red-500 @enderror">
                                        @error('new_billing_address_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Telefon</label>
                                        <input type="tel" wire:model="new_billing_address_phone" placeholder="05XX XXX XX XX"
                                               class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm">
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                        <select wire:model.live="new_billing_address_city"
                                                class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_address_city') border-red-500 @enderror">
                                            <option value="">Seçin</option>
                                            @foreach($cities ?? [] as $city)
                                                <option value="{{ $city }}">{{ $city }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_billing_address_city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                        <select wire:model="new_billing_address_district"
                                                class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_address_district') border-red-500 @enderror">
                                            <option value="">{{ empty($new_billing_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                            @foreach($billingDistricts ?? [] as $district)
                                                <option value="{{ $district }}">{{ $district }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_billing_address_district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Posta Kodu</label>
                                        <input type="text" wire:model="new_billing_address_postal"
                                               class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                                    <textarea wire:model="new_billing_address_line" rows="2" placeholder="Mahalle, sokak, bina no, daire"
                                              class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm resize-none @error('new_billing_address_line') border-red-500 @enderror"></textarea>
                                    @error('new_billing_address_line') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex justify-end">
                                    <button wire:click="saveNewAddress('billing')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                            class="px-4 py-2 bg-gray-600 hover:bg-gray-500 disabled:bg-gray-700 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                        <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                        <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('billing_address_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                @else
                {{-- DİJİTAL ÜRÜNLER İÇİN - SADECE FATURA ADRESİ --}}
                <div class="card-glass rounded-2xl p-6 mb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fa-solid fa-file-invoice text-gray-500 mr-3"></i>
                            Fatura Adresi
                        </h2>
                        <button @click="showBillingAddressForm = !showBillingAddressForm; showNewBillingForm = {{ $hasAddresses ? 'false' : 'true' }}"
                                class="text-sm text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-700">
                            <i class="fa-solid fa-{{ $billingAddr ? 'pen' : 'plus' }} mr-1"></i>{{ $billingAddr ? 'Değiştir' : 'Ekle' }}
                        </button>
                    </div>

                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 mb-4">
                        <p class="text-sm text-blue-400">
                            <i class="fa-solid fa-info-circle mr-2"></i>
                            Dijital ürün satın alıyorsunuz. Teslimat adresi gerekmez, sadece fatura adresi yeterlidir.
                        </p>
                    </div>

                    {{-- Seçili Adres Özeti --}}
                    <div x-show="!showBillingAddressForm">
                        @if($billingAddr)
                            <p class="text-sm text-gray-400">
                                <span class="text-white font-medium">{{ $billingAddr->title }}</span>
                                <span class="mx-2">•</span>
                                {{ $billingAddr->address_line_1 }}, {{ $billingAddr->district }}/{{ $billingAddr->city }}
                            </p>
                        @else
                            <div @click="showBillingAddressForm = true; showNewBillingForm = true"
                                 class="bg-gray-800/50 rounded-xl p-4 border border-gray-700 cursor-pointer hover:bg-gray-800">
                                <p class="text-sm text-gray-400">
                                    <i class="fa-solid fa-info-circle mr-2"></i>
                                    Fatura adresi seçilmedi.
                                    <span class="underline ml-1">Adres Ekle</span>
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Adres Seçim/Ekleme --}}
                    <div x-show="showBillingAddressForm" x-cloak class="space-y-3">
                        @if($userAddresses->count() > 0)
                            <div class="space-y-2">
                                @foreach($userAddresses as $addr)
                                    <label class="block cursor-pointer" @click="billingAddressId = {{ $addr->address_id }}">
                                        <div class="p-3 rounded-xl border-2 transition-all"
                                             :class="billingAddressId == {{ $addr->address_id }} ? 'border-gray-400 bg-gray-800' : 'border-gray-700 hover:border-gray-500'">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="text-sm font-medium text-white">{{ $addr->title }}</span>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $addr->address_line_1 }}, {{ $addr->district }} / {{ $addr->city }}</p>
                                                </div>
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                     :class="billingAddressId == {{ $addr->address_id }} ? 'border-white bg-white' : 'border-gray-600'">
                                                    <i class="fa-solid fa-check text-[10px] text-gray-900"
                                                       :class="billingAddressId == {{ $addr->address_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <button x-show="!showNewBillingForm" @click="showNewBillingForm = true"
                                class="text-sm text-gray-400 hover:text-white">
                            <i class="fa-solid fa-plus mr-1"></i>Adres Ekle
                        </button>

                        {{-- Yeni Fatura Adresi Formu --}}
                        <div x-show="showNewBillingForm" x-cloak x-transition class="space-y-3 pt-3 border-t border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-300">Yeni Adres</span>
                                <button @click="showNewBillingForm = false" class="text-gray-400 hover:text-white" title="Kapat">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Adres Adı <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_billing_address_title" placeholder="Örn: Şirket"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_address_title') border-red-500 @enderror">
                                    @error('new_billing_address_title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Telefon</label>
                                    <input type="tel" wire:model="new_billing_address_phone" placeholder="05XX XXX XX XX"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">İl <span class="text-red-500">*</span></label>
                                    <select wire:model.live="new_billing_address_city"
                                            class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_address_city') border-red-500 @enderror">
                                        <option value="">Seçin</option>
                                        @foreach($cities ?? [] as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_billing_address_city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">İlçe <span class="text-red-500">*</span></label>
                                    <select wire:model="new_billing_address_district"
                                            class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm @error('new_billing_address_district') border-red-500 @enderror">
                                        <option value="">{{ empty($new_billing_address_city) ? 'Önce il seçin' : 'Seçin' }}</option>
                                        @foreach($billingDistricts ?? [] as $district)
                                            <option value="{{ $district }}">{{ $district }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_billing_address_district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Posta Kodu</label>
                                    <input type="text" wire:model="new_billing_address_postal"
                                           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Adres <span class="text-red-500">*</span></label>
                                <textarea wire:model="new_billing_address_line" rows="2" placeholder="Mahalle, sokak, bina no, daire"
                                          class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm resize-none @error('new_billing_address_line') border-red-500 @enderror"></textarea>
                                @error('new_billing_address_line') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex justify-end">
                                <button wire:click="saveNewAddress('billing')" wire:loading.attr="disabled" wire:target="saveNewAddress"
                                        class="px-4 py-2 bg-gray-600 hover:bg-gray-500 disabled:bg-gray-700 disabled:cursor-wait text-white text-sm font-medium rounded-lg">
                                    <span wire:loading.remove wire:target="saveNewAddress"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                    <span wire:loading wire:target="saveNewAddress"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                                </button>
                            </div>
                        </div>

                        @if($hasAddresses)
                        <div x-show="!showNewBillingForm" class="flex justify-end pt-3">
                            <button @click="showBillingAddressForm = false"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white text-sm font-medium rounded-lg">
                                <i class="fa-solid fa-check mr-1"></i>Tamam
                            </button>
                        </div>
                        @endif
                    </div>
                    @error('billing_address_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif

            </div>

            {{-- ===================== SAĞ KOLON ===================== --}}
            <div style="flex: 1; min-width: 280px;">
                <div class="card-glass rounded-2xl sticky top-6 overflow-hidden">

                    {{-- Sipariş Özeti Header --}}
                    <div class="p-5 border-b border-gray-700 bg-gray-900/50">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fa-solid fa-receipt text-gray-500 mr-3"></i>
                            Sipariş Özeti
                        </h2>
                    </div>

                    {{-- Ürünler --}}
                    <div class="p-5 border-b border-gray-700 max-h-52 overflow-y-auto">
                        @foreach($items as $item)
                            <div class="flex gap-3 mb-4 last:mb-0">
                                <div class="w-14 h-14 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($item->product && $item->product->hasMedia('featured_image'))
                                        <img src="{{ $item->product->getFirstMediaUrl('featured_image', 'thumb') }}" class="w-full h-full object-cover">
                                    @elseif($item->product && $item->product->hasMedia('gallery'))
                                        <img src="{{ $item->product->getFirstMediaUrl('gallery', 'thumb') }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-box text-gray-500"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-white font-medium truncate">
                                        {{ $item->product ? $item->product->getTranslated('title', app()->getLocale()) : 'Ürün' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">Adet: {{ $item->quantity }}</p>
                                </div>
                                <span class="text-sm text-white font-semibold whitespace-nowrap">
                                    {{ number_format(round($item->subtotal), 0, ',', '.') }} ₺
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Fiyatlar --}}
                    <div class="p-5 border-b border-gray-700 space-y-3">
                        <div class="flex justify-between text-gray-400">
                            <span>Ara Toplam ({{ $itemCount }} Ürün)</span>
                            <span class="font-medium text-white">{{ number_format(round($subtotal), 0, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>KDV (%20)</span>
                            <span class="font-medium text-white">{{ number_format(round($taxAmount), 0, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-700">
                            <span class="text-lg font-bold text-white">Toplam</span>
                            <span class="text-xl font-bold text-white">{{ number_format(round($grandTotal), 0, ',', '.') }} ₺</span>
                        </div>
                    </div>

                    {{-- Ödeme Yöntemi --}}
                    <div class="p-5 border-b border-gray-700">
                        <h3 class="text-sm font-semibold text-white mb-4">
                            <i class="fa-solid fa-wallet text-gray-500 mr-2"></i>Ödeme Yöntemi
                        </h3>
                        <div class="space-y-3">
                            <div @click="paymentMethod = 'card'" class="cursor-pointer">
                                <div class="p-4 rounded-xl border-2 transition-all"
                                     :class="paymentMethod === 'card' ? 'border-blue-500 bg-blue-500/10' : 'border-gray-700 hover:border-gray-500'">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors"
                                             :class="paymentMethod === 'card' ? 'bg-blue-500/20' : 'bg-gray-700'">
                                            <i class="fa-solid fa-credit-card" :class="paymentMethod === 'card' ? 'text-blue-400' : 'text-gray-400'"></i>
                                        </div>
                                        <div class="flex-1">
                                            <span class="text-sm font-semibold" :class="paymentMethod === 'card' ? 'text-blue-400' : 'text-white'">Kredi Kartı</span>
                                            <p class="text-xs text-gray-500">Visa, Mastercard, Troy</p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                             :class="paymentMethod === 'card' ? 'border-blue-500 bg-blue-500' : 'border-gray-600'">
                                            <i class="fa-solid fa-check text-[10px] text-white" x-show="paymentMethod === 'card'"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div @click="paymentMethod = 'bank'" class="cursor-pointer">
                                <div class="p-4 rounded-xl border-2 transition-all"
                                     :class="paymentMethod === 'bank' ? 'border-blue-500 bg-blue-500/10' : 'border-gray-700 hover:border-gray-500'">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors"
                                             :class="paymentMethod === 'bank' ? 'bg-blue-500/20' : 'bg-gray-700'">
                                            <i class="fa-solid fa-money-bill-transfer" :class="paymentMethod === 'bank' ? 'text-blue-400' : 'text-gray-400'"></i>
                                        </div>
                                        <div class="flex-1">
                                            <span class="text-sm font-semibold" :class="paymentMethod === 'bank' ? 'text-blue-400' : 'text-white'">Havale / EFT</span>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                             :class="paymentMethod === 'bank' ? 'border-blue-500 bg-blue-500' : 'border-gray-600'">
                                            <i class="fa-solid fa-check text-[10px] text-white" x-show="paymentMethod === 'bank'"></i>
                                        </div>
                                    </div>
                                    <div x-show="paymentMethod === 'bank'" x-cloak class="mt-4 pt-4 border-t border-gray-600 text-sm text-gray-400">
                                        <p><strong class="text-gray-200">Banka:</strong> Türkiye İş Bankası</p>
                                        <p><strong class="text-gray-200">Hesap:</strong> İXTİF A.Ş.</p>
                                        <p class="mt-2"><strong class="text-gray-200">IBAN:</strong></p>
                                        <code class="block bg-gray-700 px-3 py-2 rounded-lg mt-1 text-xs font-mono">TR51 0006 4000 0011 0372 5092 58</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sözleşmeler --}}
                    <div class="p-5 border-b border-gray-700">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" x-model="agreeAll"
                                   class="w-5 h-5 mt-0.5 rounded border-gray-600 text-gray-400 focus:ring-gray-500 bg-gray-700">
                            <span class="text-sm text-gray-400 leading-relaxed">
                                <a href="/on-bilgilendirme" target="_blank" class="text-white hover:underline">Ön Bilgilendirme Formu</a>'nu ve
                                <a href="/mesafeli-satis" target="_blank" class="text-white hover:underline">Mesafeli Satış Sözleşmesi</a>'ni kabul ediyorum.
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

                    {{-- Ödeme Butonu --}}
                    <div class="p-5">
                        <button @click="submitOrder()" :disabled="!agreeAll"
                                :class="agreeAll ? 'bg-green-600 hover:bg-green-700 shadow-lg shadow-green-600/30' : 'bg-gray-600 cursor-not-allowed'"
                                class="w-full text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-2 text-lg">
                            <i class="fa-solid fa-lock"></i>
                            <span x-text="paymentMethod === 'card' ? 'Kredi Kartı ile Öde' : 'Siparişi Tamamla'"></span>
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-4 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-shield-halved text-green-500"></i>
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
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 bg-gray-900">
                            <h3 class="text-lg font-bold text-white">
                                <i class="fa-solid fa-truck mr-2 text-gray-400"></i>Teslimat Adresi
                            </h3>
                            <button wire:click="closeShippingModal" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto px-6 py-4">
                            <livewire:cart::front.address-manager :userId="$customerId" addressType="shipping" :selectedAddressId="$shipping_address_id" :key="'shipping-'.$customerId" />
                        </div>
                        <div class="px-6 py-4 border-t border-gray-700 bg-gray-900">
                            <button wire:click="closeShippingModal" class="w-full bg-gray-600 hover:bg-gray-500 text-white font-medium py-3 rounded-lg">
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
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 bg-gray-900">
                            <h3 class="text-lg font-bold text-white">
                                <i class="fa-solid fa-file-invoice mr-2 text-gray-400"></i>Fatura Bilgileri
                            </h3>
                            <button wire:click="closeBillingModal" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto px-6 py-4">
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-300 mb-3">Fatura Türü</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model.live="billing_type" value="individual" class="hidden peer">
                                        <div class="border-2 border-gray-600 peer-checked:border-gray-400 peer-checked:bg-gray-800 rounded-lg p-4 hover:border-gray-500">
                                            <div class="flex items-center justify-center">
                                                <i class="fa-solid fa-user text-2xl text-gray-400"></i>
                                            </div>
                                            <div class="text-center mt-2 text-sm font-medium text-white">Bireysel</div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model.live="billing_type" value="corporate" class="hidden peer">
                                        <div class="border-2 border-gray-600 peer-checked:border-gray-400 peer-checked:bg-gray-800 rounded-lg p-4 hover:border-gray-500">
                                            <div class="flex items-center justify-center">
                                                <i class="fa-solid fa-building text-2xl text-gray-400"></i>
                                            </div>
                                            <div class="text-center mt-2 text-sm font-medium text-white">Kurumsal</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @if($billing_type === 'individual')
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1.5">TC Kimlik No <span class="text-xs text-gray-500">(Opsiyonel)</span></label>
                                    <input type="text" wire:model="billing_tax_number" maxlength="11"
                                           class="w-full px-4 py-2.5 rounded-lg border border-gray-600 bg-gray-700 text-white">
                                </div>
                            @endif
                            @if($billing_type === 'corporate')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm text-gray-300 mb-1.5">Şirket Ünvanı <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="billing_company_name"
                                               class="w-full px-4 py-2.5 rounded-lg border border-gray-600 bg-gray-700 text-white">
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1.5">Vergi No <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="billing_tax_number" maxlength="10"
                                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-600 bg-gray-700 text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-300 mb-1.5">Vergi Dairesi <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="billing_tax_office"
                                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-600 bg-gray-700 text-white">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="px-6 py-4 border-t border-gray-700 bg-gray-900">
                            <button wire:click="closeBillingModal" class="w-full bg-gray-600 hover:bg-gray-500 text-white font-medium py-3 rounded-lg">
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
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 bg-gray-900">
                            <h3 class="text-lg font-bold text-white">
                                <i class="fa-solid fa-file-invoice-dollar mr-2 text-gray-400"></i>Fatura Adresi
                            </h3>
                            <button wire:click="closeBillingAddressModal" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto px-6 py-4">
                            <livewire:cart::front.address-manager :userId="$customerId" addressType="billing" :selectedAddressId="$billing_address_id" :key="'billing-addr-'.$customerId" />
                        </div>
                        <div class="px-6 py-4 border-t border-gray-700 bg-gray-900">
                            <button wire:click="closeBillingAddressModal" class="w-full bg-gray-600 hover:bg-gray-500 text-white font-medium py-3 rounded-lg">
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
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-credit-card text-gray-400 text-xl"></i>
                        <h3 class="text-xl font-bold text-white">Güvenli Ödeme</h3>
                    </div>
                    <button wire:click="closePaymentModal" class="text-gray-400 hover:text-white">
                        <i class="fa-solid fa-times text-2xl"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 80px);">
                    @if($paymentIframeUrl)
                        <iframe src="{{ $paymentIframeUrl }}" id="paytriframe" frameborder="0" scrolling="no"
                                style="width: 100%; min-height: 600px;" class="rounded-lg"></iframe>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-400">Ödeme ekranı yükleniyor...</p>
                        </div>
                    @endif
                </div>
                <div class="px-6 py-3 border-t border-gray-700 bg-gray-700/50">
                    <p class="text-xs text-gray-400 flex items-center justify-center gap-2">
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
    console.log('CheckoutPage: Initializing...');
    const storedCartId = localStorage.getItem('cart_id');
    if (storedCartId) {
        console.log('Found cart_id:', storedCartId);
        $wire.loadCartById(parseInt(storedCartId)).then(() => {
            console.log('Cart loaded');
            const emptyMsg = document.getElementById('empty-cart-message');
            if (emptyMsg && $wire.items && $wire.items.length > 0) emptyMsg.style.display = 'none';
        });
    }
</script>
@endscript
