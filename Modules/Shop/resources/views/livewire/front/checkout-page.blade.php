<div class="container mx-auto px-4 py-8 md:py-12">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-credit-card mr-3"></i>
            Sipariş Bilgileri
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Siparişinizi tamamlamak için lütfen bilgilerinizi doldurun
        </p>
    </div>

    <form wire:submit.prevent="submitOrder">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form Section --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- İletişim Bilgileri --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                        <i class="fa-solid fa-user text-blue-600 dark:text-blue-400"></i>
                        İletişim Bilgileri
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Ad Soyad <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model.defer="name"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="Adınız ve Soyadınız">
                            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                E-posta <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   wire:model.defer="email"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="ornek@email.com">
                            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Telefon <span class="text-red-500">*</span>
                            </label>
                            <input type="tel"
                                   wire:model.defer="phone"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="0532 123 45 67">
                            @error('phone') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Şirket Adı
                            </label>
                            <input type="text"
                                   wire:model.defer="company"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="Şirket adınız (opsiyonel)">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Vergi Dairesi
                            </label>
                            <input type="text"
                                   wire:model.defer="tax_office"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="Vergi dairesi (opsiyonel)">
                        </div>
                    </div>
                </div>

                {{-- Teslimat Adresi --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                        <i class="fa-solid fa-location-dot text-blue-600 dark:text-blue-400"></i>
                        Teslimat Adresi
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Adres <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model.defer="address"
                                      rows="3"
                                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                      placeholder="Tam adresiniz"></textarea>
                            @error('address') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Şehir <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model.defer="city"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="İstanbul">
                            @error('city') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                İlçe
                            </label>
                            <input type="text"
                                   wire:model.defer="district"
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                   placeholder="İlçe">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Sipariş Notu
                            </label>
                            <textarea wire:model.defer="notes"
                                      rows="2"
                                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                      placeholder="Sipariş hakkında not ekleyebilirsiniz (opsiyonel)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- GDPR/KVKK Onaylar --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                        <i class="fa-solid fa-shield-check text-blue-600 dark:text-blue-400"></i>
                        Sözleşmeler ve Onaylar
                    </h2>

                    <div class="space-y-4">
                        {{-- KVKK --}}
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox"
                                   wire:model.defer="agree_kvkk"
                                   class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="/page/kvkk-aydinlatma" target="_blank" class="font-semibold underline">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                            </span>
                        </label>
                        @error('agree_kvkk') <span class="text-red-500 text-sm ml-8">{{ $message }}</span> @enderror

                        {{-- Mesafeli Satış --}}
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox"
                                   wire:model.defer="agree_distance_selling"
                                   class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="/page/mesafeli-satis-sozlesmesi" target="_blank" class="font-semibold underline">Mesafeli Satış Sözleşmesi</a>'ni okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                            </span>
                        </label>
                        @error('agree_distance_selling') <span class="text-red-500 text-sm ml-8">{{ $message }}</span> @enderror

                        {{-- Ön Bilgilendirme --}}
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox"
                                   wire:model.defer="agree_preliminary_info"
                                   class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="/page/on-bilgilendirme-formu" target="_blank" class="font-semibold underline">Ön Bilgilendirme Formu</a>'nu okudum ve kabul ediyorum. <span class="text-red-500">*</span>
                            </span>
                        </label>
                        @error('agree_preliminary_info') <span class="text-red-500 text-sm ml-8">{{ $message }}</span> @enderror

                        {{-- Marketing (Opsiyonel) --}}
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox"
                                   wire:model.defer="agree_marketing"
                                   class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                Kampanya ve fırsatlardan haberdar olmak istiyorum. (Opsiyonel)
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        Sipariş Özeti
                    </h2>

                    {{-- Cart Items --}}
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        @foreach($items as $item)
                            <div class="flex gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $item->product->getTranslated('title', app()->getLocale()) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item->quantity }} adet
                                    </p>
                                </div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    @php
                                        $price = $item->unit_price;
                                        if ($item->currency && $item->currency->code !== 'TRY') {
                                            $price = $price * ($item->currency->exchange_rate ?? 1);
                                        }
                                    @endphp
                                    {{ number_format($price * $item->quantity, 2, ',', '.') }} ₺
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary Lines --}}
                    <div class="space-y-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Ara Toplam:</span>
                            <span class="font-semibold">{{ number_format($subtotal, 2, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>KDV (%{{ config('shop.tax_rate', 20) }}):</span>
                            <span class="font-semibold">{{ number_format($taxAmount, 2, ',', '.') }} ₺</span>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="flex items-center justify-between text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        <span>Toplam:</span>
                        <span class="text-blue-600 dark:text-blue-400">{{ number_format($total, 2, ',', '.') }} ₺</span>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-lg transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span wire:loading.remove wire:target="submitOrder">
                            <i class="fa-solid fa-check mr-2"></i>
                            Siparişi Tamamla
                        </span>
                        <span wire:loading wire:target="submitOrder">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                            İşleniyor...
                        </span>
                    </button>

                    {{-- Güven Sembolleri --}}
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-lock text-green-600"></i>
                                <span>Güvenli</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-shield-check text-blue-600"></i>
                                <span>SSL</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-rotate-left text-purple-600"></i>
                                <span>14 Gün İade</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
