<style>
/* 🎨 Tooltip CSS - Compact Minimal Design */
[data-tooltip] {
    position: relative;
}
[data-tooltip]:hover::before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(-4px);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    pointer-events: none;
}
[data-tooltip]:hover::after {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 4px solid transparent;
    border-top-color: rgba(0, 0, 0, 0.9);
    z-index: 999;
    pointer-events: none;
}
</style>

<div class="min-h-screen py-8" x-data="{
    showBillingList: false,
    editingBillingProfileId: null,
    showNewBillingProfile: false,
    newBillingProfileType: 'individual',
    toggleBillingProfileList() {
        this.showBillingList = !this.showBillingList;
    },
    toggleEditBillingProfile(profileId) {
        this.editingBillingProfileId = this.editingBillingProfileId === profileId ? null : profileId;
    },
    selectBillingProfile(profileId) {
        @this.call('selectBillingProfile', profileId);
    }
}">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Sipariş Tamamla</h1>

        {{-- 2 KOLONLU LAYOUT: SOL=Form, SAĞ=Fiyat Özeti --}}
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- SOL TARAF: FORM BİLGİLERİ (2/3 Genişlik) --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- 1. İletişim Bilgileri (Her Zaman Açık) --}}
                <div class="bg-white/20 dark:bg-gray-800/20 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-user mr-2 text-blue-500 dark:text-blue-400"></i>
                        İletişim Bilgileri
                    </h2>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                Ad <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <input type="text" wire:model="contact_first_name"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all">
                            @error('contact_first_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                Soyad <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <input type="text" wire:model="contact_last_name"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all">
                            @error('contact_last_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                Telefon <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <input type="tel" wire:model="contact_phone"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                                placeholder="05XX XXX XX XX">
                            @error('contact_phone') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Teslimat Adresi (ÖNCE GELMELI) --}}
                <div class="bg-white/20 dark:bg-gray-800/20 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-truck mr-2 text-blue-500 dark:text-blue-400"></i>
                            Teslimat Adresi
                        </h2>
                        <button wire:click="openShippingModal"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                            <i class="fa-solid fa-edit mr-1"></i> Düzenle
                        </button>
                    </div>

                    {{-- Özet Gösterimi --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        @php
                            $shippingAddr = $shipping_address_id ? \Modules\Shop\App\Models\ShopCustomerAddress::find($shipping_address_id) : null;
                        @endphp

                        @if($shippingAddr)
                            <p class="font-medium text-gray-900 dark:text-white mb-1">
                                <i class="fa-solid fa-map-marker-alt text-xs mr-2 text-red-500 dark:text-red-400"></i>
                                {{ $shippingAddr->title ?? 'Teslimat Adresi' }}
                            </p>
                            <p class="text-xs ml-5">
                                {{ $shippingAddr->address_line_1 }}@if($shippingAddr->address_line_2), {{ $shippingAddr->address_line_2 }}@endif
                            </p>
                            <p class="text-xs ml-5">{{ $shippingAddr->district }} / {{ $shippingAddr->city }} {{ $shippingAddr->postal_code }}</p>
                            @if($shippingAddr->phone)
                                <p class="text-xs ml-5 mt-1">
                                    <i class="fa-solid fa-phone text-xs mr-1"></i> {{ $shippingAddr->phone }}
                                </p>
                            @endif
                        @else
                            <p class="text-xs text-orange-600 dark:text-orange-400">
                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                Teslimat adresi seçilmedi
                            </p>
                        @endif
                    </div>

                    {{-- Modal: Teslimat Adresi Düzenleme --}}
                    @if($showShippingModal ?? false)
                        @teleport('body')
                        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto" wire:click.self="closeShippingModal">
                            {{-- Backdrop --}}
                            <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm" wire:click="closeShippingModal"></div>

                            {{-- Modal Content --}}
                            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 z-[10000] my-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Teslimat Adresi</h3>
                                        <button wire:click="closeShippingModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                            <i class="fa-solid fa-times text-2xl"></i>
                                        </button>
                                    </div>

                                    {{-- Teslimat Adresi Seçimi --}}
                                    <div class="mb-6">
                                        <livewire:shop::front.address-manager
                                            :customerId="$customerId"
                                            addressType="shipping"
                                            :selectedAddressId="$shipping_address_id"
                                            :key="'shipping-'.$customerId" />
                                    </div>

                                    {{-- Modal Butonlar --}}
                                    <div class="flex justify-end gap-3">
                                        <button wire:click="closeShippingModal"
                                            class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                            İptal
                                        </button>
                                        <button wire:click="closeShippingModal"
                                            class="px-6 py-2.5 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                            Kaydet
                                        </button>
                                    </div>
                            </div>
                        </div>
                        @endteleport
                    @endif
                </div>

                {{-- 3. Fatura Bilgileri - COMPACT MINIMAL --}}
                <div class="bg-white/20 dark:bg-gray-800/20 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-file-invoice text-blue-500 dark:text-blue-400 mr-3"></i>
                            Fatura Bilgileri
                        </h2>
                        @auth
                        <button @click="showNewBillingProfile = !showNewBillingProfile"
                                data-tooltip="Yeni Profil Ekle"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 px-3 py-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                            <i class="fa-solid fa-plus mr-1"></i>Ekle
                        </button>
                        @endauth
                    </div>

                    @auth
                    @if($billingProfiles && count($billingProfiles) > 0)
                        @php
                            $selectedProfile = $billingProfiles->firstWhere('billing_profile_id', $selectedBillingProfileId) ?? $billingProfiles->first();
                        @endphp

                        {{-- COMPACT MINIMAL: Seçili Profil Özeti (Tek Satır) --}}
                        @if($selectedProfile)
                            <div class="flex items-center justify-between py-3 px-4 mb-3 bg-white/5 dark:bg-white/5 backdrop-blur-sm rounded-xl border border-white/10">
                                <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                                    <span class="text-gray-900 dark:text-white font-medium">
                                        {{ $selectedProfile->isCorporate() ? $selectedProfile->company_name : $selectedProfile->title }}
                                    </span>
                                    @if($selectedProfile->is_default)
                                        <span class="mx-2 text-yellow-400" title="Varsayılan Profil">★</span>
                                    @endif
                                    <span class="mx-2 text-gray-400 dark:text-gray-600">•</span>
                                    <span>
                                        @if($selectedProfile->isCorporate())
                                            Vergi No: {{ $selectedProfile->tax_number }}
                                        @else
                                            TC: {{ $selectedProfile->identity_number ?? '-' }}
                                        @endif
                                    </span>
                                </p>
                                <button @click="toggleBillingProfileList()"
                                        data-tooltip="Profilleri Düzenle"
                                        class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-1 transition-colors">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            </div>
                        @endif

                        {{-- Profil Listesi (Collapsible) --}}
                        <div x-show="showBillingList" x-cloak x-transition class="space-y-2">
                            @foreach($billingProfiles as $profile)
                                <div wire:key="billing-profile-{{ $profile->billing_profile_id }}" class="relative group">
                                    {{-- Profil Kartı --}}
                                    <div @click="selectBillingProfile({{ $profile->billing_profile_id }}); showBillingList = false"
                                         class="p-3 rounded-xl border-2 transition-all cursor-pointer"
                                         :class="@this.selectedBillingProfileId == {{ $profile->billing_profile_id }} ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600'"
                                        <div class="flex items-center justify-between">
                                            {{-- Sol: Profil Bilgisi --}}
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $profile->isCorporate() ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30' }} flex-shrink-0">
                                                    <i class="fa-solid {{ $profile->isCorporate() ? 'fa-building text-blue-600 dark:text-blue-400' : 'fa-user text-green-600 dark:text-green-400' }}"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                            {{ $profile->isCorporate() ? $profile->company_name : $profile->title }}
                                                        </span>
                                                        <span class="text-[10px] bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-1.5 py-0.5 rounded flex-shrink-0">
                                                            {{ $profile->isCorporate() ? 'Kurumsal' : 'Bireysel' }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-0.5 truncate">
                                                        @if($profile->isCorporate())
                                                            Vergi No: {{ $profile->tax_number }}
                                                        @else
                                                            {{ $profile->identity_number ? 'TC: ' . $profile->identity_number : '-' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Sağ: Minimal Butonlar --}}
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                {{-- Varsayılan Yap (Star) --}}
                                                @if(!$profile->is_default)
                                                <button @click.stop="@this.call('setDefaultBillingProfile', {{ $profile->billing_profile_id }})"
                                                        data-tooltip="Varsayılan Yap"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 hover:bg-yellow-500/20 rounded text-yellow-400 hover:text-yellow-300">
                                                    <i class="fas fa-star text-xs"></i>
                                                </button>
                                                @endif
                                                {{-- Edit (Toggle) --}}
                                                <button @click.stop="toggleEditBillingProfile({{ $profile->billing_profile_id }})"
                                                        data-tooltip="Düzenle"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 hover:bg-blue-100 dark:hover:bg-blue-900/20 rounded transition-colors"
                                                        :class="editingBillingProfileId === {{ $profile->billing_profile_id }} ? 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/20 opacity-100' : 'text-blue-500 dark:text-blue-400'">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                {{-- Delete --}}
                                                <button @click.stop="confirm('Bu profili silmek istediğinize emin misiniz?') && @this.call('deleteBillingProfile', {{ $profile->billing_profile_id }})"
                                                        data-tooltip="Sil"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 hover:bg-red-100 dark:hover:bg-red-900/20 rounded text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                                {{-- Checkbox --}}
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                     :class="@this.selectedBillingProfileId == {{ $profile->billing_profile_id }} ? 'border-blue-600 dark:border-blue-400 bg-blue-600 dark:bg-blue-400' : 'border-gray-400 dark:border-gray-600'">
                                                    <i class="fa-solid fa-check text-[10px] text-white"
                                                       :class="@this.selectedBillingProfileId == {{ $profile->billing_profile_id }} ? 'opacity-100' : 'opacity-0'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Inline Edit Form (Profil Altında) --}}
                                    <div x-show="editingBillingProfileId === {{ $profile->billing_profile_id }}"
                                         x-cloak x-transition @click.stop
                                         class="mt-2 bg-gray-100 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 rounded-xl p-5">

                                        {{-- Edit Header --}}
                                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-300 dark:border-gray-700">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                                <i class="fa-solid fa-edit text-blue-500 dark:text-blue-400"></i>
                                                Profil Düzenle
                                            </h4>
                                            <button @click="editingBillingProfileId = null"
                                                    data-tooltip="Kapat"
                                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white p-1 transition-colors">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>

                                        {{-- Edit Form Fields (Read-only) --}}
                                        <div class="space-y-4">
                                            {{-- Profil Tipi (Display Only) --}}
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-2">Profil Tipi</label>
                                                <div class="flex gap-2">
                                                    <div class="flex-1 py-2 text-xs font-medium rounded-lg text-center {{ $profile->type === 'individual' ? 'bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                                        <i class="fa-solid fa-user mr-1.5"></i>Bireysel
                                                    </div>
                                                    <div class="flex-1 py-2 text-xs font-medium rounded-lg text-center {{ $profile->type === 'corporate' ? 'bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                                        <i class="fa-solid fa-building mr-1.5"></i>Kurumsal
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Bireysel Fields --}}
                                            @if(!$profile->isCorporate())
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Profil Adı <span class="text-red-500 dark:text-red-400">*</span></label>
                                                    <input type="text" value="{{ $profile->title }}" readonly
                                                           class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-white text-sm opacity-60 cursor-not-allowed">
                                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Profil adını değiştirmek için silip yeniden oluşturun</p>
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">TC Kimlik No</label>
                                                    <input type="text" value="{{ $profile->identity_number }}" readonly maxlength="11"
                                                           class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-white text-sm opacity-60 cursor-not-allowed">
                                                </div>
                                            @endif

                                            {{-- Kurumsal Fields --}}
                                            @if($profile->isCorporate())
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Şirket Ünvanı <span class="text-red-500 dark:text-red-400">*</span></label>
                                                    <input type="text" value="{{ $profile->company_name }}" readonly
                                                           class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-white text-sm opacity-60 cursor-not-allowed">
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi No <span class="text-red-500 dark:text-red-400">*</span></label>
                                                        <input type="text" value="{{ $profile->tax_number }}" readonly maxlength="10"
                                                               class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-white text-sm opacity-60 cursor-not-allowed">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi Dairesi <span class="text-red-500 dark:text-red-400">*</span></label>
                                                        <input type="text" value="{{ $profile->tax_office }}" readonly
                                                               class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-white text-sm opacity-60 cursor-not-allowed">
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Info Note --}}
                                            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-500/30 rounded-lg p-3">
                                                <p class="text-xs text-blue-700 dark:text-blue-400">
                                                    <i class="fa-solid fa-info-circle mr-2"></i>
                                                    Profil bilgilerini değiştirmek için profili silin ve yenisini oluşturun.
                                                </p>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex gap-2 pt-3 border-t border-gray-300 dark:border-gray-700">
                                                <button @click="editingBillingProfileId = null"
                                                        class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fa-solid fa-times mr-1"></i>Kapat
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Fallback: Henüz profil yok --}}
                        <div x-show="!showNewBillingProfile" @click="showNewBillingProfile = true"
                             class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Henüz fatura profili eklenmedi.
                                <span class="underline ml-1 text-blue-600 dark:text-blue-400">Profil Ekle</span>
                            </p>
                        </div>
                    @endif

                    {{-- Yeni Profil Formu --}}
                    <div x-show="showNewBillingProfile" x-cloak x-transition class="space-y-4 pt-3 border-t border-gray-200 dark:border-gray-700 mt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300">Yeni Profil</span>
                            <button @click="showNewBillingProfile = false"
                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>

                        {{-- Profil Tipi Seçimi --}}
                        <div class="flex gap-2">
                            <button type="button" @click="newBillingProfileType = 'individual'; @this.set('billing_type', 'individual')"
                                    :class="newBillingProfileType === 'individual' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-400'"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors">
                                <i class="fa-solid fa-user mr-1.5"></i>Bireysel
                            </button>
                            <button type="button" @click="newBillingProfileType = 'corporate'; @this.set('billing_type', 'corporate')"
                                    :class="newBillingProfileType === 'corporate' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-400'"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors">
                                <i class="fa-solid fa-building mr-1.5"></i>Kurumsal
                            </button>
                        </div>

                        {{-- Bireysel Profil Form --}}
                        <div x-show="newBillingProfileType === 'individual'">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Kayıt Adı <span class="text-gray-500 dark:text-gray-500">(Daha sonra kullanmak için)</span> <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" wire:model="billing_profile_title" placeholder="Örn: Evim, İşyerim"
                                   class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('billing_profile_title') border-red-500 dark:border-red-400 @enderror">
                            @error('billing_profile_title') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div x-show="newBillingProfileType === 'individual'">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">TC Kimlik No <span class="text-gray-500 dark:text-gray-500">(Opsiyonel)</span></label>
                            <input type="text" wire:model="billing_tax_number" placeholder="XXXXXXXXXXX" maxlength="11"
                                   class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('billing_tax_number') border-red-500 dark:border-red-400 @enderror">
                            @error('billing_tax_number') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Kurumsal Profil Form --}}
                        <div x-show="newBillingProfileType === 'corporate'" class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Şirket Ünvanı <span class="text-red-500 dark:text-red-400">*</span></label>
                                <input type="text" wire:model="billing_company_name" placeholder="ABC Ltd. Şti."
                                       class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('billing_company_name') border-red-500 dark:border-red-400 @enderror">
                                @error('billing_company_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi No <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="text" wire:model="billing_tax_number" maxlength="10"
                                           class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('billing_tax_number') border-red-500 dark:border-red-400 @enderror">
                                    @error('billing_tax_number') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi Dairesi <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="text" wire:model="billing_tax_office"
                                           class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm @error('billing_tax_office') border-red-500 dark:border-red-400 @enderror">
                                    @error('billing_tax_office') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Kaydet Butonu --}}
                        <div class="flex justify-end">
                            <button wire:click="saveBillingProfile" wire:loading.attr="disabled" wire:target="saveBillingProfile"
                                    class="px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 disabled:bg-gray-400 dark:disabled:bg-gray-700 disabled:cursor-wait text-white text-sm font-medium rounded-lg transition-colors">
                                <span wire:loading.remove wire:target="saveBillingProfile"><i class="fa-solid fa-check mr-1"></i>Kaydet</span>
                                <span wire:loading wire:target="saveBillingProfile"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Kaydediliyor...</span>
                            </button>
                        </div>
                    </div>
                    @else
                    {{-- Guest: Basit Form (Profil sistemi yok) --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-3">
                        <div class="flex gap-2 mb-4">
                            <button type="button" wire:click="$set('billing_type', 'individual')"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $billing_type === 'individual' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-400' }}">
                                <i class="fa-solid fa-user mr-1.5"></i>Bireysel
                            </button>
                            <button type="button" wire:click="$set('billing_type', 'corporate')"
                                    class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $billing_type === 'corporate' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-400' }}">
                                <i class="fa-solid fa-building mr-1.5"></i>Kurumsal
                            </button>
                        </div>

                        @if($billing_type === 'individual')
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">TC Kimlik No <span class="text-gray-500 dark:text-gray-500">(Opsiyonel)</span></label>
                                <input type="text" wire:model="billing_tax_number" maxlength="11" placeholder="XXXXXXXXXXX"
                                       class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                @error('billing_tax_number') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Şirket Ünvanı <span class="text-red-500 dark:text-red-400">*</span></label>
                                <input type="text" wire:model="billing_company_name"
                                       class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                @error('billing_company_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi No <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="text" wire:model="billing_tax_number" maxlength="10"
                                           class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                    @error('billing_tax_number') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi Dairesi <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="text" wire:model="billing_tax_office"
                                           class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                    @error('billing_tax_office') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                    @endauth
                </div>

                {{-- 4. Fatura Adresi --}}
                <div class="bg-white/20 dark:bg-gray-800/20 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-file-invoice-dollar mr-2 text-blue-500 dark:text-blue-400"></i>
                            Fatura Adresi
                        </h2>
                        @if(!$billing_same_as_shipping)
                            <button wire:click="openBillingAddressModal"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                                <i class="fa-solid fa-edit mr-1"></i> Düzenle
                            </button>
                        @endif
                    </div>

                    {{-- Checkbox: Teslimat ile aynı --}}
                    <div class="mb-3">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model.live="billing_same_as_shipping"
                                class="w-4 h-4 text-blue-600 dark:text-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 rounded transition-all">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Teslimat adresi ile aynı
                            </span>
                        </label>
                    </div>

                    {{-- Özet Gösterimi --}}
                    @if($billing_same_as_shipping)
                        {{-- Teslimat adresi ile aynı --}}
                        <div class="text-xs text-green-600 dark:text-green-400 ml-6">
                            <i class="fa-solid fa-check-circle mr-1"></i>
                            Fatura adresi, teslimat adresi ile aynı
                        </div>
                    @else
                        {{-- Farklı fatura adresi --}}
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            @php
                                $billingAddr = $billing_address_id ? \Modules\Shop\App\Models\ShopCustomerAddress::find($billing_address_id) : null;
                            @endphp

                            @if($billingAddr)
                                <p class="font-medium text-gray-900 dark:text-white mb-1">
                                    <i class="fa-solid fa-map-marker-alt text-xs mr-2 text-blue-500 dark:text-blue-400"></i>
                                    {{ $billingAddr->title ?? 'Fatura Adresi' }}
                                </p>
                                <p class="text-xs ml-5">
                                    {{ $billingAddr->address_line_1 }}@if($billingAddr->address_line_2), {{ $billingAddr->address_line_2 }}@endif
                                </p>
                                <p class="text-xs ml-5">{{ $billingAddr->district }} / {{ $billingAddr->city }} {{ $billingAddr->postal_code }}</p>
                            @else
                                <p class="text-xs text-orange-600 dark:text-orange-400">
                                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                    Fatura adresi seçilmedi
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- Modal: Fatura Adresi Düzenleme --}}
                    @if($showBillingAddressModal ?? false)
                        @teleport('body')
                        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto" wire:click.self="closeBillingAddressModal">
                            {{-- Backdrop --}}
                            <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm" wire:click="closeBillingAddressModal"></div>

                            {{-- Modal Content --}}
                            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 z-[10000] my-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Fatura Adresi</h3>
                                        <button wire:click="closeBillingAddressModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                            <i class="fa-solid fa-times text-2xl"></i>
                                        </button>
                                    </div>

                                    {{-- Fatura Adresi Seçimi --}}
                                    <div class="mb-6">
                                        <livewire:shop::front.address-manager
                                            :customerId="$customerId"
                                            addressType="billing"
                                            :selectedAddressId="$billing_address_id"
                                            :key="'billing-addr-'.$customerId" />
                                    </div>

                                    {{-- Modal Butonlar --}}
                                    <div class="flex justify-end gap-3">
                                        <button wire:click="closeBillingAddressModal"
                                            class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                            İptal
                                        </button>
                                        <button wire:click="closeBillingAddressModal"
                                            class="px-6 py-2.5 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                            Kaydet
                                        </button>
                                    </div>
                            </div>
                        </div>
                        @endteleport
                    @endif
                </div>

            </div>

            {{-- SAĞ TARAF: FİYAT ÖZETİ (1/3 Genişlik) --}}
            <div class="lg:col-span-1">
                <div class="bg-white/20 dark:bg-gray-800/20 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fa-solid fa-receipt mr-2 text-blue-500 dark:text-blue-400"></i>
                        Sipariş Özeti
                    </h2>

                    {{-- Fiyat Detayları --}}
                    <div class="space-y-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        {{-- Ürün Sayısı --}}
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-box text-xs mr-1"></i>
                                Ürün Sayısı
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $itemCount }} Adet</span>
                        </div>

                        {{-- Ara Toplam --}}
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Ara Toplam</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ number_format(round($subtotal), 0, ',', '.') }}
                                <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                            </span>
                        </div>

                        {{-- KDV --}}
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">KDV (%20)</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ number_format(round($taxAmount), 0, ',', '.') }}
                                <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                            </span>
                        </div>

                        {{-- Ara Toplam (KDV Dahil) --}}
                        <div class="flex justify-between items-center text-sm pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-gray-700 dark:text-gray-300 font-medium">Ara Toplam (KDV Dahil)</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ number_format(round($total), 0, ',', '.') }}
                                <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                            </span>
                        </div>

                    </div>

                    {{-- GENEL TOPLAM --}}
                    <div class="flex justify-between items-center mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">TOPLAM</span>
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format(round($grandTotal), 0, ',', '.') }}
                            <i class="fa-solid fa-turkish-lira text-lg ml-1"></i>
                        </span>
                    </div>

                    {{-- Ödeme Yöntemi Seçimi --}}
                    <div class="mb-6" x-data="{ paymentMethod: 'card' }">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                            <i class="fa-solid fa-wallet mr-2 text-blue-500"></i>
                            Ödeme Yöntemi
                        </h3>

                        <div class="space-y-3">
                            {{-- Kredi Kartı --}}
                            <label class="flex items-start cursor-pointer group p-4 border-2 rounded-lg transition-all"
                                :class="paymentMethod === 'card' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-gray-400'">
                                <input type="radio" x-model="paymentMethod" value="card" class="mt-1 w-4 h-4 text-blue-600">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            <i class="fa-solid fa-credit-card mr-2 text-blue-600"></i>
                                            Kredi Kartı
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">PayTR Güvencesiyle</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Visa, Mastercard, Troy kartlarınızla güvenli ödeme</p>
                                </div>
                            </label>

                            {{-- Havale/EFT --}}
                            <label class="flex items-start cursor-pointer group p-4 border-2 rounded-lg transition-all"
                                :class="paymentMethod === 'bank_transfer' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-gray-400'">
                                <input type="radio" x-model="paymentMethod" value="bank_transfer" class="mt-1 w-4 h-4 text-green-600">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            <i class="fa-solid fa-money-bill-transfer mr-2 text-green-600"></i>
                                            Havale / EFT
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Banka hesabımıza havale yaparak ödeme yapabilirsiniz</p>

                                    {{-- Banka Bilgileri (Havale seçildiğinde göster) --}}
                                    <div x-show="paymentMethod === 'bank_transfer'" x-collapse class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Banka Bilgileri:</h4>
                                        <div class="space-y-1.5 text-xs text-gray-600 dark:text-gray-400">
                                            <p><strong>Banka:</strong> Türkiye İş Bankası</p>
                                            <p><strong>Hesap Adı:</strong> İXTİF İÇ VE DIŞ TİCARET ANONİM ŞİRKETİ</p>
                                            <p><strong>IBAN:</strong> <span class="font-mono bg-white dark:bg-gray-800 px-2 py-1 rounded">TR51 0006 4000 0011 0372 5092 58</span></p>
                                            <p class="text-orange-600 dark:text-orange-400 mt-2">
                                                <i class="fa-solid fa-info-circle mr-1"></i>
                                                Havale açıklamasına sipariş numaranızı yazınız
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Tek Checkbox (Combined Agreement) --}}
                    <div class="mb-4">
                        <label class="flex items-start cursor-pointer group">
                            <input type="checkbox" wire:model="agree_all"
                                class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 rounded transition-all">
                            <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                Ön Bilgilendirme <a href="/cayma-hakki" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Formu</a>'nu ve
                                Mesafeli Satış <a href="/mesafeli-satis" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Sözleşmesi</a>'ni onaylıyorum.
                                <span class="text-red-500 dark:text-red-400 font-bold">*</span>
                            </span>
                        </label>
                        @error('agree_all')
                            <span class="text-red-500 dark:text-red-400 text-xs block ml-6 mt-1">
                                <i class="fa-solid fa-exclamation-circle mr-1"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- ⚠️ VALIDATION HATALARI (Buton üstünde) --}}
                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-500 dark:border-red-600 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i class="fa-solid fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl mr-3 mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="text-red-800 dark:text-red-300 font-bold text-sm mb-2">Lütfen eksiklikleri tamamlayın:</h4>
                                    <ul class="space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li class="text-red-700 dark:text-red-400 text-xs flex items-start">
                                                <i class="fa-solid fa-circle text-[4px] mr-2 mt-1.5"></i>
                                                <span>{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Ödemeye Geç Butonu --}}
                    <div x-data="{ paymentMethod: 'card' }">
                        <button type="button"
                            wire:click="testPayment"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                            <template x-if="paymentMethod === 'card'">
                                <span><i class="fa-solid fa-credit-card mr-2"></i> Kredi Kartı ile Öde</span>
                            </template>
                            <template x-if="paymentMethod === 'bank_transfer'">
                                <span><i class="fa-solid fa-money-bill-transfer mr-2"></i> Sipariş Tamamla (Havale)</span>
                            </template>
                        </button>
                    </div>

                    {{-- Güvenli Ödeme (Küçük) --}}
                    <div class="mt-3 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center justify-center">
                            <i class="fa-solid fa-lock text-green-600 dark:text-green-400 text-xs mr-1"></i>
                            256-bit SSL Güvenli Ödeme
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- PayTR iframe Modal --}}
    @if($showPaymentModal ?? false)
        @teleport('body')
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto">
            {{-- Backdrop (karartma) --}}
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden z-[10000] my-8">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-credit-card text-blue-600 dark:text-blue-400 text-xl"></i>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Güvenli Ödeme</h3>
                    </div>
                    <button wire:click="closePaymentModal"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fa-solid fa-times text-2xl"></i>
                    </button>
                </div>

                {{-- PayTR iframe --}}
                <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 80px);">
                    @if($paymentIframeUrl)
                        <iframe
                            src="{{ $paymentIframeUrl }}"
                            id="paytriframe"
                            frameborder="0"
                            scrolling="no"
                            style="width: 100%; min-height: 600px;"
                            class="rounded-lg">
                        </iframe>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-600 dark:text-blue-400 mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400">Ödeme ekranı yükleniyor...</p>
                        </div>
                    @endif
                </div>

                {{-- Footer - Güvenlik Bilgisi --}}
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock text-green-600 dark:text-green-400"></i>
                        256-bit SSL şifreli güvenli ödeme - PayTR Güvencesiyle
                    </p>
                </div>
            </div>
        </div>

        {{-- PayTR iframeResizer Script --}}
        @push('scripts')
        <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Iframe yüklendiğinde resize aktifleştir
                const iframe = document.getElementById('paytriframe');
                if (iframe) {
                    iFrameResize({
                        log: false,
                        checkOrigin: false,
                        heightCalculationMethod: 'bodyScroll'
                    }, '#paytriframe');
                }
            });

            // Livewire component güncellendiğinde iframe'i yeniden başlat
            Livewire.hook('message.processed', (message, component) => {
                const iframe = document.getElementById('paytriframe');
                if (iframe) {
                    iFrameResize({
                        log: false,
                        checkOrigin: false,
                        heightCalculationMethod: 'bodyScroll'
                    }, '#paytriframe');
                }
            });
        </script>
        @endpush
        @endteleport
    @endif
</div>
