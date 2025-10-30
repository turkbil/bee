<div class="container mx-auto px-4 py-8 md:py-12">
    {{-- Success/Error Messages (Fixed position - header altında) --}}
    <div x-data="{ show: false, message: '', isError: false }"
         @cart-updated.window="show = true; message = $event.detail.message; isError = false; setTimeout(() => show = false, 3000)"
         @cart-item-removed.window="show = true; message = $event.detail.message; isError = false; setTimeout(() => show = false, 3000)"
         @cart-cleared.window="show = true; message = $event.detail.message; isError = false; setTimeout(() => show = false, 3000)"
         @cart-error.window="show = true; message = $event.detail.message; isError = true; setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none; z-index: 9999;"
         :class="isError ? 'bg-red-100 dark:bg-red-900/30 border-red-400 dark:border-red-700 text-red-700 dark:text-red-300' : 'bg-green-100 dark:bg-green-900/30 border-green-400 dark:border-green-700 text-green-700 dark:text-green-300'"
         class="fixed top-32 right-4 max-w-md border px-4 py-3 rounded-lg flex items-center gap-3 shadow-lg">
        <i :class="isError ? 'fa-circle-xmark' : 'fa-circle-check'" class="fa-solid text-xl"></i>
        <span x-text="message" class="flex-1"></span>
    </div>

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-shopping-cart mr-3"></i>
            Alışveriş Sepeti
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Sepetinizdeki {{ $itemCount }} ürün
        </p>
    </div>

    @if($items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items Section --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $item)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow"
                         wire:key="cart-item-{{ $item->cart_item_id }}">
                        <div class="flex flex-col md:flex-row gap-6">
                            {{-- Product Image / Category Icon --}}
                            <div class="flex-shrink-0">
                                @php
                                    $featuredMedia = $item->product->getMedia('featured_image')->first();
                                    $categoryIcon = $item->product->category->icon_class ?? 'fa-light fa-box';
                                @endphp
                                @if($featuredMedia)
                                    <img src="{{ thumb($featuredMedia, 120, 120, ['scale' => 1]) }}"
                                         alt="{{ $item->product->getTranslated('title', app()->getLocale()) }}"
                                         class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-lg"
                                         loading="lazy">
                                @else
                                    <div class="w-24 h-24 md:w-32 md:h-32 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600 rounded-lg flex items-center justify-center">
                                        <i class="{{ $categoryIcon }} text-4xl md:text-5xl text-blue-400 dark:text-blue-400"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 truncate">
                                            <a href="{{ route('shop.show', $item->product->getTranslated('slug', app()->getLocale())) }}"
                                               class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                {{ $item->product->getTranslated('title', app()->getLocale()) }}
                                            </a>
                                        </h3>
                                        @if($item->product->sku)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                SKU: {{ $item->product->sku }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Remove Button --}}
                                    <button wire:click="removeItem({{ $item->cart_item_id }})"
                                            wire:loading.attr="disabled"
                                            class="flex-shrink-0 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors disabled:opacity-50"
                                            title="Ürünü Kaldır">
                                        <i class="fa-solid fa-trash-can text-lg"
                                           wire:loading.remove wire:target="removeItem({{ $item->cart_item_id }})"></i>
                                        <i class="fa-solid fa-spinner fa-spin text-lg"
                                           wire:loading wire:target="removeItem({{ $item->cart_item_id }})"
                                           style="display: none;"></i>
                                    </button>
                                </div>

                                {{-- Price & Quantity --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Adet:</span>
                                        <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                            <button wire:click="decreaseQuantity({{ $item->cart_item_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors disabled:opacity-50"
                                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <span class="w-12 text-center font-semibold text-gray-900 dark:text-white">
                                                {{ $item->quantity }}
                                            </span>
                                            <button wire:click="increaseQuantity({{ $item->cart_item_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors disabled:opacity-50">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Price Info --}}
                                    <div class="text-right">
                                        @php
                                            // Currency kontrolü - USD ise TRY'ye çevir
                                            $unitPriceTRY = $item->unit_price;
                                            $subtotalTRY = $item->subtotal;

                                            if ($item->currency && $item->currency->code !== 'TRY') {
                                                $exchangeRate = $item->currency->exchange_rate ?? 1;
                                                $unitPriceTRY = $item->unit_price * $exchangeRate;
                                                $subtotalTRY = $item->subtotal * $exchangeRate;
                                            }
                                        @endphp
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                            Birim: {{ number_format($unitPriceTRY, 2, ',', '.') }} ₺
                                        </div>
                                        <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ number_format($subtotalTRY, 2, ',', '.') }} ₺
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        Sipariş Özeti
                    </h2>

                    {{-- Summary Lines --}}
                    <div class="space-y-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
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

                    {{-- Action Buttons --}}
                    <div class="space-y-3 mb-6">
                        <a href="{{ route('shop.checkout') }}"
                           class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-lg text-center transition-all transform hover:scale-105 shadow-lg">
                            <i class="fa-solid fa-credit-card mr-2"></i>
                            Sipariş Ver
                        </a>
                        <a href="{{ route('shop.index') }}"
                           class="block w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors">
                            <i class="fa-solid fa-shopping-bag mr-2"></i>
                            Alışverişe Devam
                        </a>
                        <button wire:click="clearCart"
                                wire:confirm="Sepeti tamamen boşaltmak istediğinize emin misiniz?"
                                wire:loading.attr="disabled"
                                class="w-full text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-semibold py-2 transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="clearCart">
                                <i class="fa-solid fa-trash mr-1"></i>
                                Sepeti Boşalt
                            </span>
                            <span wire:loading wire:target="clearCart">
                                <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                                Boşaltılıyor...
                            </span>
                        </button>
                    </div>

                    {{-- Güven Sembolleri --}}
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 text-center">
                            Güvenli Alışveriş Garantisi
                        </h3>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-lock text-green-600 dark:text-green-400 text-lg"></i>
                                </div>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">SSL</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Güvenli</p>
                            </div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-shield-check text-blue-600 dark:text-blue-400 text-lg"></i>
                                </div>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Ödeme</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Korumalı</p>
                            </div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-rotate-left text-purple-600 dark:text-purple-400 text-lg"></i>
                                </div>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">14 Gün</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">İade</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sticky WhatsApp Yardım Butonu --}}
                <div class="fixed bottom-6 right-6 z-40 flex flex-col gap-3">
                    <a href="https://wa.me/05010056758?text=Sepet%20hakk%C4%B1nda%20soru%20sormak%20istiyorum"
                       target="_blank"
                       class="group bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl transition-all hover:scale-110 relative">
                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                        <span class="absolute right-full mr-3 bg-gray-900 text-white text-xs font-semibold px-3 py-2 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            WhatsApp Destek
                        </span>
                    </a>
                </div>
            </div>
        </div>

        {{-- GDPR/KVKK Bildirim Banner (Alta Taşındı) --}}
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-5 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-shield-check text-white text-xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                        Gizlilik ve Veri Güvenliği
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-3 leading-relaxed">
                        Sepet bilgileriniz güvenli olarak saklanmaktadır. Kişisel verileriniz
                        <a href="/page/gizlilik-politikasi" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">Gizlilik Politikamız</a>
                        ve <a href="/page/kvkk-aydinlatma" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">KVKK Aydınlatma Metni</a>
                        kapsamında işlenmekte olup, yasal saklama süreleri boyunca korunmaktadır.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-cookie-bite text-blue-600 dark:text-blue-400"></i>
                            <span>Sepet için çerez kullanılmaktadır</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-clock text-green-600 dark:text-green-400"></i>
                            <span>Sepet 30 gün saklanır</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-lock text-purple-600 dark:text-purple-400"></i>
                            <span>SSL ile şifrelenmiştir</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart State --}}
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                <i class="fa-solid fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                Sepetiniz Boş
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Alışverişe başlamak için ürünlerimize göz atın
            </p>
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                <i class="fa-solid fa-shopping-bag"></i>
                <span>Ürünleri İncele</span>
            </a>
        </div>
    @endif
</div>
