<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Sipariş Tamamla</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Sol Taraf: Formlar --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 1. İletişim Bilgileri --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            İletişim Bilgileri
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Ad <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="contact_first_name"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Soyad <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="contact_last_name"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    E-posta <span class="text-red-500">*</span>
                                </label>
                                <input type="email" wire:model="contact_email"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Telefon <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" wire:model="contact_phone"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="05XX XXX XX XX">
                                @error('contact_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Fatura Bilgileri --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Fatura Bilgileri
                        </h2>

                        {{-- Fatura Tipi Seçimi --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Fatura Tipi <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model="billing_type" value="individual"
                                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-gray-900 dark:text-white">Bireysel (TC Kimlik)</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model="billing_type" value="corporate"
                                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-gray-900 dark:text-white">Kurumsal (VKN)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Bireysel Fatura --}}
                        @if($billing_type === 'individual')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    TC Kimlik No <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="billing_tax_number"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="XXXXXXXXXXX" maxlength="11">
                                @error('billing_tax_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- Kurumsal Fatura --}}
                        @if($billing_type === 'corporate')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Şirket Unvanı <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="billing_company_name"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('billing_company_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Vergi Dairesi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="billing_tax_office"
                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('billing_tax_office') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Vergi Kimlik No (VKN) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="billing_tax_number"
                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="XXXXXXXXXX" maxlength="10">
                                        @error('billing_tax_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Fatura Adresi --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Fatura Adresi <span class="text-red-500">*</span>
                            </label>
                            @livewire('shop::front.address-manager', [
                                'customerId' => $customerId,
                                'addressType' => 'billing',
                                'selectedAddressId' => $billing_address_id
                            ])
                            @error('billing_address_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- 3. Teslimat Adresi --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                            Teslimat Adresi
                        </h2>

                        {{-- Fatura adresi ile aynı --}}
                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="shipping_same_as_billing"
                                    class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-gray-900 dark:text-white">Fatura adresi ile aynı</span>
                            </label>
                        </div>

                        @if(!$shipping_same_as_billing)
                            @livewire('shop::front.address-manager', [
                                'customerId' => $customerId,
                                'addressType' => 'shipping',
                                'selectedAddressId' => $shipping_address_id
                            ])
                            @error('shipping_address_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    {{-- 4. Sözleşmeler --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Sözleşmeler</h2>

                        <div class="space-y-3">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_kvkk"
                                    class="w-4 h-4 mt-1 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">
                                    <a href="#" class="text-blue-600 hover:underline">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>
                            @error('agree_kvkk') <span class="text-red-500 text-sm block ml-6">{{ $message }}</span> @enderror

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_distance_selling"
                                    class="w-4 h-4 mt-1 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">
                                    <a href="#" class="text-blue-600 hover:underline">Mesafeli Satış Sözleşmesi</a>'ni okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>
                            @error('agree_distance_selling') <span class="text-red-500 text-sm block ml-6">{{ $message }}</span> @enderror

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_preliminary_info"
                                    class="w-4 h-4 mt-1 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">
                                    <a href="#" class="text-blue-600 hover:underline">Ön Bilgilendirme Formu</a>'nu okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>
                            @error('agree_preliminary_info') <span class="text-red-500 text-sm block ml-6">{{ $message }}</span> @enderror

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_marketing"
                                    class="w-4 h-4 mt-1 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">
                                    Kampanya, haber ve fırsatlardan haberdar olmak istiyorum. (İsteğe bağlı)
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Sağ Taraf: Sipariş Özeti --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-8">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Sipariş Özeti</h2>

                        {{-- Ürünler --}}
                        <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                            @forelse($items as $item)
                                <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                                    @if($item->product->getMedia('gallery')->first())
                                        <img src="{{ thumb($item->product->getMedia('gallery')->first(), 60, 60) }}"
                                            alt="{{ $item->product->getTranslated('title', app()->getLocale()) }}"
                                            class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->product->getTranslated('title', app()->getLocale()) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Adet: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Sepetiniz boş</p>
                            @endforelse
                        </div>

                        {{-- Tutar Bilgileri --}}
                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Ara Toplam</span>
                                <span>{{ number_format($subtotal, 2) }} TRY</span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>KDV (%{{ config('shop.tax_rate', 20) }})</span>
                                <span>{{ number_format($taxAmount, 2) }} TRY</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span>Toplam</span>
                                <span>{{ number_format($total, 2) }} TRY</span>
                            </div>
                        </div>

                        {{-- Sipariş Tamamla Butonu --}}
                        <button wire:click="submitOrder" wire:loading.attr="disabled"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submitOrder">
                                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Siparişi Tamamla
                            </span>
                            <span wire:loading wire:target="submitOrder">
                                <svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                İşleniyor...
                            </span>
                        </button>

                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3">
                            Güvenli ödeme yapacaksınız
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</div>
