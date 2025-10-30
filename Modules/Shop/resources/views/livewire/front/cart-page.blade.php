<div class="container mx-auto px-4 py-8 md:py-12">
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
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $item)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 md:p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex gap-4">
                            {{-- Product Image --}}
                            @php
                                $media = $item->product->getFirstMedia('gallery');
                                $imageUrl = $media ? thumb($media, 150, 150) : asset('images/no-image.jpg');
                            @endphp
                            <a href="{{ route('shop.show', $item->product->getTranslated('slug', app()->getLocale())) }}"
                               class="flex-shrink-0">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $item->product->getTranslated('title', app()->getLocale()) }}"
                                     class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-lg">
                            </a>

                            {{-- Product Info --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('shop.show', $item->product->getTranslated('slug', app()->getLocale())) }}"
                                   class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    {{ $item->product->getTranslated('title', app()->getLocale()) }}
                                </a>

                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    SKU: {{ $item->product->sku }}
                                </p>

                                {{-- Variant Info --}}
                                @if($item->variant)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Varyant: {{ $item->variant->name }}
                                    </p>
                                @endif

                                {{-- Price --}}
                                <div class="mt-3">
                                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($item->final_price, 0, ',', '.') }} ₺
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        / adet
                                    </span>
                                </div>

                                {{-- Quantity Controls --}}
                                <div class="flex items-center gap-4 mt-4">
                                    <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                        <button type="button"
                                                wire:click="decreaseQuantity({{ $item->cart_item_id }})"
                                                class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>

                                        <input type="number"
                                               value="{{ $item->quantity }}"
                                               wire:change="updateQuantity({{ $item->cart_item_id }}, $event.target.value)"
                                               min="1"
                                               class="w-16 px-2 py-2 text-center border-x border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                                        <button type="button"
                                                wire:click="increaseQuantity({{ $item->cart_item_id }})"
                                                class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>

                                    <button type="button"
                                            wire:click="removeItem({{ $item->cart_item_id }})"
                                            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium text-sm transition-colors">
                                        <i class="fa-solid fa-trash-alt mr-1"></i>
                                        Kaldır
                                    </button>
                                </div>

                                {{-- Row Total --}}
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Ara Toplam:
                                        </span>
                                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ number_format($item->subtotal, 0, ',', '.') }} ₺
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Clear Cart Button --}}
                <div class="flex justify-end">
                    <button type="button"
                            wire:click="clearCart"
                            wire:confirm="Sepeti boşaltmak istediğinizden emin misiniz?"
                            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium text-sm transition-colors">
                        <i class="fa-solid fa-trash-alt mr-1"></i>
                        Sepeti Boşalt
                    </button>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        Sipariş Özeti
                    </h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Ara Toplam:</span>
                            <span class="font-semibold">{{ number_format($subtotal, 0, ',', '.') }} ₺</span>
                        </div>

                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>KDV (%20):</span>
                            <span class="font-semibold">{{ number_format($taxAmount, 0, ',', '.') }} ₺</span>
                        </div>

                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Kargo:</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">Ücretsiz</span>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Toplam:</span>
                                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($total, 0, ',', '.') }} ₺
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Checkout Button --}}
                    <a href="{{ route('shop.checkout') }}"
                       class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-semibold py-3 px-6 rounded-lg transition-colors mb-3">
                        <i class="fa-solid fa-check-circle mr-2"></i>
                        Sipariş Ver
                    </a>

                    {{-- Continue Shopping --}}
                    <a href="{{ route('shop.index') }}"
                       class="block w-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-center font-semibold py-3 px-6 rounded-lg transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Alışverişe Devam Et
                    </a>

                    {{-- Trust Badges --}}
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div>
                                <i class="fa-solid fa-shield-check text-2xl text-green-600 dark:text-green-400 mb-2"></i>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Güvenli Ödeme</p>
                            </div>
                            <div>
                                <i class="fa-solid fa-truck text-2xl text-blue-600 dark:text-blue-400 mb-2"></i>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Ücretsiz Kargo</p>
                            </div>
                            <div>
                                <i class="fa-solid fa-undo text-2xl text-orange-600 dark:text-orange-400 mb-2"></i>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Kolay İade</p>
                            </div>
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

@push('scripts')
<script>
    window.addEventListener('cart-updated', event => {
        console.log('✅ Sepet güncellendi');
    });

    window.addEventListener('cart-item-removed', event => {
        console.log('✅ Ürün sepetten çıkarıldı');
    });

    window.addEventListener('cart-cleared', event => {
        console.log('✅ Sepet boşaltıldı');
    });

    window.addEventListener('cart-error', event => {
        alert('Hata: ' + event.detail.message);
    });
</script>
@endpush
