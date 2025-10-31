<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sipariş Tamamla</h1>

            <div class="space-y-3">

                {{-- 1. İletişim Bilgileri (Her Zaman Açık) --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <i class="fa-solid fa-user mr-2 text-blue-500 dark:text-blue-400"></i>
                        İletişim Bilgileri
                    </h2>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-700 dark:text-gray-300 mb-1">
                                Ad <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <input type="text" wire:model="contact_first_name"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                            @error('contact_first_name') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs text-gray-700 dark:text-gray-300 mb-1">
                                Soyad <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <input type="text" wire:model="contact_last_name"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                            @error('contact_last_name') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs text-gray-700 dark:text-gray-300 mb-1">
                                E-posta <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <input type="email" wire:model="contact_email"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                            @error('contact_email') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs text-gray-700 dark:text-gray-300 mb-1">
                                Telefon <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <input type="tel" wire:model="contact_phone"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                                placeholder="05XX XXX XX XX">
                            @error('contact_phone') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Fatura Bilgileri (KAPALI - Sadece Özet + Modal) --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice text-blue-500 dark:text-blue-400"></i>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Fatura Bilgileri</h2>
                        </div>
                        <button wire:click="$set('showBillingModal', true)" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                            <i class="fa-solid fa-edit mr-1"></i> Düzenle
                        </button>
                    </div>

                    {{-- Fatura Özeti (Tek Satır) --}}
                    <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        @if($billing_type === 'corporate' && $billing_company_name)
                            <p><i class="fa-solid fa-building text-xs mr-1"></i> {{ $billing_company_name }} - Kurumsal</p>
                        @else
                            <p><i class="fa-solid fa-user text-xs mr-1"></i> {{ $contact_first_name }} {{ $contact_last_name }} - Bireysel</p>
                        @endif
                        @if($billing_address_id)
                            @php
                                $billingAddr = \Modules\Shop\App\Models\ShopCustomerAddress::find($billing_address_id);
                            @endphp
                            @if($billingAddr)
                                <p class="text-xs mt-1">{{ $billingAddr->title }} - {{ $billingAddr->city }}</p>
                            @else
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Fatura adresi seçilmedi</p>
                            @endif
                        @else
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Fatura adresi seçilmedi</p>
                        @endif
                    </div>

                    {{-- Fatura Modal --}}
                    @if($showBillingModal ?? false)
                        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="$set('showBillingModal', false)"></div>
                                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                    <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Fatura Bilgileri</h3>
                                            <button wire:click="$set('showBillingModal', false)" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                                                <i class="fa-solid fa-times text-xl"></i>
                                            </button>
                                        </div>

                                        {{-- Fatura Tipi --}}
                                        <div class="mb-4">
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Fatura Tipi</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <label class="cursor-pointer">
                                                    <input type="radio" wire:model.live="billing_type" value="individual" class="peer sr-only">
                                                    <div class="border-2 rounded-lg p-2 peer-checked:border-blue-600 dark:peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600">
                                                        <i class="fa-solid fa-user text-xs mr-1"></i> Bireysel
                                                    </div>
                                                </label>
                                                <label class="cursor-pointer">
                                                    <input type="radio" wire:model.live="billing_type" value="corporate" class="peer sr-only">
                                                    <div class="border-2 rounded-lg p-2 peer-checked:border-blue-600 dark:peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600">
                                                        <i class="fa-solid fa-building text-xs mr-1"></i> Kurumsal
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Bireysel Form --}}
                                        @if($billing_type === 'individual')
                                            <div class="mb-4">
                                                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">TC Kimlik No (Opsiyonel)</label>
                                                <input type="text" wire:model="billing_tax_number" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" maxlength="11">
                                            </div>
                                        @endif

                                        {{-- Kurumsal Form --}}
                                        @if($billing_type === 'corporate')
                                            <div class="space-y-3 mb-4">
                                                <div>
                                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Şirket Ünvanı *</label>
                                                    <input type="text" wire:model="billing_company_name" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Vergi Dairesi *</label>
                                                        <input type="text" wire:model="billing_tax_office" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">VKN *</label>
                                                        <input type="text" wire:model="billing_tax_number" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" maxlength="10">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Fatura Adresi --}}
                                        <div class="mb-4">
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Fatura Adresi *</label>
                                            @livewire('shop::front.address-manager', [
                                                'customerId' => $customerId,
                                                'addressType' => 'billing',
                                                'selectedAddressId' => $billing_address_id
                                            ])
                                        </div>

                                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <button wire:click="$set('showBillingModal', false)" class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Kapat</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 3. Teslimat Adresi (KAPALI - Sadece Özet + Modal) --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-truck text-blue-500 dark:text-blue-400"></i>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Teslimat Adresi</h2>
                        </div>
                        <button wire:click="$set('showShippingModal', true)" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                            <i class="fa-solid fa-edit mr-1"></i> Düzenle
                        </button>
                    </div>

                    {{-- Teslimat Özeti --}}
                    <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        @if($shipping_address_id)
                            @php
                                $shippingAddr = \Modules\Shop\App\Models\ShopCustomerAddress::find($shipping_address_id);
                            @endphp
                            @if($shippingAddr)
                                <p>{{ $shippingAddr->title }} - {{ $shippingAddr->city }}, {{ $shippingAddr->district }}</p>
                                <p class="text-xs mt-1">{{ $shippingAddr->address_line_1 }}</p>
                            @else
                                <p class="text-xs text-yellow-600 dark:text-yellow-400">Teslimat adresi seçilmedi</p>
                            @endif
                        @else
                            <p class="text-xs text-yellow-600 dark:text-yellow-400">Teslimat adresi seçilmedi</p>
                        @endif
                    </div>

                    {{-- Teslimat Modal --}}
                    @if($showShippingModal ?? false)
                        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="$set('showShippingModal', false)"></div>
                                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                    <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Teslimat Adresi</h3>
                                            <button wire:click="$set('showShippingModal', false)" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                                                <i class="fa-solid fa-times text-xl"></i>
                                            </button>
                                        </div>
                                        @livewire('shop::front.address-manager', [
                                            'customerId' => $customerId,
                                            'addressType' => 'shipping',
                                            'selectedAddressId' => $shipping_address_id
                                        ])
                                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700 mt-4">
                                            <button wire:click="$set('showShippingModal', false)" class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Kapat</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 4. Sipariş Tamamla (Tek Checkbox + Buton) --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="space-y-3">
                        {{-- Tek Checkbox - Tüm Anlaşmalar --}}
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" wire:model="agree_all" class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Ön Bilgilendirme Formu</a>'nu ve
                                <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Mesafeli Satış Sözleşmesi</a>'ni onaylıyorum.
                                <span class="text-red-500 dark:text-red-400">*</span>
                            </span>
                        </label>
                        @error('agree_all') <span class="text-red-500 dark:text-red-400 text-xs block ml-6">{{ $message }}</span> @enderror

                        {{-- Sipariş Tamamla Butonu --}}
                        <button wire:click="submitOrder" wire:loading.attr="disabled"
                            class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed mt-4">
                            <span wire:loading.remove wire:target="submitOrder">
                                <i class="fa-solid fa-check mr-2"></i>
                                Siparişi Tamamla
                            </span>
                            <span wire:loading wire:target="submitOrder">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                İşleniyor...
                            </span>
                        </button>

                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                            <i class="fa-solid fa-lock mr-1"></i> Güvenli ödeme yapacaksınız
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 dark:bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('order_success'))
        <div class="fixed bottom-4 right-4 bg-green-500 dark:bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('order_success') }}
        </div>
    @endif

    @if (session()->has('address_success'))
        <div class="fixed bottom-4 right-4 bg-green-500 dark:bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('address_success') }}
        </div>
    @endif
</div>
