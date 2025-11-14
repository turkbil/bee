<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Sipariş Tamamla</h1>

        {{-- Boş sepet kontrolü - JavaScript localStorage yüklendikten sonra --}}
        @if(!$items || $items->count() === 0)
            <div class="max-w-md mx-auto text-center py-16" id="empty-cart-message">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                    <i class="fa-solid fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sepetiniz Boş</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Checkout yapabilmek için sepetinize ürün eklemelisiniz.</p>
                    <a href="/cart" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-shopping-cart mr-2"></i>
                        Sepete Git
                    </a>
                </div>
            </div>
        @else
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

                    <div class="space-y-4">
                        {{-- Satır 1: Ad, Soyad --}}
                        <div class="grid grid-cols-2 gap-4">
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
                        </div>

                        {{-- Satır 2: E-posta, Telefon --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                                    E-posta <span class="text-red-500 dark:text-red-400">*</span>
                                </label>
                                @auth
                                    {{-- Üyeler için readonly --}}
                                    <input type="email" wire:model="contact_email" readonly
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <i class="fa-solid fa-info-circle mr-1"></i>
                                        Hesabınıza kayıtlı e-posta adresi
                                    </p>
                                @else
                                    {{-- Misafirler için editable --}}
                                    <input type="email" wire:model="contact_email"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
                                        placeholder="ornek@email.com">
                                @endauth
                                @error('contact_email') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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

                    {{-- Offcanvas: Teslimat Adresi (Slide-over) --}}
                    @if($showShippingModal ?? false)
                        <div class="fixed inset-0 z-[999999] overflow-hidden" @keydown.escape.window="$wire.closeShippingModal()">
                            {{-- Backdrop (Non-clickable) --}}
                            <div class="fixed inset-0 bg-black/60"></div>

                            {{-- Offcanvas Panel --}}
                            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                                <div class="w-screen max-w-2xl">
                                    <div class="flex h-full flex-col bg-white dark:bg-gray-800 shadow-2xl">
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                                <i class="fa-solid fa-truck mr-2 text-blue-600 dark:text-blue-400"></i>
                                                Teslimat Adresi
                                            </h3>
                                            <button wire:click="closeShippingModal"
                                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-lg">
                                                <i class="fa-solid fa-times text-xl"></i>
                                            </button>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 overflow-y-auto px-6 py-4">
                                            <livewire:shop::front.address-manager
                                                :customerId="$customerId"
                                                addressType="shipping"
                                                :selectedAddressId="$shipping_address_id"
                                                :key="'shipping-'.$customerId" />
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                            <button wire:click="closeShippingModal"
                                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg">
                                                <i class="fa-solid fa-check mr-2"></i>Kaydet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                    {{-- Offcanvas: Fatura Bilgileri (Slide-over) --}}
                    @if($showBillingModal ?? false)
                        <div class="fixed inset-0 z-[999999] overflow-hidden" @keydown.escape.window="$wire.closeBillingModal()">
                            {{-- Backdrop (Non-clickable) --}}
                            <div class="fixed inset-0 bg-black/60"></div>

                            {{-- Offcanvas Panel --}}
                            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                                <div class="w-screen max-w-xl">
                                    <div class="flex h-full flex-col bg-white dark:bg-gray-800 shadow-2xl">
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                                <i class="fa-solid fa-file-invoice mr-2 text-blue-600 dark:text-blue-400"></i>
                                                Fatura Bilgileri
                                            </h3>
                                            <button wire:click="closeBillingModal"
                                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-lg">
                                                <i class="fa-solid fa-times text-xl"></i>
                                            </button>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 overflow-y-auto px-6 py-4">
                                            <div class="space-y-6">

                                                {{-- Bireysel / Kurumsal Seçimi --}}
                                                <div class="mb-6">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Fatura Türü</label>
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <label class="cursor-pointer">
                                                            <input type="radio" wire:model.live="billing_type" value="individual" class="hidden peer">
                                                            <div class="border-2 border-gray-300 dark:border-gray-600 peer-checked:border-blue-500 dark:peer-checked:border-blue-400 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-500">
                                                                <div class="flex items-center justify-center">
                                                                    <i class="fa-solid fa-user text-2xl text-gray-600 dark:text-gray-400"></i>
                                                                </div>
                                                                <div class="text-center mt-2 text-sm font-medium text-gray-900 dark:text-white">Bireysel</div>
                                                            </div>
                                                        </label>

                                                        <label class="cursor-pointer">
                                                            <input type="radio" wire:model.live="billing_type" value="corporate" class="hidden peer">
                                                            <div class="border-2 border-gray-300 dark:border-gray-600 peer-checked:border-blue-500 dark:peer-checked:border-blue-400 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-500">
                                                                <div class="flex items-center justify-center">
                                                                    <i class="fa-solid fa-building text-2xl text-gray-600 dark:text-gray-400"></i>
                                                                </div>
                                                                <div class="text-center mt-2 text-sm font-medium text-gray-900 dark:text-white">Kurumsal</div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>

                                                {{-- Bireysel İçin TCKN --}}
                                                @if($billing_type === 'individual')
                                                    <div class="space-y-4">
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
                                                    <div class="space-y-4">
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

                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                            <button wire:click="closeBillingModal"
                                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg">
                                                <i class="fa-solid fa-check mr-2"></i>Kaydet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                    {{-- Offcanvas: Fatura Adresi (Slide-over) --}}
                    @if($showBillingAddressModal ?? false)
                        <div class="fixed inset-0 z-[999999] overflow-hidden" @keydown.escape.window="$wire.closeBillingAddressModal()">
                            {{-- Backdrop (Non-clickable) --}}
                            <div class="fixed inset-0 bg-black/60"></div>

                            {{-- Offcanvas Panel --}}
                            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                                <div class="w-screen max-w-2xl">
                                    <div class="flex h-full flex-col bg-white dark:bg-gray-800 shadow-2xl">
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                                <i class="fa-solid fa-file-invoice-dollar mr-2 text-blue-600 dark:text-blue-400"></i>
                                                Fatura Adresi
                                            </h3>
                                            <button wire:click="closeBillingAddressModal"
                                                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-lg">
                                                <i class="fa-solid fa-times text-xl"></i>
                                            </button>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 overflow-y-auto px-6 py-4">
                                            <livewire:shop::front.address-manager
                                                :customerId="$customerId"
                                                addressType="billing"
                                                :selectedAddressId="$billing_address_id"
                                                :key="'billing-addr-'.$customerId" />
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                            <button wire:click="closeBillingAddressModal"
                                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg">
                                                <i class="fa-solid fa-check mr-2"></i>Kaydet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            wire:click="proceedToPayment"
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
        @endif {{-- End of: if items not empty --}}

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

{{-- localStorage'dan cart_id restore --}}
@script
<script>
    console.log('🛒 CheckoutPage: Initializing...');

    // localStorage'dan cart_id oku
    const storedCartId = localStorage.getItem('cart_id');
    if (storedCartId) {
        console.log('📦 CheckoutPage: Found cart_id in localStorage:', storedCartId);

        // Backend'e cart_id gönder
        $wire.loadCartById(parseInt(storedCartId)).then(() => {
            console.log('✅ CheckoutPage: Cart loaded from localStorage');

            // Boş sepet mesajını gizle (eğer cart yüklendiyse sayfa yenilenecek)
            const emptyMsg = document.getElementById('empty-cart-message');
            if (emptyMsg && $wire.items && $wire.items.length > 0) {
                emptyMsg.style.display = 'none';
            }
        });
    } else {
        console.log('ℹ️ CheckoutPage: No cart_id in localStorage, using session cart');
    }
</script>
@endscript
