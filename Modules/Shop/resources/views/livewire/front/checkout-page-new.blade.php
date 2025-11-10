<div class="min-h-screen py-8">
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

                {{-- 3. Fatura Bilgileri (Vergi Bilgileri) --}}
                <div class="bg-white/20 dark:bg-gray-800/20 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fa-solid fa-file-invoice mr-2 text-blue-500 dark:text-blue-400"></i>
                            Fatura Bilgileri
                        </h2>
                        <button wire:click="openBillingModal"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                            <i class="fa-solid fa-edit mr-1"></i> Düzenle
                        </button>
                    </div>

                    {{-- Özet Gösterimi (Sadece Vergi Bilgileri) --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        @if($billing_type === 'corporate' && $billing_company_name)
                            <p class="flex items-center">
                                <i class="fa-solid fa-building text-xs mr-2 text-gray-500 dark:text-gray-500"></i>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $billing_company_name }}</span>
                                <span class="ml-2 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded">Kurumsal</span>
                            </p>
                            @if($billing_tax_number)
                                <p class="text-xs ml-5 text-gray-600 dark:text-gray-400">VKN: {{ $billing_tax_number }}</p>
                            @endif
                            @if($billing_tax_office)
                                <p class="text-xs ml-5 text-gray-600 dark:text-gray-400">Vergi Dairesi: {{ $billing_tax_office }}</p>
                            @endif
                        @else
                            <p class="flex items-center">
                                <i class="fa-solid fa-user text-xs mr-2 text-gray-500 dark:text-gray-500"></i>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $contact_first_name }} {{ $contact_last_name }}</span>
                                <span class="ml-2 text-xs bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-0.5 rounded">Bireysel</span>
                            </p>
                            @if($billing_tax_number)
                                <p class="text-xs ml-5 text-gray-600 dark:text-gray-400">TC: {{ $billing_tax_number }}</p>
                            @endif
                        @endif
                    </div>

                    {{-- Modal: Fatura Bilgileri Düzenleme --}}
                    @if($showBillingModal ?? false)
                        @teleport('body')
                        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto" wire:click.self="closeBillingModal">
                            {{-- Backdrop --}}
                            <div class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm" wire:click="closeBillingModal"></div>

                            {{-- Modal Content --}}
                            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 z-[10000] my-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Fatura Bilgileri</h3>
                                        <button wire:click="closeBillingModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                            <i class="fa-solid fa-times text-2xl"></i>
                                        </button>
                                    </div>

                                    {{-- Bireysel / Kurumsal Seçimi --}}
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Fatura Türü</label>
                                        <div class="flex gap-4">
                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" wire:model.live="billing_type" value="individual" class="hidden peer">
                                                <div class="border-2 border-gray-300 dark:border-gray-600 peer-checked:border-blue-500 dark:peer-checked:border-blue-400 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 rounded-lg p-4 transition-all">
                                                    <div class="flex items-center justify-center">
                                                        <i class="fa-solid fa-user text-2xl text-gray-600 dark:text-gray-400 peer-checked:text-blue-600"></i>
                                                    </div>
                                                    <div class="text-center mt-2 text-sm font-medium text-gray-900 dark:text-white">Bireysel</div>
                                                </div>
                                            </label>

                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" wire:model.live="billing_type" value="corporate" class="hidden peer">
                                                <div class="border-2 border-gray-300 dark:border-gray-600 peer-checked:border-blue-500 dark:peer-checked:border-blue-400 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 rounded-lg p-4 transition-all">
                                                    <div class="flex items-center justify-center">
                                                        <i class="fa-solid fa-building text-2xl text-gray-600 dark:text-gray-400 peer-checked:text-blue-600"></i>
                                                    </div>
                                                    <div class="text-center mt-2 text-sm font-medium text-gray-900 dark:text-white">Kurumsal</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Bireysel İçin TCKN --}}
                                    @if($billing_type === 'individual')
                                        <div class="space-y-4 mb-6">
                                            <div>
                                                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                                    TC Kimlik No
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">(Opsiyonel - Fatura için)</span>
                                                </label>
                                                <input type="text" wire:model="billing_tax_number" maxlength="11" placeholder="XXXXXXXXXXX"
                                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                                                @error('billing_tax_number') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Kurumsal İçin Ek Alanlar --}}
                                    @if($billing_type === 'corporate')
                                        <div class="space-y-4 mb-6">
                                            <div>
                                                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                                    Şirket Ünvanı <span class="text-red-500 dark:text-red-400">*</span>
                                                </label>
                                                <input type="text" wire:model="billing_company_name"
                                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                                                @error('billing_company_name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                                        Vergi Kimlik No (VKN) <span class="text-red-500 dark:text-red-400">*</span>
                                                    </label>
                                                    <input type="text" wire:model="billing_tax_number" maxlength="10"
                                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                                                    @error('billing_tax_number') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                                        Vergi Dairesi <span class="text-red-500 dark:text-red-400">*</span>
                                                    </label>
                                                    <input type="text" wire:model="billing_tax_office"
                                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                                                    @error('billing_tax_office') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Modal Butonlar --}}
                                    <div class="flex justify-end gap-3">
                                        <button wire:click="closeBillingModal"
                                            class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                            İptal
                                        </button>
                                        <button wire:click="closeBillingModal"
                                            class="px-6 py-2.5 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                            Kaydet
                                        </button>
                                    </div>
                            </div>
                        </div>
                        @endteleport
                    @endif
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

                        {{-- Kredi Kartı Komisyonu --}}
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-credit-card text-xs mr-1"></i>
                                Kredi Kartı Komisyonu (%4,99)
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ number_format(round($creditCardFee), 0, ',', '.') }}
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

                    {{-- Tek Checkbox (Combined Agreement) --}}
                    <div class="mb-4">
                        <label class="flex items-start cursor-pointer group">
                            <input type="checkbox" wire:model="agree_all"
                                class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 rounded transition-all">
                            <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                <a href="/cayma-hakki" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Ön Bilgilendirme Formu</a>'nu ve
                                <a href="/mesafeli-satis" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Mesafeli Satış Sözleşmesi</a>'ni onaylıyorum.
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

                    {{-- TEST BUTONU - Livewire Çalışıyor mu? --}}
                    <button type="button"
                        wire:click="testButton"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg mb-2">
                        <i class="fa-solid fa-bug mr-2"></i>
                        TEST: Livewire Çalışıyor mu?
                    </button>

                    {{-- Ödemeye Geç Butonu --}}
                    <button type="button"
                        wire:click="proceedToPayment"
                        wire:loading.attr="disabled"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-credit-card mr-2"></i>
                        <span wire:loading.remove wire:target="proceedToPayment">Ödemeye Geç</span>
                        <span wire:loading wire:target="proceedToPayment">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            İşleniyor...
                        </span>
                    </button>

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
