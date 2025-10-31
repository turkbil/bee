<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sipariş Tamamla</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Sol Taraf: Checkout Form --}}
                <div class="lg:col-span-2 space-y-3">

                    {{-- 1. İletişim Bilgileri (Her Zaman Açık) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i class="fa-solid fa-user mr-2 text-blue-500"></i>
                            İletişim Bilgileri
                        </h2>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    Ad <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="contact_first_name"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    Soyad <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="contact_last_name"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    E-posta <span class="text-red-500">*</span>
                                </label>
                                <input type="email" wire:model="contact_email"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    Telefon <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" wire:model="contact_phone"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="05XX XXX XX XX">
                                @error('contact_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Fatura Bilgileri (Özet + Modal) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i class="fa-solid fa-file-invoice mr-2 text-blue-500"></i>
                            Fatura Bilgileri
                        </h2>

                        {{-- Fatura Tipi Seçimi (Kompakt) --}}
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-2">Fatura Tipi <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model.live="billing_type" value="individual" class="peer sr-only">
                                    <div class="border-2 rounded-lg p-2 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600">
                                        <div class="flex items-center gap-1">
                                            <i class="fa-solid fa-user text-xs"></i>
                                            <span class="font-medium text-sm">Bireysel</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model.live="billing_type" value="corporate" class="peer sr-only">
                                    <div class="border-2 rounded-lg p-2 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600">
                                        <div class="flex items-center gap-1">
                                            <i class="fa-solid fa-building text-xs"></i>
                                            <span class="font-medium text-sm">Kurumsal</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Bireysel Fatura Form --}}
                        @if($billing_type === 'individual')
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border-l-2 border-blue-500">
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                        TC Kimlik No <span class="text-gray-400">(Opsiyonel)</span>
                                    </label>
                                    <input type="text" wire:model="billing_tax_number"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                        placeholder="11 haneli (opsiyonel)" maxlength="11">
                                    @error('billing_tax_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        {{-- Kurumsal Fatura Form --}}
                        @if($billing_type === 'corporate')
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border-l-2 border-blue-500">
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                            Şirket Ünvanı <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="billing_company_name"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                            placeholder="ABC Teknoloji A.Ş.">
                                        @error('billing_company_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                                Vergi Dairesi <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model="billing_tax_office"
                                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                                placeholder="Kadıköy">
                                            @error('billing_tax_office') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                                VKN <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model="billing_tax_number"
                                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                                placeholder="10 haneli" maxlength="10">
                                            @error('billing_tax_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Fatura Adresi (Özet + Modal) --}}
                        <div class="mt-3">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-2">
                                Fatura Adresi <span class="text-red-500">*</span>
                            </label>

                            @if($billing_address_id)
                                @php
                                    $billingAddr = \Modules\Shop\App\Models\ShopCustomerAddress::find($billing_address_id);
                                @endphp
                                @if($billingAddr)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-start justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $billingAddr->title }}</span>
                                            <button wire:click="$set('showBillingAddressModal', true)" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                                Ekle / Değiştir
                                            </button>
                                        </div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                            <p>{{ $billingAddr->city }} / {{ $billingAddr->district }}</p>
                                            <p>{{ $billingAddr->address_line_1 }}</p>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <button wire:click="$set('showBillingAddressModal', true)" class="w-full text-left bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border border-yellow-200 dark:border-yellow-600 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition">
                                    <span class="text-sm text-yellow-800 dark:text-yellow-200">
                                        <i class="fa-solid fa-plus mr-1"></i> Fatura Adresi Ekle / Seç
                                    </span>
                                </button>
                            @endif

                            @error('billing_address_id') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Fatura Adresi Modal --}}
                        @if($showBillingAddressModal ?? false)
                            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showBillingAddressModal', false)"></div>
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Fatura Adresi Seç</h3>
                                                <button wire:click="$set('showBillingAddressModal', false)" class="text-gray-400 hover:text-gray-500">
                                                    <i class="fa-solid fa-times text-xl"></i>
                                                </button>
                                            </div>
                                            @livewire('shop::front.address-manager', [
                                                'customerId' => $customerId,
                                                'addressType' => 'billing',
                                                'selectedAddressId' => $billing_address_id
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 3. Teslimat Adresi (Özet + Modal) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i class="fa-solid fa-truck mr-2 text-blue-500"></i>
                            Teslimat Adresi
                        </h2>

                        @if($shipping_address_id)
                            @php
                                $shippingAddr = \Modules\Shop\App\Models\ShopCustomerAddress::find($shipping_address_id);
                            @endphp
                            @if($shippingAddr)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $shippingAddr->title }}</span>
                                        <button wire:click="$set('showShippingModal', true)" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                            Ekle / Değiştir
                                        </button>
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        <p>{{ $shippingAddr->city }} / {{ $shippingAddr->district }}</p>
                                        <p>{{ $shippingAddr->address_line_1 }}</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <button wire:click="$set('showShippingModal', true)" class="w-full text-left bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border border-yellow-200 dark:border-yellow-600 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition">
                                <span class="text-sm text-yellow-800 dark:text-yellow-200">
                                    <i class="fa-solid fa-plus mr-1"></i> Teslimat Adresi Ekle / Seç
                                </span>
                            </button>
                        @endif

                        @error('shipping_address_id') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror

                        {{-- Teslimat Adresi Modal --}}
                        @if($showShippingModal ?? false)
                            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showShippingModal', false)"></div>
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Teslimat Adresi Seç</h3>
                                                <button wire:click="$set('showShippingModal', false)" class="text-gray-400 hover:text-gray-500">
                                                    <i class="fa-solid fa-times text-xl"></i>
                                                </button>
                                            </div>
                                            @livewire('shop::front.address-manager', [
                                                'customerId' => $customerId,
                                                'addressType' => 'shipping',
                                                'selectedAddressId' => $shipping_address_id
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 4. Anlaşmalar (Basit Checkbox - Hepsiburada tarzı) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <div class="space-y-3">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_kvkk" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    <a href="#" class="text-blue-600 hover:underline">KVKK</a>'yı okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>
                            @error('agree_kvkk') <span class="text-red-500 text-xs block ml-6">{{ $message }}</span> @enderror

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_distance_selling" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    <a href="#" class="text-blue-600 hover:underline">Mesafeli Satış Sözleşmesi</a>'ni okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>
                            @error('agree_distance_selling') <span class="text-red-500 text-xs block ml-6">{{ $message }}</span> @enderror

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_preliminary_info" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    <a href="#" class="text-blue-600 hover:underline">Ön Bilgilendirme Formu</a>'nu okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>
                            @error('agree_preliminary_info') <span class="text-red-500 text-xs block ml-6">{{ $message }}</span> @enderror

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_marketing" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    Kampanya, haber ve fırsatlardan haberdar olmak istiyorum. (İsteğe bağlı)
                                </span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Sağ Taraf: Sipariş Özeti (Kompakt) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sticky top-4">
                        {{-- Başlık --}}
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Sipariş Özeti</h2>

                        {{-- Ürünler (Kompakt) --}}
                        <div class="space-y-2 mb-4 max-h-60 overflow-y-auto">
                            @forelse($items as $item)
                                @php
                                    $firstMedia = $item->product->getMedia('gallery')->first();
                                    $productTitle = $item->product->getTranslated('title', app()->getLocale());
                                @endphp
                                <div class="flex items-center gap-2 py-2 border-b border-gray-100 dark:border-gray-700">
                                    {{-- Ürün Görseli --}}
                                    @if($firstMedia)
                                        <img src="{{ thumb($firstMedia, 50, 50, ['scale' => 1]) }}"
                                            alt="{{ $productTitle }}"
                                            class="w-12 h-12 object-cover rounded border border-gray-200 dark:border-gray-600"
                                            loading="lazy">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-image text-gray-400 text-lg"></i>
                                        </div>
                                    @endif

                                    {{-- Ürün Bilgisi --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $productTitle }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->quantity }} adet</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4 text-sm">Sepetiniz boş</p>
                            @endforelse
                        </div>

                        {{-- Tutar Bilgileri --}}
                        <div class="space-y-2 mb-4 pb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Ara Toplam</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($subtotal, 2) }} TRY</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">KDV (%{{ config('shop.tax_rate', 20) }})</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($taxAmount, 2) }} TRY</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span>Toplam</span>
                                <span>{{ number_format($total, 2) }} TRY</span>
                            </div>
                        </div>

                        {{-- Sipariş Tamamla Butonu --}}
                        <button wire:click="submitOrder" wire:loading.attr="disabled"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submitOrder">
                                <i class="fa-solid fa-check mr-2"></i>
                                Siparişi Tamamla
                            </span>
                            <span wire:loading wire:target="submitOrder">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                İşleniyor...
                            </span>
                        </button>

                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3">
                            <i class="fa-solid fa-lock mr-1"></i> Güvenli ödeme yapacaksınız
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('order_success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('order_success') }}
        </div>
    @endif

    @if (session()->has('address_success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('address_success') }}
        </div>
    @endif
</div>
