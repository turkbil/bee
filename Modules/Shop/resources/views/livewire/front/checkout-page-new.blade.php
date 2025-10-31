<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sipariş Tamamla</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Sol Taraf: Hepsiburada Style Checkout --}}
                <div class="lg:col-span-2 space-y-3">

                    {{-- 1. Teslimat Adresim (Hepsiburada Pattern) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Teslimat adresim</h2>

                        @if($shipping_address_id)
                            @php
                                $shippingAddr = \Modules\Shop\App\Models\ShopCustomerAddress::find($shipping_address_id);
                            @endphp
                            @if($shippingAddr)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $shippingAddr->title }}</span>
                                        <button wire:click="$set('showShippingModal', true)" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                            Ekle / Değiştir
                                        </button>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <p>{{ $shippingAddr->city }} / {{ $shippingAddr->district }}</p>
                                        <p>{{ $shippingAddr->address_line_1 }}</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border border-yellow-200 dark:border-yellow-600">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-2">Lütfen teslimat adresi seçin</p>
                                <button wire:click="$set('showShippingModal', true)" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    <i class="fa-solid fa-plus mr-1"></i> Adres Ekle / Seç
                                </button>
                            </div>
                        @endif

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

                    {{-- 2. Fatura Bilgilerim (Hepsiburada Pattern - Kompakt Özet) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i class="fa-solid fa-file-invoice mr-2 text-blue-500"></i>
                            Fatura Bilgilerim
                        </h2>

                        {{-- Fatura Özeti (Hepsiburada tarzı tek satır) --}}
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-2">
                                @if($billing_type === 'corporate' && $billing_company_name)
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        <i class="fa-solid fa-building text-xs mr-1"></i>
                                        {{ $billing_company_name }} - Kurumsal
                                        @if($billing_address_id)
                                            / {{ \Modules\Shop\App\Models\ShopCustomerAddress::find($billing_address_id)?->city }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        <i class="fa-solid fa-user text-xs mr-1"></i>
                                        {{ $contact_first_name }} {{ $contact_last_name }} - Bireysel
                                        @if($billing_address_id)
                                            / {{ \Modules\Shop\App\Models\ShopCustomerAddress::find($billing_address_id)?->city }}
                                        @endif
                                    </span>
                                @endif
                                <button wire:click="$set('showBillingModal', true)" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                    Ekle / Değiştir
                                </button>
                            </div>
                        </div>

                        {{-- Fatura Bilgileri Modal --}}
                        @if($showBillingModal ?? false)
                            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showBillingModal', false)"></div>
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Fatura Bilgileri</h3>
                                                <button wire:click="$set('showBillingModal', false)" class="text-gray-400 hover:text-gray-500">
                                                    <i class="fa-solid fa-times text-xl"></i>
                                                </button>
                                            </div>

                                            {{-- İletişim Bilgileri --}}
                                            <div class="mb-4">
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">İletişim Bilgileri</h4>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Ad *</label>
                                                        <input type="text" wire:model="contact_first_name" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Soyad *</label>
                                                        <input type="text" wire:model="contact_last_name" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">E-posta *</label>
                                                        <input type="email" wire:model="contact_email" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Telefon *</label>
                                                        <input type="tel" wire:model="contact_phone" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="05XX XXX XX XX">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Fatura Tipi --}}
                                            <div class="mb-4">
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fatura Tipi *</h4>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <label class="relative cursor-pointer">
                                                        <input type="radio" wire:model.live="billing_type" value="individual" class="peer sr-only">
                                                        <div class="border-2 rounded-lg p-2 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600">
                                                            <div class="flex items-center gap-1">
                                                                <i class="fa-solid fa-user text-sm"></i>
                                                                <span class="font-medium text-sm">Bireysel</span>
                                                            </div>
                                                        </div>
                                                    </label>
                                                    <label class="relative cursor-pointer">
                                                        <input type="radio" wire:model.live="billing_type" value="corporate" class="peer sr-only">
                                                        <div class="border-2 rounded-lg p-2 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600">
                                                            <div class="flex items-center gap-1">
                                                                <i class="fa-solid fa-building text-sm"></i>
                                                                <span class="font-medium text-sm">Kurumsal</span>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Bireysel Form --}}
                                            @if($billing_type === 'individual')
                                                <div class="mb-4">
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">TC Kimlik No <span class="text-gray-400">(Opsiyonel)</span></label>
                                                    <input type="text" wire:model="billing_tax_number" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="11 haneli" maxlength="11">
                                                </div>
                                            @endif

                                            {{-- Kurumsal Form --}}
                                            @if($billing_type === 'corporate')
                                                <div class="space-y-3 mb-4">
                                                    <div>
                                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Şirket Ünvanı *</label>
                                                        <input type="text" wire:model="billing_company_name" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Vergi Dairesi *</label>
                                                            <input type="text" wire:model="billing_tax_office" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">VKN *</label>
                                                            <input type="text" wire:model="billing_tax_number" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="10 haneli" maxlength="10">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Fatura Adresi --}}
                                            <div class="mb-4">
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fatura Adresi *</h4>
                                                @livewire('shop::front.address-manager', [
                                                    'customerId' => $customerId,
                                                    'addressType' => 'billing',
                                                    'selectedAddressId' => $billing_address_id
                                                ])
                                            </div>

                                            {{-- Kaydet Butonu --}}
                                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                <button wire:click="$set('showBillingModal', false)" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300">
                                                    İptal
                                                </button>
                                                <button wire:click="saveBillingInfo" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                                    <i class="fa-solid fa-save mr-1"></i> Kaydet
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 3. Ödeme Seçenekleri (Hepsiburada Pattern) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Ödeme seçeneklerim</h2>

                        <div class="space-y-2">
                            {{-- Banka/Kredi Kartı --}}
                            <label class="block">
                                <input type="radio" name="payment_method" value="credit_card" checked class="sr-only peer">
                                <div class="border-2 rounded-lg p-3 cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            <i class="fa-solid fa-credit-card mr-2"></i>
                                            Banka/Kredi Kartı
                                        </span>
                                        <div class="text-sm text-green-600 dark:text-green-400 font-medium">
                                            Güvenli Ödeme
                                        </div>
                                    </div>
                                </div>
                            </label>

                            {{-- Havale/EFT (Pasif gösterim) --}}
                            <div class="border-2 rounded-lg p-3 opacity-50 border-gray-300 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fa-solid fa-building-columns mr-2"></i>
                                        Havale/EFT
                                    </span>
                                    <span class="text-xs text-gray-500">Yakında</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Teslimat Seçenekleri --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Teslimat seçeneklerim</h2>

                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Standart Teslimat</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Tahmini teslim: 2-3 iş günü</div>
                                </div>
                                <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                    Kargo bedava
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Anlaşmalar (Hepsiburada tarzı basit checkbox) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <div class="space-y-3">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_kvkk" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    <a href="#" class="text-blue-600 hover:underline">KVKK</a>'yı okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_distance_selling" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    <a href="#" class="text-blue-600 hover:underline">Mesafeli Satış Sözleşmesi</a>'ni okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_preliminary_info" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    <a href="#" class="text-blue-600 hover:underline">Ön Bilgilendirme Formu</a>'nu okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                                </span>
                            </label>

                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" wire:model="agree_marketing" class="w-4 h-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                    Kampanya, haber ve fırsatlardan haberdar olmak istiyorum. (İsteğe bağlı)
                                </span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Sağ Taraf: Sipariş Özeti (Hepsiburada Ultra Kompakt) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sticky top-4">
                        {{-- Ödenecek Tutar --}}
                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">ÖDENECEK TUTAR</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($total, 2) }} <span class="text-sm font-normal">TRY</span>
                            </div>
                        </div>

                        {{-- Sipariş Detayları --}}
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Ürünler</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($subtotal, 2) }} TRY</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Kargo</span>
                                <span class="text-green-600 dark:text-green-400 font-medium">Bedava</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">KDV</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($taxAmount, 2) }} TRY</span>
                            </div>
                        </div>

                        {{-- Sipariş Onayla Butonu --}}
                        <button wire:click="submitOrder" wire:loading.attr="disabled"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submitOrder">
                                <i class="fa-solid fa-check mr-2"></i>
                                Siparişi Onayla
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

    @if (session()->has('address_success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('address_success') }}
        </div>
    @endif
</div>
